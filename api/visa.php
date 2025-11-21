<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime = date('Y-m-d H:i:s');
$today = date('Y/m/d');
$admin_email = 'abdul@zesttour.com';
//$admin_email = 'swapnil91991@gmail.com';
extract($_REQUEST);
if ($req == 1) 
{
    if($id=='')
    {
        $user_data = array();
        $sq2 = mysqli_query($connect, "SELECT * FROM `user` ORDER BY id ASC");
        while ($rs2 = mysqli_fetch_assoc($sq2)) {
            $user_data[$rs2['id']] =  $rs2;
        }
        
        mysqli_query($connect,"INSERT INTO visa(`datetime`, `visa`, `place`, `expiry`, `entry_type`,duration,added_by_id,stay,customer_id) VALUES('$datetime','$visa','$place','$expiry','$entry_type','$duration','$assigned_id','$stay','$customer_id')");
    }
    else
    {
        mysqli_query($connect,"update visa set `visa`='$visa', `place`='$place', `expiry`='$expiry', `entry_type`='$entry_type',duration='$duration' where id='$id'");
    }
}
if($req==2)
{
    $sql=mysqli_query($connect,"select * from visa where customer_id='$id' AND expiry>'$date' ORDER BY expiry ASC LIMIT 2");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
        ?>
        <div class="w-100 pull-left  border-blue-bot">
            <div class="col-12 pull-left">
                <div class="col-3 pull-left p-0 font-red">Place of issue:</div>
                <div class="col-9 pull-left font-blue"><?php echo $rs['place'] ?> <span class="pull-right font-red" onclick="set_id('<?php echo $rs['id']?>','#add_visa_modal .id'),get_visa_detail_input('<?php echo $rs['id']?>')" data-bs-target="#add_visa_modal" data-bs-toggle="modal"><ion-icon name="create-outline"></ion-icon> Edit</span></div>
            </div>
            <div class="col-12 pull-left">
                <div class="col-6 p-0 pull-left">
                    <div class="col-4 pull-left p-0 font-red">Visa :</div>
                    <div class="col-8 pull-left font-blue"><?php echo $rs['visa'] ?></div>
                </div>
                <div class="col-6 p-0 pull-left">
                    <div class="col-4 pull-left p-0 font-red">Expiry :</div>
                    <div class="col-8 pull-left font-blue"><?php echo date('d M Y',strtotime($rs['expiry'])) ?></div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
        <div class="w-100 text-center font-red pull-left" style="cursor:pointer;" onclick="active_tab('#visa')">View All</div>                            
        <?php
    }
    else
    {
        echo '<span class="text-center w-100 pull-left pb-1" data-bs-target="#add_visa_modal" data-bs-toggle="modal">Add New Visa</span>';
    }
    echo ",,$";
    $sql=mysqli_query($connect,"select * from visa where customer_id='$id'");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
    ?>
        <div class="col-12 pull-left text-center border-top p-0 flex-info">
            <div class="col-4 pull-left text-left border-right">
                <?php echo $rs['place']?>
            </div>
            <div class="col-2 pull-left text-left border-right" style="min-height: 21px;">
                <?php echo $rs['visa']?>
            </div>
            <div class="col-1 pull-left border-right text-center p-0"><?php echo date('d M Y',strtotime($rs['expiry']))?></div>
            <div class="col-2 pull-left text-left border-right">
                <?php echo $rs['entry_type']?>
            </div>
            <div class="col-2 pull-left text-left border-right">
                <?php echo $rs['duration']?>
            </div>
            <div class="col-1 pull-left">
                <span class="font-red" onclick="set_id('<?php echo $rs['id']?>','#add_visa_modal .id'),get_visa_detail_input('<?php echo $rs['id']?>')" data-bs-target="#add_visa_modal" data-bs-toggle="modal"><ion-icon name="create-outline"></ion-icon> Edit</span>
            </div>
        </div>
    <?php
        }
    }
    else
    {
        echo '<span class="text-center w-100 pull-left pb-1" data-bs-target="#add_visa_modal" data-bs-toggle="modal">Add New Visa</span>';
    }
}
if($req==3)
{
    $visa = array();
    $sq2 = mysqli_query($connect, "SELECT * FROM `visa` where id='$id'");
    while ($rs2 = mysqli_fetch_assoc($sq2)) {
        $visa[] =  $rs2;
    }
    echo json_encode($visa);
}
?>