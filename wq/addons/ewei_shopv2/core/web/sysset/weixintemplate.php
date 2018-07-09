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

class Weixintemplate_EweiShopV2Page extends WebPage
{

	function main() {
		global $_W, $_GPC;

		$list =$this->gettemplatelist();

		$industry =$this->getindustry();

		$industrytext='';

		if($industry&&is_array($industry))
		{
			foreach($industry as $indu)
			{
				$industrytext.=$indu['first_class']."/".$indu['second_class']."&nbsp;&nbsp;&nbsp;";
			}
		}


		include $this->template();
	}

	function post() {
		global $_W, $_GPC;
		$id = $_GPC['id'];
		$list =$this->gettemplatelist();
		$template=null;

		foreach($list as $temp)
		{
			if($temp['template_id']==$id)
			{
				$template=$temp;
				break;
			}
		}
		include $this->template();
	}

	function delete() {
		global $_W, $_GPC;
		$id = $_GPC['id'];
		if (empty($id)) {
			$ids=$_GPC['ids'];
			if(is_array($ids))
			{
				foreach($ids as $i)
				{
					$this->deltemplatebyid($i);
				}
			}
		}else
		{
			//show_json(0,$id);
			$this->deltemplatebyid($id);
		}

		show_json(1, array('url' => referer()));
	}


	//获取设置的行业信息
	function getindustry() {
		global $_W, $_GPC;
		load()->func('communication');

		$account = m('common')->getAccount();
		$token = $account->fetch_token();
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=" . $token;
		$c =ihttp_request($url);

		$result = @json_decode($c['content'], true);

		if (!is_array($result)) {
			return false;
		}

		if (!empty($result['errcode'])) {
			return false;
		}
		return $result;
	}


	//根据微信模板短编码获取ID并加入微信我的模板
	function gettemplateid() {
		global $_W, $_GPC;
		load()->func('communication');

		$bb = "{\"template_id_short\":\"" . $_GPC['templateidshort'] ."\"}";
		$account = m('common')->getAccount();
		$token = $account->fetch_token();
		$url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=" . $token;
		$c =ihttp_request($url,$bb);

		$result = @json_decode($c['content'], true);
		if (!is_array($result)) {
			show_json(0);
		}

		if (!empty($result['errcode'])) {
			show_json(0, $result['errmsg']);
		}
		show_json(1,$result);
	}

	//获取微信模板列表
	function gettemplatelist() {
		global $_W, $_GPC;
		load()->func('communication');

		$account = m('common')->getAccount();
		$token = $account->fetch_token();
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=" . $token;
		$c =ihttp_request($url);

		$result = @json_decode($c['content'], true);
		if (!is_array($result)) {
			return false;
		}

		if (!empty($result['errcode'])) {
			return false;
		}

		return $result['template_list'];
	}

	//删除微信模板库模板
	function deltemplatebyid($template_id) {
		global $_W, $_GPC;
		load()->func('communication');

		$bb = "{\"template_id\":\"" . $template_id ."\"}";

		$account = m('common')->getAccount();
		$token = $account->fetch_token();
		$url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=" . $token;
		$c =ihttp_request($url,$bb);

		$result = @json_decode($c['content'], true);
		if (!is_array($result)) {
			show_json(0);
		}

		if (!empty($result['errcode'])) {
			show_json(0, $result['errmsg']);
		}
		//show_json(1,$result);
	}


}
