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

require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_EweiShopV2Page extends MerchWebPage {

	function main() {
		global $_W;
		if (mcv('perm.role') && !empty($_W['accounttotal'])) {
			header('location: ' . merchUrl('perm/role'));
			exit;
		} else if (mcv('perm.user') && !empty($_W['accounttotal'])) {
			header('location: ' . merchUrl('perm/user'));
			exit;
		} else if (mcv('perm.log')) {
			header('location: ' . merchUrl('perm/log'));
			exit;
		} 
	}
 
}
