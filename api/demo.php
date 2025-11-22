<?php
include('db.php');
include('PHPExcel.php');
include('PHPExcel/IOFactory.php');


  $obj = PHPExcel_IOFactory::load('excel.xlsx');
$n = 0;
  
foreach ($obj->getWorksheetIterator() as $sheet) {
    $getHighestRow = $sheet->getHighestRow();

    for ($i = 2; $i <= $getHighestRow; $i++) {

        $name = addslashes($sheet->getCellByColumnAndRow(1, $i)->getValue());
        $corporate = addslashes($sheet->getCellByColumnAndRow(2, $i)->getValue());
        $email1 = addslashes($sheet->getCellByColumnAndRow(3, $i)->getValue());
        $email2 = addslashes($sheet->getCellByColumnAndRow(4, $i)->getValue());
        $contact1 = addslashes($sheet->getCellByColumnAndRow(5, $i)->getValue());
        $contact2 = addslashes($sheet->getCellByColumnAndRow(6, $i)->getValue());

        $address = addslashes($sheet->getCellByColumnAndRow(7, $i)->getValue());
        $reference = addslashes($sheet->getCellByColumnAndRow(8, $i)->getValue());
        
        $sql1 = mysqli_query($connect, "SELECT * FROM `contact` WHERE `name` = '$name' and `corporate` = '$corporate' AND email1='$email1'");
        if (mysqli_num_rows($sql1) <= 0) {
            //mysqli_query($connect, "INSERT INTO `contact` (`name`, `corporate`, `contact1`, `contact2`, `email1`, `email2`, `reference`, `remark`, `address`) VALUES ('$name', '$corporate', '$contact1', '$contact2', '$email1', '$email2', '$reference', '$remark', '$address')");
        }
            
    }
}
?>