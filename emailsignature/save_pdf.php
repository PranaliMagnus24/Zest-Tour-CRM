<?php
header('Access-Control-Allow-Origin: *');
include('../db.php');
extract($_REQUEST);


$prices=json_decode($_REQUEST['price'],true);
$pricess=array();
foreach($prices as $key=>$value)
{
    if($value!='')
    {
        $pricess[]=$value;
    }
}
if($req==1)
{
	$htmls='';
for($i=1;$i<=$total_flight;$i++)
{
    $cust_sql=mysqli_query($connect,"select * from customer where id='$customer_id'");
    while($cust_rs=mysqli_fetch_assoc($cust_sql))
    {
        $email=$cust_rs['email'];
    }
	$flight_detail=json_decode($_REQUEST["flight".$i],true);	
	$j=0;
	foreach($flight_detail['data'] as $key=>$value)
	{
		$times=array();
		$times[]=$value['DepartureDateTime'];
		$times[]=$value['ArrivalDateTime'];
		
		$Duration=get_duration($times);
		$htmls.='
	<tr>
		<td style="text-align:center">'.get_airline_name($value['CarrierCode']).'<br>'.$value['CarrierCode'].' - '.$value['FlightNum'].'<br><img src="https://b2b.riya.travel/Images/AirwaysLogo/'.$value['CarrierCode'].'.png"></td>
			<td style="text-align:center">'.$value['Origins'].'<br>'.$value['DepartureDateTime'].'</td>
			<td style="text-align:center">'.$value['Destinations'].'<br>'.$value['ArrivalDateTime'].'</td>
			<td style="text-align:center">'.$Duration.'</td>
			<td style="text-align:center">'.$value['ClassCode'].'</td>';
			
			if($j==0)
			{
			$htmls.='<td style="text-align:center" rowspan="'.$flight_detail['flight'].'">'.$pricess[$i-1]['price'].'</td>';
			}
			$j++;
			$htmls.='<td style="text-align:center"> '.$value['Baggage'].'</td>
		</tr>';
	}
}

$html='<table border="1" style="width:100%;font-size:10px;border-collapse:collapse;border-color: #ccc;border: 1px solid #ccc;">
	<tbody>
		<tr class="bg_clrtxt" style="background-color:#2d3e52;color:#fff;height: 35px;">
			<td colspan="7" style="text-align: center">
				<lable style="font-size:16px;"><b>'.$O_code.' - '.$D_code.'</b></lable>
			</td>
		</tr>
		<tr style="background-color:#f4f4f4">
			<th style="text-align:center;padding:0 20px;">Airline</th>
			<th style="text-align:center;padding:0 22px;">Departure</th>
			<th style="text-align:center;padding:0 35px;">Arrival</th>
			<th style="text-align:center;padding:0 20px;">Duration</th>
			<th style="text-align:center;padding:0 20px;">Class</th>
			<th style="text-align:center;padding:0 20px;">Gross</th>
			<th style="text-align:center;padding:0 20px;">Baggage</th>
		</tr>
		'.$htmls.'
	</tbody>
</table>';

	email_alert($email, "Fligth", $html);
}
if($req==2)
{
	require_once('../config/tcpdf_include.php');
	class MyCustomPDFWithWatermark extends TCPDF 
	{
		public function Header() {
			// Get the current page break margin
			$bMargin = $this->getBreakMargin();
	
			// Get current auto-page-break mode
			$auto_page_break = $this->AutoPageBreak;
	
			// Disable auto-page-break
			$this->SetAutoPageBreak(false, 0);
	
			// Define the path to the image that you want to use as watermark.
			$img_file = '../app_img/watermark.png';
	
			$this->SetAlpha(0.1);
			$this->setPage( 1 );
			
			// Get the page width/height
			$myPageWidth = $this->getPageWidth();
			$myPageHeight = $this->getPageHeight();
			
			// Find the middle of the page and adjust.
			$myX = ( $myPageWidth / 2 );
			$myY = ( $myPageHeight / 2 );
			// Rotate 45 degrees and write the watermarking text
			$this->StartTransform();
			$this->Rotate(30, $myX, $myY);
			$this->SetFont("freeserif", "", 30);
			//$this->Text($myX, $myY,$this->CustomHeaderText);
			$this->StopTransform();
			
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
	
			// Set the starting point for the page content
			$this->setPageMark();
		}
	}
$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->CustomHeaderText = "HTML";
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Zest Tour');
$pdf->SetFont('freeserif', '', 8, '', true);



$htmls='';
for($i=1;$i<=$total_flight;$i++)
{
    $flight_detail=json_decode($_REQUEST["flight".$i],true);	
	$j=0;
	foreach($flight_detail['data'] as $key=>$value)
	{
		$times=array();
		$times[]=$value['DepartureDateTime'];
		$times[]=$value['ArrivalDateTime'];
		
		$Duration=get_duration($times);
		$htmls.='
	<tr>
			<td style="text-align:center">'.get_airline_name($value['CarrierCode']).'<br>'.$value['CarrierCode'].' - '.$value['FlightNum'].'<br><img src="https://b2b.riya.travel/Images/AirwaysLogo/'.$value['CarrierCode'].'.png"></td>
			<td style="text-align:center">'.$value['Origins'].'<br>'.$value['DepartureDateTime'].'</td>
			<td style="text-align:center">'.$value['Destinations'].'<br>'.$value['ArrivalDateTime'].'</td>
			<td style="text-align:center">'.$Duration.'</td>
			<td style="text-align:center">'.$value['ClassCode'].'</td>';
			
			if($j==0)
			{
			$htmls.='<td style="text-align:center" rowspan="'.$flight_detail['flight'].'">'.$pricess[$i-1]['price'].'</td>';
			}
			$j++;
			$htmls.='<td style="text-align:center"> '.$value['Baggage'].'</td>
		</tr>';
	}
}
$pdf->AddPage();
$html= <<<EOD
<table border="1" style="width:100%;font-size:10px;border-collapse:collapse;border-color: #ccc;border: 1px solid #ccc;">
	<tbody>
		<tr class="bg_clrtxt" style="background-color:#2d3e52;color:#fff;height: 35px;">
			<td colspan="7" style="text-align: center">
				<lable style="font-size:16px;"><b>{$O_code} - {$D_code}</b></lable>
			</td>
		</tr>
		<tr style="background-color:#f4f4f4">
			<th style="text-align:center;padding:0 20px;">Airline</th>
			<th style="text-align:center;padding:0 22px;">Departure</th>
			<th style="text-align:center;padding:0 35px;">Arrival</th>
			<th style="text-align:center;padding:0 20px;">Duration</th>
			<th style="text-align:center;padding:0 20px;">Class</th>
			<th style="text-align:center;padding:0 20px;">Gross</th>
			<th style="text-align:center;padding:0 20px;">Baggage</th>
		</tr>
		{$htmls}
	</tbody>
</table>
EOD;
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->lastPage();

//unlink('../pdf/flight.pdf');
$pdf->Output(BASEPDF.'pdf/flight.pdf', 'D');
     
}
if($req==10)
{
	$htmls='';

$html=' <style>  
        
        @font-face {
            font-family: kindregards;
            src: url(https://zestonline.in/emailsignature/fonts/Angelyta.otf);
        }
        
        @font-face {
            font-family: name;
            src: url(https://zestonline.in/emailsignature/fonts/poppins.regular.ttf);
        }
        
        @font-face {
            font-family: name_main;
            src: url(https://zestonline.in/emailsignature/fonts/poppins.extralight.ttf);
        }
        
    </style>

    <table cellpadding="0" cellspacing="0" style="font-size:12px; line-height: 17px; padding:4px">
        <tbody style="float: left;border-radius: 16px;padding: 4px;">
            <tr>
                <td valign="top " width="200" style="border-right-width: 2px; border-right-style: solid; border-right-color: #fff; background-color: #fff; color: black; padding-bottom: 4px;">
                    <div style="text-align: center ">
                        <h1 style="width: 100%;margin-bottom: 0px;font-size: 33px;font-family: kindregards;font-weight: unset;">kind Regards</h1>
                        <a href="https://www.zesttour.com/" target="_blank">
                            <img src="https://zestonline.in/emailsignature/images/512x351.png" alt="" style="width: 150px;padding-top: 12px;">
                        </a>
                    </div>
                </td>

                <td valign="top " width="290" style="border-right-width: 2px; border-right-style: solid; border-right-color: #fff; background-color: #fff; color: black;padding-bottom: 4px">
                    <div style="right: left; width: 100%; line-height: 25.5px;">
                        <a href="https://twitter.com/ToursZest" target="_blank"> <img style="float: right;width: 7%;padding-right: 10px;" src="https://zestonline.in/emailsignature/images/twitter.png" alt=""> </a>
                        <a href="https://www.youtube.com/channel/UCsnTO08F9vo7KgKReT7O4VQ" target="_blank"> <img style="float: right;width: 7%;padding-right: 10px;" src="https://zestonline.in/emailsignature/images/youtube.png" alt=""> </a>
                        <a href="https://www.linkedin.com/company/zest-tour\'s-&-travels/" target="_blank"> <img style="float: right;width: 6.5%;padding-right: 10px;" src="https://zestonline.in/emailsignature/images/linkedin.png" alt=""> </a>
                    </div>
                    <div style="padding-top: 60px;">
                        <h1 style="width: 100%; margin-bottom: 0px; font-size: 25px;font-family: name; font-weight: bold;">Bhagyashree Hegde</h1>
                        <h1 style="width: 100%;margin-bottom: 0px;font-size: 20px;font-family: name_main;padding-top: 2px;font-weight: bold;">Sales Execetive</h1>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="text-align: center;float: left; border-top: 3px solid red; width: 240%">
                    </div>
                </td>
            </tr>

            <tr>
                <table cellpadding="0" cellspacing="0" style="font-size:12px; font-family: Arial; line-height: 17px; padding: 4px; padding-top: 0;">
                    <tbody style="float: left">
                        <tr>
                            <td valign="top " width="125" style="border-right-width: 2px; border-right-style: solid; border-right-color: #fff; background-color: #fff; color: black; ">
                                <div style="float: left;width: 100%;line-height: 26.5px;font-size: 13px;font-weight: bold;">
                                    <img style="float: left; width: 20%; padding-right: 4px;" src="https://zestonline.in/emailsignature/images/phn.png" alt="">
                                    <a style="float: left; color:black; font-weight: bold; font-family: name;" href="tel:022-61470000" target="_blank">022-61470000</a>
                                </div>

                                <div style="float: left;width: 100%;line-height: 26.5px;font-size: 13px;font-weight: bold;">
                                    <img style="float: left; width: 20%; padding-right: 4px;" src="https://zestonline.in/emailsignature/images/phn.png" alt="">
                                    <a style="float: left; color:black; font-weight: bold; font-family: name;" href="tel:022-61470000" target="_blank">022-61470000</a>
                                </div>

                                <div style="float: left;width: 100%;line-height: 26.5px;font-size: 13px;font-weight: bold;">
                                    <img style="float: left; width: 20%; padding-right: 4px;" src="https://zestonline.in/emailsignature/images/phn.png" alt="">
                                    <a style="float: left; color:black; font-weight: bold; font-family: name;" href="tel:022-61470000" target="_blank">022-61470000</a>
                                </div>
                            </td>

                            <td valign="top " width="400" style="border-right-width: 2px; border-right-style: solid; border-right-color: #fff; background-color: #fff; color: black; ">
                                <div style="float: left;width: 52%;line-height: 25.5px;">
                                    <img style="float: left; width: 13%; padding-right: 4px;" src="https://zestonline.in/emailsignature/images/email.png" alt="">
                                    <a style="float: left; color:black; font-weight: bold; font-family: name;" href="mailto:bhagyashree@zesttour.com" target="_blank">bhagyashree@zesttour.com</a>
                                </div>

                                <div style="float: left;width: 48%;line-height: 25.5px;">
                                    <img style="float: left; width: 13%; padding-right: 4px; padding-left: 10px;" src="https://zestonline.in/emailsignature/images/web.png" alt="">
                                    <a style="float: left; color:black; font-weight: bold; font-family: name;" href="https://www.zesttour.com/" target="_blank">www.zesttour.com</a>
                                </div>

                                <div style="float: left;width: 100%;line-height: 18.5px;padding-top: 15px;">
                                    <img style="float: left;width: 7%;padding-right: 4px; font-family: name; font-weight: bold;" src="https://zestonline.in/emailsignature/images/adress.png" alt="">
                                    <a style="float: left;color: black;font-weight: bold;font-family: name;font-size: 12px;">Pioneer Heritage Residency, Suite-17, ||,Santacruz West,<br> Mumbai, Maharashtra, 400054</a>
                                </div>
                                <div style="right: left; width: 100%; line-height: 25.5px;">
                                    <a href="https://www.facebook.com/ZestToursAndTravels/" target="_blank"> <img style="float: right;width: 35%;padding-right: 35px; padding-top: 10px;" src="https://zestonline.in/emailsignature/images/fb.png" alt=""> </a>
                                    <a href="https://www.instagram.com/zesttourstravels/" target="_blank"> <img style="float: right;width: 35%;padding-right: 10px; padding-top: 10px" src="https://zestonline.in/emailsignature/images/insta.png" alt=""> </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </tr>
        </tbody>
    </table>';

	email_alert('swapnil@creatoactive.com', "Fligth", $html);
}

?>