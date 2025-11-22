<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$admin_email = 'abdul@zesttour.com';
//$admin_email = 'swapnil91991@gmail.com';
extract($_REQUEST);
if ($req == 1) 
{
    $limit = 200;

    if ($page > 1) {
        $start = ($page - 1) * $limit;
    } else {
        $start = 0;
    }
    $count = $start + 1;
    $user_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `user` ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $user_data[$rs2['id']] =  $rs2;
    }
    $customer_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $customer_data[$rs2['id']] =  $rs2;
    }
    $reminder = array();
    $sq2 = mysqli_query($connect, "SELECT DISTINCT(customer_id),time,reminder FROM `reminder` where time>='$date' ORDER BY time ASC,customer_id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $reminder[$rs2['customer_id']] =  $rs2;
    }
    $i=1;
    $where=$where1='';
  	$num_count=$without_num_count=0;
    if(trim($user_type)=='domestic' || trim($user_type)=='international')
    {
        $wheres=" AND type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND assigned_id='$assigned_id'";
        }
    }
    if(trim($type_customer)!='')
    {
        if ($where == '') {
            $where = " type_customer='$type_customer'";
        }
        else{
            $where = " AND type_customer='$type_customer'";
        }
    }
    if($status!='travelled')
    {
        if ($where == '') 
        {
            $where .= " (status = '' OR status = 'Follow Up')";
        }
        else
        {
            $where .= " AND (status = '' OR status = 'Follow Up')";
        }
        if($where1=='')
        {
            $where1.="where (status = '' OR status = 'Follow Up')";
        }
        else
        {
            $where1.=" AND (status = '' OR status = 'Follow Up')";
        }
        if(trim($user_type)=='domestic' || trim($user_type)=='international')
        {
            if($where1=='')
            {
                $where1.="where type='$user_type'";
            }
            else
            {
                $where1.=" AND type='$user_type'";
            }
          	if(trim($user_type)=='international')
            {
              if($where1=='')
              {
              	$where1.="where type_customer='$type_customer'";
              }
              else
              {
                $where1.=" AND type_customer='$type_customer'";
              }
            }
        }
      	$sql1=mysqli_query($connect,"select * from enquiry  $where1");
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' AND $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        if($status=='')
        {
            $sqls=mysqli_query($connect,"select * from enquiry where start_date='0000-00-00' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        }
    }
    $data=array();
    while($rs=mysqli_fetch_assoc($sql))
    {
        $data[$rs['customer_id']]=$rs;
        $data[$rs['customer_id']]['time']=$reminder[$rs['customer_id']]['time'];
    }
    usort($data, function($a, $b) { 
        return $a['time'] <=> $b['time']; 
    });
    $new_array=array();
    $new_empty_array=array();
    foreach($data as $key=>$val)
    {
        if($val['time']=='')
        {
            $new_empty_array[$val['start_date']][$val['id']]=$val;
        }
        else
        {
            $new_array[$val['time']][$val['id']]=$val;
        }
    }
    
    foreach($new_array as $key=>$val)
    {
        foreach($val as $v_key=>$value)
        {
            $start_date=date('Y-m-d',strtotime($value['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'candidate_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $value['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['remark']?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    foreach($new_empty_array as $key=>$val)
    {
        foreach($val as $v_key=>$value)
        {
            $start_date=date('Y-m-d',strtotime($value['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'candidate_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $value['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['remark']?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    if($status=='' || $status=='Follow Up')
    {
        while($rs=mysqli_fetch_assoc($sqls))
        {
            $start_date=$rs['start_date'];
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $rs['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php if($rs['start_date']!='0000-00-00'){echo date('d M Y',strtotime($rs['start_date']));}?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['remark']?></div>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo 'No Date';?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="white" style="stroke: black;stroke-width: 50px;" width="20"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    $num_count=mysqli_num_rows($sql);
  	if($status=='')
    {
      $without_num_count=mysqli_num_rows($sqls);
    }
    echo ",,$".mysqli_num_rows($sql1).",,$".($num_count+$without_num_count);
}
if ($req == 2) 
{
    $limit = 200;

    if ($page > 1) {
        $start = ($page - 1) * $limit;
    } else {
        $start = 0;
    }
    $count = $start + 1;
    $user_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `user` ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $user_data[$rs2['id']] =  $rs2;
    }
    $customer_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $customer_data[$rs2['id']] =  $rs2;
    } 
  	$type=str_replace(',',"','",$type);
    $where='';
    $where1='';
    
    $reminder = array();
    $sq2 = mysqli_query($connect, "SELECT DISTINCT(customer_id),time,reminder FROM `reminder` where time>='$date' ORDER BY time ASC,customer_id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $reminder[$rs2['customer_id']] =  $rs2;
    }
    $i=1;
    $wheres=$where=$where1='';
  	$num_count=$without_num_count=0;
    if(trim($user_type)=='domestic' || trim($user_type)=='international')
    {
        $wheres=" AND e.type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND e.assigned_id='$assigned_id'";
        }
    }
    if($type!='')
    {
      	$where.=" AND e.type IN ('$type')";        
      	if($where1=='')
        {
        	$where1.="where e.type IN ('$type')";        
        }
      	else
      	{
        	$where1.=" AND e.type IN ('$type')";        
      	}
    }
    if($type_customer!='')
    {
        $type_customer=str_replace(',',"','",$type_customer);
      	$where.=" AND e.type_customer IN ('$type_customer')";        
        if($where1=='')
        {
          	$where1.="where e.type_customer IN ('$type_customer')";        
        }
      	else
        {
          $where1.=" AND e.type_customer IN ('$type_customer')";        
        }
    }
    if( $value!='')
    {
        $where.=" AND (c.name LIKE '%$value%' 
        OR c.corporate LIKE '%$value%' 
        OR c.number LIKE '%$value%' 
        OR c.email LIKE '%$value%' 
        OR c.reference LIKE '%$value%' 
        OR e.start_date LIKE '%$value%'
        OR e.status LIKE '%$value%'
        OR e.remark LIKE '%$value%'
        OR e.enquiry_id LIKE '%$value%' 
        OR e.destination LIKE '%$value%')";        
    }
    
    if($status!='travelled')
    {
        if($status=='' || $status=='Follow Up')
        {
            $where.=" AND (e.status = '' OR e.status = 'Follow Up')";
            if($where1=='')
            {
                $where1.="where (e.status = '' OR e.status = 'Follow Up')";
            }
            else
            {
                $where1.=" AND (e.status = '' OR e.status = 'Follow Up')";
            }

        }
        else
        {
            if($status=='Confirmed')
            {
                $where.=" AND (e.status='Vouchered' OR e.status = '$status')";
                if($where1=='')
                {
                    $where1.="where (e.status='Vouchered' OR e.status = '$status')";
                }
                else
                {
                    $where1.=" AND (e.status='Vouchered' OR e.status = '$status')";
                }
            }
            else
            {
                $where.=" AND e.status = '$status'";
                if($where1=='')
                {
                    $where1.="where e.status = '$status'";
                }
                else
                {
                    $where1.=" AND e.status = '$status'";
                }
            }            
        }
        if(trim($user_type)=='domestic' || trim($user_type)=='international')
        {
            if($where1=='')
            {
                $where1.="where e.type='$user_type'";
            }
            else
            {
                $where1.=" AND e.type='$user_type'";
            }
          	if(trim($user_type)=='international')
            {
              if($where1=='')
              {
              	$where1.="where e.type_customer='$type_customer'";
              }
              else
              {
                $where1.=" AND e.type_customer='$type_customer'";
              }
            }
        }
        if($select_staff_id!='')
        {
            $where.=" AND e.assigned_id='$select_staff_id'";
        }
  
      	$sql1=mysqli_query($connect,"select * from enquiry as e, customer as c  $where1");
      	$sql=mysqli_query($connect,"select * from enquiry as e, customer as c where e.start_date>='$today' AND e.customer_id = c.id $where $wheres ORDER BY e.start_date ASC LIMIT $start,$limit");
        if($status=='')
        {
            $sqls=mysqli_query($connect,"select * from enquiry as e, customer as c where e.start_date='0000-00-00' AND e.customer_id = c.id $where $wheres ORDER BY e.start_date ASC LIMIT $start,$limit");
        }
    }
    $data=array();
    while($rs=mysqli_fetch_assoc($sql))
    {
        $data[$rs['customer_id']]=$rs;
        $data[$rs['customer_id']]['time']=$reminder[$rs['customer_id']]['time'];
    }
    usort($data, function($a, $b) { 
        return $a['time'] <=> $b['time']; 
    });
    $new_array=array();
    $new_empty_array=array();
    foreach($data as $key=>$val)
    {
        if($val['time']=='')
        {
            $new_empty_array[$val['start_date']][$val['id']]=$val;
        }
        else
        {
            $new_array[$val['time']][$val['id']]=$val;
        }
    }
    
    foreach($new_array as $key=>$val)
    {
        foreach($val as $v_key=>$value)
        {
            $start_date=date('Y-m-d',strtotime($value['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'candidate_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $value['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['remark']?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    foreach($new_empty_array as $key=>$val)
    {
        foreach($val as $v_key=>$value)
        {
            $start_date=date('Y-m-d',strtotime($value['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'candidate_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $value['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['remark']?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    if($status=='' || $status=='Follow Up')
    {
        while($rs=mysqli_fetch_assoc($sqls))
        {
            $start_date=$rs['start_date'];
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;"><?php echo $rs['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php if($rs['start_date']!='0000-00-00'){echo date('d M Y',strtotime($rs['start_date']));}?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['remark']?></div>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo 'No Date';?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="white" style="stroke: black;stroke-width: 50px;" width="20"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
    }
    $num_count=mysqli_num_rows($sql);
  	if($status=='')
    {
      $without_num_count=mysqli_num_rows($sqls);
    }
    echo ",,$".mysqli_num_rows($sql1).",,$".($num_count+$without_num_count);
}
$sql = mysqli_query($connect, "select * from reminder ORDER BY customer_id ASC,time ASC");
while($rs=mysqli_fetch_assoc($sql))
{
    
    $customer_id=$rs['customer_id'];
    $c_sql = mysqli_query($connect, "select * from enquiry where customer_id='$customer_id'");
    while($c_rs=mysqli_fetch_assoc($c_sql))
    {
        if(($rs['time']<=date('Y-m-d')))
        {
            $reminder=$rs['time'];
            mysqli_query($connect, "update enquiry set reminder='$reminder' where customer_id='$customer_id' ");
        }
    }
}