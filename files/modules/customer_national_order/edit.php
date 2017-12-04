<?php
    include("../../includes/inc.main.php");
    
    $ID     = $_GET['id'];
    $Edit   = new CustomerOrder($ID);
    // print_r($Edit); die;
    $Data   = $Edit->GetData();
    ValidateID($Data['order_id']);
    $Status = $Edit->Data['status'];
    if($Status=='F' || $Status=='I')
    {
      if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"].'&error=status');
      }else{
        header('Location: list.php?error=status');
      }
			die();
    }
    $Items  = $DB->fetchAssoc('customer_order_item a INNER JOIN product b ON (a.product_id = b.product_id)','b.title AS product,a.*,(a.price * a.quantity) AS total','order_id='.$ID);
    // $Branch = $DB->fetchAssoc('customer_branch','address','branch_id='.$Data['branch_id']);
    // $Branch = $Branch[0]['address'];
    
    if($Data['type']=='N' && ($Status=='A' || $Data['delivery_id']))
    {
      $DeliveryMan = $Edit->GetDeliveryMan();
    }else{
      $DeliveryOption = ' ';
    }
    
    $OrderType = array("Y"=>"En Local","N"=>"Con Entrega");
    if($Data['type']=='Y')
    {
      $DBName = "name";
    }else{
      $DBName = "CONCAT(name,' - (Z ',zone,')') as name";
    }
    
    $PageTitle = $Data['status']=='V'? "Reactivar":"Editar";
    
    $Head->setTitle($PageTitle." Orden de ".$Data['name']);
    $Head->setSubTitle($Menu->GetTitle());
    $Head->setIcon($Menu->GetHTMLicon());
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setHead();
    include('../../includes/inc.top.php');
?>
  <div class="box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-xs-12">
        
          <div class="innerContainer main_form">
            <!--<form id="new_order">-->
            <?php echo insertElement("hidden","action",'update'); ?>
            <?php echo insertElement("hidden","id",$ID); ?>
            <?php echo insertElement("hidden","type",$Data['type']); ?>
            <?php echo insertElement("hidden","status",$Status); ?>
            <?php echo insertElement("hidden","customer_name",$Data['name']); ?>
            <?php echo insertElement("hidden","items",count($Items)); ?>
            <h4 class="subTitleB"><i class="fa fa-building"></i> Cliente</h4>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php //echo insertElement('select','customer',$Data['branch_id'],'form-control chosenSelect','data-placeholder="Seleccione un Cliente" validateEmpty="Seleccione un cliente"',$DB->fetchAssoc('customer a INNER JOIN customer_branch b ON (a.customer_id=b.customer_id) INNER JOIN customer_type c ON (a.type_id=c.type_id)',"b.branch_id,CONCAT(a.name,' - (Z ',a.zone,')') as address","a.status='A' AND a.company_id=".$_SESSION['company_id'],'b.address'),' ',''); ?>
                  <?php echo insertElement('select','customer',$Data['customer_id'],'form-control chosenSelect','data-placeholder="Seleccione un Cliente" validateEmpty="Seleccione un cliente"',$DB->fetchAssoc('customer',"customer_id,".$DBName,"status='A' AND company_id=".$_SESSION['company_id'],'name'),' ',''); ?>
              </div>
            </div>
            <div id="CustomerData">
              
            </div>
            <br>
            <h4 class="subTitleB"><i class="fa fa-ticket"></i> Tipo de Orden</h4>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php echo insertElement('select','order_type',$Data['type'],'form-control chosenSelect','disabled="disabled"',$OrderType); ?>
              </div>
            </div>
            
            <?php $Hidden = $Data['type']=='N'?'':'Hidden'; ?>
            <div id="DeliveryWrapper" class="<?php echo $Hidden; ?>">
              <h4 class="subTitleB"><i class="fa fa-truck"></i> Repartidor</h4>
              <div class="row form-group inline-form-custom">
                <div class="col-xs-12">
                    <?php echo insertElement('select','delivery_man',$DeliveryMan['admin_id'],'form-control chosenSelect','data-placeholder="Seleccionar un Repartidor"',$DB->fetchAssoc('admin_user a INNER JOIN relation_admin_group b ON (a.admin_id=b.admin_id)',"a.admin_id,CONCAT(a.first_name,' ',a.last_name) AS name","b.group_id=1 AND a.status='A'",'name'),'0','Sin Repartidor'); ?>
                </div>
              </div>
            </div>
           
            <br>
            <h4 class="subTitleB"><i class="fa fa-cubes"></i> Productos</h4>
            
            <div style="margin:0px 10px;">
              <div class="row form-group inline-form-custom bg-light-blue" style="margin-bottom:0px!important;">
                
                <div class="col-sm-4 col-xs-12 txC">
                  <strong>Art&iacute;culo</strong>
                </div>
                <div class="col-sm-2 col-xs-6 txC">
                  <strong>Precio</strong>
                </div>
                <div class="col-sm-2 col-xs-6 txC">
                  <strong>Cantidad</strong>
                </div>
                <div class="col-sm-1 col-xs-6 txC"><strong>Costo</strong></div>
                <div class="col-sm-2 col-xs-6 txC">
                  <strong>Acciones</strong>
                </div>
              </div>
              <hr style="margin-top:0px!important;margin-bottom:0px!important;">
              <!--- ITEMS --->
              <div id="ItemWrapper">
                <?php $I = 1; ?>
                <?php foreach($Items as $Item){?>
                <!--- NEW ITEM --->
                <?php 
                  $Date = explode(" ",$Item['delivery_date']); 
                  $Date = implode("/",array_reverse(explode("-",$Date[0]))); 
                ?>
                <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                  <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                  <div class="col-sm-4 col-xs-12 txC">
                    <span id="Item<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>"><?php echo $Item['product'] ?></span>
                    <?php echo insertElement('select','item_'.$I,$Item['product_id'],'ItemField1'.$I.' form-control chosenSelect itemSelect','item="'.$I.'" ',$DB->fetchAssoc('product a INNER JOIN product_brand b ON (a.brand_id=b.brand_id)',"a.product_id,CONCAT(a.title,' - ',b.name) AS title","a.status='A' AND a.company_id=".$_SESSION['company_id'],'title')); ?>
                  </div>
                  <div class="col-sm-2 col-xs-6 txC">
                    <span id="Price<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>">$ <?php echo $Item['price'] ?></span>
                    <?php echo insertElement('text','price_'.$I,$Item['price'],'ItemField'.$I.' form-control calcable txC','data-inputmask="\'mask\': \'9{+}.99\'" placeholder="Precio" validateEmpty="Ingrese un precio"'); ?>
                  </div>
                  <div class="col-sm-2 col-xs-6 txC">
                    <span id="Quantity<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>"><?php echo $Item['quantity'] ?></span>
                    <?php echo insertElement('text','quantity_'.$I,$Item['quantity'],'ItemField'.$I.' form-control calcable QuantityItem txC','validateOnlyNumbers="Solo se permiten n&uacute;meros" placeholder="Cantidad" validateEmpty="Ingrese una cantidad"'); ?>
                  </div>
                  
                  <div id="item_number_<?php echo $I ?>" class="col-sm-1 col-xs-6 txC item_number" total="<?php echo $Item['total']; ?>" item="<?php echo $I ?>">$ <?php echo $Item['total']; ?></div>
                  <div class="col-sm-2 col-xs-6 txC">
  									  <button type="button" id="SaveItem<?php echo $I ?>" class="btn btnGreen SaveItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-check"></i></button>
  									  <button type="button" id="EditItem<?php echo $I ?>" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-pencil"></i></button>
  									  <?php if($I!=1){ ?>
  									    <button type="button" id="DeleteItem<?php echo $I ?>" class="btn btnRed DeleteItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-trash"></i></button>
  									  <?php } ?>
  								</div>
  								</form>
                </div>
                <!--- NEW ITEM --->
                <?php $I++;} ?>
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
                <a href="../product/new.php" type="button" id="add_new_product" target="_blank" class="btn btn-primary bg-aqua"><i class="fa fa-cube"></i> <strong>Crear Art&iacute;culo</strong></a>
              </div>
              <div class="col-sm-6 col-xs-12 txC">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <!-- /btn-group -->
                <?php $OrderDate = $Data['status']=='V'? '': DBDate($Edit->Data['delivery_date']); echo insertElement('text','delivery_date',$OrderDate,'form-control delivery_date',' placeholder="Fecha de entrega" validateEmpty="Ingrese una fecha de entrega"'); ?>
                </span>
              </div>
            </div>
            
            <h4 class="subTitleB"><i class="fa fa-info-circle"></i> Informaci&oacute;n Extra</h4><div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                  <?php echo insertElement('textarea','extra',$Edit->Data['extra'],'form-control',' placeholder="Ingrese otros datos..."'); ?>
              </div>
          </div>
          <hr>
          <div class="row txC">
            <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-pencil"></i> Editar Orden</button>
            <?php if($Data['type']=='Y'){ ?>
            <button type="button" class="btn btn-dropbox" id="BtnPrint"><i class="fa fa-print"></i> Editar e Imprimir</button>
            <button type="button" class="btn btn-success btnBlue" id="BtnPay"><i class="fa fa-dollar"></i> Editar y Pagar</button>
            <?php } ?>
            <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
          </div>
          <!--</form>-->
        </div>
      </div>
      </div>
    </div><!-- box -->
  </div><!-- box -->
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
include('../../includes/inc.bottom.php');
?>