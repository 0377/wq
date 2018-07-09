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
class Deferred_EweiShopV2Page extends PluginMobilePage {


    public function do_deferred()
    {
        global $_W,$_GPC;
        $openid = $_W['openid'];
        $time = strtotime( $_GPC['time'] );
        $day = 86400;
        $orderid = $_GPC['orderid'];
        $isall = intval($_GPC['isall']);


        if( empty($orderid) ){
            show_json( 0 ,  '缺少订单ID' );
        }

        if( empty( $_GPC['time'] ) ){
            show_json( 0 , '缺少顺延时间' );
        }

        //获取订单信息
        $order = pdo_get( 'ewei_shop_order' , ['id' => $orderid] );

        if( empty( $order ) ){
            show_json( 0 , '没有查到该订单' );
        }
        //截取订单时间间隔
        if( !empty( $order['cycelbuy_periodic'] ) ){
            $arr = explode( ',' , $order['cycelbuy_periodic'] );
        }else{
            show_json( 0 , '无法获取周期' );
        }

        if( $arr[1] ==0){
            //间隔多少天
            $interval = $arr[0]*$day;
        }elseif( $arr[1] == 1){
            $interval = $arr[0]*($day * 7);
        }else{
            $interval = $arr[0]*($day * 30);
        }

        //查询还没有发的期数商品
        $condition = "orderid = :orderid and uniacid = :uniacid and status = 0 order by receipttime asc";

        if(empty($isall) ) {
            $condition .= ' limit 1';
        }
        $param = array(
            'orderid' => $orderid,
            'uniacid' => $_W['uniacid'],
        );
        $data = pdo_fetchall( 'select * from '.tablename('ewei_shop_cycelbuy_periods').' where '.$condition , $param);
        $data['receipttime'] = $time;
        foreach ( $data as $k => $v ){
            //计算相应的日期
            $receipttime = $time+$interval*$k;
            pdo_update( 'ewei_shop_cycelbuy_periods',array('receipttime' => $receipttime),array('id' => $v['id']) );
        }
        $this -> model -> sendMessage(null,$data,'TM_CYCELBUY_SELLER_DATE');
        $this -> model -> sendMessage($openid,$data,'TM_CYCELBUY_BUYER_DATE');
        show_json( 1 , '顺延成功' );

    }

//    public function do_deferred()
//    {
//        global $_W,$_GPC;
//        $time = strtotime( $_GPC['time'] );
//        $day = 86400;
//        $order_id = $_GPC['orderid'];
//        $order_sn = $_GPC['ordersn'];
//        $is_all = $_GPC['is_all'];
//
//        if( empty($order_id) ){show_json( 0 ,  '缺少订单ID' );}
//        if( empty( $time ) ){show_json( 0 , '缺少顺延时间' );}
//        if( empty( $type ) ){show_json( 0 , '缺少顺延类型' );}
//
//        //获取延期信息
//        $postpone = pdo_get( 'ewei_shop_cycelbuy_postpone' , ['order_id' => $order_id,'isdelete' => 0] );
//        if( $postpone ){
//            show_json( 0 , '申请已存在请勿重复提交' );
//        }
//
//        $data = [
//            'order_id' => $order_id,
//            'order_sn' => $order_sn,
//            'time' => $time,
//            'uniacid' => $_W['uniacid'],
//            'is_all' =>$is_all,
//            'createtime' => time()
//        ];
//
//        $result = pdo_insert( 'ewei_shop_cycelbuy_postpone' , $data );
//        if( $result ){
//            show_json( 1 , '申请顺延成功' );
//        }else{
//            show_json( 0 , '申请顺延失败' );
//        }
//    }
//
//    public function course()
//    {
//        global $_W,$_GPC;
//
//        $id = $_GPC['id'] ? : $this->message('缺少参数', '', 'error');
//        $data = pdo_get( 'ewei_shop_cycelbuy_postpone' , ['id' => $id,'isdelete' => 0] );
//        if( empty($data) ){
//            $this->message( '没有此申请','' ,'error' );
//        }
//
//        include $this->template();
//    }
//
//
//    public function del()
//    {
//        global $_W,$_GPC;
//        $id = $_GPC['id'] ? : show_json( 0 , '缺少申请ID' );
//        $result = pdo_update( 'ewei_shop_cycelbuy_postpone' , ['isdelete' => 1] , ['id' => $id] );
//        $result ? show_json( 1 ,'取消成功' ) : show_json( 0 ,'取消失败' );
//    }

}
