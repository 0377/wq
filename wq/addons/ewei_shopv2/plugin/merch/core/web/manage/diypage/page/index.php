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
		header('location: ' . webUrl('diypage'));
	}

	function  create(){
		global $_W, $_GPC;

        $tid_member = pdo_fetchcolumn("select id from". tablename("ewei_shop_diypage_template"). " where tplid=9 limit 1");
        $tid_commission = pdo_fetchcolumn("select id from". tablename("ewei_shop_diypage_template"). " where tplid=10 limit 1");
        $tid_detail = pdo_fetchcolumn("select id from". tablename("ewei_shop_diypage_template"). " where tplid=11 limit 1");

		include $this->template('diypage/page/create');
	}

	function keyword(){
		global $_W, $_GPC;

		$id = intval($_GPC['id']);
		$keyword = trim($_GPC['keyword']);
		if(!empty($keyword)) {
			$result = m('common')->keyExist($keyword);
			if(!empty($result)){
				if($result['name']!='ewei_shopv2:diypage:'.$id){
					show_json(0);
				}
			}
		}
		show_json(1);
	}

	function preview() {
		global $_W, $_GPC;

		$id = intval($_GPC['id']);
		if(empty($id)) {
			header('location: ' . webUrl('diypage'));
		}
		$pagetype = '';
        $diypage_plugin = p('diypage');
		$page = $diypage_plugin->getPage($id);
		if(!empty($page)) {
			if($page['type']==1) {
				$pagetype = 'diy';
			}
			elseif($page['type']>1 && $page['type']<99) {
				$pagetype = 'sys';
			}
			elseif($page['type']==99) {
				$pagetype = 'mod';
			}
		}

		include $this->template();
	}

}