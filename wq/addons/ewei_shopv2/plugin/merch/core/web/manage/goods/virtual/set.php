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
class Set_EweiShopV2Page extends MerchWebPage {

    public function __construct($_com='virtual')
    {
        parent::__construct($_com);
    }

    function main() {

        global $_W, $_GPC;
        if ($_W['ispost']) {

            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
            if(intval($data['closeorder_virtual']) < 5 && intval($data['closeorder_virtual']) > 0){
                show_json(0,'最低时间为5分钟');
            }
            if(intval($data['closeorder_virtual']) < 0){
                show_json(0,'时间不能小于0');
            }
            if(!empty($data['closeorder_virtual']))
            {
                $data['closeorder_virtual'] = intval($data['closeorder_virtual']);
            }

            m('common')->updateSysset(array('trade' => $data));

            plog('goods.virtual.main', '修改系统设置-交易设置');

            show_json(1);
        }

        $areas = m('common')->getAreas();
        $data = m('common')->getSysset('trade');
        include $this->template();
    }

}
