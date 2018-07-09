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
class Selecturl_EweiShopV2Page extends MerchWebPage {

    function main() {
        global $_W, $_GPC;

        $full = intval($_GPC['full']);

        $merchid = intval($_W['merchid']);
        $syscate = m('common')->getSysset('category');
        if(isset($_GPC['type']) && !empty($_GPC['type'])){
            $type = $_GPC['type'];
        }
        if($syscate['level']>0){
            //$categorys = pdo_fetchall("SELECT id,name,parentid FROM " . tablename('ewei_shop_category') . " WHERE enabled=:enabled and uniacid= :uniacid  ", array(':uniacid' => $_W['uniacid'], ':enabled' => '1'));
            $categorys = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
            $merch_category = $this->getSet('merch_category');

            $groups = pdo_fetchall("SELECT id,name FROM " .tablename('ewei_shop_goods_group'). " WHERE enabled=:enabled AND merchid = {$merchid} AND  uniacid= :uniacid ", array(':uniacid' => $_W['uniacid'],':enabled' => '1'));

            if (!empty($merch_category)) {
                foreach ($categorys as $index => $row) {
                    if (array_key_exists($row['id'], $merch_category)) {
                        if(empty($merch_category[$row['id']])){
                            unset($categorys[$index]);
                        }
                    }
                }
            }

        }

        if(p('diypage')) {
            if($type == 'topmenu'){
                $diypage = p('diypage')->getPageList('allpage', ' and (`type` = 1 or `type` = 2)');
                if(!empty($diypage)) {
                    foreach($diypage['list'] as $k => $v){
                        $pages = json_decode(base64_decode($v['data']),true);
                        foreach($pages['items'] as $pk => $pv){
                            if($pv['id'] == 'topmenu'){
                                unset($diypage['list'][$k]);
                            }
                        }
                    }
                }
                $allpagetype = p('diypage')->getPageType();
            }else{
                $diypage = p('diypage')->getPageList('allpage', ' and `type`<5');
                $allpagetype = p('diypage')->getPageType();
            }
        }

//        if(p('diypage')) {
//            $diypage = p('diypage')->getPageList();
//            if(!empty($diypage)) {
//                $allpagetype = p('diypage')->getPageType();
//            }
//        }

        if(p('quick')) {
            $quickList = p('quick')->getPageList($_W['merchid']);
        }

        include $this->template();
    }

    function query(){
        global $_W, $_GPC;

        $type = trim($_GPC['type']);
        $kw = trim($_GPC['kw']);
        $full = intval($_GPC['full']);

        if(!empty($kw) && !empty($type)){

            if($type=='good'){
                $list = pdo_fetchall("SELECT id,title,productprice,marketprice,thumb,sales,unit,minprice FROM " . tablename('ewei_shop_goods') . " WHERE merchid=:merchid and uniacid= :uniacid and status=:status and deleted=0 AND title LIKE :title ", array(':title' => "%{$kw}%", ':merchid'=>intval($_W['merchid']),':uniacid' => $_W['uniacid'], ':status' => '1'));
                $list = set_medias($list, 'thumb');
            }
            elseif($type=='article'){
                //$list = pdo_fetchall("select id,article_title from " . tablename('ewei_shop_article') . ' where article_title LIKE :title and article_state=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));
                $list = array();
            }
            elseif($type=='coupon'){
                $list = pdo_fetchall("select id,couponname,coupontype from " . tablename('ewei_shop_coupon') . ' where couponname LIKE :title and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));
            }
            elseif($type=='groups'){
                //$list = pdo_fetchall("select id,title from " . tablename('ewei_shop_groups_goods') . ' where title LIKE :title and status=1 and deleted=0 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));
                $list = array();
            }
            elseif($type=='sns'){
                /*
                $list_board = pdo_fetchall("select id,title from " . tablename('ewei_shop_sns_board') . ' where title LIKE :title and status=1 and enabled=0 and uniacid=:uniacid order by id desc ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));
                $list_post = pdo_fetchall("select id,title from " . tablename('ewei_shop_sns_post') . ' where title LIKE :title and checked=1 and deleted=0 and uniacid=:uniacid order by id desc ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));

                $list = array();
                if(!empty($list_board) && is_array($list_board)){
                    foreach ($list_board as &$board){
                        $board['type'] = 0;
                        $board['url'] = mobileUrl('sns/board', array('id'=>$board['id'], 'page'=>1), $full);
                    }
                    unset($board);
                    $list = array_merge($list, $list_board);
                }
                if(!empty($list_post) && is_array($list_post)){
                    foreach ($list_post as &$post){
                        $post['type'] = 1;
                        $post['url'] = mobileUrl('sns/post', array('id'=>$post['id']), $full);
                    }
                    unset($post);
                    $list = array_merge($list, $list_post);
                }*/
                $list = array();
            }
        }

        include $this->template('util/selecturl_tpl');
    }

}
