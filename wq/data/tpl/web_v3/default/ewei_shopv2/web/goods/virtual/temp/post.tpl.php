<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary"><?php  if(!empty($item['id'])) { ?>编辑<?php  } else { ?>添加<?php  } ?>模板 <small><?php  if(!empty($item['id'])) { ?>修改【<?php  echo $item['title'];?>】<?php  } ?></small></span>
</div>
<div class="page-content">
    <div class="page-sub-toolbar">
        <span class=''>
            <?php if(cv('goods.virtual.temp.add')) { ?>
            <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('goods/virtual/temp/add')?>">添加新模板</a>
            <?php  } ?>
        </span>
    </div>
    <?php if( ce('goods.virtual.temp' ,$item) ) { ?>
    <?php  if(!empty($_GPC['id'])) { ?>
    <div class="alert alert-danger">警告：当模板中已经添加数据后改变模板结构有可能导致无法使用！</div>
    <?php  } ?>
    <?php  } ?>


    <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data">
        <input type="hidden" name="tp_id" value="<?php  echo $item['id'];?>" />

        <div class="form-group">
            <label class="col-lg control-label" > 分类</label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('goods.virtual.temp' ,$item) ) { ?>
                <select name="cate" class="form-control">
                    <option value=""></option>
                    <?php  if(is_array($category)) { foreach($category as $c) { ?>
                    <option value="<?php  echo $c['id'];?>" <?php  if($item['cate']==$c['id']) { ?>selected<?php  } ?>><?php  echo $c['name'];?></option>
                    <?php  } } ?>
                </select>
                <?php  } else { ?>
                <?php  if(is_array($category)) { foreach($category as $c) { ?>
                <?php  if($item['cate']==$c['id']) { ?><?php  echo $c['name'];?><?php  } ?>
                <?php  } } ?>
                <?php  } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg control-label must" >模版名称</label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('goods.virtual.temp' ,$item) ) { ?>
                <input type="text" name="tp_title" class="form-control" value="<?php  echo $item['title'];?>" placeholder="模版名称，例：话费充值卡" data-rule-required='true' />
                <?php  } else { ?>
                <div class='form-control-static'><?php  echo $item['title'];?></div>
                <?php  } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label" >自定义链接</label>
            <div class="col-sm-9 col-xs-12">
                <div class="input-group">
                    <span class="input-group-addon">文字</span>
                    <input type="text" class="form-control novalidate" value="<?php echo empty($item['linktext'])? '使用地址': $item['linktext']?>" <?php if( ce("goods.virtual.temp" ,$item) ) { ?>name="tp_linktext"<?php  } else { ?>disabled<?php  } ?>>
                    <span class="input-group-addon" style="border-left: 0; border-right: 0;">链接</span>
                    <input type="text" class="form-control novalidate" value="<?php  echo $item['linkurl'];?>" id="linkurl"  <?php if( ce("goods.virtual.temp" ,$item) ) { ?>name="tp_linkurl"<?php  } else { ?>disabled<?php  } ?>>
                    <span class="input-group-addon btn btn-default" data-toggle="selectUrl" data-input="#linkurl">选择链接</span>
                </div>
                <div class="help-block">提示：文字不设置默认为"使用地址"，不选择链接则不显示</div>
            </div>
        </div>

        <div class="form-group-title">数据结构</div>

        <div class="fileds">

            <?php  $key="key";?>
            <?php  $name= $item['fields']['key'];?>
            <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods/virtual/temp/tpl', TEMPLATE_INCLUDEPATH)) : (include template('goods/virtual/temp/tpl', TEMPLATE_INCLUDEPATH));?>

            <?php  if(is_array($item['fields'])) { foreach($item['fields'] as $key => $name) { ?>
            <?php  if($key!='key') { ?>
               <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods/virtual/temp/tpl', TEMPLATE_INCLUDEPATH)) : (include template('goods/virtual/temp/tpl', TEMPLATE_INCLUDEPATH));?>
            <?php  } ?>
            <?php  } } ?>

            <div id="type-items"></div>
            <?php  if($datacount<=0) { ?>
            <?php if( ce('goods.virtual.temp' ,$item) ) { ?>
            <div class="form-group">
                <label class="col-lg control-label" ></label>
                <div class="col-sm-9 col-xs-12">
                    <a class="btn btn-default btn-add-type" href="javascript:;" onclick="addType();"><i class="fa fa-plus" title=""></i> 增加一条键</a>
                </div>
            </div>
            <?php  } ?>
            <?php  } ?>

        </div>




        <div class="form-group">
            <label class="col-lg control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('goods.virtual.temp' ,$item) ) { ?>
                <input type="submit" value="提交" class="btn btn-primary"  />

                <?php  } ?>
                <a href="<?php  echo webUrl('goods/virtual/temp')?>"  <?php if( ce('goods.virtual.temp' ,$item) ) { ?>style='margin-left:10px;'<?php  } ?>><span class="btn btn-default" style='margin-left:10px;'>返回列表</span></a>
            </div>
        </div>

    </form>
</div>
<script language='javascript'>
    var kw = 1;
    function addType() {
        $(".btn-add-type").button("loading");
        $.ajax({
            url: "<?php  echo webUrl('goods/virtual/temp/tpl')?>&kw=" + kw,
            cache: false
        }).done(function (html) {
            $(".btn-add-type").button("reset");
            $("#type-items").append(html);
        });
        kw++;
    }

    function removeType(obj) {
        $(obj).parent().remove();
    }

    $('form').submit(function () {


        var check = true;
        $(".fileds input[type=text]").each(function () {
            var val = $(this).val();
            if (!val) {
                $('form').validate('false');
                $(this).focus();
                tip.msgbox.err('不能为空');
                check = false;

                return false;
            }
        });
        if (!check) {
            $('form').attr('stop', 1);
            return false;
        }
        $('form').removeAttr('stop');
        var o = {}; // 判断重复
        $(".fileds input[mk=1]").each(function () {
            if (!(o[$(this).val()])) {
                o[$(this).val()] = true;
            } else {
                var val = $(this).val();
                $(".fileds input[mk=1]").each(function () {
                    if ($(this).val() == val) {
                        $(this).css("border-color", "#f01");
                    } else {
                        $(this).css("border-color", "#ccc");
                    }
                });
                tip.msgbox.err("红圈里的数据不能重复");

                check = false;
                return false;
            }
        });
        if (!check) {
            $('form').attr('stop', 1);
            return false;
        }
        $('form').removeAttr('stop');
        return check;
    });

</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
