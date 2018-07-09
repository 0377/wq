<?php defined('IN_IA') or exit('Access Denied');?><style>

    .content .table{border-collapse:collapse;border:none;}
    .content .table tr{border-collapse: collapse;border-left:1px solid #ededed;border-right:1px solid #ededed;}
    .content .table > tbody > tr > td{border-top:1px solid #ededed;border-left:0;border-right:0;}
</style>
<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <?php  if(count($ds)>0) { ?>
    <table class="table" border="1" cellspacing="0" width="100%" cellpadding="0">
        <thead style="background: #f7f7f7;">
        <tr>
            <td>商品</td>
            <th style="width:100px;text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody id="param-items" class="ui-sortable">
        <?php  if(is_array($ds)) { foreach($ds as $row) { ?>
        <tr>
          <td><img src='<?php  echo tomedia($row['thumb'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> <?php  echo $row['title'];?></td>
          <td style="width: 80px"><a  class="label label-primary" href="javascript:;" onclick='biz.selector.set(this, <?php  echo json_encode($row);?>)'>选择</a></td>
        </tr>
        <?php  } } ?>
    </tbody>
</table>
    <?php  } else { ?>
    <div style="text-align:center;width:100%;">
        未找到商品
    </div>
    <?php  } ?>
    <div style="text-align:right;width:100%;">
        <?php  echo $pager;?>
    </div>
</div>