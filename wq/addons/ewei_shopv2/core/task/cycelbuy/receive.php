<?php
/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/ewei_shopv2/defines.php';
require '../../../../../addons/ewei_shopv2/core/inc/functions.php';
global $_W, $_GPC;

ignore_user_abort(); //忽略关闭浏览器
set_time_limit(0); //永远执行

$cycel = m('common')->getSysset('cycelbuy',$_GPC['uniacid']);
$p = p('commission');
$pcoupon = com('coupon');
$_W['uniacid'] = $_GPC['uniacid'];
$trade = m('common')->getSysset('trade', $_W['uniacid']);
$days = intval($trade['receive']);
$receive_goods = empty($cycel['receive_goods']) ? $days : $cycel['receive_goods'];
if(empty($receive_goods)){
    return false;
}
$order = pdo_fetchall("select id,openid,deductcredit2,price,address,ordersn,isparent,deductcredit,deductprice,status,isparent,isverify,`virtual`,`virtual_info`,createtime,cycelbuy_periodic from " . tablename('ewei_shop_order') . " where uniacid={$_W['uniacid']}  and paytype<>3   and status=2 and iscycelbuy = 1");
if(!empty($order)){


    foreach($order as $k => $v){
        $last_periods= pdo_fetch("select * from " . tablename('ewei_shop_cycelbuy_periods') . ' where uniacid=:uniacid and orderid=:orderid order by id desc  limit 1', array(':uniacid' => $_W['uniacid'], ':orderid' => $v['id']));
        $cycel = pdo_fetchall('select * from ' .tablename('ewei_shop_cycelbuy_periods'). ' where  orderid = '.$v['id'].' and status = 1 and uniacid = '.$_W['uniacid'].' order by receipttime asc limit 1');

        $days = 86400 * $receive_goods;
        $sendtime = $cycel[0]['sendtime'];
        if($sendtime+$days >= time()){

            if(!empty($last_periods)){
                if($last_periods['id']==$cycel[0]['id']){
                    pdo_update('ewei_shop_cycelbuy_periods', array( 'status' => 2,'finishtime'=>time()), array('orderid' => $v['id'], 'uniacid' => $_W['uniacid']));
                    pdo_update('ewei_shop_order', array('status' => 3,'finishtime'=>time()), array('id' =>$v['id'],'status'=>2));
                    //会员升级
                    m('member')->upgradeLevel($v['openid'], $v['id']);
                    //余额赠送
                    m('order')->setGiveBalance($v['id'], 1);
                    //模板消息
                    m('notice')->sendOrderMessage($v['id']);
                    //商品全返
                    m('order')->fullback($v['id']);
                    //处理积分
                    m('order')->setStocksAndCredits($v['id'], 3);
//                    优惠券返利
                    if ($pcoupon) {
                        //发送赠送优惠券
                        com('coupon')->sendcouponsbytask($v['id']); //订单支付

                        if (!empty($order['couponid'])) {
                            $pcoupon->backConsumeCoupon($v['id']); //自动收货
                        }
                    }
                    //分销检测
                    if ($p) {
                        $p->checkOrderFinish($v['id']);
                    }

                }else{
                    pdo_update('ewei_shop_cycelbuy_periods', array( 'status' => 2,'finishtime'=>time()), array('id' => $cycel[0]['id'], 'uniacid' => $_W['uniacid']));
                }
            }

        }
    }
}





