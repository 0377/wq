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

class Staff_EweiShopV2Page extends WebPage {


    function main(){
        global $_W,$_GPC;


        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;

        $id = intval($_GPC['id']);
        $condition = '  np.uniacid = :uniacid  and np.deleted = 0 ';
        $params =array(":uniacid"=>$_W['uniacid']);

        $keyword = trim($_GPC['keyword']);
        if (!empty($keyword)) {
            $condition.=' and (np.nickname like :keyword or np.realname like :keyword or np.storeid like :keyword or st.storename like :keyword )';
            $params[':keyword'] = "%{$keyword}%";
        }

        $sql='SELECT np.*,st.storename FROM ' . tablename('ewei_shop_newstore_people') . ' np
            inner join '.tablename('ewei_shop_store').' st on np.storeid=st.id
            WHERE 1 and'. $condition.'ORDER BY np.id DESC';


        $sql.=' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;


        $list = pdo_fetchall($sql ,$params);

        $pager = pagination2(count($list), $pindex, $psize);



        include $this->template();
    }

    function add() {
        $this->post();
    }

    function edit() {
        $this->post();
    }

    function post()
    {
        global $_W, $_GPC;

        $TempType = p("newstore")->getTempType();
        $types =array();
        foreach($TempType as $key=>$row)
        {
            if(!empty($row['trade']))
            {
                $types[$key]=$row['trade'];
            }
        }

        $id = intval($_GPC['id']);
        $item = pdo_fetch("SELECT * FROM ".tablename('ewei_shop_newstore_people')." WHERE uniacid = :uniacid AND id = :id",array(':uniacid'=>$_W['uniacid'],':id'=>$id));
        if(!empty($item['storeid']))
        {
            $store = pdo_fetch("SELECT * FROM ".tablename('ewei_shop_store')." WHERE uniacid = :uniacid AND id = :id",array(':uniacid'=>$_W['uniacid'],':id'=>$item['storeid']));
        }

        if ($_W['ispost']){
            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['displayorder'] = intval($_GPC['displayorder']);
            $data['avatar'] = $_GPC['thumb'];
            $data['nickname'] = $_GPC['nickname'];
            $data['realname'] = $_GPC['realname'];
            $data['storeid'] = intval($_GPC['storeid']);
            $data['type'] = intval($_GPC['type']);
            $data['sex'] = intval($_GPC['sex']);
            $data['mobile'] = $_GPC['mobile'];
            $data['content'] = $_GPC['content'];
            $data['btime'] = $_GPC['btime'];
            $data['etime'] = $_GPC['etime'];;
            $data['status'] = intval($_GPC['status']);

            if (empty($id)){
                pdo_insert('ewei_shop_newstore_people', $data);
            }else{
                pdo_update('ewei_shop_newstore_people', $data, array('id'=>$id));
            }
            show_json(1,array("url"=>webUrl('store/staff')));
        }

        include $this->template();
    }


    function setstatus() {

        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        $status = empty($_GPC['status']) ? 1:0;
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }

        $items = pdo_fetchall("SELECT id FROM " . tablename('ewei_shop_newstore_people') . " WHERE id in( $id )  AND uniacid=" . $_W['uniacid']);


        foreach ($items as $item) {

            pdo_update('ewei_shop_newstore_people',array('status'=>$status),array('id'=>$item['id'],'uniacid'=>$_W['uniacid']));

            //plog('sale.coupon.delete', "删除优惠券 ID: {$id}  <br/>优惠券名称: {$item['couponname']} ");
        }
        show_json(1, array('url' => referer()));
    }

    function delete() {

        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }

        $items = pdo_fetchall("SELECT id FROM " . tablename('ewei_shop_newstore_people') . " WHERE id in( $id )  AND uniacid=" . $_W['uniacid']);

        foreach ($items as $item) {

            pdo_delete('ewei_shop_newstore_people',array('id'=> $item['id'],'uniacid'=>$_W['uniacid']));

            //plog('sale.coupon.delete', "删除优惠券 ID: {$id}  <br/>优惠券名称: {$item['couponname']} ");
        }
        show_json(1, array('url' => referer()));
    }

    function setdisplayorder(){
        global $_W,$_GPC;
        $id = intval($_GPC['id']);
        $total = floatval($_GPC['value']);
        pdo_update('ewei_shop_newstore_people',array('displayorder'=>$total),array('id'=>$id,'uniacid'=>$_W['uniacid']));
        show_json(1);
    }
}