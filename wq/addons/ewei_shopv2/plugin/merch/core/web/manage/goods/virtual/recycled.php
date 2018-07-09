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

class Recycled_EweiShopV2Page extends MerchWebPage {

    public function __construct($_com='virtual')
    {
        parent::__construct($_com);
    }

    function main() {
        global $_W, $_GPC;

        $page = empty($_GPC['page']) ? "" : $_GPC['page'];
        $pindex = max(1, intval($page));
        $psize = 12;
        $kw = empty($_GPC['keyword']) ? "" : $_GPC['keyword'];
        $items = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE uniacid=:uniacid and merchid='.$_W['merchid'].' and title like :name and recycled = 1 order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':name' => "%{$kw}%", ':uniacid' => $_W['uniacid']));
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('ewei_shop_virtual_type') . " WHERE uniacid=:uniacid and merchid=".$_W['merchid']." and title like :name and recycled = 1 order by id desc ", array(':uniacid' => $_W['uniacid'], ':name' => "%{$kw}%"));
        $pager = pagination2($total, $pindex, $psize);
        $category = pdo_fetchall('select * from '.tablename('ewei_shop_virtual_category').' where uniacid=:uniacid and merchid='.$_W['merchid'].' order by id desc',array(':uniacid'=>$_W['uniacid']),'id');
        include $this->template();
    }


    function delete() {

        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }
        $types = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_virtual_type') . " WHERE id in( $id ) and merchid=".$_W['merchid']." AND uniacid=" . $_W['uniacid']);
        foreach ($types as $type) {
            pdo_delete('ewei_shop_virtual_type', array('id' => $type['id']));
            pdo_delete('ewei_shop_virtual_data', array('typeid' => $type['id']));
            plog('virtual.temp.delete', "删除模板 ID: {$type['id']}");
        }
        show_json(1, array('url' => merchUrl('goods/virtual')));
    }

    function recover(){
        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }
        $types = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_virtual_type') . " WHERE id in( $id ) and merchid=".$_W['merchid']." AND uniacid=" . $_W['uniacid']);
        foreach ($types as $type) {
            pdo_update('ewei_shop_virtual_type', array('recycled'=> 0),array('id' => $type['id']));
            //pdo_delete('ewei_shop_virtual_data', array('typeid' => $type['id']));
            plog('virtual.recycled.recover', "模板移出回收站 ID: {$type['id']}");
        }
        show_json(1, array('url' => merchUrl('goods/virtual/recycled')));
    }

}
