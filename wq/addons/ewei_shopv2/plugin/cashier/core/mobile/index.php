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
require EWEI_SHOPV2_PLUGIN . 'cashier/core/inc/mobile_cashier.php';
class Index_EweiShopV2Page extends CashierMobilePage {

    private $operatorid = 0;

    public function __construct()
    {
        global $_W;
        parent::__construct();
        $this->operatorid = (int)pdo_fetchcolumn("SELECT id FROM ".tablename('ewei_shop_cashier_operator')." WHERE manageopenid=:manageopenid AND uniacid=:uniacid LIMIT 1",array(':manageopenid'=>$_W['openid'],':uniacid'=>$_W['uniacid']));
        if (empty($this->operatorid) && $_W['openid'] != $_W['cashieruser']['manageopenid']){
            $this->message("您不是我们的收款员!",'close','error');
        }
    }
	public function main()
    {
        global $_W,$_GPC;
        $cashierid = intval($_GPC['cashierid']);
        include $this->template();
    }

    public function collection()
    {
        global $_W,$_GPC;
        $paytype = $this->model->paytype(-1,trim($_GPC['auth_code']));
        if (is_error($paytype)){
            show_json(-101,$paytype['message']);
        }
        $res = $this->model->createOrder(array(
            'auth_code'=>$_GPC['auth_code'],
            'paytype'=>(int)$paytype,
            'money'=>(float)$_GPC['money'],
            'operatorid'=>$this->operatorid,
        ));
        $success = $this->model->payResult($res['id']);
        $success ? show_json(1,$res['id']) : show_json(0,$res['id']);
    }
}
