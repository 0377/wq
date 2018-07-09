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
class Recommand_EweiShopV2Page extends MerchWebPage {

	function main() {

		global $_W, $_GPC;
		$shop = $this->getSet('shop');
		if ($_W['ispost']) {
			$shop['indexrecommands'] = $_GPC['goodsid'];
			$this->updateSet(array('shop' => $shop));
			mplog('shop.recommand', '修改首页推荐商品设置');

			// 更新静态首页
			show_json(1);
		}
		
		$goodsids = isset($shop['indexrecommands'])?implode(",",$shop['indexrecommands']):'';
		$goods = false;
		if(!empty($goodsids)){
			$goods = pdo_fetchall('select id,title,thumb from '.tablename('ewei_shop_goods')." where id in ({$goodsids}) and status=1 and deleted=0 and uniacid={$_W['uniacid']} and merchid={$_W['merchid']} order by instr('{$goodsids}',id)");
		}
		$goodsstyle = $shop['goodsstyle'];
		include $this->template();
	}
	
	function setstyle(){
		global $_W, $_GPC;
		$shop = $this->getSet('shop');
		$shop['goodsstyle'] = intval($_GPC['goodsstyle']);
		$this->updateSet(array('shop' => $shop));
		mplog('shop.recommand', '修改手机端商品组样式');
		
		// 更新静态首页
		show_json(1,$shop);
	}

}
