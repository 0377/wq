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

class Index_EweiShopV2Page extends MobilePage {

    function detail() {
        global $_W,$_GPC;




        $id = intval($_GPC['id']);
        $merchid = intval($_GPC['merchid']);

        if($merchid){
            $item =  pdo_fetch("select * from ".tablename('ewei_shop_merch_store')." where id =:id and uniacid=:uniacid and merchid=:merchid",array(':id'=>$id,":uniacid"=>$_W['uniacid'],"merchid"=>$merchid));
        }else{
            $item =  pdo_fetch("select * from ".tablename('ewei_shop_store')." where id =:id and uniacid=:uniacid",array(':id'=>$id,":uniacid"=>$_W['uniacid']));
        }

        $item['logo'] = tomedia($item['logo']);

        //管理员门店标签
        if(!empty($item['tag']))
        {
            $tags = explode(',',$item['tag']);
            if(!empty($tags))
            {
                foreach($tags as &$tag)
                {
                    if(mb_strlen($tag,'UTF-8')>2)
                    {
                        $lable = mb_substr($tag,0,2,'UTF-8');
                    }
                }

                unset($tag);
            }
            $item['taglist'] = $tags;
            $item['hastag'] = 1;
        }else
        {
            $item['hastag'] =0;
        }

        //门店标签
        if(!empty($item['label']))
        {
            $lables = explode(',',$item['label']);
            if(!empty($lables))
            {
                foreach($lables as &$lable)
                {
                    if(mb_strlen($lable,'UTF-8')>4)
                    {
                        $lable = mb_substr($lable,0,4,'UTF-8');
                    }
                }

                unset($lable);
            }
            $item['labellist'] = $lables;
            $item['haslabel'] = 1;
        }else
        {
            $item['haslabel'] =0;
        }

        include $this->template();
    }
}