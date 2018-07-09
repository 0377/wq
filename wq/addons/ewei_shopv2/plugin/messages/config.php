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
    'id'=>'messages',
    'name'=>'消息群发',
    'v3'=>true,
    'menu'=>array(
        'plugincom'=>1,
        'items'=>array(
            array(
                'title'=>'消息群发',
                'route'=>'',
                'extends'=>array(
                    'messages.run',
                    'messages.showsign'
                )
            ),
            array(
                'title'=>'模版设置',
                'route'=>'template'
            )
        )
    )
);

