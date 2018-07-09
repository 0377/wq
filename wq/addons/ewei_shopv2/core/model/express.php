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

class Express_EweiShopV2Model {

    /**
     * 获取快递列表
     */
    function getExpressList() {
        global $_W;

        $sql = 'select * from ' . tablename('ewei_shop_express') . ' where status=1 order by displayorder desc,id asc';
        $data = pdo_fetchall($sql);

        return $data;
    } 
}
