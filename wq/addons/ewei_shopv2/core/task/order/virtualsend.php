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
$_W['uniacid'] = $_GPC['uniacid'];
$_W['acid'] = $_GPC['acid'];
ignore_user_abort(); //忽略关闭浏览器
set_time_limit(0); //永远执行

    $logs = pdo_fetchall("select * from " . tablename('ewei_shop_virtual_send_log') . " where uniacid=:uniacid and status=0 limit 10",array(":uniacid"=>$_W['uniacid']));
    if($logs){
       foreach ($logs as $log){

           $data = array(
               "openid" => $log['openid'],
               'tag' => $log['tag'],
               'default' => unserialize($log['default']),
               'cusdefault' => $log['cusdefault'],
               'orderid'=>$log['orderid'],
               'account'=>getAccount(),
               'url' => $log['url'],
               'datas' => unserialize($log['datas']),
               'appurl' => $log['appurl'],

           );

           if(time()>$log['sendtime']){

            $res= m('notice')->sendNotice($data);
            if($res){
                pdo_update('ewei_shop_virtual_send_log', array('status'=>1),
                    array('id' =>$log['id'],'uniacid' => $_W['uniacid']));
            }

           }
       }

}

//获取微信account
function getAccount() {
    global $_W;
    load()->model('account');
    if (!empty($_W['acid'])) {
        return WeAccount::create($_W['acid']);
    } else {
        $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(':uniacid' => $_W['uniacid']));
        return WeAccount::create($acid);
    }
    return false;
}



