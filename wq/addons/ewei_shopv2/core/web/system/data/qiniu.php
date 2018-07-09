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

class Qiniu_EweiShopV2Page extends SystemPage {
	
	public function main() {
		global $_W, $_GPC;

        if (!com('qiniu')) {
            header('Location: ' . webUrl());
        }
		$path = IA_ROOT . "/addons/ewei_shopv2/data/global";
		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}
		if($_W['ispost']){
			$data = is_array($_GPC['data'])?$_GPC['data']:array();
			 
			if($data['upload']){
				$check = com('qiniu')->save('addons/ewei_shopv2/static/images/nopic100.jpg',$data);
                if(is_array($check) && is_error($check)){
                    show_json(0, "保存失败: ".$check['message']);
                }
			}
			m('cache')->set('qiniu',$data,'global');
			$data_authcode = authcode(json_encode($data),'ENCODE','global');
			file_put_contents($path.'/qiniu.cache',$data_authcode);
			show_json(1);
		}
		$data = m('cache')->getArray('qiniu', 'global');
		if (empty($data['upload']) && is_file($path.'/qiniu.cache')){
			$data_authcode = authcode(file_get_contents($path.'/qiniu.cache'),'DECODE','global');
			$data = json_decode($data_authcode,true);
		}
		include $this->template();
	}

}
