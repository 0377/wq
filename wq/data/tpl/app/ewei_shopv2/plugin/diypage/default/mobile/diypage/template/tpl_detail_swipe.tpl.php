<?php defined('IN_IA') or exit('Access Denied');?><!-- 商品轮播图 -->
<style>
    .fui-swipe-page {text-align: <?php  echo $diyitem['style']['dotalign'];?>; bottom: <?php  echo $diyitem['style']['bottom'];?>px; height: 12px; line-height: 12px; padding: 0 <?php  echo $diyitem['style']['leftright'];?>px;}
    .fui-swipe-bullet {background: <?php  echo $diyitem['style']['background'];?>; opacity: <?php  echo $diypage['style']['opacity'];?>; height: 12px; width: 12px;}
    .fui-swipe-bullet.active {background: <?php  echo $diyitem['style']['background'];?>; opacity: 1;}
    .fui-swipe-page.square .fui-swipe-bullet {height: 12px; width: 12px; border-radius: 0;}
    .fui-swipe-page.rectangle .fui-swipe-bullet {height: 12px; width: 20px; border-radius: 0;}
</style>
<div class='fui-swipe goods-swipe'>
    <?php  if(!empty($islive)) { ?>
        <a class="external isliving" href="<?php  echo mobileUrl('live/room', array('id'=>$liveid))?>">直播中</a>
    <?php  } ?>

    <div class='fui-swipe-wrapper'>
        <?php  if(is_array($thumbs)) { foreach($thumbs as $thumb) { ?>
        <div class='fui-swipe-item'><img src="<?php echo EWEI_SHOPV2_PLACEHOLDER;?>"  data-lazy="<?php  echo tomedia($thumb)?>" /></div>
        <?php  } } ?>
    </div>
    <div class="fui-swipe-page <?php  echo $diyitem['style']['dotstyle'];?>"></div>
    <?php  if($goods['total']<=0 && !empty($_W['shopset']['shop']['saleout'])) { ?>
    <div class="salez">
        <img src="<?php  echo tomedia($_W['shopset']['shop']['saleout'])?>">
    </div>
    <?php  } ?>
</div>