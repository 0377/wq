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

class Debug_EweiShopV2Page extends WebPage
{

    function main()
    {
        global $_W, $_GPC;
//        phpinfo();


        $data = m('common')->getPluginset('dividend');
        $data['init'] =0;
        m('common')->updatePluginset(array('dividend'=>$data));

//        $setdata = pdo_fetchall("select * from " . tablename('ewei_shop_member') . ' where isheads=1 and headsstatus=1', array(':uniacid' => $_W['uniacid']));
//        dump($setdata);die();
//        foreach ($setdata as $me){
//            $this->createTeam($me['openid']);
//        }



//        $setdata = pdo_fetchall("select * from " . tablename('ewei_shop_member') . ' where agentid=25215 ', array(':uniacid' => $_W['uniacid']));
//        dump($setdata);


//        $setdata = pdo_fetchall("select * from " . tablename('ewei_shop_order') . ' where id=17105 ', array(':uniacid' => $_W['uniacid']));
//        dump($setdata);


//        dump($_W['siteroot']);

    }



    public  function createTeam($op){
        $member = m('member') -> getMember($op);
        if(empty($member['isheads']) || empty($member['headsstatus'])){
            show_json(1,'您还不是队长');
        }
        $data = pdo_fetchall('select id from '.tablename('ewei_shop_commission_relation').' where pid = :pid',array(':pid'=>$member['id']));
        if(!empty($data)){

            $ids = array();
            foreach($data as $k => $v){
                $ids[] = $v['id'];
            }
            if(!empty($ids)){
                pdo_update('ewei_shop_member', array("headsid"=>$member['id']), array('id' =>$ids));
            }
        }
    }
}