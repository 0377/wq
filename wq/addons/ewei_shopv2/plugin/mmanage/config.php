<?php
/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if(!defined('IN_IA')) {
     exit('Access Denied');
}

return array(
    'version'=>'1.0',
    'id'=>'mmanage',
    'name'=>'手机端管理',
    'v3'=>true,
    'menu'=>array(
        'plugincom'=>1,
        'items'=>array(
            array(
                'title'=>'基本设置',
                'route'=>'setting'
            )
        )
    )
);

