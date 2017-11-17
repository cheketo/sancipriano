<?php
    include("../../includes/inc.main.php");
    $New = new CustomerOrder();
    $Head->setTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setIcon($Menu->GetHTMLicon());
    $Head->setHead();
    
    $OrderType = array("Y"=>"En Local","N"=>"Con Entrega");
    
    if($_GET['type']=='Y')
    {
      $TypeClass = 'Hidden';
      $DBName = "name";
    }else{
      $DBName = "CONCAT(name,' - (Z ',zone,')') as name";
    }
    
    include('../../includes/inc.top.php');
?>
  <div class="box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-xs-12">
        
          <div class="innerContainer main_form">
            <!--<form id="new_order">-->
            <?php echo insertElement("hidden","action",'insert'); ?>
            <?php echo insertElement("hidden","type",'N'); ?>
            <?php //echo insertElement("hidden","total_items","1"); ?>
            <?php echo insertElement("hidden","items","1"); ?>
            <?php echo insertElement("hidden","order"); ?>
            <h4 class="subTitleB"><i class="fa fa-building"></i> Cliente</h4>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php echo insertElement('select','customer','','form-control chosenSelect','data-placeholder="Seleccione un Cliente" validateEmpty="Seleccione un cliente"',$DB->fetchAssoc('customer',"customer_id,".$DBName,"status='A' AND company_id=".$_SESSION['company_id'],'name'),' ',''); ?>
              </div>
            </div>
            
            <div id="CustomerData">
              
            </div>
            
            <h4 class="subTitleB"><i class="fa fa-ticket"></i> Tipo de Orden</h4>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php echo insertElement('select','order_type',$_GET['type'],'form-control chosenSelect','disabled="disabled"',$OrderType); ?>
              </div>
            </div>
            
            <div id="DeliveryWrapper" class="<?php echo $TypeClass; ?>">
              <h4 class="subTitleB"><i class="fa fa-truck"></i> Repartidor</h4>
              <div class="row form-group inline-form-custom">
                <div class="col-xs-12">
                    <?php echo insertElement('select','delivery_man','','form-control chosenSelect','data-placeholder="Seleccionar un Repartidor"',$DB->fetchAssoc('admin_user a INNER JOIN relation_admin_group b ON (a.admin_id=b.admin_id)',"a.admin_id,CONCAT(a.first_name,' ',a.last_name) AS name","b.group_id=1 AND status='A'",'name'),'0','Sin Repartidor'); ?>
                </div>
              </div>
            </div>
            
                <?php echo insertElement("text","currency",2,'Hidden','validateEmpty="Seleccione un Moneda"'); ?>
            
            <br>
            <h4 class="subTitleB"><i class="fa fa-cubes"></i> Art&iacute;culos</h4>
            
            <div style="margin:0px 10px;">
              <div class="row form-group inline-form-custom bg-light-blue" style="margin-bottom:0px!important;">
                
                <div class="col-xs-4 txC">
                  <strong>Art&iacute;culo</strong>
                </div>
                <div class="col-xs-1 txC">
                  <strong>Precio</strong>
                </div>
                <div class="col-xs-1 txC">
                  <strong>Cantidad</strong>
                </div>
                <div class="col-xs-3 txC"><strong>Costo</strong></div>
                <div class="col-xs-3 txC">
                  <strong>Acciones</strong>
                </div>
              </div>
              <hr style="margin-top:0px!important;margin-bottom:0px!important;">
              <!--- ITEMS --->
              <div id="ItemWrapper">
                
                <!--- NEW ITEM --->
                <div id="item_row_1" item="1" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                  <form id="item_form_1" name="item_form_1">
                  <div class="col-sm-4 col-xs-5 txC">
                    <span id="Item1" class="Hidden ItemText1"></span>
                    <?php echo insertElement('select','item_1','','ItemField1 form-control chosenSelect itemSelect','item="1" data-placeholder="Seleccione un Art&iacute;culo"',$DB->fetchAssoc('product a INNER JOIN product_brand b ON (a.brand_id=b.brand_id)',"a.product_id,CONCAT(a.title,' - ',b.name) AS title","a.status='A' AND a.company_id=".$_SESSION['company_id'],'title'),' ',''); ?>
                  </div>
                  <div class="col-sm-1 col-xs-3 txC">
                    <span id="Price1" class="Hidden ItemText1"></span>
                    <?php echo insertElement('text','price_1','','ItemField1 form-control calcable txC','data-inputmask="\'mask\': \'9{+}.99\'" placeholder="Precio" validateEmpty="Ingrese un precio"'); ?>
                  </div>
                  <div class="col-sm-1 col-xs-3 txC">
                    <span id="Quantity1" class="Hidden ItemText1"></span>
                    <?php echo insertElement('text','quantity_1','','ItemField1 form-control calcable QuantityItem txC','validateOnlyNumbers="Solo se permiten n&uacute;meros" placeholder="Cantidad" validateEmpty="Ingrese una cantidad"'); ?>
                  </div>
                  <div id="item_number_1" class="col-xs-3 txC item_number" total="0" item="1">$ 0.00</div>
                  <div class="col-sm-3 col-xs-3 txC">
  									  <button type="button" id="SaveItem1" class="btn btnGreen SaveItem" style="margin:0px;" item="1"><i class="fa fa-check"></i></button>
  									  <button type="button" id="EditItem1" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="1"><i class="fa fa-pencil"></i></button>
  									  <!--<button type="button" id="DeleteItem1" class="btn btnRed DeleteItem" style="margin:0px;" item="1"><i class="fa fa-trash"></i></button>-->
  								</div>
  								</form>
                </div>
                <!--- NEW ITEM --->
              </div>
              <!--- TOTALS --->
              <hr style="margin-top:0px!important;">
              <div class="row form-group inline-form-custom bg-light-blue">
                <div class="col-xs-4 txC">
                  Art&iacute;culos Totales: <strong id="TotalItems" >1</strong>
                </div>
                <div class="col-xs-3 txC">
                  Cantidad Total: <strong id="TotalQuantity" >0</strong>
                </div>
                <div class="col-xs-3 txC">
                  Costo Total: <strong  id="TotalPrice">$ 0.00</strong>
                  <?php echo insertElement("hidden","total_price","0"); ?>
                </div>
              </div>
              <!--- TOTALS --->
            </div>
            
            
            <div class="row">
              <div class="col-sm-6 col-xs-12 txC">
                <button type="button" id="add_order_item" class="btn btn-warning"><i class="fa fa-plus"></i> <strong>Agregar Art&iacute;culo</strong></button>
              </div>
              <div class="col-sm-6 col-xs-12 txC">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <!-- /btn-group -->
                <?php echo insertElement('text','delivery_date','','form-control delivery_date',' placeholder="Fecha de entrega" validateEmpty="Ingrese una fecha de entrega"'); ?>
                </span>
              </div>
            </div>
            
            <h4 class="subTitleB"><i class="fa fa-info-circle"></i> Informaci&oacute;n Extra</h4><div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php echo insertElement('textarea','extra','','form-control',' placeholder="Ingrese otros datos..."'); ?>
              </div>
          </div>
          <hr>
          <div class="row txC">
            <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-plus"></i> Crear Orden</button>
            <?php if($_GET['type']=='Y'){ ?>
            <button type="button" class="btn btn-success btnBlue" id="BtnPay"><i class="fa fa-dollar"></i> Crear y Pagar</button>
            <?php }else{ ?>
            <button type="button" class="btn btn-success btnBlue" id="BtnCreateNext"><i class="fa fa-plus"></i> Crear y Agregar Otra</button>
            <?php } ?>
            <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
          </div>
          <!--</form>-->
        </div>
      </div>
    </div><!-- box -->
  </div><!-- box -->
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
// $Foot->setScript('../../../vendors/select2/select2.min.js');
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
include('../../includes/inc.bottom.php');
?>