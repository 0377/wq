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

class Selecticon3_EweiShopV2Page extends WebPage {

    function main() {
        $csspath =MODULE_ROOT."/static/fonts/wxiconx/iconfont.css";
        $list = array();
        $content = file_get_contents($csspath);

        if(!empty($content)){
            preg_match_all('/.(.*?):before/', $content, $matchs);
            $list = $matchs[1];
        }

        include $this->template();
    }
}