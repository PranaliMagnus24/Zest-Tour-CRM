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
    $limit = 20;

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
  	$where=$where1='';
  	$num_count=$without_num_count=0;
    if(trim($user_type)=='domestic' || trim($user_type)=='international')
    {
        $wheres=" AND e.type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND e.assigned_id='$assigned_id'";
        }
    }
    if(trim($type_customer)!='')
    {
        $where=" AND e.type_customer='$type_customer'";
    }
    
    $where.=" AND (e.status='Vouchered' OR e.status = '$status')";
    if($where1=='')
    {
      $where1.="where (e.status='Vouchered' OR e.status = '$status')";
    }
    else
    {
      $where1.=" AND (e.status='Vouchered' OR e.status = '$status')";
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
    $cancellation_date=date('Y-m-d',strtotime('+1 days',strtotime($today)));
    $sql1=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where e.id=i.enquiry_id $where $wheres");
  	$sql=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where i.cancellation>='$cancellation_date' AND e.id=i.enquiry_id $where $wheres ORDER BY i.$column $sort LIMIT $start,$limit");
    $f_c = mysqli_num_rows($sql);
    $i=$start+1;
  	while($rs=mysqli_fetch_assoc($sql))
    {
        $start_date=date('Y-m-d',strtotime($rs['cancellation']));
        $last7_date=date('Y-m-d', strtotime('+7 days'));
        
        ?>
        <div class="col-12 p-0 pull-left border-top b-l-r Flex <?php if ($start_date>$today && $start_date<=$last7_date) {echo "bg-success";}?>" data-checkin="<?php echo $rs['checkin'] ?>" data-checkout="<?php echo $rs['checkout'] ?>" data-booking_date="<?php echo $rs['booking_date'] ?>" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'" style="background-color: #ebeefe;display: flex;" >
            <div class="pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?> position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['enquiry_number']?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $rs['enquiry_number']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?> position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['booking_date']?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo $rs['booking_date']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkin']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkin']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkout']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkout']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['cancellation']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['cancellation']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;"><?php echo $rs['product']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
            <div class="col-2 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['supplier_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['supplier_name']?></div>
            <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $rs['booking_reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['booking_reference']?></div>
            <div class="col-2 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['pax_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['pax_name']?></div>
            <div class="col-2 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $rs['nof']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['nof']?></div>
        </div>  
        <?php
    }
    if ($f_c < 20) {
      $limit = $limit - $f_c;
      $sqlss = mysqli_query($connect, "SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where i.cancellation<'$cancellation_date' AND e.id=i.enquiry_id $where $wheres ORDER BY i.$column $sort LIMIT $start,$limit");
      while($rs=mysqli_fetch_assoc($sqlss))
      {
        $start_date=date('Y-m-d',strtotime($rs['cancellation']));
          $last7_date=date('Y-m-d', strtotime('+7 days'));
          
          ?>
          <div class="col-12 p-0 pull-left border-top b-l-r Flex bg-danger" data-checkin="<?php echo $rs['checkin'] ?>" data-checkout="<?php echo $rs['checkout'] ?>" data-booking_date="<?php echo $rs['booking_date'] ?>" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'" style="background-color: #ebeefe;display: flex;">
              <div class="pull-left Flex-item text-white" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['enquiry_number']?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $rs['enquiry_number']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['booking_date']?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo $rs['booking_date']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkin']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkin']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkout']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkout']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['cancellation']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['cancellation']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;"><?php echo $rs['product']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
              <div class="col-2 text-ellipsis pull-left Flex-item text-white" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['supplier_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['supplier_name']?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $rs['booking_reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['booking_reference']?></div>
              <div class="col-2 text-ellipsis pull-left Flex-item text-white" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['pax_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['pax_name']?></div>
              <div class="col-2 text-ellipsis pull-left Flex-item text-white" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $rs['nof']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['nof']?></div>
          </div>  
          <?php
      }
    }
    echo ",,$".mysqli_num_rows($sql1).",,$".mysqli_num_rows($sql1);
}

if($req==2)
{
    $limit = 20;

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
  	$i=1;
    $where=$where1=$wheres='';
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
        $type=str_replace(',',"','",$type);
        $wheres.=" AND e.type IN ('$type')";              	
    }
    if($type_customer!='')
    {
        $type_customer=str_replace(',',"','",$type_customer);
      	$wheres.=" AND e.type_customer IN ('$type_customer')";                
    }
    $wheres.=" AND  (e.status='Vouchered' OR e.status = '$status')";
    if($select_staff_id!='' && $select_staff_id!=null)
    {
        $wheres.=" AND e.assigned_id='$select_staff_id'";
    }
    
    $cancellation_date=date('Y-m-d',strtotime('+1 days',strtotime($today)));
  	$sql1=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where e.id=i.enquiry_id $wheres");
  	if($past_data=='')
    {
        $sql=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where i.cancellation>='$cancellation_date' AND e.id=i.enquiry_id $wheres ORDER BY i.$column $sort LIMIT $start,$limit");        
    }
    else
    {
      	$sql=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where i.cancellation<'$cancellation_date' AND e.id=i.enquiry_id $wheres ORDER BY i.$column $sort LIMIT $start,$limit");        
    }
    $f_c = mysqli_num_rows($sql);
    $i=$start+1;
  	while($rs=mysqli_fetch_assoc($sql))
    {
      $start_date=date('Y-m-d',strtotime($rs['cancellation']));
      $last7_date=date('Y-m-d', strtotime('+7 days'));
      
      ?>
      <div class="col-12 p-0 pull-left border-top b-l-r Flex <?php if ($start_date>$today && $start_date<=$last7_date) {echo "bg-success";}?>" data-checkin="<?php echo $rs['checkin'] ?>" data-checkout="<?php echo $rs['checkout'] ?>" data-booking_date="<?php echo $rs['booking_date'] ?>" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'" style="background-color: #ebeefe;display: flex;" >
          <div class="pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?> position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['enquiry_number']?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $rs['enquiry_number']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?> position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['booking_date']?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo $rs['booking_date']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkin']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkin']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkout']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkout']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['cancellation']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['cancellation']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;"><?php echo $rs['product']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['supplier_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['supplier_name']?></div>
          <div class="col-1 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $rs['booking_reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['booking_reference']?></div>
          <div class="col-2 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['pax_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['pax_name']?></div>
          <div class="col-2 text-ellipsis pull-left Flex-item <?php if ($start_date > $today && $start_date <= $last7_date){echo "text-white";} else {echo " font-blue";}?>" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $rs['nof']?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['nof']?></div>
      </div>  
      <?php
    }
  if ($f_c < $limit) {
    $limit = $limit - $f_c;
    $sql2 = mysqli_query($connect, "SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where i.cancellation<'$cancellation_date' AND e.id=i.enquiry_id $wheres ORDER BY i.$column $sort LIMIT $start,$limit");
    while ($rs = mysqli_fetch_assoc($sql2)) {
      $start_date = date('Y-m-d', strtotime($rs['cancellation']));
      $last7_date = date('Y-m-d', strtotime('+7 days'));

      ?>
          <div class="col-12 p-0 pull-left border-top b-l-r Flex bg-danger" data-checkin="<?php echo $rs['checkin'] ?>" data-checkout="<?php echo $rs['checkout'] ?>" data-booking_date="<?php echo $rs['booking_date'] ?>" ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id'] ?>'" style="background-color: #ebeefe;display: flex;">
              <div class="pull-left Flex-item text-white" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['enquiry_number'] ?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $rs['enquiry_number'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white position-relative" onmouseover="show_tooltip(event,'<?php echo $rs['booking_date'] ?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo $rs['booking_date'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkin'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkin'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['checkout'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['checkout'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $rs['cancellation'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['cancellation'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;"><?php echo $rs['product'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['supplier_name'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['supplier_name'] ?></div>
              <div class="col-1 text-ellipsis pull-left Flex-item text-white" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $rs['booking_reference'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['booking_reference'] ?></div>
              <div class="col-2 text-ellipsis pull-left Flex-item text-white" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $rs['pax_name'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['pax_name'] ?></div>
              <div class="col-2 text-ellipsis pull-left Flex-item text-white" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $rs['nof'] ?>')" onmouseout="hide_tooltip(event)"><?php echo $rs['nof'] ?></div>
          </div>  
          <?php
    }
  }
    $num_count=mysqli_num_rows($sql);
  	$without_num_count=mysqli_num_rows($sql2);
    echo ",,$".mysqli_num_rows($sql1).",,$".mysqli_num_rows($sql1);
} 
if ($req == 3) 
{
    $where=$where1='';
  	$num_count=$without_num_count=0;
    if(trim($user_type)=='domestic' || trim($user_type)=='international')
    {
        $wheres=" AND e.type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND e.assigned_id='$assigned_id'";
        }
    }
    if(trim($type_customer)!='')
    {
        $where=" AND e.type_customer='$type_customer'";
    }
    
    $where.=" AND (e.status='Vouchered' OR e.status = '$status')";
    if($where1=='')
    {
      $where1.="where (e.status='Vouchered' OR e.status = '$status')";
    }
    else
    {
      $where1.=" AND (e.status='Vouchered' OR e.status = '$status')";
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
    $cancellation_date=date('Y-m-d',strtotime('+1 days',strtotime($today)));
    $sql=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where  e.id=i.enquiry_id $wheres");        
    
    echo getpage(mysqli_num_rows($sql), 20, 2, $page, $func);
} 
if ($req == 4) 
{
  $where=$where1=$wheres='';
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
      $type=str_replace(',',"','",$type);
      $wheres.=" AND e.type IN ('$type')";              	
  }
  if($type_customer!='')
  {
      $type_customer=str_replace(',',"','",$type_customer);
      $wheres.=" AND e.type_customer IN ('$type_customer')";                
  }
  $wheres.=" AND  (e.status='Vouchered' OR e.status = '$status')";
  if($select_staff_id!='' && $select_staff_id!=null)
  {
      $wheres.=" AND e.assigned_id='$select_staff_id'";
  }
  
  $cancellation_date=date('Y-m-d',strtotime('+1 days',strtotime($today)));
  $sql=mysqli_query($connect,"SELECT *,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number from itinerary as i,enquiry as e where  e.id=i.enquiry_id $wheres");        
    
  echo getpage(mysqli_num_rows($sql), 20, 2, $page, $func);
} 
?>