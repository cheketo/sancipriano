<?php
  include('../../includes/inc.main.php');
  $List = new CustomerType();
  $Head->setTitle("Tipos de Clientes");
  $Head->setIcon($Menu->GetHTMLicon());
  $Head->setSubTitle($Menu->GetTitle());
  $Head->setHead();

  /* Header */
  include('../../includes/inc.top.php');
  
  /* Body Content */ 
  // Search List Box
  $List->ConfigureSearchRequest();
  echo $List->InsertSearchList();
  // Help Modal
  //include('modal.help.php');
  
  /* Footer */
  $Foot->SetScript('../../js/script.searchlist.js');
  include('../../includes/inc.bottom.php');
?>