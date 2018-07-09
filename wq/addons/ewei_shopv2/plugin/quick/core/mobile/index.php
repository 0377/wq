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

class Index_EweiShopV2Page extends PluginMobileLoginPage {

    public function main(){
        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        if(empty($id)){
            $this->message("您访问的页面不存在", mobileUrl());
        }

        $item = pdo_fetch("SELECT * FROM". tablename("ewei_shop_quick")." WHERE id=:id AND uniacid=:uniacid", array(':id'=>$id, ':uniacid'=>$_W['uniacid']));
        if(empty($item) || empty($item['status'])){
            $this->message("您访问的页面不存在", mobileUrl());
        }
        $datas = htmlspecialchars_decode(base64_decode($item['datas']));
        if(empty($datas)){
            $this->message("页面数据出错", mobileUrl());
        }

        $merchid = empty($item['merchid'])?0:intval($item['merchid']);

        $data = $this->model->mobile($datas, $merchid);        // 处理手机端显示数据
        if($data['template']==1){
            $datas = 'null';
        }else{
            $datas = $data['datas'];
        }

        if(empty($data['template'])){
            $carts = $this->model->getCart(!empty($data['cartdata'])?$item['id']:0);       // 获取购物车数据
        }else{
            $carts = 'null';
        }

        $cartcount = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 and isnewstore=0  and selected =1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));

        $fromquick = !empty($data['cartdata'])&&empty($data['template'])?$item['id']:0;

        // 处理分享信息
        $member = m('member')->getMember($_W['openid']);
        $mid = $member['isagent']&&$member['status']?$member['id']:0;
        $_W['shopshare'] = array(
            'title' => !empty($item['share_title'])?$item['share_title']:$item['title'],
            'desc' => !empty($item['share_desc'])?$item['share_desc']:$item['title'],
            'link' => mobileUrl('quick',array('id'=>$id,'mid'=>$mid),true) ,
            'imgUrl' => !empty($item['share_icon'])?$item['share_icon']:$_W['shopset']['shop']['logo']
        );
        if(!empty($_W['shopshare']['imgUrl'])){
            $_W['shopshare']['imgUrl'] = tomedia($_W['shopshare']['imgUrl']);
        }

        $hideGoTop = 1;

        if($data['template']==1){
            $shopset = $_W['shopset']['shop'];
            if(p('merch') && !empty($merchid)){
                $merchset = p('merch')->getListUserOne($merchid);
                $shopset = array(
                    'name' => $merchset['merchname'],
                    'logo' => tomedia($merchset['logo'])
                );
            }
            if(!empty($data['style']['shoplogo'])){
                $shopset['logo'] = tomedia($data['style']['shoplogo']);
            }

            if(!empty($data['style']['shopname'])){
                $shopset['name'] = $data['style']['shopname'];
            }
        }

        /*
        $plugin_diypage = p('diypage');
        if($plugin_diypage){
            $diypage = $plugin_diypage->quickPage($item['diypage']);
            if(!empty($diypage)){
                $startadv = $plugin_diypage->getStartAdv($diypage['diyadv']);
            }
        }*/

        include $this->template();
    }

    public function get_list() {
        global $_W, $_GPC;

        $pagesize = 10;
        $page = max(1, intval($_GPC['page']));
        $datatype = intval($_GPC['datatype']);

        $merchid = intval($_GPC['merchid']);

        $goodssort = intval($_GPC['goodssort']);
        $orderby = '';
        if($goodssort==1){
            $orderby = " sales desc, displayorder desc";
        }
        else if($goodssort==2){
            $orderby = " minprice desc, displayorder desc";
        }
        else if($goodssort==3){
            $orderby = " minprice asc, displayorder desc";
        }

        $result = array('pagesize'=>$pagesize, 'page'=>$page, 'list'=>array());

        if($datatype==0){
            $goodsids = trim($_GPC['goodsids']);
            if(!empty($goodsids)){
                $ids = $this->page($goodsids, $pagesize, $page);
                if(!empty($ids)){
                    $goodslist = $this->model->getList(array('ids'=>$ids));
                    $result['list'] = $this->model->sort($ids, $goodslist['list']);
                    $result['total'] = $goodslist['total'];
                    $result['pagesize'] = $pagesize;
                }
            }
        }
        elseif($datatype==1){
            $cateid = intval($_GPC['cateid']);
            if(!empty($cateid)){
                $goodslist = $this->model->getList(array('cate'=>$cateid, 'page'=>$page, 'pagesize'=>$pagesize, 'order'=>$orderby,'merchid'=>$merchid));
                $result['list'] = $goodslist['list'];
                $result['total'] = $goodslist['total'];
                $result['pagesize'] = $pagesize;
            }
        }
        elseif($datatype==2){
            $groupid = intval($_GPC['groupid']);
            if(!empty($groupid)){
                $group = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_goods_group') . " WHERE id=:id and uniacid=:uniacid and merchid=:merchid and enabled=1 limit 1 ", array(':id'=>$groupid, ':uniacid'=>$_W['uniacid'], ':merchid'=>$merchid));
                $goodsids = $group['goodsids'];
                // 根据分分页找出商品
                if(!empty($goodsids)){
                    $ids = $this->page($goodsids, $pagesize, $page);
                    if(!empty($ids)) {
                        $goodslist = $this->model->getList(array('ids'=>$ids, 'order'=>$orderby));
                        $result['list'] = $goodslist['list'];
                        $result['total'] = $goodslist['total'];
                        $result['pagesize'] = $pagesize;
                    }
                }
            }
        }

        show_json(1, $result);
    }

    // goodsids 分页
    protected function page($goodsids, $pagesize, $page){
        $goodsids = explode(",", $goodsids);
        if(count($goodsids)<=$pagesize){
            if($page==1){
                return implode(",", $goodsids);
            }else{
                return '';
            }
        }
        $page = max(1, $page);
        $pindex = ($page-1)*$pagesize;
        $arr = array_slice($goodsids, $pindex, $pagesize);
        return implode(",", $arr);
    }

    // 获取购物车数据
    public function getCart() {
        global $_W, $_GPC;

        $quickid = intval($_GPC['quickid']);
        $carts = $this->model->getCart($quickid, false);       // 获取购物车数据
        show_json(1, $carts);
    }

    public function clearCart() {
        global $_W, $_GPC;

        $quickid = intval($_GPC['quickid']);
        $tablename = empty($quickid)?"ewei_shop_member_cart":"ewei_shop_quick_cart";

        $arr = array(
            'openid'=>$_W['openid'],
            'uniacid'=>$_W['uniacid']
        );
        if(!empty($quickid)){
            $arr['quickid'] = $quickid;
        }
        pdo_update($tablename, array("deleted"=>1), $arr);

        show_json(1);
    }

    // 更新购物车
    public function update() {
        global $_W, $_GPC;

        $quickid = intval($_GPC['quickid']);
        $goodsid = intval($_GPC['goodsid']);
        $optionid = intval($_GPC['optionid']);
        $update = intval($_GPC['update']);
        $total = intval($_GPC['total']);

        if(empty($goodsid)){
            show_json(0, "参数错误(商品id空)");
        }

        $goods = pdo_fetch('select id,marketprice,diyformid,diyformtype,diyfields, isverify, `type`,merchid, cannotrefund from '.tablename('ewei_shop_goods').' where id=:id and uniacid=:uniacid limit 1',array(':id'=>$goodsid,':uniacid'=>$_W['uniacid']));
        if (empty($goods)) {
            show_json(0, '商品未找到');
        }

        if ($goods['isverify'] == 2 || $goods['type'] == 2 || $goods['type'] == 3 || !empty($goods['cannotrefund'])) {
            show_json(0, '此商品不可加入购物车<br>请返回商城直接购买');
        }

        //自定义表单
        $diyform_plugin = p('diyform');
        $diyformid = 0;
        $diyformfields = iserializer(array());
        $diyformdata = iserializer(array());

        if ($diyform_plugin) {
            $diyformdata = $_GPC['diyformdata'];
            if (!empty($diyformdata) && is_array($diyformdata)) {
                $diyformfields = false;
                if( $goods['diyformtype']==1){
                    //模板
                    $diyformid = intval($goods['diyformid']);
                    $formInfo = $diyform_plugin->getDiyformInfo($diyformid);
                    if(!empty($formInfo)){
                        $diyformfields = $formInfo['fields'];
                    }

                } else if($goods['diyformtype']==2){
                    //自定义
                    $diyformfields = iunserializer($goods['diyfields']);
                }

                if(!empty($diyformfields)){
                    $insert_data = $diyform_plugin->getInsertData($diyformfields, $diyformdata);
                    $diyformdata = $insert_data['data'];
                    $diyformfields = iserializer($diyformfields);
                }
            }
        }

        $tablename = empty($quickid)?"ewei_shop_member_cart":"ewei_shop_quick_cart";
        $condition = " goodsid=:id and openid=:openid and deleted=0 and  uniacid=:uniacid ";
        $params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':id' => $goodsid);
        if(!empty($optionid)){
            $condition .= " and optionid=:optionid ";
            $params[':optionid'] = $optionid;
        }

        if(!empty($quickid)){
            $condition .= " and quickid=:quickid ";
            $params[':quickid'] = $quickid;
            $data = pdo_fetch("select id,total,optionid from " . tablename($tablename) . ' where '.$condition.' limit 1', $params);
        }else{
            $data = pdo_fetch("select id,total,optionid from " . tablename($tablename) . ' where '.$condition.' limit 1', $params);
        }

        if (empty($data)) {
            if(empty($total)){
                show_json(1);
            }
            $data = array(
                'uniacid' => $_W['uniacid'],
                'merchid' => intval($_GPC['merchid']),
                'openid' => $_W['openid'],
                'goodsid' => $goodsid,
                'optionid' => $optionid,
                'marketprice' => $goods['marketprice'],
                'total' => $total,
                'diyformid'=>$diyformid,
                'diyformdata'=> $diyformdata,
                'diyformfields'=> $diyformfields,
                'createtime' => time()
            );
            if(!empty($quickid)){
                $data['quickid'] = $quickid;
            }
            pdo_insert($tablename, $data);
        } else {
            if(empty($total)){
                $data['deleted'] = 1;
            }else{
                if($update){
                    $data['total'] = $data['total'] + $total;
                }else{
                    $data['total'] = $total;
                }
                $data['diyformid'] = $diyformid;
                $data['diyformdata'] = $diyformdata;
                $data['diyformfields'] = $diyformfields;
            }

            $arr2 = array('id' => $data['id'], 'uniacid'=>$_W['uniacid']);
            if(!empty($quickid)){
                $arr['quickid'] = $quickid;
            }

            pdo_update($tablename, $data, $arr2);
        }

        show_json(1);

        // 1. 获取quickid
        // 1.1. 如果为空则添加系统购物车
        // 1.2. 如果不为空则添加 指定购物车
    }

    // 提交结算
    public function submit() {
        global $_W,$_GPC;

        $uniacid = $_W['uniacid'];
        $openid =$_W['openid'];
        $member = m('member')->getMember($openid);

        $quickid = intval($_GPC['quickid']);
        $tablename = empty($quickid)?"ewei_shop_member_cart":"ewei_shop_quick_cart";

        $condition = ' and f.uniacid= :uniacid and f.openid=:openid and f.selected=1 and f.deleted=0 ';
        $params = array(':uniacid' => $uniacid, ':openid' => $openid);

        if(!empty($quickid)){
            $condition .= ' and f.quickid=:quickid';
            $params['quickid'] = $quickid;
        }

        $sql = 'SELECT f.id,f.total,f.goodsid,g.total as stock, o.stock as optionstock, g.maxbuy,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,'
            . ' g.productprice,o.title as optiontitle,f.optionid,o.specs,g.minbuy,g.maxbuy,g.unit,f.merchid,g.checked,g.isdiscount_discounts,g.isdiscount,g.isdiscount_time,g.isnodiscount,g.discounts,g.merchsale'
            . ' ,f.selected,g.status,g.deleted as goodsdeleted FROM ' . tablename($tablename) . ' f '
            . ' left join ' . tablename('ewei_shop_goods') . ' g on f.goodsid = g.id '
            . ' left join ' . tablename('ewei_shop_goods_option') . ' o on f.optionid = o.id '
            . ' where 1 ' . $condition . ' ORDER BY `id` DESC ';

        $list = pdo_fetchall($sql, $params);
        if(empty($list)){
            show_json(0,'没有选择任何商品');
        }
        foreach ($list as &$g) {
            if(empty($g['unit'])){
                $g['unit'] = "件";
            }
            if($g['status']!=1 || $g['goodsdeleted']==1){
                show_json(0,$g['title'].'<br/> 已经下架');
            }
            $seckillinfo = plugin_run('seckill::getSeckill',$g['goodsid'] ,$g['optionid'] ,true, $_W['openid']);
            if (!empty($g['optionid'])) {
                $g['stock'] = $g['optionstock'];
            }
            if( $seckillinfo && $seckillinfo['status']==0){
                $check_buy = plugin_run('seckill::checkBuy',  $seckillinfo , $g['title'] ,$g['unit']);
                if(is_error($check_buy)){
                    show_json(-1 ,  $check_buy['message']);
                }
            } else{
                $levelid = intval($member['level']);
                $groupid = intval($member['groupid']);
                //判断会员权限
                if ($g['buylevels'] != '') {
                    $buylevels = explode(',', $g['buylevels']);
                    if (!in_array($levelid, $buylevels)) {
                        show_json(0, '您的会员等级无法购买<br/>' . $g['title'] . '!');
                    }
                }
                //会员组权限
                if ($g['buygroups'] != '') {
                    $buygroups = explode(',', $g['buygroups']);
                    if (!in_array($groupid, $buygroups)) {
                        show_json(0, '您所在会员组无法购买<br/>' . $g['title'] . '!');
                    }
                }
                if ($g['minbuy'] > 0) {
                    if ($g['total'] < $g['minbuy']) {
                        show_json(0, $g['title'] . '<br/> ' . $g['minbuy'] . $g['unit'] . "起售!");
                    }
                }
                if ($g['maxbuy'] > 0) {
                    if ($g['total'] > $g['maxbuy']) {
                        show_json(0, $g['title'] . '<br/> 一次限购 ' . $g['maxbuy'] . $g['unit'] . "!");
                    }
                }
                if ($g['usermaxbuy'] > 0) {
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og '
                        . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id '
                        . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $g['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));
                    if ($order_goodscount >= $g['usermaxbuy']) {
                        show_json(0, $g['title'] . '<br/> 最多限购 ' . $g['usermaxbuy'] . $g['unit'] . "!");
                    }
                }
                if (!empty($optionid)) {
                    $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,stock,`virtual`,weight from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $g['id'], ':id' => $optionid));
                    if (!empty($option)) {
                        if ($option['stock'] != -1) {
                            if (empty($option['stock'])) {
                                show_json(-1, $g['title'] . "<br/>" . $option['title'] . " 库存不足!");
                            }
                        }
                    }
                }else{
                    if ($g['stock'] != -1) {
                        if (empty($g['stock'])) {
                            show_json(0, $g['title'] . "<br/>库存不足!");
                        }
                    }
                }
            }
        }
        show_json(1);
    }


}
