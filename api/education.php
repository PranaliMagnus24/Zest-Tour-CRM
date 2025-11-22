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
    $sql=mysqli_query($connect,"select * from education where candidate_id='$id' ORDER BY id ASC");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
            $deg[$rs['degree']]=$rs['degree'];
            ?>
            <div class="col-12 pull-left text-center p-0 tab-color position-relative flex-info">
                <div class="col-1 pull-left border-right border-top">
                    <?php if($rs['other']!=''){echo $rs['other'];} else{echo $rs['degree'];} ?>
                </div>
                <div class="col-3 pull-left border-right border-top">
                <?php echo $rs['sch_name'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                <?php echo $rs['board'] ?>
                </div>
                <div class="col-2 pull-left border-right border-top">
                <?php echo $rs['grades'] ?>
                </div>
                <div class="col-1 pull-left border-right border-top">
                <?php echo $rs['year'] ?>
                </div>
                <div class="col-1 pull-left text-ellipsis border-right border-top">
                <a href="<?php echo  $rs['doc_link'] ?>" target="_blank"><?php echo  $rs['doc_link'] ?></a>
                </div>
                <div class="pull-left border_table  border_top col-2">
                    <span class="glyphicon glyphicon-edit p-r-5 col-6 p-0 pull-left" data-bs-target="#add_education" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','','#add_education',''),get_single_education_data('<?php echo $rs['id']?>')" style="    line-height: 18px">Edit</span>
                    <span class="glyphicon glyphicon-trash col-6 p-0 pull-left" data-bs-target="#delete_data" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','education','#delete_data','get_education')" style="line-height: 18px">Delete</span>            
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
    $sql=mysqli_query($connect,"select * from education where id='$id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        echo $rs['degree'].',,$'.$rs['sch_name'].',,$'.$rs['board'].',,$'.$rs['grades'].',,$'.$rs['year'].',,$'.$rs['doc_link'].',,$'.$rs['other'];
    }
}
?>