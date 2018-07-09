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

$sets = pdo_fetchall('select uniacid from ' . tablename('ewei_shop_sysset'));
foreach ($sets as $set) {

    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])) {
        continue;
    }
    $trade = m('common')->getSysset('trade', $_W['uniacid']);

    $logs = pdo_fetchall("select id,`day`,fullbackday,openid,priceevery,price from ".tablename('ewei_shop_fullback_log')." where uniacid = ".$_W['uniacid']." and isfullback = 0 and (fullbacktime =0 or fullbacktime < ".strtotime('-1 days').") and fullbackday < day ");
    foreach ($logs as $key => $value){
        if(($value['day']-$value['fullbackday'])>1){
            $result = m('member')->setCredit($value['openid'], 'credit2', $value['priceevery'], array('0', $_W['shopset']['shop']['name'] . '全返余额' . $value['priceevery']));
            pdo_update('ewei_shop_fullback_log', array('fullbackday'=>$value['fullbackday']+1,'fullbacktime'=>time()), array('id' => $value['id']));
        }elseif(($value['day']-$value['fullbackday'])==1){
            $value['priceevery'] = $value['price']-$value['priceevery']*$value['fullbackday'];
            $result = m('member')->setCredit($value['openid'], 'credit2', $value['priceevery'], array('0', $_W['shopset']['shop']['name'] . '全返余额' . $value['priceevery']));
            pdo_update('ewei_shop_fullback_log', array('fullbackday'=>$value['day'],'fullbacktime'=>time(),'isfullback'=>1), array('id' => $value['id']));
        }
    }

}




