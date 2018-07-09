<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .shop-index-page .menu {height: auto; background: #fff;}
    .shop-index-page .menu .item {height: auto; width: 1%; padding: 0.5rem 0; margin: 0; font-size: 0.65rem; color: #636363; text-align: center; display: table-cell;position: relative;}
    .shop-index-page .menu .item:after  {font-size: 0.65rem;position: absolute;content: '';left:0;top:0.5rem;bottom:0.5rem;border-left:1px solid #d9d9d9;}
    .content-empty{padding:1rem 0;margin:0;}
</style>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/common.css?v=<?php  echo time()?>">

<div class="fui-page shop-index-page">
    <div class="fui-header">
        <div class="fui-header-left">
            <!--<a class="back"></a>-->
        </div>
        <div class="title">
            <form action="<?php  echo mobileUrl('live/list')?>" method="post">
                <div class="fui-searchbar bar">
                    <div class="searchbar center">
                        <input type="submit" class="searchbar-cancel searchbtn" value="搜索" />
                        <div class="search-input">
                            <i class="icon icon-search"></i>
                            <input type="search" placeholder="搜索您感兴趣的内容..." class="search" name="keywords">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="fui-header-right"></div>
    </div>
    <div class="fui-content navbar" style="padding-bottom:0;">
        <?php  if(!empty($advs)) { ?>
            <div class='fui-swipe' >
                <div class='fui-swipe-wrapper'>
                    <?php  if(is_array($advs)) { foreach($advs as $item) { ?>
                    <div class='fui-swipe-item' <?php  if(!empty($item['link'])) { ?>onclick="location.href='<?php  echo $item['link'];?>'"<?php  } ?>><img src="<?php  echo tomedia($item['thumb'])?>" /></div>
                <?php  } } ?>
            </div>
            <div class='fui-swipe-page'></div>
        </div>
        <?php  } ?>
        <div class="menu">
            <a class="item external" href="<?php  echo mobileUrl('live/follow')?>">
                <span class="text">我的订阅</span>
            </a>
            <a class="item external" href="<?php  echo mobileUrl('live/view')?>">
                <span class="text">观看记录</span>
            </a>
        </div>
        <div class="fui-cell-group fui-comment-group">
            <div class="fui-cell fui-cell-click">
                <div class="fui-cell-text desc">热门分类</div>
                <div class="fui-cell-text desc label" onclick="javascript:window.location.href='<?php  echo mobileUrl('live/category')?>'" style="text-align: right;">
                    全部
                </div>
                <div class="fui-cell-remark"></div>
            </div>
            <div class="fui-list live-category-list">
                <div class="fui-list-inner">
                    <div class="live-category-list-inner">
                        <?php  if(is_array($categorys)) { foreach($categorys as $row) { ?>
                        <a href="<?php  echo mobileUrl('live/list',array('cate'=>$row['id']))?>" class="external">
                            <p style="width:4.5rem;"><img src="<?php  echo tomedia($row['thumb'])?>" alt="<?php  echo $row['name'];?>"></p>
                            <span><?php  echo $row['name'];?></span>
                        </a>
                        <?php  } } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="live-list">
            <?php  if(is_array($recommend)) { foreach($recommend as $row) { ?>
            <div class="fui-list">
                <div class="fui-list-media">
                    <a href="<?php  echo mobileUrl('live/room',array('id'=>$row['id']))?>" data-nocache="true" class="external">
                        <?php  if($row['living']==1) { ?>
                            <span class="live-living">
                                <i></i>直播中
                            </span>
                        <?php  } ?>
                        <img src="<?php  echo tomedia($row['thumb'])?>" alt="<?php  echo $row['title'];?>">
                    </a>
                </div>
                <div class="fui-list-inner">
                    <a href="<?php  echo mobileUrl('live/room',array('id'=>$row['id']))?>" data-nocache="true" class="external">
                        <h3><?php  echo $row['title'];?></h3>
                    </a>
                    <p><?php  echo date('Y-m-d H:i:s', $row['livetime'])?> 开播</p>
                    <div class="live-list-subscribe">
                        <span>订阅人数 <?php  echo $row['subscribe'];?></span>
                        <span>
                            <?php  if($row['is_subscribe']>0) { ?>
                                <a href="javascript:void(0);" class="live-subscribe-a live-subscribe-default-a disabled" data-id="<?php  echo $row['id'];?>"  data-subscribe="<?php  echo $row['is_subscribe'];?>">取消订阅</a>
                            <?php  } else { ?>
                                <a href="javascript:void(0);" class="live-subscribe-a live-subscribe-default-a" data-id="<?php  echo $row['id'];?>" data-subscribe="<?php  echo $row['is_subscribe'];?>">点击订阅</a>
                            <?php  } ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php  } } ?>
        </div>
        <div class='content-loading' style="display: none;">
            <span class='fui-preloader'></span>
            <span class='text'>正在加载...</span>
        </div>
        <div class='content-empty'>
            已经没有更多了
        </div>
        <div class="live-hot-list">
            <div id="container">

            </div>
            <div style="clear:both;"></div>
        </div>
    <script id='tpl_live_list' type='text/html'>
        <%each list as row%>
        <div class="live-hot-list-room">
            <div class="live-hot-list-inner">
                <a href="<?php  echo mobileUrl('live/room');?>&id=<%row.id%>" data-nocache="true" class="live-hot-list-img external">
                    <%if row.living==1%>
                    <span class="live-living">
                        <i></i>直播中
                    </span>
                    <%/if%>
                    <img src="<%row.thumb%>" alt="<%row.title%>">
                </a>
                <a href="<?php  echo mobileUrl('live/room');?>&id=<%row.id%>" class="external" data-nocache="true"><h3><%row.title%></h3></a>
                <p><%row.livetime%> 开播</p>
                <div class="live-list-subscribe" style="padding:0 0.3rem;">
                    <span>订阅人数 <%row.subscribe%></span>
                    <span>

                        <%if row.is_subscribe > 0%>
                            <a href="javascript:void(0);" class="live-subscribe-a live-subscribe-default-a disabled" data-id="<%row.id%>"  data-subscribe="<%row.is_subscribe%>">取消订阅</a>
                        <%else%>
                            <a href="javascript:void(0);" class="live-subscribe-a live-subscribe-default-a" data-id="<%row.id%>" data-subscribe="<%row.is_subscribe%>">点击订阅</a>
                        <%/if%>

                    </span>
                </div>
            </div>
        </div>
        <%/each%>
    </script>

    </div>

</div>
<script language='javascript'>
    require(['../addons/ewei_shopv2/plugin/live/static/js/index.js'], function (modal) {
        modal.init({page:1});
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('live/_menu', TEMPLATE_INCLUDEPATH)) : (include template('live/_menu', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>