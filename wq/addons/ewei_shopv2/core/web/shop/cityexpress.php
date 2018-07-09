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

class Cityexpress_EweiShopV2Page extends WebPage {

    function main() {
        global $_W, $_GPC;

        $cityexpress = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_city_express') . " WHERE uniacid=:uniacid AND merchid=:merchid",array(":uniacid"=>$_W['uniacid'],":merchid"=>0));
        //修改
        if ($_W['ispost']) {

            $data = array(
                'uniacid' => $_W['uniacid'],
                'merchid' => 0,
                'start_fee' => floatval(round($_GPC['start_fee'],2)),
                'start_km' => intval($_GPC['start_km']),
                'pre_km' => intval($_GPC['pre_km']),
                'pre_km_fee' => floatval(round($_GPC['pre_km_fee'],2)),
                'fixed_km' => intval($_GPC['fixed_km']),
                'fixed_fee' => floatval(round($_GPC['fixed_fee'],2)),
                'receive_goods' => intval($_GPC['receive_goods']),
                'geo_key'=>trim($_GPC['geo_key']),
                'lat' => trim($_GPC['lat']),
                'lng' => trim($_GPC['lng']),
                'range' => intval($_GPC['range']),
                'zoom' => intval($_GPC['zoom']),
                'express_type' => intval($_GPC['express_type']),
                'config' => iserializer($this->TrimArray($_GPC['config'])),
                'tel1' => trim($_GPC['tel1']),
                'tel2' => trim($_GPC['tel2']),
                'is_sum' => trim($_GPC['is_sum']),
                'is_dispatch' => trim($_GPC['is_dispatch']),
                'enabled' => intval($_GPC['enabled'])
            );

            if (!empty($cityexpress)) {
                plog('shop.cityexpress.edit', "修改同城配送 ID: {$cityexpress['id']}");
                pdo_update('ewei_shop_city_express', $data, array('id' => $cityexpress['id']));
            } else {
                pdo_insert('ewei_shop_city_express', $data);
                $id = pdo_insertid();
                plog('shop.cityexpress.add', "添加同城配送 ID: {$id}");
            }

            show_json(1, array('url' => webUrl('shop/cityexpress')));
        }



        if (!empty($cityexpress)) {
            $config=unserialize($cityexpress['config']);

            //如果是达达
            if($cityexpress['express_type']==1){
                $cityexpress['app_key']=$config['app_key'];
                $cityexpress['app_secret']=$config['app_secret'];
                $cityexpress['source_id']=$config['source_id'];
                $cityexpress['shop_no']=$config['shop_no'];
                $cityexpress['city_code']=$config['city_code'];
            }
        }
        include $this->template();
    }

    //清除数组中字符串元素两边的空格
    function TrimArray($arr){
       foreach ($arr as $key=>$val){
           $arr[$key]=trim($val);
       }
        return $arr;
    }

    function getlocation(){
        global $_W, $_GPC;
        $data=m('util')->geocode($_GPC['city']);
        if($data['status']==1 && $data['count']>0){
            $location=explode(',',$data['geocodes'][0]['location']);
            show_json(1, array('location' => $location));
        }
    }
}
