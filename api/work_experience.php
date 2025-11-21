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
    $sql=mysqli_query($connect,"select * from work_exp where candidate_id='$id' ORDER BY id ASC");
    while($rs=mysqli_fetch_assoc($sql))
    {
        ?>
        <?php
    }
}