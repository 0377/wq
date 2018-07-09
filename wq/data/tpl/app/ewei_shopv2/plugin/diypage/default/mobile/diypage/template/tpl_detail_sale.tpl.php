<?php defined('IN_IA') or exit('Access Denied');?>
<div class="detail-sale" style="margin-top: <?php  echo $diyitem['style']['margintop'];?>px; margin-bottom: <?php  echo $diyitem['style']['marginbottom'];?>px;">

    <!-- 商品可用优惠券 -->
    <?php  if(com('coupon')&&$coupons) { ?>
    <div class="fui-cell-group fui-cell-click fui-sale-group noborder" style="background: <?php  echo $diyitem['style']['background'];?>;">
        <div class="fui-cell">
            <div class="fui-cell-text coupon-selector">
                <span style="margin-right: 0.25rem; color: <?php  echo $diyitem['style']['textcolor'];?>;">优惠券</span>
                <?php  if(is_array($coupons)) { foreach($coupons as $index => $couponmini) { ?>
                <?php  if($index<5) { ?>
                <span class="coupon-mini"><?php  if($couponmini['backpre']) { ?>&yen;<?php  } ?><?php  echo price_format($couponmini['backmoney'])?><?php  if($couponmini['backtype']==1) { ?>折<?php  } ?></span>
                <?php  } ?>
                <?php  } } ?>
            </div>
            <div class="fui-cell-remark"><?php  if(count($coupons)>5) { ?>更多<?php  } ?></div>
        </div>
    </div>
    <?php  } ?>

    <!-- 所有营销 -->
    <div class="fui-cell-group  fui-sale-group nomargin" style="background: <?php  echo $diyitem['style']['background'];?>;">

        <!-- 会员等级 -->
        <?php  if(empty($goods['isdiscount']) || (!empty($goods['isdiscount']) &&$goods['isdiscount_time']<time())) { ?>
            <?php  if(!empty($memberprice) && $memberprice!=$goods['minprice'] && !empty($level)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label" style="padding-top: 0.2rem; color: <?php  echo $diyitem['style']['textcolor'];?>;">会员</div>
                <div class="fui-cell-text" style="white-space: normal; color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                        <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;"><?php  echo $level['levelname'];?></span>
                        <span>可享受 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $memberprice;?></span> 的价格</span>
                    </div>
                </div>
            </div>
        <?php  } ?>
        <?php  } ?>

        <?php  if(($enoughs && count($enoughs)>0 && empty($seckillinfo)) || ($hasSales && empty($seckillinfo)) || ((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) || (floatval($goods['buyagain'])>0 && empty($seckillinfo)) || (!empty($fullbackgoods) && $isfullback)) { ?>
        <div class="fui-cell fui-sale-cell" id="picker-sales">
            <div class="fui-cell-label" style="padding-top: 0.2rem; color: <?php  echo $diyitem['style']['textcolor'];?>;">活动</div>
            <div class="fui-cell-text">
                <!-- 满减 -->
                <?php  if($enoughs && count($enoughs)>0 && empty($seckillinfo)) { ?>
                <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">满减</span>
                    <span>全场满 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $enoughs[0]['enough'];?></span> 立减 <span class="text-danger"style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $enoughs[0]['money'];?></span></span>
                </div>
                <?php  } ?>

                <!-- 包邮 -->
                <?php  if($hasSales && empty($seckillinfo)) { ?>
                <?php  if((!is_array($goods['dispatchprice']) && $goods['type']==1 && $goods['isverify']!=2 && $goods['dispatchprice']==0) || (($enoughfree && $enoughfree==-1) || ($enoughfree>0 || $goods['ednum']>0 || $goods['edmoney']>0))) { ?>
                <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">包邮</span>
                    <span>
                            <?php  if(!is_array($goods['dispatchprice'])) { ?>
                            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                            <?php  if($goods['dispatchprice']==0) { ?>
                            本商品包邮;
                            <?php  } ?>
                            <?php  } ?>
                            <?php  } ?>

                            <?php  if($enoughfree && $enoughfree==-1) { ?>
                            全场包邮
                            <?php  } else { ?>
                            <?php  if($goods['ednum']>0) { ?>单品满 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;"><?php  echo $goods['ednum'];?></span> <?php echo empty($goods['unit'])?'件':$goods['unit']?>包邮; <?php  } ?>
                            <?php  if($goods['edmoney']>0) { ?>单品满 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $goods['edmoney'];?></span> 包邮; <?php  } ?>
                            <?php  if($enoughfree) { ?>全场满 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $enoughfree;?></span> 包邮; <?php  } ?>
                            <?php  } ?>
                        </span>
                </div>
                <?php  } ?>
                <?php  } ?>

                <!-- 积分 -->
                <?php  if((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) { ?>
                <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;"><?php  echo $_W['shopset']['trade']['credittext'];?></span>
                    <span>
                        <?php  if(!empty($goods['deduct']) && $goods['deduct'] != '0.00') { ?>最高抵扣 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">&yen;<?php  echo $goods['deduct'];?></span><?php  } ?> <?php  if(!empty($goods['credit'])) { ?>购买赠送 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;"><?php  echo $goods['credit'];?></span> <?php  echo $_W['shopset']['trade']['credittext'];?><?php  } ?>
                    </span>
                </div>
                <?php  } ?>

                <!-- 二次购买 -->
                <?php  if(floatval($goods['buyagain'])>0 && empty($seckillinfo)) { ?>
                <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">复购</span>
                    <span>此商品重复购买可享受 <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;"><?php  echo $goods['buyagain'];?></span> 折优惠<?php  if(empty($goods['buyagain_sale'])) { ?><br>重复购买 不与其他优惠共享<?php  } ?></span>
                </div>
                <?php  } ?>

                <!--全返-->
                <?php  if(!empty($fullbackgoods) && $isfullback) { ?>
                <div class="sale-line" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                    <span class="sale-tip" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>; border-color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">全返</span>
                    <span>
                    该商品享受
                    <?php  if($fullbackgoods['type']>0) { ?>
                        <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">
                            <?php  if($goods['hasoption'] > 0) { ?>
                                <?php  if($fullbackgoods['minallfullbackallprice'] == $fullbackgoods['maxallfullbackallprice']) { ?>
                                    <?php  echo $fullbackgoods['minallfullbackallratio'];?>%
                                <?php  } else { ?>
                                    <?php  echo $fullbackgoods['minallfullbackallratio'];?>% ~ <?php  echo $fullbackgoods['maxallfullbackallratio'];?>%
                                <?php  } ?>
                            <?php  } else { ?>
                                <?php  echo $fullbackgoods['minallfullbackallratio'];?>%
                            <?php  } ?>
                        </span>
                    <?php  } else { ?>
                        <span class="text-danger" style="color: <?php  echo $diyitem['style']['textcolorhigh'];?>;">
                            <?php  if($goods['hasoption'] > 0) { ?>
                                <?php  if($fullbackgoods['minallfullbackallprice'] == $fullbackgoods['maxallfullbackallprice']) { ?>
                            &yen;<?php  echo $fullbackgoods['minallfullbackallprice'];?>
                                <?php  } else { ?>
                            &yen;<?php  echo $fullbackgoods['minallfullbackallprice'];?> ~ &yen;<?php  echo $fullbackgoods['maxallfullbackallprice'];?>
                                <?php  } ?>
                            <?php  } else { ?>
                            &yen;<?php  echo $fullbackgoods['minallfullbackallprice'];?>
                            <?php  } ?>
                        </span>
                    <?php  } ?>
                    的全返
                </span>
                </div>
                <?php  } ?>

            </div>
            <div class="fui-cell-remark"></div>
        </div>
        <?php  } ?>
    </div>

    <?php  if(!empty($goods['city_express_state'])) { ?>
    <div class="fui-cell-group fui-cell-click fui-sale-group noborder" style="background: <?php  echo $diyitem['style']['background'];?>;">
        <a class="external fui-cell"  href="<?php  echo mobileUrl('shop/cityexpress/map')?>">
            <div class="fui-cell-text">
                <span style="vertical-align:middle;margin-right: 0.25rem; color: <?php  echo $diyitem['style']['textcolor'];?>;">配送</span>
                <span class="sale-line">
                        <span class="sale-tip">同城</span>
                        <span>查看商家位置</span>
                </span>
            </div>
            <div class="fui-cell-remark"></div>
        </a>
    </div>
    <?php  } ?>

    <!--赠品-->
    <?php  if($gifts && $goods['total'] > 0) { ?>
    <div class="fui-cell-group  fui-sale-group nomargin" style="background: <?php  echo $diyitem['style']['background'];?>;">
        <div class="fui-cell fui-sale-cell sendgoods">
            <div class="fui-cell-label" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">赠品</div>
            <?php  if(count($gifts)>1) { ?>
            <div class="fui-cell-text fui-cell-giftclick" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                <label id="gifttitle">请选择赠品</label>
                <input type="hidden" name="giftid" id="giftid" value="">
            </div>
            <?php  } else { ?>
            <?php  if(is_array($gifts)) { foreach($gifts as $item) { ?>
            <div class='fui-cell-text ' style="color: <?php  echo $diyitem['style']['textcolor'];?>;" onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/gift',array('id'=>$item['id']))?>'">
                <?php  echo $gifttitle;?><input type="hidden" name="giftid" id="giftid" value="<?php  echo $item['id'];?>">
            </div>
            <?php  } } ?>
            <?php  } ?>
            <div class="fui-cell-remark"></div>
        </div>
    </div>
    <?php  } ?>


    <!--服务完成-->
    <?php  if($hasServices || $labelname) { ?>
    <div class="fui-cell-group fui-option-group" style='margin-top:0;'>

        <div class="goods-label-demo">
            <div class="goods-label-list
        <?php  if(empty($style['style'])) { ?>goods-label-style1
        <?php  } else if($style['style']==1) { ?>goods-label-style2
        <?php  } else if($style['style']==2) { ?>goods-label-style3
        <?php  } else if($style['style']==3) { ?>goods-label-style4
        <?php  } else if($style['style']==4) { ?>goods-label-style5<?php  } ?>">
                <?php  if($goods['cash']==2) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>货到付款</strong></span><?php  } ?>
                <?php  if($goods['quality']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>正品保证</strong></span><?php  } ?>
                <?php  if($goods['repair']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>保修</strong></span><?php  } ?>
                <?php  if($goods['invoice']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>发票</strong></span><?php  } ?>
                <?php  if($goods['seven']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>7天退换</strong></span><?php  } ?>
                <?php  if($labelname) { ?>
                <?php  if(is_array($labelname)) { foreach($labelname as $item) { ?>
                <span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong><?php  echo $item;?></strong></span>
                <?php  } } ?>
                <?php  } ?>
                <div style="clear: both;"></div>
            </div>
        </div>


    </div>
    <?php  } ?>

    <!--配送区域完成-->
    <?php  if($has_city && $goods['type']!=5) { ?>
    <div class='fui-cell-group fui-cell-click fui-sale-group noborder'  id="city-picker" style="background: <?php  echo $diyitem['style']['background'];?>;">
        <div class='fui-cell'>
            <div class='fui-cell-text' style="color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: 0.7rem;line-height: 0.7rem"><?php  if(empty($onlysent)) { ?>不<?php  } else { ?>只<?php  } ?>配送区域:
                <?php  if(is_array($citys)) { foreach($citys as $item) { ?>
                <?php  echo $item;?>
                <?php  } } ?>
            </div>
            <div class='fui-cell-remark'></div>
        </div>
    </div>
    <?php  } ?>

</div>