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

class Set_EweiShopV2Page extends PluginWebPage {


    function main()
    {
        global $_W, $_GPC;
        if ($_W['ispost']) {
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
            $data['applycontent'] = m('common')->html_images($data['applycontent']);
            $data['regbg'] = save_media($data['regbg']);
            $data['cashalipay'] =(int)$data['cashalipay'];
            $data['cashcard'] = (int)$data['cashcard'];
            $data['withdrawcharge'] = (float)$data['withdrawcharge'];
            m('common')->updatePluginset(array('cashier'=>$data));
            //模板缓存
            plog('cashier.set.edit', '修改基本设置');

            show_json(1,array('url'=>webUrl('cashier/set', array('tab'=>str_replace("#tab_","",$_GPC['tab'])))));
        }
        $form_list = false;
        if(p('diyform')){
            $form_list = p('diyform')->getDiyformList();
        }
        $data = m('common')->getPluginset('cashier');
        $url = $_W['siteroot']."web/cashier.php?i=".$_W['uniacid'];
        $qrcode = m('qrcode')->createQrcode($url);
        include $this->template();
    }

}
