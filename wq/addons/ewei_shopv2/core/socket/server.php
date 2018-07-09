<?php

define('IN_MOBILE', true);

require dirname(__FILE__). '/socket.config.php';

// 错误提示
error_reporting(SOCKET_SERVER_DEBUG? E_ALL: 0);
ini_set('default_socket_timeout', -1);

require dirname(__FILE__).'/../../../../framework/bootstrap.inc.php';
require IA_ROOT.'/addons/ewei_shopv2/defines.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/functions.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/plugin_model.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/com_model.php';


class Socket {

    public $server, $redis, $tablename='ewei_shop_fd_scene';

    public function __construct() {
        global $_W;

        //判断Swoole扩展
        if(!class_exists('swoole_websocket_server')){
            return $this->error('Swoole扩展未安装或服务未启动');
        }

        // 判断Redis扩展
        if(!function_exists('redis') || is_error(redis())) {
            return $this->error('Redis扩展未安装或服务未启动');
        }
        $this->redis = redis();
        $this->clearScene();
        $_this = $this;

        // 开发时使用 ******
        //$this->redis->del('ewei_shop_live_room_1');

        // 初始化Socket服务
        $server = new swoole_websocket_server(SOCKET_SERVER_IP, SOCKET_SERVER_PORT, SWOOLE_BASE, SOCKET_SERVER_SSL? SWOOLE_SOCK_TCP | SWOOLE_SSL: SWOOLE_SOCK_TCP);

        $config = array(
            'worker_num'=>SOCKET_SERVER_WORKNUM,
            'daemonize'=>1,
            'log_file' => __DIR__ . "/swoole.log",
        );

        // 如果是开发者模式 关闭 守护进程化
        if(SOCKET_SERVER_DEBUG){
            unset($config['daemonize']);
        }

        // 设置SSL证书
        if(SOCKET_SERVER_SSL){
            $config['ssl_key_file'] = SOCKET_SERVER_SSL_KEY_FILE;
            $config['ssl_cert_file'] = SOCKET_SERVER_SSL_CERT_FILE;
        }

        $server->set($config);

        $server->on('workerstart', function($server, $id) use(&$_W) {
            if($id!=0){
                return false;
            }
            $redis_config = $_W['config']['setting']['redis'];
            if (empty($redis_config['server'])) {
                $redis_config['server'] = '127.0.0.1';
            }
            if (empty($redis_config['port'])) {
                $redis_config['port'] = '6379';
            }

            $redis = new redis();
            if ($redis_config['pconnect']) {
                $connect = $redis->pconnect($redis_config['server'], $redis_config['port'], $redis_config['timeout']);
            }else{
                $connect = $redis->connect($redis_config['server'], $redis_config['port'], $redis_config['timeout']);
            }
            if(!$connect){
                return false;
            }
            if (!empty($redis_config['requirepass'])) {
                $redis->auth($redis_config['requirepass']);
            }
            try{
                $ping = $redis->ping();
            }catch (ErrorException $e){
                return false;
            }
            if ($ping != '+PONG') {
                return false;
            }

            $server->redis = $redis;
        });

        $server->on('Start', function (){
            swoole_set_process_name("PHP EWEISHOPSOCKET MASTER");
        });

        // 监听Open事件
        $server->on('Open', function(swoole_websocket_server $server, $request){

        });

        // 监听Message事件
        $server->on('Message', function(swoole_websocket_server $server, $frame) use(&$_W, &$_this){
            $data = json_decode($frame->data, true);
            $data = $_this->special($data);

            $_this->log('msg_server', json_encode($frame));

            if(empty($data) || empty($data['scene'])){
                return $_this->error('scene值为空', 1);
            }

            if($data['scene']=='reload'){
                $server->reload();
                return $_this->error('reload服务', 1);
            }
            elseif($data['scene']=='communication'){
                $server->push($frame->fd, json_encode(array(
                    'type'=>'communication'
                )));
                return;
            }

            if(empty($data['uniacid'])){
                return $_this->error('uniacid值为空', 1);
            }
            $_W['uniacid'] = $data['uniacid'];
            // 根据scene定向到插件

            $result = socket($data['scene'])->onMessage($server, $data, $frame->fd);
            if($result){
                $_this->setScene($frame->fd, $data);
            }
        });

        // 监听Close事件
        $server->on('Close', function(swoole_websocket_server $server, $fd) use(&$_this){
            $data = $_this->getScene($fd);
            if(!$data){
                return $_this->error('可能存在丢scene情况，fd:'.$fd, 1);
            }
            // 移除sceneFd
            $_this->delScene($fd);
            // 根据scene定向到插件
            if(!empty($data['scene'])){
                $result = socket($data['scene'])->onClose($server, $fd, $data);
            }
        });

        // 启动服务
        $this->server = $server;
        $this->server->start();
    }

    /**
     * 清除所有Fd所在场景
     */
    public function clearScene() {
        $this->redis->hDel($this->tablename);
    }

    /**
     * 设置Fd所在场景
     * @param $fd
     * @param $scene
     * @return bool
     */
    public function setScene($fd, $scene) {
        $scene = is_array($scene)? json_encode($scene): $scene;
        $this->redis->hSet($this->tablename, $fd, $scene);
        return true;
    }

    /**
     * 根据Fd获取所在场景
     * @param $fd
     * @return string
     */
    public function getScene($fd) {
        $scene = $this->redis->hGet($this->tablename, $fd);
        $scene = !empty($scene)? json_decode($scene, true): false;
        return $scene;
    }

    /**
     *  删除Fd所在场景
     * @param $fd
     * @return bool
     */
    public function delScene($fd) {
        $this->redis->hDel($this->tablename, $fd);
        return true;
    }

    /**
     * @param $msg
     * @param int $type  0:error 1:notice -1:log
     */
    public function error($msg,  $type=0) {
        echo $msg."\n";
    }

    public function log($name, $text){
        $filename = dirname(__FILE__). '/log_'. $name. '.log';
        $text = '['. date("Y-m-d H:i:s", time()). '] '. $text;
        file_put_contents($filename, $text. "\r\n", FILE_APPEND);
    }

    public function special($obj) {
        if(!is_array($obj)){
            $obj = istripslashes($obj);
            $obj = ihtmlspecialchars($obj);
        }else{
            foreach ($obj as $k=>&$v){
                $v = istripslashes($v);
                $v = ihtmlspecialchars($v);
            }
        }
        return $obj;
    }

}

$server = new Socket();
