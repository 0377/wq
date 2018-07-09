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
require EWEI_SHOPV2_PLUGIN .'creditshop/core/page_mobile.php';
class Lists_EweiShopV2Page extends CreditshopMobilePage {
	
	function main(){
		global $_W, $_GPC;
		
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$shop = m('common')->getSysset('shop');
		$uniacid = $_W['uniacid'];
		$cateid = intval($_GPC['cate']);
		//多商户
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		} else {
			$is_openmerch = 0;
		}
		
		if(!empty($cateid)){
			$cate  = pdo_fetch('select id,name from '.tablename('ewei_shop_creditshop_category').' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$cateid,':uniacid'=>$uniacid));
		}
		//分类
		$category = array();
		if(intval($_GPC['merchid'])>0) {
			$merch_category = p('merch')->getSet('merch_creditshop_category',$_GPC['merchid']);
			if (!empty($merch_category)) {
				foreach ($merch_category as $index => $row) {
					if($row>0){
						$category[$index] = pdo_fetchall("select id,name,thumb,isrecommand from " . tablename('ewei_shop_creditshop_category') . '
						where id = ".$index." and uniacid=:uniacid and  enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
						$category[$index] = set_medias($category, 'thumb');
					}
				}
			}
		}else{
			$category = pdo_fetchall("select id,name,thumb,isrecommand from " . tablename('ewei_shop_creditshop_category') . ' where uniacid=:uniacid and  enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
			$category = set_medias($category, 'thumb');
		}
		array_values($category);
		
		$_W['shopshare'] = array(
		    'title' => $this->set['share_title'],
		    'imgUrl' => tomedia($this->set['share_icon']),
		    'link' => mobileUrl('creditshop', array(), true),
		    'desc' => $this->set['share_desc']
		);
		$com = p('commission');
		if ($com) {
		    $cset = $com->getSet();
		    if (!empty($cset)) {
		        if ($member['isagent'] == 1 && $member['status'] == 1) {
		            $_W['shopshare']['link'] = mobileUrl('creditshop', array('mid' => $member['id']), true);
		            if (empty($cset['become_reg']) && ( empty($member['realname']) || empty($member['mobile']))) {
		                $trigger = true;
		            }
		        } else if (!empty($_GPC['mid'])) {
		            $_W['shopshare']['link'] = mobileUrl('creditshop/detail', array('mid' => $_GPC['mid']), true);
		        }
		    }
		}
		
		include $this->template();
	}
	 
	function getlist(){
		global $_W, $_GPC;
		
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$shop = m('common')->getSysset('shop');
		$uniacid = $_W['uniacid'];
		$cateid = intval($_GPC['cate']);
		$merchid = intval($_GPC['merchid']);
		
		$cate  = pdo_fetch('select id,name from '.tablename('ewei_shop_creditshop_category').' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$cateid,':uniacid'=>$uniacid));
		
		$pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = ' and uniacid = :uniacid and status=1 and deleted=0 ';
		if($merchid>0){
			$condition .= " and merchid = ".$merchid." ";
		}
        $params = array(':uniacid' => $_W['uniacid'],);
        if(!empty($cate)){
            $condition.=" and cate=".$cateid;
        }
		
		$keywords = trim($_GPC['keywords']);
		if(!empty($keywords)){
			$condition .= " AND title like '%{$keywords}%' ";
		}
	
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_creditshop_goods') . " where 1 {$condition}";
        $total = pdo_fetchcolumn($sql, $params);
		$list = array();
        if (!empty($total)) {
            $sql = 'SELECT id,title,thumb,subtitle,`type`,price,credit,money,goodstype,hasoption,mincredit,minmoney FROM ' . tablename('ewei_shop_creditshop_goods') . '
            		where 1 ' . $condition . ' ORDER BY displayorder desc,id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);
            $list = set_medias($list, 'thumb');
            foreach ($list as &$row) {
                if ($row['credit'] > 0 && $row['money'] > 0) {
                    $row['acttype'] = 0; //积分+钱
                } else if ($row['credit'] > 0) {
                    $row['acttype'] = 1; //积分
                } else if ($row['money'] > 0) {
                    $row['acttype'] = 2; //钱
                }
				/*if((intval($row['money'])-$row['money'])==0){
					$row['money'] = intval($row['money']);
				}*/
                $row['money'] = price_format($row['money'],2);
				if($row['hasoption']>0){
                    $row['credit'] = intval($row['mincredit']);
                    $row['money'] = price_format($row['minmoney'],2);
                }
            }
            unset($row);
        }
		show_json(1,array('list'=>$list,'pagesize'=>$psize,'total'=>$total));
	}

}
