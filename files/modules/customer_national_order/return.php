<?php
    include("../../includes/inc.main.php");
    
    $ID     = $_GET['id'];
    $Edit   = new CustomerOrder($ID);
    $Data   = $Edit->GetData();
    ValidateID($Data['order_id']);
    $Status = $Edit->Data['status'];
    
    function ReturnToList()
    {
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"].'&error=status');
        }else{
            header('Location: list.php?type='.$_GET['type'].'&status=F&error=returned');
        }
	    die();
    }
    
    $GoBack = true;
    foreach($Data['items'] as $Item)
    {
        if($Item['return_restriction']>0)
        {
            $GoBack = false;
        }
    }
    
    if($Status!='F' || $GoBack) ReturnToList();
    
    $Customer = new Customer($Data['customer_id']);
    $CData = $Customer->GetData();
    $InitialBalance = $CData['balance']!=0? $CData['balance']*(-1):0;
    
    $Head->setTitle($Data['name']);
    $Head->setSubTitle("Devolver Orden");
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
                    <?php echo insertElement("hidden","action",'returnorder'); ?>
                    <?php echo insertElement("hidden","id",$Data['order_id']); ?>
                    <?php echo insertElement("hidden","cid",$Data['customer_id']); ?>
                    <?php echo insertElement("hidden","items",count($Data['items'])); ?>
                    <h4 class="subTitleB"><i class="fa fa-cubes"></i> Productos</h4>
                    <div style="margin:0px 10px;">
                        <div class="row form-group inline-form-custom bg-black" style="margin-bottom:0px!important;">
                            <div class="col-sm-4 col-xs-12 txC">
                                <strong>Art&iacute;culo</strong>
                            </div>
                            <div class="col-sm-2 col-xs-6 txC">
                                <strong>Precio</strong>
                            </div>
                                <div class="col-sm-2 col-xs-6 txC">
                            <strong>Cantidad</strong>
                            </div>
                            <div class="col-sm-1 col-xs-6 txC">
                                <strong>Costo</strong>
                            </div>
                            <div class="col-sm-2 col-xs-6 txC">
                                <strong>Acciones</strong>
                            </div>
                        </div>
                        <!--- ITEMS --->
                        <div id="ItemWrapper">
                            <?php $I = 1; ?>
                            <?php foreach($Data['items'] as $Item){if($Item['return_restriction']>0){?>
                            <!--- NEW ITEM --->
                            <?php $DecimalKgs = strtolower($Item['size'])=='kgs'? '.99':''; ?>
                            <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow bg-gray" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                                    <div class="col-sm-4 col-xs-12 txC">
                                        <span id="Item<?php echo $I ?>" class="ItemText<?php echo $I ?>"><i class="fa fa-cube"></i> <?php echo $Item['title'] ?></span>
                                        <?php echo insertElement('hidden','item_'.$I,$Item['item_id']);?>
                                    </div>
                                    <div class="col-sm-2 col-xs-6 txC">
                                        $ <span id="PriceReturn<?php echo $I ?>" class="ItemText<?php echo $I ?>"><?php echo $Item['price'] ?></span>
                                        <?php echo insertElement('hidden','price_'.$I,$Item['price']);?>
                                    </div>
                                    <div class="col-sm-2 col-xs-6 txC">
                                        <span id="QuantityReturn<?php echo $I ?>" class="Hidden ItemText<?php echo $I ?>"><?php echo '0 '.$Item['size'] ?></span>
                                        <?php echo insertElement('text','quantity_'.$I,0,'ItemField'.$I.' form-control calcableReturn QuantityItemReturn txC','data-inputmask="\'mask\': \'9{+}'.$DecimalKgs.'\'" placeholder="Cantidad" validateEmpty="Ingrese una cantidad" validateMaxValue="'.$Item['return_restriction'].'///Ingrese un valor menor o igual a '.$Item['return_restriction'].'" style="max-width:70%!important;display:inline-block;"').' '.$Item['size']; ?>
                                    </div>
                                    <div id="item_number_<?php echo $I ?>" class="col-sm-1 col-xs-6 txC item_number" total="0" item="<?php echo $I ?>">$ 0.00</div>
                                    <div class="col-sm-2 col-xs-6 txC">
                                        <button type="button" id="SaveItemReturn<?php echo $I ?>" title="Devolver" class="btn btn-success SaveItemReturn" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-check"></i></button>
                                        <button type="button" id="EditItemReturn<?php echo $I ?>" class="btn btnBlue EditItemReturn Hidden" style="margin:0px;" item="<?php echo $I ?>"><i class="fa fa-pencil"></i></button>
                                        
                                        <?php echo insertElement('hidden','selected_'.$I);?>
                                    </div>
                                </form>
                            </div>
                            <?php $I++;}} ?>
                            <!--- NEW ITEM --->
                        </div>
                        <!--- TOTALS --->
                        <div class="row form-group inline-form-custom bg-black">
                            <div class="col-xs-4 txC Hidden">
                                Art&iacute;culos Totales: <strong id="TotalItemsReturn" >1</strong>
                            </div>
                            <div class="col-xs-3 txC Hidden">
                                Cantidad Total: <strong id="TotalQuantityReturn" >0</strong>
                            </div>
                            &nbsp;
                        </div>
                        <!--- TOTALS --->
                        <div class="row form-group inline-form-custom">
                            <div class="col-xs-12 txC">
                                <h3>Total a Devolver: <strong  id="TotalPriceReturn" class="text-red">$ 0.00</strong></h3>
                                <?php echo insertElement("hidden","total_price","0"); ?>
                            </div>
                        </div>
                        <!--- TOTALS --->
                    </div>
                    
                    
                    <!--- TOTALS --->
                    <div class="row form-group inline-form-custom">
                        <div class="col-sm-6 col-xs-12 txC">
                            <h4>Saldo Incial: <strong  id="InitialBalance" class="">$ <?php echo $InitialBalance; ?></strong></h4>
                            <?php echo insertElement("hidden","initial_balance",$CData['balance']); ?>
                        </div>
                        <div class="col-sm-6 col-xs-12 txC">
                            <h4>Saldo Final: <strong  id="FinalBalance" class="text-green">$ 0.00</strong></h4>
                        </div>
                    </div>
                    <!--- TOTALS --->
                    <hr>
                    <div class="row txC">
                        <!--<button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check-square"></i> Terminar Entrega</button>-->
                        <button type="button" class="btn btn-success btnGreen" id="BtnReturn"><i class="fa fa-retweet"></i> Aceptar y Devolver</button>
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