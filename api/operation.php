<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime=date('Y-m-d H:i:s');
$today = date('Y/m/d');
extract($_REQUEST);

if ($req == 1) 
{
    $i=1;
    $deg=array();
    $sql=mysqli_query($connect,"select * from operation_staff where candidate_id='$id' ORDER BY id ASC");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
            ?>
            <div class="col-12 pull-left text-center p-0 tab-color position-relative flex-info">
                <div class="col-3 pull-left border-right border-top">
                <?php echo $rs['company_name'] ?>
                </div>
                <div class="col-3 pull-left border-right border-top">
                <?php echo $rs['agent_name'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                <?php echo $rs['email_id'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                 <?php echo $rs['country_code'] ?> <?php echo $rs['phone_no'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                <?php echo $rs['comments'] ?>
                </div>
                <div class="pull-left border_table  border_top col-2">
                    <span class="glyphicon glyphicon-edit p-r-5 col-6 p-0 pull-left" data-bs-target="#add_operation" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','','#add_operation',''),get_single_operation_data('<?php echo $rs['id']?>')" style="    line-height: 18px">Edit</span>
                    <span class="glyphicon glyphicon-trash col-6 p-0 pull-left" data-bs-target="#delete_data" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','operation','#delete_data','get_operation')" style="line-height: 18px">Delete</span>            
                </div>
            </div>

            <?php
        }
    }
    else
    {
        ?>
            <div class="col-12 pull-left text-center p-0 tab-color position-relative">
                No Data Found
            </div>
        <?php
    }
    echo ",,$".json_encode($deg);
}
if ($req == 2) 
{
    $i=1;
    $sql=mysqli_query($connect,"select * from operation_staff where id='$id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        echo $rs['company_name'].',,$'.$rs['agent_name'].',,$'.$rs['email_id'].',,$'.$rs['phone_no'].',,$'.$rs['comments'];
    }
}
?>