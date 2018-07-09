<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">虚拟卡密管理</span></div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="goods.virtual.temp" />

        <div class="page-toolbar">
            <div class="col-sm-4">
                <?php if(cv('goods.virtual.temp.add')) { ?>
                <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('goods/virtual/temp/add')?>"><i class='fa fa-plus'></i> 添加模板</a>
                <?php  } ?>
                <?php if(cv('goods.virtual.recycled')) { ?>&nbsp;
                <a class='btn btn-default btn-sm' href="<?php  echo webUrl('goods/virtual/recycled')?>"><i class='fa fa-trash-o'></i> 回收站</a>
                <?php  } ?>
            </div>
            <div class="col-sm-6 pull-right">
                <div class="input-group">
                    <span class="input-group-btn">
                        <div class="input-group" style="width: 100%;">
                            <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入模板名称进行搜索"> <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"> 搜索</button> </span>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </form>
    <?php  if(count($items)>0) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="page-table-header">
                <input type="checkbox">
                <div class="btn-group">
                    <?php if(cv('goods.virtual.temp.delete')) { ?>
                    <button class="btn btn-default dropdown-toggle btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要放入回收站?" data-href="<?php  echo webUrl('goods/virtual/temp/recycled')?>">
                        <i class="icow icow icow-shanchu1"></i> 回收站
                    </button>
                    <?php  } ?>
                </div>
            </div>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th style="width:25px;"></th>
                        <th >模版名称</th>
                        <th>已使用/总共数据</th>
                        <th style="width:120px;text-align: center;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  if(is_array($items)) { foreach($items as $item) { ?>
                    <tr>

                        <td>
                            <input type='checkbox'   value="<?php  echo $item['id'];?>"/>
                        </td>
                        <td><label class='label label-primary'><?php  echo $category[$item['cate']]['name']?></label> <?php  echo $item['title'];?></td>
                        <td>
                            <?php if(cv('goods.virtual.data.view')) { ?>
                            <a href="<?php  echo webUrl('goods/virtual/data', array('typeid'=>$item['id']))?>" title="点击查看/编辑"><?php  echo $item['usedata'];?> / <?php  echo $item['alldata'];?> 详细</a>
                            <?php  } else { ?>
                            <?php  echo $item['usedata'];?> / <?php  echo $item['alldata'];?>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php if(cv('goods.virtual.temp.edit|goods.virtual.temp.view')) { ?>
                                <a class='btn btn-default btn-sm btn-operation btn-op' href="<?php  echo webUrl('goods/virtual/temp/edit', array('id' => $item['id']))?>">
                                     <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php if(cv('goods.virtual.temp.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                                         <i class="icow icow-bianji2"></i>
                                    </span>
                                </a>
                            <?php  } ?>
                            <?php if(cv('goods.virtual.temp.view')) { ?>
                                <a class='btn btn-default  btn-sm btn-operation btn-op' href="<?php  echo webUrl('goods/virtual/data', array('typeid' => $item['id']))?>" >
                                     <span data-toggle="tooltip" data-placement="top" title="" data-original-title="数据">
                                         <i class="icow icow-digital"></i>
                                    </span>
                                </a>
                            <?php  } ?>
                            <?php if(cv('goods.virtual.data.add')) { ?>
                                <a class='btn btn-default  btn-sm btn-operation btn-op' href="<?php  echo webUrl('goods/virtual/data/add', array('typeid' => $item['id']))?>">
                                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="添加数据">
                                         <i class="icow icow-tianjia"></i>
                                    </span>
                                </a>
                            <?php  } ?>
                            <?php if(cv('goods.virtual.temp.delete')) { ?>
                                <a class='btn btn-default  btn-sm btn-operation btn-op'  data-toggle='ajaxRemove' href="<?php  echo webUrl('goods/virtual/temp/recycled', array('id' => $item['id']))?>" data-confirm="确认放入回收站吗" title='放入回收站'>
                                 <span data-toggle="tooltip" data-placement="top" title="" data-original-title="放入回收站">
                                         <i class="icow icow-shanchu1"></i>
                                    </span>
                                </a>
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    <tfoot>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox">
                            <div class="btn-group">
                                <?php if(cv('goods.virtual.temp.delete')) { ?>
                                <button class="btn btn-default dropdown-toggle btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要放入回收站?" data-href="<?php  echo webUrl('goods/virtual/temp/recycled')?>">
                                    <i class="icow icow icow-shanchu1"></i> 回收站
                                </button>
                                <?php  } ?>
                            </div>
                        </td>
                        <td colspan="2" style="text-align: right">
                            <?php  echo $pager;?>
                        </td>
                    </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
    </div>
    <?php  } else { ?>
    <div class="panel panel-default">
        <div class="panel-body empty-data">暂时没有任何模板!</div>
    </div>
    <?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
