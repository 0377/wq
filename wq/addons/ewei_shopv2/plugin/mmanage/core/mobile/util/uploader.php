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

require EWEI_SHOPV2_PLUGIN . 'mmanage/core/inc/page_mmanage.php';
class Uploader_EweiShopV2Page extends MmanageMobilePage
{

    function main()
    {
        global $_W, $_GPC;

        load()->func('file');
        $field = $_GPC['file'];

        if (!empty($_FILES[$field]['name'])) {

            if (is_array($_FILES[$field]['name'])) {
                $files = array();

                //多图
                foreach ($_FILES[$field]['name'] as $key => $name) {
                    if (strrchr($name,'.') === false){
                        $name = $name.'.jpg';
                    }
                    $file = array(
                        'name' => $name,
                        'type' => $_FILES[$field]['type'][$key],
                        'tmp_name' => $_FILES[$field]['tmp_name'][$key],
                        'error' => $_FILES[$field]['error'][$key],
                        'size' => $_FILES[$field]['size'][$key],
                    );
                    /*
                     * iphone上传图片旋转问题
                     * 检测iPhone上传图片Orientation属性实施偏转
                     * exif_read_data是否开启php exif扩展
                     * */
                    if(function_exists('exif_read_data')){
                        $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
                        $exif = exif_read_data($file['tmp_name']);
                        if(!empty($exif['Orientation'])) {
                            switch($exif['Orientation']) {
                                case 8:
                                    $image = imagerotate($image,90,0);
                                    break;
                                case 3:
                                    $image = imagerotate($image,180,0);
                                    break;
                                case 6:
                                    $image = imagerotate($image,-90,0);
                                    break;
                            }
                        }
                        imagejpeg($image,$file['tmp_name']);
                    }
                    
                    $files[] = $this->upload($file);

                }
                $ret = array(
                    'status'=>'success',
                    'files'=>$files
                 );
                exit(json_encode($ret));

            } else {
                //单图
                if (strrchr($_FILES[$field]['name'],'.') === false){
                    $_FILES[$field]['name'] = $_FILES[$field]['name'].'.jpg';
                }
                $result  = $this->upload($_FILES[$field]);
                exit(json_encode($result));
            }

        } else {
            $result['message'] = '请选择要上传的图片！';
            exit(json_encode($result));
        }
    }

    protected function upload($uploadfile)
    {

        global $_W, $_GPC;
        $result['status'] = 'error';
        if ($uploadfile['error'] != 0) {
            $result['message'] = '上传失败，请重试！';
            return $result;
        }

        load()->func('file');

        $path = '/images/ewei_shop/' . $_W['uniacid'];
        if (!is_dir(ATTACHMENT_ROOT . $path)) {
            mkdirs(ATTACHMENT_ROOT . $path);
        }
        $_W['uploadsetting'] = array();
        $_W['uploadsetting']['image']['folder'] = $path;
        $_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
        $_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
        $file = file_upload($uploadfile, 'image');
        if (is_error($file)) {
            $result['message'] = $file['message'];
            return $result;
        }

        //判断远程
        if (function_exists('file_remote_upload')) {
            $remote = file_remote_upload($file['path']);
            if (is_error($remote)) {
                $result['message'] = $remote['message'];
                return $result;
            }
        }

        $result['status'] = "success";
        $result['url'] = $file['url'];
        $result['error'] = 0;
        $result['filename'] = $file['path'];
        $result['url'] = trim($_W['attachurl'] . $result['filename']);
//2018-3-23  用户手机端修改头像之后 会出现在 上传图片的本地服务器中  就是不用存到数据库中
//        pdo_insert('core_attachment', array(
//            'uniacid' => $_W['uniacid'],
//            'uid' => $_W['member']['uid'],
//            'filename' => $uploadfile['name'],
//            'attachment' => $result['filename'],
//            'type' => 1,
//            'createtime' => TIMESTAMP,
//        ));
        return $result;

    }

    function remove()
    {
        global $_W, $_GPC;
        load()->func('file');
        $file = $_GPC['file'];
        file_delete($file);
        exit(json_encode(array('status' => 'success')));
    }

}
