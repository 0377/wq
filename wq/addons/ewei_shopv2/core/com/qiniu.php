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

//七牛 classLoader 冲突解决
function qiniuClassLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = IA_ROOT . '/framework/library/qiniu/src/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

class Qiniu_EweiShopV2ComModel extends ComModel
{

    public function save($url, $config = null, $enforce = false)
    {
        global $_W, $_GPC;

        $oldurl = $url;

        set_time_limit(0);
        if (empty($url)) {
            return '';
        }
        $ext = strrchr($url, ".");
        if ($ext != ".jpeg" && $ext != ".gif" && $ext != ".jpg" && $ext != ".png") {
            return "";
        }
        if (!$config) {
            $config = $this->getConfig();
        }

        if (empty($config)) {
            if (!empty($_W['setting']['remote']['type']) && !(strexists($url, 'http:') || strexists($url, 'https:'))) {
                if (is_file(ATTACHMENT_ROOT . $url)) {
                    load()->func('file');
                    $remotestatus = file_remote_upload($url, false);
                    if (is_error($remotestatus)) {
                        return $url;
                    }
                }
                $remoteurl = $_W['attachurl_remote'] . $url;  // 远程图片的访问URL
                return $remoteurl;
            }
            return $url;
        }


        if (strexists($url, $config['url'])) {
            return $url;
        }

        if (strexists($url, '../addons/ewei_shopv2')) {
            $url = str_replace("../addons/ewei_shopv2", "addons/ewei_shopv2", $url);

        }

        if (!strexists($url, 'addons/ewei_shopv2')) {
            $url = tomedia($url);
        }

        if (!empty($_W['setting']['remote']['type'])) {
            $enforce = true;
        }

        $outlinkEnforce = false;
        if (!strexists($url, $_W['siteroot'])) {
            if (strexists($url, 'http:') || strexists($url, 'https:')) {
                if (!$enforce) {
                    return $url;
                }
                $outlinkEnforce = true;
            }
        }

        if (!$outlinkEnforce) {
            if (strexists($url, 'http:') || strexists($url, 'https:')) {
                if (!strexists($url, 'addons/ewei_shopv2')) {
                    $url = ATTACHMENT_ROOT . str_replace($_W['siteroot'] . "attachment/", "", str_replace($_W['attachurl'], "", $url));
                } else {
                    $url = IA_ROOT . "/" . $url;
                }
            } else {
                $outlinkEnforce = true;
                if (strexists($url, 'addons/ewei_shopv2')) {
                    $url = IA_ROOT . "/" . $url;
                    $outlinkEnforce = false;
                }
            }
        }

        $key = ($outlinkEnforce ? md5($url) : md5_file($url)) . $ext;

        if ($outlinkEnforce) {
            //先临时保存本地
            $local = ATTACHMENT_ROOT . "ewei_shopv2_temp/";
            load()->func('file');
            if (!is_dir($local)) {
                mkdirs($local);
            }
            $filename = $local . $key;
            load()->func('communication');
            $resp = ihttp_get($url);
            if ($resp['code'] != 200) {
                return $oldurl;
            }
            file_put_contents($filename, $resp['content']);
            $url = $filename;
        }


        if (!function_exists('classLoader')) {
            require_once(IA_ROOT . '/framework/library/qiniu/autoload.php');
        } else {

            spl_autoload_register('qiniuClassLoader');
            require_once IA_ROOT . '/framework/library/qiniu/src/Qiniu/functions.php';
        }

        $auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);
        if (is_callable("\Qiniu\Zone::zone0")) {
            $zone = \Qiniu\Zone::zone0();
            if ($config['zone'] == 'zone1') {
                $zone = \Qiniu\Zone::zone1();
            }
            $uploadmgr = new Qiniu\Storage\UploadManager(new \Qiniu\Config($zone));
            $putpolicy = Qiniu\base64_urlSafeEncode(json_encode(array('scope' => $config['bucket'] . ':' . $url)));
            $uploadtoken = $auth->uploadToken($config['bucket'], $key, 3600, $putpolicy);
        } else {
            $uploadmgr = new Qiniu\Storage\UploadManager();
            $uploadtoken = $auth->uploadToken($config['bucket'], $key, 3600);
        }

        $ret = $uploadmgr->putFile($uploadtoken, $key, $url);
        if (!empty($ret[1])) {
            $err = $ret[1]->getResponse()->error;
            return error(1, $err);
        }

        if ($outlinkEnforce) {
            @unlink($url);
        }
        //删除网络文件
//			if (!empty($oldurl))
//			{
//				$this->deletewqfile($oldurl);
//			}
        if (strexists($config['url'], 'http:') || strexists($config['url'], 'https:')) {
            return trim($config['url']) . "/" . $ret[0]['key'];
        }
        return "http://" . trim($config['url']) . "/" . $ret[0]['key'];
    }

    /**
     * 获取配置
     * @return boolean
     */
    function getConfig()
    {

        global $_W, $_GPC;
        $config = false;
        $set = m('common')->getSysset('qiniu');
        //用户设置
        if (isset($set['user']) && is_array($set['user']) && !empty($set['user']['upload']) && !empty($set['user']['access_key']) && !empty($set['user']['secret_key']) && !empty($set['user']['bucket']) && !empty($set['user']['url'])) {

            $config = $set['user'];

        } else {
            $path = IA_ROOT . "/addons/ewei_shopv2/data/global";
            $admin = m('cache')->getArray('qiniu', 'global');
            if (empty($admin['upload']) && is_file($path . '/qiniu.cache')) {
                $data_authcode = authcode(file_get_contents($path . '/qiniu.cache'), 'DECODE', 'global');
                $admin = json_decode($data_authcode, true);
            }
            if (is_array($admin) && !empty($admin['upload']) && !empty($admin['access_key']) && !empty($admin['secret_key']) && !empty($admin['bucket']) && !empty($admin['url'])) {
                $config = $admin;
            }
        }
        return $config;
    }

    function deletewqfile($attachment)
    {
        global $_W;
        $attachment = trim($attachment);
        $media = pdo_get('core_attachment', array('uniacid' => $_W['uniacid'], 'attachment' => $attachment));
        if (empty($media)) {
            return false;
        }
        if (empty($_W['isfounder']) && $_W['role'] != 'manager') {
            return false;
        }
        load()->func('file');
        if (!empty($_W['setting']['remote']['type'])) {
            $status = file_remote_delete($media['attachment']);
        } else {
            $status = file_delete($media['attachment']);
        }
        if (is_error($status)) {
            return $status['message'];
        }
        pdo_delete('core_attachment', array('uniacid' => $_W['uniacid'], 'id' => $media['id']));
        return true;
    }

}
