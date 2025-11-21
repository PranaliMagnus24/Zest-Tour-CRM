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
            <div class="col-12 pull-left p-0 tab-color position-relative flex-info" style="<?php if($rs['status']=='student accepted'){echo "background:#80e7a9";} ?>">
                <div class="pull-left border_table text-center border-right border_top" style="width: 4%;">
                    <?php echo $i++?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                    <?php echo $rs['country_name']?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                    <?php echo $rs['university_name']?>
                </div>
                <div class="col-1 pull-left border_table border-right border_top">
                <?php echo $rs['course']?> 
                </div>
                <div class="col-1 pull-left border_table border-right border_top">
                    <?php echo $rs['commission']?>
                </div>
                <div class="col-2 pull-left border_table border-right border_top text-capitalize">
                    <?php echo $rs['status']?>                
                </div>
                <div class="col-2 pull-left border_table border-right border_top">
                <?php echo $rs['comment']?>
                </div>
                <?php 
                if($user_type=='source')
                {
                    ?>
                    <div class="pull-left border_table  text-center border_top" style="width: 12.5%;">
                        <span class="p-r-5 col-6 p-0 pull-left" style="line-height: 18px">NA</span>
                        <span class="col-6 p-0 pull-left" style="line-height: 18px">NA</span>
                    </div>
                    <?php 
                }
                else
                {
                ?>
                <div class="pull-left border_table  text-center border_top" style="width: 12.5%;">
                    <span class="glyphicon glyphicon-edit p-r-5 col-6 p-0 pull-left" style="line-height: 18px" data-bs-target="#add_university" data-bs-toggle="modal" onclick="set_delete_id('<?php echo $rs['id']?>','','#add_university',''),get_single_university_data('<?php echo $rs['id']?>')">Edit</span>
                    <span class="glyphicon glyphicon-trash col-6 p-0 pull-left" data-bs-target="#delete_data" onclick="set_delete_id('<?php echo $rs['id']?>','candidate_university','#delete_data','get_university')" data-bs-toggle="modal" style="line-height: 18px">Delete</span>
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
  	$university_name=addslashes($university_name);
  
    if($id=='')
    {     
        mysqli_query($connect,"INSERT INTO `candidate_university` (assigned_name,assigned_id,`university_id`, `university_name`, `course`, `status`, `comment`, `candidate_id`,country_id,country_name) VALUES ('$assigned_name','$assigned_id','$university_id','$university_name','$course','$status','$comment','$candidate_id','$country_id','$country_name')");
        if($status=='student accepted')
        {
            $sql=mysqli_query($connect,"select * from university where id='$university_id'");
            while($rs=mysqli_fetch_assoc($sql))
            {
                $commission=$rs['commission'];
                mysqli_query($connect,"update candidate set commission='$commission' where id='$candidate_id'");
            }
        }
        else
        {
            mysqli_query($connect,"update candidate set commission='' where id='$candidate_id'");
        }
    }
    else
    {
        mysqli_query($connect,"update `candidate_university` set assigned_name='$assigned_name',assigned_id='$assigned_id',`university_id`='$university_id',country_id='$country_id',country_name='$country_name', `university_name`='$university_name', `course`='$course', `status`='$status', `comment`='$comment' where id='$id'");
        if($status=='student accepted')
        {
            $sql=mysqli_query($connect,"select * from university where id='$university_id'");
            while($rs=mysqli_fetch_assoc($sql))
            {
                $commission=$rs['commission'];
                mysqli_query($connect,"update candidate set commission='$commission' where id='$candidate_id'");
            }
        }
        else
        {
            mysqli_query($connect,"update candidate set commission='' where id='$candidate_id'");
        }
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
        $data[$rs['id']]['falg'] = $rs['falg_url'];
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
            <th><?php echo $i++?></th>
            <td><?php if($rs['flag_url']!=''){?><img src="<?php echo $rs['flag_url']?>" width="20px" style="border: 1px solid #ddd;"><?php }?></td>
            <td><?php echo $rs['country']?></td>
            <td class="text-center"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_country .id'),get_single_country('<?php echo $rs['id']?>')"></ion-icon></td>
        </tr>
        <?php    
	}	
}
if ($req == 7) 
{
    $sql = mysqli_query($connect, "select * from country where id='$id'");
	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        echo $rs['country'].",,$".$rs['flag_url'];
    }
}
if ($req == 8) {
    $country = mysqli_real_escape_string($connect, $_POST['country']); // Get the country name from POST data
    $flag_url = mysqli_real_escape_string($connect, $_POST['flag_url']); // Get the flag URL from POST data
    $id = mysqli_real_escape_string($connect, $_POST['id']); // Get the ID from POST data

    if ($id == '') {
        // Insert new country
        if (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM country WHERE country='$country'")) <= 0) {
            mysqli_query($connect, "INSERT INTO country (country, flag_url) VALUES ('$country', '$flag_url')");
            echo "ok";
        } else {
            echo "Country Already Exists";
        }
    } else {
        // Update existing country
        if (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM country WHERE country='$country' AND id NOT LIKE '$id'")) <= 0) {
            $sql = mysqli_query($connect, "UPDATE country SET country='$country', flag_url='$flag_url' WHERE id='$id'");
            mysqli_query($connect, "UPDATE candidate_university SET country_name='$country' WHERE country_id='$id'");
            echo "ok";
        } else {
            echo "Country Already Exists";
        }
    }
}

if($req==9)
{
  $university=addslashes($university);
    if($id=='')
    {
        if(mysqli_num_rows(mysqli_query($connect,"select * from university where country_id='$country_id' AND university='$university'"))<=0)
        {
            mysqli_query($connect,"insert into university(country_id,country_name,university,commission) value('$country_id','$country_name','$university','$commission')");
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
            $sql=mysqli_query($connect,"update university set commission='$commission',country_id='$country_id',country_name='$country_name',university='$university' where id='$id'");
            mysqli_query($connect,"update candidate_university set country_id='$country_id',country_name='$country_name',university_name='$university',commission='$commission' where university_id='$id'");
            $sql=mysqli_query($connect,"select * from candidate_university where status='student accepted' and university_id='$id'");
            while($rs=mysqli_fetch_assoc($sql))
            {
                $id=$rs['candidate_id'];
                mysqli_query($connect,"update candidate set commission='$commission' where id='$id'");
            }
            
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
        <tr class="searching_data" style="display: flex;align-items: initial;">
            <th style="width: 15%;float: left;"><?php echo $i++ ?></th>
            <td style="width: 30%;float: left;"><?php echo $rs['country_name']?></td>
            <td style="float: left;width: 36%;"><?php echo $rs['university']?></td>
            <td style="float: left;width: 25%;"><?php echo $rs['commission']?></td>
            <td class="text-center text-primary p-0" style="width: 12%;float: left;"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_country .id'),get_single_university('<?php echo $rs['id']?>')"></ion-icon></td>
        </tr>
        <?php    
	}	
}
if ($req == 11) 
{
    $i=1;
  	
    $where='';
    if($country_id!='' && $country_id!='All')
    {
        if($where=='')
            $where.="where country_id='$country_id'";
        else
            $where.="&country_id='$country_id'";
    }
    if($commission!='')
    {
        if($where=='')
            $where.="where commission='$commission'";
        else
            $where.="&commission='$commission'";
    }
    $sql = mysqli_query($connect, "select * from university $where ORDER BY country_name ASC,university ASC");

	while ($rs = mysqli_fetch_assoc($sql)) 
    {
        ?>
        <tr class="searching_data" style="display: flex;align-items: initial;">
            <th style="width: 15%;float: left;"><?php echo $i++ ?></th>
            <td style="width: 30%;float: left;"><?php echo $rs['country_name']?></td>
            <td style="float: left;width: 36%;"><?php echo $rs['university']?></td>
            <td style="float: left;width: 25%;"><?php echo $rs['commission']?></td>
            <td class="text-center text-primary p-0" style="width: 12%;float: left;"><ion-icon name="create-outline" onclick="set_id_new('<?php echo $rs['id'] ?>','#add_country .id'),get_single_university('<?php echo $rs['id']?>')"></ion-icon></td>
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