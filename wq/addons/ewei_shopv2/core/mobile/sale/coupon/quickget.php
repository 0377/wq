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

class Quickget_EweiShopV2Page extends MobileLoginPage {

    public function main(){
        global $_W, $_GPC;


        $id = intval($_GPC['id']);

        $openid= $_W['openid'];
        $member = m('member')->getMember($openid);
        if(empty($member))
        {
            header('location: ' . mobileUrl());die;
        }
        $time = time();

        $coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where  (timelimit = 0  or  (timelimit =1 and timestart<=:time && timeend>=:time)) and uniacid=:uniacid  and id=:id', array(':uniacid' => $_W['uniacid'],':id' => $id,':time' => $time));



        if (empty($coupon)||empty($coupon['quickget'])) {
            header('location: ' . mobileUrl());die;
        }


        //增加优惠券日志
        $couponlog = array(
            'uniacid' => $_W['uniacid'],
            'openid' => $member['openid'],
            'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'),
            'couponid' => $id,
            'status' => 1,
            'paystatus' => -1,
            'creditstatus' => -1,
            'createtime' => time(),
            'getfrom' => 8
        );
        pdo_insert('ewei_shop_coupon_log', $couponlog);

        //增加用户优惠券
        $data = array(
            'uniacid' => $_W['uniacid'],
            'openid' => $member['openid'],
            'couponid' => $id,
            'gettype' => 8,
            'gettime' => time()
        );
        pdo_insert('ewei_shop_coupon_data', $data);
        $id = pdo_insertid();

        header('location: ' . mobileUrl('sale/coupon/my/showcoupons2',array("id"=>$id)));

    }
}
