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
class Query_EweiShopV2Page extends MerchWebPage
{

    function main()
    {
        global $_W, $_GPC;
        $kwd = trim($_GPC['keyword']);
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $condition = " and uniacid=:uniacid";

        if (!empty($kwd)) {
            $condition .= " AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)";
            $params[':keyword'] = "%{$kwd}%";
        }
        $ds = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . " WHERE 1 {$condition} order by id asc", $params);

        if ($_GPC['suggest']) {
            die(json_encode(array('value' => $ds)));
        }

        include $this->template();
        exit;
    }
}
