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
class Printset_EweiShopV2Page extends MerchWebPage {

    function main() {
        global $_W, $_GPC;

        $merchid = $_W['merchid'];

        $sys = pdo_fetch('select * from ' . tablename('ewei_shop_exhelper_sys') . ' where uniacid=:uniacid and merchid=:merchid limit 1 ', array(':uniacid' => $_W['uniacid'], ':merchid'=>$merchid));

        if($_W['ispost']){

//            ca('exhelper.printset');

            $port = intval($_GPC['port']);
            $ip = "localhost";

            if(!empty($port)){

                if(empty($sys)){
                    pdo_insert('ewei_shop_exhelper_sys', array("port"=>$port,"ip"=>$ip, 'uniacid'=>$_W['uniacid'], 'merchid'=>$merchid));
                }else{
                    pdo_update('ewei_shop_exhelper_sys', array("port"=>$port,"ip"=>$ip), array('uniacid' => $_W['uniacid'], 'merchid'=>$merchid));
                }

                plog('merch.exhelper.printset.edit', "修改打印机端口 原端口: {$sys['port']} 新端口: {$port}");

                show_json(1);

            }
        }

        include $this->template();
    }

}
