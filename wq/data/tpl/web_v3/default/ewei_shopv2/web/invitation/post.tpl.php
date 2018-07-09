<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<link href="../addons/ewei_shopv2/plugin/invitation/static/css/web.css?v=20170728" rel="stylesheet" type="text/css"/>

<div class="page-header">
    当前位置：<span class="text-primary"><?php  if(!empty($item)) { ?>编辑<?php  } else { ?>添加<?php  } ?>邀请卡 <?php  if(!empty($item)) { ?><small>(<?php  echo $item['title'];?>)</small><?php  } ?></span>
</div>

<div class="page-content">
    <div class="page-toolbar">
        <?php if(cv('invitation.add')) { ?>
        <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('invitation/add')?>"><i class="fa fa-plus"></i> 添加邀请卡</a>
        <?php  } ?>
        <a class="btn btn-default " href="<?php  echo webUrl('invitation')?>">返回列表</a>
    </div>
    <div class="diy-phone">
        <div class="phone-head"></div>
        <div class="phone-body">
            <div class="phone-title">邀请卡</div>
            <div class="phone-main">
                <img class="card-bg" src="" />
                <div id="phone"></div>
            </div>
        </div>
        <div class="phone-foot"></div>
    </div>

    <div class="diy-editor form-horizontal">
        <div class="editor-arrow"></div>
        <div class="inner" id="editor"></div>
    </div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('invitation/post/template', TEMPLATE_INCLUDEPATH)) : (include template('invitation/post/template', TEMPLATE_INCLUDEPATH));?>
</div>
<script type="text/javascript">
    myrequire(['../../plugin/invitation/static/js/web','tpl'],function(modal, tpl) {
        window.tpl = tpl;
        modal.init(<?php  echo $json;?>)
    })
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>