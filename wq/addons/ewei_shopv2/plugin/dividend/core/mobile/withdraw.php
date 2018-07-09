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

require EWEI_SHOPV2_PLUGIN .'dividend/core/page_login_mobile.php';

class Withdraw_EweiShopV2Page extends DIvidendMobileLoginPage
{

    function main(){
        global $_W, $_GPC;

        $page_title = '商城';
        if(!empty($_W['shopset']['shop']['name'])){
            $page_title = $_W['shopset']['shop']['name'];
        }

        $openid = $_W['openid'];
        $member = $this->model->getInfo($openid, array('total','ok', 'apply', 'check', 'lock','pay','wait','fail'));
        $cansettle = $member['dividend_ok'] >=1 && $member['dividend_ok'] >= floatval($this->set['withdraw']);

        $agentid = $member['id'];
        if (!empty($agentid)) {
            $data = pdo_fetch('select sum(deductionmoney) as sumcharge from ' . tablename('ewei_shop_dividend_log') .' where mid=:mid and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $agentid));
            $dividend_charge = $data['sumcharge'];
            $member['dividend_charge'] =  $dividend_charge;
        } else {
            $member['dividend_charge'] = 0;
        }

        include $this->template();
    }

}
