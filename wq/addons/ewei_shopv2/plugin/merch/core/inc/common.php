<?php

function merchUrl($do = '', $query = null, $full = false) {
    global $_W, $_GPC;
    //$do = 'merch.manage.'.$do;
    $dos = explode('/', trim($do));
    $routes = array();
    $routes[] = $dos[0];
    if (isset($dos[1])) {
        $routes[] = $dos[1];
    }
    if (isset($dos[2])) {
        $routes[] = $dos[2];
    }
    if (isset($dos[3])) {
        $routes[] = $dos[3];
    }
    $r = implode(".", $routes);

    if(!is_array($query)){
        $query = array();
    }
    if (!empty($r)) {
        $query = array_merge(array('r' => $r), $query);
    }
    $query = array_merge(array('do' => 'web'), $query);
    $query = array_merge(array('m' => "ewei_shopv2"), $query);
    return str_replace("./index.php", "./merchant.php", wurl('site/entry', $query) );

}

function mce($permtype = '', $item = null)
{
    $perm = plugin_run('merch::check_edit', $permtype, $item);
    return $perm;
}

function mcp($plugin = ''){
    return true;
    $perm = plugin_run('merch::permCheckPlugin', $plugin);
    return true;
}

function mcv($permtypes = '')
{
    $perm = plugin_run('merch::check_perm', $permtypes);
    return $perm;
}
function mplog($type = '', $op = '')
{
    plugin_run('merch::log', $type, $op);
}
function mca($permtypes = '')
{
    return;
    global $_W;
    $err = '您没有权限操作，请联系管理员!';
    if (!mcv($permtypes)) {
        if ($_W['isajax']) {
            show_json(0, $err);
        }
        show_message($err, '', 'error');
    }
}

function mp($plugin = ''){
    $plugin = p($plugin);
    if(!$plugin){
        return false;
    }
    if( mcp($plugin)){
        return $plugin;
    }
    return false;
}

function mcom($com = ''){
    return true;
    $com = com($com);
    if(!$com){
        return false;
    }
    return false;
}


global $_W;
$routes = explode('.', $_W['routes'] );
$GLOBALS['_W']['tab'] = isset($routes[2])?$routes[2]:'';
$uniacid = intval($_GPC['__uniacid']);
$session = $_SESSION['__merch_uniacid'];
if(!empty($session)){
    $uniacid = $session;
}
if($_W['routes']!='merch.manage.login'){
    $session_key = '__merch_'.$uniacid .'_session';
    $session = json_decode(base64_decode($_GPC[$session_key]), true);

    if(is_array($session)) {

        $account = pdo_fetch("select * from ".tablename('ewei_shop_merch_account')." where id=:id limit 1",array(':id'=>$session['id']));
        if(!is_array($account)  || $session['hash'] != md5($account['pwd'] . $account['salt'])) {
            isetcookie($session_key, false, -100);
            header('location: '.merchUrl('login'));
            exit;
        }
        $GLOBALS['_W']['uniaccount'] = $account;
    } else{

        isetcookie($session_key, false, -100);
        header('location: '.merchUrl('login'));
        exit;
    }

}
$GLOBALS['_W']['uniacid'] = $uniacid;
$GLOBALS['_W']['merchid'] = $session['merchid'];
$GLOBALS['_W']['merchuid'] = $session['id'];
$GLOBALS['_W']['merchusername'] = $session['username'];
$GLOBALS['_W']['merchisfounder'] = $session['isfounder'];
$merch_user = pdo_fetch("select u.*,g.groupname,g.goodschecked,g.commissionchecked,g.changepricechecked,g.finishchecked from ".tablename('ewei_shop_merch_user')." u left join ".tablename('ewei_shop_merch_group')." g on u.groupid=g.id where u.id=:id limit 1",array(':id'=>$session['merchid']));
$GLOBALS['_W']['merch_user'] = $merch_user;
$GLOBALS['_W']['merch_username'] = $merch_user['merchname'];
$GLOBALS['_W']['accounttotal'] = $merch_user['accounttotal'];
unset($merch_user);

// 处理新版微擎附件路径
$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
if (!empty($_W['setting']['remote'][$_W['uniacid']]['type'])) {
    $_W['setting']['remote'] = $_W['setting']['remote'][$_W['uniacid']];
}
if (!empty($_W['setting']['remote']['type'])) {
    if ($_W['setting']['remote']['type'] == ATTACH_FTP) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_OSS) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_QINIU) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_COS) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
    }
}

load()->func('tpl');