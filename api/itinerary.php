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
$sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` WHERE customer_id='$customer_id'");

if ($sq2 && mysqli_num_rows($sq2) > 0) {
    $enquiry = mysqli_fetch_assoc($sq2);
    $enquiry_datetime = $enquiry['datetime']; // Assuming 'datetime' is the column name

    if (isset($booking_date)) {
        // Convert the enquiry datetime and booking date to just the date (removing time)
        $enquiryDate = date('Y-m-d', strtotime($enquiry_datetime));
        $bookingDate = date('Y-m-d', strtotime($booking_date));

        // Compare only the dates
        if ($bookingDate < $enquiryDate) {
            echo json_encode(
                //'status' => 'error',
                 'Booking date should come after the enquiry date.',
            );
            exit;
        }

       
    }
}
if ($req == 1) 
{
    $itinerary = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `itinerary` where customer_id='$customer_id' AND is_deleted=0 ORDER BY checkin ASC");
    ?>

<div class="pull-left col-12 pb-1 mt-1 mb-1 itinerarys p-0">
    <div class="pull-left border_8px w-100 font-blue border-blue overflow-hidden ">
        <div class="col-12 pull-left text-center blue-back bold p-0 flex-info">
            <div class="col-1 pull-left border-right" style="min-height: 21px;">
                Book Date
            </div>
            <div class="col-1 pull-left border-right">
                CheckIn
            </div>
            <div class="col-1 pull-left border-right">
                CheckOut
            </div>
            <div class="col-1 pull-left border-right">
                Cancel
            </div>
            <div class="col-1 pull-left border-right">
                Product
            </div>
            <div class="col-1 pull-left border-right">
                Prod. Amt.
            </div>
            <div class="col-1 pull-left text-left border-right">
                Supp. Name
            </div>
            <div class="col-1 pull-left text-left border-right">
                B. Ref
            </div>
            <div class="col-1 pull-left text-left border-right">
                Pax Name
            </div>
            <div class="col-1 pull-left text-left border-right">
                No. of Pax
            </div>
            <div class="col-1 pull-left text-left border-right">
                Remark
            </div>
            <div class="col-1 pull-left">
                Action
            </div>
        </div>
        <?php
            if(mysqli_num_rows($sq2)>0)
            {
                while ($rs2 = mysqli_fetch_assoc($sq2)) 
                {                 
                ?>
        <div class="col-12 pull-left text-center border-top p-0 flex-info">
            <div class="col-1 pull-left">
                <?php echo date('d-M-y',strtotime($rs2['booking_date']))?>
            </div>
            <div class="col-1 pull-left">
                <?php echo date('d-M-y',strtotime($rs2['checkin']))?>
            </div>
            <div class="col-1 pull-left">
                <?php echo date('d-M-y',strtotime($rs2['checkout']))?>
            </div>
            <div class="col-1 pull-left">
                <?php echo date('d-M-y',strtotime($rs2['cancellation']))?>
            </div>
            <div class="col-1 pull-left">
                <?php echo $rs2['product'].'-'.$rs2['product_name']?>
            </div>
            <div class="col-1 pull-left">
                <?php echo $rs2['product_amount']?>
            </div>
            <div class="col-1 pull-left text-ellipsis text-left"
                onmouseover="show_tooltip(event,'<?php echo $rs2['supplier_name']?>')" onmouseout="hide_tooltip(event)">
                <?php echo $rs2['supplier_name']?>
            </div>
            <div class="col-1 pull-left text-ellipsis text-left"
                onmouseover="show_tooltip(event,'<?php echo $rs2['booking_reference']?>')"
                onmouseout="hide_tooltip(event)">
                <?php echo $rs2['booking_reference']?>
            </div>
            <div class="col-1 pull-left text-ellipsis text-left"
                onmouseover="show_tooltip(event,'<?php echo $rs2['pax_name']?>')" onmouseout="hide_tooltip(event)">
                <?php echo $rs2['pax_name']?>
            </div>
            <div class="col-1 pull-left text-ellipsis text-left"
                onmouseover="show_tooltip(event,'<?php echo $rs2['no_of_pax']?>')" onmouseout="hide_tooltip(event)">
                <?php echo $rs2['no_of_pax']?>
            </div>
            <div class="col-1 pull-left text-ellipsis text-left"
                onmouseover="show_tooltip(event,'<?php echo $rs2['remark']?>')" onmouseout="hide_tooltip(event)">
                <?php echo $rs2['remark']?>
            </div>
            <div class="col-1 pull-left">
                <span onclick="delete_itinerary('<?php echo $rs2['id']; ?>')" style="color: red; font-size: 11px;">
                    <img src="assets/img/delete.png" style="width: 12px;"> Delete
                </span>

                <span onclick="get_itinerary_detail_input('<?php echo $rs2['id']?>')"
                    data-bs-target="#add_itinerary_modal" data-bs-toggle="modal" class="glyphicon glyphicon-edit"
                    style="width: 12px; font-size: 11px;"></span> Edit
            </div>




            <!-- <div class="col-1 pull-left">
                    <span class="glyphicon glyphicon-trash"></span> Delete
                    </div> -->


        </div>
        <?php
                }
            }
            else
            {
                ?>
        <div class="col-12 text-center bold pull-left pull-left text-center border-top p-0">
            No Iterinary found
        </div>
        <?php
            }
            ?>
    </div>
</div>

<?php
       echo ",,$";
       $type= '';
   


// $sq2 = mysqli_query($connect, "SELECT 
//     e.*, cc.country_name,
//     GROUP_CONCAT(ct.country_name) AS city_names
// FROM `enquiry` e
// LEFT JOIN `crcountry` cc ON e.country_id = cc.country_id
// LEFT JOIN `crcity` ct ON FIND_IN_SET(ct.city_id, e.city_id)
// WHERE e.customer_id ='$customer_id'");

$sq2 = mysqli_query($connect, "
    SELECT 
        e.*, 
        GROUP_CONCAT(DISTINCT cc.country_name ORDER BY cc.country_name ASC) AS country_name,
        GROUP_CONCAT(DISTINCT ct.country_name ORDER BY ct.country_name ASC) AS city_names
    FROM `enquiry` e
    LEFT JOIN `crcountry` cc ON FIND_IN_SET(cc.country_id, e.country_id)
    LEFT JOIN `crcity` ct ON FIND_IN_SET(ct.city_id, e.city_id)
    WHERE e.customer_id = '$customer_id'
");


    while ($rs = mysqli_fetch_assoc($sq2)) 
    {
        $enquiry_id=$rs['enquiry_id'];
        $sq = mysqli_query($connect, "SELECT * FROM `customer` where id='$customer_id'");
        while ($rss = mysqli_fetch_assoc($sq)) {
            $assigned_id=$rss['assigned_id'];
            $reference=$rss['reference'];            
        }
        $sq = mysqli_query($connect, "SELECT * FROM `user` where id='$assigned_id'");
        while ($rss = mysqli_fetch_assoc($sq)) {
            $assigned_name=$rss['name'];
        }
        
    ?>
<!-- <div class="w-100 pull-left">
    <?php if (!empty($rs['country_name']) ): ?>
    <div class="col-12 pull-left text-left border-blue-bot">
        <div class="pull-left p-0 font-red col-3">Destination : </div>
        <div class="col-9 pull-left p-0 font-blue">
            <?php echo $rs['country_name'] ?> <?php  echo '('.$rs['city_names'].')'?>
        </div>
    </div>
    <?php else: ?>
    <div class="col-12 pull-left text-left border-blue-bot">
        <div class="pull-left p-0 font-red col-3">Destination : </div>
        <div class="col-9 pull-left p-0 font-blue">
            <?php echo $rs['destination'] ?>
        </div>
    </div>
    <?php endif; ?> -->

    <div class="w-100 pull-left">
    <?php if (!empty($rs['country_name'])): ?>
        <div class="col-12 pull-left text-left border-blue-bot">
            <div class="pull-left p-0 font-red col-3">Destination :</div>
            <div class="col-9 pull-left p-0 font-blue">
                <?php echo $rs['country_name'] ?> <?php echo '('.$rs['city_names'].')' ?>
            </div>
        </div>
    <?php else: ?>
        <div class="col-12 pull-left text-left border-blue-bot">
            <div class="pull-left p-0 font-red col-3">Destination :</div>
            <div class="col-9 pull-left p-0 font-blue">
                <?php echo $rs['destination'] ?>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php $type = $rs['type']; ?>
    <div class="col-12 pull-left text-left border-blue-bot">
        <div class="pull-left p-0 font-red col-3">Type :</div>
        <div class="col-9 text-capitalize pull-left p-0 font-blue"><?php echo $rs['type']?></div>
    </div>
</div>
<div class="col-12 pull-left border-blue-bot">
    <div class=" pull-left p-0 font-red col-3">No of pax :</div>
    <div class="col-9 pull-left p-0 font-blue"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')"
        onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?>
    </div>
</div>
<div class="col-12 pull-left border-blue-bot">
    <div class="col-6 pull-left p-0">
        <div class="col-6 pull-left p-0 font-red">Start Date :</div>
        <div class="col-6 p-0 pull-left font-blue">
            <?php if($rs['start_date']!='0000-00-00'){ echo date('d-M-Y',strtotime($rs['start_date']));}?></div>
    </div>
    <div class="col-6 pull-left p-0">
        <div class="col-5 pull-left p-0 font-red">End Date :</div>
        <div class="col-7 p-0 pull-left font-blue">
            <?php if($rs['end_date']!='0000-00-00'){ echo date('d-M-Y',strtotime($rs['end_date']));}?></div>
    </div>
</div>
<div class="col-12 pull-left border-blue-bot">
    <div class="col-6 p-0 pull-left">
        <div class=" pull-left p-0 font-red col-6">Nights :</div>
        <div class="col-6 pull-left p-0 font-blue"><?php echo $rs['days']?></div>
    </div>
    <div class="col-6 p-0 pull-left">
        <div class="col-5 pull-left p-0 font-red">Status :</div>
        <div class="col-7 pull-left font-blue"><?php echo $rs['status']?></div>
    </div>
</div>
<div class="col-12 pull-left border-blue-bot">
    <div class="col-3 pull-left p-0 font-red">Assigned :</div>
    <div class="col-9 pull-left font-blue"><?php echo $assigned_name?>
        <button type="button" data-bs-target="#transfer_lead" data-bs-toggle="modal"
            onclick="cancel_clr('#transfer_lead select'),set_id('<?php echo $assigned_id?>','#transfer_lead select')"
            class="btn transfer-lead d-none btn-primary btn-sm pull-right"
            style="margin-top: 2px !important;height: 20px;padding: 0px 6px;font-size: 11px;margin-left: 7px;margin-bottom:2px;line-height: 20px;">Transfer
            Lead</button>
    </div>
</div>
<div class="col-12 pull-left border-blue-bot">
    <div class="col-3 pull-left p-0 font-red">Remark :</div>
    <div class="col-9 pull-left font-blue"><?php echo $rs['remark']?></div>
</div>
<div class="col-12 pull-left">
    <div class="col-3 pull-left p-0 font-red">Reference :</div>
    <div class="col-9 pull-left font-blue"><?php echo $reference?></div>
</div>
<?php 
    }       
   // echo ",,$".$enquiry_id;   
   echo ",,$".$enquiry_id.",,$".$type;      
}
if ($req == 2) 
{
    $enquiry = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where id='$id'");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $enquiry[] =  $rs2;
    }
    echo json_encode($enquiry);
}
if ($req == 3) 
{
   mysqli_query($connect,"DELETE FROM enquiry where id='$id'");
   mysqli_query($connect,"DELETE FROM itinerary where enquiry_id='$id'"); 
}
if ($req == 4) 
{
    $enquiry = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `itinerary` where id='$id'");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $enquiry[0] =  $rs2;
        $enquiry_id=$rs2['enquiry_id'];
        $enquiry[0]['time']=date('h:i a',strtotime($rs2['datetime']));
    }
    $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where id='$enquiry_id'");
    while ($rs = mysqli_fetch_assoc($sq2)) 
    {         
        $enquiry[0]['days']=$rs['days'];
    }
    echo json_encode($enquiry);
}
if($req==5)
{
    $booking_date=date('Y-m-d',strtotime($booking_date));
    $checkin=date('Y-m-d',strtotime($checkin));
    $checkout=date('Y-m-d',strtotime($checkout));
    $cancellation=date('Y-m-d',strtotime($cancellation));
    
    if($id=='')
    {
        // $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where id='$enquiry_id'");
        // while ($rs = mysqli_fetch_assoc($sq2)) 
        // {         
        //     $start_date=$rs['start_date'];
        // }
        // $start_dates=date('Y-m-d',strtotime('+'.$day.' day', strtotime($start_date)));
        // $start_datetime=date('Y-m-d H:i',strtotime('+'.$day.' day', strtotime($start_date.' '.$starttime)));
        // $end_datetime=date('Y-m-d H:i',strtotime('+'.$day.' day', strtotime($start_date.' '.$endtime)));

        // $last_end=$next_start='';
        // $add_data='';
        // $sql=mysqli_query($connect,"select * from itinerary where customer_id='$customer_id' AND DATE(start_datetime)='$start_dates' ORDER BY start_datetime ASC");
        // $row=mysqli_num_rows($sql);
        // if($row>0)
        // {
        //     while($rs=mysqli_fetch_assoc($sql))
        //     {
        //         if($next_start=='' && $rs['start_datetime']>$end_datetime && $row==1)
        //         {
        //             //ECHO "ok start";
        //             $add_data='yes';
        //         }
        //         else if($last_end=='' && $rs['end_datetime']<$start_datetime && $row==1)
        //         {
        //             $last_end.'== && '.$rs['end_datetime'].'<'.$start_datetime;
        //             $last_end=$rs['end_datetime'];
        //             $add_data='yes';
        //             // if($start_datetime>$last_end && $end_datetime<=$rs['start_datetime'])    
        //             // {
        //             //     echo $start_datetime.'>'.$last_end.' && '.$start_datetime.'<='.$rs['start_datetime'];
        //             //     //echo "ok between";
        //             //     $add_data='yes';
        //             // }
        //             // else
        //             // {
        //             //     $last_end=$rs['end_datetime'];
        //             //     $add_data='';
        //             // }                
        //         }
        //         else
        //         {
        //             if($last_end=='')
        //             {
        //                 $last_end=$rs['end_datetime'];
        //                 $next_start='next';
        //             }
        //             //echo $start_datetime.'>'.$last_end.' && '.$end_datetime.'<='.$rs['start_datetime'];
        //             if($start_datetime>$last_end && $end_datetime<$rs['start_datetime'])    
        //             {
        //                 $add_data='yes';
        //                 //echo "ok between";
        //             }
        //             else
        //             {
        //                 $last_end=$rs['end_datetime'];
        //             }                
        //         }
        //     }
        // }
        // else
        // {
        //     $add_data='yes'; 
        // }
        $add_data='yes';
        if($add_data=='yes')
        {
            mysqli_query($connect,"insert into itinerary(product_name,product_amount, `enquiry_id`, `day`, `booking_date`, `checkin`, `checkout`, `cancellation`, `product`, `supplier_name`, `booking_reference`, `pax_name`, `no_of_pax`, `remark`, `customer_id`, `added_by_id`)value('$product_name','$product_amount','$enquiry_id','$day','$booking_date','$checkin','$checkout','$cancellation','$product','$supplier_name','$booking_reference','$pax_name','$no_of_pax','$remark','$customer_id','$added_by_id')");
            echo "ok";
        }
        else
        {
            echo "Start Time OR End Time is Already Booked for Day ".$day;
        }
    }
    else
    {
        // $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where id='$enquiry_id'");
        // while ($rs = mysqli_fetch_assoc($sq2)) 
        // {         
        //     $start_date=$rs['start_date'];
        // }
        // $start_datetime=date('Y-m-d H:i',strtotime('+'.$day.' day', strtotime($start_date.' '.$starttime)));
        // $end_datetime=date('Y-m-d H:i',strtotime('+'.$day.' day', strtotime($start_date.' '.$endtime)));
        // if(mysqli_num_rows(mysqli_query($connect,"select * from itinerary where customer_id='$customer_id' AND ((start_datetime<='$start_datetime' && end_datetime>='$start_datetime') OR (start_datetime<='$end_datetime' && end_datetime>='$end_datetime')) AND id NOT LIKE '$id'"))>0)
        // {
        //     echo "Start Time OR End Time is Already Booked for Day ".$day;
        // }
        // else
        // {
            mysqli_query($connect,"update itinerary set product_name='$product_name',product_amount='$product_amount', `day`='$day', `booking_date`='$booking_date', `checkin`='$checkin', `checkout`='$checkout', `cancellation`='$cancellation', `product`='$product', `supplier_name`='$supplier_name', `booking_reference`='$booking_reference', `pax_name`='$pax_name', `no_of_pax`='$no_of_pax', `remark`='$remark' where id='$id'");
            echo "ok";
        //}
    }
} 
if($req==6)
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
        $where=" AND type_customer='$type_customer'";
    }
    if($status!='travelled')
    {
        if($status=='' || $status=='Follow Up')
        {
            $where.=" AND (status = '' OR status = 'Follow Up')";
            if($where1=='')
            {
                $where1.="where (status = '' OR status = 'Follow Up')";
            }
            else
            {
                $where1.=" AND (status = '' OR status = 'Follow Up')";
            }

        }
        else
        {
            if($status=='Confirmed')
            {
                $where.=" AND (status='Vouchered' OR status = '$status')";
                if($where1=='')
                {
                    $where1.="where (status='Vouchered' OR status = '$status')";
                }
                else
                {
                    $where1.=" AND (status='Vouchered' OR status = '$status')";
                }
            }
            else
            {
                $where.=" AND status = '$status'";
                if($where1=='')
                {
                    $where1.="where status = '$status'";
                }
                else
                {
                    $where1.=" AND status = '$status'";
                }
            }            
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
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        if($status=='')
        {
            $sqls=mysqli_query($connect,"select * from enquiry where start_date='0000-00-00' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        }
    }
    else
    {
        $where1.=" where (status = 'travelled' OR start_date<'$today')";
        if(trim($user_type)=='domestic' || trim($user_type)=='international')
        {
            if($where1=='')
            {
                $where1.=" where type='$user_type'";
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
        $sql1=mysqli_query($connect,"select * from enquiry $where1");
        $sql=mysqli_query($connect,"select * from enquiry where (start_date<'$today' OR status='$status') and start_date NOT LIKE '0000-00-00' $where $wheres ORDER BY start_date DESC LIMIT $start,$limit");
    }
    while($rs=mysqli_fetch_assoc($sql))
    {
        $start_date=$rs['start_date'];
        $last3_date=date('Y-m-d', strtotime('+4 days'));
    ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;">
        <?php echo $rs['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')"
        onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;">
        <?php echo date('d M Y',strtotime($rs['start_date']))?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;"
        onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $rs['remark']?></div>
    <?php if($start_date<$today){$tool_data='Previous';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data;?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512"
            fill="<?php if($start_date<$today){echo "red";}elseif($start_date==$today){echo "green";}elseif($start_date>$today && $start_date<=$last3_date){echo "orange";}else{echo "white";}?>"
            style="<?php if($start_date>$last3_date){?>stroke: black;stroke-width: 50px;<?php }?>" width="20">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
</div>
<?php
    }
    if($status=='' || $status=='Follow Up')
    {
        while($rs=mysqli_fetch_assoc($sqls))
        {
            $start_date=$rs['start_date'];
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;">
        <?php echo $rs['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')"
        onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;">
        <?php if($rs['start_date']!='0000-00-00'){echo date('d M Y',strtotime($rs['start_date']));}?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;"
        onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $rs['remark']?></div>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo 'No Date';?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512"
            fill="<?php if($start_date<$today){echo "red";}elseif($start_date==$today){echo "green";}elseif($start_date>$today && $start_date<=$last3_date){echo "orange";}else{echo "white";}?>"
            style="<?php if($start_date>$last3_date){?>stroke: black;stroke-width: 50px;<?php }?>" width="20">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
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
if($req==7)
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
    $customer_ids = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` where (name LIKE '%$value%' OR corporate LIKE '%$value%' OR number LIKE '%$value%' OR email LIKE '%$value%' OR reference LIKE '%$value%') ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        array_push($customer_ids,$rs2['id']);
    }
    $i=1;
    
    //$sqls=mysqli_query($connect,"select * from enquiry where start_date>='$today'");
    $customer_id= implode(',',$customer_ids);
    $type=str_replace(',',"','",$type);
    $where='';
    $where1='';
    if($type!='')
    {
        $where1.=$where.=" AND type IN ('$type')";        
    }
    if($type_customer!='')
    {
        $type_customer=str_replace(',',"','",$type_customer);
        $where1.=$where.=" AND type_customer IN ('$type_customer')";        
    }
    if($customer_id=='' && $value!='')
    {
        $where.=" AND (adult LIKE '%$value%' 
        OR children LIKE '%$value%' 
        OR infant LIKE '%$value%' 
        OR destination LIKE '%$value%' 
        OR days LIKE '%$value%'  
        OR start_date LIKE '%$value%'
        OR status LIKE '%$value%'
        OR remark LIKE '%$value%'
        OR enquiry_id LIKE '%$value%')";        
    }
    if($customer_id!='')
    {
        $where.=" AND customer_id IN ($customer_id)";
        $where1.=" AND customer_id IN ($customer_id)";
    }
    $wheres='';
    if(trim($user_type)=='domestic' || trim($user_type)=='international' )
    {
        $wheres=" AND type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND assigned_id='$assigned_id'";
        }
        // else
        // {
        //     $wheres.=" AND assigned_id='$select_staff_id'";
        // }
        
    }
    if($select_staff_id!='')
    {
        $wheres.=" AND assigned_id='$select_staff_id'";
    }
    if($status!='travelled')
    {
        if($status=='' || $status=='Follow Up')
        {
            $where.=" AND (status = '' OR status = 'Follow Up')";
            $where1.=" AND (status = '' OR status = 'Follow Up')";  
          	if($allwhere1=='')
            {
                $allwhere1.="where (status = '' OR status = 'Follow Up')";
            }
            else
            {
                $allwhere1.=" AND (status = '' OR status = 'Follow Up')";
            }
        }
        else
        {
            if($status=='Confirmed')
            {
                $where.=" AND (status='Vouchered' OR status = '$status')";    
                $where1.=" AND (status='Vouchered' OR status = '$status')";    
              	if($allwhere1=='')
                {
                    $allwhere1.="where (status='Vouchered' OR status = '$status')";
                }
                else
                {
                    $allwhere1.=" AND (status='Vouchered' OR status = '$status')";
                }
            }
            else
            {
                $where.=" AND status = '$status'";
                $where1.=" AND status = '$status'";
              	if($allwhere1=='')
                {
                    $allwhere1.="where status = '$status'";
                }
                else
                {
                    $allwhere1.=" AND status = '$status'";
                }
            }            
        }
        $sql1=mysqli_query($connect,"select * from enquiry $allwhere1");       
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        if($status=='')
        {
            $sqls=mysqli_query($connect,"select * from enquiry where start_date='0000-00-00' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        }
    }
    else
    {
        // $where.=" OR status = '$status'";
        // $where1.=" OR status = '$status'";
        //(start_date<'$today'  OR 
        $sql1=mysqli_query($connect,"select * from enquiry where status = '$status' $wheres");       
        $sql=mysqli_query($connect,"select * from enquiry where (start_date<'$today' OR status='$status') and start_date NOT LIKE '0000-00-00' $where $wheres ORDER BY start_date DESC LIMIT $start,$limit");
        
    }
    if(mysqli_num_rows($sql)<=0)
    {
        $where=" (start_date LIKE '%$value%' 
        OR destination LIKE '%$value%') AND "; 
        if($status!='travelled')
        {       
            $sql=mysqli_query($connect,"select * from enquiry where $where start_date>='$today' $where1 $wheres ORDER BY start_date ASC LIMIT $start,$limit");
        }
        else
        {
            $sql=mysqli_query($connect,"select * from enquiry where $where start_date<'$today' $where1 $wheres ORDER BY start_date ASC LIMIT $start,$limit");    
        }
    }
    $num_count=$without_num_count=0;
    while($rs=mysqli_fetch_assoc($sql))
    {
        $start_date=$rs['start_date'];
        $last3_date=date('Y-m-d', strtotime('+4 days'));
    ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue position-relative" style="width: 10%;">
        <?php echo $rs['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')"
        onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;">
        <?php echo date('d M Y',strtotime($rs['start_date']))?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;"
        onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $rs['remark']?></div>
    <?php if($start_date<$today){$tool_data='Previous';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data;?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512"
            fill="<?php if($start_date<$today){echo "red";}elseif($start_date==$today){echo "green";}elseif($start_date>$today && $start_date<=$last3_date){echo "orange";}else{echo "white";}?>"
            style="<?php if($start_date>$last3_date){?>stroke: black;stroke-width: 50px;<?php }?>" width="20">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
</div>
<?php
    }
    if($status=='' || $status=='Follow Up')
    {
        while($rs=mysqli_fetch_assoc($sqls))
        {
            $start_date=$rs['start_date'];
            $last3_date=date('Y-m-d', strtotime('+4 days'));
        ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="javascript:window.location.href='dashboard.html?id=<?php echo $rs['customer_id']?>'"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue position-relative" style="width:12%;"><?php echo $rs['enquiry_id']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$rs['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$rs['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$rs['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$rs['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $rs['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$rs['adult'].'<br>Child-'.$rs['children'].'<br>Infant-'.$rs['infant'].'<br>Total-'.($rs['adult']+$rs['children']+$rs['infant']);?>')"
        onmouseout="hide_tooltip(event)"><?php echo 'A-'.$rs['adult'].', C-'.$rs['children'].', I-'.$rs['infant']?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;">
        <?php echo date('d M Y',strtotime($rs['start_date']))?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width:13%;"
        onmouseover="show_tooltip(event,'<?php echo $rs['remark']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $rs['remark']?></div>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo "No Date" ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512"
            fill="<?php if($start_date<$today){echo "red";}elseif($start_date==$today){echo "green";}elseif($start_date>$today && $start_date<=$last3_date){echo "orange";}else{echo "white";}?>"
            style="<?php if($start_date>$last3_date){?>stroke: black;stroke-width: 50px;<?php }?>" width="20">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
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
if ($req == 8) 
{
    $wheres='';
    if(trim($user_type)=='domestic' || trim($user_type)=='international' )
    {
        $wheres=" AND type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND assigned_id='$assigned_id'";
        }
        // else
        // {
        //     $wheres.=" AND assigned_id='$select_staff_id'";
        // }
        
    }
    if($select_staff_id!='')
    {
        $wheres.=" AND assigned_id='$select_staff_id'";
    }
    if($status!='travelled')
    {
        if($status=='' || $status=='Follow Up')
        {
            $where.=" AND (status = '' OR status = 'Follow Up')";
        }
        else
        {
            $where.=" AND status = '$status'";
        }
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' $where $wheres");
    }
    else
    {
        $where.=" OR status = '$status'";
        $sql=mysqli_query($connect,"select * from enquiry where start_date<'$today' $where $wheres");
    }
    echo getpage(mysqli_num_rows($sql), 200, 2, $page, $func);

} 
if($req==9)
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
    $customer_ids = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` where (name LIKE '%$value%' OR corporate LIKE '%$value%' OR number LIKE '%$value%' OR email LIKE '%$value%' OR reference LIKE '%$value%') ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        array_push($customer_ids,$rs2['id']);
    }
    $i=1;
    
    $sqls=mysqli_query($connect,"select * from enquiry where start_date>='$today'");
    $customer_id= implode(',',$customer_ids);
    $type=str_replace(',',"','",$type);
    $where='';
    $where1='';
    if($type!='')
    {
        $where1.=$where.=" AND type IN ('$type')";        
    }
    if($type_customer!='')
    {
        $type_customer=str_replace(',',"','",$type_customer);
        $where1.=$where.=" AND type_customer IN ('$type_customer')";        
    }
    if($customer_id=='' && $value!='')
    {
        $where.=" AND (adult LIKE '%$value%' 
        OR children LIKE '%$value%' 
        OR infant LIKE '%$value%' 
        OR destination LIKE '%$value%' 
        OR days LIKE '%$value%'  
        OR start_date LIKE '%$value%'
        OR status LIKE '%$value%'
        OR remark LIKE '%$value%'
        OR enquiry_id LIKE '%$value%')";               
    }
    if($customer_id!='')
    {
        $where.=" AND customer_id IN ($customer_id)";
        $where1.=" AND customer_id IN ($customer_id)";
    }
    $wheres='';
    if(trim($user_type)=='domestic' || trim($user_type)=='international' )
    {
        $wheres=" AND type='$user_type'";
        if(trim($user_designation)!='tl')
        {
            $wheres.=" AND assigned_id='$assigned_id'";
        }
        // else
        // {
        //     $wheres.=" AND assigned_id='$select_staff_id'";
        // }
        
    }
    if($select_staff_id!='')
    {
        $wheres.=" AND assigned_id='$select_staff_id'";
    }
    if($status!='travelled')
    {
        if($status=='' || $status=='Follow Up')
        {
            $where.=" AND (status = '' OR status = 'Follow Up')";
        }
        else
        {
            $where.=" AND status = '$status'";
        }
        
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
    }
    else
    {
        $where.=" OR status = '$status'";
        $sql=mysqli_query($connect,"select * from enquiry where start_date<'$today' $where $wheres ORDER BY start_date ASC LIMIT $start,$limit");
    }
    if(mysqli_num_rows($sql)<=0)
    {
        $sql=mysqli_query($connect,"select * from enquiry where start_date>='$today' $where1 $wheres ORDER BY start_date ASC LIMIT $start,$limit");
    }
    echo getpage(mysqli_num_rows($sql), 200, 2, $page, $func);
} 
if($req==10)
{
    $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where customer_id='$customer_id'");
  	while($rs=mysqli_fetch_assoc($sq2))
    {
      echo $rs['id'];
    }
}
if ($req == 11) 
{
   mysqli_query($connect,"UPDATE itinerary SET `is_deleted` = 1 where id='$id'"); 
   echo "ok";
}
                         
?>