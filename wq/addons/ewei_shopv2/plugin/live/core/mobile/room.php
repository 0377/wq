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

class Room_EweiShopV2Page extends PluginMobileLoginPage {

    public function main(){
        global $_W, $_GPC;
        $uniacid = intval($_W['uniacid']);
        $openid = trim($_W['openid']);
        //$this->message('指定直播间不存在');

        /*
         * 邀请卡扫描记录
         * */
        $invitation_id = intval($_GPC['invitationid']);
        if(p('invitation') && $invitation_id > 0){
            $invitation_openid = trim($_GPC['invitation_openid']);
            $invitation_data = array(
                'uniacid' => $uniacid,
                'invitation_id' => $invitation_id,
                'invitation_openid' => $invitation_openid,
                'openid' => $openid,
                'scan_time' => time(),
            );
            pdo_insert('ewei_shop_invitation_log',$invitation_data);
            pdo_query("update ".tablename('ewei_shop_invitation')." set scan = scan+1 where id = ".$invitation_id." and uniacid = ".$uniacid." ");
        }

        $roomid = intval($_GPC['id']);
        if(empty($roomid)){
            $this->message('指定直播间不存在','','error');
        }

        $room = pdo_fetch("SELECT * FROM ". tablename('ewei_shop_live')." where uniacid = :uniacid and id = :roomid ",array(":uniacid"=>$uniacid,":roomid"=>$roomid));
        if(empty($room)){
            $this->message('指定直播间不存在','','error');
        }
        //自定义选项卡
        $menu = unserialize($room['tabs']);
        $nestables = unserialize($room['nestable']);
        if(empty($nestables)){
            $nestables = array(
                0=>'interaction',
                1=>'goods',
                2=>'introduce',
            );
        }
        /*
         * 会员权限
         * */
        $member = m('member')->getMember($openid);
        $showlevels = $room['showlevels']!='' ? explode(',', $room['showlevels']) : array();
        $showgroups = $room['showgroups']!='' ? explode(',', $room['showgroups']) : array();
        $showroom = 0;
        if(!empty($member)){
            if((!empty($showlevels)&&in_array($member['level'], $showlevels)) || (!empty($showgroups) && in_array($member['groupid'], $showgroups)) || (empty($showlevels) && empty($showgroups))){
                $showroom = 1;
            }
        }else{
            if(empty($showlevels) && empty($showgroups)){
                $showroom = 1;
            }
        }
        /*
         * 分销商等级浏览权限
         * */
        $plugin_commission = p('commission');
        if ($plugin_commission) {
            $set = $plugin_commission->getSet();
            //分销商等级
            if ($room['showcommission']) {
                $arr = explode(',', $room['showcommission']);
                if (!in_array($member['agentlevel'], $arr)) {
                    header("location: " . mobileUrl('live/room/jurisdiction',array('roomid'=>$room['id'])));
                    exit;
                }
            }
        }
        if(empty($showroom)){
            //$this->jurisdiction('您没有权限浏览该直播间',mobileUrl('live'),'error');
            header("location: " . mobileUrl('live/room/jurisdiction',array('roomid'=>$room['id'])));
            exit;
        }



        if($room['covertype']==1){
            $room['thumb'] = tomedia($room['cover']);
        }
        if($room['livetype']==2){
            $room['url'] = $room['video'];
        }

        // 获取直播间商品列表
        $room_goods = $this->model->getAllGoods($roomid);

        /*优惠券*/
        $coupon = false;
        if(!empty($room['couponid'])){
            $coupon = true;
            $room_coupon = pdo_fetchall("select lc.*, sc.couponname, sc.coupontype,sc.backtype ,sc.enough,sc.deduct from ".tablename('ewei_shop_live_coupon')." lc left join ". tablename('ewei_shop_coupon')." sc on sc.id=lc.couponid where lc.uniacid=:uniacid and lc.roomid=:roomid ", array(':uniacid'=>$_W['uniacid'], ':roomid'=>$roomid));
            foreach ($room_coupon as $key=>&$coupon){
                if($coupon['backtype']==0)
                {
                    if($coupon['enough']=='0'){
                        $coupon['color']='orange ';
                    }else{
                        $coupon['color']='blue';
                    }
                    $coupon['display_title']='减'.(price_format($coupon['deduct'],2)).'元';
                }
                if($coupon['backtype']==1){
                    $coupon['color']='red ';
                    $coupon['display_title']='打'.(price_format($coupon['discount'],2)).'折 ';
                }
                if($coupon['backtype']==2){
                    if($coupon['coupontype']=='0'||$coupon['coupontype']=='2'){
                        $coupon['color']='red ';
                    }else{
                        $coupon['color']='pink ';
                    }
                    if (!empty($coupon['backmoney']) && $coupon['backmoney'] > 0) {
                        $coupon['display_title'] =  $coupon['display_title'].'送'.$coupon['backmoney'].'元余额 ';
                    }
                    if (!empty($coupon['backcredit']) && $coupon['backcredit'] > 0) {
                        $coupon['display_title'] =  $coupon['display_title'].'送'.$coupon['backcredit'].'积分 ';
                    }
                    if (!empty($coupon['backredpack']) && $coupon['backredpack'] > 0) {
                        $coupon['display_title'] =  $coupon['display_title'].'送'.$coupon['backredpack'].'元红包 ';
                    }
                }

                /*
                 * 直播间优惠券redis
                 * */
                $table_roomcoupon = $this->model->getRedisTable('room_coupon_'.$coupon['couponid'].'_'. $room['livetime'], $roomid);
                $couponset = redis()->hGet($table_roomcoupon,'couponset');
                $couponset = json_decode($couponset,true);
                $coupon['iscoupon'] = true;

                /*
                 * redis判断优惠券数量
                 * */
                if($couponset['coupontotal'] <= $couponset['receivetotal']){
                    $coupon['iscoupon'] = false;
                    $coupon['couponmessage'] = '优惠券已领完';
                }
                /*
                 * 优惠券领取redis list、hash
                 * */
                $table_roomcoupon_hash = $this->model->getRedisTable('roomcoupon_hash_'.$room['livetime'].'_'.$coupon['couponid'].'', $roomid);
                $coupon_total = $couponset['receivetotal'];//已领取优惠券数量
                $roomcoupon = redis()->hGet($table_roomcoupon_hash,$openid);
                $roomcoupon = json_decode($roomcoupon,true);
                if(!empty($roomcoupon)){
                    if(floatval($roomcoupon['limit']) >= intval($couponset['couponlimit']) && intval($couponset['couponlimit']) > 0 ){
                        $coupon['iscoupon'] = false;
                        $coupon['couponmessage'] = '您已领取过了';
                    }else{
                        //优惠券领取限制
                        if(intval($couponset['couponlimit']) > 0){
                            if(intval($roomcoupon['limit']) >= intval($couponset['couponlimit'])){
                                $coupon['iscoupon'] = false;
                                $coupon['couponmessage'] = '您已领取过了';
                            }
                        }
                    }
                }else{
                    $coupon['iscoupon'] = true;
                }

            }
            unset($coupon);
        }

        /*红包*/
        $packet = false;
        if($room['packetmoney']>0){
            $packet = true;
        }

        // 获取会员信息
        $member = m('member')->getMember($_W['openid']);

        // 获取关注状态
        $favorite = $this->model->isFavorite($_W['openid'], $roomid);


        // 表情列表
        $emojiList = $this->model->getEmoji();

        // 聊天记录
        $records = $this->model->handleRecords($roomid, false, $member['id']);

        // 是否全屏
        $fullscreen = intval($room['screen']);
        $video = trim($room['url']);
        $poster = $room['thumb'];
        if(!empty($video) && $room['livetype'] != 2){
            $video_info = $this->model->getLiveInfo($video, $room['liveidentity']);
            if(!is_error($video_info) && !empty($video_info['hls_url'])){
                $video = $video_info['hls_url'];
                pdo_update("ewei_shop_live",array('thumb'=>$video_info['poster']),array('uniacid'=>$uniacid,'id'=>$roomid));
            }else{
                $video = '';
            }
        }
        /*dump($video_info);
        die();*/

        // 通讯地址
        $wsConfig = json_encode(array(
            'address'=>$this->model->getWsAddress(),
            'roomid'=>$roomid,
            'uniacid'=>$_W['uniacid'],
            'openid'=>$_W['openid'],
            'uid'=>$member['id'],
            'nickname'=>$member["nickname"],
            'attachurl'=>$_W['attachurl'],
            'isMobile'=>is_mobile(),
            'isIos'=>is_ios(),
            'fullscreen'=>$fullscreen,
            'siteroot'=>$_W['siteroot']
        ));

        $moneytext = empty($_W['shopset']['trade']['moneytext'])? '余额': $_W['shopset']['trade']['moneytext'];

        /*
         * 写入观看记录
         * */
        //访问量
        pdo_update("ewei_shop_live",array('visit'=>$room['visit']+1),array('uniacid'=>$uniacid,'id'=>$roomid));
        $view = pdo_fetch("select * from ".tablename('ewei_shop_live_view')." where uniacid = ".$uniacid." and openid = '".$openid."' and roomid = ".$roomid." ");
        $viewing = pdo_fetch("select max(viewing) as viewing from ".tablename('ewei_shop_live_view')." where uniacid = ".$uniacid." and openid = '".$openid."' ");
        if(!empty($view)){
            pdo_update("ewei_shop_live_view",array('viewing'=>$viewing['viewing']+1),array('uniacid'=>$uniacid,'openid'=>$openid,'roomid'=>$roomid));
        }else{
            $view_data = array(
                'uniacid'=>$uniacid,
                'openid'=>$openid,
                'roomid'=>$roomid,
            );
            $view_data['viewing'] = !empty($viewing) ? $viewing['viewing'] + 1 : 1 ;
            pdo_insert("ewei_shop_live_view",$view_data);
        }

        $sysset = m('common')->getSysset();

        $shop = set_medias($sysset['shop'], 'logo');
        $setting = pdo_fetch("select * from ".tablename('ewei_shop_live_setting')." where uniacid = :uniacid  ",array(":uniacid"=>$uniacid));

        $followqrcode = !empty($room['followqrcode'])? $room['followqrcode']: $sysset['share']['followqrcode'];
        if(!empty($followqrcode)){
            $followqrcode = tomedia($followqrcode);
        }

        $_W['shopshare'] = array(
            'title' => !empty($room['share_title']) ? $room['share_title'] : $shop['name'] ,
            'imgUrl' => !empty($room['share_icon']) ? tomedia($room['share_icon']) : tomedia($room['thumb']),
            'link' => !empty($room['share_url']) ? $room['share_url'] : mobileUrl('live/room', array('id'=>$roomid), true),
            'desc' => !empty($room['share_desc']) ? $room['share_desc'] : $room['title']
        );

        include $this->template();
    }

    public function jurisdiction(){
        global $_W, $_GPC;
        $uniacid = intval($_W['uniacid']);
        $roomid = intval($_GPC['roomid']);
        $room = pdo_fetch("SELECT jurisdictionurl_show,jurisdiction_url FROM ". tablename('ewei_shop_live')." where id = :roomid and uniacid = :uniacid ",array(":uniacid"=>$uniacid,":roomid"=>$roomid));
        if(empty($room)){
            $this->message('指定直播间不存在','','error');
        }

        include $this->template('live/jurisdiction');
    }

    public function favorite() {
        global $_W, $_GPC;

        $roomid = intval($_GPC['roomid']);

        if(!empty($roomid)){
            $favorite = pdo_fetch('SELECT * FROM '. tablename('ewei_shop_live_favorite'). 'WHERE uniacid=:uniacid AND roomid=:roomid AND openid=:openid LIMIT 1', array(
                ':uniacid'=>$_W['uniacid'],
                ':roomid'=>$roomid,
                ':openid'=>$_W['openid'],
            ));
            if(empty($favorite)){
                pdo_insert('ewei_shop_live_favorite', array(
                    'uniacid'=>$_W['uniacid'],
                    'roomid'=>$roomid,
                    'openid'=>$_W['openid'],
                    'createtime'=>time()
                ));
                //$this->model->sendLiveMessage($_W['openid'],'livefollow',$roomid);
                pdo_query('update '.tablename('ewei_shop_live').' set subscribe = subscribe+1 where id = '.$roomid.' and uniacid = '.intval($_W['uniacid']).' ');
                show_json(1, array('favorite'=>1));
            }else{
                pdo_update('ewei_shop_live_favorite', array(
                    'deleted'=>!empty($favorite['deleted'])? 0 :1,
                    'createtime'=>time()
                ), array(
                    'id'=>$favorite['id']
                ));
                if(empty($favorite['deleted'])){
                    //$this->model->sendLiveMessage($_W['openid'],'livefollow',$roomid);
                    pdo_query('update '.tablename('ewei_shop_live').' set subscribe = subscribe-1 where id = '.$roomid.' and uniacid = '.intval($_W['uniacid']).' ');
                }else{
                    pdo_query('update '.tablename('ewei_shop_live').' set subscribe = subscribe+1 where id = '.$roomid.' and uniacid = '.intval($_W['uniacid']).' ');
                }
                show_json(1, array('favorite'=>!empty($favorite['deleted'])? 1 :0));
            }
        }

        show_json(0, '参数错误');
    }

    public function draw() {
        global $_W, $_GPC;

        $type = trim($_GPC['type']);

    }
    /*
     * 直播间优惠券
     * */
    function roomcoupon(){
        global $_W, $_GPC;
        $uniacid = intval($_W['uniacid']);
        $openid = trim($_W['openid']);
        $roomid = intval($_GPC['roomid']);//直播间id
        $couponid = intval($_GPC['couponid']);//优惠券id
        $livetime = intval($_GPC['livetime']);//直播时间

        $open_redis = function_exists('redis') && !is_error(redis());
        if( $open_redis ) {
            $redis_key = "{$_W['setting']['site']['key']}_{$_W['account']['key']}_{$uniacid}_{$roomid}_roomcoupon_{$openid}";
            $redis = redis();
            if (!is_error($redis)) {
                if ($redis->setnx($redis_key, time())) {
                    $redis->expireAt($redis_key, time() + 3);
                } else {
                    show_json(0,array('status'=>'-1','message'=>'操作频繁，请稍后再试!'));
                }
            }
        }

        /*
         * 直播间优惠券redis
         * */
        $table_roomcoupon = $this->model->getRedisTable('room_coupon_'.$couponid.'_'. $livetime, $roomid);
        $couponset = redis()->hGet($table_roomcoupon,'couponset');
        $room_coupon = array();//直播间优惠券
        if(empty($couponset)){
            /*
             * 获取优惠券信息存入redis
             * */
            $room_coupon = pdo_fetch("select lc.couponid,lc.coupontotal,lc.couponlimit,l.living,l.iscoupon,c.couponname from ".tablename('ewei_shop_live_coupon')." lc
                    left join ".tablename('ewei_shop_live')." as l on l.id = lc.roomid
                    left join ".tablename('ewei_shop_coupon')." as c on c.id = lc.couponid
                    where lc.roomid = :roomid and lc.couponid = :couponid and lc.uniacid = :uniacid ",array(":uniacid"=>$uniacid,":roomid"=>$roomid,":couponid"=>$couponid));
            $couponset = array(
                'title'=>trim($room_coupon['couponname']),
                'couponid'=>intval($room_coupon['couponid']),
                'coupontotal'=>floatval($room_coupon['coupontotal']),
                'couponlimit'=>intval($room_coupon['couponlimit']),
                'receivetotal'=>0,
                'livetime'=>intval($livetime),
            );
            $couponset = json_encode($couponset);
            $couponset = redis()->hSet($table_roomcoupon,'couponset',$couponset);
            $couponset = redis()->hGet($table_roomcoupon,'couponset');
        }
        $couponset = json_decode($couponset,true);

        /*
         * redis判断优惠券数量
         * */
        if($couponset['coupontotal'] <= $couponset['receivetotal']){
            show_json(-2, '优惠券已领完！');
        }
        /*
         * 优惠券领取redis list、hash
         * */
        $table_roomcoupon_hash = $this->model->getRedisTable('roomcoupon_hash_'.$livetime.'_'.$couponid.'', $roomid);

        $coupon_total = $couponset['receivetotal'];//已领取优惠券数量
        $roomcoupon = redis()->hGet($table_roomcoupon_hash,$openid);
        /*$roompack = redis()->hGetAll($table_roompack_hash);*/
        //show_json(0, $roomcoupon);
        if(empty($roomcoupon)){
            $coupon_log = array(
                'limit'=>0,
                'time'=>0
            );
            $coupon_log = json_encode($coupon_log);
            $couponHash = redis()->hSet($table_roomcoupon_hash,$openid,$coupon_log);
            $roomcoupon = redis()->hGet($table_roomcoupon_hash,$openid);

        }
        $roomcoupon = json_decode($roomcoupon,true);
        if(floatval($roomcoupon['limit']) >= intval($couponset['couponlimit']) && intval($couponset['couponlimit']) > 0 ){
            show_json(-3, '您已领取过了！');//status = -3 您已领取过了
        }else{
            //优惠券领取限制
            if(intval($couponset['couponlimit']) > 0){
                if(intval($roomcoupon['limit']) < intval($couponset['couponlimit'])){
                    //m('member')->setCredit($openid, 'credit2', $packetset['packetprice'], array('0', "【".$packetset['title']."】直播间领取余额红包: ".price_format($packetset['packetprice'],2)."元 "));
                    $member = m('member')->getMember($openid);
                    com("coupon")->poster($member,$couponid,1,11);//直播间领取优惠券
                    /*
                     * 更新优惠券领取redis
                     * */
                    $roomcoupon['limit'] = $roomcoupon['limit'] + 1;
                    $roomcoupon['time'] = time();
                    $roomcoupon = json_encode($roomcoupon);
                    $couponHash = redis()->hSet($table_roomcoupon_hash,$openid,$roomcoupon);
                    /*
                     * 更新直播间优惠券redis
                     * */
                    $couponset['receivetotal']++;
                    $couponset = json_encode($couponset);
                    $couponset = redis()->hSet($table_roomcoupon,'couponset',$couponset);
                }else{
                    show_json(-3, "您已领取过了！");//status = -2 优惠券领完
                }
            }else{
                //优惠券领取无限制
                $member = m('member')->getMember($openid);
                com("coupon")->poster($member,$couponid,1,11);//直播间领取优惠券
                /*
                 * 更新优惠券领取redis
                 * */
                $roomcoupon['limit'] = $roomcoupon['limit'] + 1;
                $roomcoupon['time'] = time();
                $roomcoupon = json_encode($roomcoupon);
                $couponHash = redis()->hSet($table_roomcoupon_hash,$openid,$roomcoupon);
                /*
                 * 更新直播间优惠券redis
                 * */
                $couponset['receivetotal']++;
                $couponset = json_encode($couponset);
                $couponset = redis()->hSet($table_roomcoupon,'couponset',$couponset);
            }

        }

        //访问量
        pdo_query('update '.tablename('ewei_shop_live').' set coupon_num = coupon_num+1 where id= '.$roomid.' and uniacid = '.$uniacid.' ');
        /*show_json(0, $roompack);*/

        show_json(1);
    }
    /*
     * 直播间红包领取
     * */
    function roompacket(){
        global $_W, $_GPC;
        $uniacid = intval($_W['uniacid']);
        $openid = trim($_W['openid']);
        $roomid = intval($_GPC['roomid']);//直播间id
        $livetime = intval($_GPC['livetime']);//直播时间

        $open_redis = function_exists('redis') && !is_error(redis());
        if( $open_redis ) {
            $redis_key = "{$_W['setting']['site']['key']}_{$_W['account']['key']}_{$uniacid}_{$roomid}_roompacket_{$openid}";
            $redis = redis();
            if (!is_error($redis)) {
                if ($redis->setnx($redis_key, time())) {
                    $redis->expireAt($redis_key, time() + 3);
                } else {
                    show_json(0,array('status'=>'-1','message'=>'操作频繁，请稍后再试!'));
                }
            }
        }

        /*
         * 直播间红包redis
         * */
        $table_packset = $this->model->getRedisTable('packset_'. $livetime, $roomid);
        $packetset = redis()->hGet($table_packset,'packset');
        if(empty($packetset)){
            /*
             * 获取红包信息存入redis
             * */
            $packet = pdo_fetch("select title,packetmoney,packettotal,packetprice,living,livetime from ".tablename('ewei_shop_live')." where id = :roomid and status = 1 and uniacid = :uniacid ",array(":uniacid"=>$uniacid,":roomid"=>$roomid));
            $packetset = array(
                'title'=>trim($packet['title']),
                'packetmoney'=>floatval($packet['packetmoney']),
                'packettotal'=>intval($packet['packettotal']),
                'receivemoney'=>0,
                'receivetotal'=>0,
                'packetprice'=>floatval($packet['packetprice']),
                'livetime'=>intval($packet['livetime']),
            );
            $packetset = json_encode($packetset);
            $packetset = redis()->hSet($table_packset,'packset',$packetset);
            $packetset = redis()->hGet($table_packset,'packset');
        }
        $packetset = json_decode($packetset,true);
        /*
         * redis判断红包数量
         * */
        if($packetset['packetmoney'] < $packetset['receivemoney']){
            show_json(0, '红包金额不足！');
        }
        if($packetset['packettotal'] < $packetset['receivetotal']){
            show_json(0, '红包数量不足！');
        }
        /*
         * 红包领取redis list、hash
         * */
        $table_roompack_list = $this->model->getRedisTable('roompack_list_'. $livetime, $roomid);
        $table_roompack_hash = $this->model->getRedisTable('roompack_hash_'. $livetime, $roomid);

        $packet_total = redis()->lLen($table_roompack_list);
        $roompack = redis()->hGet($table_roompack_hash,$openid);
        /*$roompack = redis()->hGetAll($table_roompack_hash);*/
        /*show_json(0, $roompack);*/
        if(empty($roompack)){
            if(intval($packet_total) < $packetset['packettotal']){
                $packList = redis()->rPush($table_roompack_list,$openid);
                $index = redis()->lLen($table_roompack_list);
                if($index <= $packetset['packettotal']){
                    $packet_log = array(
                        'money'=>0,
                        'time'=>0,
                        'index'=>$index,
                    );
                    $packet_log = json_encode($packet_log);
                    $packHash = redis()->hSet($table_roompack_hash,$openid,$packet_log);
                    $roompack = redis()->hGet($table_roompack_hash,$openid);
                }else{
                    show_json(-2, "红包已领完！");//status = -2 红包领完
                }
            }else{
                show_json(-2, "红包已领完！");
            };

        }else{
            $roompack = json_decode($roompack,true);
            if(floatval($roompack['money'])>0){
                show_json(-3, '您已领取过了！');//status = -3 您已领取过了
            }else{
                if(intval($roompack['index'])<=$packetset['packettotal']){
                    m('member')->setCredit($openid, 'credit2', $packetset['packetprice'], array('0', "【".$packetset['title']."】直播间领取余额红包: ".price_format($packetset['packetprice'],2)."元 "));
                    /*
                     * 更新红包领取redis
                     * */
                    $roompack['money'] = $packetset['packetprice'];
                    $roompack['time'] = time();
                    $roompack = json_encode($roompack);
                    $packHash = redis()->hSet($table_roompack_hash,$openid,$roompack);
                    /*
                     * 更新直播间红包redis
                     * */
                    $packetset['receivetotal'] = $packetset['receivetotal']+1;
                    $packetset['receivemoney'] = floatval($packetset['receivemoney']) + floatval($packetset['packetprice']);
                    $packetset = json_encode($packetset);
                    $packetset = redis()->hSet($table_packset,'packset',$packetset);
                }else{
                    show_json(-2, "红包已领完！");//status = -2 红包领完
                }
            }
        }

        /*show_json(0, $roompack);*/

        show_json(1);
    }

}
