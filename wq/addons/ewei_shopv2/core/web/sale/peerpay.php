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

class Peerpay_EweiShopV2Page extends WebPage {

    function main()
    {
        global $_W,$_GPC;
        if ($_W['ispost']) {
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();

            $enough1 = array();
            $enough1 = is_array($_GPC['enough1']) ? $_GPC['enough1'] : array();

            $enough2 = array();
            $postenough2 = is_array($_GPC['enough2_1']) ? $_GPC['enough2_1'] : array();
            foreach ($postenough2 as $key => $value) {
                $enough2[] = array(
                    'enough2_1' => $_GPC['enough2_1'][$key],
                    'enough2_2' => $_GPC['enough2_2'][$key],
                );
            }
            $data['enough1'] = $enough1;
            $data['enough2'] = $enough2;
            m('common')->updatePluginset(array('sale' => array('peerpay'=>$data)));
            plog('sale.peerpay.edit', '修改找人代付配置');
            show_json(1,array('url'=>webUrl('sale/peerpay', array('tab'=>str_replace("#tab_","",$_GPC['tab'])))));
        }
        $data = m('common')->getPluginset('sale');
        $data = $data['peerpay'];
        $enough1 = empty($data['enough1']) ? array() : $data['enough1'];
        $enough2 = empty($data['enough2']) ? array() : $data['enough2'];
        include $this->template();
    }

}
