<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">
    当前位置：
    <span class="text-primary"><?php  if(empty($item)) { ?>添加<?php  } else { ?>编辑<?php  } ?>模板消息</span>
</div>

<div class="page-content">

    <form <?php if( ce('app.tmessage' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate" enctype="multipart/form-data">

    <div class="row">
        <div class="col-sm-8" style="padding-right: 50px;">

            <div class="alert alert-info">
                <p>说明：</p>
                <p>“键名”填写公众平台模板消息中的变量，例如：{{keyword1.DATA}}</p>
                <p>“键值”请在右侧选择，例如：[支付时间]</p>
                <p>“放大显示”每条消息允许一个字段放大显示</p>
            </div>

            <div class="form-group">
                <label class="col-lg control-label must" >模板名称</label>
                <div class="col-sm-9 ">
                    <?php if( ce('app.tmessage' ,$item) ) { ?>
                    <input type="text" name="name" class="form-control" value="<?php  echo $item['name'];?>" placeholder="例如：支付提醒" data-rule-required='true' />
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['templateid'];?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label must" >模板消息ID</label>
                <div class="col-sm-9 ">
                    <?php if( ce('app.tmessage' ,$item) ) { ?>
                    <input type="text" name="templateid" class="form-control" value="<?php  echo $item['templateid'];?>" placeholder="例如：P8MxRKmW7wdejmZl14-swiGmsJVrFJiWYM7zKSPXq4I" data-rule-required='true' />
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['templateid'];?></div>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group splitter"></div>

            <div id="items">
                <?php  if(is_array($datas)) { foreach($datas as $index => $row) { ?>
                    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('app/tmessage/tpl', TEMPLATE_INCLUDEPATH)) : (include template('app/tmessage/tpl', TEMPLATE_INCLUDEPATH));?>
                <?php  } } ?>
            </div>
            <div class="form-group">
                <label class="col-lg control-label"></label>
                <div class="col-sm-9 ">
                    <div class="btn btn-default" id="addItem"><i class="fa fa-plus"></i> 添加一条消息内容</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">模板状态</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('app.tmessage' ,$item) ) { ?>
                    <label class='radio-inline'><input type='radio' name='status' value=1' <?php  if($item['status']==1) { ?>checked<?php  } ?> /> 启用</label>
                    <label class='radio-inline'><input type='radio' name='status' value=0' <?php  if($item['status']==0) { ?>checked<?php  } ?> /> 禁用</label>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($item['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <div class="help-block">
                        <button class="btn btn-primary" type="submit">提交</button>
                        <a class="btn btn-default" href="<?php  echo webUrl('app/tmessage')?>">返回列表</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4" style="max-width:350px;">
            <div class="panel panel-default">
                <div class="panel-heading" style="display: none">
                    <select class="form-control" onclick="$('.tm').hide();$('.tm-' + $(this).val()).show()">
                        <option value="">选择模板变量类型</option>
                        <option value="order">订单类</option>
                    </select>
                </div>
                <div class="panel-heading tm tm-order">订单信息</div>
                <div class="panel-body tm tm-order">
                    <a href='JavaScript:' class="btn btn-default  btn-sm">商城名称</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">粉丝昵称</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">订单号</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">订单金额</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">运费</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">商品详情</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">单品详情</a>(单品商家下单通知变量)<br>
                    <a href='JavaScript:' class="btn btn-default btn-sm">快递公司</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">快递单号</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">购买者姓名</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">购买者电话</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">收货地址</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">下单时间</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">支付时间</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">发货时间</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">收货时间</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">门店</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">门店地址</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">门店联系人</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">门店营业时间</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">虚拟物品自动发货内容</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">虚拟卡密自动发货内容</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">自提码</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">备注信息</a>
                </div>
                <div class="panel-heading tm tm-order" style="display: none";>售后相关</div>
                <div class="panel-body tm tm-order" style="display: none";>
                    <a href='JavaScript:' class="btn btn-default btn-sm">售后类型</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">申请金额</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">退款金额</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">退货地址</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">换货快递公司</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">换货快递单号</a>
                </div>
                <div class="panel-heading tm tm-order" style="display: none";>订单状态更新</div>
                <div class="panel-body tm tm-order" style="display: none";>
                    <a href='JavaScript:' class="btn btn-default btn-sm">粉丝昵称</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">订单编号</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">原收货地址</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">新收货地址</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">订单原价格</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">订单新价格</a>
                    <a href='JavaScript:' class="btn btn-default btn-sm">修改时间</a>
                </div>
            </div>
        </div>
    </div>
    <style> .form-horizontal .form-group{margin-right: -50px;}  .col-sm-9{padding-right: 0;}  .tm .btn { margin-bottom:5px;}</style>

    <script>
        $(function () {
            require(['jquery.caret'],function(){
                var jiaodian;
                $(document).on('focus', 'input,textarea',function () {
                    jiaodian = this;
                });
                $("a[href='JavaScript:']").click(function () {
                    if (jiaodian) {
                        $(jiaodian).insertAtCaret("["+this.innerText+"]" );
                    }
                });
            });
            $('#addItem').click(function () {
                var $this = $(this);
                $this.button("loading");
                $.ajax({
                    url: "<?php  echo webUrl('app/tmessage/tpl')?>&new=1",
                    cache: false
                }).done(function (html) {
                    $this.button("reset");
                    $("#items").append(html);
                });
            });
            $(document).on('click', '.btn-remove', function () {
                var $this = $(this);
                tip.confirm('确认删除此条吗？', function () {
                    $this.closest('.form-group').remove();
                })
            });
            $(document).on('click', 'input[name=tpl_bigkey]', function (e) {
                var checked = $(this).is(':checked');
                if(checked){
                    $('input[name=tpl_bigkey]').prop('checked', false);
                    $(this).prop('checked', true);
                    $(this).val($(this).closest('.form-group').index())
                }
            });
        })
    </script>

    </form>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>