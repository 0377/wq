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

class AbonusMobilePage extends PluginMobilePage {

	public function __construct() {
		parent::__construct();
		
		global $_W, $_GPC;
 
		if ($_W['action'] != 'register' && $_W['action'] != 'myshop' && $_W['action'] != 'share') {
			$member = m('member')->getMember($_W['openid']);

			if (empty($member['isagent']) || empty($member['status'])) {
				header("location: " . mobileUrl('commission/register'));
				exit;
			}

			if (empty($member['isaagent']) || empty($member['aagentstatus'])) {
				header("location: " . mobileUrl('abonus/register'));
				exit;
			}
		}
	}
	public function footerMenus($diymenuid = NULL) {
		global $_W, $_GPC;
		include $this->template('abonus/_menu');
	}

}
