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
    $university_ids=array();
    $sql=mysqli_query($connect,"select * from candidate_university where candidate_id='$id' ORDER BY id ASC");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
            $university_ids[$rs['university_id']]=$rs['university_id'];
            ?>
            <div class="col-12 pull-left p-0 tab-color position-relative flex-info">
                <div class="pull-left border_table text-center border-right border_top" style="width: 4%;">
                    <?php echo $i++?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                    <?php echo $rs['country_name']?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                    <?php echo $rs['university_name']?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                <?php echo $rs['course']?> 
                </div>
                <div class="col-2 pull-left border_table border-right border_top text-capitalize">
                    <?php echo $rs['status']?>                
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                <?php echo $rs['comment']?>
                </div>
                <div class="pull-left border_table  text-center border_top" style="width: 12.5%;">
                    <span class="glyphicon glyphicon-edit p-r-5 col-6 p-0 pull-left" style="    line-height: 18px" data-bs-target="#add_university" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','','#add_university',''),get_single_university_data('<?php echo $rs['id']?>')">Edit</span>
                    <span class="glyphicon glyphicon-trash col-6 p-0 pull-left" data-bs-target="#delete_data" onclick="set_delete_id('<?php echo $rs['id']?>','candidate_university','#delete_data','get_university')" data-bs-toggle="modal" style="line-height: 18px">Delete</span>
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
}
if ($req == 2) {
	$data = array();
    $university_ids=array();
    $sql=mysqli_query($connect,"select * from candidate_university where candidate_id='$id' ORDER BY id ASC");
    if(mysqli_num_rows($sql)>0)
    {
        while($rs=mysqli_fetch_assoc($sql))
        {
            $university_ids[$rs['university_id']]=$rs['university_id'];
        }
    }
	$sql = mysqli_query($connect, "select * from university where country_id='$country_id'");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        if($uni_id==$rs['id'])
        {
            $data[$rs['id']]=$rs;
            $data[$rs['id']]['search_name'] = $rs['university'];
            $data[$rs['id']]['university_name'] = $rs['university'];
            $data[$rs['id']]['university_id'] = $rs['id'];
        }
        else if(!in_array($rs['id'],$university_ids))
        {
            $data[$rs['id']]=$rs;
            $data[$rs['id']]['search_name'] = $rs['university'];
            $data[$rs['id']]['university_name'] = $rs['university'];
            $data[$rs['id']]['university_id'] = $rs['id'];
        }
	}
	echo json_encode($data);
}
if($req==3)
{
    if($id=='')
    {     
        mysqli_query($connect,"INSERT INTO `candidate_university` (`university_id`, `university_name`, `course`, `status`, `comment`, `candidate_id`,country_id,country_name) VALUES ('$university_id','$university_name','$course','$status','$comment','$candidate_id','$country_id','$country_name')");
    }
    else
    {
        mysqli_query($connect,"update `candidate_university` set `university_id`='$university_id',country_id='$country_id',country_name='$country_name', `university_name`='$university_name', `course`='$course', `status`='$status', `comment`='$comment' where id='$id'");
    }
}
if ($req == 4) 
{
    $i=1;
    $sql=mysqli_query($connect,"select * from candidate_university where id='$id'");
    while($rs=mysqli_fetch_assoc($sql))
    {
        echo $rs['university_name'].',,$'.$rs['course'].',,$'.$rs['status'].',,$'.$rs['comment'].',,$'.$rs['university_id'].',,$'.$rs['country_id'].',,$'.$rs['country_name'];
    }
}
if ($req == 5) 
{
	$data = array();
    $sql = mysqli_query($connect, "select * from country ORDER BY country ASC");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        $data[$rs['id']]=$rs;
        $data[$rs['id']]['search_name'] = $rs['country'];
        $data[$rs['id']]['country_name'] = $rs['country'];
        $data[$rs['id']]['country_id'] = $rs['id'];
        $data[$rs['id']]['funs'] ='get_university_list('.$rs['id'].')';
        
	}
	echo json_encode($data);
}
if ($req == 6) 
{
    $i=1;
	$sql = mysqli_query($connect, "select * from country ORDER BY country ASC");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        ?>
        <tr>
            <th scope="row"><?php echo $i++?></th>
            <td><?php echo $rs['country']?></td>
            <td class="text-end text-primary hidden"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_country .id'),get_single_country('<?php echo $rs['id']?>')"></ion-icon></td>
        </tr>
        <?php    
	}	
}
if ($req == 7) 
{
    $sql = mysqli_query($connect, "select * from country where id='$id'");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        echo $rs['country'];
    }
}
if($req==8)
{
    if($id=='')
    {
        if(mysqli_num_rows(mysqli_query($connect,"select * from country where country='$country'"))<=0)
        {
            mysqli_query($connect,"insert into country(country) value('$country')");
            echo "ok";
        }
        else
        {
            echo "Country Already Exists";
        }
    }
    else
    {
        if(mysqli_num_rows(mysqli_query($connect,"select * from country where country='$country' AND id NOT LIKE '$id'"))<=0)
        {
            $sql=mysqli_query($connect,"update country set country='$country' where id='$id'");
            mysqli_query($connect,"update candidate_university set country_name='$country_name' where country_id='$id'");
            echo "ok";
        }
        else
        {
            echo "Country Already Exists";
        }
    }
}
if($req==9)
{
    if($id=='')
    {
        if(mysqli_num_rows(mysqli_query($connect,"select * from university where country_id='$country_id' AND university='$university'"))<=0)
        {
            mysqli_query($connect,"insert into university(country_id,country_name,university) value('$country_id','$country_name','$university')");
            echo "ok";
        }
        else
        {
            echo "University Already Exists";
        }
    }
    else
    {
        if(mysqli_num_rows(mysqli_query($connect,"select * from university where country_id='$country_id' AND university='$university' AND id NOT LIKE '$id'"))<=0)
        {
            $sql=mysqli_query($connect,"update university set country_id='$country_id',country_name='$country_name',university='$university' where id='$id'");
            mysqli_query($connect,"update candidate set country_id='$country_id',country_name='$country_name',university_name='$university' where university_id='$id'");
            echo "ok";
        }
        else
        {
            echo "University Already Exists";
        }
    }
}
if ($req == 10) 
{
    $i=1;
	$sql = mysqli_query($connect, "select * from university ORDER BY country_name ASC,university ASC");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        ?>
        <tr class="searching_data">
            <th scope="row" style="width: 15%;float: left;"><?php echo $i++ ?></th>
            <td style="width: 30%;float: left;"><?php echo $rs['country_name']?></td>
            <td style="float: left;width: 36%;"><?php echo $rs['university']?></td>
            <td class="text-end text-primary" style="width: 19%;float: left;"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_country .id'),get_single_university('<?php echo $rs['id']?>')"></ion-icon></td>
        </tr>
        <?php    
	}	
}
if ($req == 11) 
{
    $i=1;
  	if($country_id=='All')
    {
      	$sql = mysqli_query($connect, "select * from university ORDER BY country_name ASC,university ASC");
    }
    else
    {
      	$sql = mysqli_query($connect, "select * from university where country_id='$country_id' ORDER BY country_name ASC,university ASC");
    }
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        ?>
        <tr class="searching_data">
            <th scope="row" style="width: 15%;float: left;"><?php echo $i++?></th>
            <td style="width: 30%;float: left;"><?php echo $rs['country_name']?></td>
            <td style="float: left;width: 36%;"><?php echo $rs['university']?></td>
            <td class="text-end text-primary" style="width: 19%;float: left;"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_university .id'),get_single_university('<?php echo $rs['id']?>')"></ion-icon></td>
        </tr>
        <?php    
	}	
}
if ($req == 12) 
{
    $sql = mysqli_query($connect, "select * from university where id='$id'");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        echo $rs['country_id'].',,$'.$rs['country_name'].',,$'.$rs['university'];
    }
}
?>