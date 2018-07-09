<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary">邀请卡管理 </span>
</div>
<div class="page-content">
<form action="./index.php" method="get" class="form-horizontal" role="form">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r"  value="invitation" />
    <div class="page-toolbar">
        <div class="col-sm-4">
            <?php if(cv('invitation.add')) { ?>
                <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('invitation/add')?>"><i class="fa fa-plus"></i> 添加邀请卡</a>
            <?php  } ?>
        </div>
        <div class="col-sm-6 pull-right">
            <div class="input-group">
                <div class="input-group-select">
                    <select name="type" class='form-control'>
                        <option value="" <?php  if($_GPC['type'] == '') { ?> selected<?php  } ?>>类型</option>
                        <option value="0" <?php  if($_GPC['type'] == '0') { ?> selected<?php  } ?>>直播</option>
                    </select>
                </div>
                <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> <span class="input-group-btn">
                <button class="btn btn-primary" type="submit"> 搜索</button> </span>
            </div>
        </div>
    </div>
</form>
<form action="" method="post" >
   <?php  if(count($list)>0) { ?>
    <div class="page-table-header">
        <input type="checkbox">
        <div class="btn-group">
            <?php if(cv('invitation.delete')) { ?>
            <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('invitation/delete')?>">
                <i class='icow icow-shanchu1'></i> 删除
            </button>
            <?php  } ?>
        </div>
    </div>
    <table class="table table-hover table-responsive">
        <thead>
            <tr>
                <th style="width:25px;"></th>
                <th>名称</th>
                <th style='width:100px; text-align: center;'>类型</th>
                <th style='width:80px;'>扫描数</th>
                <th style='width:80px;'>关注数</th>
                <th style='width:80px;text-align: center;'>状态</th>
                <th style="width: 95px;">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php  if(is_array($list)) { foreach($list as $row) { ?>
            <tr>
                <td>
                    <input type='checkbox' value="<?php  echo $row['id'];?>"/>
                </td>
                <td><?php  echo $row['title'];?></td>
                <td style="text-align: center;">
                    <?php  if($row['type']==0) { ?>
                    <label class='label label-default'>直播间</label>
                    <?php  } ?>
                </td>
                <td><?php  echo $row['scan'];?></td>
                <td><?php  echo $row['follow'];?></td>
                <td style="text-align: center;">
                    <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
                    <?php if( ce('leve.room' ,$item) ) { ?>
                    data-toggle='ajaxSwitch'
                    data-switch-value='<?php  echo $row['status'];?>'
                    data-switch-value0='0|禁用|label label-default|<?php  echo webUrl('invitation/property',array('type'=>'status', 'value'=>1,'id'=>$row['id']))?>'
                    data-switch-value1='1|启用|label label-primary|<?php  echo webUrl('invitation/property',array('type'=>'status', 'value'=>0,'id'=>$row['id']))?>'
                    <?php  } ?>><?php  if($row['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></span>
                </td>
                <td>
                    <?php if(cv('invitation.log')) { ?>
                        <a class='btn btn-default  btn-sm btn-op btn-operation' href="<?php  echo webUrl('invitation/log', array('id' => $row['id']))?>">
                              <span data-toggle="tooltip" data-placement="top" title="" data-original-title="查看扫描记录">
                                    <i class="icow icow-chakan"></i>
                             </span>
                        </a>
                    <?php  } ?>
                    <?php if(cv('invitation.edit|invitation.view')) { ?>
                        <a class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('invitation/edit', array('id' => $row['id']))?>">
                             <span data-toggle="tooltip" data-placement="top" title="" data-original-title=" <?php if(cv('invitation.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                                        <?php if(cv('invitation.edit')) { ?>
                                        <i class="icow icow-bianji2"></i>
                                        <?php  } else { ?>
                                        <i class="icow icow-chakan-copy"></i>
                                        <?php  } ?>
                                     </span>
                        </a>
                    <?php  } ?>
                    <?php if(cv('invitation.delete')) { ?>
                        <a class='btn btn-default btn-sm btn-op btn-operation' data-toggle='ajaxRemove'  href="<?php  echo webUrl('invitation/delete', array('id' => $row['id']))?>" data-confirm="确认删除此邀请卡吗？<br>删除后扫描记录将一并删除">
                            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                                <i class="icow icow-shanchu1"></i>
                             </span>
                        </a>
                    <?php  } ?>
                </td>
            </tr>
            <?php  } } ?>
        </tbody>
        <tfooter>
            <tr>
                <td><input type="checkbox"></td>
                <td>
                    <div class="btn-group">
                        <?php if(cv('invitation.delete')) { ?>
                        <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('invitation/delete')?>">
                            <i class='icow icow-shanchu1'></i> 删除
                        </button>
                        <?php  } ?>
                    </div>
                </td>
                <td colspan="5" class="text-right">
                    <?php  echo $pager;?>
                </td>
            </tr>
        </tfooter>
    </table>
    <?php  } else { ?>
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                 暂时没有任何邀请卡!
            </div>
        </div>
    <?php  } ?>
</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
