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

class Notice_EweiShopV2Page extends MobileLoginPage {

	function main() {
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid);
		$notice = iunserializer($member['noticeset']);

		//分销信息通知
		$hascommission = false;
		if (p('commission')) {
			$cset = p('commission')->getSet();
			$hascommission = !empty($cset['level']);
		}

		if ($_W['ispost']) {

			$type = trim($_GPC['type']);
			if(empty($type)){
				show_json(0,'参数错误');
			}
			$checked = intval($_GPC['checked']);
			if(empty($checked)){
				$notice[$type] =1;
			}
			else{
				unset($notice[$type]);
			}
	
			pdo_update('ewei_shop_member', array('noticeset' => iserializer($notice)), array('openid' => $openid, 'uniacid' => $uniacid));
			show_json(1);
		}

		include $this->template();
	}

}
