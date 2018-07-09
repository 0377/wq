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

class SeckillWebPage extends PluginWebPage {

    public function __construct() {
        parent::__construct();

        global $_W, $_GPC;

        if(!function_exists('redis')){
            $this->message('请更新到最新版本才能使用秒杀应用','exit','error');
            exit;
        }

        $redis = redis();

        if(is_error($redis)){

            $message= '请联系管理员开启 redis 支持，才能使用秒杀应用';
            if($_W['isfounder']){
                $message.="<br/><br/>错误信息: ".$redis['message'];
            }
            $this->message($message, 'exit','error');
            exit;
        }

    }
}
