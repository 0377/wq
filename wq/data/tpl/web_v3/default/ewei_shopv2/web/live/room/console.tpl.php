<?php defined('IN_IA') or exit('Access Denied');?><?php  $no_left=true?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/console.css">
<style>
    #topbar .navbar, .main-wrapper, .page-content{
        width:100% !important;

    }
</style>
<div class="page-header">
    当前位置：<span class="text-primary">直播间控制台 <small>(<?php  echo $item['title'];?>)</small></span>
</div>

<div class="page-content">
    <div class="page-toolbar">
        <a href="<?php  echo webUrl('live/room')?>" class="btn btn-default btn-sm">返回直播间列表</a>
    </div>
    <div class="alert alert-danger" style="display: none;">
        <b>错误：</b>通讯服务器连接失败(通讯服务未启动或已停止) <a href="">点击查看</a>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <div class="panel-video">
                <div class="online stop"><span class="status">未开播</span> | 显示人数: <span id="showOnline">0</span></div>
                <iframe style="border: 0; overflow: hidden; width: 100%; height: 100%;" src="<?php  echo $url;?>"></iframe>
            </div>

            <div class="panel panel-default panel-push">
                <div class="panel-heading noborder">直播状态设置
                    <span class="pull-right">
                        <a class="btn btn-loading disabled">加载中...</a>
                        <a class="btn btn-play"><i class="fa fa-play"></i> 开启直播</a>
                        <a class="btn btn-pause"><i class="fa fa-pause"></i> 暂停直播</a>
                        <a class="btn btn-stop"><i class="fa fa-stop"></i> 关闭直播</a>
                    </span>
                </div>
            </div>
        </div>


        <div class="col-sm-7" style="padding-left: 0;">
            <div class="panel panel-default">
                <div class="panel-heading nopadding">
                    <span class="panel-tab-small panel-chat-tab">
                        <a data-tab="chat" class="active">互动列表</a>
                        <a data-tab="visitor">访客列表(<span id="online">0</span>)</a>
                        <a data-tab="bannedlist">禁言列表(<span id="banned">0</span>)</a>
                        <a data-tab="setting">互动设置</a>
                    </span>
                    <span class="pull-right">
                        <a class="btn-bannedall">全员禁言</a>
                    </span>
                </div>
                <div class="panel-body panel-chat" data-tab="chat" style="display: block">
                    <?php  if(is_array($records)) { foreach($records as $record) { ?>
                        <p data-msgid="<?php  echo $record['id'];?>">
                            <?php  if(!empty($record['status'])) { ?>
                                <span class="tip"><span class="text"><?php  echo $record['text'];?></span></span>
                            <?php  } else { ?>
                                <span class="time" title="2017-06-06">[<?php  echo date('H:i:s', $record['sendtime'])?>] </span>
                                <?php  if($record['mid']==$uid) { ?>
                                    <span class="nickname self" title="点击修改昵称" data-nickname="<?php  echo $record['nickname'];?>" data-uid="<?php  echo $record['openid'];?>"><?php  echo $record['nickname'];?>(我)：</span>
                                <?php  } else { ?>
                                    <span class="nickname" title="点击@Ta" data-nickname="<?php  echo $record['nickname'];?>" data-uid="<?php  echo $record['mid'];?>"><?php  echo $record['nickname'];?>：</span>
                                <?php  } ?>

                                <?php  if($record['type']=='goods') { ?>
                                    <a class="goods" href="<?php  echo webUrl('goods/edit', array('id'=>$record['goodsid']))?>" target="_blank">
                                        <span class="thumb"><img src="<?php  echo tomedia($record['goodsthumb'])?>"/></span>
                                        <span class="info">
                                            <span class="title"><?php  echo $record['goodstitle'];?></span>
                                            <span class="price">￥<?php  echo $record['goodsprice'];?></span>
                                        </span>
                                    </a>
                                <?php  } else { ?>
                                    <?php  echo $record['text'];?>
                                    <?php  if($record['type']=='text' || $record['type']=='image') { ?>
                                        <?php  if($record['mid'] == $uid) { ?>
                                            <a class="btn-repeal" title="删除此条消息"> 撤回</a>
                                        <?php  } else { ?>
                                            <a class="btn-delete" title="删除此条消息"> 删除</a>
                                            <a class="btn-banned" title="<?php  if(empty($record['banned'])) { ?>禁止<?php  } else { ?>允许<?php  } ?>此用户发言" <?php  if(!empty($record['banned'])) { ?>data-banned="1"<?php  } ?>> <?php  if(empty($record['banned'])) { ?>禁言<?php  } else { ?>解除禁言<?php  } ?></a>
                                        <?php  } ?>
                                    <?php  } ?>
                                <?php  } ?>
                            <?php  } ?>
                        </p>
                    <?php  } } ?>
                </div>
                <div class="panel-body panel-chat" data-tab="visitor">
                    <span class="empty-data">没有任何数据~</span>
                    <div class="inner"></div>
                </div>
                <div class="panel-body panel-chat" data-tab="bannedlist">
                    <span class="empty-data">没有任何数据~</span>
                    <div class="inner"></div>
                </div>
                <div class="panel-body panel-chat" data-tab="setting">
                    <div class="form-horizontal" style="overflow: hidden;padding-top: 15px;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label must">管理员昵称</label>
                            <div class="col-sm-3 col-xs-12">
                                <input type="text" class="form-control input-sm managenick" value="<?php  echo $nickname;?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户@功能</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" class="canat" name="canat" value="1"> 允许</label>
                                <label class="radio-inline"><input type="radio" class="canat" name="canat" value="0"> 不允许</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户撤回消息</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" class="canrepeal" name="canrepeal" value="1"> 允许</label>
                                <label class="radio-inline"><input type="radio" class="canrepeal" name="canrepeal" value="0"> 不允许</label>
                            </div>
                        </div>
                        <div class="form-group splitter"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">虚拟人数</label>
                            <div class="col-sm-3 col-xs-12">
                                <span class="input-group">
                                    <input type="tel" class="form-control input-sm virtual" />
                                    <span class="input-group-addon">人</span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">进入1人增加</label>
                            <div class="col-sm-3 col-xs-12">
                                <span class="input-group">
                                    <input type="tel" class="form-control input-sm virtualadd" />
                                    <span class="input-group-addon">人</span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="btn btn-sm btn-primary btn-setting">设置并推送</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="emoji-list">
                        <?php  if(is_array($emojiList)) { foreach($emojiList as $i => $emojiItem) { ?>
                            <?php  if(!empty($emojiItem)) { ?>
                                <div class="item" title="<?php  echo $emojiItem;?>" data-index="<?php  echo $i;?>"></div>
                            <?php  } ?>
                        <?php  } } ?>
                    </div>
                    <div class="input">
                        <div class="emoji btn-image" title="发送图片">
                            <i class="icon icon-pic"></i>
                        </div>
                        <div class="emoji btn-emoji" title="选择表情">
                            <i class="icon icon-emoji"></i>
                        </div>
                        <div class="emoji btn-ats">
                            <div class="item big">
                                <div class="text">@0个用户</div>
                                <div class="at-remove" title="点击移除">×</div>
                            </div>
                            <div class="at-list"></div>
                        </div>
                        <input placeholder="请输入文本内容..." id="input" />
                    </div>
                    <div class="btn btn-primary disabled" id="btn-send">发送</div>
                </div>
                </div>
        </div>

        <div class="col-sm-12">
            <div class="panel panel-default panel-push">
                <div class="panel-heading">推送记录(<span id="pushcount"><?php  echo $push_count;?></span>)
                <span class="pull-right">
                    <a class="label label-danger btn-push" data-action="redpack" data-toggle="tooltip" data-placement="top" data-original-title="点击推送红包"><i class="fa fa-plus"></i> 推送红包</a>
                    <a class="label label-warning btn-push" data-action="coupon" data-toggle="tooltip" data-placement="top" data-original-title="点击推送优惠券"><i class="fa fa-plus"></i> 推送优惠券</a>
                    <a class="label label-primary btn-push" data-action="link" data-toggle="tooltip" data-placement="top" data-original-title="点击推送商品"><i class="fa fa-plus"></i> 推送商品</a>
                </span>
                </div>
                <div class="panel-body">
                    <p class="empty-data" <?php  if($push_count>0) { ?>style="display: none;"<?php  } ?>>暂无推送记录</p>

                    <table class="table" <?php  if($push_count<1) { ?>style="display: none"<?php  } ?>>
                        <thead>
                            <th style="width: 90px; text-align: center;">推送类型</th>
                            <th>标题</th>
                            <th style="width: 180px">推送时间</th>
                            <th style="width: 120px;">推送数量(个/张)</th>
                            <th style="width: 120px">剩余数量(个/张)</th>
                            <th style="width: 100px">推送金额(元)</th>
                            <th style="width: 100px">剩余金额(元)</th>
                            <th style="width: 100px;">其他</th>
                            <th style="width: 100px;"></th>
                        </thead>
                    </table>
                    <div class="max350">
                        <table class="table height0" <?php  if($push_count<1) { ?>style="display: none"<?php  } ?>>
                            <thead>
                                <th style="width: 90px; text-align: center;"></th>
                                <th></th>
                                <th style="width: 180px"></th>
                                <th style="width: 120px;"></th>
                                <th style="width: 120px"></th>
                                <th style="width: 100px"></th>
                                <th style="width: 100px"></th>
                                <th style="width: 100px;"></th>
                                <th style="width: 100px;"></th>
                            </thead>
                            <tbody>
                                <?php  if(is_array($push_records)) { foreach($push_records as $push_index => $push_item) { ?>
                                <tr data-pushid="<?php  echo $push_item['time'];?>">
                                    <td>
                                        <?php  if($push_item['type']<2) { ?>
                                            <label class="label label-danger">红包(余额)</label>
                                        <?php  } else if($push_item['type']==2) { ?>
                                            <label class="label label-warning">优惠券</label>
                                        <?php  } ?>
                                    </td>
                                    <td><?php  echo $push_item['title'];?></td>
                                    <td><?php  echo date('Y-m-d H:i:s', $push_item['time'])?></td>
                                    <td><?php  echo $push_item['total'];?></td>
                                    <td class="total_remain"><?php  echo $push_item['total_remain'];?></td>
                                    <td>
                                        <?php  if($push_item['type']<2) { ?>
                                            <?php  echo $push_item['money'];?>
                                        <?php  } else if($push_item['type']==2) { ?>-<?php  } ?>
                                    </td>
                                    <td class="money_remain">
                                        <?php  if($push_item['type']<2) { ?>
                                            <?php  echo $push_item['money_remain'];?>
                                        <?php  } else if($push_item['type']==2) { ?>-<?php  } ?>
                                    </td>
                                    <td>
                                        <?php  if($push_item['type']<2) { ?>
                                        <?php echo $push_item['type']==1? '拼手气红包': '普通红包'?>
                                        <?php  } else if($push_item['type']==2) { ?>-<?php  } ?>
                                    </td>
                                    <td>
                                        <a data-toggle="ajaxModal" href="<?php  echo webUrl('live/room/console_record', array('pushid'=>$push_item['time'], 'type'=>$push_item['type'], 'id'=>$id))?>">领取记录</a>
                                    </td>
                                </tr>
                                <?php  } } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pushRedpackModal" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title">高级推送 - 红包(余额)</h4>
                    </div>
                    <div class="modal-body form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-2 control-label must">红包类型</div>
                            <div class="col-sm-10">
                                <label class="radio-inline"><input type="radio" class="repacktype" name="repacktype" value="0" checked> 普通红包</label>
                                <label class="radio-inline"><input type="radio" class="repacktype" name="repacktype" value="1"> 拼手气红包</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label must text-danger"><span class="repacktype">单个</span>金额</div>
                            <div class="col-sm-5">
                                <span class="input-group input-group-sm">
                                    <input class="form-control" type="number" value="0" id="redpackmoney" />
                                    <span class="input-group-addon">元</span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label must">红包个数</div>
                            <div class="col-sm-5">
                                <span class="input-group input-group-sm">
                                    <input class="form-control" type="number" value="0" id="redpacktotal" />
                                    <span class="input-group-addon">个</span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">红包标题</div>
                            <div class="col-sm-5">
                                <input class="form-control" type="text" placeholder="红包来袭，手慢无！" id="redpacktitle" maxlength="15" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-10">
                                <label class="checkbox-inline text-danger"><input type="checkbox" value="1" id="redpackconfirm"> 我确认将 <b>红包</b> 即时推送给用户</label>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn btn-primary submit-push" data-action="redpack">推送</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pushCouponModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title">高级推送 - 优惠券</h4>
                    </div>
                    <div class="modal-body form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-2 control-label must">优惠券</div>
                            <div class="col-sm-10">
                                <?php  echo tpl_selector('couponid',array(
                                    'readonly'=>true,
                                        'autosearch'=>1,
                                        'type'=>'image',
                                        'url'=>webUrl('sale/coupon/query', array('live'=>1)),
                                        'text'=>'couponname',
                                        'items'=>$coupon,
                                        'buttontext'=>'选择优惠券',
                                        'placeholder'=>'请选择优惠券',
                                        'callback'=>'callbackCoupon'
                                ))
                                ?>
                                <input type="hidden" id="coupon_value_text" />
                                <input type="hidden" id="coupon_value_total" />
                                <input type="hidden" id="coupon_uselimit" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label must">设置总数</div>
                            <div class="col-sm-4">
                                <span class="input-group input-group-sm">
                                    <input class="form-control" type="number" value="0" id="coupontotal" />
                                    <span class="input-group-addon">张</span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-10">
                                <label class="checkbox-inline text-danger"><input type="checkbox" name="confirm" value="1" id="couponconfirm"> 我确认将 <b>优惠券</b> 即时推送给用户</label>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn btn-primary submit-push" data-action="coupon">推送</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    var path = '../../plugin/live/static/js/console';
    myrequire([path], function (modal) {
        modal.init({wsConfig: <?php  echo $wsConfig;?>});
    });

    function callbackGoods(data) {
        myrequire([path], function(modal) {
            modal.callbackGoods(data);
        });
    }
    function callbackCoupon(data) {
        myrequire([path], function(modal) {
            modal.callbackCoupon(data);
        });
    }
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>