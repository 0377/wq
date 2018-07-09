<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Picker_EweiShopV2Page extends PluginMobilePage  {

    public $year;//年
    public $month;//月
    public $day;//日
    public $weekday;//今天周几

    function date_list(){
        global $_W,$_GPC;

        $tmonth = $_GPC['tmonth'];
        $tdate = $_GPC['tdate'];

        //判断来源
        $from= trim($_GPC['from']);
        if(!empty($from) && $from=='create'){
            //增加时间范围
            $cycelbuy_sys = m('common') -> getSysset('cycelbuy');
            $ahead_goods = intval($cycelbuy_sys['ahead_goods']);//提前下单天数
            $days= intval($cycelbuy_sys['days']);//开始时间可选天数
            if(empty($ahead_goods)){
                $ahead_goods=3;
            }
            if(empty($days)){
                $days=7;
            }
            $ttime = time() + 86400*$ahead_goods;//加上 提前下单时间 的当前日期
            $endtimes=$ttime+($days*86400);//加上 开始时间可选天数 的截至日期
        }else if(!empty($from) && $from=='update'){

            $receipttime= trim($_GPC['receipttime']);//当前收货时间
            $period_index=intval($_GPC['period_index']);//当前期数
            $select_receipttime=trim($_GPC['select_receipttime']);//选择的日期


            $cycelbuy_periodic=trim($_GPC['cycelbuy_periodic']);//周期订单的周期信息 (天数，单位1天2周3月，期数)
            if(!empty($cycelbuy_periodic)){
                $cycelbuy_periodic=explode(",",$cycelbuy_periodic);
            }

            $cycelbuy_sys = m('common') -> getSysset('cycelbuy');
            $max_day = intval($cycelbuy_sys['max_day']);//最多可延期的天数

            if(empty($max_day)){
                $max_day=15;
            }

            $isall=intval($_GPC['isall']);
            if(empty($isall)){
                $ttime =strtotime($receipttime);
                $endtimes=$ttime+($max_day*86400);//加上 最多可延期天数 的可选天数的截至日期
            }else{

                $ttime =strtotime($receipttime);
                $endtimes=$ttime+($max_day*86400);//加上 最多可延期天数 的可选天数的截至日期
                $unit_time=array(1,7,30);
                $node_time=array();

                if(empty($select_receipttime)){
                    $select_receipttime=$receipttime;
                }

                $select_time=strtotime($select_receipttime);

                array_push($node_time,date("Ymd",$select_time));
                for ($i = 1 ; $i <intval($cycelbuy_periodic[2])-$period_index ; $i++){
                    array_push($node_time,date("Ymd",$select_time+(($unit_time[intval($cycelbuy_periodic[1])]*intval($cycelbuy_periodic[0])*$i)*86400)));
                }

            }

        }else{
            $ttime = time();
        }


        $month = '';
        if ($tmonth > 0) {
            $month = "+".$tmonth." month";
        } else if ($tmonth < 0) {
            $month = $tmonth." month";
        }

        $firstday = date("Y-m-01", $ttime);

//        $firstday=date('Y-m-d',strtotime("$firstday -1 month"));

        $ftime = strtotime("$firstday ".$month);

        $this->year = intval(date('Y', $ftime));

        $this->month = intval(date('m', $ftime));
        $this->day = intval(date('d', $ftime));
        $this->weekday = intval(date('w', $ftime));

        $rangesize = 1;//显示接下来的12个月
        $calendar = array();

        while ($rangesize > 0){
            $month_first_weekday = date('w',strtotime($this->year.'-'.$this->month.'-01'));
            if ($month_first_weekday == 0){
                $emptydays = 6;
            }else{
                $emptydays = $month_first_weekday - 1;
            }
            $total = $this->how_many_days($this->month, $this->year);//当月多少天
            $calendar[$this->year.'-'.$this->month] = array($this->year,$this->month++,$total,$emptydays);
            if ($this->month > 12){
                $this->month = 1;
                $this->year++;
            }
            $rangesize--;
        }
        $weekarray = array("日","一","二","三","四","五","六");


        //选择的日期，当天
        if(!empty($tdate)){
            $year = date('Y',$ttime);
            if(intval($this->month-1) < 10){
                $tmonth='0'.intval($this->month-1);
            }
            if(intval($tdate)<10){
                $tdate = '0'.intval($tdate);
            }
            $nowtime = $year.$tmonth.$tdate;
        }else{
            $nowtime = date('Ymd',$ttime);
        }


        include $this->template();
    }

    //检测是否是闰年
    function is_leap_year($year){
        global $_W;
        if ($year % 400 === 0){
            return true;
        }elseif ($year % 100 !== 0){
            if ($year % 4 === 0) return true;
        }
        return false;
    }

    //返回本月有多少天
    function how_many_days($month, $year){

        if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12){
            for ($i = 0 ; $i < 31 ; $i++){
                $return[$i]=1;
            }
            return $return;
        }elseif ($month == 2){
            if ($this->is_leap_year($year)){
                for ($i = 0 ; $i < 29 ; $i++){
                    $return[$i]=1;
                }
                return $return;
            }else{
                for ($i = 0 ; $i < 28 ; $i++){
                    $return[$i]=1;
                }
                return $return;
            }
        }else{
            for ($i = 0 ; $i < 30 ; $i++){
                $return[$i]=1;
            }
            return $return;
        }
    }

/*    function time(){
        global $_W,$_GPC;

        $goodsid = intval($_GPC['goodsid']);
        $storeid = intval($_GPC['storeid']);

        $ttime = $_GPC['ttime'];
        $tmonth = $_GPC['tmonth'];
        $tdate = $_GPC['tdate'];

        $store_goods_option = $this->model->getNgoodsOptionList($tdate, $goodsid, $storeid);

        $html = "";
        if (!empty($store_goods_option)) {
            $i = 0;
            foreach ($store_goods_option as $k => $v) {
                if ($i == 3) {
                    break;
                }
                $time = str_replace(':', '_', $v['optime']);
                $html .= "<div class='time_item' id='op_time".$time."' data-id='".$v['optime']."'>";
                $html .= "<p>{$v['optime']}</p>";
                $html .= "</div>";
                $i++;
            }
            if(count($store_goods_option)>3)
            {
                $html.="<div class=\"time_item chose-time\" id=\"other_time\"><p>其他时间</p></div>";

            }
        }else{
            $html.="<div class=\"fui-cell-info\" style=\" font-size: 0.7rem;height: 3rem;\"><p style=\"margin: 1rem 0;\">当前无可预约时间</p></div>";
        }

        echo $html;exit;
    }

    function time_list(){
        global $_W,$_GPC;

        $goodsid = intval($_GPC['goodsid']);
        $storeid = intval($_GPC['storeid']);

        $ttime = $_GPC['ttime'];
        $tmonth = $_GPC['tmonth'];
        $tdate = $_GPC['tdate'];

        $store_goods_option = $this->model->getNgoodsOptionList($tdate, $goodsid, $storeid);

        $html = "";
        if (!empty($store_goods_option)) {
            foreach ($store_goods_option as $k => $v) {
                $time = str_replace(':', '_', $v['optime']);
                $html .= "<div id='optime".$time."' class='optime";
                $html .= "' data-id='".$v['optime']."'>".$v['optime']."</div>";
            }
        }

        echo $html;exit;
    }*/

    public function getDayNum(){
        global $_GPC,$_W;
        $res = $this -> how_many_days($_GPC['month']+1,$_GPC['year']);
        $arr['num'] = count($res);
        echo  json_encode($arr);
        exit;
    }


}