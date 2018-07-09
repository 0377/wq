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

class CommissionMobilePage extends PluginMobilePage {

	public function __construct() {
		parent::__construct();
		
		global $_W, $_GPC;
 
		if ($_W['action'] != 'register' && $_W['action'] != 'myshop' && $_W['action'] != 'share') {
			$member = m('member')->getMember($_W['openid']);
			if ($member['isagent'] != 1 || $member['status'] != 1) {
				header('location:' . mobileUrl('commission/register'));
				exit;
			}
		}
	}
//	public function footerMenus() {
//		global $_W, $_GPC;
//		include $this->template('commission/_menu');
//	}

}
