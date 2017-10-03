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
            header('Location: delivery.list.php?error=status');
        }
	    die();
    }
    $Head->setTitle($Data['address']);
    $Head->setSubTitle("Entrega");
    // $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    // $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setHead();
    include('../../includes/inc.top.php');
?>
    <div class="box animated fadeIn">
        <div class="box-header flex-justify-center">
            <div class="col-xs-12">
                <div class="innerContainer main_form">
                    <?php echo insertElement("hidden","action",'delivery'); ?>
                    <?php echo insertElement("hidden","id",$ID); ?>
                    <?php echo insertElement("hidden","items",count($Items)); ?>
                    <h4 class="subTitleB"><i class="fa fa-cubes"></i> Art&iacute;culos</h4>
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
                        <hr style="margin-top:0px!important;margin-bottom:0px!important;">
                        <!--- ITEMS --->
                        <div id="ItemWrapper">
                            <?php $I = 1; ?>
                            <?php foreach($Data['items'] as $Item){?>
                            <!--- NEW ITEM --->
                            <?php 
                            $Date = explode(" ",$Item['delivery_date']); 
                            $Date = implode("/",array_reverse(explode("-",$Date[0]))); 
                            ?>
                            <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                                    <div class="col-xs-3 txC">
                                        <span id="Item<?php echo $I ?>" class="ItemText<?php echo $I ?>"><i class="fa fa-cube"></i> <?php echo $Item['title'] ?></span>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        $ <span id="Price<?php echo $I ?>" class="ItemText<?php echo $I ?>"><?php echo $Item['price'] ?></span>
                                    </div>
                                    <div class="col-xs-2 txC">
                                        <span id="Quantity<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>"><?php echo $Item['quantity'].' '.$Item['size'] ?></span>
                                        <?php echo insertElement('text','quantity_'.$I,$Item['quantity'],'ItemField'.$I.' form-control calcable QuantityItem txC','data-inputmask="\'mask\': \'9{+}\'" placeholder="Cantidad" validateEmpty="Ingrese una cantidad" style="max-width:50%!important;display:inline-block;"').' '.$Item['size']; ?>
                                    </div>
                                    <div id="item_number_<?php echo $I ?>" class="col-xs-2 txC item_number" total="<?php echo $Item['total']; ?>" item="<?php echo $I ?>">$ <?php echo $Item['total']; ?></div>
                                    <div class="col-xs-3 txC">
                                        <button type="button" id="SaveItem<?php echo $I ?>" title="Entregar" class="btn btnGreen SaveItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-check"></i></button>
                                        <button type="button" id="EditItem<?php echo $I ?>" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-pencil"></i></button>
                                        <button type="button" id="DeleteItem<?php echo $I ?>"  title="No puede ser entregado" class="btn btnRed DeleteItem" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-times"></i></button>	  
                                    </div>
                                </form>
                            </div>
                            <!--- NEW ITEM --->
                            <?php $I++;} ?>
                        </div>
                        <!--- TOTALS --->
                        <hr style="margin-top:0px!important;">
                        <div class="row form-group inline-form-custom bg-red">
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
                    <hr>
                    <div class="row txC">
                        <!--<button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check-square"></i> Terminar Entrega</button>-->
                        <button type="button" class="btn btn-success btnGreen" id="BtnDelivery"><i class="fa fa-check-square"></i> Terminar e Imprimir</button>
                        <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- box -->
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
// $Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
// $Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');
include('../../includes/inc.bottom.php');
?>