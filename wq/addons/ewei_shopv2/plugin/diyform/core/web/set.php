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

class Set_EweiShopV2Page extends PluginWebPage {

	function main() {

		global $_W,$_GPC;
		$form_list = $this->model->getDiyformList();
		if ($_W['ispost']) {
			ca('diyform.set.edit');
			$data = is_array($_GPC['setdata']) ? $_GPC['setdata'] : array();
			$this->updateSet($data);
			plog('diyform.set.edit', '修改基本设置');
			show_json(1);
		}
		$set = $this->set;
		include $this->template();
	}

}
