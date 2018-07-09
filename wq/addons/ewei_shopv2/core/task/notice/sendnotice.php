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
 
$notice_redis = m('common') -> getSysset('notice_redis');
if(empty($notice_redis['notice_redis'])){ //开启了redis版消息通知
    $open_redis = function_exists('redis') && !is_error(redis());
    if($open_redis){  //redis开启
        $redis = redis();
        $_W['uniacid'] = $_GPC['uniacid'];
        $key = 'notice_uniacid'.$_GPC['uniacid'].'_list';
        $list = $redis->lrange($key,0,-1);
        if($list){
            foreach($list as $k => $value){
                $sendData = unserialize($value);
                if($sendData['is_send'] == 0){
                    $res = m('notice') -> sendNotice($sendData);
                    if(is_error($res)){
                        $res = m('notice') -> sendNotice($sendData);
                        if(is_error($res)){
                            $res = m('notice') -> sendNotice($sendData);
                        }else{
                            $redis->lPop($key);
                        }
                        if(is_error($res)){
                            file_put_contents(__DIR__.'/noticeErrorLog.json',json_encode(date('Y-m-d H:i:s',time()).','.$sendData['openid'].','.serialize($res)),FILE_APPEND);
                            $redis->lPop($key);
                        }
                    }else{
                        $redis->lPop($key);
                    }
                }
            }
        }
    }
}




