<?php defined('IN_IA') or exit('Access Denied');?><!-- 商品信息块 -->
<div class="fui-cell-group fui-detail-group" style="margin-top: <?php  echo $diyitem['style']['margintop'];?>px; margin-bottom: <?php  echo $diyitem['style']['marginbottom'];?>px; background: <?php  echo $diyitem['style']['background'];?>;" >
    <div class="fui-cell">
        <div class="fui-cell-text name" style="color: <?php  echo $diyitem['style']['titlecolor'];?>;"><?php  if($goods['ispresell']==1) { ?><i class="fui-tag fui-tag-danger" style="vertical-align: bottom;">预</i> <?php  } ?><?php  echo $goods['title'];?></div>
        <?php  if(empty($diyitem['params']['hideshare'])) { ?>
            <a class="fui-cell-remark share"  <?php  if(!empty($diyitem['params']['share_link'])) { ?>href="<?php  echo $diyitem['params']['share_link'];?>" data-nocache="true"<?php  } else if(!empty($goods['sharebtn']) && $member['isagent']==1 && $member['status']==1) { ?> href="<?php  echo mobileUrl('commission/qrcode', array('goodsid'=>$goods['id']))?>" <?php  } else { ?> id='btn-share' <?php  } ?>>
            <i class="icon <?php echo empty($diyitem['params']['share_icon'])?'icon-share':$diyitem['params']['share_icon']?>"></i><p><?php echo !empty($diyitem['params']['share'])?$diyitem['params']['share']:"分享"?></p>
            </a>
        <?php  } ?>
    </div>
    <?php  if(!empty($goods['subtitle'])) { ?>
    <div class="fui-cell goods-subtitle">
        <span class='text-danger' style="color: <?php  echo $diyitem['style']['subtitlecolor'];?>;"><?php  echo $goods['subtitle'];?></span>
    </div>
    <?php  } ?>

    <?php  if($goods['type']==4) { ?>
    <?php  if($goods['cansee']>0 &&  $goods['seecommission']>0 ) { ?>
    <div style="height: 1.3rem;margin:0.2rem 0.6rem">
        <div class="detail-Commission flex" style="padding-bottom: 0;display: inline-flex;float: right;">
            <div class="text"> <?php echo empty($goods['seetitle'])?'预计最高佣金':$goods['seetitle']?></div>
            <div class="num flex1">￥<?php  echo $goods['seecommission'];?></div>
        </div>
    </div>
    <?php  } ?>
        <!--批发价格-->
        <div class="fui-cell goods-bulk">
            <div class="fui-cell-text  flex">
                <?php  if($goods['intervalfloor']>0) { ?>
                <span>
                        <p class="price" style="color: <?php  echo $diyitem['style']['pricecolor'];?>;"><small>￥</small><?php  echo $goods['intervalprice1'];?></p>
                        <p><?php  echo $goods['intervalnum1'];?> <?php  if($goods['intervalfloor']>1) { ?>-<?php  echo intval($goods['intervalnum2'])-1?>件<?php  } else { ?>件以上<?php  } ?></p>
                    </span>
                <?php  } ?>
                <?php  if($goods['intervalfloor']>1) { ?>
                <span>
                        <p class="price" style="color: <?php  echo $diyitem['style']['pricecolor'];?>;"><small>￥</small><?php  echo $goods['intervalprice2'];?></p>
                        <p><?php  echo $goods['intervalnum2'];?><?php  if($goods['intervalfloor']>2) { ?>-<?php  echo intval($goods['intervalnum3'])-1?>件<?php  } else { ?>件以上<?php  } ?></p>
                    </span>
                <?php  } ?>
                <?php  if($goods['intervalfloor']>2) { ?>
                <span>
                        <p class="price" style="color: <?php  echo $diyitem['style']['pricecolor'];?>;"><small>￥</small><?php  echo $goods['intervalprice3'];?></p>
                        <p>><?php  echo $goods['intervalnum3'];?>件</p>
                    </span>
                <?php  } ?>
            </div>
        </div>
    <?php  } ?>

    <?php  if(empty($seckillinfo) && $goods['type']!=4) { ?>
    <div class="fui-cell">
        <div class="fui-cell-text price">
            <?php  if($islive) { ?>
                <span class="live-price" style="background-color: <?php  echo $diyitem['style']['pricecolor'];?>;">直播价</span>
            <?php  } ?>

			<span class="text-danger" style="vertical-align: middle; color: <?php  echo $diyitem['style']['pricecolor'];?>; ">
			￥<?php  if($goods['ispresell']>0 && ($goods['preselltimeend'] == 0 || $goods['preselltimeend'] > time())) { ?><?php  echo $goods['presellprice'];?>
                <?php  } else { ?>
                    <?php  if($goods['minprice']==$goods['maxprice']) { ?><?php  echo $goods['minprice'];?><?php  } else { ?><?php  echo $goods['minprice'];?>~<?php  echo $goods['maxprice'];?><?php  } ?>
                <?php  } ?>
			<?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
			     <span class="original">￥<?php  echo $goods['productprice'];?></span>
			<?php  } else { ?>
			<?php  if($goods['productprice']>0) { ?><span  class="original">￥<?php  echo $goods['productprice'];?></span><?php  } ?>
			<?php  } ?>
			</span>
        </div>
        <!--分销佣金-->
        <?php  if($goods['cansee']>0 &&  $goods['seecommission']>0 ) { ?>
        <div class="detail-Commission flex" style="padding-bottom: 0">
            <div class="text"> <?php echo empty($goods['seetitle'])?'预计最高佣金':$goods['seetitle']?></div>
            <div class="num flex1">￥<?php  echo $goods['seecommission'];?></div>
        </div>
        <?php  } ?>
    </div>


        <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
        <div class="row row-time">
            <div id='discount-container' class='fui-labeltext fui-labeltext-danger' style="background: <?php  echo $diyitem['style']['timecolor'];?>;""
                 data-now="<?php  echo date('Y-m-d H:i:s')?>"
                 data-end="<?php  echo date('Y-m-d H:i:s', $goods['isdiscount_time'])?>"
                 data-end-label='<?php echo empty($goods['isdiscount_title'])?'促销':$goods['isdiscount_title']?>' >
            <i class="icon icon-clock" style="float: left;color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></i>
            <div class='label' style="background: <?php  echo $diyitem['style']['timecolor'];?>;color: <?php  echo $diyitem['style']['timetextcolor'];?>;"><?php echo empty($goods['isdiscount_title'])?'促销':$goods['isdiscount_title']?></div>
            <div class='text' style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;"><span class="day number " style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">-</span><span class="time">天</span><span class="hour number " style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">-</span><span class="time">小时</span><span class="minute number " style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">-</span><span class="time">分</span><span class="second number " style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">-</span><span class="time">秒</span>
            </div>
        </div>
    </div>
    <?php  } ?>


<?php  if($goods['istime']) { ?>
<div class="row row-time">
    <div id='time-container' class='fui-labeltext fui-labeltext-danger' style="background: <?php  echo $diyitem['style']['timecolor'];?>;";
         data-now="<?php  echo date('Y-m-d H:i:s')?>"
         data-start-label="距离<?php echo empty($_W['shopset']['trade']['istimetext'])? '限时购': $_W['shopset']['trade']['istimetext']?>开始"
         data-end-label="距离<?php echo empty($_W['shopset']['trade']['istimetext'])? '限时购': $_W['shopset']['trade']['istimetext']?>结束"
         data-end-text='活动已经结束，下次早点来~'
         data-start="<?php  echo date('Y-m-d H:i:s', $goods['timestart'])?>"
         data-end="<?php  echo date('Y-m-d H:i:s', $goods['timeend'])?>">
        <i class="icon icon-clock" style="float: left;color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></i>
        <div class='label' style="background: <?php  echo $diyitem['style']['timecolor'];?>;color: <?php  echo $diyitem['style']['timetextcolor'];?>;"><?php echo empty($_W['shopset']['trade']['istimetext'])? '限时购': $_W['shopset']['trade']['istimetext']?></div>
        <div class='text' style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">
            <span class="day number" style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></span>
            <span class="time">天</span>
            <span class="hour number" style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></span>
            <span class="time">小时</span>
            <span class="minute number" style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></span>
            <span class="time">分</span>
            <span class="second number" style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></span>
            <span class="time">秒</span>
        </div>
    </div>
</div>
<?php  } ?>
<?php  } ?>

<?php  if($goods['ispresell']==1 && ($goods['preselltimestart'] > time() || $goods['preselltimeend'] > time())) { ?>
<div class="row row-time" >
    <div id='time-presell' class='fui-labeltext fui-labeltext-danger'
         data-now="<?php  echo date('Y-m-d H:i:s')?>"
         data-start-label='距离预售开始'
         data-end-label='距离预售结束'
         data-end-text='活动已经结束，下次早点来~'
         data-start="<?php  echo date('Y-m-d H:i:s', $goods['preselltimestart'])?>"
         data-end="<?php  echo date('Y-m-d H:i:s', $goods['preselltimeend'])?>" style="background: <?php  echo $diyitem['style']['timecolor'];?>;color: <?php  echo $diyitem['style']['timetextcolor'];?>;">
        <i class="icon icon-clock" style="float: left;color: <?php  echo $diyitem['style']['timetextcolor'];?>;"></i>
        <div class='label'  style="background: <?php  echo $diyitem['style']['timecolor'];?>;color: <?php  echo $diyitem['style']['timetextcolor'];?>;">预售</div>
        <div class='text' style="color: <?php  echo $diyitem['style']['timetextcolor'];?>;">
            <span class="day number" ></span><span class="time">天</span><span class="hour number" ></span><span class="time">小时</span><span class="minute number"></span><span class="time">分</span><span class="second number" ></span><span class="time">秒</span>
        </div>
    </div>
</div>
<?php  } ?>
<div class="fui-cell"  style="font-size: 0.55rem;line-height: 1.2rem;justify-content: space-between;-webkit-justify-content: space-between;flex-wrap: wrap;-webkit-flex-wrap: wrap;color: #666;">
        <?php  if(is_array($goods['dispatchprice'])) { ?>
            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                <p style="color: <?php  echo $diyitem['style']['textcolor'];?>;">快递:  <?php  echo number_format($goods['dispatchprice']['min'],2)?> ~ <?php  echo number_format($goods['dispatchprice']['max'],2)?></p>
            <?php  } ?>
        <?php  } else { ?>
            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                <p style="color: <?php  echo $diyitem['style']['textcolor'];?>;">快递:  <?php echo $goods['dispatchprice'] == 0 ? '包邮' : number_format($goods['dispatchprice'],2)?></p>
            <?php  } ?>
        <?php  } ?>
        <?php  if($seckillinfo==false || ( $seckillinfo && $seckillinfo.status==1)) { ?>
            <?php  if($goods['showtotal'] == 1) { ?>
            <p style="color: <?php  echo $diyitem['style']['textcolor'];?>;">库存:  <?php  echo $goods['total'];?></p>
            <?php  } ?>
            <?php  if(!empty($goods['showsales'])) { ?>
                <p style="color: <?php  echo $diyitem['style']['textcolor'];?>;">销量:  <?php  echo number_format($goods['sales'],0)?> <?php  echo $goods['unit'];?></p>
            <?php  } ?>
        <?php  } else { ?>
            <p></p>
            <p></p>
        <?php  } ?>
        <?php  if($goods['province'] != '请选择省份' && $goods['city'] != '请选择城市') { ?>
            <p style="color: <?php  echo $diyitem['style']['textcolor'];?>;"><?php  echo $goods['province'];?> <?php  echo $goods['city'];?></p>
        <?php  } ?>
</div>
<?php  if($goods['ispresell']==1 && ( $goods['preselltimeend'] > time() ||  $goods['preselltimeend'] == 0)) { ?>
<div class='fui-cell-group fui-cell-click  fui-sale-group' style='margin-top:0'>
    <div class="fui-list">
        <div class="fui-list-media" style="margin-right: 0;align-self: flex-start;-webkit-align-self: flex-start">
            <div class='fui-cell-text'>
                <span style="font-size: 0.65rem;color: #666;vertical-align: top">预售：</span>
            </div>
        </div>
        <div class="fui-list-inner" style="font-size:0.65rem;color:#666;">
            <?php  if($goods['preselltimeend'] > 0) { ?>
            结束时间：<?php  echo date('m月d日 H:i:s', $goods['preselltimeend'])?><br />
            <?php  } ?>
            预计发货：<?php  if($goods['presellsendtype']>0) { ?>购买后<?php  echo $goods['presellsendtime'];?>天发货<?php  } else { ?><?php  echo date('m月d日', $goods['presellsendstatrttime'])?><?php  } ?>
        </div>
    </div>
</div>
<?php  } ?>

</div>