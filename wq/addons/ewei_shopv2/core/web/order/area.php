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

class Area_EweiShopV2Page extends WebPage
{

    function unicodeDecode($name){
        $json = '{"str":"'.$name.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }

    function main()
    {
        global $_W;


//        $v = '[["340000",["安徽","安徽"],"1"]';
//        $response = preg_replace('/(\[|\]|\")/','',$v);
//        $array = explode(",",$response);
//        print_r($array);exit;
//        exit;

        print_r(123);exit;


        load()->func('communication');
        $url = "https://g.alicdn.com/kg/??address/6.0.5/index-min.js?t=1469670241662.js";
//        $url = "https://g.alicdn.com/kg/??address/6.1.0/index-min.js?t=1469670241662.js";
        $response = ihttp_get($url);
        $content = $response['content'];

//        preg_match_all("|[[^>]+(.*)[\d]+\"]|U", $content, $params1);


        $l1_array = array();
        $l2_array = array();
        $l3_array = array();

        $data = array();

        preg_match_all("/[\"[\d]+\",[^>]+(.*)[\d]+\"]/U", $content, $params1);
        if (!empty($params1[0])) {

            foreach($params1[0] as &$row) {
                $response = preg_replace('/(\[|\]|\")/','',$row);
                $city_data = explode(",",$response);

                $code = $city_data[0];
                $name = $city_data[1];
                $pcode = $city_data[3];

//                $name = $this->unicodeDecode($name);
//                if ($code == 440307) {
//                    print_r($city_data);
//                }

                $data[$code]['code'] = $code;
                $data[$code]['status'] = 1;
                $data[$code]['level'] = 0;
                $data[$code]['pcode'] = $pcode;

                if ($pcode == 1 || $pcode == 2) {
                    $l1_array[$code] = $name;
                    $data[$code]['level'] = 1;
                    $data[$code]['name'] = $name;

                } else if ($pcode > 2) {
                    if (array_key_exists($pcode, $l1_array)) {
                        //2级
                        $l2_array[$code] = $name;
                        $data[$code]['level'] = 2;
                        $data[$code]['name'] = $name;
                    } else {
                        if (array_key_exists($pcode, $l2_array)) {
                            //3级
                            if (!array_key_exists($code, $l3_array)) {
                                $l3_array[$code] = $name;
                                $data[$code]['level'] = 3;
                                $data[$code]['name'] = $name;

                            }
                        }
                    }
                } else {
                    $data[$code]['name'] = $name;
                }
            }
        }

        unset($row);

//        print_r($data);exit;

        foreach($data as $k => $v) {
            if (!empty($v)) {
                pdo_insert("ewei_shop_city2", $v);
            }
        }

        print_r("OK");exit;

    }

    function get_area($code)
    {
        global $_W;

        $params = array();
        $params[':code'] = $code;

        $sql = "select * from " . tablename('ewei_shop_city') . " where  code=:code and status=1 Limit 1";
        $item = pdo_fetch($sql, $params);

        return $item;
    }

    function add_area($l1, $l2, $l3)
    {
        global $_W;

        error_reporting(0);
        set_time_limit(0); //永远执行


//        print_r('666');exit;

//        $l1 = 370000;
//        $l2 = 370300;
//        $l3 = 370322;

        load()->func('communication');
        $url = "https://lsp.wuliu.taobao.com/locationservice/addr/output_address_town_array.do?l1=" . $l1 . "&l2=" . $l2 . "&l3=" . $l3 . "&lang=zh-S&_ksTS=1482116962514_7635&callback=jsonp7636&qq-pf-to=pcqq.group";
        $response = ihttp_get($url);
        $content = $response['content'];

        preg_match_all("/[\'[\d]+\',[^>]+(.*)\']/U", $content, $params1);

        print_r($params1[0]);exit;

        $data = array();

        if (!empty($params1[0])) {
            foreach($params1[0] as &$row) {
                $response = preg_replace('/(\[|\]|\')/','',$row);
                $city_data = explode(",",$response);
//                print_r($city_data);exit;

                $code = $city_data[0];
                $name = $city_data[1];
                $pcode = $city_data[2];
                $words = $city_data[3];

                if (!empty($code) && !empty($name)) {
                    $data[$code]['name'] = $name;
                    $data[$code]['code'] = $code;
                    $data[$code]['status'] = 1;
                    $data[$code]['level'] = 4;
                    $data[$code]['pcode'] = $pcode;
                    $data[$code]['words'] = $words;
                }
            }
        }

//        print_r($data);exit;

        if (!empty($data)) {
            foreach($data as $k => $v) {
                if (!empty($v)) {
                    $item2 = $this->get_area($v['code']);
                    if (empty($item2)) {
                        pdo_insert("ewei_shop_city", $v);
                    }
                }
            }

        }

    }

    function add_area2($l1, $l2, $l3)
    {
        global $_W;

        error_reporting(0);
        set_time_limit(0); //永远执行


//        print_r('666');exit;

//        $l1 = 370000;
//        $l2 = 370300;
//        $l3 = 370322;

        load()->func('communication');
        $url = "https://lsp.wuliu.taobao.com/locationservice/addr/output_address_town_array.do?l1=" . $l1 . "&l2=" . $l2 . "&l3&lang=zh-S&_ksTS=1482116962514_7635&callback=jsonp7636&qq-pf-to=pcqq.group";
        $response = ihttp_get($url);
        $content = $response['content'];

        preg_match_all("/[\'[\d]+\',[^>]+(.*)\']/U", $content, $params1);

//        print_r($content);exit;

//        print_r($params1[0]);exit;

        $data = array();

        if (!empty($params1[0])) {
            foreach($params1[0] as &$row) {
                $response = preg_replace('/(\[|\]|\')/','',$row);
                $city_data = explode(",",$response);
//                print_r($city_data);exit;

                $code = $city_data[0];
                $name = $city_data[1];
//                $pcode = $city_data[2];
                $pcode = $l3;
                $words = $city_data[3];

                if (!empty($code) && !empty($name)) {
                    $data[$code]['name'] = $name;
                    $data[$code]['code'] = $code;
                    $data[$code]['status'] = 1;
                    $data[$code]['level'] = 4;
                    $data[$code]['pcode'] = $pcode;
                    $data[$code]['words'] = $words;
                }
            }
        }

//        print_r($data);exit;

        if (!empty($data)) {
            foreach($data as $k => $v) {
                if (!empty($v)) {
                    $item2 = $this->get_area($v['code']);
                    if (empty($item2)) {
                        pdo_insert("ewei_shop_city", $v);
                    }
                }
            }

        }

    }





    function get()
    {
        global $_W;

        set_time_limit(0); //永远执行

        $list = $this->get_area_list(3);

        print_r($list);exit;

//        print_r($list);exit;

        foreach($list as $k3 => $v3) {
            $l3 = $v3['code'];

            $item2 = $this->get_area($v3['pcode']);
            if(!empty($item2)) {
                $l2 = $item2['code'];
                $l1 = $item2['pcode'];

                if (!empty($l2) && !empty($l1)) {
                    $this->add_area($l1, $l2, $l3);
                }
            }
//            print_r($l3 ."\n");
            sleep(1.5);
        }

        print_r("OK2");
    }

    function getnoarea()
    {
        global $_W;

        set_time_limit(0); //永远执行

        $list2 = $this->get_area_list(2);

        print_r($list2);exit;

        foreach($list2 as $k2 => $v2) {
            $l1 = $v2['pcode'];
            $l2 = $v2['code'];

            $list3 = $this->get_child_list($v2['code']);

            if(!empty($list3)) {
                foreach($list3 as $k3 => $v3) {
                    if (!empty($l2) && !empty($l1)) {

                        $l3 = $v3['code'];

//                        print_r($l3);exit;

                        $this->add_area2($l1, $l2, $l3);
                    }
                }

            }
//            print_r($l3 ."\n");
            sleep(1.5);
        }

        print_r("OK3");
    }

    function count()
    {
        global $_W;


        $list = $this->get_area_list(4);

        foreach($list as $k => $v) {

            $params = array();
            $params[':code'] = $v['code'];

            $sql = "select count(1) from " . tablename('ewei_shop_city') . " where  code=:code";
            $count = pdo_fetchcolumn($sql, $params);

            if ($count>1) {
                print_r($count);
                print_r("\n");
                print_r($v['name']);
                print_r("\n");


            }
        }

    }

    function get_area_list($level)
    {
        global $_W;

        $params = array();
        $params[':level'] = $level;

//        $sql = "select * from " . tablename('ewei_shop_city') . " where  level=:level and status=1 and noarea=1 order by id";
//        $sql .= " Limit 3";
        $sql = "select * from " . tablename('ewei_shop_city') . " where  level=:level and status=1";
        $list = pdo_fetchall($sql, $params);

        return $list;
    }


    function get_child_list($pcode)
    {
        global $_W;

        $params = array();
        $params[':pcode'] = $pcode;

        $sql = "select * from " . tablename('ewei_shop_city') . " where  pcode=:pcode and status=1 order by id";
        $list = pdo_fetchall($sql, $params);

        return $list;
    }


    function check_area($name)
    {
        if(strpos($name,"属于")>-1 || strpos($name,"合并")>-1 || strpos($name,"更名为")>-1)
        {
            return 1;
        } else {
            return 0;
        }
    }

    function xml()
    {
        global $_W;

//        print_r("11");exit;

        $xml = '<?xml version="1.0" encoding="utf-8"?>
 <address>
<province name="请选择省份">
	<city name="请选择城市">
		<county name="请选择区域"/>
	</city>
</province>';

        $list1 = $this->get_area_list(1);

//        print_r($list1);exit;

        foreach($list1 as $k1 => $v1) {
            $xml .= "\n";
            $xml .= '<province name="' . $v1['name'] . '" code="' . $v1['code'] . '">';

            $list2 = $this->get_child_list($v1['code']);

            foreach($list2 as $k2 => $v2) {
                $xml .= "\n";
                $xml .= '<city name="' . $v2['name'] . '" code="' . $v2['code'] . '">';

                $list3 = $this->get_child_list($v2['code']);

                foreach($list3 as $k3 => $v3) {
                    $check = $this->check_area($v3['name']);
                    if (!empty($check)) {
                        continue;
                    }

                    $xml .= "\n";
                    $xml .= '<county name="' . $v3['name'] . '" code="' . $v3['code'] . '" />';
                }
                $xml .= "\n";
                $xml .= '</city>';
            }
            $xml .= "\n";
            $xml .= '</province>';
        }
        $xml .= "\n";
        $xml .= '</address>';

        print_r($xml);exit;
    }

    function xmllist()
    {
        global $_W;

//        print_r("33");exit;

        $list2 = $this->get_area_list(2);
//        $count = count($list2);

        print_r($list2);exit;

        foreach($list2 as $k2 => $v2) {

//            if($v2['code'] != 370200) {
//                continue;
////                print_r($v2);exit;
//            }

            $list3 = $this->get_child_list($v2['code']);

            $xml = '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= "\n";
            $xml .= '<address>';

            $xml .= "\n";
            $xml .= '<city name="' . $v2['name'] . '" code="' . $v2['code'] . '">';

            foreach($list3 as $k3 => $v3) {
                $check = $this->check_area($v3['name']);
                if (!empty($check)) {
                    continue;
                }

                $xml .= "\n";
                $xml .= '<county name="' . $v3['name'] . '" code="' . $v3['code'] . '">';

                $list4 = $this->get_child_list($v3['code']);
//            print_r($list4);exit;

                foreach($list4 as $k4 => $v4) {
                    $xml .= "\n";
                    $xml .= '    <street name="' . $v4['name'] . '" code="' . $v4['code'] . '" />';
                }

                $xml .= "\n";
                $xml .= '</county>';
            }

            $xml .= "\n";
            $xml .= '</city>';

            $xml .= "\n";
            $xml .= '</address>';

//            print_r($xml);exit;

            $left = substr($v2['code'], 0, 2);

            $styles = array();
            $dir = IA_ROOT . "/addons/ewei_shopv2/static/js/dist/area/list/" .$left."/";

            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }

            $filename = $dir .$v2['code'].'.xml';
            $fp = fopen($filename, 'w');
            fwrite($fp, $xml);
            fclose($fp);
//            print_r($left);exit;
        }

    }

    function js()
    {
        global $_W;

        $xml = 'var FoxUICityData = [';

        $list1 = $this->get_area_list(1);

        foreach($list1 as $k1 => $v1) {
//            $xml .= "\n";
            $xml .= '{"text": "' . $v1['name'] . '",';
            $xml .= '"children": [';

            $list2 = $this->get_child_list($v1['code']);

            foreach($list2 as $k2 => $v2) {
//                $xml .= "\n";
                $xml .= '{"text": "' . $v2['name'] . '",';
                $xml .= '"children": [';

                $list3 = $this->get_child_list($v2['code']);

                foreach($list3 as $k3 => $v3) {
                    $check = $this->check_area($v3['name']);
                    if (!empty($check)) {
                        continue;
                    }

                    $xml .= '"' . $v3['name'] . '",';
                }
                $xml = trim($xml, ',');
//                $xml .= "\n";
                $xml .= ']},';
            }

//            $xml .= "\n";
            $xml .= ']},';
        }




//        $xml .= "\n";
//        $xml .= '    <province name="境外">
//        <city name="境外地区"></city>
//    </province>
//  </address>';

        $xml = trim($xml, ',');

        $xml .= '];';

        print_r($xml);exit;
    }


    function js2()
    {
        global $_W;

//        exit;

        $xml = 'var FoxUICityDataNew = [';

        $list1 = $this->get_area_list(1);

        foreach($list1 as $k1 => $v1) {
//            $xml .= "\n";
            $xml .= '{"text": "' . $v1['name'] . '",';
            $xml .= '"value": "' . $v1['code'] . '",';
            $xml .= '"children": [';

            $list2 = $this->get_child_list($v1['code']);

            foreach($list2 as $k2 => $v2) {
//                $xml .= "\n";
                $xml .= '{"text": "' . $v2['name'] . '",';
                $xml .= '"value": "' . $v2['code'] . '",';
                $xml .= '"children": [';

                $list3 = $this->get_child_list($v2['code']);

                foreach($list3 as $k3 => $v3) {
                    $check = $this->check_area($v3['name']);
                    if (!empty($check)) {
                        continue;
                    }

//                    $xml .= '"' . $v3['name'] . '",';

                    $xml .= '{"text": "' . $v3['name'] . '",';
                    $xml .= '"value": "' . $v3['code'] . '",';

                    $xml .= '"children": []';

//                    $xml .= "\n";
                    $xml .= '},';
                }
                $xml = trim($xml, ',');
//                $xml .= "\n";
                $xml .= ']},';
            }

//            $xml .= "\n";
            $xml .= ']},';
        }




//        $xml .= "\n";
//        $xml .= '    <province name="境外">
//        <city name="境外地区"></city>
//    </province>
//  </address>';

        $xml = trim($xml, ',');

        $xml .= '];';

        print_r($xml);exit;
    }

    function copy()
    {
        global $_W;

        $list = $this->get_area_list(4);

        foreach($list as $k => $v) {

            if (!empty($v)) {
                if ($v['id'] <= 30340) {
                    continue;
                }
                unset($v['id']);
//                pdo_insert("ewei_shop_city2", $v);
            }
        }
    }

    function query()
    {

        $i = 0;
        $list2 = $this->get_area_list(2);

        $data = array();

        foreach($list2 as $k2 => $v2) {
            $list3 = $this->get_child_list($v2['code']);

            if (empty($list3)){

                $data[$i]['code'] = $v2['code'] . '001';
                $data[$i]['name'] = $v2['name'];
                $data[$i]['level'] = 3;
                $data[$i]['pcode'] = $v2['code'];
                $data[$i]['status'] = 1;
                $data[$i]['ishand'] = 1;

//                pdo_update('ewei_shop_city', array('noarea' => 1), array('id' => $v2['id']));

                $i++;
            }

        }

        foreach($data as $k => $v) {
            if (!empty($v)) {
//                pdo_insert("ewei_shop_city", $v);
            }
        }

        print_r($data);

    }



}