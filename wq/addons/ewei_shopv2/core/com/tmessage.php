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

class Tmessage_EweiShopV2ComModel extends ComModel {

	function perms() {
		return array(
			'tmessage' => array(
				'text' => $this->getName(), 'isplugin' => true,
				'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'send' => '发送-log'
			)
		);
	}

}
