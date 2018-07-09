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

require EWEI_SHOPV2_PLUGIN . 'dividend/core/page_login_mobile.php';

class Down_EweiShopV2Page extends DIvidendMobileLoginPage {

	function main() {
		global $_W, $_GPC;

        $page_title = '商城';
        if(!empty($_W['shopset']['shop']['name'])){
            $page_title = $_W['shopset']['shop']['name'];
        }

        $member = $this->model->getInfo($_W['openid']);
		include $this->template();
	}

	function get_list() {
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$member = $this->model->getInfo($openid);

		$groupscount = $member['groupscount'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$list = pdo_fetchall("select * from " . tablename('ewei_shop_member') . " where headsid = :headsid and uniacid = :uniacid order by id desc limit " . ($pindex - 1) * $psize . ',' . $psize,array(':headsid'=>$member['id'],':uniacid'=>$_W['uniacid']));
		if(!empty($list)){

            foreach ($list as &$row) {
                $order = pdo_fetchall('select id,price,dividend from ' . tablename('ewei_shop_order') . ' where openid=:openid and headsid = :headsid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid'],':headsid'=>$member['id']));
                $money = 0;
                foreach($order as $k => $v){
                    $dividend = iunserializer($v['dividend']);
                    $money += $dividend['dividend_price'];
                }
                $row['ordercount'] = count($order);
                $row['moneycount']= floatval($money);
                $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            }
            unset($row);
        }

		show_json(1, array(
			'list' => $list,
			'total' => $groupscount,
			'pagesize' => $psize
		));
	}

}
