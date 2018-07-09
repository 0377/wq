<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label must">消息内容</label>
    <div class="col-sm-9" style="padding-bottom: 15px;">
        <?php if( ce('app.tmessage' ,$item) ) { ?>
        <div class="input-group">
            <span class="input-group-addon">键名</span>
            <input type="text" name="tpl_key[]" class="form-control" value="<?php  echo $row['key'];?>" placeholder="例如：购买时间" data-rule-required='true'/>
            <span class="input-group-addon" style="padding: 0;">
                <input type="color" name="tpl_color[]" value="<?php  echo $row['color'];?>" style="width:50px; height:28px; border: 0; padding: 0;">
            </span>
            <span class="input-group-addon" style="padding: 0 6px;">
                <label class="checkbox-inline" style="padding-top: 0; margin-right: 0;">
                    <input type="checkbox" name="tpl_bigkey" value="0" style="margin-top: -8px" <?php  if($item['emphasis_keyword']==$index && empty($new)) { ?>checked<?php  } ?>> 放大显示
                </label>
            </span>
        </div>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $row['key'];?></div>
        <?php  } ?>
    </div>

    <label class="col-lg control-label"></label>
    <div class="col-sm-9 ">
        <?php if( ce('app.tmessage' ,$list) ) { ?>
        <div class="input-group">
            <span class="input-group-addon">键值</span>
            <input type="text" name="tpl_value[]" class="form-control" value="<?php  echo $row['value'];?>" placeholder="例如：[购买时间]，请在右侧选择" data-rule-required='true'/>
            <?php  if($index>0 || $new==1) { ?>
            <span class="input-group-addon btn btn-default btn-remove"><i class="fa fa-trash"></i> 删除此条</span>
            <?php  } ?>
        </div>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $row['value'];?></div>
        <?php  } ?>
    </div>
</div>