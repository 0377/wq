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

	function main() {
		global $_W, $_GPC;
		//定制
		/*if(p('offic')){
            header('location: ' . mobileUrl('offic'));
        }*/
        $_SESSION['newstoreid']=0;
		$this->diypage('home');

		$trade = m('common')->getSysset('trade');
		//交易增强功能
		if(empty($trade['shop_strengthen']))
		{
			$order = pdo_fetch("select id,price  from ".tablename("ewei_shop_order")." where uniacid=:uniacid and status = 0 and paytype<>3 and openid=:openid order by createtime desc limit 1",array(":uniacid"=>$_W['uniacid'],":openid"=>$_W['openid']));

			if(!empty($order))
			{
				$goods = pdo_fetchall("select g.*,og.total as totals  from ".tablename("ewei_shop_order_goods")." og inner join ".tablename("ewei_shop_goods")." g on og.goodsid = g.id   where og.uniacid=:uniacid    and og.orderid=:orderid  limit 3",array(":uniacid"=>$_W['uniacid'],":orderid"=>$order['id']));

				$goodstotal = pdo_fetchcolumn("select COUNT(*)  from ".tablename("ewei_shop_order_goods")." og inner join ".tablename("ewei_shop_goods")." g on og.goodsid = g.id   where og.uniacid=:uniacid    and og.orderid=:orderid ",array(":uniacid"=>$_W['uniacid'],":orderid"=>$order['id']));

			}
		}

		$mid = intval($_GPC['mid']);
		$index_cache = $this->getpage();
		if(!empty($mid)){
			$index_cache = preg_replace_callback("/href=[\'\"]?([^\'\" ]+).*?[\'\"]/", function($matches)use($mid){
				$preg = $matches[1];
				if(strexists($preg,"mid=")){
					return "href='".$preg."'";
				}

				if(!strexists($preg,"javascript")){
					$preg = preg_replace('/(&|\?)mid=[\d+]/', "", $preg);

					if(strexists($preg,"?")){
						$newpreg = $preg."&mid=$mid";

					}else{
						$newpreg = $preg."?mid=$mid";
					}
					return "href='".$newpreg."'";
				}
			}, $index_cache);
		}
		$shop_data = m('common')->getSysset('shop');
		if(com('coupon')){
            $cpinfos = com('coupon')->getInfo();
        }
		include $this->template();
	}

	function get_recommand(){
		global $_W, $_GPC;
		$args = array(
			'page' => $_GPC['page'],
			'pagesize' => 6,
			'isrecommand' => 1,
			'order' => 'displayorder desc,createtime desc',
			'by' => ''
		);
		$recommand = m('goods')->getList($args);
		show_json(1,array('list'=>$recommand['list'], 'pagesize'=>$args['pagesize'], 'total'=>$recommand['total'], 'page'=>intval($_GPC['page'])));
	}

	private function getcache(){
		global $_W, $_GPC;
		return m("common")->createStaticFile(mobileUrl('getpage',null,true));
	}

	function getpage(){
		global $_W, $_GPC;
		$uniacid =$_W['uniacid'];
		$defaults = array(
			'adv' =>array('text'=> '幻灯片','visible'=>1),
			'search' =>array('text'=> '搜索栏','visible'=>1),
			'nav' =>array('text'=> '导航栏','visible'=>1),
			'notice' => array('text'=>'公告栏','visible'=>1),
			'cube' =>array('text'=> '魔方栏','visible'=>1),
			'banner' =>array('text'=> '广告栏','visible'=>1),
			'goods' =>array('text'=> '推荐栏','visible'=>1)
		);
		$sorts = isset($_W['shopset']['shop']['indexsort'])?$_W['shopset']['shop']['indexsort']:$defaults;
		$sorts['recommand'] = array('text'=>'系统推荐', 'visible'=>1);
		$advs = pdo_fetchall("select id,advname,link,thumb from " . tablename('ewei_shop_adv') . ' where uniacid=:uniacid and iswxapp=0 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$navs = pdo_fetchall("select id,navname,url,icon from " . tablename('ewei_shop_nav') . ' where uniacid=:uniacid and iswxapp=0 and status=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$cubes = is_array($_W['shopset']['shop']['cubes'])?$_W['shopset']['shop']['cubes']:array();
		$banners = pdo_fetchall("select id,bannername,link,thumb from " . tablename('ewei_shop_banner') . ' where uniacid=:uniacid and iswxapp=0 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$bannerswipe = $_W['shopset']['shop']['bannerswipe'];
		if(!empty($_W['shopset']['shop']['indexrecommands'])){
			$goodids = implode(",", $_W['shopset']['shop']['indexrecommands']);
			if(!empty($goodids)){
				$indexrecommands = pdo_fetchall("select id, title, thumb, marketprice,ispresell,presellprice, productprice, minprice, total,type from " . tablename('ewei_shop_goods') . " where id in( $goodids ) and uniacid=:uniacid and deleted = 0 and status=1 order by instr('{$goodids}',id),displayorder desc", array(':uniacid' => $uniacid));
				foreach($indexrecommands as $key => $value){
					if($value['ispresell']>0){
						$indexrecommands[$key]['minprice'] = $value['presellprice'];
					}
				}
			}
		}
		$goodsstyle = $_W['shopset']['shop']['goodsstyle'];
		$notices = pdo_fetchall("select id, title, link, thumb from " . tablename('ewei_shop_notice') . ' where uniacid=:uniacid and iswxapp=0 and status=1 order by displayorder desc limit 5', array(':uniacid' => $uniacid));

        //秒杀信息
        $seckillinfo = plugin_run('seckill::getTaskSeckillInfo');
		ob_start();
		ob_implicit_flush(false);
		require($this->template('index_tpl'));
		return ob_get_clean();
	}
	function seckillinfo(){

        $seckillinfo = plugin_run('seckill::getTaskSeckillInfo');
        include $this->template('shop/index/seckill_tpl');
        exit;
    }

    function qr()
    {
        global $_W,$_GPC;
        $url = trim($_GPC['url']);
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($url, false, QR_ECLEVEL_L, 16,1);
    }

    function share_url()
    {
        global $_W,$_GPC;
        $url = trim($_GPC['url']);
        $account_api = WeAccount::create($_W['acid']);
        $jssdkconfig = $account_api->getJssdkConfig($url);
        show_json(1,$jssdkconfig);
    }
}
