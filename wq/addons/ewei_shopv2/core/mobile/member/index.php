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

class Index_EweiShopV2Page extends MobileLoginPage {

	function main() {

		global $_W, $_GPC;

		//会员信息
		$member = m('member')->getMember($_W['openid'], true);

		//会员等级
		$level = m('member')->getLevel($_W['openid']);

		if(com('wxcard')) {
			$wxcardupdatetime = intval($member['wxcardupdatetime']);

			//更新用户会员卡信息
			if (($wxcardupdatetime + 86400) < time()) {
				com_run('wxcard::updateMemberCardByOpenid', $_W['openid']);

				pdo_update("ewei_shop_member", array('wxcardupdatetime' => time()), array('openid' => $_W['openid']));
			}
		}

        $this->diypage('member');

		//是否开启积分兑换
		$open_creditshop = p('creditshop') && $_W['shopset']['creditshop']['centeropen'];
		
		//统计
		$params = array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']);
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');

        if ($merch_plugin && $merch_data['is_openmerch'])
        {
            $statics = array(
                'order_0'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and status=0 and (isparent=1 or (isparent=0 and parentid=0)) and paytype<>3 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_1'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and (status=1 or (status=0 and paytype=3)) and isparent=0 and refundid=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_2'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and (status=2 or (status=1 and sendtype>0)) and isparent=0 and refundid=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_4'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and refundstate=1 and isparent=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'cart'=>pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0', $params),
                'favorite'=>pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0', $params)
            );
        }
        else
        {
            $statics = array(
                'order_0'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and status=0 and isparent=0 and paytype<>3 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_1'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and (status=1 or (status=0 and paytype=3)) and isparent=0 and refundid=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_2'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and (status=2 or (status=1 and sendtype>0)) and isparent=0 and refundid=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_4'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and refundstate=1 and isparent=0 and uniacid=:uniacid and istrade=0 and userdeleted=0',$params),
                'order_5'=>  pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and uniacid=:uniacid and iscycelbuy=1 and status in(0,1,2)',$params),
                'cart'=>pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 and selected = 1', $params),
                'favorite'=>$merch_plugin && $merch_data['is_openmerch'] ? pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0 and `type`=0', $params) : pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0', $params),
            );
        }

        $newstore_plugin = p('newstore');
        if ($newstore_plugin) {
            $statics['norder_0'] = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and status=0 and isparent=0 and istrade=1 and uniacid=:uniacid',$params);
            $statics['norder_1'] = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and status=1 and isparent=0 and istrade=1 and refundid=0 and uniacid=:uniacid',$params);
            $statics['norder_3'] = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and status=3 and isparent=0 and istrade=1 and uniacid=:uniacid',$params);
            $statics['norder_4'] = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where openid=:openid and ismr=0 and refundstate=1 and isparent=0 and istrade=1 and uniacid=:uniacid',$params);
        }

		//优惠券
		$hascoupon = false;
		$hascouponcenter = false;
		
		$plugin_coupon = com('coupon');
		if($plugin_coupon){

			$time = time();
			$sql = "select count(*) from ".tablename('ewei_shop_coupon_data')." d";
			$sql.=" left join ".tablename('ewei_shop_coupon')." c on d.couponid = c.id";
			$sql.=" where d.openid=:openid and d.uniacid=:uniacid and  d.used=0 "; //类型+最低消费+示使用
			$sql.=" and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time})) order by d.gettime desc"; //有效期

			$statics['coupon'] = pdo_fetchcolumn($sql,array(':openid'=>$_W['openid'],':uniacid'=>$_W['uniacid']));

//			$statics['newcoupon'] = pdo_fetchcolumn("SELECT 1  FROM ".tablename('ewei_shop_coupon_data')." where openid=:openid and uniacid=:uniacid and  isnew=1", array(':openid'=>$_W['openid'],':uniacid'=>$_W['uniacid']));

			$pcset = $_W['shopset']['coupon'];
			if(empty($pcset['closemember'])){
				$hascoupon = true;
			}
			if(empty($pcset['closecenter'])){
				$hascouponcenter = true;
			}

			if($hascoupon){
			    $couponnum = com('coupon')->getCanGetCouponNum($_W['merchid']);
            }
		}

		//股东
		$hasglobonus = false;
		$plugin_globonus = p('globonus');
		if($plugin_globonus){
			$plugin_globonus_set = $plugin_globonus->getSet();
			$hasglobonus = !empty($plugin_globonus_set['open']) && !empty($plugin_globonus_set['openmembercenter']);
		}
		//直播
        $haslive = false;
        $haslive = p('live');
		if($haslive){
            $live_set = $haslive->getSet();
            $haslive = $live_set['ismember'];
        }

		//3N倍增营销(定制)
        $hasThreen = false;
		$hasThreen = p('threen');
        if($hasThreen){
            $plugin_threen_set = $hasThreen->getSet();
            $hasThreen = !empty($plugin_threen_set['open']) && !empty($plugin_threen_set['threencenter']);
        }

		//联合创始人
		$hasauthor = false;
		$plugin_author = p('author');
		if($plugin_author){
			$plugin_author_set = $plugin_author->getSet();
			$hasauthor = !empty($plugin_author_set['open']) && !empty($plugin_author_set['openmembercenter']);
		}

		//区域代理
		$hasabonus = false;
		$plugin_abonus = p('abonus');
		if($plugin_abonus){
			$plugin_abonus_set = $plugin_abonus->getSet();
			$hasabonus = !empty($plugin_abonus_set['open']) && !empty($plugin_abonus_set['openmembercenter']);
		}

		//微信会员卡信息
		$card = m('common')->getSysset("membercard");
		$actionset = m('common')->getSysset("memberCardActivation");

		//会员卡商品信息


		$haveverifygoods = m('verifygoods')->checkhaveverifygoods($_W['openid']);
		if(!empty($haveverifygoods))
		{
			$verifygoods = m('verifygoods')->getCanUseVerifygoods($_W['openid']);
		}


		$showcard =0;
		if(!empty($card))
		{
			$membercardid = $member['membercardid'];

			if(!empty($membercardid)&&$card['card_id']==$membercardid)
			{
				$cardtag = '查看微信会员卡信息';
				$showcard =1;
			}
			else if(!empty($actionset["centerget"]))
			{
				$showcard =1;
				$cardtag = '领取微信会员卡';
			}
		}


		$hasqa = false;
		$plugin_qa = p('qa');
		if($plugin_qa){
			$plugin_qa_set = $plugin_qa->getSet();
			if(!empty($plugin_qa_set['showmember'])){
				$hasqa = true;
			}
		}

		$hassign = false;
		$com_sign = p('sign');
		if($com_sign){
			$com_sign_set = $com_sign->getSet();
			if(!empty($com_sign_set['iscenter']) && !empty($com_sign_set['isopen'])){
				$hassign = empty($_W['shopset']['trade']['credittext']) ? "积分" : $_W['shopset']['trade']['credittext'];
				$hassign .= empty($com_sign_set['textsign']) ? "签到" : $com_sign_set['textsign'];
			}
		}

        $hasLineUp = false;
        $lineUp = p('lineup');
        if($lineUp){
            $lineUpSet = $lineUp->getSet();
            if(!empty($lineUpSet['isopen']) && !empty($lineUpSet['mobile_show'])){
                $hasLineUp = true;
            }
        }

		$wapset = m('common')->getSysset('wap');
		$appset = m('common')->getSysset('app');

        $needbind = false;
        if(empty($member['mobileverify']) || empty($member['mobile'])){
            if((empty($_W['shopset']['app']['isclose']) && !empty($_W['shopset']['app']['openbind'])) || !empty($_W['shopset']['wap']['open']) || $hasThreen){
                // 判断 如果小程序开启绑定 或者 开启WAP 则需要绑定
                $needbind = true;
            }
        }

        if(p('mmanage')){
            $roleuser = pdo_fetch("SELECT id, uid, username, status FROM". tablename("ewei_shop_perm_user"). "WHERE openid=:openid AND uniacid=:uniacid AND status=1 LIMIT 1", array(":openid"=>$_W['openid'], ":uniacid"=>$_W['uniacid']));
        }

        //全返
        $hasFullback = true;
        $ishidden = m('common')->getSysset('fullback');
        if($ishidden['ishidden']==true){
            $hasFullback = false;
        }


        //团队分红
        $hasdividend = false;
        $plugin_dividend = p('dividend');
        if($plugin_dividend){
            $plugin_dividend_set = $plugin_dividend->getSet();
            if(!empty($plugin_dividend_set['open']) && !empty($plugin_dividend_set['membershow'])){
                $hasdividend = true;
            }
        }



        include $this->template();
	}

}
