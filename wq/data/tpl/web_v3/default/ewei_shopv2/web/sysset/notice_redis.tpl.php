<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .select2 {width: 100%;}
    .form-horizontal .form-group {margin-left: 0; margin-right: 0;}
    .table > thead > tr > th {border:none;}
    .is_advanced {display: table-block;}
    #openids_selector .input-group {width: 100%;}
    #openids2_selector .input-group {width: 100%;}
    .is_sms {display: <?php  if(!empty($opensms)) { ?>table-block<?php  } else { ?>none<?php  } ?>;}
</style>

<div class="page-header">
    当前位置：<span class="text-primary">消息提醒设置</span>
</div>

<div class="page-content">
    <div class="alert alert-warning">
        <h3>温馨提示：</h3>
        <p>当您的卖家通知人超过5人时，<span style="color:#f55">建议开启</span>消息通知列队开关，消息队列通知发送会有所延迟。</p>
    </div>

    <form action="" method="post" class="form-horizontal  form-validate" enctype="multipart/form-data" >

        <table class="table table-responsive">
            <tbody>
            <tr>
                <td>消息通知队列开关</td>
                <td style="text-align: right;" class="is_advanced">
                    <label class="notice-default">
                        <input type="hidden" name="notice_redis" value="<?php  echo intval($data['notice_redis'])?>" />
                        <input class="js-switch small checkone" data-type="tpl-advanced"  data-tag="notice_redis"  type="checkbox" value="<?php  echo intval($data['notice_redis'])?>" <?php  if(!empty($data['notice_redis'])) { ?>checked<?php  } ?>/>
                    </label>
                    <label style="display: none;">
                        <img src="../addons/ewei_shopv2/static/images/loading.gif" width="20" alt=""  onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'" />
                    </label>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form-group splitter"></div>

        <div class="form-group">
            <div class="col-sm-12 col-xs-12" >
                <?php if(cv('sysset.notice_redis.edit')) { ?>
                <input type="submit"  value="提交" class="btn btn-primary"  />
                <?php  } ?>
            </div>
        </div>
    </form>
</div>

<script>
    $(function () {
        $(".js-switch").not(".checkhi").click(function () {
            var next = $(this).next();
            if(next.hasClass('checked')){
                $(this).val("0").prev().val("1");
            }else{
                $(this).val("1").prev().val("0");
            }
        });
    })
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>     