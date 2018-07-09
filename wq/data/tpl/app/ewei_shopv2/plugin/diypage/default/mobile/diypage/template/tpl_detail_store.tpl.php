<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($stores)) { ?>
<script language='javascript' src='https://api.map.baidu.com/api?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&s=1'></script>
<div class='fui-according-group' style="margin-top: <?php  echo $diyitem['style']['margintop'];?>px; margin-bottom: <?php  echo $diyitem['style']['marginbottom'];?>px; background: <?php  echo $diyitem['style']['background'];?>;">
    <div class='fui-according expanded'>
        <div class='fui-according-header'>
            <i class='icon icon-shop' style="color: <?php  echo $diyitem['style']['titlecolor'];?>;"></i>
            <span class="text" style="color: <?php  echo $diyitem['style']['titlecolor'];?>;">适用门店</span>
            <span class="remark"><div class="badge"><?php  echo count($stores)?></div></span>
        </div>
            <div class="fui-according-content store-container fui-cell-group">
                <?php  if(is_array($stores)) { foreach($stores as $item) { ?>
                <a href="<?php  echo mobileUrl('store/detail',array('id'=>$item['id'],'merchid'=>$item['merchid']))?>"  class="fui-cell store-item external" data-lng="<?php  echo floatval($item['lng'])?>" data-lat="<?php  echo floatval($item['lat'])?>">
                    <div class="fui-cell-icon">
                        <i class='icon icon-dingwei1'></i>
                    </div>
                    <div class="fui-cell-text">
                        <div class="title" style="color: <?php  echo $diyitem['style']['shopnamecolor'];?>;"><span class='storename'><?php  echo $item['storename'];?></span></div>
                    </div>
                    <div class="fui-cell-remark ">
                        查看
                    </div>
                </a>
                <?php  } } ?>
                <?php  if(count($stores)>3) { ?>
                <div class='show-allshop'><span class='show-allshop-btn'>加载更多门店</span></div>
                <?php  } ?>
            </div>
        <div id="nearStore" style="display:none">
            <div class='fui-list store-item'  id='nearStoreHtml'></div>
        </div>
    </div>
</div>
<?php  } ?>

