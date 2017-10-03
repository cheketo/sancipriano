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
  $Deliverys = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Today."'");
  
  //TOMORROW
  $Tomorrow = date('Y-m-d', strtotime(' +1 day'));
  $PendingOrders2 = $DB->numRows("customer_order","order_id","delivery_date = '".$Tomorrow."' AND status = 'P' AND type='N'");
  $Deliverys2 = $DB->numRows("customer_delivery","delivery_id","delivery_date='".$Tomorrow."'");
  
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
     
     <!--EXPIRED ORDERS-->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $ExpiredOrders ?></h3>
            <p>Ordenes Vencidas</p>
          </div>
          <div class="icon">
            <i class="ion ion-clipboard"></i>
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
      
      <!--DELIVERYS-->
      <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $Deliverys ?></h3>
  
              <p>Repartos de hoy</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="../customer_delivery/list.php?delivery_date=today" class="small-box-footer">Repartos del D&iacute;a <i class="fa fa-arrow-circle-right"></i></a>
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
              <h3><?php echo $Deliverys ?></h3>
  
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
   

<?php
  include('../../includes/inc.bottom.php');
?>
