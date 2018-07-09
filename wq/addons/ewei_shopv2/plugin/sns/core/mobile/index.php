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
require EWEI_SHOPV2_PLUGIN .'sns/core/page_mobile.php';
class Index_EweiShopV2Page extends SnsMobilePage {

	function main() {
		global $_W, $_GPC;

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$shop = m('common') -> getSysset('shop');

		//广告
		$advs = pdo_fetchall("select id,advname,link,thumb from " . tablename('ewei_shop_sns_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));

		//当前积分
		$credit = m('member') -> getCredit($openid, 'credit1');

		//分类
		$category = pdo_fetchall("select id,`name`,thumb,isrecommand from " . tablename('ewei_shop_sns_category') . ' where uniacid=:uniacid and isrecommand = 1 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));

		//推荐的
		$recommands = pdo_fetchall("select sb.id,sb.title,sb.logo,sb.`desc`  from " . tablename('ewei_shop_sns_board') . " as sb
						left join ".tablename('ewei_shop_sns_category')." as sc on sc.id = sb.cid
						where sb.uniacid=:uniacid and sb.isrecommand=1 and sb.status=1 and sc.enabled = 1 order by sb.displayorder desc", array(':uniacid' => $uniacid));
		
		foreach($recommands as &$row){
			$row['postcount'] =  $this->model->getPostCount($row['id']);
			//关注数
			$row['followcount'] = $this->model->getFollowCount($row['id']);

		}
		unset($row);

		$_W['shopshare'] = array( 
			'title' => $this -> set['share_title'], 
			'imgUrl' => tomedia($this -> set['share_icon']), 
			'link' => mobileUrl('sns', array(), true), 
			'desc' => $this -> set['share_desc']
		);

		include $this -> template();
	}

}
