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
require IA_ROOT . '/addons/ewei_shopv2/defines.php';
require EWEI_SHOPV2_INC . 'plugin_processor.php';

class TaskProcessor extends PluginProcessor {

    public function respond_new($obj = null)
    {
        global $_W;
        $message = $obj->message;
        $msgtype = strtolower($message['msgtype']);
        $event = strtolower($message['event']);
        //更新用户信息
        $obj->member = $this->model->checkMember($message['from']);
        if ($msgtype == 'text' || $event == 'click') {
            //文字或点击
            return $this->responseText_new($obj);
        } else if ($msgtype == 'event') {
            if ($event == 'scan') {
                //扫描
                return $this->responseScan_new($obj);
            } else if ($event == 'subscribe') {

                //关注
                return $this->responseSubscribe_new($obj);
            }
        }
    }

    //关键字回复
    private function responseText_new($obj) {
        global $_W;
        //关键词
        $content = $obj->message['content'];
        //取出id
        $id = pdo_fetchcolumn('select id from '.tablename('ewei_shop_task_list').' where keyword_pick = :keyword_pick and uniacid = :uniacid',array(':uniacid'=>$_W['uniacid'],':keyword_pick'=>$content));
        //接取任务
        $timeout = 4;
        load()->func('communication');
        $url = mobileUrl('task.build.picktask',array('id'=>$id),1);
        ihttp_post($url, array('openid' => $obj->message['from']));
    }

    /**
     * 扫描
     * @param $obj
     * @return mixed
     */
    private function responseScan_new($obj) {
        global $_W;
        m('message')->sendCustomNotice($_W['openid'], '您已关注过');
//        return $obj->respText('您已关注过');
    }
    /**
     * 关注
     * @param $obj
     * @return mixed
     */
    function responseSubscribe_new($obj) {
        global $_W;
        $member_info = $obj->member;
        if (strtolower($obj->message['event']) == 'scan'){
            $eventkey = $obj->message['eventkey'];
        }elseif($obj->message['event'] == 'subscribe'){
            $eventkey = $obj->message['scene'];
        }

        $recordid = pdo_fetchcolumn("select recordid from ".tablename('ewei_shop_task_qr')." where uniacid = :uniacid and sceneid = :sceneid",array(':uniacid'=>$_W['uniacid'],':sceneid'=>$eventkey));
        $record = pdo_fetch("select openid,member_group,tasktitle,auto_pick,taskid from ".tablename('ewei_shop_task_record')." where uniacid = :uniacid and id = :recordid",array(':uniacid'=>$_W['uniacid'],':recordid'=>$recordid));
        $list = pdo_fetch("select member_level,followreward from ".tablename('ewei_shop_task_list')." where id = :id and uniacid = :uniacid ",array(':id'=>$record['taskid'],':uniacid'=>$_W['uniacid']));
        $openid = $record['openid'];
        $beagent = iserializer($list['followreward']);

        if (empty($recordid)) return $obj->respText('任务不存在');
        if ($member_info['isnew']){//新会员//会员组
            if (!empty($record['member_group'])){
                m('member')->setGroups($_W['openid'],$record['member_group'],'任务中心，扫码关注，完成任务[#'.$recordid.']'.$record['tasktitle'].'加入会员组');
            }
            //会员等级
            if (!empty($list['member_level'])){
                m('member')->upgradeLevelByLevelId($_W['openid'],$list['member_level']);
            }
            //绑定下级
            $agent = m('member')->getMember($openid);
            //是否有分销
            $p = p('commission');
            if ($p){
                $cset = m('common')->getPluginset('commission');
                $member = m('member')->getMember($_W['openid']);
                if ($member['isagent'] != 1){
                    $become_check = intval($cset['become_check']);
                    if ($agent['isagent']){
                        $a = pdo_update('ewei_shop_member', array('isagent' => 1,'agentid' => $agent['id'], 'status' => $become_check, 'childtime' => time(), 'agenttime' => time()), array('id' => $member['id']));
                    }else{
                        //判断是否直接成为分销商
                        if (!empty($beagent['beagent'])) {
                            pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => $become_check, 'agenttime' => time()), array('id' => $member['id']));
                            if ($become_check == 1) {

                                $p->sendMessage($agent['openid'], array('nickname' => $member['nickname'], 'agenttime' => time()), TM_COMMISSION_BECOME);
                                //检测升级
                                $p->upgradeLevelByAgent($agent['id']);

                                //股东升级
                                if(p('globonus')){
                                    p('globonus')->upgradeLevelByAgent($agent['id']);
                                }
                                //区域代理升级
                                if (p('abonus')) {
                                    p('abonus')->upgradeLevelByAgent($agent['id']);
                                }
                                //创始人升级
                                if(p('author')){
                                    p('author')->upgradeLevelByAgent($agent['id']);
                                }
                            }
                        }
                    }
                }
            }
            //任务进度
            p('task')->checkTaskProgress(1,'poster',$recordid,$openid);

            if ($record['auto_pick'] == 1){//自动接取任务
                $url = mobileUrl('task.build.picktask',array('id'=>$record['taskid']),1);
                //接取任务
                $timeout = 4;
                load()->func('communication');
                ihttp_post($url, array('openid' => $_W['openid']));
            }

            $this -> saleVirtual($obj);
            return $obj->respText('增加了一点人气值'.(string)$a);
        }else{
            $this -> saleVirtual($obj);
            return $obj->respText('您不是首次关注，不能帮TA增加人气');
            return $this->responseEmpty();
        }
    }


    public function saleVirtual($obj=null)
    {

        global $_W;

        $sale = m('common')->getSysset('sale');
        $data = $sale['virtual'];
        if (empty($data['status'])){
            return false;
        }
        load()->model('account');
        $account = account_fetch($_W['uniacid']);

        //会员统计查询
        $open_redis = function_exists('redis') && !is_error(redis());
        if( $open_redis ) {
            $redis_key1 = "ewei_{$_W['uniacid']}_member_salevirtual_isagent";
            $redis_key2 = "ewei_{$_W['uniacid']}_member_salevirtual";
            $redis = redis();
            if (!is_error($redis)) {
                if ($redis->get($redis_key1) != false) {
                    $totalagent = $redis->get($redis_key1);
                    $totalmember = $redis->get($redis_key2);
                } else {
                    $totalagent = pdo_fetchcolumn("select count(*) from" . tablename('ewei_shop_member') . " where uniacid =" . $_W['uniacid'] . " and isagent =1");
                    $totalmember = pdo_fetchcolumn("select count(*) from" . tablename('ewei_shop_member') . " where uniacid =" . $_W['uniacid']);
                    $redis->set($redis_key1, $totalagent, array('nx', 'ex' => '3600'));  //设置缓存  过期时间为1小时
                    $redis->set($redis_key2, $totalmember, array('nx', 'ex' => '3600'));  //设置缓存  过期时间为1小时
                }
            }
        }else{
            $totalagent = pdo_fetchcolumn("select count(*) from" . tablename('ewei_shop_member') . " where uniacid =" . $_W['uniacid'] . " and isagent =1");
            $totalmember = pdo_fetchcolumn("select count(*) from" . tablename('ewei_shop_member') . " where uniacid =" . $_W['uniacid']);
        }

        $acc = WeAccount::create();

        $member = abs((int)$data['virtual_people']) + (int)$totalmember;
        $commission =abs((int)$data['virtual_commission']) + (int)$totalagent;
        $user = m('member')->checkMemberFromPlatform($obj->message['from'],$acc);

        if ($obj->member['isnew'])
        {
            $message = str_replace('[会员数]', $member, $data['virtual_text']);
            $message = str_replace('[排名]', $member+1, $message);
        } else{
            $message = str_replace('[会员数]', $member, $data['virtual_text2']);
        }
        $message = str_replace('[分销商数]', $commission, $message);
        $message = str_replace('[昵称]', $user['nickname'], $message);
        $message = htmlspecialchars_decode($message,ENT_QUOTES);
        $message = str_replace('"','\"',$message);
        return $this->sendText($acc,$obj->message['from'],$message);
    }

    public function sendText($acc,$openid,$content){
        $send['touser'] = trim($openid);
        $send['msgtype'] = 'text';
        $send['text'] = array('content' => urlencode($content));
        $data = $acc->sendCustomNotice($send);
        return $data;
    }

    private $new = false;//新版
    public function __construct() {
        parent::__construct('task');
        $this->new = $this->model->isnew();
    }

    public function respond($obj = null) {
        global $_W;

        if ($this->new){
            $this->respond_new($obj);
            exit;
        }
        $message = $obj->message;
        $msgtype = strtolower($message['msgtype']);
        $event = strtolower($message['event']);
        //更新用户信息
        $obj->member = $this->model->checkMember($message['from']);

        if ($msgtype == 'text' || $event == 'click') {

            return $this->responseText($obj);

        } else if ($msgtype == 'event') {

            if ($event == 'scan') {
                //扫描
                return $this->responseScan($obj);
            } else if ($event == 'subscribe') {
                //关注
                return $this->responseSubscribe($obj);
            }
        }
    }
    private function responseText($obj) {

        global $_W;
        //url调用，避免5秒超时返回
        $timeout = 4;
        load()->func('communication');
        $url = mobileUrl('task/build',array('timestamp'=>TIMESTAMP),true);
        ihttp_request($url, array('openid' => $obj->message['from'], 'content' => urlencode($obj->message['content'])), array(), $timeout);
        unset($_SESSION['postercontent']);
        return $this->responseEmpty();
    }
    private function responseEmpty() {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
    private function responseDefault($obj) {
        global $_W;
        //未找到推荐人， 查找默认回复信息
        return $obj->respText('感谢您的关注!');
    }
    private function responseScan($obj) {
        global $_W;

        $openid = $obj->message['from'];
        $sceneid = $obj->message['eventkey'];
        $ticket = $obj->message['ticket'];

        if (empty($ticket)) {
            return $this->responseDefault($obj);
        }

        $qr = $this->model->getQRByTicket($ticket);
        //$this->respCustom();
        //判断二维码是否为空
        if (empty($qr)) {
            return $this->responseDefault($obj);
        }
        //获取海报信息
        $poster = pdo_fetch('select * from ' . tablename('ewei_shop_task_poster') . ' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$qr['posterid'], ':uniacid' => $_W['uniacid']));
        if (empty($poster)) {
            return $this->responseDefault($obj);
        }
        //用户信息
        $member_info =  $obj->member;
        //推荐者
        $qrmember = m('member')->getMember($qr['openid']);
        $join_info = pdo_fetch('select `join_id`,`needcount`,`completecount`,`failtime`,`task_type`,`reward_data`,`is_reward` from ' . tablename('ewei_shop_task_join') . ' where uniacid=:uniacid and join_user=:join_user and task_id=:task_id and task_type=:task_type and failtime>'.time().' order by addtime DESC limit 1', array(':uniacid' => $_W['uniacid'],':join_user'=>$qrmember['openid'],':task_id'=>$poster['id'],':task_type'=>$poster['poster_type']));
        //判断是否是自己扫描
        if($openid==$qr['openid']){
            //默认文字
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));

            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                if(!empty($default_text['self'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['self'] as $key=>$val){
                        $default_text['self'][$key]['value'] = $this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    return m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['self'], '');
                }
            }
            return m('message')->sendCustomNotice($openid, '扫描自己的海报是不会增加人气值的,快快把你的海报发送给你的小伙伴吧~');
        }
        load()->func('logging');
        //判断用户是否是新用户
        if($member_info['isnew'] || $_SESSION['postercontent']){
            //判断任务完成情况并更新任务进度
            load()->func('logging');
            if(!empty($join_info)){
                if($join_info['task_type']==1){
                    //奖励  条件：任务未过期，已完成，未完成只奖励扫描用户，任务完成奖励双方
                    $this->model->reward($member_info,$poster,$join_info,$qr,$openid,$qrmember);

                }elseif($join_info['task_type']==2){
                    //奖励  条件：任务未过期，已完成，未完成只奖励扫描用户，任务完成奖励双方
                    $this->model->rankreward($member_info,$poster,$join_info,$qr,$openid,$qrmember);
                }
                //分销
                $this->commission($poster, $member_info, $qrmember);
                if (!empty($_SESSION['postercontent'])){
                    $content = trim($_SESSION['postercontent']);
                    $timeout = 10;
                    $url = mobileUrl('task/build',array('timestamp'=>TIMESTAMP),true);
                    ihttp_request($url, array('openid' => $_W['openid'], 'content' => urlencode($content)), array(), $timeout);
                    unset($_SESSION['postercontent']);
                    exit();
                }
            }
        }else{
            $params =array(
                ':uniacid'=>$_W['uniacid'],
                ':task_user'=>$qr['openid'],
                ':joiner_id'=>$openid,
                ':join_id'=>$join_info['join_id']
            );
            //判断是否之前加入过此数据
            $scan_count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_task_joiner') . " where uniacid=:uniacid and task_user=:task_user and joiner_id=:joiner_id and join_id=:join_id ", $params);
            if(!empty($join_info)&&$scan_count&&empty($_SESSION['postercontent'])){
                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
                if(!empty($default_text)) {
                    $default_text = unserialize($default_text);
                    if(!empty($default_text['fail'])){
                        $poster['okdays']= $join_info['failtime'];
                        $poster['completecount']=$join_info['completecount'];
                        foreach ($default_text['fail'] as $key=>$val){
                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                        }
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '您之前已经参加过此任务');
                }
            }elseif (empty($join_info)){
                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
                if(!empty($default_text)) {
                    $default_text = unserialize($default_text);
                    if(!empty($default_text['fail'])){
                        $poster['okdays']= $join_info['failtime'];
                        $poster['completecount']=$join_info['completecount'];
                        foreach ($default_text['fail'] as $key=>$val){
                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                        }
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '此任务已过期或不存在');
                }
            }else{
                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
                if(!empty($default_text)) {
                    $default_text = unserialize($default_text);
                    if(!empty($default_text['fail'])){
                        $poster['okdays']= $join_info['failtime'];
                        $poster['completecount']=$join_info['completecount'];
                        foreach ($default_text['fail'] as $key=>$val){
                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                        }
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '此任务只对新用户开放');
                }
            }
        }


        $url = trim($poster['respurl']);
        if (empty($url)) {
            if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {
                $url = mobileUrl('commission/myshop',array('mid'=>$qrmember['id']));
            } else {
                $url = mobileUrl('',array('mid'=>$qrmember['id']));
            }
        }
        if ($poster['resptype'] == '0')
        {
            if(!empty($poster['resptitle'])){
                $news = array(array('title' => $poster['resptitle'], 'description' => $poster['respdesc'], 'picurl' => tomedia($poster['respthumb']), 'url' => $url));
                return $obj->respNews($news);
            }
        }
        if ($poster['resptype'] == '1')
        {
            if(!empty($poster['resptext'])){
                return $obj->respText($poster['resptext']);
            }
        }

        return $this->responseEmpty();
    }
    //关注处理
    private function responseSubscribe($obj) {
        global $_W;
        $openid = $obj->message['from'];
        $ticket = $obj->message['ticket'];
        $member_info =  $obj->member;

        if (empty($ticket)) {
            return $this->responseDefault($obj);
        }

        $qr = $this->model->getQRByTicket($ticket);
        if (empty($qr)) {
            return $this->responseDefault($obj);
        }

        $poster = pdo_fetch('select * from ' . tablename('ewei_shop_task_poster') . ' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$qr['posterid'], ':uniacid' => $_W['uniacid']));

        if (empty($poster)) {
            return $this->responseDefault($obj);
        }
        //推荐者
        $qrmember = m('member')->getMember($qr['openid']);
        $join_info = pdo_fetch('select `join_id`,`needcount`,`completecount`,`failtime`,`task_type`,`reward_data`,`is_reward` from ' . tablename('ewei_shop_task_join') . ' where uniacid=:uniacid and join_user=:join_user and task_id=:task_id and task_type=:task_type and failtime>'.time().' order by addtime DESC limit 1', array(':uniacid' => $_W['uniacid'],':join_user'=>$qrmember['openid'],':task_id'=>$poster['id'],':task_type'=>$poster['poster_type']));

        //判断是否是自己扫描
        if($openid==$qr['openid']){
            //默认文字
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                if(!empty($default_text['self'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['self'] as $key=>$val){
                        $default_text['self'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    return m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['self'], '');
                }
            }
            return m('message')->sendCustomNotice($openid, '扫描自己的海报是不会增加人气值的,快快把你的海报发送给你的小伙伴吧~');
        }

        //用户是否是新用户
        if($member_info['isnew'] || $_SESSION['postercontent']){
            //判断任务完成情况并更新任务进度
            if(!empty($join_info)){
                if($join_info['task_type']==1){
                    //奖励  条件：任务未过期，已完成，未完成只奖励扫描用户，任务完成奖励双方
                    $this->model->reward($member_info,$poster,$join_info,$qr,$openid,$qrmember);

                }elseif($join_info['task_type']==2){
                    //奖励  条件：任务未过期，已完成，未完成只奖励扫描用户，任务完成奖励双方
                    $this->model->rankreward($member_info,$poster,$join_info,$qr,$openid,$qrmember);
                }
                //分销
                $this->commission($poster, $member_info, $qrmember);
                if (!empty($_SESSION['postercontent'])){
                    $content = trim($_SESSION['postercontent']);
                    $timeout = 10;
                    $url = mobileUrl('task/build',array('timestamp'=>TIMESTAMP),true);
                    ihttp_request($url, array('openid' => $_W['openid'], 'content' => urlencode($content)), array(), $timeout);
                    unset($_SESSION['postercontent']);
                    exit();
                }
            }
        }else{
            $params =array(
                ':uniacid'=>$_W['uniacid'],
                ':task_user'=>$qr['openid'],
                ':joiner_id'=>$openid,
                ':join_id'=>$join_info['join_id']
            );
            //判断是否之前加入过此数据
            $scan_count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_task_joiner') . " where uniacid=:uniacid and task_user=:task_user and joiner_id=:joiner_id and join_id=:join_id ", $params);
            if(!empty($join_info)&&$scan_count&&empty($_SESSION['postercontent'])){
//                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
//                if(!empty($default_text)) {
//                    $default_text = unserialize($default_text);
//                    if(!empty($default_text['fail'])&&!empty($default_text['templateid'])){
//                        $poster['okdays']= $join_info['failtime'];
//                        $poster['completecount']=$join_info['completecount'];
//                        foreach ($default_text['fail'] as $key=>$val){
//                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
//                        }
//                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
//                    }else{
//                        m('message')->sendCustomNotice($openid, '您之前已经参加过此任务');
//                    }
//                }else{
//                    m('message')->sendCustomNotice($openid, '您之前已经参加过此任务');
//                }
            }elseif (empty($join_info)){
                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
                if(!empty($default_text)&&$_SESSION['postercontent']) {
                    $default_text = unserialize($default_text);
                    if(!empty($default_text['fail'])&&!empty($default_text['templateid'])){
                        $poster['okdays']= $join_info['failtime'];
                        $poster['completecount']=$join_info['completecount'];
                        foreach ($default_text['fail'] as $key=>$val){
                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                        }
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
                    }else{
                        m('message')->sendCustomNotice($openid, '此任务已过期或不存在');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '此任务已过期或不存在');
                }
            }else{
                $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
                if(!empty($default_text)&&$_SESSION['postercontent']) {
                    $default_text = unserialize($default_text);
                    if(!empty($default_text['fail'])&&!empty($default_text['templateid'])){
                        $poster['okdays']= $join_info['failtime'];
                        $poster['completecount']=$join_info['completecount'];
                        foreach ($default_text['fail'] as $key=>$val){
                            $default_text['fail'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                        }
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['fail'], '');
                    }else{
                        m('message')->sendCustomNotice($openid, '此任务只对新用户开放');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '此任务只对新用户开放');
                }
            }
        }
        //推送通知
        $url = trim($poster['respurl']);
        if (empty($url)) {
            if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {
                $url = mobileUrl('commission/myshop',array('mid'=>$qrmember['id']));
            } else {
                $url = mobileUrl('',array('mid'=>$qrmember['id']));
            }
        }

        if ($poster['resptype'] == '0')
        {
            if(!empty($poster['resptitle'])){
                $news = array(array('title' => $poster['resptitle'], 'description' => $poster['respdesc'], 'picurl' => tomedia($poster['respthumb']), 'url' => $url));
                return $obj->respNews($news);
            }
        }
        if ($poster['resptype'] == '1')
        {
            if(!empty($poster['resptext'])){
                return $obj->respText($poster['resptext']);
            }
        }
        return $this->responseEmpty();

    }
    private function commission($poster, $member, $qrmember) {

        $time = time();

        $p = p('commission');
        if ($p) {
            $cset = $p->getSet();
            if (!empty($cset)) {
                if ($member['isagent'] != 1) {//如果扫码会员不是分销商或准分销商，且没有上线
                    if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {//如果推荐人分销商
                        if (!empty($poster['bedown'])) {//如果扫码成为下线
                            if (empty($member['agentid'])) {
                                if(empty($member['fixagentid'])){
                                    //上级是分销商,扫码成为下线，没有上线
                                    pdo_update('ewei_shop_member', array('agentid' => $qrmember['id'], 'childtime' => $time), array('id' => $member['id']));
                                    $member['agentid'] = $qrmember['id'];

                                    //发送增加通知
                                    $p->sendMessage($qrmember['openid'], array('nickname' => $member['nickname'], 'childtime' => $time), TM_COMMISSION_AGENT_NEW);

                                    //检测升级
                                    $p->upgradeLevelByAgent($qrmember['id']);

                                    //股东升级
                                    if(p('globonus')){
                                        p('globonus')->upgradeLevelByAgent($qrmember['id']);
                                    }
                                    //区域代理升级
                                    if (p('abonus')) {
                                        p('abonus')->upgradeLevelByAgent($qrmember['id']);
                                    }
                                    //创始人升级
                                    if(p('author')){
                                        p('author')->upgradeLevelByAgent($qrmember['id']);
                                    }
                                }
                            }
                        }
                        //判断是否直接成为分销商
                        if (!empty($poster['beagent'])) {
                            $become_check = intval($cset['become_check']);
                            pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => $become_check, 'agenttime' => $time), array('id' => $member['id']));
                            if ($become_check == 1) {

                                $p->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
                                //检测升级
                                $p->upgradeLevelByAgent($qrmember['id']);

                                //股东升级
                                if(p('globonus')){
                                    p('globonus')->upgradeLevelByAgent($qrmember['id']);
                                }
                                //区域代理升级
                                if (p('abonus')) {
                                    p('abonus')->upgradeLevelByAgent($qrmember['id']);
                                }
                                //创始人升级
                                if(p('author')){
                                    p('author')->upgradeLevelByAgent($qrmember['id']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    //普通海报奖励
    private function reward($member_info,$poster,$join_info,$qr,$openid,$qrmember){
        if(empty($member_info)||empty($poster)||empty($join_info)||empty($openid)||empty($qr)){
            return false;
        }
        global $_W;
        //载入日志函数
        load()->func('logging');


        $reward_data = unserialize($poster['reward_data']);
        $count = $join_info['completecount']+1;
        if($join_info['needcount']==$count && $join_info['is_reward']==0 ){

            //更新任务推广人数
            $reward = serialize($reward_data['rec']);
            $sub_reward = serialize($reward_data['sub']);
            //奖励双方（推荐人和扫描人）并发送奖励通知
            $reward_log = array(
                'uniacid'=>$_W['uniacid'],
                'openid'=>$qr['openid'],
                'from_openid'=>$openid,
                'join_id'=>$join_info['join_id'],
                'taskid'=>$qr['posterid'],
                'task_type'=>1,
                'subdata'=>$sub_reward,
                'recdata'=>$reward,
                'createtime'=>time()

            );
            //更新任务进度
            pdo_update('ewei_shop_task_join', array('completecount'=>$count,'is_reward'=>1,'reward_data'=> $reward), array('uniacid' => $_W['uniacid'],'join_id'=>$join_info['join_id'],'join_user'=>$qr['openid'],'task_id'=>$poster['id'],'task_type'=>1));
            //插入日志
            pdo_insert('ewei_shop_task_log', $reward_log);
            $log_id = pdo_insertid();
            //插入扫描者
            $scaner = array(
                'uniacid'=>$_W['uniacid'],
                'task_user'=>$qr['openid'],
                'joiner_id'=>$openid,
                'task_id'=>$qr['posterid'],
                'join_id'=>$join_info['join_id'],
                'task_type'=>1,
                'join_status'=>1,
                'addtime'=>time()
            );
            pdo_insert('ewei_shop_task_joiner', $scaner);
            foreach($reward_data as $key=>$val){
                if($key=='rec'){
                    //积分
                    if(isset($val['credit'])&&$val['credit']>0){
                        m('member')->setCredit($qr['openid'], 'credit1', $val['credit'], array(0, '推荐扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if(isset($val['money'])&&$val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        m('finance')->pay($qr['openid'], $val['money']['type'], $pay, '', '任务活动推荐奖励',false);
                    }
                    //红包
                    if(isset($val['bribery'])&&$val['bribery']>0){
                        //红包参数
                        $tid = rand(1,1000).time().rand(1,10000);//订单编号
                        $params = array(
                            'openid'=>$qr['openid'],
                            'tid'=>$tid,
                            'send_name'=>'推荐奖励',
                            'money'=>$val['bribery'],
                            'wishing'=>'推荐奖励',
                            'act_name'=>$poster['title'],
                            'remark'=>'推荐奖励',
                        );

                        $err = m('common')->sendredpack($params);
                        if(!is_error($err)){
                            $reward = unserialize($reward);
                            $reward['briberyOrder'] = $tid;
                            $reward = serialize($reward);
                            $upgrade = array(
                                'recdata'=>$reward
                            );
                            pdo_update('ewei_shop_task_log',$upgrade,array('id'=>$log_id));
                        }
                    }
                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //优惠券通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($qrmember, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }
                }elseif($key=='sub'){
                    //积分
                    if($val['credit']>0){
                        m('member')->setCredit($openid, 'credit1', $val['credit'], array(0, '扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if($val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        $res = m('finance')->pay($openid, $val['money']['type'], $pay, '', '任务活动奖励',false);
                        if(is_error($res)){
                            logging_run($res['message']);
                        }
                    }

                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //推荐人奖励通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($member_info, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }
                }
            }
            //推送通知
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));

            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                //扫描人通知
                if(!empty($default_text['successscaner'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['successscaner'] as $key=>$val){
                        $default_text['successscaner'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    logging_run($default_text);
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['successscaner'],'');
                    }else{
                        m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                }
                //任务人通知
                if(!empty($default_text['complete'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$count;
                    foreach ($default_text['complete'] as $key=>$val){
                        $default_text['complete'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,2);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($qrmember['openid'], $default_text['templateid'], $default_text['complete'], mobileUrl('task',array('tabpage'=>'complete'),true));
                    }else{
                        m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
                    }
                }else{
                    m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
                }
            }else{
                m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                m('message')->sendCustomNotice($openid, '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
            }
        }else{
            //奖励扫描人，发送奖励通知给双方
            $reward = serialize($reward_data['rec']);
            $sub_reward = serialize($reward_data['sub']);
            $reward_log = array(
                'uniacid'=>$_W['uniacid'],
                'openid'=>$qr['openid'],
                'from_openid'=>$openid,
                'join_id'=>$join_info['join_id'],
                'taskid'=>$qr['posterid'],
                'task_type'=>1,
                'subdata'=>$sub_reward,
                'createtime'=>time()
            );
            //更新任务进度
            pdo_update('ewei_shop_task_join', array('completecount'=>$count), array('uniacid' => $_W['uniacid'],'join_user'=>$qr['openid'],'task_id'=>$poster['id'],'task_type'=>1));
            //插入日志
            pdo_insert('ewei_shop_task_log', $reward_log);
            $log_id = pdo_insertid();
            //插入扫描者
            $scaner = array(
                'uniacid'=>$_W['uniacid'],
                'task_user'=>$qr['openid'],
                'joiner_id'=>$openid,
                'task_id'=>$qr['posterid'],
                'join_id'=>$join_info['join_id'],
                'task_type'=>1,
                'join_status'=>1,
                'addtime'=>time()
            );
            pdo_insert('ewei_shop_task_joiner', $scaner);
            foreach($reward_data as $key=>$val){
                //至奖励扫描者
                if($key=='sub'){
                    //积分
                    if($val['credit']>0){
                        m('member')->setCredit($openid, 'credit1', $val['credit'], array(0, '扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if($val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        $res = m('finance')->pay($openid, $val['money']['type'], $pay, '', '任务活动奖励',false);
                        logging_run('submoney'.json_encode($res));
                        if(is_error($res)){
                            logging_run('submoney'.$res['message']);
                        }
                    }
                    //红包
                    if($val['bribery']>0){
                        //红包参数
                        $tid = rand(1,1000).time().rand(1,10000);//订单编号
                        $params = array(
                            'openid'=>$openid,
                            'tid'=>$tid,
                            'send_name'=>'推荐奖励',
                            'money'=>$val['bribery'],
                            'wishing'=>'推荐奖励',
                            'act_name'=>$poster['title'],
                            'remark'=>'推荐奖励',
                        );
                        $err = m('common')->sendredpack($params);
                        logging_run('bribery'.json_encode($err));
                        if(!is_error($err)){
                            $sub_reward = unserialize($sub_reward);
                            $sub_reward['briberyOrder'] = $tid;
                            $sub_reward = serialize($sub_reward);
                            $upgrade = array(
                                'subdata'=>$sub_reward
                            );
                            pdo_update('ewei_shop_task_log',$upgrade,array('id'=>$log_id));
                        }else{
                            logging_run('bribery'.$err['message']);
                        }
                    }
                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                $cansendreccoupon = false;
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //推荐人奖励通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($member_info, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }

                }
            }
            //推送通知
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                //扫描人通知
                if(!empty($default_text['successscaner'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['successscaner'] as $key=>$val){
                        $default_text['successscaner'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['successscaner'], '');
                    }else{
                        m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                }
                //任务人通知
                if(!empty($default_text['successtasker'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$count;
                    foreach ($default_text['successtasker'] as $key=>$val){
                        $default_text['successtasker'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,2);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($qrmember['openid'], $default_text['templateid'], $default_text['successtasker'], mobileUrl('task',array('tabpage'=>'runninga'),true));
                    }else{
                        m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
                    }

                }else{
                    m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
                }
            }else{
                m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
            }
        }
    }
    //多级海报奖励
    private function rankreward($member_info,$poster,$join_info,$qr,$openid,$qrmember){
        if(empty($member_info)||empty($poster)||empty($join_info)||empty($openid)||empty($qr)){
            return false;
        }
        global $_W;
        //载入日志函数
        load()->func('logging');


        $reward_data = unserialize($poster['reward_data']);
        $count = $join_info['completecount']+1;
        if($join_info['needcount']==$count && $join_info['is_reward']==0 ){

            //更新任务推广人数
            $reward = serialize($reward_data['rec']);
            $sub_reward = serialize($reward_data['sub']);
            //奖励双方（推荐人和扫描人）并发送奖励通知
            $reward_log = array(
                'uniacid'=>$_W['uniacid'],
                'openid'=>$qr['openid'],
                'from_openid'=>$openid,
                'join_id'=>$join_info['join_id'],
                'taskid'=>$qr['posterid'],
                'task_type'=>1,
                'subdata'=>$sub_reward,
                'recdata'=>$reward,
                'createtime'=>time()

            );
            //更新任务进度
            pdo_update('ewei_shop_task_join', array('completecount'=>$count,'is_reward'=>1,'reward_data'=> $reward), array('uniacid' => $_W['uniacid'],'join_id'=>$join_info['join_id'],'join_user'=>$qr['openid'],'task_id'=>$poster['id'],'task_type'=>1));
            //插入日志
            pdo_insert('ewei_shop_task_log', $reward_log);
            $log_id = pdo_insertid();
            //插入扫描者
            $scaner = array(
                'uniacid'=>$_W['uniacid'],
                'task_user'=>$qr['openid'],
                'joiner_id'=>$openid,
                'task_id'=>$qr['posterid'],
                'join_id'=>$join_info['join_id'],
                'task_type'=>1,
                'join_status'=>1,
                'addtime'=>time()
            );
            pdo_insert('ewei_shop_task_joiner', $scaner);
            foreach($reward_data as $key=>$val){
                if($key=='rec'){
                    //积分
                    if(isset($val['credit'])&&$val['credit']>0){
                        m('member')->setCredit($qr['openid'], 'credit1', $val['credit'], array(0, '推荐扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if(isset($val['money'])&&$val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        m('finance')->pay($qr['openid'], $val['money']['type'], $pay, '', '任务活动推荐奖励',false);
                    }
                    //红包
                    if(isset($val['bribery'])&&$val['bribery']>0){
                        //红包参数
                        $tid = rand(1,1000).time().rand(1,10000);//订单编号
                        $params = array(
                            'openid'=>$qr['openid'],
                            'tid'=>$tid,
                            'send_name'=>'推荐奖励',
                            'money'=>$val['bribery'],
                            'wishing'=>'推荐奖励',
                            'act_name'=>$poster['title'],
                            'remark'=>'推荐奖励',
                        );
                        $err = m('common')->sendredpack($params);
                        if(!is_error($err)){
                            $reward = unserialize($reward);
                            $reward['briberyOrder'] = $tid;
                            $reward = serialize($reward);
                            $upgrade = array(
                                'recdata'=>$reward
                            );
                            pdo_update('ewei_shop_task_log',$upgrade,array('id'=>$log_id));
                        }
                    }
                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //优惠券通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($qrmember, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }
                }elseif($key=='sub'){
                    //积分
                    if($val['credit']>0){
                        m('member')->setCredit($openid, 'credit1', $val['credit'], array(0, '扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if($val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        $res = m('finance')->pay($openid, $val['money']['type'], $pay, '', '任务活动奖励',false);
                        if(is_error($res)){
                            logging_run($res['message']);
                        }
                    }

                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //推荐人奖励通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($member_info, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }
                }
            }
            //推送通知
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));

            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                //扫描人通知
                if(!empty($default_text['successscaner'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['successscaner'] as $key=>$val){
                        $default_text['successscaner'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    logging_run($default_text);
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['successscaner'],'');
                    }else{
                        m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                }
                //任务人通知
                if(!empty($default_text['complete'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$count;
                    foreach ($default_text['complete'] as $key=>$val){
                        $default_text['complete'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,2);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($qrmember['openid'], $default_text['templateid'], $default_text['complete'], mobileUrl('task',array('tabpage'=>'complete'),true));
                    }else{
                        m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
                    }
                }else{
                    m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
                }
            }else{
                m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                m('message')->sendCustomNotice($openid, '亲爱的'.$qrmember['nickname'].'恭喜您完成任务获得奖励',mobileUrl('task',array('tabpage'=>'complete'),true));
            }
        }else{
            //奖励扫描人，发送奖励通知给双方
            $reward = serialize($reward_data['rec']);
            $sub_reward = serialize($reward_data['sub']);
            $reward_log = array(
                'uniacid'=>$_W['uniacid'],
                'openid'=>$qr['openid'],
                'from_openid'=>$openid,
                'join_id'=>$join_info['join_id'],
                'taskid'=>$qr['posterid'],
                'task_type'=>1,
                'subdata'=>$sub_reward,
                'createtime'=>time()
            );
            //更新任务进度
            pdo_update('ewei_shop_task_join', array('completecount'=>$count), array('uniacid' => $_W['uniacid'],'join_user'=>$qr['openid'],'task_id'=>$poster['id'],'task_type'=>1));
            //插入日志
            pdo_insert('ewei_shop_task_log', $reward_log);
            $log_id = pdo_insertid();
            //插入扫描者
            $scaner = array(
                'uniacid'=>$_W['uniacid'],
                'task_user'=>$qr['openid'],
                'joiner_id'=>$openid,
                'task_id'=>$qr['posterid'],
                'join_id'=>$join_info['join_id'],
                'task_type'=>1,
                'join_status'=>1,
                'addtime'=>time()
            );
            pdo_insert('ewei_shop_task_joiner', $scaner);
            foreach($reward_data as $key=>$val){
                //至奖励扫描者
                if($key=='sub'){
                    //积分
                    if($val['credit']>0){
                        m('member')->setCredit($openid, 'credit1', $val['credit'], array(0, '扫码关注积分+' . $val['credit']));
                    }
                    //现金
                    if($val['money']['num']>0){
                        // $val['money']['type'] 0:余额1：微信
                        $pay = $val['money']['num'];
                        if ($val['money']['type'] == 1) {
                            $pay *= 100;
                        }
                        $res = m('finance')->pay($openid, $val['money']['type'], $pay, '', '任务活动奖励',false);
                        logging_run('submoney'.json_encode($res));
                        if(is_error($res)){
                            logging_run('submoney'.$res['message']);
                        }
                    }
                    //红包
                    if($val['bribery']>0){
                        //红包参数
                        $tid = rand(1,1000).time().rand(1,10000);//订单编号
                        $params = array(
                            'openid'=>$openid,
                            'tid'=>$tid,
                            'send_name'=>'推荐奖励',
                            'money'=>$val['bribery'],
                            'wishing'=>'推荐奖励',
                            'act_name'=>$poster['title'],
                            'remark'=>'推荐奖励',
                        );

                        $err = m('common')->sendredpack($params);
                        logging_run('bribery'.json_encode($err));
                        if(!is_error($err)){
                            $sub_reward = unserialize($sub_reward);
                            $sub_reward['briberyOrder'] = $tid;
                            $sub_reward = serialize($sub_reward);
                            $upgrade = array(
                                'subdata'=>$sub_reward
                            );
                            pdo_update('ewei_shop_task_log',$upgrade,array('id'=>$log_id));
                        }else{
                            logging_run('bribery'.$err['message']);
                        }
                    }
                    //优惠券
                    if(isset($val['coupon'])&&!empty($val['coupon'])){
                        //赠送优惠券
                        $cansendreccoupon =false;
                        $plugin_coupon = com('coupon');
                        unset($val['coupon']['total']);
                        foreach ($val['coupon'] as $k=>$v){
                            if($plugin_coupon){
                                //推荐者奖励
                                $cansendreccoupon = false;
                                if(!empty($v['id']) && $v['couponnum']>0){
                                    $reccoupon = $plugin_coupon->getCoupon($v['id']);
                                    if(!empty($reccoupon)){
                                        $cansendreccoupon = true;
                                    }
                                }
                            }

                            //推荐人奖励通知
                            if($cansendreccoupon){
                                //发送优惠券
                                $plugin_coupon->taskposter($member_info, $v['id'],$v['couponnum']);
                            }
                        }
                    }
                    //指定价格商品
//                    if(isset($val['goods'])&&!empty($val['goods'])){
//                    }

                }
            }
            //推送通知
            $default_text =  pdo_fetchcolumn("SELECT `data` FROM " . tablename('ewei_shop_task_default') . " WHERE uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid']));
            if(!empty($default_text)) {
                $default_text = unserialize($default_text);
                //扫描人通知
                if(!empty($default_text['successscaner'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$join_info['completecount'];
                    foreach ($default_text['successscaner'] as $key=>$val){
                        $default_text['successscaner'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,1);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($openid, $default_text['templateid'], $default_text['successscaner'], '');
                    }else{
                        m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                    }
                }else{
                    m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                }
                //任务人通知
                if(!empty($default_text['successtasker'])){
                    $poster['okdays']= $join_info['failtime'];
                    $poster['completecount']=$count;
                    foreach ($default_text['successtasker'] as $key=>$val){
                        $default_text['successtasker'][$key]['value']=$this->model->notice_complain($val['value'],$qrmember,$poster,$member_info,2);
                    }
                    if($default_text['templateid']){
                        m('message')->sendTplNotice($qrmember['openid'], $default_text['templateid'], $default_text['successtasker'], mobileUrl('task',array('tabpage'=>'runninga'),true));
                    }else{
                        m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
                    }

                }else{
                    m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
                }
            }else{
                m('message')->sendCustomNotice($openid, '感谢您的关注，恭喜您获得关注奖励');
                m('message')->sendCustomNotice($qrmember['openid'], '亲爱的'.$qrmember['nickname'].'您的海报被'.$member_info['nickname'].'关注,增加了1点人气值',mobileUrl('task',array('tabpage'=>'runninga'),true));
            }
        }
    }

}
