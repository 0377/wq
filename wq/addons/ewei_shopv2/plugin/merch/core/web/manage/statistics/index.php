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
 
		if(mcv('statistics.sale.main')){
			header('location: '.merchUrl('statistics/sale'));
		} 
		else if(mcv('statistics.sale_analysis.main')){
			header('location: '.merchUrl('statistics/sale_analysis'));
		}
		else if(mcv('statistics.order.main')){
			header('location: '.merchUrl('statistics/order'));
		}
		else if(mcv('statistics.sale_analysis.main')){
			header('location: '.merchUrl('statistics/sale_analysis'));
		}
		else if(mcv('statistics.goods.main')){
			header('location: '.merchUrl('statistics/goods'));
		}
		else if(mcv('statistics.goods_rank.main')){
			header('location: '.merchUrl('statistics/goods_rank'));
		}
		else if(mcv('statistics.goods_trans.main')){
			header('location: '.merchUrl('statistics/goods_trans'));
		}
		else if(mcv('statistics.member_cost.main')){
			header('location: '.merchUrl('statistics/member_cost'));
		}
		else if(mcv('statistics.member_increase.main')){
			header('location: '.merchUrl('statistics/member_increase'));
		}else{
			header('location: '.merchUrl());
		}
	}
}