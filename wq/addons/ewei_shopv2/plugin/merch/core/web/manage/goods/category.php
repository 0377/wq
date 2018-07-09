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
class Category_EweiShopV2Page extends MerchWebPage {

    function main() {

        global $_W, $_GPC;

        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
        $merch_category = $this->getSet('merch_category');

        if (!empty($merch_category)) {
            foreach ($category as $index => $row) {
                if (array_key_exists($row['id'], $merch_category)) {
                    $category[$index]['enabled'] = $merch_category[$row['id']];
                }
            }
        }

        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][] = $row;
                unset($category[$index]);
            }
        }

        $set = m('common')->getSysset(array('shop'));
        $shopset = $set['shop'];

        include $this->template();
    }

    function enabled() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }

        $items = pdo_fetchall("SELECT id,enabled FROM " . tablename('ewei_shop_category') . " WHERE uniacid=" . $_W['uniacid']);

        $merch_category = $this->getSet('merch_category');
        if (empty($merch_category)) {
            foreach ($items as $item) {
                if ($id == $item['id']) {
                    $merch_category[$item['id']] = intval($_GPC['enabled']);
                } else {
                    $merch_category[$item['id']] = $item['enabled'];
                }
            }
        } else {
            foreach ($items as $item) {
                if ($id == $item['id']) {
                    $merch_category[$item['id']] = intval($_GPC['enabled']);
                } else {
                    if (!array_key_exists($item['id'], $merch_category)) {
                        $merch_category[$item['id']] = $item['enabled'];
                    }
                }
            }
        }

        $this->updateSet(array('merch_category' => $merch_category));
        show_json(1, array('url' => referer()));
    }

    function query(){
        global $_W, $_GPC;
        $kwd = trim($_GPC['keyword']);

        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $condition=" and enabled=1 and uniacid=:uniacid";
        if (!empty($kwd)) {
            $condition.=" AND `name` LIKE :keyword";
            $params[':keyword'] = "%{$kwd}%";
        }
        $ds = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_category') . " WHERE 1 {$condition} order by displayorder desc,id desc", $params);
        $ds = set_medias($ds,array('thumb','advimg'));

        $merch_category = $this->getSet('merch_category');
        if(!empty($merch_category)){
            foreach ($ds as $i=>$d) {
                $did = $d['id'];
                if(empty($merch_category[$did])){
                    unset($ds[$i]);
                }
            }
        }

        if($_GPC['suggest']){
            die(json_encode(array('value'=>$ds)));
        }
        include $this->template();
    }

}
