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

class Bind_EweiShopV2Model {

    /**
     * @param array $member
     * @return bool
     */
    public function iswxm($member=array()) {
        if(empty($member) || !is_array($member)){
            return true;
        }
        if(strexists($member['openid'], 'sns_wx_') || strexists($member['openid'], 'sns_qq_') || strexists($member['openid'], 'sns_wa_') || strexists($member['openid'], 'wap_user_')) {
            return false;
        }
        return true;
    }

    /**
     * @param int $mid
     * @param array $arr
     */
    public function update($mid=0, $arr=array()){
        global $_W;
        if(empty($mid) || empty($arr) || !is_array($arr)){
            return;
        }
        pdo_update('ewei_shop_member', $arr, array('id'=>$mid,'uniacid'=>$_W['uniacid']));
    }

    /**
     * @param array $a
     * @param array $b
     * @return array
     */
    public function merge($a=array(), $b=array()){
        global $_W;

        if(empty($a) || empty($b) || $a['id']==$b['id']){
            return error(0, "params error");
        }

        // 2. 会员基本信息  level groupid  createtime childtime isblack
        $createtime = $a['createtime'] > $b['createtime'] ? $b['createtime'] : $a['createtime'];
        $childtime = $a['childtime'] > $b['childtime'] ? $b['childtime'] : $a['childtime'];
        $comparelevel = m('member')->compareLevel(array($a['level'], $b['level']));
        $level = $comparelevel ? $b['level'] : $a['level'];
        //$groupid = '';
        $isblack = !empty($a['isblack']) || !empty($b['isblack']) ? 1 : 0;

        // qq openid
        $openid_qq = !empty($b['openid_qq']) && empty($a['openid_qq']) ? $b['openid_qq'] : $a['openid_qq'];
        $openid_wx = !empty($b['openid_wx']) && empty($a['openid_wx']) ? $b['openid_wx'] : $a['openid_wx'];
        $openid_wa = !empty($b['openid_wa']) && empty($a['openid_wa']) ? $b['openid_wa'] : $a['openid_wa'];

        // 2.2. 上级关系
        // 如果A是分销商
        if(!empty($a['isagent']) && empty($b['isagent'])){
            $isagent = 1;
            $agentid = $a['agentid'];
            $status = !empty($a['status']) ? 1 : 0;
            $agenttime = $a['agenttime'];
            $agentlevel = $a['agentlevel'];
            $agentblack = $a['agentblack'];
            $fixagentid = $a['fixagentid'];
        }
        // 如果B是分销商
        elseif(!empty($b['isagent']) && empty($a['isagent'])){
            $isagent = 1;
            $agentid = $b['agentid'];
            $status = !empty($b['status']) ? 1 : 0;
            $agenttime = $b['agenttime'];
            $agentlevel = $b['agentlevel'];
            $agentblack = $b['agentblack'];
            $fixagentid = $b['fixagentid'];
        }
        // 如果A、B都是分销商
        elseif(!empty($b['isagent']) && !empty($a['isagent'])){
            // 判断分销商等级 取高级用户
            $compare = p('commission')->compareLevel(array($a['agentlevel'], $b['agentlevel']));
            $isagent = 1;
            if($compare){
                $agentid = $b['agentid'];
                if(empty($b['agentid']) && !empty($a['agentid'])){
                    $agentid = $a['agentid'];
                }
                $status = !empty($b['status']) ? 1 : 0;
                $agentblack = !empty($b['agentblack']) ? 1 : 0;
                $fixagentid = !empty($b['fixagentid']) ? 1 : 0;
            }else{
                $agentid = $a['agentid'];
                if(empty($a['agentid']) && !empty($b['agentid'])){
                    $agentid = $b['agentid'];
                }

                $status = !empty($a['status']) ? 1 : 0;
                $agentblack = !empty($a['agentblack']) ? 1 : 0;
                $fixagentid = !empty($a['fixagentid']) ? 1 : 0;
            }
            $agenttime = $compare ? $b['agenttime'] : $a['agenttime'];
            $agentlevel = $compare ? $b['agentlevel'] : $a['agentlevel'];
        }
        // 如果A、B都不是分销商
        elseif (empty($b['isagent']) && empty($a['isagent'])){

            //如果A、B的上级id都不为空的情况
            if(!empty($a['agentid']) && !empty($b['agentid'])){
                $agentid = $a['agentid'];
            }
            //如果A的上级id为空B的不为空的情况
            elseif(empty($a['agentid']) && !empty($b['agentid'])){
                $agentid = $b['agentid'];
            }
            //如果A的上级id不为空B的为空的情况
            elseif (!empty($a['agentid']) && empty($b['agentid'])){
                $agentid = $a['agentid'];
            }

            //如果A、B的上级id都不为空的情况
            if(!empty($a['inviter']) && !empty($b['inviter'])){
                $inviter = $a['inviter'];
            }
            //如果A的上级id为空B的不为空的情况
            elseif(empty($a['inviter']) && !empty($b['inviter'])){
                $inviter = $b['inviter'];
            }
            //如果A的上级id不为空B的为空的情况
            elseif (!empty($a['inviter']) && empty($b['inviter'])){
                $inviter = $a['inviter'];
            }
        }

        // 3. 合伙人
        if(!empty($a['isauthor']) && empty($b['isauthor'])){
            // 如果A是合伙人
            $isauthor = $a['isauthor'];
            $authorstatus = !empty($a['authorstatus']) ? 1 : 0;
            $authortime = $a['authortime'];
            $authorlevel = $a['authorlevel'];
            $authorblack = $a['authorblack']; // 合伙人黑名单
        }
        elseif(!empty($b['isauthor']) && empty($a['isauthor'])){
            // 如果B是合伙人
            $isauthor = $b['isauthor'];
            $authorstatus = !empty($b['authorstatus']) ? 1 : 0;
            $authortime = $b['authortime'];
            $authorlevel = $b['authorlevel'];
            $authorblack = $b['authorblack']; // 合伙人黑名单
        }
        elseif(!empty($b['isauthor']) && !empty($a['isauthor'])){
            // 如果A、B都是合伙人
            return error(0, "此手机号已绑定另一用户(a1)<br>请联系管理员");
        }

        // 4. 股东
        if(!empty($a['ispartner']) && empty($b['ispartner'])){
            // 如果A是股东
            $ispartner = 1;
            $partnerstatus = !empty($a['partnerstatus']) ? 1 : 0;
            $partnertime = $a['partnertime'];
            $partnerlevel = $a['partnerlevel'];
            $partnerblack = $a['partnerblack'];
        }
        elseif(!empty($b['ispartner']) && empty($a['ispartner'])){
            // 如果B是股东
            $ispartner = 1;
            $partnerstatus = !empty($b['partnerstatus']) ? 1 : 0;
            $partnertime = $b['partnertime'];
            $partnerlevel = $b['partnerlevel'];
            $partnerblack = $b['partnerblack'];
        }
        elseif(!empty($b['ispartner']) && !empty($a['ispartner'])){
            // 如果A、B都是股东
            return error(0, "此手机号已绑定另一用户(p)<br>请联系管理员");
        }

        // 4. 区域代理
        if(!empty($a['isaagent']) && empty($b['isaagent'])){
            // 如果A是区域代理
            $isaagent = $a['isaagent'];
            $aagentstatus = !empty($a['aagentstatus']) ? 1 : 0;
            $aagenttime = $a['aagenttime'];
            $aagentlevel = $a['aagentlevel'];
            $aagenttype = $a['aagenttype'];
            $aagentprovinces = $a['aagentprovinces'];
            $aagentcitys = $a['aagentcitys'];
            $aagentareas = $a['aagentareas'];
        }
        elseif(!empty($b['isaagent']) && empty($a['isaagent'])){
            // 如果B是区域代理
            $isaagent = $b['isaagent'];
            $aagentstatus = !empty($b['aagentstatus']) ? 1 : 0;
            $aagenttime = $b['aagenttime'];
            $aagentlevel = $b['aagentlevel'];
            $aagenttype = $b['aagenttype'];
            $aagentprovinces = $b['aagentprovinces'];
            $aagentcitys = $b['aagentcitys'];
            $aagentareas = $b['aagentareas'];
        }
        elseif(!empty($b['isaagent']) && !empty($a['isaagent'])){
            // 如果A、B都是区域代理
            return error(0, "此手机号已绑定另一用户(a2)<br>请联系管理员");
        }

        // 处理更新数据
        $arr = array();
        // 基本信息
        if(isset($createtime)){
            $arr['createtime'] = $createtime;
        }
        if(isset($childtime)){
            $arr['childtime'] = $childtime;
        }
        if(isset($level)){
            $arr['level'] = $level;
        }
        if(isset($groupid)){
            $arr['groupid'] = $groupid;
        }
        if(isset($isblack)){
            $arr['isblack'] = $isblack;
        }
        if(isset($openid_qq)){
            $arr['openid_qq'] = $openid_qq;
        }
        if(isset($openid_wx)){
            $arr['openid_wx'] = $openid_wx;
        }
        if(isset($openid_wa)){
            $arr['openid_wa'] = $openid_wa;
        }
        // 分销
        if(isset($status)){
            $arr['status'] = $status;
        }
        if(isset($isagent)){
            $arr['isagent'] = $isagent;
        }
        if(isset($agentid)){
            $arr['agentid'] = $agentid;
        }
        if(isset($agenttime)){
            $arr['agenttime'] = $agenttime;
        }
        if(isset($agentlevel)){
            $arr['agentlevel'] = $agentlevel;
        }
        if(isset($agentblack)){
            $arr['agentblack'] = $agentblack;
        }
        if(isset($fixagentid)){
            $arr['fixagentid'] = $fixagentid;
        }
        // 合伙人
        if(isset($isauthor)){
            $arr['isauthor'] = $isauthor;
        }
        if(isset($authorstatus)){
            $arr['authorstatus'] = $authorstatus;
        }
        if(isset($authortime)){
            $arr['authortime'] = $authortime;
        }
        if(isset($authorlevel)){
            $arr['authorlevel'] = $authorlevel;
        }
        if(isset($authorblack)){
            $arr['authorblack'] = $authorblack;
        }
        // 股东
        if(isset($ispartner)){
            $arr['ispartner'] = $ispartner;
        }
        if(isset($partnerstatus)){
            $arr['partnerstatus'] = $partnerstatus;
        }
        if(isset($partnertime)){
            $arr['partnertime'] = $partnertime;
        }
        if(isset($partnerlevel)){
            $arr['partnerlevel'] = $partnerlevel;
        }
        if(isset($partnerblack)){
            $arr['partnerblack'] = $partnerblack;
        }
        // 区域代理
        if(isset($isaagent)){
            $arr['isaagent'] = $isaagent;
        }
        if(isset($aagentstatus)){
            $arr['aagentstatus'] = $aagentstatus;
        }
        if(isset($aagenttime)){
            $arr['aagenttime'] = $aagenttime;
        }
        if(isset($aagentlevel)){
            $arr['aagentlevel'] = $aagentlevel;
        }
        if(isset($aagenttype)){
            $arr['aagenttype'] = $aagenttype;
        }
        if(isset($aagentprovinces)){
            $arr['aagentprovinces'] = $aagentprovinces;
        }
        if(isset($aagentcitys)){
            $arr['aagentcitys'] = $aagentcitys;
        }
        if(isset($aagentareas)){
            $arr['aagentareas'] = $aagentareas;
        }
        if(isset($inviter)){
            $arr['inviter'] = $inviter;
        }

        if(!empty($arr) && is_array($arr)){
            pdo_update('ewei_shop_member', $arr, array('id'=>$b['id']));
        }

        // 2. 分销信息
        pdo_update('ewei_shop_commission_apply', array('mid' => $b['id']), array('uniacid' => $_W['uniacid'], 'mid' => $a['id']));
        //订单上级
        pdo_update('ewei_shop_order',array('agentid'=>$b['id']),array('agentid'=>$a['id']));
        //会员上级
        pdo_update('ewei_shop_member',array('agentid'=>$b['id']),array('agentid'=>$a['id']));


        $mergeinfo = ' 合并前用户: '.$a['nickname'].'('.$a['id'].') 合并后用户: '.$b['nickname'].'('.$b['id'].')';
        // 1. 合并用户余额积分合并
        if($a['credit1']>0){
            m('member')->setCredit($b['openid'], 'credit1', abs($a['credit1']), '全网通会员数据合并增加积分 +' . $a['credit1']. $mergeinfo);
        }
        if($a['credit2']>0) {
            m('member')->setCredit($b['openid'], 'credit2', abs($a['credit2']), '全网通会员数据合并增加余额 +' . $a['credit2']. $mergeinfo);
        }

        // 删除用户A
        pdo_delete('ewei_shop_member', array('id' => $a['id'], 'uniacid' => $_W['uniacid']));
        if(method_exists(m('member'),'memberRadisCountDelete')) {
            m('member')->memberRadisCountDelete();  //清除会员统计radis缓存
        }
        // 0. 替换所有数据表里的openid
        $tables = pdo_fetchall("SHOW TABLES like '%_ewei_shop_%'");
        foreach ($tables as $k => $v) {
            $v = array_values($v);
            $tablename = str_replace($_W['config']['db']['tablepre'], '', $v[0]);
            // 更新表中 含有 openid、uniacid的表
            if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'uniacid')) {
                pdo_update($tablename, array('openid' => $b['openid']), array('uniacid' => $_W['uniacid'], 'openid' => $a['openid']));
            }
            // 更新表中 含有 openid、acid的表
            if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'acid')) {
                pdo_update($tablename, array('openid' => $b['openid']), array('acid' => $_W['acid'], 'openid' => $a['openid']));
            }
            // 更新表中 含有 mid、uniacid的表
            if (pdo_fieldexists($tablename, 'mid') && pdo_fieldexists($tablename, 'uniacid')) {
                pdo_update($tablename, array('mid' => $b['id']), array('uniacid' => $_W['uniacid'], 'mid' => $a['id']));
            }
        }

        $c = m('member')->getMember($b['openid']);
        // 插入合并日志
        pdo_insert("ewei_shop_member_mergelog", array(
            'uniacid'=>$_W['uniacid'],
            'mergetime'=>time(),
            'openid_a'=>$a['openid'],
            'openid_b'=>$b['openid'],
            'mid_a'=>$a['id'],
            'mid_b'=>$b['id'],
            'detail_a'=>iserializer($a),
            'detail_b'=>iserializer($b),
            'detail_c'=>iserializer($c)
        ));

        return error(1);
    }

    /**
     * 绑定送积分
     * @param array $member
     */
    public function sendCredit($member = array()) {
        if(empty($member)){
            return;
        }
        $data = m('common')->getPluginset('sale');
        if(!empty($data['bindmobile']) && intval($data['bindmobilecredit'])>0){
            m('member')->setCredit($member['openid'], 'credit1', abs($data['bindmobilecredit']), '绑定手机号送积分 +'. $data['bindmobilecredit']);
        }
    }


    /**
     * 数据迁移
     * @param array $a
     * @param array $b
     * @return array
     */
    public function mergeforuniacid($a=array(), $b=array()){
        global $_W;

        if(empty($a) || empty($b) || $a['id']==$b['id']){
            return error(0, "params error");
        }

        if(!empty($b['mobileverify']))
        {
            return error(0, "params error");
        }

        // 2. 会员基本信息  level groupid  createtime childtime isblack
        $createtime = $a['createtime'] > $b['createtime'] ? $b['createtime'] : $a['createtime'];
        $childtime = $a['childtime'] > $b['childtime'] ? $b['childtime'] : $a['childtime'];
        $comparelevel = m('member')->compareLevel(array($a['level'], $b['level']));
        $level = $comparelevel ? $b['level'] : $a['level'];
        //$groupid = '';
        $isblack = !empty($a['isblack']) || !empty($b['isblack']) ? 1 : 0;

        // qq openid
        $openid_qq = !empty($b['openid_qq']) && empty($a['openid_qq']) ? $b['openid_qq'] : $a['openid_qq'];
        $openid_wx = !empty($b['openid_wx']) && empty($a['openid_wx']) ? $b['openid_wx'] : $a['openid_wx'];
        $openid_wa = !empty($b['openid_wa']) && empty($a['openid_wa']) ? $b['openid_wa'] : $a['openid_wa'];

        // 2.2. 上级关系
        // 如果A是分销商
        if(!empty($a['isagent']) && empty($b['isagent'])){
            $isagent = 1;
            $agentid = $a['agentid'];
            $status = !empty($a['status']) ? 1 : 0;
            $agenttime = $a['agenttime'];
            $agentlevel = $a['agentlevel'];
            $agentblack = $a['agentblack'];
            $fixagentid = $a['fixagentid'];
        }
        // 如果B是分销商
        elseif(!empty($b['isagent']) && empty($a['isagent'])){
            $isagent = 1;
            $agentid = $b['agentid'];
            $status = !empty($b['status']) ? 1 : 0;
            $agenttime = $b['agenttime'];
            $agentlevel = $b['agentlevel'];
            $agentblack = $b['agentblack'];
            $fixagentid = $b['fixagentid'];
        }
        // 如果A、B都是分销商
        elseif(!empty($b['isagent']) && !empty($a['isagent'])){
            // 判断分销商等级 取高级用户
            $compare = p('commission')->compareLevel(array($a['agentlevel'], $b['agentlevel']));
            $isagent = 1;
            if($compare){
                $agentid = $b['agentid'];
                if(empty($b['agentid']) && !empty($a['agentid'])){
                    $agentid = $a['agentid'];
                }
                $status = !empty($b['status']) ? 1 : 0;
                $agentblack = !empty($b['agentblack']) ? 1 : 0;
                $fixagentid = !empty($b['fixagentid']) ? 1 : 0;
            }else{
                $agentid = $a['agentid'];
                if($a['agentid'] && !empty($b['agentid'])){
                    $agentid = $b['agentid'];
                }
                $status = !empty($a['status']) ? 1 : 0;
                $agentblack = !empty($a['agentblack']) ? 1 : 0;
                $fixagentid = !empty($a['fixagentid']) ? 1 : 0;
            }
            $agenttime = $compare ? $b['agenttime'] : $a['agenttime'];
            $agentlevel = $compare ? $b['agentlevel'] : $a['agentlevel'];
        }

        /*// 3. 合伙人
        if(!empty($a['isauthor']) && empty($b['isauthor'])){
            // 如果A是合伙人
            $isauthor = $a['isauthor'];
            $authorstatus = !empty($a['authorstatus']) ? 1 : 0;
            $authortime = $a['authortime'];
            $authorlevel = $a['authorlevel'];
            $authorblack = $a['authorblack']; // 合伙人黑名单
        }
        elseif(!empty($b['isauthor']) && empty($a['isauthor'])){
            // 如果B是合伙人
            $isauthor = $b['isauthor'];
            $authorstatus = !empty($b['authorstatus']) ? 1 : 0;
            $authortime = $b['authortime'];
            $authorlevel = $b['authorlevel'];
            $authorblack = $b['authorblack']; // 合伙人黑名单
        }
        elseif(!empty($b['isauthor']) && !empty($a['isauthor'])){
            // 如果A、B都是合伙人
            return error(0, "此手机号已绑定另一用户(a1)<br>请联系管理员");
        }

        // 4. 股东
        if(!empty($a['ispartner']) && empty($b['ispartner'])){
            // 如果A是股东
            $ispartner = 1;
            $partnerstatus = !empty($a['partnerstatus']) ? 1 : 0;
            $partnertime = $a['partnertime'];
            $partnerlevel = $a['partnerlevel'];
            $partnerblack = $a['partnerblack'];
        }
        elseif(!empty($b['ispartner']) && empty($a['ispartner'])){
            // 如果B是股东
            $ispartner = 1;
            $partnerstatus = !empty($b['partnerstatus']) ? 1 : 0;
            $partnertime = $b['partnertime'];
            $partnerlevel = $b['partnerlevel'];
            $partnerblack = $b['partnerblack'];
        }
        elseif(!empty($b['ispartner']) && !empty($a['ispartner'])){
            // 如果A、B都是股东
            return error(0, "此手机号已绑定另一用户(p)<br>请联系管理员");
        }

        // 4. 区域代理
        if(!empty($a['isaagent']) && empty($b['isaagent'])){
            // 如果A是区域代理
            $isaagent = $a['isaagent'];
            $aagentstatus = !empty($a['aagentstatus']) ? 1 : 0;
            $aagenttime = $a['aagenttime'];
            $aagentlevel = $a['aagentlevel'];
            $aagenttype = $a['aagenttype'];
            $aagentprovinces = $a['aagentprovinces'];
            $aagentcitys = $a['aagentcitys'];
            $aagentareas = $a['aagentareas'];
        }
        elseif(!empty($b['isaagent']) && empty($a['isaagent'])){
            // 如果B是区域代理
            $isaagent = $b['isaagent'];
            $aagentstatus = !empty($b['aagentstatus']) ? 1 : 0;
            $aagenttime = $b['aagenttime'];
            $aagentlevel = $b['aagentlevel'];
            $aagenttype = $b['aagenttype'];
            $aagentprovinces = $b['aagentprovinces'];
            $aagentcitys = $b['aagentcitys'];
            $aagentareas = $b['aagentareas'];
        }
        elseif(!empty($b['isaagent']) && !empty($a['isaagent'])){
            // 如果A、B都是区域代理
            return error(0, "此手机号已绑定另一用户(a2)<br>请联系管理员");
        }*/

        // 处理更新数据
        $arr = array();
        $arr['ishb'] = 1;
        // 基本信息
        if(isset($createtime)){
            $arr['createtime'] = $createtime;
        }
        if(isset($childtime)){
            $arr['childtime'] = $childtime;
        }
        if(isset($level)){
            $arr['level'] = $level;
        }
        if(isset($groupid)){
            $arr['groupid'] = $groupid;
        }
        if(isset($isblack)){
            $arr['isblack'] = $isblack;
        }
        if(isset($openid_qq)){
            $arr['openid_qq'] = $openid_qq;
        }
        if(isset($openid_wx)){
            $arr['openid_wx'] = $openid_wx;
        }
        if(isset($openid_wa)){
            $arr['openid_wa'] = $openid_wa;
        }
        // 分销
        if(isset($status)){
            $arr['status'] = $status;
        }
        if(isset($isagent)){
            $arr['isagent'] = $isagent;
        }
        if(isset($agentid)){
            $arr['agentid'] = $agentid;
        }
        if(isset($agenttime)){
            $arr['agenttime'] = $agenttime;
        }
        if(isset($agentlevel)){
            $arr['agentlevel'] = $agentlevel;
        }
        if(isset($agentblack)){
            $arr['agentblack'] = $agentblack;
        }
        if(isset($fixagentid)){
            $arr['fixagentid'] = $fixagentid;
        }
        /*// 合伙人
        if(isset($isauthor)){
            $arr['isauthor'] = $isauthor;
        }
        if(isset($authorstatus)){
            $arr['authorstatus'] = $authorstatus;
        }
        if(isset($authortime)){
            $arr['authortime'] = $authortime;
        }
        if(isset($authorlevel)){
            $arr['authorlevel'] = $authorlevel;
        }
        if(isset($authorblack)){
            $arr['authorblack'] = $authorblack;
        }
        // 股东
        if(isset($ispartner)){
            $arr['ispartner'] = $ispartner;
        }
        if(isset($partnerstatus)){
            $arr['partnerstatus'] = $partnerstatus;
        }
        if(isset($partnertime)){
            $arr['partnertime'] = $partnertime;
        }
        if(isset($partnerlevel)){
            $arr['partnerlevel'] = $partnerlevel;
        }
        if(isset($partnerblack)){
            $arr['partnerblack'] = $partnerblack;
        }
        // 区域代理
        if(isset($isaagent)){
            $arr['isaagent'] = $isaagent;
        }
        if(isset($aagentstatus)){
            $arr['aagentstatus'] = $aagentstatus;
        }
        if(isset($aagenttime)){
            $arr['aagenttime'] = $aagenttime;
        }
        if(isset($aagentlevel)){
            $arr['aagentlevel'] = $aagentlevel;
        }
        if(isset($aagenttype)){
            $arr['aagenttype'] = $aagenttype;
        }
        if(isset($aagentprovinces)){
            $arr['aagentprovinces'] = $aagentprovinces;
        }
        if(isset($aagentcitys)){
            $arr['aagentcitys'] = $aagentcitys;
        }
        if(isset($aagentareas)){
            $arr['aagentareas'] = $aagentareas;
        }*/

        if(!empty($arr) && is_array($arr)){
            pdo_update('ewei_shop_member', $arr, array('id'=>$b['id']));
        }

        // 2. 分销信息
        pdo_update('ewei_shop_commission_apply', array('mid' => $b['id']), array('mid' => $a['id']));
        //订单上级
        pdo_update('ewei_shop_order',array('agentid'=>$b['id']),array('agentid'=>$a['id']));
        //会员上级
        pdo_update('ewei_shop_member',array('agentid'=>$b['id']),array('agentid'=>$a['id']));


        $mergeinfo = ' 合并前用户: '.$a['nickname'].'('.$a['id'].') 合并后用户: '.$b['nickname'].'('.$b['id'].')';
        // 1. 合并用户余额积分合并
        if($a['credit1']>0){
            m('member')->setCredit($b['openid'], 'credit1', abs($a['credit1']), '数据迁移会员数据合并增加积分 +' . $a['credit1']. $mergeinfo);
        }
        if($a['credit2']>0) {
            m('member')->setCredit($b['openid'], 'credit2', abs($a['credit2']), '数据迁移会员数据合并增加余额 +' . $a['credit2']. $mergeinfo);
        }

        //清空A用户积分余额记录
        //pdo_update('mc_members',array('credit1'=>0,'credit2'=>0), array('uid' => $a['uid']));
        //pdo_update('ewei_shop_member',array('credit1'=>0,'credit2'=>0), array('openid' => $a['openid']));
        //pdo_insert('mc_credits_record', $log_data);

        // 删除用户A
        //pdo_delete('ewei_shop_member', array('id' => $a['id'], 'uniacid' => $_W['uniacid']));

        // 0. 替换所有数据表里的openid
        $tables = pdo_fetchall("SHOW TABLES like '%_ewei_shop_%'");
        foreach ($tables as $k => $v) {
            $v = array_values($v);
            $tablename = str_replace($_W['config']['db']['tablepre'], '', $v[0]);
            // 更新表中 含有 openid、uniacid的表
            if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'uniacid')) {
                if($tablename !='ewei_shop_member')
                {
                    pdo_update($tablename, array('openid' => $b['openid']), array('uniacid' => $_W['uniacid'], 'openid' => $a['openid']));
                }
            }
            // 更新表中 含有 openid、acid的表
            if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'acid')) {
                pdo_update($tablename, array('openid' => $b['openid']), array('acid' => $_W['acid'], 'openid' => $a['openid']));
            }
            // 更新表中 含有 mid、uniacid的表
            if (pdo_fieldexists($tablename, 'mid') && pdo_fieldexists($tablename, 'uniacid')) {
                pdo_update($tablename, array('mid' => $b['id']), array('uniacid' => $_W['uniacid'], 'mid' => $a['id']));
            }
        }

        $c = m('member')->getMember($b['openid']);
        // 插入合并日志
        pdo_insert("ewei_shop_member_mergelog", array(
            'uniacid'=>$_W['uniacid'],
            'fromuniacid'=>$_W['uniacid'],
            'mergetime'=>time(),
            'openid_a'=>$a['openid'],
            'openid_b'=>$b['openid'],
            'mid_a'=>$a['id'],
            'mid_b'=>$b['id'],
            'detail_a'=>iserializer($a),
            'detail_b'=>iserializer($b),
            'detail_c'=>iserializer($c)
        ));

        return error(1);
    }

}
