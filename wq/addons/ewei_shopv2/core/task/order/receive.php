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
require '../../../../../addons/ewei_shopv2/core/inc/plugin_model.php';
global $_W, $_GPC;

ignore_user_abort(); //忽略关闭浏览器
set_time_limit(0); //永远执行

$sets = pdo_fetchall('select uniacid from ' . tablename('ewei_shop_sysset'));
foreach ($sets as $set) {

    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])) {
        continue;
    }

    //同城配送自动收货时间
    $cityexpress_receive=0;
    $cityexpress = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_city_express') . " WHERE uniacid=:uniacid AND merchid=:merchid",array(":uniacid"=>$_W['uniacid'],":merchid"=>0));
    if(!empty($cityexpress['enabled']) && !empty($cityexpress['receive_goods'])){
        $cityexpress_receive=intval($cityexpress['receive_goods'])>0?intval($cityexpress['receive_goods']):0;
    }

    $trade = m('common')->getSysset('trade', $_W['uniacid']);
    $days = intval($trade['receive']);

    $p = p('commission');
    $pcoupon = com('coupon');

    $orders = pdo_fetchall("select id,couponid,openid,isparent,sendtime,price,merchid,isverify,addressid,isvirtualsend,`virtual`,dispatchtype,city_express_state from " . tablename('ewei_shop_order') . " where uniacid={$_W['uniacid']} and status=2", array(), 'id');

    if (!empty($orders)) {
        foreach ($orders as $orderid => $order) {

            if(!empty($order['city_express_state']) && !empty($cityexpress_receive)){
                $days=$cityexpress_receive;
            }

            $result = goodsReceive($order, $days);
            if (!$result) {
                //不自动收货
                continue;
            }
            $time = time();
            pdo_query("update " . tablename('ewei_shop_order') . " set status=3,finishtime=:time where id=:orderid",array(':time'=>$time,':orderid'=>$orderid) );

            //多商户父订单跳过
            if ($order['isparent'] == 1) {
                continue;
            }
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
                if (!empty($order['couponid'])) {
                    $pcoupon->backConsumeCoupon($order['id']); //用券送券
                }
                //发送赠送优惠券
                $pcoupon->sendcouponsbytask($order['id']); //订单完成购物送券
            }
            //分销检测
            if ($p) {
                $p->checkOrderFinish($orderid);
            }
            //抽奖
            if(p('lottery') && $order['merchid'] == 0){
                //type 1:消费 2:签到 3:任务 4:其他
                $res = p('lottery')->getLottery($order['openid'],1,array('money'=>$order['price'],'paytype'=>2));
                if($res){
                    p('lottery')->getLotteryList($order['openid'],array('lottery_id'=>$res));
                }
            }
        }
    }
}

function goodsReceive($order, $sysday=0){
    $days = array();
    if (checkFetchOrder($order)){
        //禁止自提订单自动收货
        return false;
    }

    $isonlyverifygoods = m('order')->checkisonlyverifygoods($order['id']);
    //纯会员卡商品订单不自动收货
    if($isonlyverifygoods)
    {
        return false;
    }

    if ($order['merchid'] == 0) {
        $goods = pdo_fetchall("select og.goodsid, g.autoreceive from".tablename("ewei_shop_order_goods") ." og left join ".tablename("ewei_shop_goods")." g on g.id=og.goodsid where og.orderid=".$order['id']);

        foreach ($goods as $i=>$g){
            $days[] = $g['autoreceive'];
        }

        $day = max($days);
    } else {
        $day = 0;
    }

    if($day<0){
        return false;
    }
    elseif($day==0){
        if($sysday<=0){
            return false;
        }
        $day = $sysday;
    }

    $daytimes = 86400 * $day;

    if($order['sendtime']+$daytimes<=time()){
        return true;
    }

    return false;
}

//检查是否是自提订单
function checkFetchOrder($order)
{
    //如果不是核销、地址为空、不是虚拟、dispathtype不为空,那么就是自提商品
    if ($order['isverify'] != 1 && empty($item['addressid']) && empty($item['isvirtualsend']) && empty($item['virtual']) && $item['dispatchtype']){
        //是自提单
        return true;
    }else{
        //不是自提单
        return false;
    }
}


