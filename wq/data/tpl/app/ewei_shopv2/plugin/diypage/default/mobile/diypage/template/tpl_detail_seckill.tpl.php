<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($seckillinfo) ) { ?>
<div class=" <?php  echo $diyitem['style']['theme']?>" >
    <div class="seckill-container "
        data-starttime="<?php  echo $seckillinfo['starttime'];?>"
        data-endtime="<?php  echo $seckillinfo['endtime'];?>"
        data-status="<?php  echo $seckillinfo['status'];?>">
        <div class="fui-list seckill-list" style="flex: 2;">
            <div class="fui-list-media seckill-price">
                ¥
                <span>
               <?php  echo $seckillinfo['price'];?>
              </span>
            </div>
            <div class="fui-list-inner">
                <div class="text">
                <span class="oldprice">
                  <?php  echo $goods['productprice'];?>
                </span>
                </div>
            </div>
        </div>
        <div class="fui-list seckill-list1">
            <div class="fui-list-inner">
                <div class="text ">
                    <?php  if($seckillinfo['status']==0) { ?>已出售 <?php  echo $seckillinfo['percent'];?>%<?php  } ?>
                </div>
                <div class="text ">
                <span class="process">
                  <div class="process-inner" style="width:<?php  echo $seckillinfo['percent'];?>%">
                  </div>
                </span>
                </div>
            </div>
        </div>
        <div class="fui-list seckill-list2" style="">
            <div class="fui-list-inner">
                <div class="text ">
                    距<?php  if($seckillinfo['status']==1) { ?>开始<?php  } else { ?>结束<?php  } ?>还有
                </div>
                <div class="text timer">
                <span class="time-hour">
                  -
                </span>
                    :
                    <span class="time-min">
                  -
                </span>
                    :
                    <span class="time-sec">
                  -
                </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php  } ?>