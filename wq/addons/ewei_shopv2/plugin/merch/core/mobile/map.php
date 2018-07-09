<?php

/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */

class Map_EweiShopV2Page extends PluginMobilePage
{

    function  main(){
        global $_W, $_GPC;
        $merchid =intval($_GPC['merchid']);
        $store = pdo_fetch('select * from ' . tablename('ewei_shop_merch_user') . ' where id=:merchid and uniacid=:uniacid Limit 1', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
        $store['storename']=$store['merchname'];

        //如果位数大于6位，则转换为百度坐标
        $lat_num=explode ( '.', $store['lat'] );
        if (sizeof ( $lat_num ) > 1) {
            $decimal = end ( $lat_num );
            $count = strlen ( $decimal );

            if($count>6){
                $gcj02 = $this->Convert_BD09_To_GCJ02($store['lat'],$store['lng']);
                $store['lat'] =$gcj02['lat'];
                $store['lng'] =$gcj02['lng'];
            }
        }


        include $this->template();
    }

    function Convert_BD09_To_GCJ02($lat,$lng){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        return array('lat'=>$lat,'lng'=>$lng);
    }
}