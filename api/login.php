<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime = date('Y-m-d H:i:s');
$today = date('Y/m/d');
extract($_REQUEST);
if ($req == 1) 
{	
  	$data=[];
    $sql2 = mysqli_query($connect, "select * from user where email='$email' AND password='$password'");
    if (mysqli_num_rows($sql2) > 0) 
    {
      $data['success']=true;
      $data['data']=mysqli_fetch_assoc($sql2);      
    } else {
      $data['success']=false;
        $data['data']= "Invalid Username or Password";
    }
  echo json_encode($data);        
}
if ($req == 2) 
{
    if($id!='')
	{
		$sql2=mysqli_query($connect,"select * from customer where email='$email' AND id NOT LIKE '$id'");
		if(mysqli_num_rows($sql2)>0)
		{
			echo "Email Already Exist";
		}
		else
		{
			echo "ok";
		}
	}
	else
	{
		$sql2=mysqli_query($connect,"select * from customer where email='$email'");
		if(mysqli_num_rows($sql2)>0)
		{
			echo "Email Already Exist";
		}
		else
		{
			echo "ok";
		}
	}
}
if ($req == 3) 
{
    $where="";
    if($type=='domestic' || $type=='iternational' )
    {
        $where="where type='$type'";
    }
    if($type_customer!='')
    {
        if($where!='')
        {
            $where.=" AND type_customer='$type_customer'";
        }
        else
        {
            $where.="where type_customer='$type_customer'";
        }
    }
  	if($where=='')
    {
      $where.="where type NOT IN('admin','sales','account')";
    }
  	else
    {
      $where.="AND type NOT IN('admin','sales','account')";
    }
    $user=array();
  	$sql2 = mysqli_query($connect, "select * from user  $where ORDER BY type ASC,type_customer ASC,designation DESC");
    if (mysqli_num_rows($sql2) > 0) {
        while ($rs = mysqli_fetch_assoc($sql2)) 
        {
            $user[$rs['type']][]=$rs;
        }
        ?>
        <option value="">Select Staff</option>
        <?php
        foreach($user as $key=>$value)
        {
        ?>
        <optgroup class="text-capitalize" label="<?php echo $key?>">
            <?php
            foreach($value as $i_key=>$i_val)
            {
            ?>
            <option class="text-capitalize" style="<?php if($i_val['designation']=='tl'){echo "background: #ededf5;font-weight:bold";}?>"  value="<?php echo $i_val['id']?>"><?php echo $i_val['name']?></option>
            <?php
            } 
            ?>
        </optgroup>
        <?php
        }
        ?>

        <?php
    }
}
if($req==4)
{
    mysqli_query($connect,"update user set password='$password' where id='$id'");
}
if ($req == 10) {
    extract($_REQUEST);
    require '../vendor/autoload.php';  // Ensure Composer's autoloader is included
    $sendgridApiKey = getenv('SENDGRID_API_KEY');
    $fromEmail = 'dev@zesttour.com';  // Sender email address
    
    $sql2 = "select * from user where (email='$email')";

    $ss = mysqli_query($connect, $sql2);
    $unique = rand(10000, 99999);
    $datas = [];

    if (mysqli_num_rows($ss) > 0) {
        while ($rs = mysqli_fetch_assoc($ss)) {
            $id = $rs['id'];
            $email = $rs['email'];
            $email_template = "
                Hello !,<br/><br/>
                We received a request to reset your password. <br/><br/>
                <span style='font-size:20px'>$unique</span>";

            // Update OTP in the database
            mysqli_query($connect, "update user set OTP='$unique' where id='$id'");

            // Send OTP email via SendGrid
            $emailMessage = new \SendGrid\Mail\Mail();
            $emailMessage->setFrom($fromEmail);
            $emailMessage->setSubject("Reset Password OTP");
            $emailMessage->addTo($email);
            $emailMessage->addContent("text/plain", "Your OTP code is: $unique");
            $emailMessage->addContent("text/html", $email_template);

            // Send email using SendGrid API
            $sendgrid = new \SendGrid($sendgridApiKey);
            try {
                $response = $sendgrid->send($emailMessage);
                
                // Log the status and response
                $statusCode = $response->statusCode();
                $body = $response->body();
                $headers = $response->headers();

                // Check if the email was sent successfully
                if ($statusCode == 202) {
                    $datas['success'] = true;
                    $datas['message'] = "OTP sent successfully.";
                } else {
                    $datas['success'] = false;
                    $datas['message'] = "Error sending OTP via email. Status Code: $statusCode";
                    $datas['response_body'] = $body;  // Add response body for debugging
                    $datas['response_headers'] = $headers;  // Add headers for debugging
                }

            } catch (Exception $e) {
                $datas['success'] = false;
                $datas['message'] = "Error sending OTP via email: " . $e->getMessage();
            }
        }
    } else {
        $datas['success'] = false;
        $datas['message'] = "Account details are not valid.";
    }

    echo json_encode($datas);
}


if ($req == 11) {
  extract($_POST);

  $sql2 = mysqli_query($connect, "select * from user where (email='$email') AND OTP='$otp'");


  if (mysqli_num_rows($sql2) > 0) {
      echo "verify";
  } else {
      echo "OTP invalid";
  }
}

if ($req == 12) {
  extract($_POST);

  if (mysqli_num_rows(mysqli_query($connect, "select * from user where (email='$email')")) > 0) {
      if (mysqli_query($connect, "update user set password='$password' where (email='$email') ")) {
          echo "Password is updated";
      }
  } else {
      echo "Account Not Exist";
  }
}

if ($req == 55) {
    if ($id == '') {
        if (mysqli_query($connect, "insert into sales_contact (`candidate_id`,`company_name`,`agent_name`,`email_id`,`phone_no`,`comments`) values ('$candidate_id','$company_name','$agent_name','$email_id','$phone_no','$comments')")) {
            echo "ok";
        }
    } else {
        if (mysqli_query($connect, "update sales_contact set `company_name`='$company_name',`agent_name`='$agent_name',`email_id`='$email_id',`phone_no`='$phone_no',`comments`='$comments' where id='$id'")) {
            echo "ok";
        }
    }
}

if ($req == 77) {
    if ($id == '') {
        if (mysqli_query($connect, "insert into operation_staff (`candidate_id`,`company_name`,`agent_name`,`email_id`,`phone_no`,`comments`) values ('$candidate_id','$company_name','$agent_name','$email_id','$phone_no','$comments')")) {
            echo "ok";
        }
    } else {
        if (mysqli_query($connect, "update operation_staff set `company_name`='$company_name',`agent_name`='$agent_name',`email_id`='$email_id',`phone_no`='$phone_no',`comments`='$comments' where id='$id'")) {
            echo "ok";
        }
    }
}

if ($req == 78) {
    if ($id == '') {
        if (mysqli_query($connect, "insert into accounts (`candidate_id`,`company_name`,`agent_name`,`email_id`,`phone_no`,`comments`) values ('$candidate_id','$company_name','$agent_name','$email_id','$phone_no','$comments')")) {
            echo "ok";
        }
    } else {
        if (mysqli_query($connect, "update accounts set `company_name`='$company_name',`agent_name`='$agent_name',`email_id`='$email_id',`phone_no`='$phone_no',`comments`='$comments' where id='$id'")) {
            echo "ok";
        }
    }
}

?>