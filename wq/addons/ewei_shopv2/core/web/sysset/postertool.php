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

class Postertool_EweiShopV2Page extends WebPage {

	function main() {
        global $_W, $_GPC;
		include $this->template();
	}

    function clear()
	{
	    global $_W,$_GPC;
        load()->func('file');
        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/poster/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/qrcode/" . $_W['uniacid']);
        $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(':uniacid' => $_W['uniacid']));
        pdo_update('ewei_shop_poster_qr', array('mediaid' => ''), array('acid' => $acid));
        plog('poster.clear', "清除海报缓存");

        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/goodscode/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/poster_wxapp/commission/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/poster_wxapp/goods/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . "/addons/ewei_shopv2/data/postera/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . " /addons/ewei_shopv2/data/task/poster/" . $_W['uniacid']);
        @rmdirs(IA_ROOT . " /addons/ewei_shopv2/data/upload/exchange/" . $_W['uniacid']);


		show_json(1);
	}


}
