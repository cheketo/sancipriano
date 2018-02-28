<?php
    include("../../includes/inc.main.php");
    $Head->setTitle($Menu->GetTitle());
    $Head->setHead();
    
    $Title = "An&aacute;lisis";
    
    if($_GET['from'])
    {
      $FromFilter = " AND a.creation_date >= '".$_GET['from']."'";
      $Title .= " del ".DBDate($_GET['from']);
    }else{
     $Title .= " desde el primer d&iacute;a"; 
    }
    if($_GET['to'])
    {
      $ToFilter = " AND a.creation_date <= '".$_GET['to']."'";
      $Title .= " al ".DBDate($_GET['to']);
    }else{
      $Title .= " hasta hoy";
    }
    
    $Report = $DB->fetchAssoc('customer_order_item a',
                    "SUM((a.quantity_delivered-a.quantity_returned)*(SELECT cost FROM product_cost_history WHERE cost_date <=a.creation_date ORDER BY cost_date DESC limit 1)) AS costs,
                    SUM((a.quantity_delivered-a.quantity_returned)*a.price) AS sales,
                    SUM((a.quantity_delivered-a.quantity_returned)*a.price) - SUM((a.quantity_delivered-a.quantity_returned)*(SELECT cost FROM product_cost_history WHERE cost_date <=a.creation_date ORDER BY cost_date DESC limit 1)) AS profits",
                    "a.status <>'P'".$FromFilter.$ToFilter);
    
    if(!$Report[0]['sales']) $Report[0]['sales'] = "0.00";
    if(!$Report[0]['costs']) $Report[0]['costs'] = "0.00";
    if(!$Report[0]['profits']) $Report[0]['profits'] = "0.00";
    // $Query = $BD->lastQuery;
    // $Report[0]['profits'] = -10023.30;
    if($Report[0]['profits']>0)
    {
      $ProfitClass = 'green';
      $ProfitIcon = 'android-done';
    }else{
      $ProfitClass = 'red';
      $ProfitIcon = 'android-close';
    }
    
    include('../../includes/inc.top.php');
    // echo $Query;
?>
  <div class="box animated fadeIn">
  <div class="box-header flex-justify-center">
<div class="col-xs-12">
  <div class="innerContainer main_form">
  <h4 class="subTitleB"><i class="fa fa-calendar"></i> <?php echo $Title ?></h4>
    <div class="row">
      <!--SALES-->
      <div class="col-xs-12">
          <div class="small-box bg-blue">
            <div class="inner">
              <strong>Venta</strong>
              <h3><?php echo "$ ".number_format($Report[0]['sales'],2,",",".") ?></h3>
            </div>
            <div class="icon">
              <i class="ion ion-thumbsup"></i>
            </div>
          </div>
      </div>
      <!--COSTS-->
      <div class="col-xs-12">
          <div class="small-box bg-orange">
            <div class="inner">
              <strong>Costo</strong>
              <h3><?php echo "$ ".number_format($Report[0]['costs'],2,",",".") ?></h3>
            </div>
            <div class="icon">
              <i class="ion ion-thumbsdown"></i>
            </div>
          </div>
      </div>
      <!--PROFITS-->
      <div class="col-xs-12">
          <div class="small-box bg-<?php echo $ProfitClass ?>">
            <div class="inner">
              <strong>Ganancia</strong>
              <h3><?php echo "$ ".number_format($Report[0]['profits'],2,",",".") ?></h3>
            </div>
            <div class="icon">
              <i class="ion ion-<?php echo $ProfitIcon ?>"></i>
            </div>
          </div>
      </div>
    </div>
    
    <hr>
            <div class="row txC">
              <button type="button" class="btn btn-primary" id="BtnCancel"><i class="fa fa-arrow-left"></i> Generar otro An&aacute;lisis</button>
            </div>
      </div><!-- box -->
    </div><!-- box -->
  </div><!-- box -->
  </div><!-- box -->
  
<?php
include('../../includes/inc.bottom.php');
?>