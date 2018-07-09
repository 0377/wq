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

class DataModel  {

	public function read($key =''){
		global $_W,$_GPC;
		return m('cache')->getArray("data_".$_W['uniacid']."_".$key);
	}
	public function write($key,$data){
		global $_W,$_GPC;
		m('cache')->set("data_".$_W['uniacid']."_".$key,$data);
	}
}