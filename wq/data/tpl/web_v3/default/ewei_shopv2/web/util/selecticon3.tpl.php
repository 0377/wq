<?php defined('IN_IA') or exit('Access Denied');?><style type="text/css">#selectIcon3 .modal-dialog {width: 750px;}#selectIcon3 .modal-body {padding: 10px;}#selectIcon3 .main {height: 420px; overflow-y: auto;}#selectIcon3 .main nav {height: 70px; width: 12.5%; margin: 5px; display: block; float: left; cursor: pointer;}#selectIcon3 .main nav .icox {font-size: 30px; height: 38px; width: 100%; display: block; text-align: center; line-height: 42px;}#selectIcon3 .main nav .text {font-size: 10px; height: 32px; width: 100%; display: block; text-align: center; line-height: 16px; overflow: hidden}#selectIcon3 .main nav:hover {background: #f6f6f6;}#selectIcon3 .empty-data {line-height: 250px; text-align: center; color: #666; font-size: 14px}</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">选择图标</h4>
        </div>
        <div class="modal-body">
            <?php  if(empty($list)) { ?>
            <div class="empty-data">数据读取失败</div>
            <?php  } else { ?>
            <div class="main">
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <nav data-class="<?php  echo $item;?>"><span class="icox <?php  echo $item;?>"></span><span class="text"><?php  echo str_replace('icox-', '', $item)?></span></nav>
                <?php  } } ?>
            </div>
            <?php  } ?>
        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button">关闭</button>
        </div>
    </div>
</div>