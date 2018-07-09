<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
    <div class="page-header">
            当前位置：<span class="text-primary">砍价中</span>
            <!--<span class='label label-success' style="background-color: #18a689">-->
            <!--</span>-->

    </div>
    <div class="page-content">
        <form action="" method="post" class="form-horizontal form-search" role="form">
            <div class="page-toolbar">
                <span>
                    <?php if(cv('bargain.warehouse')) { ?>
                        <span class=''>
                            <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('bargain/warehouse');?>"><i class='fa fa-plus'></i> 添加商品</a>
                        </span>
                    <?php  } ?>
                </span>
                <div class=" col-sm-6 pull-right">
                    <div class="input-group">
                        <input type="text" class="input-sm form-control" name="search" value="" placeholder="商品名称"> <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit" style="float: left"> 搜索</button> </span>
                    </div>
                </div>
            </div>
        </form>
        <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="ewei_shopv2" />
            <input type="hidden" name="do" value="web" />
            <input type="hidden" name="r"  value="goods" />
            <input type="hidden" name="goodsfrom" value="sale" />

        </form>
        <?php  if(count($onSell)>0) { ?>
        <table class="table  table-responsive">
            <thead class="navbar-inner">
            <tr>
                <th style="width:50px;">序号</th>
                <th style="width:50px;">商品</th>
                <th  style="width:160px;">&nbsp;</th>
                <th style="width:80px;" >标价</th>
                <th style="width:80px;" >底价</th>

                <th style="width:70px;" >库存</th>
                <th style="width:80px;" >已发起</th>
                <th style="width:70px;" >距结束</th>

                <th style="width: 65px;">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php  if(is_array($onSell)) { foreach($onSell as $key => $value) { ?>
            <tr>
                <td>
                    <?php  echo $key+$psize*$page-$psize+1;?>
                </td>
                <td>
                    <img src="<?php  echo tomedia($value['thumb']);?>" style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"   onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'"/>
                </td>
                <td class='full' style="overflow-x: hidden">
                    <br/>
                    <a title="<?php  echo $value['title'];?>"><?php  echo $value['title'];?></a>
                </td>
                <td>
                    <a><?php  echo $value['marketprice'];?></a>
                </td>

                <td>
                    <a><?php  echo $value['end_price'];?></a>
                </td>
                <td>
                    <a><?php  if($value['total']<10) { ?><font style="color: red"><?php  } ?><?php  echo $value['total'];?></font></a>
                </td>
                <td><a><?php  echo $value['act_times'];?></a></td>
                <td><a title="<?php  $day = intval((strtotime($value['end_time'])-time())/3600/24);$hour = intval((strtotime($value['end_time'])-time()-$day*3600*24)/3600);?>距离活动结束剩余:<?php  if($day > 0) { ?><?php  echo $day;?>天<?php  } else { ?><?php  echo $hour;?>小时<?php  } ?>"><?php  if($day > 0) { ?><?php  echo $day;?>天<?php  } else { ?><font style="color: red"><?php  echo $hour;?>小时</font><?php  } ?></a></td>

                <td  style="overflow:visible;position:relative">
                    <?php if(cv('bargain.react')) { ?>
                    <a  class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('bargain/react',array('actid'=>$value['id']));?>" title="编辑">
                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑">
                             <i class="icow icow-bianji2"></i>
                         </span>
                    </a>
                    <?php  } ?>
                    <?php if(cv('bargain.huishou')) { ?>
                        <a  class='btn btn-default btn-sm btn-op btn-operation' data-toggle="ajaxRemove" href="<?php  echo webUrl('bargain/huishou',array('id'=>$value['id']));?>" data-confirm='确认删除此商品？'>
                             <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                                <i class='icow icow-shanchu1'></i>
                           </span>
                        </a>
                    <?php  } ?>
                    <a href="javascript:;" class='btn btn-default btn-sm js-clip btn-op btn-operation' data-url="<?php  echo mobileUrl('bargain/detail',array('id'=>$value['id']),true);?>">
                         <span data-toggle="tooltip" data-placement="top"  data-original-title="复制链接">
                               <i class='icow icow-lianjie2'></i>
                           </span>
                    </a>
                </td>
            </tr>
            <?php  } } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9" class="text-right"><?php  echo $pager;?></td>
                </tr>
            </tfoot>
        </table>
        <?php  } else { ?>
        <div class="panel panel-default">
            <div class="panel-body empty-data">暂时没有任何商品!</div>
        </div>
        <?php  } ?>
    </div>
    <script language="javascript">myrequire(['web/init'],function(){
        if($('.form-validate').length<=0) {  $('#page-loading').remove(); }
    });</script>
    <div id="page-loading" style="position: fixed;width:100%;height:100%;background:rgba(255,255,255,0.8);left:0;top:0;z-index:9999">
        <div class="sk-spinner sk-spinner-double-bounce" style="position:fixed;top:50%;left:50%;width:40px;height:40px;margin-left:-20px;margin-top:-20px;">
            <div class="sk-double-bounce1"></div>
            <div class="sk-double-bounce2"></div>
        </div>
    </div>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>