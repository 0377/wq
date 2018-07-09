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
class Sale_analysis_EweiShopV2Page extends MerchWebPage {

	function main() {
		global $_W, $_GPC;

		function sale_analysis_count($sql) {
			$c = pdo_fetchcolumn($sql);
			return intval($c);
		}

        //会员数
		$member_count = sale_analysis_count("select count(*) from " . tablename('ewei_shop_member') . " where uniacid={$_W['uniacid']} and  openid in ( SELECT distinct openid from " . tablename('ewei_shop_order') . "   WHERE uniacid = '{$_W['uniacid']}' and merchid='{$_W['merchid']}'  )");

//订单总金额
		$orderprice = sale_analysis_count("SELECT sum(price) FROM " . tablename('ewei_shop_order') . " WHERE  status>=1 and uniacid = '{$_W['uniacid']}' and merchid='{$_W['merchid']}' ");

//订单总数
		$ordercount = sale_analysis_count("SELECT count(*) FROM " . tablename('ewei_shop_order') . " WHERE status>=1 and uniacid = '{$_W['uniacid']}' and merchid='{$_W['merchid']}'");

//商品总浏览量
		$viewcount = sale_analysis_count("SELECT sum(viewcount) FROM " . tablename('ewei_shop_goods') . " WHERE uniacid = '{$_W['uniacid']}' and merchid='{$_W['merchid']}'");

//消费过的会员数
		$member_buycount = sale_analysis_count("select count(*) from " . tablename('ewei_shop_member') . " where uniacid={$_W['uniacid']} and  openid in ( SELECT distinct openid from " . tablename('ewei_shop_order') . "   WHERE uniacid = '{$_W['uniacid']}' and merchid='{$_W['merchid']}' and status>=1 )");

		include $this->template('statistics/sale_analysis');
	}

}
