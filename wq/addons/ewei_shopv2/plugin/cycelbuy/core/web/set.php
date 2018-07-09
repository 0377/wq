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

class Set_EweiShopV2Page extends PluginWebPage{

    public function main(){
        global $_W, $_GPC;

        if ($_W['ispost']) {
            ca('cycelbuy.set.edit');
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();

            $data['receive_goods']=intval($data['receive_goods']);
            $data['ahead_goods']=intval($data['ahead_goods']);
            $data['days']=intval($data['days']);
            $data['max_day']=intval($data['max_day']);
            $data['terminal']=intval($data['terminal']);

            if($data['days']<1){
                show_json( 0 , '请填写正确天数' );
            }

            m('common')->updateSysset(array('cycelbuy' => $data));
            plog('cycelbuy.set.edit', '周期购-修改基本设置');
            show_json(1, array('url' => webUrl('cycelbuy/set')));
        }
        $data = m('common')->getSysset('cycelbuy');
        include $this->template();
    }

}