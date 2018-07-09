<?php defined('IN_IA') or exit('Access Denied');?>                <div class="form-group">
                    <label class="col-lg control-label must"><?php  if($key=='key') { ?>主键(不可删除)<?php  } else { ?>自定义键名<?php  } ?>&nbsp;</label>
                    <div class="col-sm-1">
                        <input type="text" name="tp_kw[]" class="form-control" value="<?php  echo $key;?>" placeholder="英文键值" <?php  if($key=='key') { ?>mk="1" readonly<?php  } ?> />
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="tp_name[]" class="form-control" value="<?php  echo $name;?>" placeholder="名称，如：激活码" <?php  if($key=='key') { ?> style="width:522px;"<?php  } ?> />
                    </div>
                    <?php  if($datacount<=0) { ?>
                     <?php  if($key!='key') { ?><a class="btn btn-default" href='javascript:;' onclick='removeType(this)'><i class='fa fa-remove'></i> 删除</a><?php  } ?>
                    <?php  } ?>
                </div>

    