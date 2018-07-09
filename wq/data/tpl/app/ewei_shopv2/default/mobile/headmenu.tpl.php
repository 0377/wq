<?php defined('IN_IA') or exit('Access Denied');?><div class="head-menu-mask"></div>
<div class="head-menu">
    <?php  if(empty($_W['shopset']['wap']['inh5app'])) { ?>
        <nav data-type="share"><i class="icon icon-share"></i> 分享</nav>
    <?php  } ?>
    <nav data-type="reload"><i class="icon icon-refresh"></i> 刷新</nav>
    <nav data-type="eraser"><i class="icon icon-eraser"></i> 清除缓存</nav>
    <?php  if(is_h5app() && !is_ios()) { ?>
        <nav data-type="exitapp"><i class="icon icon-roundclose"></i> 退出商城</nav>
    <?php  } ?>
</div>