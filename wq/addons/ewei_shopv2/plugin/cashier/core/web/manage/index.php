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
require EWEI_SHOPV2_PLUGIN . 'cashier/core/inc/page_cashier.php';
class Index_EweiShopV2Page extends CashierWebPage
{
    function main()
    {
        global $_W, $_GPC;
        $userset = $this->getUserSet();
        if ($_W['ispost']){
            $data = $_GPC['data'];
            $paytype = $this->model->paytype((int)$data['paytype'],$data['auth_code']);
            if (is_error($paytype)){
                show_json(-101,$paytype['message']);
            }
            if ((float)$data['money'] <= 0){
                show_json(-101,'请输入有效收款金额');
            }

            if (empty($data['mobile']) || empty($userset['use_credit2'])){
                $data['mobile'] = 0;//会员手机号
                $data['deduction'] = 0;//余额抵扣金额
            }

            if (!empty($data['mobile'])){
                $userinfo = m('member')->getMobileMember($data['mobile']);
            }

            $res = $this->model->createOrder(array(
                'auth_code'=>$data['auth_code'],
                'paytype'=>(int)$paytype,
                'money'=>(float)$data['money'],
                'openid'=>isset($userinfo['openid']) ? $userinfo['openid'] : '',
                'mobile'=>(int)$data['mobile'],
                'deduction'=>(float)$data['deduction'],
                'operatorid'=>isset($_W['cashieruser']['operator']) ? $_W['cashieruser']['operator']['id'] : 0,
            ));
            if (is_error($res['res'])){
                if($res['res']['errno'] == -2){
                    $message = explode(':',$res['res']['message']);
                    if ($message[0] != 'USERPAYING' && $message[0] != 'need_query'){
                        show_json(-101,$res['res']);
                    }
                }else{
                    show_json(-101,$res['res']);
                }
            }
            $success = $this->model->payResult($res['id']);
            $success ? show_json(1,'收款成功!') : show_json(0,$res['id']);
        }

        include $this->template();
    }

    function quit()
    {
        global $_W, $_GPC;
        unset($_SESSION['__cashier_' . (int)$_GPC['i'] . '_session']);
        header('location: ' . cashierUrl('login'));
    }

    function query()
    {
        global $_W, $_GPC;
        $kwd = trim($_GPC['keyword']);
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $condition = " and uniacid=:uniacid";

        if (!empty($kwd)) {
            $condition .= " AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)";
            $params[':keyword'] = "%{$kwd}%";
        }
        $ds = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . " WHERE 1 {$condition} order by id asc", $params);

        if ($_GPC['suggest']) {
            die(json_encode(array('value' => $ds)));
        }
        include $this->template();
    }

    public function orderquery()
    {
        global $_W,$_GPC;
        $orderid = $_GPC['orderid'];
        if (!empty($orderid)){
            $res = $this->model->orderQuery($orderid);
            if (!empty($res)){
                show_json(1,array('list'=>$res));
            }
        }
        show_json(0,'支付结果等待中!');
    }

    function query_member()
    {
        global $_W,$_GPC;
        $mobile = trim($_GPC['mobile']);
        if (!$mobile){
            show_json(0);
        }
        $info = m('member')->getMobileMember($mobile);
        if (!empty($info['salt']) && !empty($info['pwd'])){
            show_json(1);
        }else{
            show_json(2);
        }
        show_json(0);
    }

    function verify_password()
    {
        global $_W,$_GPC;
        if ($_W['ispost']){
            $password = trim($_GPC['password']);
            $mobile = trim($_GPC['mobile']);
            $info = m('member')->getMobileMember($mobile);
            if (md5($password.$info['salt']) == $info['pwd']){
                show_json(1,$info);
            }
        }
        show_json(0);
    }

    function set_password()
    {
        global $_W,$_GPC;
        if ($_W['ispost']){
            $password = trim($_GPC['password']);
            $mobile = intval($_GPC['mobile']);
            $info = m('member')->getMobileMember($mobile);
            if (empty($info['salt']) && empty($info['pwd'])){
                $salt = random(8);
                $pwd = md5($password.$salt);
                pdo_update('ewei_shop_member',array('pwd'=>$pwd,'salt'=>$salt),array('id'=>$info['id']));
                show_json(1,$info);
            }
        }
        show_json(0);
    }

    function goodsquery(){
        global $_W, $_GPC;

        $where = '';
        $params = array(
            'uniacid' => $_W['uniacid'],
            'merchid' => $_W['cashieruser']['merchid']
        );
        if (!empty($_GPC['keyword'])){
            $where = " AND (title LIKE :keyword OR subtitle LIKE :keyword OR shorttitle LIKE :keyword)";
            $params[':keyword'] = "%{$_GPC['keyword']}%";
        }

        $ds = pdo_fetchall("SELECT id,uniacid,title,subtitle,shorttitle,thumb,share_icon FROM ".tablename('ewei_shop_goods')." WHERE uniacid=:uniacid AND merchid=:merchid AND cashier=1 {$where}",$params);
        $ds = set_medias($ds,array('thumb','share_icon'));
        include $this->template('index/goodsquery');
    }
}