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

class Notice_EweiShopV2Page extends PluginWebPage {


    function main()
    {
        global $_W, $_GPC;
        if ($_W['ispost']) {
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
            if (is_array($_GPC['openids'])) {
                $data['openid'] = implode(",", $_GPC['openids']);
            }
            m('common')->updatePluginset(array('cashier'=>array('notice'=>$data)));
            plog('cashier.notice.edit', '修改收银台通知设置');
            show_json(1);
        }
        $data = m('common')->getPluginset('cashier');
        $notice = $data['notice'];
        $salers = array();
        if (!empty($notice['openid'])) {
            $openids = array();
            $strsopenids = explode(",", $notice['openid']);
            foreach ($strsopenids as $openid) {
                $openids[] = "'" . $openid . "'";
            }
            $salers = pdo_fetchall("select id,nickname,avatar,openid from " . tablename('ewei_shop_member') . ' where openid in (' . implode(",", $openids) . ") and uniacid={$_W['uniacid']}");
        }
        $template_list= pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_member_message_template') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
        include $this->template();
    }

}
