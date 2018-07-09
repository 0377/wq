<?php defined('IN_IA') or exit('Access Denied');?><div style='max-height:500px;overflow:auto;min-width:850px;'>
<table class="table table-hover" style="min-width:850px;">
    <thead>
        <th style='width:40px;'></th>
        <th>优惠券</th>
        <th>使用条件</th>
        <th>优惠</th>
        <th style="width: 100px;">剩余数量</th>
        <th style="width:120px;">领券中心领取</th>
        <th style="width: 60px;">选择</th>
    </thead>
    <tbody>   
        <?php  if(is_array($ds)) { foreach($ds as $row) { ?>
        <tr>
			<td><img src="<?php  echo $row['thumb'];?>" style="width:30px;height:30px;padding:1px solid #ccc" onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'"/></td>
            <td><?php  if($row['coupontype']==0) { ?>
				  <label class='label label-success'>购物</label>
						  <?php  } else { ?>
						  <label class='label label-warning'>充值</label>
					 <?php  } ?>
					 <?php  if(!empty($row['catid'])) { ?>
					 <label class='label label-primary'><?php  echo $category[$row['catid']]['name'];?></label>
					 <?php  } ?>[<?php  echo $row['id'];?>]<?php  echo $row['couponname'];?></td>
	  <td><?php  if($row['enough']>0) { ?><?php  if($row['coupontype']==0) { ?>消费<?php  } else { ?>充值<?php  } ?>满<?php  echo $row['enough'];?>元<?php  } else { ?>不限制<?php  } ?></td>
	  <td>   <?php  if($row['backtype']==0) { ?>
						  立减 <?php  echo $row['deduct'];?> 元
						  <?php  } else if($row['backtype']==1) { ?>
						  打 <?php  echo $row['discount'];?> 折
						  <?php  } else if($row['backtype']==2) { ?>
						  <?php  if($row['backmoney']>0) { ?>返 <?php  echo $row['backmoney'];?> 余额;<?php  } ?>
						  <?php  if($row['backcredit']>0) { ?>返 <?php  echo $row['backcredit'];?> 积分;<?php  } ?>
						  <?php  if($row['backredpack']>0) { ?>返 <?php  echo $row['backredpack'];?> 红包;<?php  } ?>
						  <?php  } ?></td>
	  <td><?php  echo $row['last'];?></td>
            <td><?php  if($row['gettype']==1) { ?>可以<?php  } else { ?>不可以<?php  } ?></td>
            <td style="width:80px;"><a href="javascript:;" onclick='biz.selector.set(this, <?php  echo json_encode($row)?>)'>选择</a></td>
        </tr>
        <?php  } } ?>
        <?php  if(count($ds)<=0) { ?>
        <tr> 
            <td colspan='5' align='center'>未找到任何优惠券, 点击 <a href="<?php  echo webUrl('sale/coupon/add')?>" target='_blank'>【创建购物优惠券】</a> <a href="<?php  echo webUrl('sale/coupon/add',array('type'=>1))?>" target='_blank'>【创建充值优惠券】</a></td>
        </tr>
        <?php  } ?>
    </tbody>
</table>
</div>