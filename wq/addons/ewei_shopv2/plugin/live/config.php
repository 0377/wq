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
    'id'=>'live',
    'name'=>'互动直播',
    'v3'=>true,
    'menu'=>array(
        'title'=>'页面',
        'plugincom'=>1,
        'icon'=>'page',
        'items'=>array(
            array(
                'title'=>'直播间管理',
                'route'=>'room'
            ),
            array(
                'title'=>'分类管理',
                'route'=>'category'
            ),

            array(
                'title'=>'幻灯片管理',
                'route'=>'banner'
            ),
            array(
                'title'=>'其他',
                'items'=>array(
                    array(
                        'title'=>'通信服务',
                        'route'=>'service'
                    )
                )
            ),
            array(
                'title'=>'设置',
                'items'=>array(
                    array(
                        'title'=>'入口设置',
                        'route'=>'cover'
                    ),
                    array(
                        'title'=>'基础设置',
                        'route'=>'setting'
                    )
                )
            )
        )
    )
);