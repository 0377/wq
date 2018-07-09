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

class Category_EweiShopV2Page extends PluginMobileLoginPage {

    public function main(){
        global $_W, $_GPC;
        $uniacid = intval($_W['uniacid']);
        $categorys = pdo_fetchall("select * from ".tablename('ewei_shop_live_category')." where uniacid = ".$uniacid." and enabled = 1 ");

        $shop = m('common') -> getSysset('shop');
        $setting = pdo_fetch("select * from ".tablename('ewei_shop_live_setting')." where uniacid = :uniacid  ",array(":uniacid"=>$uniacid));
        $_W['shopshare'] = array(
            'title' => !empty($setting['share_title']) ? $setting['share_title'] : $shop['name'] ,
            'imgUrl' => !empty($setting['share_icon']) ? tomedia($setting['share_icon']) : tomedia($shop['logo']),
            'link' => !empty($setting['share_url']) ? $setting['share_url'] : mobileUrl('live', array(), true),
            'desc' => !empty($setting['share_desc']) ? $setting['share_desc'] : $shop['description']
        );
        include $this->template();
    }

}
