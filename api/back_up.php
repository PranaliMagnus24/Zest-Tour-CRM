<?php
header('Access-Control-Allow-Origin: *');
include('db.php');
ini_set('max_input_time', 300000000);
ini_set('max_execution_time', 3000000000);
$datetime = date('Y-m-d H:i:s');
$today = date('Y/m/d');
extract($_REQUEST); 

require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/IOFactory.php';
$query = mysqli_query($connect,"SELECT * FROM candidate");
// $result1 = mysqli_query($connect,"SELECT * FROM salary_data");
/* Create new PHPExcel object*/
$objPHPExcel = new PHPExcel();
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="backup.xls"');
//header('Cache-Control: max-age=0');
// Column names 
$fields = array('unique_id', 'name', 'number', 'email', 'address', 'status');
$ColumnArray2 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'unique_id');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'date');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'name');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'number');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'email');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Country');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Course');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Intake');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Status');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Stage');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Source of Inquiry');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Primary Counsellor');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Assigned Counsellor');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Source of lead');
$objPHPExcel->getActiveSheet()->setCellValue('P1', 'source of lead second');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Added By');
$objPHPExcel->getActiveSheet()->setCellValue('R1', 'Emergency Name');
$objPHPExcel->getActiveSheet()->setCellValue('S1', 'Emergency Phone');
$objPHPExcel->getActiveSheet()->setCellValue('T1', 'Emergency Email');

$source_data = array();
$source_inq = mysqli_query($connect, "SELECT * FROM `add_source_of_inq`");
while ($rs = mysqli_fetch_assoc($source_inq)) {
  $source_data[$rs['id']] = $rs['name'];
}
$counsllors = array();
$coun = mysqli_query($connect, "SELECT * FROM `user`");
while ($rs2 = mysqli_fetch_assoc($coun)) {
  $counsllors[$rs2['id']] = $rs2['name'];
}

$row = 2;
$result = mysqli_query($connect, "SELECT * FROM `candidate` ORDER BY id ASC");
while($row1= mysqli_fetch_assoc($result)) {
  if($row1['name']!='')
  {
    $assigned_councellor=$primary_councellor=$counsllor_name=$source_of_enquiry='';
    if($row1['added_by_id']>0)
    {
      $counsllor_name=$counsllors[$row1['added_by_id']];
    }
    if($row1['source_of_inq']>0 && $row1['source_of_inq']!='')
    {
      $source_of_enquiry=$source_data[$row1['source_of_inq']];
    }
    if(array_key_exists($row1['primary_id'],$counsllors))
    {
      $primary_councellor=$counsllors[$row1['primary_id']];
    }
    if(array_key_exists($row1['assigned_id'],$counsllors))
    {
      $assigned_councellor=$counsllors[$row1['assigned_id']];
    }
    $row_data = array($row1['id'],$row1['unique_id'], $row1['date'], $row1['name'], $row1['number'], $row1['email'], $row1['country'], $row1['course'], $row1['intake'], $row1['status'], $row1['stage'], $source_of_enquiry, $primary_councellor, $assigned_councellor,$row1['source_of_lead'], $row1['source_of_lead_second'],$counsllor_name, $row1['ename'], $row1['ephone'], $row1['eemail']); 
    $col = 0; 
    foreach($row_data as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}
}
 $objPHPExcel->getActiveSheet()->setTitle('Students Details');

// /* Create a new worksheet, after the default sheet*/
 $objPHPExcel->createSheet();

/* Add some data to the second sheet, resembling some different data types*/
$objPHPExcel->setActiveSheetIndex(1);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Country');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'University Name');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Course');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Status');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'comment');

$candidate_data = array();
$candidate_sql = mysqli_query($connect, "SELECT * FROM `candidate`");
while ($rs3 = mysqli_fetch_assoc($candidate_sql)) {
  $candidate_data[$rs3['id']] = $rs3;
}
// echo "<pre>";
// print_r($counsllors);
// echo "</pre>";
$result2 = mysqli_query($connect, "SELECT * FROM `candidate_university`");
$row3 = 2;
while($row2= mysqli_fetch_assoc($result2)) 
{
  $candidate_id = $row2['candidate_id'];
  if(array_key_exists($candidate_id,$candidate_data))
  {
    $row_data2 = array($candidate_data[$candidate_id]['id'],$candidate_data[$candidate_id]['name'], $candidate_data[$candidate_id]['number'], $candidate_data[$candidate_id]['email'], $row2['country_name'], $row2['university_name'],$row2['course'], $row2['status'], $row2['comment']); 
    $col2 = 0; 
    foreach($row_data2 as $key2=>$value2) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col2, $row3, $value2);
        $col2++;
    }
    $row3++;
  }
}

 $objPHPExcel->getActiveSheet()->setTitle('Students Universities');


 // /* Create a new worksheet, after the default sheet 3*/
 $objPHPExcel->createSheet();

/* Add some data to the second sheet, resembling some different data types*/
$objPHPExcel->setActiveSheetIndex(2);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Document type');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Link');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Other');

$result_doc = mysqli_query($connect, "SELECT * FROM `documents`");
$row_doc = 2;
while($row2_doc= mysqli_fetch_assoc($result_doc)) 
{
  $candidate_doc = $row2_doc['candidate_id'];
  if(array_key_exists($candidate_doc,$candidate_data))
  {
    $row_data_doc = array($candidate_data[$candidate_doc]['id'],$candidate_data[$candidate_doc]['name'], $candidate_data[$candidate_doc]['number'], $candidate_data[$candidate_doc]['email'], $row2_doc['type'], $row2_doc['link'], $row2_doc['other']); 
    $col_doc = 0; 
    foreach($row_data_doc as $key_doc=>$value_doc) {
      // echo $col2.','. $row3.','. $value2;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_doc, $row_doc, $value_doc);
        $col_doc++;
    }
    $row_doc++;
  }
}

 $objPHPExcel->getActiveSheet()->setTitle('Students Documents');

 // /* Create a new worksheet, after the default sheet 4*/
 $objPHPExcel->createSheet();

/* Add some data to the second sheet, resembling some different data types*/
$objPHPExcel->setActiveSheetIndex(3);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'School Name');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Board');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Grades');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Year');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Docement Link');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Degree');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Other');

$result_edu = mysqli_query($connect, "SELECT * FROM `education`");
$row_edu = 2;
while($row2_edu= mysqli_fetch_assoc($result_edu)) 
{
  $candidate_edu = $row2_edu['candidate_id'];
  if(array_key_exists($candidate_edu,$candidate_data))
  {
    $row_data_edu = array($candidate_data[$candidate_edu]['id'],$candidate_data[$candidate_edu]['name'], $candidate_data[$candidate_edu]['number'], $candidate_data[$candidate_edu]['email'], $row2_edu['sch_name'], $row2_edu['board'], $row2_edu['grades'], $row2_edu['year'], $row2_edu['doc_link'], $row2_edu['degree'], $row2_edu['other']); 
    $col_edu = 0; 
    foreach($row_data_edu as $key_edu=>$value_edu) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_edu, $row_edu, $value_edu);
        $col_edu++;
    }
    $row_edu++;
  }
}
 $objPHPExcel->getActiveSheet()->setTitle('Students Education');

 
 // /* Create a new worksheet, after the default sheet 5*/
 $objPHPExcel->createSheet();

/* Add some data to the second sheet, resembling some different data types*/
$objPHPExcel->setActiveSheetIndex(4);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Feedback');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Time');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Feedback By');

$result_feed = mysqli_query($connect, "SELECT * FROM `remark`");
$row_feed = 2;
while($row2_feed= mysqli_fetch_assoc($result_feed)) {
  $candidate_feed = $row2_feed['candidate_id'];
  if(array_key_exists($candidate_feed,$candidate_data))
  {
    $remark_by='';
    if(array_key_exists($row2_feed['remarkby'],$counsllors))
    {
        $remark_by=$counsllors[$row2_feed['remarkby']];
    }
    $row_data_feed = array($candidate_data[$candidate_feed]['id'],$candidate_data[$candidate_feed]['name'], $candidate_data[$candidate_feed]['number'], $candidate_data[$candidate_feed]['email'], $row2_feed['remark'], $row2_feed['time'], $remark_by);                                           
    $col_feed = 0; 
    foreach($row_data_feed as $key_feed=>$value_feed) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_feed, $row_feed, $value_feed);
        $col_feed++;
    }
    $row_feed++;
  }
}
 $objPHPExcel->getActiveSheet()->setTitle('Feedbacks');

  // /* Create a new worksheet, after the default sheet 6*/
  $objPHPExcel->createSheet();

  /* Add some data to the second sheet, resembling some different data types*/
  $objPHPExcel->setActiveSheetIndex(5);
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Reminder');
  $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Time');
  $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Reminder By');
  
  $result_rem = mysqli_query($connect, "SELECT * FROM `reminder`");
  $row_rem = 2;
  while($row2_rem= mysqli_fetch_assoc($result_rem)) {
    $candidate_rem = $row2_rem['candidate_id'];
    if(array_key_exists($candidate_rem,$candidate_data))
    {
      $reminder_by='';
      if(array_key_exists($row2_rem['reminderby'],$counsllors))
      {
          $reminder_by=$counsllors[$row2_rem['reminderby']];
      }
      $row_data_rem = array($candidate_data[$candidate_rem]['id'],$candidate_data[$candidate_rem]['name'], $candidate_data[$candidate_rem]['number'], $candidate_data[$candidate_rem]['email'], $row2_rem['reminder'], $row2_rem['time'], $reminder_by);                                           
      $col_rem = 0; 
      foreach($row_data_rem as $key_rem=>$value_rem) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_rem, $row_rem, $value_rem);
          $col_rem++;
      }
      $row_rem++;
    }
  }
   $objPHPExcel->getActiveSheet()->setTitle('Reminders');

     // /* Create a new worksheet, after the default sheet 7*/
  $objPHPExcel->createSheet();

  /* Add some data to the second sheet, resembling some different data types*/
  $objPHPExcel->setActiveSheetIndex(6);
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Test Type');
  $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Test Name');
  $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Test Score');
  $objPHPExcel->getActiveSheet()->setCellValue('H1', 'other');
  
  $result_test = mysqli_query($connect, "SELECT * FROM `test`");
  $row_test = 2;
  while($row2_test= mysqli_fetch_assoc($result_test)) {
    $candidate_test = $row2_test['candidate_id'];
    if(array_key_exists($candidate_test,$candidate_data))
    {
      $row_data_test = array($candidate_data[$candidate_test]['id'],$candidate_data[$candidate_test]['name'], $candidate_data[$candidate_test]['number'], $candidate_data[$candidate_test]['email'], $row2_test['type'], $row2_test['test_name'], $row2_test['score'], $row2_test['other']);                                           
      $col_test = 0; 
      foreach($row_data_test as $key_test=>$value_test) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_test, $row_test, $value_test);
          $col_test++;
      }
      $row_test++;
    }
  }
   $objPHPExcel->getActiveSheet()->setTitle('Test');

   
     // /* Create a new worksheet, after the default sheet 8*/
  $objPHPExcel->createSheet();

  /* Add some data to the second sheet, resembling some different data types*/
  $objPHPExcel->setActiveSheetIndex(7);
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'name');
  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'number');
  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'email');
  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Type');
  $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Employer Name');
  $objPHPExcel->getActiveSheet()->setCellValue('G1', 'From');
  $objPHPExcel->getActiveSheet()->setCellValue('H1', 'To');
  
  $result_exp = mysqli_query($connect, "SELECT * FROM `work_exp`");
  $row_exp = 2;
  while($row2_exp= mysqli_fetch_assoc($result_exp)) {
    $candidate_exp = $row2_exp['candidate_id'];
    if(array_key_exists($candidate_exp,$candidate_data))
    {
      $row_data_exp = array($candidate_data[$candidate_exp]['id'],$candidate_data[$candidate_exp]['name'], $candidate_data[$candidate_exp]['number'], $candidate_data[$candidate_exp]['email'], $row2_exp['type'], $row2_exp['employer_name'], $row2_exp['from'], $row2_exp['to']);                                           
      $col_exp = 0; 
      foreach($row_data_exp as $key_exp=>$value_exp) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_exp, $row_exp, $value_exp);
          $col_exp++;
      }
      $row_exp++;
    }
  }
   $objPHPExcel->getActiveSheet()->setTitle('Work experience');

 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
 $pls = strtotime($datetime).'-'.$user_id.'.xls';
 $objWriter->save('Excels/'.$pls);
mysqli_query($connect,"insert into backup(date,link,user_id) value('$datetime','Excels/$pls','$user_id')");