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

class Pay_EweiShopV2Model
{
    private $qpay;

    public function __construct()
    {
        $this->qpay = p('qpay');
    }

    public function __call($method, $args)
    {
        if (!empty($this->qpay) && method_exists($this->qpay, $method)) {
            return call_user_func_array(array($this->qpay,$method),$args);
        }else{
            return error(-1,'没有全付通支付!');
        }
    }
}
