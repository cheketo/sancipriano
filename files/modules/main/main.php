<?php
  include('../../includes/inc.main.php');
  $Menu   = new Menu();
  $Head->setTitle($Menu->GetTitle());
  $Head->setIcon($Menu->GetHTMLicon());
  $Head->setHead();
  
  //TODAY
  $Today = date("Y-m-d");
  $PendingOrders = $DB->numRows("customer_order","order_id","delivery_date = '".$Today."' AND status = 'P' AND type='N'");
  $ExpiredOrders = $DB->numRows("customer_order","order_id","status = 'V' AND type='N'");
  $FinishedOrders = $DB->numRows("customer_order","order_id","delivery_date = '".$Today."' AND status = 'F' AND type='N'");
  
  $PendingDeliverys = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Today."' AND status='P'");
  $ActiveDeliverys = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Today."' AND status='A'");
  $FinishedDeliverys = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Today."' AND status='F'");
  
  $LocalOrders = $DB->numRows("customer_order","order_id","delivery_date = '".$Today."' AND status = 'A' AND type='Y'");
  $ExpiredLocalOrders = $DB->numRows("customer_order","order_id","status = 'A' AND delivery_date < '".$Today."' AND type='Y'");
  $FinishedLocalOrders = $DB->numRows("customer_order","order_id","delivery_date = '".$Today."' AND status = 'F' AND type='Y'");
  
  //echo $DB->LastQuery();
  //TOMORROW
  $Tomorrow = date('Y-m-d', strtotime(' +1 day'));
  $PendingOrders2 = $DB->numRows("customer_order","order_id","delivery_date = '".$Tomorrow."' AND status = 'P' AND type='N'");
  $Deliverys2 = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Tomorrow."'");
  $LocalOrders2 = $DB->numRows("customer_order","order_id","delivery_date = '".$Tomorrow."' AND status = 'P' AND type='Y'");
  
  $DBDateTomorrow = date('d/m/Y', strtotime(' +1 day'));


  include('../../includes/inc.top.php');
 ?>

   <!--<div class="form-group heckbox icheck">-->
   <!-- <button id="alertDemoError" type="button" class="btn btnRed">Error</button>-->
   <!-- <button id="alertDemoSuccess" type="button" class="btn btnGreen">Success</button>-->
   <!-- <button id="alertDemoInfo" type="button" class="btn btnBlue">Info</button>-->
   <!-- <button id="alertDemoWarning" type="button" class="btn btnYellow">Warning</button>-->
   <!--</div>-->

   <!--<button id="activateLoader" type="button" onclick="toggleLoader()" class="btn btnBlue animated fadeIn">Activate Loader</button>-->
   <!--<br><br>-->
   <h3><i class="fa fa-calendar"></i> Hoy</h3>
   
    <div class="row">
      <!--Pending DELIVERYS-->
      <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-orange">
            <div class="inner">
              <h3><?php echo $PendingDeliverys ?></h3>
              <p>Repartos de hoy Sin Empezar</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="../customer_delivery/list.php?delivery_date=today" class="small-box-footer">Repartos del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
          </div>
      </div>
      <!--Active DELIVERYS-->
      <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $ActiveDeliverys ?></h3>
              <p>Repartos de hoy en Progreso</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="../customer_delivery/list.php?delivery_date=today&status=A" class="small-box-footer">Repartos del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
          </div>
      </div>
      <!--Finished DELIVERYS-->
      <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $FinishedDeliverys ?></h3>
              <p>Repartos de hoy Finalizados</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="../customer_delivery/list.php?delivery_date=today&status=F" class="small-box-footer">Repartos del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
          </div>
      </div>
    </div>
    <div class="row">
     
     <!--EXPIRED ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $ExpiredOrders ?></h3>
            <p>Ordenes Vencidas</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-cancel"></i>
          </div>
          <a href="../customer_national_order/list.php?status=V&type=N" class="small-box-footer">Ordenes Vencidas <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
       
      <!--PENDING ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
          <div class="inner">
            <h3><?php echo $PendingOrders ?></h3>
            <p>Ordenes Pendientes</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
          </div>
          <a href="../customer_national_order/list.php?delivery_date=today" class="small-box-footer">Ordenes del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      
      <!--Finished ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $FinishedOrders ?></h3>
            <p>Ordenes Entregadas</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-done"></i>
          </div>
          <a href="../customer_national_order/list.php?delivery_date=today&status=F&type=N" class="small-box-footer">Ordenes del D&iacute;a Entregadas <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
    <div class="row">
     
     <!--EXPIRED LOCAL ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $ExpiredLocalOrders ?></h3>
            <p>Ordenes en Local Vencidas</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-cancel"></i>
          </div>
          <a href="../customer_national_order/list.php?status=A&type=Y" class="small-box-footer">Ordenes en Local Vencidas <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
       
      <!--PENDING LOCAL ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
          <div class="inner">
            <h3><?php echo $LocalOrders ?></h3>
            <p>Ordenes en Local Pendientes de Pago</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
          </div>
          <a href="../customer_national_order/list.php?status=A&type=Y&delivery_date=today" class="small-box-footer">Ordenes en Local del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      
      <!--FINISHED LOCAL ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $FinishedLocalOrders ?></h3>
            <p>Ordenes en Local Finalizadas</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-done"></i>
          </div>
          <a href="../customer_national_order/list.php?status=F&type=Y&delivery_date=today" class="small-box-footer">Ordenes en Local Finalizadas <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      
    </div>
    
      <h3><i class="fa fa-calendar"></i> Ma&ntilde;ana</h3>
      <div class="row">
        <!--PENDING ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
          <div class="inner">
            <h3><?php echo $PendingOrders2 ?></h3>
            <p>Ordenes Pendientes</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
          </div>
          <a href="../customer_national_order/list.php?delivery_date=<?php echo $DBDateTomorrow ?>" class="small-box-footer">Ordenes de Ma&ntilde;ana <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      
      <!--DELIVERYS-->
      <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $Deliverys2 ?></h3>
  
              <p>Repartos de ma&ntilde;ana</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="../customer_delivery/list.php?delivery_date=<?php echo $DBDateTomorrow ?>" class="small-box-footer">Repartos de Ma&ntilde;ana <i class="fa fa-arrow-circle-right"></i></a>
          </div>
      </div>
      <!--  <div class="col-lg-3 col-xs-6">-->
        <!-- small box -->
      <!--  <div class="small-box bg-green">-->
      <!--    <div class="inner">-->
      <!--      <h3>53<sup style="font-size: 20px">%</sup></h3>-->

      <!--      <p>Bounce Rate</p>-->
      <!--    </div>-->
      <!--    <div class="icon">-->
      <!--      <i class="ion ion-stats-bars"></i>-->
      <!--    </div>-->
      <!--    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
      <!--  </div>-->
      <!--</div>-->
      <!--  <div class="col-lg-3 col-xs-6">-->
          <!-- small box -->
      <!--    <div class="small-box bg-yellow">-->
      <!--      <div class="inner">-->
      <!--        <h3>44</h3>-->
  
      <!--        <p>User Registrations</p>-->
      <!--      </div>-->
      <!--      <div class="icon">-->
      <!--        <i class="ion ion-person-add"></i>-->
      <!--      </div>-->
      <!--      <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
      <!--    </div>-->
      <!--</div>-->
      <!--  <div class="col-lg-3 col-xs-6">-->
          <!-- small box -->
      <!--    <div class="small-box bg-aqua">-->
      <!--      <div class="inner">-->
      <!--        <h3>65</h3>-->
  
      <!--        <p>Unique Visitors</p>-->
      <!--      </div>-->
      <!--      <div class="icon">-->
      <!--        <i class="ion ion-pie-graph"></i>-->
      <!--      </div>-->
      <!--      <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
      <!--    </div>-->
      <!--</div>-->
        
    </div>
    <div class="row">
        <!--PENDING LOCAL ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
          <div class="inner">
            <h3><?php echo $LocalOrders2 ?></h3>
            <p>Ordenes en Local</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
          </div>
          <a href="../customer_national_order/list.php?delivery_date=<?php echo $DBDateTomorrow ?>&status=A&type=Y" class="small-box-footer">Ordenes en Local para Ma&ntilde;ana <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      
    </div>
   

<?php
  include('../../includes/inc.bottom.php');
?>
