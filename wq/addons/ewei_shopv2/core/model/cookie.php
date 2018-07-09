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

class Cookie_EweiShopV2Model {
	
	private $prefix;
	public function __construct() {
		global $_W;
		$this->prefix = EWEI_SHOPV2_PREFIX . '_cookie_'. $_W['uniacid'] . '_' ;
	}
	function set($key ,$value) {
		setcookie($this->prefix.$key, iserializer($value),time() + 3600 * 24 * 365);
	}
	function get($key){
		if(!isset($_COOKIE[$this->prefix.$key])){
			return false;
		}
		return iunserializer($_COOKIE[$this->prefix.$key]);
	}
	
}
