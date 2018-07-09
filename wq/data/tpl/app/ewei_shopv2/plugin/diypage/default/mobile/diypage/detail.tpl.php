<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .page-goods-detail .basic-block {background: <?php  echo $diypage['background'];?>;}
    .fui-cell-group{width:100%;}

    .fui-header {background: <?php  echo $diypage['tab']['style']['background'];?>;}
    .fui-tab a {color: <?php  echo $diypage['tab']['style']['textcolor'];?>;}
    .fui-tab.fui-tab-danger a.active {color: <?php  echo $diypage['tab']['style']['activecolor'];?>; border-color: <?php  echo $diypage['tab']['style']['activecolor'];?>;}
    .page-goods-detail .fui-comment-group .fui-cell .comment .info .star .shine {color: <?php  echo $diypage['comment']['style']['maincolor'];?>;}
    .fui-list-media.radius img {border-radius: 0.3rem;}
    .fui-list-media.circle img {border-radius: 2.5rem;}

    .btn-like {color: <?php  echo $diypage['navbar']['style']['iconcolor'];?>}
    .btn-like.icon-likefill {color: #f01}

    .fui-cell-group{width:100%;}
    .fullback-title{color:#999999;font-size:0.7rem;padding:0.75rem 0 0.5rem 0;}
    .fullback-info{}
    .fullback-info p{height:1rem;line-height: 1rem;font-size:0.625rem;color:#333;display: inline-block;padding:0 0.5rem 0 0;}
    .fullback-info p i{border:none;height:0.75rem;width:0.75rem;display: inline-block;background: #ff4753;color:#fff;font-size:0.4rem;line-height: 0.8rem;text-align: center;
        font-style: normal;border-radius: 0.75rem;-webkit-border-radius: 0.75rem;margin-right:0.25rem;}
    .fullback-remark{line-height: 1rem;font-size:0.6rem;color:#666;padding:0.2rem 0;}
    .content-images img{display: block;}
</style>

<link rel="stylesheet" href="../addons/ewei_shopv2/static/js/dist/swiper/swiper.min.css">
<link href="../addons/ewei_shopv2/plugin/diypage/static/css/foxui.diy.css?v=201610091000"rel="stylesheet"type="text/css"/>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon.css?v=2.0.0">
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon-new.css?v=2017030302">
<link rel="stylesheet" href="../addons/ewei_shopv2/static/js/dist/swiper/swiper.min.css">

<?php  if(!empty($islive)) { ?>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/living.css?v=20170628" />
<?php  } ?>

<div class='fui-page fui-page-current  page-goods-detail' id='page-goods-detail-index'>
    <?php  if($diypage['followbar']) { ?>
        <?php  $this->followBar(true)?>
    <?php  } ?>

    <?php  if(empty($err)) { ?>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back" id="btn-back"></a>
        </div>
        <div class="title">
            <?php  if($new_temp) { ?>
            <?php  if(!empty($diypage['tab']['params']['goodstext'])) { ?><?php  echo $diypage['tab']['params']['goodstext'];?><?php  } else { ?>商品<?php  } ?><?php  if(!empty($diypage['tab']['params']['detailtext'])) { ?><?php  echo $diypage['tab']['params']['detailtext'];?><?php  } else { ?>详情<?php  } ?>
            <?php  } else { ?>
            <div id="tab" class="fui-tab fui-tab-danger">
                <a data-tab="tab1" class="tab active"><?php  if(!empty($diypage['tab']['params']['goodstext'])) { ?><?php  echo $diypage['tab']['params']['goodstext'];?><?php  } else { ?>商品<?php  } ?></a>
                <a data-tab="tab2" class="tab"><?php  if(!empty($diypage['tab']['params']['detailtext'])) { ?><?php  echo $diypage['tab']['params']['detailtext'];?><?php  } else { ?>详情<?php  } ?></a>
                <?php  if(count($params)>0) { ?>
                <a data-tab="tab3" class="tab">参数</a>
                <?php  } ?>
                <?php  if($getComments) { ?>
                <a  data-tab="tab4" class="tab" style="display:none" id="tabcomment">评价</a>
                <?php  } ?>
            </div>
            <?php  } ?>
        </div>
        <div class="fui-header-right"></div>
    </div>
    <?php  } else { ?>
    <div class="fui-header ">
        <div class="fui-header-left">
            <a class="back" id="btn-back"></a>
        </div>
        <div class="title">
            找不到宝贝
        </div>
    </div>
    <?php  } ?>

    <?php  if(empty($err)) { ?>

    <!--参数已完成-->
    <?php  if(count($params)>0) { ?>
    <div class="fui-content param-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>">
        <div class="fui-cell-group notop">
            <?php  if(!empty($params)) { ?>
            <?php  if(is_array($params)) { foreach($params as $p) { ?>
            <div class="fui-cell">
                <div class="fui-cell-label md" ><?php  echo $p['title'];?></div>
                <div class="fui-cell-info overflow md"><?php  echo $p['value'];?></div>
            </div>
            <?php  } } ?>

            <?php  } else { ?>
            <div class="fui-cell">
                <div class="fui-cell-info text-align">商品没有参数</div>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <!--评价-->
    <?php  if(!$new_temp) { ?>
    <div class='fui-content comment-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>' data-getcount='1' id='comments-list-container'>
    <div class='fui-icon-group col-5 '>
        <div class='fui-icon-col' data-level='all'><span class='text-danger'>全部<br/><span class="count"></span></span></div>
        <div class='fui-icon-col' data-level='good'><span>好评<br/><span class="count"></span></span></div>
        <div class='fui-icon-col' data-level='normal'><span>中评<br/><span class="count"></span></span></div>
        <div class='fui-icon-col' data-level='bad'><span>差评<br/><span class="count"></span></span></div>
        <div class='fui-icon-col' data-level='pic'><span>晒图<br/><span class="count"></span></span></div>
    </div>
    <div class='content-empty' style='display:none;'>
        <i class='icon icon-community'></i><br/>暂时没有任何评价
    </div>
    <div class='container' id="comments-all"></div>
    <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
</div>
<!--商品详情已完成-->
<div class="fui-content detail-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>">
    <div class="text-danger look-basic"><i class='icon icon-unfold'></i> <span>上拉返回商品详情</span></div>
    <div class='content-block content-images'></div>
</div>
<?php  } ?>

<div class='fui-content basic-block pulldown <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>'>


<?php  if(!empty($err)) { ?>
<div class='content-empty'>
    <i class='icon icon-search'></i><br/> 宝贝找不到了~ 您看看别的吧 ~<br/><a href="<?php  echo mobileUrl()?>" class='btn btn-default-o external'>到处逛逛</a>
</div>
<?php  } else { ?>
<?php  if($commission_data['qrcodeshare'] > 0) { ?>
<i class="icon icon-qrcode" id="alert-click"></i>
<?php  } ?>



<!-- diy元素 -->
<?php  if(is_array($diypage['items'])) { foreach($diypage['items'] as $diyitem) { ?>
    <?php  if($diyitem['id']=='detail_comment') { ?>
        <div id='comments-container'></div>
    <?php  } else if($diyitem['id']=='detail_pullup') { ?>
        <?php  if(empty($diyitem['params']['hide']) && !$new_temp) { ?>
        <div class="fui-cell-group" style="background: <?php  echo $diyitem['style']['background'];?>; margin-top: <?php  echo $diyitem['style']['margintop'];?>px;">
            <div class="fui-cell">
                <div class="fui-cell-text text-center look-detail" style="color: <?php  echo $diyitem['style']['textcolor'];?>"><i class='icon icon-fold'></i> <span>上拉查看图文详情</span></div>
            </div>
        </div>
        <?php  } ?>
    <?php  } else { ?>
        <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diypage/template/tpl_'.$diyitem['id'], TEMPLATE_INCLUDEPATH)) : (include template('diypage/template/tpl_'.$diyitem['id'], TEMPLATE_INCLUDEPATH));?>
    <?php  } ?>
<?php  } } ?>

<?php  if($new_temp) { ?>
<?php  if(count($params)>0 || $showComments) { ?>
<div class="fui-tab fui-tab-danger detail-tab" id="tabnew">
    <a data-tab="tab2" class="active"><?php  if(!empty($diypage['tab']['params']['detailtext'])) { ?><?php  echo $diypage['tab']['params']['detailtext'];?><?php  } else { ?>详情<?php  } ?></a>
    <?php  if(count($params)>0) { ?>
    <a data-tab="tab3">参数</a>
    <?php  } ?>
    <?php  if($showComments) { ?>
    <a data-tab="tab4">评价</a>
    <?php  } ?>
</div>
<?php  } else { ?>
<div class="fui-cell-group">
    <div class="fui-cell">
        <div class="fui-cell-info"><?php  if(!empty($diypage['tab']['params']['goodstext'])) { ?><?php  echo $diypage['tab']['params']['goodstext'];?><?php  } else { ?>商品<?php  } ?><?php  if(!empty($diypage['tab']['params']['detailtext'])) { ?><?php  echo $diypage['tab']['params']['detailtext'];?><?php  } else { ?>详情<?php  } ?></div>
    </div>
</div>
<?php  } ?>
<div class="detail-tab-panel">
    <div class="tab-panel show detail-block" data-tab="tab2">
        <div class="content-block content-images"></div>
    </div>
    <div class="tab-panel" data-tab="tab3">
        <div class="fui-cell-group">
            <?php  if(!empty($params)) { ?>
            <?php  if(is_array($params)) { foreach($params as $p) { ?>
            <div class="fui-cell">
                <div class="fui-cell-label" ><?php  echo $p['title'];?></div>
                <div class="fui-cell-info overflow"><?php  echo $p['value'];?></div>
            </div>
            <?php  } } ?>
            <?php  } else { ?>
            <div class="fui-cell">
                <div class="fui-cell-info text-align">商品没有参数</div>
            </div>
            <?php  } ?>
        </div>
    </div>
    <div class="tab-panel comment-block" data-tab="tab4" data-getcount='1' id='comments-list-container'>
        <div class='fui-icon-group col-5 '>
            <div class='fui-icon-col' data-level='all'><span class='text-danger'>全部<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='good'><span>好评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='normal'><span>中评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='bad'><span>差评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='pic'><span>晒图<br/><span class="count"></span></span></div>
        </div>
        <div class='content-empty' style='display:none;'>暂时没有任何评价
        </div>
        <div class='container' id="comments-all"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
    </div>
</div>
<?php  } ?>

<?php  } ?>
</div>

<!--赠品弹层-->
<?php  if($isgift && $goods['total'] > 0) { ?>
<div id='gift-picker-modal' style="margin:-100%;">
    <div class='gift-picker'>
        <div class='fui-cell-title text-center' style="background: white;">请选择赠品</div>
        <div class="fui-cell-group fui-sale-group" style="margin-top:0; overflow-y: auto;">
            <div class="fui-cell">
                <div class="fui-cell-text dispatching">
                    <div class="dispatching-info" style="max-height:12rem;overflow-y: auto ">
                        <?php  if(is_array($gifts)) { foreach($gifts as $item) { ?>
                        <div class="fui-list goods-item align-start" data-giftid="<?php  echo $item['id'];?>">
                            <div class="fui-list-media">
                                <input type="radio" name="checkbox" class="fui-radio fui-radio-danger gift-item" value="<?php  echo $item['id'];?>" style="display: list-item;">
                            </div>
                            <div class="fui-list-inner">
                                <?php  if(is_array($item['gift'])) { foreach($item['gift'] as $gift) { ?>
                                <div class="fui-list">
                                    <div class="fui-list-media image-media" style="position: initial;">
                                        <a href="javascript:void(0);">
                                            <img class="round" src="<?php  echo tomedia($gift['thumb'])?>" data-lazyloaded="true">
                                        </a>
                                    </div>
                                    <div class="fui-list-inner">
                                        <a href="javascript:void(0);">
                                            <div class="text">
                                                <?php  echo $gift['title'];?>
                                            </div>
                                        </a>
                                    </div>
                                    <div class='fui-list-angle'>
                                        <span class="price">&yen;<del class='marketprice'><?php  echo $gift['marketprice'];?></del></span>
                                    </div>
                                </div>
                                <?php  } } ?>
                            </div>
                        </div>
                        <?php  } } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn btn-danger block fullbtn">确定</div>
    </div>
</div>
<?php  } ?>
<!--底部按钮-->

<?php  if($goods['canbuy']) { ?>
<div class="fui-navbar bottom-buttons" style="background: <?php  echo $diypage['navbar']['style']['background'];?>;">

    <?php  if(!empty($diypage['diynavbar'])) { ?>
    <?php  if(is_array($diypage['diynavbar'])) { foreach($diypage['diynavbar'] as $navbaritem) { ?>
    <?php  if($navbaritem['type']=='like') { ?>
    <a  class="nav-item favorite-item <?php  if($isFavorite) { ?>active<?php  } ?>" data-isfavorite="<?php  echo intval($isFavorite)?>">
        <span class="icon btn-like <?php  if($isFavorite) { ?>icon-likefill<?php  } else { ?>icon-like<?php  } ?>"></span>
        <span class="label" style="color: <?php  echo $diypage['navbar']['style']['textcolor'];?>">关注</span>
    </a>
    <?php  } else { ?>
    <a  class="nav-item external" href="<?php  if($navbaritem['type']=='shop') { ?><?php echo !empty($goods['merchid']) ? mobileUrl('merch',array('merchid'=>$goods['merchid'])) : mobileUrl('');?><?php  } else { ?><?php  echo $navbaritem['linkurl'];?><?php  } ?>" <?php  if($navbaritem['type']=='cart') { ?>id="menucart"<?php  } ?>>
        <?php  if($navbaritem['type']=='cart' && $cartCount>0) { ?>
        <span class='badge <?php  if($cartCount<=0) { ?>out<?php  } else { ?>in<?php  } ?>'><?php  echo $cartCount;?></span>
        <?php  } ?>
        <span class="icon <?php  echo $navbaritem['iconclass'];?>" style="color: <?php  echo $diypage['navbar']['style']['iconcolor'];?>"></span>
        <span class="label" style="color: <?php  echo $diypage['navbar']['style']['textcolor'];?>"><?php  echo $navbaritem['icontext'];?></span>
    </a>
    <?php  } ?>
    <?php  } } ?>
    <?php  } ?>

    <?php  if($canAddCart && empty($diypage['navbar']['params']['hidecartbtn'])) { ?>
    <a  class="nav-item btn cartbtn" style="background: <?php  echo $diypage['navbar']['style']['cartcolor'];?>;"  data-type="<?php  echo $goods['type'];?>">加入购物车</a>
    <?php  } ?>
    <?php  if(empty($seckillinfo) || $seckillinfo['status']==0 || $seckillinfo['status']==-1) { ?>
    <a  class="nav-item btn buybtn" style="background: <?php  echo $diypage['navbar']['style']['buycolor'];?>;"  data-type="<?php  echo $goods['type'];?>" data-time="<?php  if(!empty($access_time)) { ?>access_time<?php  } else if(!empty($timeout)) { ?>timeout<?php  } ?>" data-timeout="false"><?php echo !empty($diypage['navbar']['params']['textbuy'])?$diypage['navbar']['params']['textbuy']:"立刻购买"?></a>
    <?php  } else { ?>

    <a  class="nav-item btn buybtn" style="color: <?php  echo $diypage['seckill']['style']['buybtntextwait'];?>;background: <?php  echo $diypage['seckill']['style']['buybtnbgwait'];?>;"  data-type="<?php  echo $goods['type'];?>">
        <?php echo !empty($diypage['seckill']['params']['buybtn'])?$diypage['seckill']['params']['buybtn']:"原价购买"?>
    </a>

    <?php  } ?>
</div>
<?php  } ?>

<!--配送区域弹层-->
<?php  if($has_city) { ?>
<div id='city-picker-modal' style="margin: -100%;">
    <div class='city-picker'>
        <div class='fui-cell-title text-center' style="background: white;"><?php  if(empty($onlysent)) { ?>不<?php  } else { ?>只<?php  } ?>配送区域</div>
        <div class="fui-cell-group fui-sale-group" style="margin-top:0; overflow-y: auto;">
            <div class="fui-cell">
                <div class="fui-cell-text dispatching">
                    <div class="dispatching-info">
                        <?php  if(is_array($citys)) { foreach($citys as $item) { ?>
                        <i><?php  echo $item;?></i>
                        <?php  } } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn btn-danger block fullbtn">确定</div>
    </div>
</div>
<?php  } ?>

<!-- 促销活动层 ---------------------------------------------------------------------------------------->
<div id="picker-sales-modal" style="margin: -100%">
    <div class='picker-sales page-goods-detail'>
        <div class='fui-cell-title text-center' style="background: white;">活&nbsp;动</div>
        <div class="fui-cell-group fui-sale-group noborder" style="margin-top:0; overflow-y: auto;">
            <!-- 商城满减 -->
            <?php  if($enoughfree || ($enoughs && count($enoughs)>0)) { ?>
            <?php  if($enoughs && count($enoughs)>0 && empty($seckillinfo)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">满减</span>
                </div>
                <div class="fui-cell-text" style="white-space: inherit;">
                    <?php  if(is_array($enoughs)) { foreach($enoughs as $key => $enough) { ?>
                    <div>全场 满<span class="text-danger">&yen;<?php  echo $enough['enough'];?></span>立减<span class="text-danger">&yen;<?php  echo $enough['money'];?></span></div>
                    <?php  } } ?>
                </div>
            </div>
            <?php  } ?>
            <?php  } ?>

            <!-- 商户满减 -->
            <?php  if(!empty($merch_set['enoughdeduct']) && empty($seckillinfo)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">满减</span>
                </div>
                <div class="fui-cell-text" style="white-space: inherit;">
                    <?php  if(is_array($merch_set['enoughs'])) { foreach($merch_set['enoughs'] as $key => $enough) { ?>
                    <div>商户 满<span class="text-danger">&yen;<?php  echo $enough['enough'];?></span>立减<span class="text-danger">&yen;<?php  echo $enough['give'];?></span></div>
                    <?php  } } ?>
                </div>
            </div>
            <?php  } ?>

            <!-- 包邮 -->
            <?php  if($hasSales && empty($seckillinfo)) { ?>
            <?php  if((!is_array($goods['dispatchprice']) && $goods['type']==1 && $goods['isverify']!=2 && $goods['dispatchprice']==0) || (($enoughfree && $enoughfree==-1) || ($enoughfree>0 || $goods['ednum']>0 || $goods['edmoney']>0))) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">包邮</span>
                </div>
                <div class="fui-cell-text">
                    <?php  if(!is_array($goods['dispatchprice'])) { ?>
                    <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                    <?php  if($goods['dispatchprice']==0) { ?><div>本商品包邮</div> <?php  } ?>
                    <?php  } ?>
                    <?php  } ?>
                    <?php  if($enoughfree && $enoughfree==-1) { ?><div>全场包邮</div>
                    <?php  } else { ?>
                    <?php  if($enoughfree>0) { ?><div>全场满<span class="text-danger">&yen;<?php  echo $enoughfree;?></span>包邮</div><?php  } ?>
                    <?php  if($goods['ednum']>0) { ?><div>单品满<span class="text-danger"><?php  echo $goods['ednum'];?></span> <?php echo empty($goods['unit'])?'件':$goods['unit']?>包邮</div><?php  } ?>
                    <?php  if($goods['edmoney']>0) { ?><div>单品满<span class="text-danger">&yen;<?php  echo $goods['edmoney'];?></span>包邮</div><?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <?php  } ?>
            <?php  } ?>

            <!-- 积分 -->
            <?php  if((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip"><?php  echo $_W['shopset']['trade']['credittext'];?></span>
                </div>
                <div class="fui-cell-text">
                    <?php  if(!empty($goods['deduct']) && $goods['deduct'] != '0.00') { ?><div>最高抵扣<span class="text-danger">&yen;<?php  echo $goods['deduct'];?></span></div><?php  } ?>
                    <?php  if(!empty($goods['credit'])) { ?><div>购买赠送<span class="text-danger"><?php  echo $goods['credit'];?></span><?php  echo $_W['shopset']['trade']['credittext'];?></div><?php  } ?>
                </div>
            </div>
            <?php  } ?>

            <!-- 二次购买 -->
            <?php  if(floatval($goods['buyagain'])>0 && empty($seckillinfo)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">复购</span>
                </div>
                <div class="fui-cell-text">
                    此商品重复购买可享受<span class="text-danger"><?php  echo $goods['buyagain'];?></span>折优惠<?php  if(empty($goods['buyagain_sale'])) { ?><br>重复购买 不与其他优惠共享<?php  } ?>
                </div>
            </div>
            <?php  } ?>

            <?php  if($isfullback) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">全返</span>
                </div>
                <div class="fui-cell-text" style="white-space: inherit;">
                    <div class="fullback-info">
                        <p style="display: block;"><i>&yen;</i>全返总额：
                            <?php  if($fullbackgoods['type']>0) { ?>
                            <?php  if($goods['hasoption'] > 0) { ?>
                            <?php  if($fullbackgoods['minallfullbackallratio'] == $fullbackgoods['maxallfullbackallratio']) { ?>
                            <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>%
                            <?php  } else { ?>
                            <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>% ~ <?php  echo price_format($fullbackgoods['maxallfullbackallratio'],2)?>%
                            <?php  } ?>
                            <?php  } else { ?>
                            <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>%
                            <?php  } ?>
                            <?php  } else { ?>
                            <?php  if($goods['hasoption'] > 0) { ?>
                            <?php  if($fullbackgoods['minallfullbackallprice'] != $fullbackgoods['maxallfullbackallprice']) { ?>
                            &yen;<?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?>
                            <?php  } else { ?>
                            &yen;<?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?> ~ &yen;<?php  echo price_format($fullbackgoods['maxallfullbackallprice'],2)?>
                            <?php  } ?>
                            <?php  } else { ?>
                            &yen;<?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?>
                            <?php  } ?>
                            <?php  } ?>
                        </p>
                        <p style="display: block;"><i>&yen;</i>每天返：
                            <?php  if($fullbackgoods['type']>0) { ?>
                            <?php  if($goods['hasoption'] > 0) { ?>
                            <?php  if($fullbackgoods['minfullbackratio'] == $fullbackgoods['maxfullbackratio']) { ?>
                            <?php  echo price_format($fullbackgoods['minfullbackratio'],2)?>%
                            <?php  } else { ?>
                            <?php  echo price_format($fullbackgoods['minfullbackratio'],2)?>% ~ <?php  echo price_format($fullbackgoods['maxfullbackratio'],2)?>%
                            <?php  } ?>
                            <?php  } else { ?>
                            <?php  echo price_format($fullbackgoods['fullbackratio'],2)?>%
                            <?php  } ?>
                            <?php  } else { ?>
                            <?php  if($goods['hasoption'] > 0) { ?>
                            <?php  if($fullbackgoods['minfullbackprice'] == $fullbackgoods['maxfullbackprice']) { ?>
                            &yen;<?php  echo price_format($fullbackgoods['minfullbackprice'],2)?>
                            <?php  } else { ?>
                            &yen;<?php  echo price_format($fullbackgoods['minfullbackprice'],2)?> ~ &yen;<?php  echo price_format($fullbackgoods['maxfullbackprice'],2)?>
                            <?php  } ?>
                            <?php  } else { ?>
                            &yen;<?php  echo price_format($fullbackgoods['fullbackprice'],2)?>
                            <?php  } ?>
                            <?php  } ?>
                        </p>
                        <p style="display: block;"><i>&yen;</i>时间：
                            <?php  if($goods['hasoption'] > 0) { ?>
                            <?php  if($fullbackgoods['minday'] == $fullbackgoods['maxday']) { ?>
                            <?php  echo $fullbackgoods['minday'];?>
                            <?php  } else { ?>
                            <?php  echo $fullbackgoods['minday'];?> ~ <?php  echo $fullbackgoods['maxday'];?>
                            <?php  } ?>
                            <?php  } else { ?>
                            <?php  echo $fullbackgoods['day'];?>
                            <?php  } ?>天
                        </p>
                    </div>
                    <?php  if($fullbackgoods['startday']>0) { ?>
                    <div class="fullback-remark" style="line-height: inherit;">
                        确认收货<?php  echo $fullbackgoods['startday'];?>天后开始全返。如申请退款，则全返金额退还。
                    </div>
                    <?php  } ?>
                </div>
            </div>
            <?php  } ?>
        </div>
        <div class="btn btn-danger block fullbtn">确定</div>
    </div>
</div>

<?php  if($goods['type']==4) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('goods/wholesalePicker', TEMPLATE_INCLUDEPATH)) : (include template('goods/wholesalePicker', TEMPLATE_INCLUDEPATH));?>
<?php  } else { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('goods/picker', TEMPLATE_INCLUDEPATH)) : (include template('goods/picker', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>

<!--评论模版-->
<?php  if($getComments) { ?>
<script type='text/html' id='tpl_goods_detail_comments_list'>
    <div class="fui-cell-group fui-comment-group">
        <%each list as comment%>
        <div class="fui-cell">
            <div class="fui-cell-text comment ">
                <div class="info head">
                    <div class='img'><img src='<%comment.headimgurl%>'/></div>
                    <div class='nickname'><%comment.nickname%></div>

                    <div class="date"><%comment.createtime%></div>
                    <div class="star star1">
                        <span <%if comment.level>=1%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=2%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=3%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=4%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=5%>class="shine"<%/if%>>★</span>
                    </div>
                </div>
                <div class="remark"><%comment.content%></div>
                <%if comment.images.length>0%>
                <div class="remark img">
                    <%each comment.images as img%>
                    <div class="img"><img data-lazy="<%img%>" /></div>
                    <%/each%>
                </div>
                <%/if%>

                <%if comment.reply_content%>
                <div class="reply-content" style="background:#EDEDED;">
                    掌柜回复：<%comment.reply_content%>
                    <%if comment.reply_images.length>0%>
                    <div class="remark img">
                        <%each comment.reply_images as img%>
                        <div class="img"><img data-lazy="<%img%>" /></div>
                        <%/each%>
                    </div>
                    <%/if%>
                </div>
                <%/if%>
                <%if comment.append_content && comment.replychecked==0%>
                <div class="remark reply-title">用户追加评价</div>
                <div class="remark"><%comment.append_content%></div>
                <%if comment.append_images.length>0%>
                <div class="remark img">
                    <%each comment.append_images as img%>
                    <div class="img"><img data-lazy="<%img%>" /></div>
                    <%/each%>
                </div>
                <%/if%>
                <%if comment.append_reply_content%>
                <div class="reply-content" style="background:#EDEDED;">
                    掌柜回复：<%comment.append_reply_content%>
                    <%if comment.append_reply_images.length>0%>
                    <div class="remark img">
                        <%each comment.append_reply_images as img%>
                        <div class="img"><img data-lazy="<%img%>" /></div>
                        <%/each%>
                    </div>
                    <%/if%>
                </div>
                <%/if%>
                <%/if%>
            </div>
        </div>
        <%/each%>
    </div>
</script>
<!--评价-->
<script type='text/html' id='tpl_goods_detail_comments'>
    <div class="fui-cell-group fui-comment-group">

        <div class="fui-cell fui-cell-click">
            <div class="fui-cell-text desc">评价(<%count.all%>)</div>
            <div class="fui-cell-text desc label"><span><%percent%>%</span> 好评</div>
            <div class="fui-cell-remark"></div>

        </div>
        <%each list as comment%>
        <div class="fui-cell">

            <div class="fui-cell-text comment ">
                <div class="info">
                    <div class="star">
                        <span <%if comment.level>=1%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=2%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=3%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=4%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=5%>class="shine"<%/if%>>★</span>
                    </div>
                    <div class="date"><%comment.nickname%> <%comment.createtime%></div>
                </div>
                <div class="remark"><%comment.content%></div>
                <%if comment.images.length>0%>
                <div class="remark img">
                    <%each comment.images as img%>
                    <div class="img"><img data-lazy="<%img%>" height="50" /></div>
                    <%/each%>
                </div>
                <%/if%>
            </div>
        </div>
        <%/each%>
        <div style="text-align: center;margin: 0.8rem 0">
            <span class="btn btn-default-o external btn-show-allcomment">查看全部评价</span>
        </div>
    </div>


</script>
<?php  } ?>

<?php  } else { ?>

<div class='fui-content'>
    <div class='content-empty'>
        <i class='icon icon-searchlist'></i><br/> 商品已经下架，或者已经删除!<br/><a href="<?php  echo mobileUrl()?>" class='btn btn-default-o external'>到处逛逛</a>
    </div>
</div>
<?php  } ?>

<!--分享弹层-->
<div id='cover'>
    <div class='fui-mask-m visible'></div>
    <div class='arrow'></div>
    <div class='content'><?php  if(empty($share['goods_detail_text'])) { ?>请点击右上角<br/>通过【发送给朋友】<br/>邀请好友购买<?php  } else { ?><?php  echo $share['goods_detail_text'];?><?php  } ?></div>
</div>


<!--优惠券弹层完成-->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('sale/coupon/util/couponpicker', TEMPLATE_INCLUDEPATH)) : (include template('sale/coupon/util/couponpicker', TEMPLATE_INCLUDEPATH));?>
<script language="javascript">
    require(['biz/goods/detail'], function (modal) {
        modal.init({
            goodsid:"<?php  echo $goods['id'];?>",
            getComments : "<?php  echo $getComments;?>",
            seckillinfo: <?php  echo json_encode($seckillinfo)?>,
            attachurl_local:"<?php  echo $GLOBALS['_W']['attachurl_local'];?>",
            attachurl_remote:"<?php  echo $GLOBALS['_W']['attachurl_remote'];?>",
            coupons: <?php  echo json_encode($coupons)?>,
            new_temp: <?php  echo intval($new_temp)?>,
            liveid: <?php  echo intval($liveid)?>,
            close_preview: <?php  echo intval($close_preview)?>//是否关闭商品详情图片的放大预览
        });
    });

    require(['../addons/ewei_shopv2/plugin/diypage/static/js/mobile.js'], function(diypagemodal){
        diypagemodal.init();
    });
</script>
</div>

<!--分享二维码弹层-->

<div id="alert-picker">
    <script type="text/javascript" src="../addons/ewei_shopv2/static/js/dist/jquery/jquery.qrcode.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $(".alert-qrcode-i").html('')
            $(".alert-qrcode-i").qrcode({
                typeNumber: 0,      //计算模式
                correctLevel: 0,//纠错等级
                text:"<?php  echo mobileUrl('goods/detail', array('id'=>$goods['id'],'mid'=>$mid),true)?>"/*<?php  echo $_W['siteroot'].'app/'.mobileUrl('goods/detail', array('id'=>$goods['id']))?>*/
            });
        });
    </script>


    <div id="alert-mask"></div>
    <?php  if($commission_data['codeShare'] == 1) { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;">
            <i class="alert-close alert-close1 icon icon-close"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt="">
            </div>
        </div>
    </div>
    <?php  } else if($commission_data['codeShare'] == 2) { ?>
    <div class="alert-content">
        <div class="alert2">
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt="">
            </div>
        </div>
    </div>
    <?php  } else { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;background: #f5f4f9;border:none;-webkit-border-radius: 0.3rem;border-radius: 0.3rem;top:2rem;">
            <i class="alert-close alert-close1 icon icon-close" style="right: -0.7rem;top: -0.8rem;background: #e1040d;border:none;z-index:10"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" class="alert-goods-img" alt="">
            </div>

        </div>
    </div>
    <?php  } ?>
</div>

<div class="goods-layer bottom-buttons">
    <div class="inner">
        <div class="goods-content">
            <div class="goods-title">温馨提示</div>
            <div class="goods-con"><?php  echo $hint;?></div>
        </div>
        <div class="goods-btn buybtn"  data-time="<?php  if(!empty($access_time)) { ?>access_time<?php  } else if(!empty($timeout)) { ?>timeout<?php  } ?>" data-timeout="true">
            确定
        </div>
    </div>
</div>

<style type="text/css">
    .share-text1{text-align: center;padding:0.5rem 0.5rem 0;font-size:0.6rem;color:#666;line-height: 1rem;}
    .share-text2{text-align: center;padding:0 0.5rem 0.5rem;font-size:0.6rem;color:#666;line-height: 1rem;}
</style>

<?php  if(!empty($diypage['diygotop'])) { ?>
<?php  $this->diyGotop(true, false, $diypage['merch'])?>
<?php  } ?>

<?php  if(!empty($diypage['diylayer'])) { ?>
<?php  echo $this->diyLayer(false, false, $goods['merchid'])?>
<?php  } ?>

<?php  if(!empty($diypage['danmu'])) { ?>
<?php  $this->diyDanmu(true)?>
<?php  } ?>

<?php  if(!empty($startadv)) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diypage/startadv', TEMPLATE_INCLUDEPATH)) : (include template('diypage/startadv', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>