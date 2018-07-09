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

class Util_EweiShopV2Page extends MobilePage {

    function query() {
        global $_W, $_GPC;

        $type = intval($_GPC['type']);
        $money = floatval($_GPC['money']);
        $merchs = $_GPC['merchs'];
        $goods = $_GPC['goods'];

        if($type==0)
        {
            $list = com_run('coupon::getAvailableCoupons', $type, 0, $merchs,$goods);
            $list2 = com_run('wxcard::getAvailableWxcards', $type, 0, $merchs,$goods);
        }else if( $type==1)
        {
            $list = com_run('coupon::getAvailableCoupons', $type, $money, $merchs);
            $list2=array();

        }

        show_json(1, array('coupons' => $list,'wxcards'=>$list2));
    }

    function picker(){
        include $this->template();
    }

}
