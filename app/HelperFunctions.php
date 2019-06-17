<?php
/**
* change plain date to formatted date
*
* @param $unformmated
*/
function dateFormatter($unformatted){
    $frmtDueDate = substr($unformatted, 0, 2);
    $frmtDueMonth = substr($unformatted, 3, 2);
    $frmtDueYear = substr($unformatted, 6, 4);

    $formattedDate = $frmtDueDate."-".$frmtDueMonth."-".$frmtDueYear;

    // $formattedDate = $frmtDueYear."-".$frmtDueMonth."-".$frmtDueDate;

    $output = $formattedDate;

    return $output;
}

?>