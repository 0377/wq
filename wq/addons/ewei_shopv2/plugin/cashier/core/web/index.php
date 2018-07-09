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

class Index_EweiShopV2Page extends PluginWebPage {

	public function main(){
		if (cv('cashier.user')){
            header('location: ' . webUrl('cashier/user'));
        }elseif(cv('cashier.user')){
            header('location: ' . webUrl('cashier/user'));
        }
	}

    public function ajaxcleartotle()
    {
        global $_W;
        $status0 = (int)pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("ewei_shop_cashier_clearing")." WHERE uniacid=:uniacid AND status=0 AND deleted=0",array(':uniacid'=>$_W['uniacid']));
        $status1 = (int)pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("ewei_shop_cashier_clearing")." WHERE uniacid=:uniacid AND status=1 AND deleted=0",array(':uniacid'=>$_W['uniacid']));
        $status2 = (int)pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("ewei_shop_cashier_clearing")." WHERE uniacid=:uniacid AND status=2 AND deleted=0",array(':uniacid'=>$_W['uniacid']));

        show_json(1,array(
            'status0' => $status0,
            'status1' => $status1,
            'status2' => $status2
        ));
	}

}
