<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/room.css?v=<?php  echo time()?>">
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/video.css?v=<?php  echo time()?>">
<style>
    <?php  if($fullscreen) { ?>
    .fullscreen .block-video .video {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    <?php  } else { ?>
    .block-video .video {
        height: 100%;
        width: 100%;
        object-position: 0 50%;
        object-fit: fill;
    }
    <?php  } ?>
</style>
<div class="fui-page">
    <div class="fui-content <?php  if($fullscreen) { ?>fullscreen<?php  } ?>">

        <?php  if(is_mobile() && !is_ios()) { ?>
        <div class="block-title" ><?php  echo $_W['shopset']['shop']['name'];?></div>
        <?php  } ?>
        <div class="prompt">
            <div class="live-tips loading">
                <div class="inner">
                    <div class="text">加载直播间配置中</div>
                    <div class="date"></div>
                </div>
            </div>

            <div class="live-tips play btn-play">
                <div class="inner">
                    <div class="text"><i class="icon icon-iconfontplay2"></i></div>
                </div>
            </div>

            <div class="live-tips failed">
                <div class="inner btn-reconnect">
                    <div class="text">与通讯服务器连接失败</div>
                    <div class="date">点击此处重新连接</div>
                </div>
            </div>

            <div class="live-tips stop">
                <div class="inner">
                    <div class="text">直播还未开始</div>
                    <div class="date">预计<?php  echo date('Y-m-d H:i:s', $room['livetime'])?>开播</div>
                </div>
            </div>

            <div class="live-tips pause">
                <div class="inner">
                    <div class="text">管理员暂停了直播</div>
                    <div class="date">请稍等片刻</div>
                </div>
            </div>
        </div>
        <div class="block-video" style="background-image: url('<?php  echo $poster;?>');">

            <video id="player" class="video-js vjs-default-skin vjs-big-play-centered video" src="<?php  echo $video;?>" x-webkit-airplay="allow" poster="" preload="auto" webkit-playsinline playsinline x5-video-player-type="h5" x5-video-player-fullscreen="true">
                <source src="<?php  echo $video;?>"  type="application/x-mpegURL">
                <source src="<?php  echo $video;?>"  type="application/x-mpegURL">
            </video>

            <div class="block-info">
                <div class="live-info">
                    <div class="face">
                        <img src="<?php  echo tomedia($room['thumb'])?>" />
                    </div>
                    <div class="text"><?php  echo $room['title'];?></div>
                </div>
            </div>

            <div class="block-online">
                <div class="online"><span id="online">1</span>人观看</div>
            </div>
        </div>

        <?php  if(!$fullscreen) { ?>
        <div class="block-tab">
            <?php  if(is_array($nestables)) { foreach($nestables as $row) { ?>
            <a href="<?php  if($menu[$row]['type']==1) { ?><?php  echo $menu[$row]['url'];?><?php  } else { ?>javascript:void(0);<?php  } ?>" data-tab="<?php  echo $row;?>" class="<?php  if($row=='interaction') { ?>active<?php  } ?>" <?php  if(($row=='goods' && empty($room_goods)) || ($row=='goods' && $menu['goods']['isshow']==1) || ($row=='introduce' && $menu['introduce']['isshow']==1) ) { ?>style="display:none;"<?php  } ?>>
                <?php  if(empty($menu[$row]['name'])) { ?>
                    <?php  if($row=='interaction') { ?>
                        直播间
                    <?php  } else if($row=='goods') { ?>
                        商品
                    <?php  } else if($row=='introduce') { ?>
                        介绍
                    <?php  } else { ?>
                        自定义
                    <?php  } ?>
                <?php  } else { ?>
                    <?php  echo $menu[$row]['name'];?>
                <?php  } ?>
            </a>
            <?php  } } ?>
        </div>
        <?php  } ?>

        <div class="block-content" style="z-index: 11">
            <div class="layer-enter">
                <div class="name">...</div>
                <div class="text">进入直播间</div>
            </div>

            <div class="layer-at layer">
                <div class="layer-close"></div>
                <div class="at-text">管理员@了你</div>
                <div class="at-icon">@</div>
            </div>

            <div class="tab-content" data-tab="interaction" style="display: block;">
                <?php  if(is_array($records)) { foreach($records as $record) { ?>
                <?php  if($record['type']=='goods') { ?>
                    <a class="msg <?php  if($fullscreen&&$record['type']=='goods') { ?>nopadding<?php  } ?>" data-msgid="<?php  echo $record['id'];?>" data-nocache="true" href="<?php  echo mobileUrl('goods/detail', array('id'=>$record['goodsid'], 'liveid'=>$roomid))?>">
                        <div class="content">
                            <div class="goods">
                                <div class="thumb"><img src="<?php  echo tomedia($record['goodsthumb'])?>"></div>
                                <div class="info">
                                    <div class="title"><?php  echo $record['goodstitle'];?></div>
                                    <div class="price">
                                        <div class="num">￥<?php  echo $record['goodsprice'];?></div>
                                        <div class="btn-buy">购买</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php  } else { ?>
                    <div class="msg <?php  if($fullscreen&&(!empty($record['status']) || $record['type']=='redpack' || $record['type']=='coupon' || $record['type']=='goods')) { ?>nopadding<?php  } ?>" data-msgid="<?php  echo $record['id'];?>">
                        <?php  if(!empty($record['status'])) { ?>
                        <div class="tip">
                            <div class="text"><?php  echo $record['text'];?></div>
                        </div>
                        <?php  } else { ?>
                        <?php  if($record['type']=='text' || $record['type']=='image' || !$fullscreen) { ?>
                        <div class="nickname <?php  if($record['mid']==$member['id']) { ?>self<?php  } ?>" data-uid="<?php  echo $record['mid'];?>" data-nickname="<?php  echo $record['nickname'];?>"><?php  echo $record['nickname'];?><?php  if($record['mid']==$member['id']) { ?>(你)<?php  } ?>:&nbsp;</div>
                        <?php  } ?>
                        <div class="content">
                                <?php  echo $record['text'];?>
                                <?php  if($record['mid']==$member['id']) { ?><span class="btn-repeal"><i class="icon icon-chexiao"></i></span><?php  } ?>
                        </div>
                        <?php  } ?>
                    </div>
                <?php  } ?>
                <?php  } } ?>
            </div>
            <?php  if(!$fullscreen) { ?>
                <div class="tab-content" data-tab="goods">
                    <?php  if(is_array($room_goods)) { foreach($room_goods as $row) { ?>
                    <a class="goods-item" href="<?php  echo mobileUrl('goods/detail',array('id'=>$row['id'], 'liveid'=>$roomid))?>">
                        <div class="thumb">
                            <img src="<?php  echo tomedia($row['thumb'])?>" alt="<?php  echo $row['title'];?>" />
                        </div>
                        <div class="info">
                            <div class="title"><?php  echo $row['title'];?></div>
                            <div class="price">
                                <div class="num">
                                    <span class="label">直播价</span>
                                    <span class="bigprice">&yen;<?php  echo price_format($row['liveprice'],2)?></span>
                                    <?php  if($row['marketprice']>$row['liveprice']) { ?>
                                    <span class="product">&yen;<?php  echo price_format($row['marketprice'],2)?></span>
                                    <?php  } ?>
                                </div>
                                <div class="btn-buy">立即去购买</div>
                            </div>
                        </div>
                    </a>
                    <?php  } } ?>
                </div>
                <div class="tab-content" data-tab="introduce">
                    <div class="shop-info">
                        <div class="logo">
                            <img src="<?php  echo tomedia($shop['logo'])?>" alt="<?php  echo $shop['name'];?>" />
                        </div>
                        <div class="info"><?php  echo $shop['name'];?></div>
                        <a class="btn-enter" href="<?php  echo mobileUrl()?>" data-nocache="true">进入商城</a>
                    </div>
                    <div class="room-info">
                        <p>直播间: <?php  echo $room['title'];?></p>
                        <p>直播时间: <?php  echo date('Y-m-d H:i:s', $room['livetime'])?></p>
                    </div>
                    <div class="room-info">
                        <p>直播间介绍: <?php  if(!empty($room['introduce'])) { ?><?php  echo htmlspecialchars_decode($room['introduce'])?><?php  } else { ?>暂无介绍<?php  } ?></p>
                    </div>
                </div>
                <div class="tab-content" data-tab="customname1" style="background: #fff;padding:0.5rem;"><?php  if(!empty($menu['customname1']['introduce'])) { ?><?php  echo htmlspecialchars_decode($menu['customname1']['introduce'])?><?php  } else { ?>暂无介绍<?php  } ?></div>
                <div class="tab-content" data-tab="customname2" style="background: #fff;padding:0.5rem;"><?php  if(!empty($menu['customname2']['introduce'])) { ?><?php  echo htmlspecialchars_decode($menu['customname2']['introduce'])?><?php  } else { ?>暂无介绍<?php  } ?></div>
                <div class="tab-content" data-tab="customname3" style="background: #fff;padding:0.5rem;"><?php  if(!empty($menu['customname3']['introduce'])) { ?><?php  echo htmlspecialchars_decode($menu['customname3']['introduce'])?><?php  } else { ?>暂无介绍<?php  } ?></div>
                <div class="tab-content" data-tab="customname4" style="background: #fff;padding:0.5rem;"><?php  if(!empty($menu['customname4']['introduce'])) { ?><?php  echo htmlspecialchars_decode($menu['customname4']['introduce'])?><?php  } else { ?>暂无介绍<?php  } ?></div>
            <?php  } ?>
        </div>

        <?php  if(!empty($room['notice'])) { ?>
        <div class="block-notice">
            <a class="inner" href="<?php echo !empty($room['notice_url'])? $room['notice_url']: 'javascript:;'?>" data-nocache="true">直播间公告：<?php  echo $room['notice'];?></a>
        </div>
        <?php  } ?>

        <div class="block-icon">
            <?php  if($fullscreen) { ?>
                <div class="btn-content btn-hide" ></div>
                <div class="btn-content btn-show"></div>
            <?php  } ?>
            <?php  if(!empty($room_coupon) && $room['iscoupon']>0) { ?>
                <div class="btn-gift btn-gifts" ></div>
            <?php  } ?>
            <div class="btn-like live-info"></div>
            <?php  if(p('invitation') && !empty($room['invitation_id'])) { ?>
            <a  href="<?php  echo mobileUrl('invitation',array('id'=>$room['invitation_id'],'roomid'=>$room['id'],'type'=>'live'))?>" class="btn-share external"></a>
            <?php  } ?>
        </div>

        <!---------------------------------------------------------------------------------------------------------->

        <div class="layer-mask"></div>

        <!-- 店铺信息+关注弹窗 -->
        <div class="layer-roominfo layer">
            <div class="layer-close"></div>
            <div class="room-face">
                <img src="<?php  echo tomedia($room['thumb'])?>" alt="<?php  echo $shop['name'];?>" />
            </div>
            <div class="room-title"><?php  echo $room['title'];?></div>
            <div class="room-info">
                <p>直播间：<?php  echo $room['title'];?></p>
                <p>直播时间：<?php  echo date('Y-m-d H:i:s', $room['livetime'])?></p>
            </div>
            <?php  if(!empty($room['introduce'])) { ?>
                <div class="room-info">直播间介绍：<?php  echo htmlspecialchars_decode($room['introduce'])?></div>
            <?php  } ?>
            <?php  if(!empty($followqrcode)) { ?>
            <div class="room-qrcode">
                <img src="<?php  echo $followqrcode;?>" />
            </div>
            <?php  } ?>
        </div>

        <!-- 礼物弹窗 -->
        <div class="layer-gifts layer" style="margin-top:-8.7rem;">
            <div class="layer-close"></div>
            <div class="inner">
                <?php  if(is_array($room_coupon)) { foreach($room_coupon as $coupon) { ?>
                <div class="coupon-item roomcoupon <?php  if($coupon['iscoupon'] === false) { ?>disabled<?php  } ?>" data-roomid="<?php  echo $room['id'];?>" data-couponid="<?php  echo $coupon['couponid'];?>" data-livetime="<?php  echo $room['livetime'];?>">
                    <div class="left">
                        <span><?php  echo $coupon['display_title'];?></span>
                    </div>
                    <div class="center">
                        <div class="title"><?php  echo $coupon['couponname'];?></div>
                        <div class="subtitle">满<?php  echo $coupon['enough'];?>元可用</div>
                    </div>
                    <div class="right"></div>
                    <div class="live-mask"><?php  if($coupon['iscoupon'] === false) { ?><?php  echo $coupon['couponmessage'];?><?php  } ?></div>
                </div>
                <?php  } } ?>
            </div>
        </div>

        <!-- 红包弹窗 -->
        <div class="layer-redpack layer">
            <div class="layer-close"></div>
            <div class="redpack-title">红包来了，速抢速抢速抢速抢速抢</div>
            <!-- 抢之前显示 -->
            <div class="redpack-bg"></div>
            <div class="redpack-draw">抢</div>
            <!-- 抢之后显示 -->
            <div class="redpack-info">
                <div class="price"><span>￥</span>0.00</div>
                <div class="type"><?php  echo $moneytext;?>红包</div>
            </div>
            <div class="redpack-list">
                <div class="title">领取记录：</div>
                <div class="inner"></div>
            </div>
        </div>

        <!-- 优惠券弹层 -->
        <div class="layer-coupon layer">
            <div class="layer-close"></div>
            <div class="coupon-title">很遗憾，没抢到</div>
            <div class="coupon-fail-title">优惠券飞走了</div>
            <div class="coupon-inner">
                <div class="coupon-bg">
                    <div class="coupon-block">
                        <div class="coupon-gold"></div>
                        <div class="coupon-gift"></div>
                        <div class="price">&yen; <span class="money">0</span></div>
                        <div class="desc">无使用条件</div>
                    </div>
                </div>
            </div>
            <div class="coupon-btn">
                <a class="btn btn-blue" href="<?php  echo mobileUrl('sale/coupon/my')?>" data-nocache="true">去查看</a>
                <a class="btn btn-goon" href="javascript:void(0);">继续领取</a>
                <div class="btn btn-yellow">去使用</div>
            </div>
            <div class="coupon-fail"></div>
        </div>

        <!--商品弹层 -->
        <div class="layer-goods layer">
            <div class="layer-close"></div>
            <div class="inner">
                <?php  if(is_array($room_goods)) { foreach($room_goods as $row) { ?>
                <a class="goods-item" href="<?php  echo mobileUrl('goods/detail',array('id'=>$row['id'], 'liveid'=>$roomid))?>" data-nocache="true">
                    <div class="thumb">
                        <img src="<?php  echo tomedia($row['thumb'])?>" alt="<?php  echo $row['title'];?>" />
                    </div>
                    <div class="info">
                        <div class="title"><?php  echo $row['title'];?></div>
                        <div class="price">
                            <div class="num">
                                <span class="label">直播价</span>
                                <span class="bigprice">&yen;<?php  echo price_format($row['liveprice'],2)?></span>
                                <?php  if($row['marketprice']>$row['liveprice']) { ?>
                                <span class="product">&yen;<?php  echo price_format($row['marketprice'],2)?></span>
                                <?php  } ?>
                            </div>
                            <div class="btn-buy">立即去购买</div>
                        </div>
                    </div>
                </a>
                <?php  } } ?>
            </div>
        </div>

        <div class="fui-footer block-input">
            <?php  if(!empty($room_goods)) { ?>
                <div class="btn-goods"></div>
            <?php  } ?>
            <div class="input">
                <div class="input-place">服务器连接中...</div>
                <input id="input" placeholder="跟大家说点什么吧..."  style="display: none" />
                <div class="btn-emoji"></div>
            </div>
            <?php  if(!$fullscreen) { ?>
            <div class="btn-plus"></div>
            <?php  } ?>
            <div class="btn-like"></div>
            <div class="btn-send">发送</div>
        </div>

        <?php  if(!$fullscreen) { ?>
        <div class="block-plus">
            <div class="inner">
                <a class="item btn-refresh">
                    <div class="item-icon">
                        <i class="icon icon-refresh"></i>
                    </div>
                    <div class="item-text">刷新</div>
                </a>
                <a class="item external" href="<?php  echo mobileUrl()?>">
                    <div class="item-icon">
                        <i class="icon icon-home"></i>
                    </div>
                    <div class="item-text">商城</div>
                </a>
                <a class="item external" href="<?php  echo mobileUrl('member')?>">
                    <div class="item-icon">
                        <i class="icon icon-people"></i>
                    </div>
                    <div class="item-text">会员中心</div>
                </a>
                <?php  if(p('invitation') && !empty($room['invitation_id'])) { ?>
                <a class="item external" href="<?php  echo mobileUrl('invitation',array('id'=>$room['invitation_id'],'roomid'=>$room['id'],'type'=>'live'))?>">
                    <div class="item-icon">
                        <i class="icon icon-dingdan"></i>
                    </div>
                    <div class="item-text">邀请卡</div>
                </a>
                <?php  } ?>
            </div>
        </div>
        <?php  } ?>

        <div class="block-emoji">
            <?php  if(is_array($emojiList)) { foreach($emojiList as $i => $emojiItem) { ?>
                <?php  if(!empty($emojiItem)) { ?>
                    <div class="item" data-index="<?php  echo $i;?>" title="<?php  echo $emojiItem;?>"></div>
                <?php  } ?>
            <?php  } } ?>
        </div>

        <script type="text/javascript">
            require(['../addons/ewei_shopv2/plugin/live/static/js/live.js'],function(modal){
                modal.init({wsConfig: <?php  echo $wsConfig;?>});
            });
        </script>
    </div>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>