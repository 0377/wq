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

class Index_EweiShopV2Page extends PluginWebPage {

    function main()
    {
        global $_W;
        include $this->template();
    }

    function ajaxuser()
    {
        global $_GPC, $_W;

        $totals = $this->model->getUserTotals();
        $order0 = $this->model->getMerchOrderTotals(0);
        $order3 = $this->model->getMerchOrderTotals(3);

        $totals['totalmoney'] = $order0['totalmoney'];
        $totals['totalcount'] = $order0['totalcount'];
        $totals['tmoney'] = $order3['totalmoney'];
        $totals['tcount'] = $order3['totalcount'];

        show_json(1,$totals);
    }



}
