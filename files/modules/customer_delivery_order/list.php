<?php
  include('../../includes/inc.main.php');
  
  $Order = new CustomerDeliveryOrder();
  $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
  $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
  $Head->setTitle("Repartos del D&iacute;a");
  $Head->setIcon($Menu->GetHTMLicon());
  if(isset($_SESSION['first_name']) && isset($_SESSION['last_name']))
  {
    $Head->setSubTitle('de '.$_SESSION['first_name'].' '.$_SESSION['last_name']);
  }
  $Head->setHead();
  
//   $DB->fetchAssoc('customer_delivery',"*","delivery_man_id=".$_SESSION['admin_id']." AND ")

  /* Header */
  include('../../includes/inc.top.php');
  
  /* Body Content */ 
  // Search List Box
  $Order->SetRegsPerView(200);
  $Order->ConfigureSearchRequest();
  echo $Order->InsertSearchList();
  // Help Modal
  // include('modal.associate.php');
  
  /* Footer */
  $Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
  $Foot->SetScript('../../js/script.searchlist.js');
  $Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
  include('../../includes/inc.bottom.php');
?>