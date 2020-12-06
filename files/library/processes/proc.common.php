<?php
    include('../../includes/inc.main.php');
    
    if( isset($_GET['action']) && $_GET['action'])
    {
        $Action = $_GET['action'];
    }elseif( isset($_POST['action']) && $_POST['action'] ){
        $Action = $_POST['action'];
    }
    $Action = ucfirst($Action);
    if( isset($_REQUEST['object']) && $_REQUEST['object'] )
    {
        if(strtolower($_REQUEST['object'])!='admindata')
        {
            $Class  = $_REQUEST['object'];
        	$Object = new $Class();
        }else{
        	$Object = $Admin;
        }
        $Object->$Action();
        //var_dump($Object);
    }
?>