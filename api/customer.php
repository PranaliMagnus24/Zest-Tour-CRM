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
    $as_sql=mysqli_query($connect,"select * from user where id='$assigned_id'");
    while($rs=mysqli_fetch_assoc($as_sql))
    {
        $ass_name=$rs['name'];
    }
    if($id=='')
    {
        if($user_type_enquiry!=$etype || $etype_customer!=$type_customer)
        {
            $as_sql=mysqli_query($connect,"select * from user where designation='tl' and type='$etype' AND type_customer='$etype_customer'");
            while($rs=mysqli_fetch_assoc($as_sql))
            {
                $assigned_ids=$rs['id'];
            }
        }
        else
        {
            $assigned_ids=$assigned_id;
        }
        $sqla="show table status like 'enquiry'";
        $rea=mysqli_query($connect,$sqla);
        while($rsa=mysqli_fetch_assoc($rea))
        {   
            $enquiry_id=$rsa['Auto_increment'];
        }
        $sqla="show table status like 'customer'";
        $rea=mysqli_query($connect,$sqla);
        while($rsa=mysqli_fetch_assoc($rea))
        {
            $customer_id=$rsa['Auto_increment'];
        }
        if($anniversary!='')
        {
            $anniversary=date('Y-m-d',strtotime($anniversary));
        }
        if($dob!='')
        {
            $dob=date('Y-m-d',strtotime($dob));
        }
        mysqli_query($connect,"INSERT INTO `customer`(`datetime`, `name`, `address`, `number`, `alternatenumber`, `email`, `alternateemail`, `reference`, `corporate`, `dob`, `anniversary`, `added_by_id`, `assigned_id`, `stage`, `aadhar`, `pan`, `passport_expiry`, `nationality`, `passport_dob`, `passport_place`, `passport_name`, `passport_no`, `type`,agent_id) VALUES ('$datetime','$name','$address','$number','$alternatenumber','$email','$alternateemail','$reference','$corporate','$dob','$anniversary','$assigned_id','$assigned_ids','$stage','$aadhar','$pan','$passport_expiry','$nationality','$passport_dob','$passport_place','$passport_name','$passport_no','$type','$agent_id')");
        // if($eadult!='')
        // {
            if($estart_date!='')
            {
                $estart_date=date('Y-m-d',strtotime($estart_date));
            }
            if($eend_date!='')
            {
                $eend_date=date('Y-m-d',strtotime($eend_date));
            }
            $edays = (strtotime($eend_date) - strtotime($estart_date))/86400;
            if($etype=='international')
            {
                if($etype_customer=='B2B')
                {
                    $enquiry_id='B-'.date('d/m/Y',strtotime($datetime)).'-'.$enquiry_id;
                }
                elseif($etype_customer=='B2C')
                {
                    $enquiry_id='C-'.date('d/m/Y',strtotime($datetime)).'-'.$enquiry_id;
                }
            }
            else
            {
                $enquiry_id='D-'.date('d/m/Y',strtotime($datetime)).'-'.$enquiry_id;   
            }
            // $ids_str = '';

            // if (!empty($ecity_id) && is_array($ecity_id)) {
            //     $ids_str = implode(',', $ecity_id);
            // }
           
            // mysqli_query($connect,"INSERT INTO `enquiry`(`enquiry_id`, `no_of_pax`, `destination`, `days`, `start_date`, `end_date`, `customer_id`, `status`, `datetime`, `assigned_id`, `type`, `remark`, `type_customer`, `adult`, `children`, `infant`, `country_id`, `city_id`) VALUES ('$enquiry_id','$eno_of_pax','$edestination','$edays','$estart_date','$eend_date','$customer_id','$estatus','$datetime','$assigned_ids','$etype','$eremark','$etype_customer','$eadult','$echildren','$einfant','$ecountry_id', '$ids_str')");


$ecity_id = $_POST['ecity_id'] ?? [];
        $ecountry_id = $_POST['country_ids'] ?? [];

        $city_ids_str = '';
        $country_ids_str = '';

        if (!empty($ecity_id) && is_array($ecity_id)) {
        $city_ids_str = implode(',', $ecity_id);
        }
        
        if (!empty($ecountry_id) && is_array($ecountry_id)) {
            $country_ids_str = implode(',', $ecountry_id);
        }
        
        $sql = "
        INSERT INTO `enquiry` (
          `enquiry_id`, `no_of_pax`, `destination`, `days`, `start_date`, `end_date`,
          `customer_id`, `status`, `datetime`, `assigned_id`, `type`, `remark`, `type_customer`,
          `adult`, `children`, `infant`, `country_id`, `city_id`
        ) VALUES (
          '$enquiry_id', '$eno_of_pax', '$edestination', '$edays', '$estart_date', '$eend_date',
          '$customer_id', '$estatus', '$datetime', '$assigned_ids', '$etype', '$eremark', '$etype_customer',
          '$eadult', '$echildren', '$einfant', '$country_ids_str', '$city_ids_str'
        )";

// echo "<pre>$sql</pre>"; // for debugging

mysqli_query($connect, $sql);


           
        // }
        if($eremark!='')
        {
            mysqli_query($connect, "insert into remark (`customer_id`,`remarkby_name`,`remarkby`,`remark`,`time`) values ('" . trim($customer_id) . "','" . trim($ass_name) . "','" . trim($assigned_id) . "','$eremark','$datetime')");
        }
    }
    else
    {
        if($anniversary!='')
        {
            $anniversary=date('Y-m-d',strtotime($anniversary));
        }
        if($dob!='')
        {
            $dob=date('Y-m-d',strtotime($dob));
        }
        mysqli_query($connect,"UPDATE `customer` set agent_id='$agent_id',`name`='$name', `address`='$address', `number`='$number', `alternatenumber`='$alternatenumber', `email`='$email', `alternateemail`='$alternateemail', `reference`='$reference', `corporate`='$corporate', `dob`='$dob', `anniversary`='$anniversary' where id='$id'");
        if($eadult!='')
        {
            $sql=mysqli_query($connect,"select * from enquiry where customer_id='$id'");
            while($rs=mysqli_fetch_assoc($sql))
            {
                $enquiry_id=$rs['id'];
            }
            
            if($estart_date!='')
            {
                $estart_date=date('Y-m-d',strtotime($estart_date));
            }
            if($eend_date!='')
            {
                $eend_date=date('Y-m-d',strtotime($eend_date));
            }
            $edays = (strtotime($eend_date) - strtotime($estart_date))/86400;
           
            if($user_type_enquiry!=$etype || $etype_customer!=$type_customer)
            {
                $as_sql=mysqli_query($connect,"select * from user where designation='tl' and type='$etype' AND type_customer='$etype_customer'");
                while($rs=mysqli_fetch_assoc($as_sql))
                {
                    $assigned_ids=$rs['id'];
                    mysqli_query($connect,"update `enquiry` set `assigned_id`='$assigned_ids' where customer_id='$id'");
                }
            }
            if($etype!='')
            {
                mysqli_query($connect,"update `enquiry` set `type`='$etype' where customer_id='$id'");
            }
            if($etype_customer!='')
            {
                mysqli_query($connect,"update `enquiry` set  `type_customer`='$etype_customer' where customer_id='$id'");
            }
         
            //  echo $ids_str = implode(',', $ecity_id);
            $ids_str = '';

            if (!empty($ecity_id) && is_array($ecity_id)) {
                $ids_str = implode(',', $ecity_id);
            }
            $country_ids_str = '';
            
            if (!empty($ecountry_id) && is_array($ecountry_id)) {
                $country_ids_str = implode(',', $ecountry_id);
            }
            mysqli_query($connect,"update `enquiry` set  `destination`='$edestination',  `days`='$edays', `start_date`='$estart_date', `end_date`='$eend_date',`remark`='$eremark', `adult`='$eadult', `children`='$echildren', `infant`='$einfant',  `country_id` = '$country_ids_str', 
            `city_id` = '$ids_str' where customer_id='$id'");
    
        }
        
    }
}    



if($req==5)
{
    $passport_dob=date('Y-m-d',strtotime($passport_dob));
    $passport_expiry=date('Y-m-d',strtotime($passport_expiry));
    mysqli_query($connect,"update `customer` set  `aadhar`='$aadhar', `pan`='$pan', `passport_expiry`='$passport_expiry', `nationality`='$nationality', `passport_dob`='$passport_dob', `passport_place`='$passport_place', `passport_name`='$passport_name', `passport_no`='$passport_no' where id='$id'");
}
if($req==9)
{
    $user=array();
    $admin=array();
    $sql2 = mysqli_query($connect, "select * from user ORDER BY type ASC");
    if (mysqli_num_rows($sql2) > 0) {
        while ($rs = mysqli_fetch_assoc($sql2)) 
        {
            $user[$rs['id']]=$rs;
            if($rs['type']=='admin')
            {
                $admin[$rs['email']]=$rs['email'];
            }
        }
    }
    $customer=array();
    $sql=mysqli_query($connect,"select * from customer where id='$customer_id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        $customer[$rs['id']]=$rs;
    }
    $sql=mysqli_query($connect,"select * from enquiry where customer_id='$customer_id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        $email1=$user[$rs['assigned_id']]['email'];
        $email2=$user[$assigned_id]['email'];
        $old_status=$rs['status'];
        $email_template = "
        Lead Status Change ".trim($customer[$rs['customer_id']]['name'])." by ".$user[$assigned_id]['name']."<br/><br/>
        From : ".$rs['status'].". <br/>
        To : ".$estatus;
    }
  	if($estatus=='Confirmed' || $estatus=='Travelled')
    {
      $sqls=mysqli_query($connect,"select * from itinerary where customer_id='$customer_id'");
      if(mysqli_num_rows($sqls)>0)
      {
      	mysqli_query($connect,"update `enquiry` set `status`='$estatus' where customer_id='$customer_id'");
        if($old_status!=$estatus)
        {        
            if($estatus=='Confirmed' || $estatus=='DropOut')
            {
                echo email_alert($email1, "Lead Status Change", $email_template);
                email_alert($email2, "Lead Status Change", $email_template);
                foreach($admin as $key=>$val)
                {
                    email_alert($val, "Lead Status Change", $email_template);
                }
            }
        }  
        echo "ok";
      }
      else
      {
        	echo "no itinerary";
      }
    }
  	else
    {
  	  mysqli_query($connect,"update `enquiry` set `status`='$estatus' where customer_id='$customer_id'");
      if($old_status!=$estatus)
      {        
          if($estatus=='Confirmed' || $estatus=='DropOut')
          {
              echo email_alert($email1, "Lead Status Change", $email_template);
              email_alert($email2, "Lead Status Change", $email_template);
              foreach($admin as $key=>$val)
              {
                  email_alert($val, "Lead Status Change", $email_template);
              }
          }
      }
      echo "ok";
    }
}    
if($req==10)
{
    $user=array();
    $sql2 = mysqli_query($connect, "select * from user  ORDER BY type ASC");
    if (mysqli_num_rows($sql2) > 0) {
        while ($rs = mysqli_fetch_assoc($sql2)) 
        {
            $user[$rs['id']]=$rs;            
        }
    }
    $sql=mysqli_query($connect,"select * from customer where id='$customer_id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        $email1=$user[$rs['assigned_id']]['email'];
        $email2=$user[$assigned_id]['email'];
        $old_user=$user[$rs['assigned_id']]['name'];
        $new_user=$user[$assigned_id]['name'];
        $email_template = "
        Lead Trasferred ".$rs['name']."<br/><br/>
        From : ".$user[$rs['assigned_id']]['name'].". <br/>
        To : ".$user[$assigned_id]['name'];
    }
    mysqli_query($connect,"update `enquiry` set `assigned_id`='$assigned_id' where customer_id='$customer_id'");
    mysqli_query($connect,"update `customer` set `assigned_id`='$assigned_id' where id='$customer_id'");
    if($old_user!=$new_user)
    {        
        email_alert($email1, "Lead Trasfer", $email_template);
        email_alert($email2, "Lead Trasfer", $email_template);

    }
}                       
?>