<?php
/**
 * Created by Yang.
 * User: pc
 * Date: 2016/3/21
 * Time: 20:07
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_EweiShopV2Page extends MerchWebPage
{

    function main()
    {
        global $_W;

        include $this->template();
    }

    /**
     * 查询订单金额
     * @param int $day 查询天数
     * @param bool $is_all 是否是全部订单
     * @param bool $is_avg 是否是查询付款平均数
     * @return bool
     */
    protected function selectOrderPrice($day=0,$is_all=false,$is_avg=false)
    {
        global $_W;
        $day = (int)$day;

        if($day != 0)
        {
            if($day == 30){
                $d = date("t");
                $year = date("Y");
                $month = date("m");
                $createtime1 = strtotime("{$year}-{$month}-1 00:00:00");
                $createtime2 = strtotime("{$year}-{$month}-{$d} 23:59:59");
            }else if($day == 7){
                $yest = date('Y-m-d');
                $createtime1 = strtotime(date('Y-m-d',strtotime("-7 day")));
                $createtime2 = strtotime("{$yest} 23:59:59");//time();//
            }else{
                $yesterday = strtotime("-1 day");
                $yy = date("Y",$yesterday);
                $ym = date("m",$yesterday);
                $yd = date("d",$yesterday);
                $createtime1 = strtotime("{$yy}-{$ym}-{$yd} 00:00:00");
                $createtime2 = strtotime("{$yy}-{$ym}-{$yd} 23:59:59");
            }
        }
        else
        {
            $createtime1 = strtotime(date('Y-m-d',time()));
            $createtime2 = strtotime(date('Y-m-d',time()))+3600*24-1;
        }

        $time='paytime';
        $where=' and (( status > 0 and (paytime between :createtime1 and :createtime2)) or ((createtime between :createtime1 and :createtime2 ) and status>=0 and paytype=3))';

        //所有订单
        if(!empty($is_all)){
            $time='createtime';
            $where=' and createtime between :createtime1 and :createtime2';
        }

        //付款的订单
        if(!empty($is_avg)){
            $time='paytime';
            $where=' and (status >0 and (paytime between :createtime1 and :createtime2))';
        }

        //成交量统计，按付款时间，并且包含维权换货的、维权没处理的
        $sql = 'select id,price,openid,'.$time.'  from '.tablename('ewei_shop_order').' where uniacid = :uniacid and merchid = :merchid and ismr=0 and isparent=0  and deleted=0 '.$where;

        $param = array(
            ':uniacid'=>$_W['uniacid'],
            ':merchid'=>$_W['merchid'],
            ':createtime1'=>$createtime1,
            ':createtime2'=>$createtime2,
        );

        $pdo_res = pdo_fetchall($sql,$param);

        $price = 0;
        $avg=0;
        $member=array();
        foreach ($pdo_res as $arr){
            $price += $arr['price'];
            $member[]=$arr['openid'];
        }

        if(!empty($is_avg)) {
            //消费者去重复并得出总数
            $member_num = count(array_unique($member));
            $avg = empty($member_num) ? 0 : round($price / $member_num, 2);
        }

        $result = array(
            'price' => round($price,2),
            'count' => count($pdo_res),
            'avg'=>$avg,
            'fetchall' => $pdo_res,
        );
        return $result;
    }

    /**
     * 查询近七天交易记录
     * @param array $pdo_fetchall 查询订单的记录
     * @param int $days 查询天数默认7
     * @param int $is_all 是否是全部订单
     * @return $transaction["price"] 七日 每日交易金额
     * @return $transaction["count"] 七日 每日交易订单数
     */
    protected function selectTransaction(array $pdo_fetchall,$days=7,$is_all=false)
    {
        $transaction = array();
        $days = (int)$days;
        if (!empty($pdo_fetchall))
        {
            for ($i = $days; $i >=1;$i--)
            {
                $transaction['price'][date("Y-m-d",time()-$i*3600*24)] = 0;
                $transaction['count'][date("Y-m-d",time()-$i*3600*24)] = 0;
            }

            if(empty($is_all)){
                foreach($pdo_fetchall as $key=>$value)
                {
                    if ( array_key_exists(date("Y-m-d",$value['paytime']),$transaction['price']))
                    {
                        $transaction['price'][date("Y-m-d",$value['paytime'])] += $value['price'];
                        $transaction['count'][date("Y-m-d",$value['paytime'])] += 1;

                    }
                }
            }else{
                foreach($pdo_fetchall as $key=>$value)
                {
                    if ( array_key_exists(date("Y-m-d",$value['createtime']),$transaction['price']))
                    {
                        $transaction['price'][date("Y-m-d",$value['createtime'])] += $value['price'];
                        $transaction['count'][date("Y-m-d",$value['createtime'])] += 1;
                    }
                }
            }
            return $transaction;
        }
        return array();
    }

    public function ajaxorder()
    {
        global $_GPC;
        $day = (int)$_GPC['day'];

        //成交量
        $order = $this->selectOrderPrice($day);
        unset($order['fetchall']);

        //交易量
        $allorder=$this->selectOrderPrice($day,true);
        unset($allorder['fetchall']);

        //平均数
        $avg=$this->selectOrderPrice($day,true,true);
        unset($allorder['fetchall']);

        $order=array(
            'order_count'=>$order['count'],
            'order_price'=>$order['price'],
            'allorder_count'=>$allorder['count'],
            'allorder_price'=>$allorder['price'],
            'avg'=>$avg['avg'],
        );
        show_json(1,array('order'=>$order));
    }

    /**
     * ajax return 七日交易记录.近7日交易时间,交易金额,交易数量
     */
    function ajaxtransaction()
    {
        //成交量
        $orderPrice = $this->selectOrderPrice(7);
        $transaction = $this->selectTransaction($orderPrice['fetchall'],7);
        if (empty($transaction))
        {
            for ($i = 7; $i >=1;$i--)
            {
                $transaction['price'][date("Y-m-d",time()-$i*3600*24)] = 0;
                $transaction['count'][date("Y-m-d",time()-$i*3600*24)] = 0;
            }
        }

        //交易量
        $allorderPrice = $this->selectOrderPrice(7,true);
        $alltransaction = $this->selectTransaction($allorderPrice['fetchall'],7,true);
        if (empty($transaction))
        {
            for ($i = 7; $i >=1;$i--)
            {
                $alltransaction['price'][date("Y-m-d",time()-$i*3600*24)] = 0;
                $alltransaction['count'][date("Y-m-d",time()-$i*3600*24)] = 0;
            }
        }

        echo json_encode(array(
            'price_key'=>array_keys($transaction['price']),//交易时间
            'price_value'=>array_values($transaction['price']),//成交金额
            'count_value'=>array_values($transaction['count']),//成交数量
            'allprice_value'=>array_values($alltransaction['price']),//交易金额
            'allcount_value'=>array_values($alltransaction['count']),//交易数量
        ));
    }
}