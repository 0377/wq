<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">卡密设置</span></div>
<div class="page-content">

    <form action="" method="post" class="form-horizontal form-validate">
        <div class="form-group">
            <label class="col-lg control-label">下单多少分钟后</label>
            <div class="col-sm-9 col-xs-12">
                <?php if(cv('goods.virtual.set')) { ?>
                <div class="input-group fixsingle-input-group">
                    <input type="text" name="data[closeorder_virtual]" class="form-control" value="<?php  echo $data['closeorder_virtual'];?>" />
                    <div class="input-group-addon">分钟</div>
                </div>
                <span class='help-block'>订单下单未付款，n分钟后自动关闭，空为15分钟后关闭，最低为5分钟</span>
                <?php  } else { ?>
                <input type="hidden" name="data[closeorder_virtual]" value="<?php  echo $data['closeorder_virtual'];?>"/>
                <div class='form-control-static'>
                    <?php  if(empty($data['closeorder_virtual'])) { ?>9<?php  } else { ?><?php  echo $data['closeorder_virtual'];?><?php  } ?> 分钟
                </div>
                <?php  } ?>
            </div>
        </div>


        <div class="form-group">
            <label class="col-lg control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <?php if(cv('sysset.trade.edit')) { ?>
                <input type="submit" value="提交" class="btn btn-primary"  />
                <?php  } ?>
            </div>
        </div>
    </form>
</div>
<script>
    function addCategory(){
        var html ='<tr>';
        html+='<td><i class="fa fa-plus"></i></td>';
        html+='<td>';
        html+='<input type="text" class="form-control" name="catname[new]" value="">';
        html+='</td><td></td></tr>';;
        $('#tbody-items').append(html);
    }

</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

