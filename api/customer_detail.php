<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime = date('Y-m-d H:i:s');
$today = date('Y/m/d');
extract($_REQUEST);
if($req==1) 
{
    $data = array();
    $i = 0;
    $where='';
    if($type!='')
    {
        $where="AND type='$type'";
    }
    $sql = mysqli_query($connect, "SELECT * FROM `remark` where `customer_id`= $customer_id $where ORDER BY `time` DESC");
    while ($rs = mysqli_fetch_assoc($sql)) {
        $data[strtotime($rs['time'])] = $rs;
        $data[strtotime($rs['time'])]['data'] = $rs['remark'];
        $data[strtotime($rs['time'])]['strtotime'] = strtotime($rs['time']);
        $data[strtotime($rs['time'])]['type'] = 'feedback';
        $i++;
    }
    $sql = mysqli_query($connect, "SELECT * FROM `reminder`  where `customer_id`= $customer_id $where ORDER BY `time` DESC");
    while ($rs = mysqli_fetch_assoc($sql)) {
        $data[strtotime($rs['time'])] = $rs;
        $data[strtotime($rs['time'])]['data'] = $rs['reminder'];
        $data[strtotime($rs['time'])]['strtotime'] = strtotime($rs['time']);
        $data[strtotime($rs['time'])]['type'] = 'reminder';
        $i++;
    }
    usort($data, function ($a, $b) {
        return new DateTime($b['time']) <=> new DateTime($a['time']);
    });

    $user_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `user` ORDER BY id ASC");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $user_data[$rs2['id']] =  $rs2;
    }

    foreach ($data as $key => $value) {
        if ($value['type'] == 'reminder') {
        ?>
            <div class="item">
                <button type="button" class="btn btn-primary me-1 time-center-btn blue-back no-border font-blue"><?php if ($value['datetime'] == '0000-00-00 00:00:00') {echo "0000-00-00";} else {
                echo date('d M Y', strtotime($value['datetime']));
            } ?></button>
                <div class="content">
                    <div class="col-1 pull-left pe-0 text-center font-blue centerline" style="padding-left: 75px!important;"></div>
                    <div class="col-9 pull-left p-0" style="line-height: 35px;">
                        <div class="col-10 pull-left border-blue pe-0">
                            <div class="col-8 pull-left ">
                                <div class="pull-left w-100 font-blue"><?php echo $value['reminder'] ?></div>
                            </div>
                            <div class="col-4 pull-left p-0">
                                <button type="button" class="btn btn-primary time-center-btn blue-back no-border font-blue in-btn"><?php $username=explode(' ',$user_data[$value['reminderby']]['name']);echo $username[0]; ?></button>
                            </div>
                        </div>
                        <?php if($value['time']!='0000-00-00 00:00:00'){?>
                        <div class="col-2 pull-left"><span class=" btn btn-primary time-center-btn blue-back no-border font-blue in-btn pull-left text-center" style="border: #283477 solid 1px !important;"><?php echo date('d-m-Y h:i a',strtotime($value['time']))?></span></div>
                        <?php }?>
                    </div>
                </div>
            </div>

            <div class="item datetimeline" style=" margin-bottom: 50px; ">
                <button type="button" class="btn btn-primary btn-sm me-1 time-center-small font-blue blue-back btn-border-blue"><?php if ($value['datetime'] == '0000-00-00 00:00:00') {echo "00:00 ";}else{echo date('h:i a', strtotime($value['datetime']));} ?></button>
                <div class="content"></div>
            </div>
        <?php
        } else {
        ?>
            <div class="item">
            <!-- btn btn-primary me-1 time-center-btn light_pink_back2 no-border font-blue  -->
                <button type="button" class="btn btn-primary me-1 time-center-btn blue-back no-border font-blue"><?php echo date('d M Y', strtotime($value['time'])) ?></button>
                <div class="content">
                <!-- centerline-pink -->
                    <div class="col-1 pull-left pe-0 text-center font-blue centerline" style="padding-left: 75px!important;"></div>
                    <div class="col-9 pull-left p-0" style="line-height: 35px;">
                        <div class="col-10 pull-left pink_border pe-0">
                            <div class="col-8 pull-left ">
                                <div class="pull-left w-100 text-black "><?php echo $value['remark'] ?></div>
                            </div>
                            <div class="col-4 pull-left p-0">
                            <!-- btn btn-primary time-center-btn light_pink_back no-border font-blue in-btn-pink -->
                                <button type="button" class="btn btn-primary time-center-btn blue-back no-border font-blue in-btn"><?php $username=explode(' ',$user_data[$value['remarkby']]['name']);echo $username[0]; ?></button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="item datetimeline" style=" margin-bottom: 50px; ">
            <!-- btn btn-primary btn-sm me-1 time-center-small light_pink_back2 no-border font-blue blue-back -->
                <button type="button" class="btn btn-primary btn-sm me-1 time-center-small font-blue blue-back btn-border-blue"><?php echo date('h:i a', strtotime($value['time'])) ?></button>
                <div class="content"></div>
            </div>
        <?php
        }
    }
}
if ($req==2) {
    $sql2 = mysqli_query($connect, "SELECT * FROM customer where `id`= '$customer_id'");
    while ($rs2 = mysqli_fetch_assoc($sql2)) {
        $name =  $rs2['name'];
        $customer_id =  $rs2['id'];
    }
    $reminder=addslashes($reminder);
    if ($edit_to_remark != '') 
    {
        if (mysqli_query($connect, "UPDATE remark SET type='$type',`remark` = '$reminder', `time` = '$remark_time' where id='$edit_to_remark'")) {
            echo "ok";
        }
    } 
    if($remind_time != '')
    {
        $remind_time=date('Y-m-d H:i',strtotime($remind_time));           
        mysqli_query($connect, "insert into reminder (`customer_id`,`name`,`reminderby`,`reminder`,`time`,datetime,type) values ('" . trim($customer_id) . "','" . trim($name) . "','" . trim($remarkby) . "','$reminder','$remind_time','$datetime','$type')");
    }
    else
    {
        mysqli_query($connect, "insert into remark (`customer_id`,`remarkby_name`,`remarkby`,`remark`,`time`,type) values ('" . trim($customer_id) . "','" . trim($name) . "','" . trim($remarkby) . "','$reminder','$datetime','$type')");
    }
    echo "ok";                
}
if ($req == 3) 
{
    $sql2 = mysqli_query($connect, "SELECT * FROM customer where `id`= '$reminder'");
    while ($rs2 = mysqli_fetch_assoc($sql2)) {
        $name =  $rs2['name'];
        $customer_id =  $rs2['id'];
    }
    $remind_time=date('Y-m-d H:i',strtotime($remind_time));        
    if ($edit_to_remind != '') 
    {
        if (mysqli_query($connect, "UPDATE reminder SET `reminder` = '$remind' ,`time` = '$remind_time' where id='$edit_to_remind'")) {
            echo "ok";
        }
    } elseif (mysqli_query($connect, "insert into reminder (`customer_id`,`name`,`reminderby`,`reminder`,`time`,datetime) values ('" . trim($customer_id) . "','" . trim($name) . "','" . trim($remindby) . "','$remind','$remind_time','$datetime')")) {
        echo "ok";
    }

    $sql5 = mysqli_query($connect, "SELECT * FROM reminder where `customer_id`= '$reminder' ORDER BY `time` DESC  LIMIT 1");
    while ($rs5 = mysqli_fetch_assoc($sql5)) {
        $customer_id =  $rs5['id'];
        $can_reminder =  $rs5['time'];
    }
    //$sql6 = mysqli_query($connect, "UPDATE customer SET `can_reminder_id` = '$customer_id',`can_reminder` = '$can_reminder' where id='$reminder'");
}

if ($req == 4) 
{
    $user_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` where id='$id'");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $user_data[] =  $rs2;
        if($rs2['dob']!='0000-00-00')
        {
            $user_data[0]['dob'] =  date('d-m-Y',strtotime($rs2['dob']));
        }
        if($rs2['anniversary']!='0000-00-00')
        {
            $user_data[0]['anniversary'] =  date('d-m-Y',strtotime($rs2['anniversary']));
        }
    }
    $sq2 = mysqli_query($connect, "SELECT * FROM `enquiry` where customer_id='$id'");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $user_data[0]['eno_of_pax'] =  $rs2['no_of_pax'];
        $user_data[0]['edestination'] =  $rs2['destination'];
        $user_data[0]['edays'] =  $rs2['days'];
        $user_data[0]['eadult'] =  $rs2['adult'];
        $user_data[0]['echildren'] =  $rs2['children'];
        $user_data[0]['einfant'] =  $rs2['infant'];
        $user_data[0]['estart_date'] = '';
        if($rs2['start_date']!='0000-00-00')
        {
            $user_data[0]['estart_date'] =  date('d-m-Y',strtotime($rs2['start_date']));
        }
        $user_data[0]['eend_date'] = '';
        if($rs2['end_date']!='0000-00-00')
        {
            $user_data[0]['eend_date'] =  date('d-m-Y',strtotime($rs2['end_date']));
        }
        $user_data[0]['etype'] =  $rs2['type'];
        $user_data[0]['estatus'] =  $rs2['status'];
        $user_data[0]['etype_customer'] =  $rs2['type_customer'];
        $user_data[0]['ecountry_id'] =  $rs2['country_id'];
        $user_data[0]['ecity_id'] =  $rs2['city_id'];
        $user_data[0]['eremark'] =  $rs2['remark'];
    }
    echo json_encode($user_data);
}
if ($req == 5)   
{
    $user_data = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `customer` where id='$id'");
    while ($rs = mysqli_fetch_assoc($sq2)) {
        ?>
        <div class="col-12 pull-left border-blue-bot">
            <div class=" pull-left p-0 font-red col-3">Full Name :</div>
            <div class="col-9 pull-left p-0 font-blue"><?php echo $rs['name']?></div>
        </div>
        <div class="col-12 p-0 pull-left">
            <div class="col-12 pull-left border-blue-bot">
                <div class="col-3 pull-left p-0 font-red">Address :</div>
                <div class="col-9 pull-left font-blue"><?php echo $rs['address']?></div>
            </div>

            <div class="col-12 pull-left border-blue-bot">
                <div class="col-4 pull-left p-0 font-red">Primary Contact :</div>
                <div class="col-8 pull-left p-0 font-blue"><?php echo $rs['number']?></div>
            </div>
            <div class="col-12 pull-left border-blue-bot">
                <div class="col-4 pull-left p-0 font-red">Alt. Number :</div>
                <div class="col-8 pull-left p-0 font-blue"><?php echo $rs['alternatenumber']?></div>
            </div>
            
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-4 pull-left p-0 font-red">Primary Email :</div>
            <div class="col-8 pull-left p-0 font-blue"><?php echo $rs['email']?></div>
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-4 pull-left p-0 font-red">Alt. Email :</div>
            <div class="col-8 pull-left p-0 font-blue"><?php echo $rs['alternateemail']?></div>
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-4 pull-left p-0 font-red">Corporate Name :</div>
            <div class="col-8 pull-left font-blue"><?php echo $rs['corporate']?></div>
        </div>
        <div class=" border-blue-bot w-100 pull-left">
            <div class="col-5 pull-left">
                <div class="col-4 pull-left p-0 font-red">DOB :</div>
                <div class="col-8 pull-left p-0 font-blue"><?php if($rs['dob']!='0000-00-00'){echo date('d M Y',strtotime($rs['dob']));}?></div>
            </div>
            <div class="col-7 pull-left">
                <div class="col-6 pull-left p-0 font-red">Anniversary :</div>
                <div class="col-6 pull-left p-0 font-blue"><?php if($rs['anniversary']!='0000-00-00'){echo date('d M Y',strtotime($rs['anniversary']));}?></div>
            </div>
        </div>
        <div class="d-none col-12 pull-left border-blue-bot mb-1">
            <div class="col-3 pull-left p-0 font-red">Reference :</div>
            <div class="col-9 pull-left font-blue"><?php echo $rs['reference']?></div>
        </div>
    <?php
    echo ",,$";
    ?>
    <div class="col-12 pull-left border-blue-bot">
        <div class=" pull-left p-0 font-red col-4">Aadhar No. :</div>
        <div class="col-8 pull-left p-0 text-right font-blue text-ellipsis"><?php echo $rs['aadhar'];?></div>
    </div>
    <div class="col-12 p-0 pull-left">
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-3 pull-left p-0 font-red">Pan :</div>
            <div class="col-9 pull-left text-right p-0 font-blue text-ellipsis"><?php echo $rs['pan'];?></div>
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-12 pull-left p-0">
                <div class="col-6 pull-left p-0 font-red">Name on Passport :</div>
                <div class="col-6 pull-left p-0 font-blue text-right"><?php echo $rs['passport_name'];?></div>
            </div>            
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-12 pull-left p-0">
                <div class="col-4 pull-left p-0 font-red">Passport No :</div>
                <div class="col-8 pull-left p-0 text-right font-blue"><?php echo $rs['passport_no'];?></div>
            </div>
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-12 pull-left p-0">
                <div class="col-5 pull-left p-0 font-red">Passport Expiry :</div>
                <div class="col-7 pull-left p-0 font-blue text-right"><?php if($rs['passport_expiry']!='0000-00-00'){echo date('d-M-Y',strtotime($rs['passport_expiry']));}?></div>
            </div>            
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-12 pull-left p-0">
                <div class="col-4 pull-left p-0 font-red">Place of issue :</div>
                <div class="col-8 pull-left p-0 font-blue text-right"><?php echo $rs['passport_place'];?></div>
            </div>            
        </div>
        <div class="col-12 pull-left border-blue-bot">
            <div class="col-12 pull-left p-0">
                <div class="col-5 pull-left p-0 font-red">DOB on Passport :</div>
                <div class="col-7 pull-left p-0 font-blue text-right"><?php if($rs['passport_dob']!='0000-00-00'){echo date('d-M-Y',strtotime($rs['passport_dob']));}?></div>
            </div>            
        </div>
    </div>
    <div class="col-12 pull-left">
        <div class="col-3 pull-left p-0 font-red">Nationality :</div>
        <div class="col-9 pull-left font-blue text-right"><?php echo $rs['nationality'];?></div>
    </div>
    <?php
    }
}

?>