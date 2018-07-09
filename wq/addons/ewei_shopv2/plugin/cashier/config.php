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
    'id'=>'cashier',
    'name'=>'人人收银台',
    'v3'=>true,
    'menu'=>array(
        'title'=>'页面',
        'plugincom'=>1,
        'icon'=>'page',
        'items'=>array(
            array(
                'title'=>'收银台管理',
                'items'=>array(
                    array(
                        'title'=>'收银台管理',
                        'route'=>'user'
                    ),
                    array(
                        'title'=>'收银台分类',
                        'route'=>'category'
                    )
                )
            ),
            array(
                'title'=>'收银台结算',
                'route'=>'clearing',
                'items'=>array(
                    array(
                        'title'=>'待审核',
                        'param'=>array(
                            'status'=>0
                        )
                    ),
                    array(
                        'title'=>'待结算',
                        'param'=>array(
                            'status'=>1
                        )
                    ),
                    array(
                        'title'=>'已结算',
                        'param'=>array(
                            'status'=>2
                        )
                    )
                )
            ),
            array(
                'title'=>'设置',
                'items'=>array(
                    array(
                        'title'=>'基础设置',
                        'route'=>'set'
                    ),
                    array(
                        'title'=>'消息通知',
                        'route'=>'notice'
                    )
                )
            )
        )
    )
);