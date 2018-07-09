<?php defined('IN_IA') or exit('Access Denied');?>
    <?php  if($value['data_type'] == 0 || $value['data_type'] == 1 || $value['data_type'] == 2 || $value['data_type'] == 6) { ?>
    <?php  echo str_replace("\n","<br/>",$datas[$key])?>

    <?php  } else if($value['data_type'] == 3) { ?>
    <?php  if(!empty($datas[$key])) { ?>
    <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
    <?php  echo $v1?>
    <?php  } } ?>
    <?php  } ?>

    <?php  } else if($value['data_type'] == 5) { ?>
    <?php  if(!empty($datas[$key])) { ?>
        <?php  if(empty($show_style)) { ?>
            <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
            <a target="_blank" href="<?php  echo tomedia($v1)?>"><img style='height:25px;padding:1px;border:1px solid #ccc' src="<?php  echo tomedia($v1)?>"></a>
            <?php  } } ?>
        <?php  } else if($show_style==1) { ?>
            <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
            <a target="_blank" href="<?php  echo tomedia($v1)?>"><img style='height:25px;padding:1px;border:1px solid #ccc' src="<?php  echo tomedia($v1)?>"></a><br>
            <?php  } } ?>
        <?php  } else if($show_style==2) { ?>
            <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
            <a target='_blank' href='<?php  echo tomedia($v1)?>'><img style='height:25px;padding:1px;border:1px solid #ccc' src='<?php  echo tomedia($v1)?>'></a>
            <?php  break;?>
            <?php  } } ?>

            <a data-toggle='popover' data-html='true' data-placement='right'
               data-content="<table style='width:100%;'>
                                <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
                                <tr>
                                <td  style='border:none;text-align:right;padding:5px;'>
                                    <a target='_blank' href='<?php  echo tomedia($v1)?>'><img style='width:100px;;padding:1px;border:1px solid #ccc' src='<?php  echo tomedia($v1)?>'></a>
                                </td>
                                </tr>
                                <?php  } } ?>
                        </table>
           ">查看全部</a>
        <?php  } ?>
    <?php  } ?>

    <?php  } else if(($value['data_type'] == 7 || $value['data_type'] == 11 || $value['data_type'] == 14)) { ?>
    <?php  echo $datas[$key]?>

    <?php  } else if(($value['data_type'] == 8 || $value['data_type'] == 12)) { ?>
    <?php  if(!empty($datas[$key])) { ?>
    <?php  if(is_array($datas[$key])) { foreach($datas[$key] as $k1 => $v1) { ?>
    <?php  echo $v1?>
    <?php  } } ?>
    <?php  } ?>

    <?php  } else if($value['data_type'] == 9) { ?>
    <?php echo $datas[$key]['province']!='请选择省份'?$datas[$key]['province']:''?>-<?php echo $datas[$key]['city']!='请选择城市'?$datas[$key]['city']:''?><?php  if(!empty($datas[$key]['area'])) { ?>-<?php  echo $datas[$key]['area']?><?php  } ?>

    <?php  } else if($value['data_type'] == 10) { ?>
    <?php  echo $datas[$key]['name1']?>
    <br/>
    <?php  echo $datas[$key]['name2']?>
    <?php  } ?>
