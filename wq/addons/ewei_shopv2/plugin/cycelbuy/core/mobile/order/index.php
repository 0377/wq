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

class Index_EweiShopV2Page extends PluginMobilePage {

    function main() {
        global $_W,$_GPC;
        $cycelbuy_plugin = p('cycelbuy');
        if (!$cycelbuy_plugin) {
            show_message('未找到周期购应用，请联系系统管理员！');die();
        }
        $trade = m('common')->getSysset('trade');
        header('location:'.mobileUrl('cycelbuy/order/list'));
//        include $this->template('cycelbuy/order/main');
    }

    function detail() {

        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $member = m('member')->getMember($openid, true);
        $orderid = intval($_GPC['id']);
        $ispeerpay = m('order')->checkpeerpay($orderid);//检查是否是代付订单

        if (empty($orderid)) {
            header('location: ' . mobileUrl('cycelbuy/order'));
            exit;
        }


        $order = pdo_fetch("select * from " . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1'
            , array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

        if (empty($order)) {
            header('location: ' . mobileUrl('order'));
            exit;
        }

        if ($order['merchshow'] == 1) {
            header('location: ' . mobileUrl('order'));
            exit;
        }

        if ($order['userdeleted'] == 2) {
            $this->message('订单已经被删除!','','error');
        }

        if (!empty($order['istrade'])) {
            header('location: ' . mobileUrl('newstore/norder/detail', array('id' => $orderid)));
            exit;
        }

        //如果维过权则取维权信息
        if($order['refundid'] !=0){
            $refund = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_order_refund') . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc", array(':orderid' => $order['id'], ':uniacid' => $_W['uniacid']));
        }

        $area_set = m('util')->get_area_config_set();
        $new_area = intval($area_set['new_area']);
        $address_street = intval($area_set['address_street']);

        //商品信息
        $diyform_plugin = p('diyform');
        $diyformfields = "";
        if ($diyform_plugin) {
            $diyformfields = ",og.diyformfields,og.diyformdata";
        }

        $param = array();
        $param[':uniacid'] = $_W['uniacid'];

        if ($order['isparent'] == 1) {
            $scondition = " og.parentorderid=:parentorderid";
            $param[':parentorderid'] = $orderid;
        } else {
            $scondition = " og.orderid=:orderid";
            $param[':orderid'] = $orderid;
        }

        $condition1 = '';
        if(p('ccard')) {
            $condition1 .= ',g.ccardexplain,g.ccardtimeexplain';
        }

        $goodsid_array =array();
        $goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,g.status, g.cannotrefund, og.total,g.credit,og.optionid,
            og.optionname as optiontitle,g.isverify,g.storeids,og.seckill,g.isfullback,
            og.seckill_taskid{$diyformfields}{$condition1},og.prohibitrefund  from " . tablename('ewei_shop_order_goods') . " og "
            . " left join " . tablename('ewei_shop_goods') . " g on g.id=og.goodsid "
            . " where $scondition and og.uniacid=:uniacid ", $param);

		//禁止退款prohibit refund
        $prohibitrefund=false;
        foreach($goods as &$g){
            if($g['isfullback']){
                $fullbackgoods = pdo_fetch("SELECT * FROM ".tablename('ewei_shop_fullback_goods')." WHERE goodsid = :goodsid and uniacid = :uniacid  limit 1 ",array(':goodsid'=>$g['goodsid'],':uniacid'=>$uniacid));
                if($g['optionid']){
                    $option = pdo_fetch("select `day`,allfullbackprice,fullbackprice,allfullbackratio,fullbackratio,isfullback
                      from ".tablename('ewei_shop_goods_option')." where id = :id and uniacid = :uniacid ",array(":id"=>$g['optionid'],":uniacid"=>$uniacid));
                    $fullbackgoods['minallfullbackallprice'] = $option['allfullbackprice'];
                    $fullbackgoods['fullbackprice'] = $option['fullbackprice'];
                    $fullbackgoods['minallfullbackallratio'] = $option['allfullbackratio'];
                    $fullbackgoods['fullbackratio'] = $option['fullbackratio'];
                    $fullbackgoods['day'] = $option['day'];
                }
                $g['fullbackgoods'] = $fullbackgoods;
                unset($fullbackgoods,$option);
            }

            $g['seckill_task'] = false;
            if($g['seckill']){
                $g['seckill_task'] = plugin_run('seckill::getTaskInfo',$g['seckill_taskid']);
            }

            if(!empty($g['prohibitrefund']))
            {
                $prohibitrefund=true;
            }
        }
        unset($g);
        //商品是否支持退换货
        $goodsrefund = true;

        if(!empty($goods)) {
            foreach ($goods as &$g) {
                $goodsid_array[] = $g['goodsid'];
                if (!empty($g['optionid'])) {
                    $thumb = m('goods')->getOptionThumb($g['goodsid'], $g['optionid']);
                    if (!empty($thumb)) {
                        $g['thumb'] = $thumb;
                    }
                }
                if(!empty($g['cannotrefund']) && $order['status']==2){
                    $goodsrefund = false;
                }
            }
            unset($g);
        }
        $diyform_flag = 0;

        if ($diyform_plugin) {
            foreach ($goods as &$g) {
                $g['diyformfields'] = iunserializer($g['diyformfields']);
                $g['diyformdata'] = iunserializer($g['diyformdata']);
                unset($g);
            }

            //订单统一模板
            if (!empty($order['diyformfields']) && !empty($order['diyformdata'])) {
                $order_fields = iunserializer($order['diyformfields']);
                $order_data = iunserializer($order['diyformdata']);
            }
        }

        //收货地址
        $address = false;
        if (!empty($order['addressid'])) {
            $address = iunserializer($order['address']);
            if (!is_array($address)) {
                $address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
            }
        }

        //联系人
        $carrier = @iunserializer($order['carrier']);
        if (!is_array($carrier) || empty($carrier)) {
            $carrier = false;
        }

        //门店
        $store = false;
        if (!empty($order['storeid'])) {
            $store = pdo_fetch('select * from  ' . tablename('ewei_shop_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
        }

        //核销门店
        $stores = false;
        $showverify = false;  //是否显示消费码
        $canverify = false;  //是否可以核销
        $verifyinfo = false;
        if (com('verify')) {
            $showverify = $order['dispatchtype'] || $order['isverify'];

            if ($order['isverify']) {
                //lynn核销限制时间判断
                if($order['verifyendtime'] > 0 && $order['verifyendtime'] < time()){
                    $order['status'] = -1;
                }
                //核销单
                $storeids = array();
                foreach ($goods as $g) {
                    if (!empty($g['storeids'])) {
                        $storeids = array_merge(explode(',', $g['storeids']), $storeids);
                    }
                }

                if (empty($storeids)) {
                    //全部门店
                    $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                } else {
                    $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                }

                if ($order['verifytype'] == 0 || $order['verifytype'] == 1 || $order['verifytype'] == 3) {
                    $vs = iunserializer($order['verifyinfo']);
                    $verifyinfo = array(
                        array(
                            'verifycode' => $order['verifycode'],
                            'verified' => $order['verifytype'] == 0 || $order['verifytype'] == 3 ?$order['verified']: count($vs)>=$goods[0]['total']
                        )
                    );
                    if( $order['verifytype']==0 || $order['verifytype'] == 3) {
                        $canverify = empty($order['verified']) && $showverify;
                    } else if( $order['verifytype']==1 ){
                        $canverify = count($vs)<$goods[0]['total']  && $showverify;
                    }

                } else {
                    $verifyinfo = iunserializer($order['verifyinfo']);

                    $last = 0;
                    foreach($verifyinfo as $v){
                        if(!$v['verified']){
                            $last++;
                        }
                    }
                    $canverify = $last>0 && $showverify;
                }
            }
            else if(!empty($order['dispatchtype'])){

                $verifyinfo = array(
                    array(
                        'verifycode' => $order['verifycode'],
                        'verified' => $order['status'] == 3
                    )
                );

                $canverify = $order['status']==1 && $showverify;
            }

        }
        $order['canverify'] = $canverify;
        $order['showverify'] = $showverify;

        //是否可以退款
        if ($order['status'] == 1 || $order['status'] == 2) {
            $canrefund = true;
            if ($order['status'] == 2 && $order['price'] == $order['dispatchprice']) {
                if ($order['refundstate'] > 0) {
                    $canrefund = true;
                } else {
                    $canrefund = false;
                }
            }
        } else if ($order['status'] == 3) {
            if ($order['isverify'] != 1 && empty($order['virtual'])) { //如果不是核销或虚拟物品，则可以退货
                if ($order['refundstate'] > 0) {
                    $canrefund = true;
                } else {
                    $tradeset = m('common')->getSysset('trade');
                    $refunddays = intval($tradeset['refunddays']);

                    if ($refunddays > 0) {
                        $days = intval((time() - $order['finishtime']) / 3600 / 24);
                        if ($days <= $refunddays) {
                            $canrefund = true;
                        }
                    }
                }
            }
        }

        if (!empty($order['isnewstore']) && $order['status'] > 1) {
            $canrefund = false;
        }
		if($prohibitrefund)
        {
            $canrefund = false;
        }

        if(!$goodsrefund && $canrefund){
            $canrefund = false;
        }


        if(p('ccard')) {

            if(!empty($order['ccard']) && $order['status'] > 1) {
                $canrefund = false;
            }

            $comdata = m('common')->getPluginset('commission');
            if (!empty($comdata['become_goodsid']) && !empty($goodsid_array)) {
                if(in_array($comdata['become_goodsid'], $goodsid_array)) {
                    $canrefund = false;
                }
            }
        }

        $haveverifygoodlog = m('order')->checkhaveverifygoodlog($orderid);
        if($haveverifygoodlog)
        {
            $canrefund = false;
        }

        $order['canrefund'] = $canrefund;
        //如果发货，查找第一条物流
        $express = false;
        $order_goods = array();
        if ($order['status'] >= 2 && empty($order['isvirtual']) && empty($order['isverify'])) {
            $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
            if (count($expresslist) > 0) {
                $express = $expresslist[0];
            }
        }
        if($order['sendtype']>0 && $order['status']>=1){
            $order_goods = pdo_fetchall("select orderid,goodsid,sendtype,expresscom,expresssn,express,sendtime from ".tablename('ewei_shop_order_goods')."
            where orderid = ".$orderid." and uniacid = ".$uniacid." and sendtype > 0 group by sendtype order by sendtime asc ");
            $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
            if (count($expresslist) > 0) {
                $express = $expresslist[0];
            }
            $order['sendtime'] = $order_goods[0]['sendtime'];
        }
        $shopname = $_W['shopset']['shop']['name'];

        $cycelSql = 'SELECT * FROM '.tablename('ewei_shop_cycelbuy_periods').' WHERE orderid=:orderid AND uniacid=:uniacid';
        $cycelParams = array(
            ':orderid' => $order['id'],
            ':uniacid' => $_W['uniacid'],
        );
        $cycelData = pdo_fetchall($cycelSql,$cycelParams);
        $cycelUnderway = pdo_fetch('SELECT count(*) as count FROM '.tablename('ewei_shop_cycelbuy_periods').' WHERE orderid='.$order['id'].' AND status<=1 AND uniacid='.$_W['uniacid']);
        $activity = com('coupon') -> activity($order['price']);

        if(count($cycelData) == $cycelUnderway['count']){
            $norStart = 1;
        }else{
            $norStart = 0;
        }

        $notArray= array();
        $start = false;
        $cycelids = array();
        foreach($cycelData as $key=> &$row){
            if($row['status']==0){
                $notArray[] = $key;
            }elseif ($row['status']==1){
                $start = true;
                $period_index=$key;
            }elseif ($row['status']==2){

            }
        }
        unset($row);
        if(empty($start) && !empty($notArray)){
            $period_index = min($notArray);
        }
        include $this->template();
    }

    function express() {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $orderid = intval($_GPC['orderid']);
        $id = intval($_GPC['id']);
//        $sendtype = intval($_GPC['sendtype']);
//        $bundle = trim($_GPC['bundle']);

        if (empty($orderid) || empty($id)) {
            header('location: ' . mobileUrl('cycelbuy/order'));
            exit;
        }
        $order = pdo_fetch("select * from " . tablename('ewei_shop_cycelbuy_periods') . ' where id=:id  and orderid = :orderid and uniacid=:uniacid limit 1'
            , array(':id' => $id,':orderid'=>$orderid, ':uniacid' => $uniacid));
        if (empty($order)) {
            header('location: ' . mobileUrl('cycelbuy/order'));
            exit;
        }

        if (empty($order['addressid'])) {
            $this->message('订单非快递单，无法查看物流信息!');
        }
        if ($order['status'] < 1) {
            $this->message('订单未发货，无法查看物流信息!');
        }
        $condition = '';
        //商品信息
        $goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,og.expresssn,og.express,
            og.sendtype,og.expresscom,og.sendtime,g.storeids{$diyformfields}
            from " . tablename('ewei_shop_order_goods') . " og "
            . " left join " . tablename('ewei_shop_goods') . " g on g.id=og.goodsid "
            . " where og.orderid=:orderid ".$condition." and og.uniacid=:uniacid ", array(':uniacid' => $uniacid, ':orderid' => $orderid));

        $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
        include $this->template();
    }

    function dispatch() {
        global $_W, $_GPC;

        $merchid = intval($_GPC['merchid']);

        $list = m('dispatch')->getDispatchList($merchid);

        include $this->template();

    }

    /**
     *  周期购所有数据
     */
    function cycledetail(){
        global $_GPC,$_W;
        $orderid = intval($_GPC['id']);
        if(empty($orderid)){
            $this->message('数据不存在!');
        }
        $oSql = 'select id,status,refundstate from '.tablename('ewei_shop_order')." where id = {$orderid} and uniacid = {$_W['uniacid']}";
        $oData = pdo_fetch($oSql);

        $sql = 'SELECT * FROM '.tablename('ewei_shop_cycelbuy_periods').' WHERE orderid = :orderid AND uniacid = :uniacid ';
        $params = array(
            ':orderid' => $orderid,
            ':uniacid' => intval($_W['uniacid']),
        );

        $list = pdo_fetchall($sql,$params);

        $notStart = false; //未开始

        $status0=0;//未开始数
        $status2=0;//已完成数
        $weekArr = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        $notArray= array();
        $start = false;
        foreach($list as $key=> &$row){
            $row['week'] = $weekArr[date('w',$row['receipttime'])];
            $address = unserialize($row['address']);
            $row['addressInfo'] = $address['province'].$address['city'].$address['area'].$address['address'];

            if($row['status']==0){
                $notArray[] = $key;
                $receipttimeArray[]=$list[$key]['receipttime'];
                $status0+=1;//未开始数加1
            }elseif ($row['status']==1){
                $start = true;
                $period_index=$key;
                if (!empty($list[$key + 1]['receipttime'])) {
                    $receipttime = $list[$key + 1]['receipttime'];
                }else {
                    $receipttime = $list[$key]['receipttime'];
                }
            }elseif ($row['status']==2){
                $status2+=1;//已完成数加1
            }
        }
        unset($row);



        if(empty($start) && !empty($notArray)){
            $period_index = min($notArray);
        }
        if(empty($start) && !empty($receipttimeArray)){
            $receipttime = min($receipttimeArray);
        }


        //是否有修改地址申请
        $existApply = '';
        $existApply = pdo_get( 'ewei_shop_address_applyfor' , array('orderid' => $orderid , 'uniacid' => $_W['uniacid'], 'isdelete' => 0) );

        $cycelbuy_periodic = pdo_fetchcolumn("select cycelbuy_periodic from " . tablename('ewei_shop_order') . ' where id=:orderid and uniacid=:uniacid limit 1'
            , $params);

       include $this -> template();
    }

    function success()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $member = m('member')->getMember($openid, true);
        $orderid = intval($_GPC['id']);

        if (empty($orderid)) {
            $this->message('参数错误', mobileUrl('order'), 'error');
        }
        $order = pdo_fetch("select * from " . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1'
            , array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

        @session_start();
        if (!isset($_SESSION[EWEI_SHOPV2_PREFIX . "_order_pay_complete"])) {
            if (empty($order['istrade'])) {
                header('location: ' . mobileUrl('order'));
            } else {
                header('location: ' . mobileUrl('newstore/norder'));
            }
            exit;
        }
        unset($_SESSION[EWEI_SHOPV2_PREFIX . "_order_pay_complete"]);

        $hasverifygood  = m("order")->checkhaveverifygoods($orderid);
        $isonlyverifygoods  = m("order")->checkisonlyverifygoods($orderid);

        $ispeerpay = m('order')->checkpeerpay($orderid);
        if (!empty($ispeerpay)) {//代付
            $peerpay = floatval($_GPC['peerpay']);
            $openid = pdo_fetchcolumn("select openid from " . tablename('ewei_shop_order') . ' where id=:orderid and uniacid=:uniacid limit 1', array(':orderid' => $orderid, ':uniacid' => $uniacid));
            $order['price'] = $ispeerpay['realprice'];
            $peerpayuid = m('member')->getInfo($_W['openid']);
            $peerprice = pdo_fetch("SELECT `price` FROM ".tablename('ewei_shop_order_peerpay_payinfo')." WHERE uid = :uid ORDER BY id DESC LIMIT 1",array(':uid'=>$peerpayuid['id']));

            //查询是否存在支付领优惠券活动
            $activity = com('coupon') -> activity(empty($peerprice)?0:$peerprice['price']);
            if($activity){
                $share = true;
            }else{
                $share = false;
            }
        }else{

            if (!empty($order['istrade'])) {
                if($order['status'] == 1 && $order['tradestatus'] == 1) {
                    $order['price'] = $order['dowpayment'];
                } else if ($order['status'] == 1 && $order['tradestatus'] == 2){
                    $order['price'] = $order['betweenprice'];
                }
            }

            $merchid = $order['merchid'];
            //商品
            $goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids from " . tablename('ewei_shop_order_goods') . " og "
                . " left join " . tablename('ewei_shop_goods') . " g on g.id=og.goodsid "
                . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(':uniacid' => $uniacid, ':orderid' => $orderid));

            //地址
            $address = false;
            if (!empty($order['addressid'])) {
                $address = iunserializer($order['address']);
                if (!is_array($address)) {
                    $address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
                }
            }

            //联系人
            $carrier = @iunserializer($order['carrier']);
            if (!is_array($carrier) || empty($carrier)) {
                $carrier = false;
            }



            //查询是否存在支付领优惠券活动
            $activity = com('coupon') -> activity($order['price']);
            if($activity){
                $share = true;
            }else{
                $share = false;
            }

        }

        include $this->template();

    }

    //确认收货
    function confirm_receipt(){
        global $_W,$_GPC;
        $p = p('commission');
        $pcoupon = com('coupon');

        $id = $_GPC['id'];
        $orderid = $_GPC['orderid'];

        if(empty($id)){
            show_json( 0,'缺少分期ID' );
        }
        if(empty($orderid)){
            show_json( 0,'缺少订单ID' );
        }

        $order= pdo_fetch("select * from " . tablename('ewei_shop_order') . ' where uniacid=:uniacid and id=:id  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $orderid));
        $last_periods= pdo_fetch("select * from " . tablename('ewei_shop_cycelbuy_periods') . ' where uniacid=:uniacid and orderid=:orderid order by id desc  limit 1', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
        if(!empty($last_periods)){
            if($last_periods['id']==$id){
                pdo_update('ewei_shop_order', array('status' => 3,'finishtime'=>time()), array('id' =>$orderid,'status'=>2));
                $result = pdo_update('ewei_shop_cycelbuy_periods', array( 'status' => 2,'finishtime'=>time()), array('orderid' => $orderid, 'uniacid' => $_W['uniacid']));

                //会员升级
                m('member')->upgradeLevel($order['openid'], $orderid);
                //余额赠送
                m('order')->setGiveBalance($orderid, 1);
                //模板消息
                m('notice')->sendOrderMessage($orderid);
                //商品全返
                m('order')->fullback($orderid);
                //处理积分
                m('order')->setStocksAndCredits($orderid, 3);
                //优惠券返利
                    if ($pcoupon) {
                        //发送赠送优惠券
                        com('coupon')->sendcouponsbytask($orderid); //订单支付

                        if (!empty($order['couponid'])) {
                            $pcoupon->backConsumeCoupon($orderid); //自动收货
                        }
                    }
                //分销检测
                if ($p) {
                    $p->checkOrderFinish($orderid);
                }
            }else{
                $result = pdo_update('ewei_shop_cycelbuy_periods', array( 'status' => 2,'finishtime'=>time()), array('id' => $id, 'uniacid' => $_W['uniacid']));
            }

            if( $result != false ){
                show_json( 1 ,'确认收货成功' );
            }else{
                show_json( 0 ,'确认收货失败' );
            }
        }
    }

}