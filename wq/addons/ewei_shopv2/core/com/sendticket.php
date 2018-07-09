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

class Sendticket_EweiShopV2ComModel extends ComModel {

    //获取是否发送优惠券状态
    function getInfo(){
        global $_W,$_GPC;
        $openid = $_W['openid'];

        if(!com('coupon')){
            return false;
        }

        $member =  m('member')->getMember($_W['openid']);
        $condition = ' WHERE uniacid = :uniacid AND openid = :openid';
        $paramso = array(
            ':uniacid' => intval($_W['uniacid']),
            ':openid' => trim($openid),
        );
        $osql = 'SELECT * FROM '.tablename('ewei_shop_order').$condition;
        $order = pdo_fetchall($osql,$paramso);
        //根据余额和订单判断是否是新用户
        if (empty($order)) {

            $sql2 = 'SELECT * FROM '.tablename('ewei_shop_sendticket').' WHERE uniacid = '.intval($_W['uniacid']);
            $ticket = pdo_fetch($sql2);
            if ($ticket['status'] == 1) {
                if ($ticket['expiration'] == 1) {
                    if (TIMESTAMP > $ticket['endtime']) {
                        $status = array('status' => 0);
                        pdo_update('ewei_shop_sendticket',$status,array('id' => $ticket['id']));
                        return false;
                    } else {

                        $cpinfo = $this -> getCoupon($ticket['cpid']);

                        if (empty($cpinfo)) {
                            return false;
                        } else {
                            $insert = $this -> insertDraw($openid,$cpinfo);
                            if ($insert) {
                                if(count($cpinfo) == count($cpinfo, 1)){
                                        $status = $this -> sendTicket($openid,$cpinfo['id'],14);
                                        if (!$status) {
                                            return false;
                                        } else {
                                            $cpinfo['did'] = $status;
                                        }
                                }else{
                                    foreach ($cpinfo as $cpk => $cpv) {
                                        $status = $this -> sendTicket($openid,$cpv['id'],14);
                                        if (!$status) {
                                            return false;
                                        } else {
                                            $cpinfo[$cpk]['did'] = $status;
                                        }
                                    }
                                }

                                return $cpinfo;
                            } else {
                                return false;
                            }
                        }
                    }
                } else {
                    $cpinfo = $this -> getCoupon($ticket['cpid']);
                    if (empty($cpinfo)) {
                        return false;
                    } else {
                        $insert = $this -> insertDraw($openid,$cpinfo);
                        if ($insert) {
                            if(count($cpinfo) == count($cpinfo, 1)){
                                $status = $this -> sendTicket($openid,$cpinfo['id'],14);
                                if (!$status) {
                                    return false;
                                } else {
                                    $cpinfo['did'] = $status;
                                }
                            }else{
                                foreach ($cpinfo as $cpk => $cpv) {
                                    $status = $this -> sendTicket($openid,$cpv['id'],14);
                                    if (!$status) {
                                        return false;
                                    } else {
                                        $cpinfo[$cpk]['did'] = $status;
                                    }
                                }
                            }

                            return $cpinfo;
                        } else {
                            return false;
                        }
                    }
                }
            } else if ($ticket['status'] == 0) {
                return false;
            }
        } else {
            return false;
        }

    }

    //获取优惠券信息
    function getCoupon($cpid){
        global $_W,$_GPC;
        if (strpos($cpid,',')) {
            $cpids = explode(',',$cpid);
        } else {
            $cpids = $cpid;
        }

        if(is_array($cpids)){
            $cpinfo = array();
            foreach ($cpids as $cpk => $cpv) {
                $cpsql = 'SELECT * FROM '.tablename('ewei_shop_coupon').' WHERE uniacid = '.intval($_W['uniacid']).' AND id = '.intval($cpv);
                $list = pdo_fetch($cpsql);
                if($list['timelimit'] == 1) {
                    if (TIMESTAMP < $list['timeend']) {
                        $cpinfo[$cpk] = $list;
                    }
                }else if($list['timelimit'] == 0){
                    $cpinfo[$cpk] = $list;
                }

            }
            return $cpinfo;
        }else {
            $cpsql = 'SELECT * FROM '.tablename('ewei_shop_coupon').' WHERE uniacid = '.intval($_W['uniacid']).' AND id = '.intval($cpid);
            $cpinfo = pdo_fetch($cpsql);
            return $cpinfo;
        }
    }

//    //插入领取记录
//    function insertDraw($openid,$cpinfo){
//        global $_W,$_GPC;
//        $drawsql = 'SELECT * FROM '.tablename('ewei_shop_sendticket_draw').' WHERE uniacid = :uniacid AND openid = :openid';
//        $drawparpams = array(
//            ':uniacid' => intval($_W['uniacid']),
//            ':openid' => trim($openid),
//        );
//
//        $drawdata = pdo_fetch($drawsql,$drawparpams);
//
//        if (empty($drawdata)) {
//            $drawcpid = array();
//            if (count($cpinfo) == count($cpinfo, 1)) {
//                foreach ($cpinfo as $cpk => $cpv) {
//                    $drawcpid[$cpk] = $cpv;
//                }
//                //$drawcpids = trim(implode(',',$drawcpid));
//                $drawcpids = $cpinfo['id'];
//            }else{
//                foreach ($cpinfo as $cpk => $cpv) {
//                    $drawcpid[$cpk] = $cpv['id'];
//                }
//                $drawcpids = trim(implode(',',$drawcpid));
//
//            }
//
//            $data = array(
//                'uniacid' => intval($_W['uniacid']),
//                'cpid' => $drawcpids,
//                'openid' => trim($openid),
//                'createtime' => TIMESTAMP,
//            );
//            $insert = pdo_insert('ewei_shop_sendticket_draw',$data);
//            return $insert;
//        } else {
//            return false;
//        }
//    }

    //发送优惠卷
    function sendTicket($openid, $couponid,$gettype=0) {
        global $_W, $_GPC;
        //增加优惠券日志
        $couponlog = array(
            'uniacid' => $_W['uniacid'],
            'openid' => $openid,
            'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'),
            'couponid' => $couponid,
            'status' => 1,
            'paystatus' => -1,
            'creditstatus' => -1,
            'createtime' => time(),
            'getfrom' => 3
        );
        $log = pdo_insert('ewei_shop_coupon_log', $couponlog);

        //增加用户优惠券
        $data = array(
            'uniacid' => $_W['uniacid'],
            'openid' => $openid,
            'couponid' => $couponid,
            'gettype' => $gettype,
            'gettime' => time()
        );
        $data = pdo_insert('ewei_shop_coupon_data', $data);
        $did = pdo_insertid();
        if ($log && $data) {
            return $did;
        } else {
            return false;
        }

    }

    //支付是否达到条件领取优惠券
    function share($money){
        $activity = $this -> activity($money);
        if (!empty($activity)) {
            return true;
        }else{
            return false;
        }
    }

    function activity($money){
        global $_W;
        $sql = 'SELECT * FROM '.tablename('ewei_shop_sendticket_share').' WHERE uniacid = '.intval($_W['uniacid']).' AND status = 1 AND (enough = '.$money.' OR enough <= '.$money.') AND (expiration = 0 OR (expiration = 1 AND endtime >= '.TIMESTAMP.')) ORDER BY enough DESC,createtime DESC LIMIT 1';
        $activity = pdo_fetch($sql);
        return $activity;
    }

}
