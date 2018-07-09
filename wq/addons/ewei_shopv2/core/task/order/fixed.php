<?php
/*
 * 人人商城V2
 * 
 * @author ewei 狸小狐 QQ:22185157 
 */
error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/ewei_shopv2/defines.php';
require '../../../../../addons/ewei_shopv2/core/inc/functions.php';
global $_W, $_GPC;
ignore_user_abort(); //忽略关闭浏览器
set_time_limit(0); //永远执行
//    //没过期的核销商品为1
//    pdo_query("update " . tablename('ewei_shop_order') . ' set status = 1 , canceltime = 0 where `isverify` = 1 and `verifyendtime` = 0 and `verifytime` = 0 and `verified` = 0 and `paytype` > 0 and status <> 1');
//    //已完成为3
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = 3 , canceltime = 0 where `isverify` = 1 and (`verified` = 1 or (verifyinfo is not null and verifyinfo<>'a:0:{}')) and `paytype` > 0 and status <> 3");
//    //未使用为1
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = 1 , canceltime = 0 where `isverify` = 1 and `verifytime` = 0 and (`verified` = 0 and (verifyinfo is null or verifyinfo='a:0:{}')) and `paytype` > 0 and status <> 1");
//    //已发货设置为2
//    pdo_query("update " . tablename('ewei_shop_order') . ' set status = 2 , canceltime = 0 where `isverify` = 0 and sendtime > 0 and `paytype` > 0 and status <> 2');
//    //未发货设置为1
//    pdo_query("update " . tablename('ewei_shop_order') . ' set status = 1 , canceltime = 0 where `isverify` = 0 and sendtime = 0 and `paytype` > 0 and status <> 1');
//    //未支付设为0
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = 0 , canceltime = 0 where status = 1 and paytype = 0 and `verifyendtime` = 0 and `verifytime` = 0 and `verified` = 0 and status <> 0");
//    //維權狀態爲-1
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = -1 where refundtime<>0");
//    //
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = 3 where finishtime>0 and status <> 3");
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = -1 where canceltime >0");
//    pdo_query("update " . tablename('ewei_shop_order') . " set status = -1 where (paytime=0 and status>0) and createtime + {$daytimes}<=unix_timestamp()");
//   pdo_query("update " . tablename('ewei_shop_order') . " set status = 0 where (paytime=0 and status>0) and createtime + {$daytimes}>unix_timestamp()");
//
//     pdo_query("update " . tablename('ewei_shop_order') . " set status = 1 where `isverify` = 1 and sendtime = 0 and `verified` = 0 and `verifytime` = 0");




$table = tablename('ewei_shop_order');
//全部关闭
pdo_query("update {$table} set status = -1 where createtime <1501257600");
//待发货
pdo_query("update {$table} set status = 1 where (paytime > 0 or paytype =3) and (sendtime = 0 and sendtype = 0) and createtime <1501257600");
//待收货
pdo_query("update {$table} set status = 2 where (paytime>0 or paytype = 3) and (sendtime > 0 or sendtype >0) and finishtime = 0 and createtime <1501257600");
//已完成
pdo_query("update {$table} set status = 3 where (paytime>0  or paytype = 3) and (sendtime >0 or sendtype >0) and finishtime >0 and createtime <1501257600");
//待付款
pdo_query("update {$table} set status = 0 where (paytime = 0 and paytype <>3) and canceltime = 0 and createtime + 86400 <= unix_timestamp() and createtime <1501257600");
//维权中
pdo_query("update {$table} set refundstate = 0,status = -1 where refundtime <> 0 and createtime <1501257600");





$sets = pdo_fetchall('select uniacid from ' . tablename('ewei_shop_sysset'));
foreach ($sets as $set) {

    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])) {
        continue;
    }
    $trade = m('common')->getSysset('trade', $_W['uniacid']);
    $days = intval($trade['closeorder']);
    if ($days <= 0) {
        //不自动关闭订单
        continue;
    }

    $daytimes = 86400 * $days;
    $orders = pdo_fetchall("select id,openid,deductcredit2,ordersn,isparent,deductcredit,deductprice,status,isparent,isverify from " . tablename('ewei_shop_order') . " where uniacid={$_W['uniacid']}  and paytype<>3  and ((createtime + {$daytimes} <=unix_timestamp() and status=0) or (status = 1 and `isverify` = 1 and `verifyendtime` <= unix_timestamp() and `verifyendtime` > 0))");

    $p = com('coupon');
    foreach ($orders as $o) {
        if($o['status']==0){
            //检查是否是代付订单
            $isPeerpay = m('order')->checkpeerpay($o['id']);
            //如果是多商户父订单则跳过返还优惠和积分
            if ($o['isparent'] == 0) {
                if ($p) {
                    //退还优惠券
                    if (!empty($o['couponid'])) {
                        $p->returnConsumeCoupon($o['id']); //自动关闭订单
                    }
                }

                //处理订单库存及用户积分情况
                m('order')->setStocksAndCredits($o['id'], 2);

                //返还抵扣余额
                m('order')->setDeductCredit2($o);

                //返还抵扣积分
                if ($o['deductprice'] > 0) {
                    m('member')->setCredit($o['openid'], 'credit1', $o['deductcredit'], array('0', $_W['shopset']['shop']['name'] . "自动关闭订单返还抵扣积分 积分: {$o['deductcredit']} 抵扣金额: {$o['deductprice']} 订单号: {$o['ordersn']}"));
                }
            }
            //如果是代付要退款
            if (!empty($isPeerpay)){
                $refundsql = "SELECT * FROM ".tablename("ewei_shop_order_peerpay_payinfo")." WHERE pid = :pid";
                $refundlist = pdo_fetchall($refundsql, array(':pid'=>$isPeerpay['id']));
                foreach ($refundlist as $k => $v){
                    $openid = $v['openid'];
                    if (!empty($openid) && !empty($v['tid'])) {//微信退款
                        $result = m('finance')->pay($openid, 1, $v['price'] * 100, $o['ordersn'], "退款: ".$v['price']."元 订单号: " . $o['ordersn']);
                        if (is_error($result)){//微信退款失败
                            m('member')->setCredit($openid, 'credit2', $v['price'], array(0, "退款: {$v['price']}元 订单号: " . $o['ordersn']));
                        }
                    }else{//余额退款
                        m('member')->setCredit($openid, 'credit2', $v['price'], array(0, "退款: {$v['price']}元 订单号: " . $o['ordersn']));
                    }
                }
            }
            pdo_query("update " . tablename('ewei_shop_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
        }elseif ($o['status'] == 1 && $o['isverify'] == 1){
            //如果是核销码过期的订单则关闭
            pdo_query("update " . tablename('ewei_shop_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
        }
    }
}

die('补丁运行成功，请检查订单状态是否已经修复，如有异常订单请联系客服');