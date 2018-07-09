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
class Mod_EweiShopV2Page extends PluginWebPage {

	function main() {
		global $_W, $_GPC;
		$pagetype = 'mod';

		if (!empty($_GPC['keyword'])) {
			$keyword = '%' . trim($_GPC['keyword']) . '%';
			$condition = " and name like '{$keyword}' ";
		}
		$result = $this->model->getPageList('mod',$condition,  intval($_GPC['page']));
		extract($result);

		include $this->template('diypage/page/list');
	}
	function edit() {
		$this->post('edit');
	}

	function  add(){
		$this->post('add');
	}

	protected function post($do){
		global $_W, $_GPC;

		$result = $this->model->verify($do, 'mod');
		extract($result);

		$allpagetype = $this->model->getPageType();
		$typename = $allpagetype[$type]['name'];

		if($_W['ispost']) {
			$data = $_GPC['data'];
			$this->model->savePage($id, $data);
		}

        $hasplugins = json_encode(array(
            'creditshop' => p('creditshop') ? 1 : 0,
            'merch' => p('merch') ? 1 : 0,
            'seckill' => p('seckill') ? 1 : 0
        ));

		include $this->template('diypage/page/post');
	}

	function delete(){
		global $_W, $_GPC;

		$id = intval($_GPC['id']);
		if(empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$this->model->delPage($id);
	}

	function query() {
		global $_W, $_GPC;

		$result = $this->model->getPageList('mod');
		extract($result);
		include  $this->template();
	}
}