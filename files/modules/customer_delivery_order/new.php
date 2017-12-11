<?php
    include("../../includes/inc.main.php");
    
    $ID     = $_GET['id'];
    $Edit   = new CustomerDeliveryOrder($ID);
    $Data   = $Edit->GetData();
    ValidateID($Data['order_id']);
    $Status = $Edit->Data['status'];
    if($Status!='A')
    {
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"].'&error=status');
        }else{
            header('Location: list.php?error=status');
        }
	    die();
    }
    
    $Merluza = $DB->fetchAssoc("product a INNER JOIN product_size b ON (a.size_id=b.size_id) INNER JOIN customer c ON (c.customer_id=".$Data['customer_id'].") INNER JOIN product_configuration z","a.*,IF(c.type_id=1,IF(a.variation_id=2,a.cost+z.additional_price_retailer,a.cost+((a.cost*z.additional_percentage_retailer)/100)),IF(c.type_id=2,IF(a.variation_id=2,a.cost+z.additional_price_wholesaler,a.cost+((a.cost*z.additional_percentage_wholesaler)/100)),IF(a.variation_id=2,a.cost+c.additional_price,a.cost+((a.cost*c.additional_percentage)/100)))) price,b.prefix AS size","product_id=1",'z.configuration_id DESC');
    
    foreach($Data['items'] as $Item)
    {
        $Products .= $Products? ','.$Item['product_id']:$Item['product_id'];
    }
    
    $Products = $DB->fetchAssoc("customer_order_item a INNER JOIN customer_order b ON (a.order_id=b.order_id)","product_id,(quantity-quantity_delivered) AS leftover","a.delivered='Y' AND a.quantity_delivered < quantity AND a.product_id IN (".$Products.") AND b.delivery_id=".$Data['delivery_id']);
    // echo $DB->lastQuery();
    for($I=0;$I<count($Data['items']);$I++)
    {
        foreach($Products as $Product)
        {
            if($Data['items'][$I]['product_id']==$Product['product_id'])
            {
                $Data['items'][$I]['leftover']==$Product['leftover'];
            }
        }
    }
    
    $Head->setTitle($Data['name']);
    $Head->setSubTitle("Entrega");
    $Head->setIcon($Menu->GetHTMLicon());
    // $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setHead();
    include('../../includes/inc.top.php');
?>
    <div class="box animated fadeIn">
        <div class="box-header flex-justify-center">
            <div class="col-xs-12">
                <div class="innerContainer main_form">
                    <?php echo insertElement("hidden","action",'delivery'); ?>
                    <?php echo insertElement("hidden","id",$ID); ?>
                    <?php echo insertElement("hidden","cid",$Data['customer_id']); ?>
                    <?php echo insertElement("hidden","items",count($Data['items'])+1); ?>
                    <?php echo insertElement("hidden","checks","00"); ?>
                    <?php echo insertElement("hidden","merluza_price",$Merluza[0]['price']); ?>
                    <h4 class="subTitleB"><i class="fa fa-cubes"></i> Productos</h4>
                    <div style="margin:0px 10px;">
                        <div class="row form-group inline-form-custom bg-red" style="margin-bottom:0px!important;">
                            <div class="col-xs-3 txC">
                                <strong>Art&iacute;culo</strong>
                            </div>
                            <div class="col-xs-2 txC">
                                <strong>Precio</strong>
                            </div>
                                <div class="col-xs-2 txC">
                            <strong>Cantidad</strong>
                            </div>
                            <div class="col-xs-2 txC">
                                <strong>Costo</strong>
                            </div>
                            <div class="col-xs-3 txC">
                                <strong>Acciones</strong>
                            </div>
                        </div>
                        <!--- ITEMS --->
                        <div id="ItemWrapper">
                            <?php $I = 1; ?>
                            <?php 
                                foreach($Data['items'] as $Item)
                                {
                                     if(strtolower($Item['size'])=='kgs')
                                    {
                                        $DecimalKgs = '[.99]';
                                        $Item['quantity'] = number_format($Item['quantity'],2,'.','');
                                    }else{
                                        $DecimalKgs = '';
                                        $Item['quantity'] = number_format($Item['quantity'],0,'.','');
                                    }
                            ?>
                            <!--- NEW ITEM --->
                            <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                                    <div class="col-xs-3 txC">
                                        <span id="Item<?php echo $I ?>" class="ItemText<?php echo $I ?>"><i class="fa fa-cube"></i> <?php echo $Item['title'] ?></span>
                                        <?php echo insertElement('hidden','item_'.$I,$Item['item_id']);?>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        $ <span id="Price<?php echo $I ?>" class="ItemText<?php echo $I ?>"><?php echo $Item['price'] ?></span>
                                        <?php echo insertElement('hidden','price_'.$I,$Item['price']);?>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        <span id="Quantity<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>"><?php echo $Item['quantity'].' '.$Item['size'] ?></span>
                                        <?php echo insertElement('text','quantity_'.$I,$Item['quantity'],'ItemField'.$I.' form-control calcable QuantityItem InputMask txC','data-inputmask="\'mask\': \'9{+}'.$DecimalKgs.'\'" placeholder="Cantidad" validateEmpty="Ingrese una cantidad" validateMaxValue="'.$Item['restriction'].'///Ingrese un valor menor o igual a '.$Item['restriction'].'" style="max-width:50%!important;display:inline-block;"').' '.$Item['size']; ?>
                                    </div>
                                    <div id="item_number_<?php echo $I ?>" class="col-xs-2 txC item_number" total="<?php echo $Item['total']; ?>" item="<?php echo $I ?>">$ <?php echo $Item['total']; ?></div>
                                    <div class="col-xs-3 txC">
                                        <button type="button" id="SaveItem<?php echo $I ?>" title="Entregar" class="btn btnGreen SaveItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-check"></i></button>
                                        <button type="button" id="EditItem<?php echo $I ?>" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-pencil"></i></button>
                                        <button type="button" id="DeleteItem<?php echo $I ?>"  title="No puede ser entregado" class="btn btnRed DeleteItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-times"></i></button>	  
                                        <?php echo insertElement('hidden','selected_'.$I);?>
                                    </div>
                                </form>
                            </div>
                            <!--- NEW ITEM --->
                            <?php $I++;} ?>
                            <!--- MERLUZA --->
                            <?php if($Data['merluza_left']>0){ ?>
                            <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                                    <div class="col-xs-3 txC">
                                        <span id="Item<?php echo $I ?>" class="ItemText<?php echo $I ?>"><i class="fa fa-leaf text-blue"></i> <?php echo $Merluza[0]['title'] ?></span>
                                        <?php echo insertElement('hidden','item_'.$I,1); ?>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        $ <span id="Price<?php echo $I ?>" class="ItemText<?php echo $I ?>"><?php echo $Merluza[0]['price'] ?></span>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        <span id="Quantity<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>">0 <?php ' '.$Merluza[0]['size'] ?></span>
                                        <?php echo insertElement('text','quantity_'.$I,"0",'ItemField'.$I.' form-control calcable QuantityItem txC InputMask','data-inputmask="\'mask\': \'9{+}.99\'" placeholder="Cantidad"  validateMaxValue="'.$Data['merluza_left'].'///Ingrese un valor menor o igual a '.$Data['merluza_left'].'" style="max-width:50%!important;display:inline-block;"').' '.$Merluza[0]['size']; ?>
                                    </div>
                                    <div id="item_number_<?php echo $I ?>" class="col-xs-2 txC item_number" total="0" item="<?php echo $I ?>">$ 0.00</div>
                                    <div class="col-xs-3 txC">
                                        <button type="button" id="SaveItem<?php echo $I ?>" title="Entregar" class="btn btnGreen SaveItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-check"></i></button>
                                        <button type="button" id="EditItem<?php echo $I ?>" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-pencil"></i></button>
                                        <?php echo insertElement('hidden','selected_'.$I);?>
                                    </div>
                                </form>
                            </div>
                            <!--- MERLUZA --->
                            <?php $I++;} ?>
                        </div>
                        <!--- TOTALS --->
                        <div class="row form-group inline-form-custom bg-red">
                            <div class="col-xs-4 txC Hidden">
                                Art&iacute;culos Totales: <strong id="TotalItems" >1</strong>
                            </div>
                            <div class="col-xs-3 txC Hidden">
                                Cantidad Total: <strong id="TotalQuantity" >0</strong>
                            </div>
                            &nbsp;
                        </div>
                        <!--- TOTALS --->
                        <!--- TOTALS --->
                        <div class="row form-group inline-form-custom">
                            <div class="col-xs-12 txC">
                                <h3>Total: <strong  id="TotalPrice" class="text-green">$ 0.00</strong></h3>
                                <?php echo insertElement("hidden","total_price","0"); ?>
                            </div>
                        </div>
                        <!--- TOTALS --->
                    </div>
                    
                    <h4 class="subTitleB"><i class="fa fa-cubes"></i> Pago</h4>
                    <div class="row">
                        <div class="col-sm-3 col-xs-12">Pago con efectivo: <b>$<?php echo insertElement('text','cash','0','form-control txC InputMask','data-inputmask="\'mask\': \'9{+}\'" placeholder="0.00" style="max-width:50px!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"'); ?></b></div>
                        <div class="col-sm-9 col-xs-12 txC">
                            <!--Pago con Cheque: $9,889.00-->
                            <form id="check_form" name="check_form">
                                <div id="check_row" class="row form-group inline-form-custom Hidden" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                    <div class="col-xs-12">
                                        <b><?php echo insertElement('text','check_amount','','form-control txC InputMask CheckForm','data-inputmask="\'mask\': \'$9{+}\'" placeholder="$" validateEmpty="Ingrese un Monto" style="max-width:50%!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"'); ?></b>
                                    </div>
                                    <div class="col-xs-12">
                                        <b><?php echo insertElement('text','check_number','','form-control txC InputMask CheckForm','data-inputmask="\'mask\': \'9999999{+}\'" placeholder="N&deg; Cheque" validateEmpty="Ingrese un N&uacute;mero" style="max-width:50%!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"'); ?></b>
                                    </div>
                                    <div class="col-xs-12">
                                        <b><?php echo insertElement('text','check_from','','form-control txC CheckForm','placeholder="Nombre Emisor" validateEmpty="Ingrese un Nombre" style="max-width:50%!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"'); ?></b>
                                    </div>
                                    <div class="col-xs-12">
                                        <b><?php echo insertElement('select','check_bank','','form-control txC CheckForm ','placeholder="Seleccionar Banco" validateEmpty="Ingrese un Banco" style="max-width:50%!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"',$DB->fetchAssoc('payment_bank',"title AS name,title","",'title'),'','Seleccionar Banco'); ?></b>
                                    </div>
                                    <div class="col-xs-12">
                                        <b><?php echo insertElement('text','check_date','','form-control txC CheckForm delivery_date',' placeholder="Fecha de vencimiento" style="max-width:50%!important;display:inline-block; border:0px; border-bottom:1px solid;padding:0px!important;height:auto;"'); ?></b>f
                                    </div>
                                </div>
                            </form>
                                <button type="button" class="btn bg-navy" id="ShowCheck"><i class="fa fa-credit-card"></i> Agregar Cheque</button>
                                <button type="button" class="btn bg-navy Hidden" id="AddCheck"><i class="fa fa-plus-square"></i> Agregar</button>
                                <button type="button" class="btn btn-danger Hidden" id="CancelCheck"><i class="fa fa-times"></i> Cancelar</button>
                            </div>
                    </div>
                    <div class="row" id="CheckWrapper">
                        <!--<div class="col-xs-12 col-sm-6 col-md-4">-->
                        <!--    <div class="box box-success">-->
                        <!--        <div class="box-header with-border">-->
                        <!--            <h4 class="box-title">Cheque N&deg;871287871278</h4>-->
                        <!--            <div class="box-tools pull-right">-->
                        <!--                <i class="fa fa-times text-danger" style="cursor:pointer;"></i>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="box-body">-->
                        <!--            <div class="row">-->
                        <!--                <div class="col-xs-6">Monto:</div>-->
                        <!--                <div class="col-xs-6"><b>$ 21312</b></div>-->
                        <!--            </div>-->
                        <!--            <div class="row">-->
                        <!--                <div class="col-xs-6">Banco:</div>-->
                        <!--                <div class="col-xs-6"><b>Santander</b></div>-->
                        <!--            </div>-->
                        <!--            <div class="row">-->
                        <!--                <div class="col-xs-6">Emisor:</div>-->
                        <!--                <div class="col-xs-6"><b>Carlos Echegue</b></div>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                    </div>
                    <hr>
                    <div class="row txC">
                        <!--<button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check-square"></i> Terminar Entrega</button>-->
                        <button type="button" class="btn btn-success btnGreen" id="BtnDelivery"><i class="fa fa-check-square"></i> Aceptar y Pagar</button>
                        <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- box -->
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
// $Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
include('../../includes/inc.bottom.php');
?>