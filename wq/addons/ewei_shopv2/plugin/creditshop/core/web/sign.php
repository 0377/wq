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

class Sign_EweiShopV2Page extends PluginWebPage {

    function main() {
        global $_W, $_GPC;

        include $this->template();
    }

    function tpl() {
        include $this->template();
    }

}
