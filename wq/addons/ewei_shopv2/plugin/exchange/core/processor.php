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
require_once IA_ROOT . '/addons/ewei_shopv2/defines.php';
require_once EWEI_SHOPV2_INC . 'plugin_processor.php';
require_once EWEI_SHOPV2_INC . 'receiver.php';

class ExchangeProcessor extends PluginProcessor {

    public function __construct() {
        parent::__construct('exchange');
    }

    public function respond($obj = null) {
        global $_W;
        $message = $obj->message;
        $msgtype = strtolower($message['msgtype']);
        $message['content'] = trim($message['content']);
        m('member')->checkMember();
        if ($msgtype == 'text'){
            if ($message['content'] === $_SESSION['kouling']['koulingend']){//退出
                $respond = $_SESSION['kouling']['chufaend'];
                unset($_SESSION['kouling']);
                $obj->endContext();
                return $obj->respText($respond);
            }

            if (!$obj->inContext && empty($_SESSION['kouling']['id'])) {
                $kouling = $this->model->redKeyword($message['content']);
                @session_start();
                $_SESSION['kouling'] = $kouling;
                if (empty($kouling)){
                    $obj->endContext();
                    return $obj->respText('口令不存在');
                }
                $obj->beginContext();
                return $obj->respText($kouling['chufa']);
            }else if ($obj->inContext) {
                $ExchangeRedCode = trim($message['content']);
                $obj->endContext();
                $this->model->sendRedpacket($ExchangeRedCode);
                return $obj->respText('已退出口令红包');
            }
        } else{
            return $obj->respText('暂不受理非文本消息');
        }
    }
}
