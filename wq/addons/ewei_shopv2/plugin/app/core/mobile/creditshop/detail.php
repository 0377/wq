<?php

/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Detail_EweiShopV2Page extends AppMobilePage
{
    public function main()
    {
        global $_W, $_GPC;

        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        //多商户
        $merch_plugin = p('merch');
        $merch_data = m('common')->getPluginset('merch');
        if ($merch_plugin && $merch_data['is_openmerch']) {
            $is_openmerch = 1;
        } else {
            $is_openmerch = 0;
        }
        $merchid = intval($_GPC['merchid']);
        $_W['merchid'] = $merchid;
        if(!$id){
            app_error(AppError::$ParamsError, '参数错误');
        }
        $shop = m('common')->getSysset('shop');
        $member = m('member')->getMember($openid);
        $goods = p( 'creditshop' )->getGoods($id, $member);
        if(empty($goods)){
            app_error(AppError::$GoodsNotFound, '商品未找到');
        }

        $showgoods = m('goods')->visit($goods, $member);
        if (empty($showgoods)){
            app_error(AppError::$GoodsNotFound, '您没有权限浏览此商品');
        }
        $pay = m('common')->getSysset('pay');

        $set = m('common')->getPluginset('creditshop');

        $goods['subdetail'] = m('common')->html_to_images($goods['subdetail']);
        $goods['noticedetail'] = m('common')->html_to_images($goods['noticedetail']);
        $goods['usedetail'] = m('common')->html_to_images($goods['usedetail']);
        $goods['goodsdetail'] = m('common')->html_to_images($goods['goodsdetail']);
        $credit = $member['credit1'];
        $money = $member['credit2'];

        if (!empty($goods)) {
            //浏览次数
            pdo_update('ewei_shop_creditshop_goods', array('views' => $goods['views'] + 1), array('id' => $id));
            $goods['followed'] = m('user')->followed($openid);
        }else{
            app_error(AppError::$GoodsNotFound, '商品已下架或被删除');
        }
        /*
         * 运费
         * */
        if(is_array($goods['dispatch'])){
            $goods['dispatchprice'] = number_format($goods['dispatch']['min'],2).'~'.number_format($goods['dispatch']['max'],2).'元';
        }else{
            $goods['dispatchprice'] = $goods['dispatch']>0?price_format($goods['dispatch'],2).'元':"包邮";
        }

        //参与记录
        $log = array();
        $log = pdo_fetchall("select openid,createtime from ".tablename('ewei_shop_creditshop_log')."
                where uniacid = ".$uniacid." and goodsid = ".$id." and status > 0 order by createtime desc limit 5 ");
        foreach($log as $key => $value){
            $mem = m('member')->getMember($value['openid']);
            $log[$key]['avatar'] = tomedia($mem['avatar']);
            $log[$key]['nickname'] = $mem['nickname'];
            $log[$key]['createtime_str'] = date('Y/m/d H:i', $value['createtime']);
            unset($mem);
        }
        $logtotal = 0;
        $logtotal = pdo_fetchcolumn("select count(1) from ".tablename('ewei_shop_creditshop_log')." where uniacid = ".$uniacid." and goodsid = ".$id." and status > 0 ");
        $logmore = ceil($logtotal/5) > 1;

        //评论
        $replys = array();
        $replys = pdo_fetchall("select * from ".tablename('ewei_shop_creditshop_comment')."
                where uniacid = ".$uniacid." and goodsid = ".$id." and checked = 1 and deleted = 0 order by `time` desc limit 5 ");
        //评论敏感词替换
        $replykeywords = explode(',', $set['desckeyword']);
        $replykeystr = trim($set['replykeyword']);
        if(empty($replykeystr)){
            $replykeystr = "**";
        }
        foreach($replys as $key => $value){
            //评论替换敏感关键字
            foreach($replykeywords as $k => $val){
                if(!empty($value['content'])){
                    if(!strstr($val, $value['content'])){
                        $value['content'] = str_replace($val, $replykeystr, $value['content']);

                    }
                }
                if(!empty($value['reply_content'])){
                    if(!strstr($val, $value['reply_content'])){
                        $value['reply_content'] = str_replace($val, $replykeystr, $value['reply_content']);
                    }
                }
                if(!empty($value['append_content'])){
                    if(!strstr($val, $value['append_content'])){
                        $value['append_content'] = str_replace($val, $replykeystr, $value['append_content']);
                    }
                }
                if(!empty($value['append_reply_content'])){
                    if(!strstr($val, $value['append_reply_content'])){
                        $value['append_reply_content'] = str_replace($val, $replykeystr, $value['append_reply_content']);
                    }
                }
            }
            $replys[$key]['content'] = $value['content'];
            $replys[$key]['reply_content'] = $value['reply_content'];
            $replys[$key]['append_content'] = $value['append_content'];
            $replys[$key]['append_reply_content'] = $value['append_reply_content'];

            $replys[$key]['time_str'] = date('Y/m/d', $value['time']);
            $replys[$key]['images'] = set_medias(iunserializer($value['images']));
            $replys[$key]['reply_images'] = set_medias(iunserializer($value['reply_images']));
            $replys[$key]['append_images'] = set_medias(iunserializer($value['append_images']));
            $replys[$key]['append_reply_images'] = set_medias(iunserializer($value['append_reply_images']));
            $replys[$key]['nickname'] = cut_str($value['nickname'], 1, 0).'**'.cut_str($value['nickname'], 1, -1);
            $replys[$key]['content'] = str_replace('=', "**", $value['content']);
            $replys[$key]['append_time_str'] = date('Y/m/d',$value['append_time']);
        }

        $replytotal = 0;
        $replytotal = pdo_fetchcolumn("select count(1) from ".tablename('ewei_shop_creditshop_comment')."
                where uniacid = ".$uniacid." and goodsid = ".$id." and checked = 1 and deleted = 0 order by `time` desc ");
        $replymore = ceil($replytotal/5) > 1;
        //如果线下兑换，读取门店
        $stores = array();
        if($goods['goodstype']==0){
            if(!empty($goods['isverify'])){
                $storeids = array();
                if (!empty($goods['storeids'])) {
                    $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
                }
                if (empty($storeids)) {
                    //全部门店
                    if ($merchid > 0) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                    } else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                    }
                } else {
                    if ($merchid > 0) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                    } else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                    }
                }
            }
        }

        //商品推荐
        $goodsrec = pdo_fetchall("select id,thumb,title,credit,money,mincredit,minmoney from ".tablename('ewei_shop_creditshop_goods')."
                    where goodstype = :goodstype and `type` = :gtype and uniacid = :uniacid and status = 1 and deleted = 0 ORDER BY rand() limit 3 ",array(':goodstype'=>$goods['goodstype'],':gtype'=>$goods['type'],':uniacid'=>$uniacid));
        foreach($goodsrec as $key => $value){
            $goodsrec[$key]['credit'] = intval($value['credit']);
            if((intval($value['money'])-$value['money'])==0){
                $goodsrec[$key]['money'] = intval($value['money']);
            }
            $goodsrec[$key]['mincredit'] = intval($value['mincredit']);
            if((intval($value['minmoney'])-$value['minmoney'])==0){
                $goodsrec[$key]['minmoney'] = intval($value['minmoney']);
            }
        }
        app_json(array(
            'goods'=>$goods,
            'log'=>$log,
            'logmore' => $logmore,
            'stores'=>$stores,
            'replys'=>$replys,
            'replymore'=>$replymore,
            'goodsrec'=>$goodsrec,
        ));
    }

    //参与记录
    function getlistlog(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goodsid = intval($_GPC['id']);

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;

        $log = array();
        $log = pdo_fetchall("select openid,createtime from ".tablename('ewei_shop_creditshop_log')."
                where uniacid = ".$uniacid." and goodsid = ".$goodsid." order by createtime desc LIMIT " . (($pindex - 1) * $psize ) . " , " . $psize);
        foreach($log as $key => $value){
            $mem = m('member')->getMember($value['openid']);
            $log[$key]['avatar'] = $mem['avatar'];
            $log[$key]['nickname'] = $mem['nickname'];
            $log[$key]['createtime_str'] = date('Y/m/d H:i', $value['createtime']);
            unset($mem);
        }
        $logtotal = 0;
        $logtotal = pdo_fetchcolumn("select count(1) from ".tablename('ewei_shop_creditshop_log')." where uniacid = ".$uniacid." and goodsid = ".$goodsid." and status > 0 ");
        $more = ceil($logtotal/$psize) > $pindex;

        app_json(array('list'=>$log,'pagesize'=>$psize,'total'=>$logtotal,'more' => $more ));
    }
    //评价
    function getlistreply(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goodsid = intval($_GPC['id']);

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;

        $replys = array();
        $replys = pdo_fetchall("select * from ".tablename('ewei_shop_creditshop_comment')."
                where uniacid = ".$uniacid." and goodsid = ".$goodsid." and checked = 1 and deleted = 0 order by `time` desc LIMIT " . (($pindex - 1) * $psize) . " , " . $psize);
        foreach($replys as $key => $value){
            $replys[$key]['time_str'] = date('Y/m/d', $value['time']);
            $replys[$key]['images'] = set_medias(iunserializer($value['images']));
            $replys[$key]['reply_images'] = set_medias(iunserializer($value['reply_images']));
            $replys[$key]['append_images'] = set_medias(iunserializer($value['append_images']));
            $replys[$key]['append_reply_images'] = set_medias(iunserializer($value['append_reply_images']));
            $replys[$key]['nickname'] = cut_str($value['nickname'], 1, 0).'**'.cut_str($value['nickname'], 1, -1);
        }
        $replytotal = 0;
        $replytotal = pdo_fetchcolumn("select count(1) from ".tablename('ewei_shop_creditshop_comment')."
                where uniacid = ".$uniacid." and goodsid = ".$goodsid." and checked = 1 and deleted = 0 ");
        $more = ceil($replytotal/$psize) > $pindex;
        app_json(array('list'=>$replys,'pagesize'=>$psize,'total'=>$replytotal,'more' => $more));
    }
    function option(){
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $uniacid = intval($_W['uniacid']);
        $goods = pdo_fetch("select id,thumb,credit,money,total,title from ".tablename('ewei_shop_creditshop_goods')." where id= :id and uniacid = :uniacid ",array(':id'=>$id,':uniacid'=>$uniacid));
        $goods = set_medias($goods, 'thumb');
        $specs =false;
        $options = false;
        $specs = pdo_fetchall('select * from ' . tablename('ewei_shop_creditshop_spec') . ' where goodsid=:goodsid and uniacid=:uniacid order by displayorder asc', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
        foreach($specs as &$spec) {
            $spec['items'] = pdo_fetchall('select * from '.tablename('ewei_shop_creditshop_spec_item')." where specid=:specid and `show`=1 order by displayorder asc",array(':specid'=>$spec['id']));
        }
        unset($spec);
        $options = pdo_fetchall('select * from ' . tablename('ewei_shop_creditshop_option') . ' where goodsid=:goodsid and uniacid=:uniacid order by displayorder asc', array(':goodsid' => $id, ':uniacid' => $uniacid));
        if (!empty($specs))
        {
            foreach ($specs as $key => $value)
            {
                foreach ($specs[$key]['items'] as $k=>&$v)
                {
                    $v['thumb'] = tomedia($v['thumb']);
                }
            }
        }

        if(!$options){
            app_error(AppError::$GoodsNotFound, '商品规格不存在');
        }
        app_json(array('specs'=>$specs,'options'=>$options,'goods'=>$goods));
    }
    function pay($a=array(), $b=array()){
        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $num = max(1,$_GPC['num']);
        $id = intval($_GPC['id']);
        $shop = m('common')->getSysset('shop');
        $member = m('member')->getMember($openid);
        $optionid = intval($_GPC['optionid']);
        $goods = p('creditshop')->getGoods($id, $member,$optionid,$num);
        $credit = $member['credit1'];
        $money = $member['credit2'];
        $paytype = $_GPC['paytype'];
        $addressid = intval($_GPC['addressid']);
        $storeid = intval($_GPC['storeid']);
        $paystatus = 0;
        $dispatch = 0;

        //是否有规格
        if($goods['hasoption'] && $optionid){
            $option = pdo_fetch("select total from ".tablename('ewei_shop_creditshop_option')." where uniacid = ".$uniacid." and id = ".$optionid." and goodsid = ".$id." ");
            if($option['total']<=0){
                app_error( AppError::$CanBuy , $goods['buymsg'] );
            }
        }

        if($addressid){
            $dispatch = p('creditshop')->dispatchPrice($id,$addressid,$optionid,$num);
        }

        $goods['dispatch'] = $dispatch;
        //确认支付
        if (empty($goods['canbuy'])) {
            app_error( AppError::$CanBuy , $goods['buymsg'] );
        }

        $needpay = false;
        if ($goods['money'] > 0 || floatval($goods['dispatch'])>0) {
            //删除以前无效的记录
            pdo_delete('ewei_shop_creditshop_log',array('goodsid'=>$id, 'openid'=>$openid,'status'=>0,'paystatus'=>0));

            $needpay = true;
            //找出上次支付但未参加的记录（例如断电，断网等特殊情况)
            $lastlog = pdo_fetch('select * from ' . tablename('ewei_shop_creditshop_log') . ' where goodsid=:goodsid and openid=:openid  and status=0 and paystatus=1 and uniacid=:uniacid limit 1', array(':goodsid' => $id, ':openid' => $openid, ':uniacid' => $uniacid));
            if (!empty($lastlog)) {
                app_json(array('logid' => $lastlog['id']));
            }
        }else{
            //删除以前无效的记录
            pdo_delete('ewei_shop_creditshop_log',array('goodsid'=>$id, 'openid'=>$openid,'status'=>0));
        }
        $dispatchstatus =  0;
        if( $goods['isverify'] == 1 || $goods['goodstype'] > 0 || $goods['dispatch'] == 0 || $goods['type'] == 1){
            $dispatchstatus = -1;
        }
        //地址
        $address = false;
        if (!empty($addressid)) {
            $address = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('ewei_shop_member_address') . '
            where id=:id and uniacid=:uniacid limit 1', array(':id' => $addressid, ':uniacid' => $_W['uniacid']));
            if (empty($address)) {
                app_error( AppError::$NotFoundAddress , '未找到地址' );
            }
        }

        //生成日志
        $log = array(
            'uniacid' => $uniacid,
            'merchid' => intval($goods['merchid']),
            'openid' => $openid,
            'logno' => m('common')->createNO('creditshop_log', 'logno', $goods['type']==0?'EE':'EL'),
            'goodsid' => $id,
            'storeid' => $storeid,
            'optionid' => $optionid,
            'addressid' => $addressid,
            'address' => iserializer($address),
            'status' => 0,
            'paystatus' => $goods['money'] > 0 ? 0 : -1,
            'dispatchstatus' => $dispatchstatus,
            'createtime' => time(),
            'realname'=>trim($_GPC['realname']),
            'mobile'=>trim($_GPC['mobile']),
            'goods_num'=>$num,
            'paytype'=>0
        );

        /*if ($goods['isverify'] == 1) {
            //如果是兑换，直接出兑奖码
            $log['eno'] = p('creditshop')->createENO();
        }*/
        pdo_insert('ewei_shop_creditshop_log', $log);
        $logid = pdo_insertid();
        if(!empty($log['realname']) && !empty($log['mobile'])){
            //更新会员信息
            $up = array('realname'=>$log['realname'],'carrier_mobile'=>$log['mobile']);
            pdo_update('ewei_shop_member',$up,array('id'=>$member['id'],'uniacid'=>$_W['uniacid']));
            if(!empty($member['uid'])){
                mc_update($member['uid'], array('realname'=>$log['realname']));
            }
        }

        $set = m('common')->getSysset();
        if ($needpay) {
            if($paytype == "credit"){
                if ($money > ($goods['money'] + $goods['dispatch'])) {
                    //如果足够
                    $paystatus = 0;
                }else{
                    app_error( AppError::$MoneyInsufficient , '余额不足' );
                }
                //支付方式
                pdo_update('ewei_shop_creditshop_log', array('paytype' => $paystatus), array('id' => $logid));
            }else if ($paytype == "wechat") {
                $paystatus = 1;
                //支付方式
                pdo_update('ewei_shop_creditshop_log', array('paytype' => $paystatus), array('id' => $logid));
                //微信支付
                $set['pay']['weixin'] = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];

                //微信环境
                if (empty($set['pay']['wxapp']) && $this->iswxapp) {
                    app_error(AppError::$OrderPayFail, "未开启微信支付");
                }
                $wechat = array('success' => false);
                $payinfo = array(
                    'openid' => $_W['openid_wa'],
                    'title' => $set['shop']['name'] . ( empty($goods['type']) ? "积分兑换" : '积分抽奖') . ' 单号:' . $log['logno'],
                    'tid' => $log['logno'],
                    'fee' => $goods['money'] * $num + $goods['dispatch'],
                );
                $res = $this->model->wxpay($payinfo,3);
                if (!is_error($res)){
                    $wechat = array(
                        'success' => true,
                        'payinfo'=>$res
                    );
                }else{
                    $wechat['payinfo']=$res;
                }
                if (!$wechat['success']) {
                    app_error(AppError::$ParamsError, '微信支付参数错误');
                }

                app_json(array('logid' => $logid , 'wechat' => $wechat));
            }
        }

        app_json(array('logid' => $logid));
    }
    function lottery(){
        global $_W, $_GPC;
        $number = max(1,$_GPC['num']);
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $open_redis = function_exists('redis') && !is_error(redis());
        if( $open_redis ) {
            $redis_key = "{$_W['setting']['site']['key']}_{$_W['account']['key']}_{$uniacid}_creditshop_lottery_{$openid}";
            $redis = redis();
            if (!is_error($redis)) {
                if ($redis->setnx($redis_key, time())) {
                    $redis->expireAt($redis_key, time() + 2);
                } else {
                    app_error(AppError::$ParamsError, '操作频繁，请稍后再试');
                }
            }
        }
        $id = intval($_GPC['id']);
        $logid = intval($_GPC['logid']);

        //上面两个id产生歧义，并不知道用的是哪个，谨慎起见，做了以下处理，来尝试解决没有兑换记录以及积分不扣除的bug
        if(!$logid){
            $logid=$id;
        }
        $shop = m('common')->getSysset('shop');
        $member = m('member')->getMember($openid);
        $goodsid = intval($_GPC['goodsid']);

        $log = pdo_fetch('select * from ' . tablename('ewei_shop_creditshop_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $logid, ':uniacid' => $uniacid));

        if(empty($log)){
            $logno=$_GPC['logno'];
            $log = pdo_fetch('select * from ' . tablename('ewei_shop_creditshop_log') . ' where logno=:logno and uniacid=:uniacid limit 1', array(':logno' => $logno, ':uniacid' => $uniacid));
        }


        $optionid = $log['optionid'];
        $goods = p('creditshop')->getGoods($log['goodsid'], $member,$log['optionid'],$number);
        $goods['money'] *= $number;
        $goods['credit'] *= $number;
        $goods['dispatch'] = p('creditshop')->dispatchPrice($log['goodsid'],$log['addressid'],$log['optionid'],$number);
        $credit = $member['credit1'];
        $money = $member['credit2'];
        if (empty($log)) {
            app_error(AppError::$ParamsError, '服务器错误');
        }
//        if ($log['status']>=1) {
//            show_json(0,array('status'=>'-1','message'=>'支付成功!'));
//        }

        if (empty($goods['canbuy'])) {
            app_error(AppError::$ParamsError, $goods['buymsg']);
        }
        $update = array('couponid'=>$goods['couponid']);

        if (empty($log['paystatus'])){
            if ($goods['credit']>0 && $credit<$goods['credit']) {
                app_error(AppError::$ParamsError, '积分不足');
            }
            if ($goods['money'] > 0 && $money<$goods['money'] && $log['paytype'] == 0) {
                app_error(AppError::$ParamsError, '余额不足');
            }
        }
        $update['money'] = $goods['money'];

        //支付状态
        if (($goods['money'] + $goods['dispatch']) > 0 && $log['paystatus']<1) {
            if ($log['paytype'] == 0) {
                //余额支付
                m('member')->setCredit($openid, 'credit2', -($goods['money'] + $goods['dispatch']), "积分商城扣除余额度 {$goods['money']}");
                $update['paystatus']  = 1;
            }

            if ($log['paytype'] == 1){
                $payquery = m('finance')->isWeixinPay($log['logno'],($goods['money'] + $goods['dispatch']), is_h5app()?true:false);

                $payqueryBorrow = m('finance')->isWeixinPayBorrow($log['logno'],($goods['money'] + $goods['dispatch']));
                if (!is_error($payquery) || !is_error($payqueryBorrow)) {
                    //微信支付
                    p('creditshop')->payResult($log['logno'], 'wechat',($goods['money'] + $goods['dispatch']), is_h5app()?true:false);

                }else{
                    app_error(AppError::$ParamsError, '支付出错,请重试(1)');
                }
            }
            if ($log['paytype'] == 2){
                if ($log['paystatus']<1){
                    app_error(AppError::$ParamsError, '未支付成功');
                }
            }

            //支付状态
        }

        if ($log['paytype'] == 0) {
            if ($goods['credit'] > 0 && empty($log['creditpay'])) {
                //扣除积分
                m('member')->setCredit($openid, 'credit1', -$goods['credit'], "积分商城扣除积分 {$goods['credit']}");
                $update['creditpay'] = 1;
                //参加次数
                pdo_query('update ' . tablename('ewei_shop_creditshop_goods') . ' set joins=joins+1 where id=' . $log['goodsid']);
            }
        }

        $status = 1;

        if ($goods['type']==1) {
            if ($goods['rate1'] > 0 && $goods['rate2'] > 0) {
                if ($goods['rate1'] == $goods['rate2']) {
                    //永远中奖
                    $status = 2;
                } else {
                    $rand = rand(0, intval($goods['rate2']));
                    if ($rand <= intval($goods['rate1'])) {
                        //中奖
                        $status = 2;
                    }
                }
            }
        }else{
            $status=2;
        }
        //核销生成核销码
        if ($status == 2 && $goods['isverify']==1) {
            $update['eno'] = p('creditshop')->createENO();
        }
        //核销限制时间，核销次数
        if($goods['isverify'] == 1){
            $update['verifynum'] = $goods['verifynum']>0 ? $goods['verifynum'] : 1;
            if($goods['isendtime']==0){
                if($goods['usetime'] > 0){
                    $update['verifytime'] = time() + 3600*24*intval($goods['usetime']);
                }else{
                    $update['verifytime'] = 0;
                }
            }else{
                $update['verifytime'] = intval($goods['endtime']);
            }
        }
        $update['credit'] = $goods['credit'];
        $update['status'] =  $status;
        if($goods['dispatch']>0 && $goods['goodstype']==0 && $goods['type'] == 0){
            $update['dispatchstatus'] = '1';
            $update['dispatch'] = $goods['dispatch'];
        }
        pdo_update('ewei_shop_creditshop_log', $update, array('id' => $log['id']));

        $log = pdo_fetch('select * from ' . tablename('ewei_shop_creditshop_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $logid, ':uniacid' => $uniacid));
        if($status==2 && $update['creditpay'] == 1){
            if($goods['goodstype']==1){
                //如果是优惠券
                if(com('coupon')){
                    com('coupon')->creditshop($logid);
                    $status = 3;
                }
                $update['time_finish'] = time();
            }elseif($goods['goodstype']==2){
                $credittype = "credit2";
                $creditstr = "积分商城兑换余额";
                $num = abs($goods['grant1'])*intval($log['goods_num']);
                $member = m('member')->getMember($openid);
                $credit2 = floatval($member['credit2']) + $num;
                m('member')->setCredit($openid, $credittype, $num, array($_W['uid'], $creditstr));

                $set = m('common')->getSysset('shop');
                $logno = m('common')->createNO('member_log', 'logno', 'RC');
                $data = array(
                    'openid' => $openid,
                    'logno' => $logno,
                    'uniacid' => $_W['uniacid'],
                    'type' => '0',
                    'createtime' => TIMESTAMP,
                    'status' => '1',
                    'title' => $set['name'] . "积分商城兑换余额",
                    'money' => $num,
                    'remark' => $creditstr,
                    'rechargetype' => 'creditshop'
                );
                pdo_insert('ewei_shop_member_log', $data);
                $mlogid = pdo_insertid();
                m('notice')->sendMemberLogMessage($mlogid);
                plog('finance.recharge.' . $credittype, "充值{$creditstr}: {$num} <br/>会员信息: ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
                $status = 3;
                $update['time_finish'] = time();
            }elseif($goods['goodstype']==3){


                /*$money = abs($goods['grant2']);
                $setting = uni_setting($_W['uniacid'], array('payment'));
                if (!is_array($setting['payment'])) {
                    return error(1, '没有设定支付参数');
                }
                $sec = m('common')->getSec();
                $sec = iunserializer($sec['sec']);
                $certs = $sec;
                $wechat = $setting['payment']['wechat'];
                $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
                $row = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));

                //红包参数
                $params = array(
                    'openid'=>$openid,
                    'tid'=>$log['logno'],
                    'send_name'=>'积分商城红包兑换',
                    'money'=>$money,
                    'wishing'=>'红包领到手抽筋，别人加班你加薪!',
                    'act_name'=>'积分商城红包兑换',
                    'remark'=>'积分商城红包兑换',
                );
                //微信接口参数
                $wechat = array(
                    'appid' => $row['key'],
                    'mchid' => $wechat['mchid'],
                    'apikey' => $wechat['apikey'],
                    'certs' => $certs
                );
                $err = m('common')->sendredpack($params,$wechat);
                if(is_error($err)){
                    show_json(-1,array('status'=>-1,'message'=>'红包发放出错，请联系管理员!'));
                }else{
                    $status = 3;
                    $update['time_finish'] = time();
                }*/
            }
            $update['status'] =  $status;
            pdo_update('ewei_shop_creditshop_log', $update, array('id' => $logid));
            //模板消息
            p('creditshop')->sendMessage($logid);
            if($status == 3){
                //修改库存
                pdo_query('update '.tablename('ewei_shop_creditshop_goods').' set total=total-'.$number.' where id='.$log['goodsid']);
            }
            if($goods['goodstype']==0 && $status == 2){
                //实体商品修改库存
                pdo_query('update '.tablename('ewei_shop_creditshop_goods').' set total=total-'.$number.' where id='.$log['goodsid']);
            }
            //红包修改数量
            if($goods['goodstype']==3 && $status == 2){
                pdo_query('update '.tablename('ewei_shop_creditshop_goods').' set packetsurplus=packetsurplus-'.$number.' where id='.$log['goodsid']);
            }
            //是否有规格
            if($goods['hasoption'] && $log['optionid']){
                //规格商品修改库存
                pdo_query('update '.tablename('ewei_shop_creditshop_option').' set total=total-'.$number.' where id='.$log['optionid']);
            }
        }
        app_json(array('status'=>$status,'goodstype'=>$goods['goodstype']));
    }

    function express()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $orderid = intval($_GPC['id']);

        if (empty($orderid)) {
            app_error(AppError::$OrderNotFound);
        }

        $order = pdo_fetch("select expresscom,expresssn,addressid,status,express from " . tablename('ewei_shop_creditshop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1'
            , array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

        if (empty($order)) {
            app_error(AppError::$OrderNotFound);
        }
        if (empty($order['addressid'])) {
            app_error(AppError::$OrderNoExpress);
        }
        if ($order['status'] < 2) {
            app_error(AppError::$OrderNoExpress);
        }

        //商品信息
        $goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from " . tablename('ewei_shop_order_goods') . " og "
            . " left join " . tablename('ewei_shop_creditshop_order') . " g on g.id=og.goodsid "
            . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(':uniacid' => $uniacid, ':orderid' => $orderid));

        $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);

        $status = '';
        if (!empty($expresslist)) {
            if (strexists($expresslist[0]['step'], '已签收')) {
                $status = '已签收';
            } else if (count($expresslist) <= 2) {
                $status = '备货中';
            } else {
                $status = '配送中';
            }
        }


        app_json(array(
            'com' => $order['expresscom'],
            'sn' => $order['expresssn'],
            'status' => $status,
            'count' => count($goods),
            'thumb' => tomedia($goods[0]['thumb']),
            'expresslist' => $expresslist
        ));

    }
}