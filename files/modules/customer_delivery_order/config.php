<?php
    include("../../includes/inc.main.php");
    
    $ID     = $_GET['id'];
    $Edit   = new CustomerDelivery($ID);
    $Edit->GetOrders();
    $Edit->GetTotalProducts();
    $Data   = $Edit->GetData();
    ValidateID($Data['delivery_id']);
    
    $Head->setTitle('Reparto del '.DBDate($Data['delivery_date']));
    $Head->setSubTitle($Data['delivery_man']);
    $Head->setHead();
    
    include('../../includes/inc.top.php');
?>
    <div class="box animated fadeIn">
        <div class="box-header flex-justify-center">
            <div class="col-xs-12">
                <div class="innerContainer main_form">
                    <?php echo insertElement("hidden","action",'start'); ?>
                    <?php echo insertElement("hidden","id",$ID); ?>
                    <?php echo insertElement("hidden","items",count($Items)); ?>
                    <h4 class="subTitleB"><i class="fa fa-cubes"></i> Productos a Cargar</h4>
                    <div style="margin:0px 10px;">
                        <div class="row form-group inline-form-custom bg-navy" style="margin-bottom:0px!important;">
                            <div class="col-xs-4 txC">
                                <strong>Producto</strong>
                            </div>
                            <div class="col-xs-4 txC">
                                <strong>Cantidad</strong>
                            </div>
                            <div class="col-xs-4 txC">
                                <strong>Total</strong>
                            </div>
                        </div>
                        <hr style="margin-top:0px!important;margin-bottom:0px!important;">
                        <!--- ITEMS --->
                        <div id="ItemWrapper">
                            <?php $I = 1; ?>
                            <?php $Total = 0;
                            foreach($Data['products'] as $Item){
                                $Total = $Total + $Item['total'];
                                if($Class=='bg-gray-active')
                                    $Class='bg-gray';
                                else
                                    $Class='bg-gray-active';
                            ?>
                            <!--- NEW ITEM --->
                            <div id="item_row_<?php echo $I ?>" item="<?php echo $I ?>" class="row form-group inline-form-custom ItemRow <?php echo $Class ?>" style="margin-bottom:0px!important;padding:10px 0px!important;">
                                <form id="item_form_<?php echo $I ?>" name="item_form_<?php echo $I ?>">
                                    <div class="col-xs-4 txC">
                                        <span id="Item<?php echo $I ?>" class="ItemText<?php echo $I ?>"><i class="fa fa-cube"></i> <?php echo $Item['title'] ?></span>
                                    </div>
                                    <div class="col-xs-4 txC">
                                        <span id="Quantity<?php echo $I ?>" class="ItemText<?php echo $I ?>"><?php echo $Item['quantity'].' '.$Item['unit'] ?></span>
                                    </div>
                                    <div id="item_number_<?php echo $I ?>" class="col-xs-4 txC item_number" total="<?php echo $Item['total']; ?>" item="<?php echo $I ?>">$ <?php echo $Item['total']; ?></div>
                                </form>
                            </div>
                            <!--- NEW ITEM --->
                            <?php $I++;} ?>
                        </div>
                        <!--- TOTALS --->
                        <hr style="margin-top:0px!important;">
                        <div class="row form-group inline-form-custom bg-navy">
                            <div class="col-xs-12 txC pull-right">
                                Total a Cobrar: <strong  id="TotalProductPrice">$ <?php echo number_format($Total,2) ?></strong> <span class="text-red">(SIN MERLUZA)</span>
                            </div>
                        </div>
                        <!--- TOTALS --->
                    </div>
                    <div>
                        <i class="fa fa-leaf"></i> <b>Cargar </b> <?php echo insertElement('text','merluza','0','form-control','data-inputmask="\'mask\': \'9{+}\'" placeholder="Cant." validateEmpty="Ingrese una cantidad" style="max-width:60px;display:inline-block;"') ?>
                        <b>kilos de Merluza</b>
                    </div>
                    <hr>
                    <div class="row txC">
                        <!--<button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check-square"></i> Terminar Entrega</button>-->
                        <button type="button" class="btn btn-success btnGreen" id="BtnStart"><i class="fa fa-truck"></i> Empezar Reparto</button>
                        <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- box -->
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
include('../../includes/inc.bottom.php');
?>