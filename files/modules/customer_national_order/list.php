<?php
include('../../includes/inc.main.php');

$Status = $_GET['status'] ? $_GET['status'] : 'P';
$Type = $_GET['type'] ? $_GET['type'] : 'N';
if($Type=='N')
{
    $Title = "Ordenes de Compra ";
    switch ($Status)
    {
        case 'P':
            $Title .= "Pendientes";
        break;
        case 'A':
            $Title .= "Activas";
        break;
        case 'F':
            $Title .= "Finalizadas";
        break;
        case 'I':
            $Title .= "Eliminadas";
        break;
    }
  }else {
    $Title = "Ordenes en Local ";
    switch ($Status)
    {
        
        case 'I':
            $Title .= "Eliminadas";
        break;
    } 
  }
  $Order = new CustomerOrder();
  $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
  $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
  $Head->setTitle($Title);
  $Head->setIcon($Menu->GetHTMLicon());
  $Head->setSubTitle($Menu->GetTitle());
  $Head->setHead();

  /* Header */
  include('../../includes/inc.top.php');
  
  /* Body Content */ 
  // Search List Box
  $Order->ConfigureSearchRequest();
  echo $Order->InsertSearchList();
  // Help Modal
  include('modal.associate.php');
  
  /* Footer */
  $Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
  $Foot->SetScript('../../js/script.searchlist.js');
  $Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
  include('../../includes/inc.bottom.php');
?>