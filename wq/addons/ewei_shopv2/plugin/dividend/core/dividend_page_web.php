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

class DividendWebPage extends PluginWebPage {

    public function __construct() {
        parent::__construct();

        global $_W, $_GPC;

        if ($_W['action'] != 'init' && empty( $this->set['init'])) {
            header('location: ' . webUrl('dividend/init'));
            exit;
        }

        if($_W['action'] == 'init' && !empty( $this->set['init'])){
            header('location: ' . webUrl('dividend/index'));
            exit;
        }

    }
}
