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
    'id'=>'diyform',
    'name'=>'自定义表单',
    'v3'=>true,
    'menu'=>array(
        'plugincom'=>1,
        'items'=>array(
            array(
                'title'=>'模版管理',
                'route'=>'temp'
            ),
            array(
                'title'=>'分类管理',
                'route'=>'category'
            ),
            array(
                'title'=>'基础设置',
                'route'=>'set'
            )
        )
    )
);
 