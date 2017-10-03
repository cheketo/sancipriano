<?php
    include("../../includes/inc.main.php");
    $New = new CustomerDelivery();
    $Head->setTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setStyle('../../../vendors/jquery-sorteable/sorteable.css'); // Draggable List
    $Head->setHead();
    include('../../includes/inc.top.php');
    
    echo insertElement("hidden","action",'insert');
    echo insertElement("hidden","orders","0");
    echo insertElement("hidden","id","0");
?>
  <!-- ///////// FIRST SCREEN ////////// -->
  <!--<div class="box" id="FirstScreen">-->
    
  <!--    <div class="row">-->
  <!--      <div class="col-sm-3 col-xs-12 txC">-->
  <!--      </div>-->
  <!--      <div class="col-sm-6 col-xs-12 txC">-->
  <!--        <br>-->
  <!--        <div class="categoryTitle"><span><b>Entrega</b> | Seleccione una Fecha</span></div>-->
  <!--        <br>-->
  <!--        <span class="input-group">-->
  <!--          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>-->
  <!--            <form id="FormDate">-->
  <!--              <?php echo insertElement('text','delivery_date','','form-control delivery_date','placeholder="Fecha de entrega" validateEmpty="Ingrese una Fecha"'); ?>  -->
  <!--            </form>-->
  <!--        </span>-->
  <!--      </div>-->
  <!--      <div class="col-sm-3 col-xs-12 txC">-->
  <!--      </div>-->
  <!--    </div>-->
  <!--    <br>-->
  <!--    <div class="txC">-->
  <!--      <button type="button" class="btn btnBlue" id="BtnContinue">Continuar</button>-->
  <!--      <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>-->
  <!--    </div>-->
  <!--    <br>-->
    
  <!--</div>-->
  <!-- ///////// END FIRST SCREEN ////////// -->
  
  <!-- ////////// SECOND SCREEN ////////////////// -->
  <div class="ProductDetails box animated fadeIn" id="SecondScreen">
    <div class="box-header flex-justify-center">
      <div class="col-md-6 ">
        <div class="innerContainer">
          <h4 class="subTitleB"><i class="fa fa-truck"></i> Detalles del Reparto</h4>
          
            <div class="form-group">
              <b>Entrega:</b> <?php echo insertElement('text','delivery_date','','form-control delivery_date','placeholder="Fecha de entrega" validateEmpty="Ingrese una Fecha"'); ?>  
              <!--<b><span id="date_selected"></span></b>-->
            </div>
            <!--<div class="form-group">-->
            <!--  <?php //echo insertElement('text','title','','form-control','placeholder="Nombre del Art&iacute;culo"') ?>-->
            <!--</div>-->
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-12">
                <b>Repartidor:</b>
                <?php echo insertElement('select','delivery_man','','form-control selectTags chosenSelect','validateEmpty="Seleccione un repartidor."',$DB->fetchAssoc("admin_user a INNER JOIN relation_admin_group b ON (a.admin_id=b.admin_id)","a.admin_id,CONCAT(a.first_name,' ',a.last_name) as name","a.status='A' AND b.group_id = 1 AND a.company_id=".$_SESSION['company_id'],"name"),' ','') ?>
              </div>
            </div>
            
            <b>Ordenes del Reparto:</b>
            <!--<div class="row form-group inline-form-custom">-->
            <div>
              <ul id="OrdersList" class="jquerySorteable col-xs-12" style="border:1px solid #333;padding:10px;">
                Seleccione una fecha de entrega
                <!--<li cli="1"><span><i class="fa fa-map-marker"></i> Robertson 1041, Flores</span></li>-->
                <!--<li cli="2"><span><i class="fa fa-map-marker"></i> J. M. Moreno 255, Caballito</span></li>-->
                <!--<li cli="3"><span><i class="fa fa-map-marker"></i> Oro 512, Palermo</span></li>-->
              </ul>
            </div>
            <div id="PendingListContainer">
              <b>Ordenes Pendientes:</b>
            <!--<div class="row form-group inline-form-custom">-->
              <ul id="PendingOrdersList" class="jquerySorteable col-xs-12" style="border:1px solid #333;padding:10px;">
                Seleccione una fecha entrega
                <!--<li cli="1"><span><i class="fa fa-map-marker"></i> Robertson 1041, Flores</span></li>-->
                <!--<li cli="2"><span><i class="fa fa-map-marker"></i> J. M. Moreno 255, Caballito</span></li>-->
                <!--<li cli="3"><span><i class="fa fa-map-marker"></i> Oro 512, Palermo</span></li>-->
              </ul>
            </div>
            <br>
            <br>
              <!--<div class="txC">-->
              <!--  <button type="button" class="btn btn-github" id="AddDelivery"><i class="fa fa-plus"></i> Agregar Reparto</button>-->
              <!--</div>-->
            <!--</div>-->
            <div>
            <div class="form-group textWithCounter">
              <textarea id="extra" name="extra" class="text-center" placeholder="Informac&oacute;n extra" rows="4" maxlength="150"></textarea>
              <div class="indicator-wrapper">
                <p>Caracteres restantes</p>
                <div class="indicator"><span class="current-length">150</span></div>
              </div>
            </div>
            </div>
            <hr>
            <div class="txC">
              <button type="button" class="btn btnGreen" id="BtnCreate"><i class="fa fa-check"></i> Finalizar</button>
              <button type="button" class="btn btnBlue" id="BtnCreateNext"><i class="fa fa-plus"></i> Finalizar y Crear Otro</button>
              <button type="button" class="btn btnRed" id="BtnCancel">Regresar</button>
            </div>
        </div>
        <!-- Description (Character Counter) -->
      </div>
    </div><!-- box -->
  </div><!-- box -->
  
<?php
include("modal.associate.php");
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
$Foot->setScript('../../../vendors/jquery-sorteable/sorteable.js');
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
include('../../includes/inc.bottom.php');
?>