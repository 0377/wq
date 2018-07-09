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

class Index_EweiShopV2Page extends WebPage {

	function main() {
		
		if(cv('finance.recharge.view')){
			header('location: '.webUrl('finance/log/recharge'));
		} else if(cv('finance.withdraw.view')){
			header('location: '.webUrl('finance/log/withdraw'));
		} else if(cv('finance.downloadbill')){
			header('location: '.webUrl('finance/downloadbill'));
		}elseif(cv('finance.credit.credit1')){
            header('location:'.webUrl('finance.credit.credit1'));
        }elseif(cv('finance.credit.credit2')){
            header('location:'.webUrl('finance.credit.credit2'));
        }else{
			header('location: '.webUrl());
		}
	}
}