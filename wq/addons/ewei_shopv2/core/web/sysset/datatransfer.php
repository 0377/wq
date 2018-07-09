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

class Datatransfer_EweiShopV2Page extends WebPage {

	function main() {

		global $_W, $_GPC;

		$item = pdo_fetch("select dt.*,w.name from ".tablename('ewei_shop_datatransfer')." dt left join ".tablename('account_wechats')." w on w.uniacid = dt.touniacid where dt.fromuniacid =:uniacid limit 1" ,array(":uniacid"=>$_W['uniacid']));

		$senduniacid = $_GPC['acid'];
		$isopen = $_GPC['isopen'];
		if ($_W['ispost']) {
			if(!empty($isopen))
			{
				pdo_delete('ewei_shop_datatransfer',array('fromuniacid'=>$_W['uniacid']));
				show_json(1, array('url' => referer()));
			}
			$data = array(
				'fromuniacid'=>$_W['uniacid'],
				'touniacid'=>$senduniacid,
				'status'=>1
			);
			pdo_insert('ewei_shop_datatransfer',$data);

			$tables = array(
				'ewei_shop_category', 				//商品分类表1
				'ewei_shop_carrier',               	//自提点表1
				'ewei_shop_adv',                    	//幻灯片表1
				'ewei_shop_feedback',              	//反馈表1	2
				'ewei_shop_form',                  	//表单表1
				'ewei_shop_form_category',        	//表单分类表1
				'ewei_shop_gift',                  	//赠品表1
				'ewei_shop_goods',                 	//商品表1
				'ewei_shop_goods_comment',    		//商品评论表1
				'ewei_shop_goods_group',        		//商品组表1
				'ewei_shop_goods_label',            //商品标签表1
				'ewei_shop_goods_labelstyle',    	//v2 商品标签风格表1
				'ewei_shop_goods_option',        	//商品规格表 *ERP1
				'ewei_shop_goods_param',        		//商品参数表1
				'ewei_shop_goods_spec',            	//商品规格表1
				'ewei_shop_goods_spec_item',    	//商品规格项目表1

				//用户对应表
				//'ewei_shop_member',						//用户表 12
				'ewei_shop_member_address',    			//用户地址表	12
				'ewei_shop_member_printer',        		//打印配置	1
				'ewei_shop_member_printer_template',  //打印模板	1
				'ewei_shop_member_group',
				'ewei_shop_member_level',

				'ewei_shop_member_log',					//用户表
				'mc_credits_record',						//微擎积分余额记录
				//'mc_credits_recharge',					//??


				//人人分销
				'ewei_shop_commission_apply',            //提现申请表1 mid
				'ewei_shop_commission_bank',            //提现银行表1
				'ewei_shop_commission_level',            //分销分级表	1
				'ewei_shop_commission_log',               //分销日志表	1 mid
				'ewei_shop_commission_rank',            //分销排行设置表	1
				'ewei_shop_commission_repurchase',		//分销回购表	1	2
				'ewei_shop_commission_shop',            //我的小店表	1


				'ewei_shop_order',                       //订单表			1	2
				'ewei_shop_order_comment',            	//订单评论表	1	2
				'ewei_shop_order_goods',                //订单商品表		1
				'ewei_shop_order_peerpay',                //订单代付信息	1
				'ewei_shop_order_peerpay_payinfo',    //订单代付付款信息	1
				'ewei_shop_order_refund',                //订单退货表		1
			);



			foreach ($tables as $table) {
				pdo_update($table, array("uniacid" => $senduniacid), array("uniacid" => $_W['uniacid']));
			}

			show_json(1, array('url' => referer()));
		}

		include $this->template();
	}
}
