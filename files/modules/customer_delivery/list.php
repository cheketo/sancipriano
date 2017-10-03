<?php
  include('../../includes/inc.main.php');
  
  $Status = $_GET['status'] ? $_GET['status'] : 'P';
  switch ($Status)
  {
    case 'P':
        $Title = "Pendientes";
      break;
    case 'A':
        $Title = "Activos";
      break;
    case 'F':
        $Title = "Finalizados";
      break;
    case 'I':
        $Title = "Eliminados";
      break;
    case 'V':
        $Title = "Vencidos";
      break;
  }
  
  if($_GET['delivery_date']=='today')
  {
      $Title = 'Pendientes del D&iacute;a';
      $_GET['delivery_date'] = date("d/m/Y");
  }
  
  if($_GET['delivery_date']=='expired')
  {
      $Title = 'Pendientes Vencidos';
  }
  
  $Order = new CustomerDelivery();
  $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
  $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
  $Head->setSubTitle("Repartos ".$Title." a Clientes");
  $Head->setIcon($Menu->GetHTMLicon());
  $Head->setTitle($Menu->GetTitle());
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