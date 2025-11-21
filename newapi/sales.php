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
    $sql=mysqli_query($connect,"select * from sales_contact where id='$id' ORDER BY id ASC");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
            $deg[$rs['degree']]=$rs['degree'];
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
                <?php echo $rs['phone_no'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                <?php echo $rs['comments'] ?>
                </div>
                <?php
                if($user_type=='source')
                {
                    ?>
                    <div class="pull-left border_table  border_top col-2">
                        <span class="p-r-5 col-6 p-0 pull-left" style="line-height: 18px">NA</span>
                        <span class="col-6 p-0 pull-left" style="line-height: 18px">NA</span>
                    </div>
                    <?php 
                }
                else
                {
                ?>
                <div class="pull-left border_table  border_top col-2">
                    <span class="glyphicon glyphicon-edit p-r-5 col-6 p-0 pull-left" data-bs-target="#add_sales" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','','#add_sales',''),get_single_sales_data('<?php echo $rs['id']?>')" style="    line-height: 18px">Edit</span>
                    <span class="glyphicon glyphicon-trash col-6 p-0 pull-left" data-bs-target="#delete_data" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','sales_contact','#delete_data','get_sales')" style="line-height: 18px">Delete</span>            
                </div>
                <?php 
                }
                ?>
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
    $sql=mysqli_query($connect,"select * from sales_contact where id='$id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        echo $rs['degree'].',,$'.$rs['sch_name'].',,$'.$rs['board'].',,$'.$rs['grades'].',,$'.$rs['year'].',,$'.$rs['doc_link'].',,$'.$rs['other'];
    }
}
?>