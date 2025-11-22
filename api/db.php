<?php
// $connect=mysqli_connect("localhost", 'admin_enquiry', 'MgdPNGom',"admin_enquiry") or die("error");
$connect=mysqli_connect("localhost", 'root', '',"admin_enquiry") or die("error");
$connect->set_charset("utf8");
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
define('BASEURL','http://localhost/zest-tour-crm/');
define('IMAGEURL','http://localhost/zest-tour-crm/');
$date=date('Y-m-d');
function is_image($webfile)
{
 $fp = @fopen($webfile, "r");
 if ($fp !== false)
  fclose($fp);

 return($fp);
}
function convert_text($text) {

$t = $text;

$specChars = array(
    '!' => '!',    '"' => '',
    '#' => '',    '$' => '$',    '%' => '_',
    '&' => '',    '\'' => '',   '(' => '(',
    ')' => ')',    '*' => '',    '+' => '+',
    ',' => ',',    '-' => '-',    '.' => '.',
    '/' => '',    ':' => '',    ';' => ';',
    '<' => '',    '=' => '=',    '>' => '',
    '?' => '_',    '@' => '@',    '[' => '[',
    '\\' => '',   ']' => ']',    '^' => '',
    '_' => '_',    '`' => '',    '{' => '',
    '|' => '_',    '}' => '',    '~' => '~',
    ',' => ',',  ' ' => '_'
);

foreach ($specChars as $k => $v) {
    $t = str_replace($k, $v, $t);
}

return $t;
}
function upload_image($image,$name,$exe)
{
	$filterData=substr($image, strpos($image, ",")+1); //Get the base-64 string from data
	$decodeData=base64_decode($filterData); //Decode the string
	//echo $decodeData;
	
	$output_dir = "uploads/";
	$RandomNum   = date("Y_m_d_H_i_s");
	$ImageExt    = $exe;
	$NewImageName = $name.'.'.$ImageExt;
	while(file_exists(BASEURL.$output_dir.$NewImageName))
	{
		unlink(BASEURL.$output_dir.$NewImageName);
	}
	if(file_put_contents('uploads/'.$NewImageName, $decodeData))
	return $image=BASEURL.$output_dir. $NewImageName;
	
}
function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hr'),
        array(60 , 'min'),
        array(1 , 'sec')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
}
function dateDiff($time1, $time2, $precision = 6) {
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }

    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();

    foreach ($intervals as $interval) {
      $diffs[$interval] = 0;
      $ttime = strtotime("+1 " . $interval, $time1);
      while ($time2 >= $ttime) {
    $time1 = $ttime;
    $diffs[$interval]++;
    $ttime = strtotime("+1 " . $interval, $time1);
      }
    }

    $count = 0;
    $times = array();
    foreach ($diffs as $interval => $value) {
      if ($count >= $precision) {
    break;
      }
      if ($value > 0) {
    if ($value != 1) {
      $interval .= "s";
    }
    $times[] = $value . " " . $interval;
    $count++;
      }
    }
    return implode(", ", $times);
}
function getpage($total_pages,$limit,$adjacents,$page,$fun)
{
	if ($page == 0 || $page==''){$page = 1;}					
	$prev = $page - 1;							
	$next = $page + 1;							
	$lastpage = ceil($total_pages/$limit);		
	$lpm1 = $lastpage - 1;						
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination width100 display-block text-right\">";
		if ($page > 1) 
			$pagination.= "<a href=\"#\" onclick=\"$fun('$prev')\"><button type='button' class='btn  btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></a>";
		else
			$pagination.= "<span class=\"disabled\"><button type='button' class='btn  btn-outline-primary btn-default disabled waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></span>";	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>$counter</button></span>";
				else
					$pagination.= "<a href=\"#\" onclick=\"$fun('$counter')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
			}
		}
		else if($lastpage > 5 + ($adjacents * 2))	
		{
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary  waves-effect  m-l-5 m-r-5'>$counter</button></span>";
					else
						$pagination.= "<a href=\"#\" onclick=\"$fun('$counter')\"><button type='button' class='btn  border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
				}
				$pagination.= "<button type='button' class='btn btn-outline-primary border-radius-0 border-radius-0 btn-default waves-effect  m-l-5 m-r-5'>...</button>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('$lpm1')\"><button type='button' class='btn border-radius-0 btn-outline-primary border-radius-0 btn-default waves-effect  m-l-5 m-r-5'>$lpm1</button></a>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('$lastpage')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lastpage</button></a>";		
			}
			else if($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"#\" onclick=\"$fun('1')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('2')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
				$pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>$counter</button></span>";
					else
						$pagination.= "<a href=\"#\" onclick=\"$fun('$counter')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
				}
				$pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('$lpm1')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lpm1</button></a>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('$lastpage')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lastpage</button></a>";		
			}
			else
			{
				$pagination.= "<a href=\"#\" onclick=\"$fun('1')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
				$pagination.= "<a href=\"#\" onclick=\"$fun('2')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
				$pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect m-l-5 m-r-5'>$counter</button></span>";
					else
						$pagination.= "<a href=\"#\" onclick=\"$fun('$counter')\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
				}
			}
		}
		
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"#\" onclick=\"$fun('$next')\"><button type='button' class='btn btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></a>";
		else
			$pagination.= "<span class=\"disabled\"><button type='button' class='btn btn-outline-primary btn-primary disabled waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></span>";
		$pagination.= "</div>\n";		
	}

	return $pagination;
}


function email_alert($to, $subject, $message)
{
	require 'phpmailer/PHPMailerAutoload.php';
	$message="<html>
	<head>
		<title>Zest Education</title>
	</head>
	<body style='padding:0px; margin:0px; background:#ccc;'>
	<table width='100%' bgcolor='#ccc' style='padding:20px 0px; margin:0px;'><tr><td width='100%'>

	<table width='70%' style='padding:8px; font-family:verdana, arial; font-size:12px; color:#444;' border='0' cellpadding='0' cellspacing='0' bgcolor='#f8f8f8' align='center'>
		<tr style='background:#bdc5f1'>
			<td style='padding:10px; font-size:16px; text-align:center'>
				<a href='https://zestglobaleducation.com/' style='color:#283477;'>Zest Education</a>
			</td>
		</tr>
		<tr bgcolor='#fcfcfc'>
			<td colspan='2' style='padding:20px 10px;text-align:center;'>
			".$message."
			</td>
		</tr>
		<tr bgcolor='#ddd'>
			<td colspan='2' style='padding:20px 10px; text-align:center'>
				Copyright &copy; 2018. https://zestglobaleducation.com/ A platform By Zest Education
			</td>
		</tr>	
	</table>

	</td></tr></table>
	</body>
	</html>
	";
	
	 $mail = new PHPMailer;

	$mail->isSMTP();    
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);                                  // Set mailer to use SMTP
	$mail->Host = 'Localhost';                       // Specify main and backup server
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'help@zestonline.in';                   // SMTP username
	$mail->Password = 'grGvwgtCS8';               // SMTP password
	$mail->setFrom('help@zestonline.in', 'Zest Global Education');     //Set who the message is to be sent from
	$mail->addReplyTo('help@zestonline.in', 'Zest Global Education');  //Set an alternative reply-to address
 $mail->addAddress($to);   
            // Name is optional
	$mail->isHTML(true);                                  // Set email format to HTML
	 
	$mail->Subject = $subject;
	$mail->Body    = $message;
	$mail->AltBody = $message;
	 
	if(!$mail->send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   exit;
	}
	return "Please Check Your Email";
}
function replacequote($str)
{
return str_replace("'", "\'",$str);
}
function hebrew($string)
{
$str=preg_match("/\p{Hebrew}/u", $string);
if($str=0)
{
	return $string;
}
else
{
}
}

?>