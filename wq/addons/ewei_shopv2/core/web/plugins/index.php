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
		global $_W,$_GPC;
		$category = m('plugin')->getList(1);

		$wxapp_array = array(
		    'commission',
            'creditshop',
            'diyform',
            'bargain',
            'quick',
            'cycelbuy',
            'seckill',
            'groups',
            'dividend'
        );
        $apps = false;
        if ($_W['role'] == 'founder' || empty($_W['role'])) {
            $apps = true;
        }
        $filename = "../addons/ewei_shopv2/core/model/grant.php";
        if(file_exists($filename)){
            $setting = pdo_fetch("select * from ".tablename('ewei_shop_system_grant_setting')." where id = 1 limit 1 ");
            $permPlugin = false;
            if($setting['condition_type']==0){
                $permPlugin = true;
            }elseif($setting['condition_type']==1){
                $total = m("goods")->getTotals();
                if($total['sale'] >= $setting['total']){
                    $permPlugin = true;
                }
            }elseif($setting['condition_type']==2){
                $price = pdo_fetch("select sum(price) as price from ".tablename('ewei_shop_order')." where uniacid = ".$_W['uniacid']." and status = 3 ");
                if($price['price'] >= $setting['price']){
                    $permPlugin = true;
                }
            }elseif($setting['condition_type']==3){
                $time = floor((time()-$_W['user']['joindate']) / 86400);
                if($time >= $setting['day']){
                    $permPlugin = true;
                }
            }
        } 
        if(p("grant")){
            $pluginsetting = pdo_fetch("select adv from ".tablename('ewei_shop_system_plugingrant_setting')." where 1 = 1 limit 1 ");
        }

		include $this->template();
	}
}