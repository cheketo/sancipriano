<?php
    include("../../includes/inc.main.php");
    $ID = $_GET['id'];
    $View = new Customer($ID);
    $Movements = Movement::GetMovementsByCustomerID($ID);
    
    $Head->setTitle($View->Data['name']);
    $Head->setSubTitle($Menu->GetTitle());
    $Head->setHead();
    include('../../includes/inc.top.php');
    echo insertElement("hidden","cname",$View->Data['name']);
    echo insertElement("hidden","cid",$ID);
?>
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="box box-success animated fadeIn">
                <div class="box-header with-border">
                    <!--<h3 class="box-title">Default Box Example</h3>-->
                    <h3 class="txC">Asignar Cr&eacute;dito</h3>
                    <!--<div class="box-tools pull-right">-->
                    <!--    <i class="fa fa-id-card" aria-hidden="true"></i>-->
                    <!--</div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                <div class="box-body">
                    <form id="new_credit" name="new_credit">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 txC">
                                <h4>Monto:</h4>
                                <?php echo insertElement('text','credit','','form-control txC','validateOnlyNumbers="Ingrese n&uacute;meros &uacute;nicamente." validateEmpty="Ingrese un monto" placeholder="Monto"'); ?>
                            </div>
                            <div class="col-sm-6 col-xs-12 txC">
                                <h4>Detalle:</h4>
                                <?php echo insertElement('textarea','credit_description','','form-control','validateEmpty="Ingrese un detalle" placeholder="Detalle"'); ?>
                            </div>
                        </div>
                    </form>
                </div><!-- /.box-body -->
                <div class="box-footer txC">
                    <button type="button" class="btn btn-success btnGreen" id="BtnCredit"><i class="fa fa-plus"></i> Asignar Cr&eacute;dito</button>
                </div><!-- box-footer -->
            </div><!-- /.box -->
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="box box-danger animated fadeIn">
                <div class="box-header with-border">
                    <!--<h3 class="box-title">Default Box Example</h3>-->
                    <h3 class="txC">Cargar D&eacute;bito</h3>
                    <!--<div class="box-tools pull-right">-->
                    <!--    <i class="fa fa-id-card" aria-hidden="true"></i>-->
                    <!--</div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                <div class="box-body">
                    <form id="new_debit" name="new_debit">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 txC">
                                <h4>Monto:</h4>
                                <?php echo insertElement('text','debit','','form-control txC','validateOnlyNumbers="Ingrese n&uacute;meros &uacute;nicamente." validateEmpty="Ingrese un monto" placeholder="Monto"'); ?>
                            </div>
                            <div class="col-sm-6 col-xs-12 txC">
                                <h4>Detalle:</h4>
                                <?php echo insertElement('textarea','debit_description','','form-control','validateEmpty="Ingrese un detalle" placeholder="Detalle"'); ?>
                            </div>
                        </div>
                    </form>
                </div><!-- /.box-body -->
                <div class="box-footer txC">
                    <button type="button" class="btn btn-success btnRed" id="BtnDebit"><i class="fa fa-minus"></i> Asignar D&eacute;bito</button>
                </div><!-- box-footer -->
            </div><!-- /.box -->
        </div>
    </div>
 
    <div class="box box-info animated fadeIn">
        <div class="box-header">
            <h4 class=" txC">Cuenta Corriente de <?php echo $View->Data['name'] ?></h4>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
            <table class="table table-striped txC">
                <tbody>
                    <tr>
                        <th class="txC">Fecha</th>
                        <th class="txC">Concepto</th>
                        <th class="txC">Detalle</th>
                        <th class="txC">Monto</th>
                        <th class="txC">Balance</th>
                        <th class="txC">Acciones</th>
                    </tr>
                    <?php 
                        foreach($Movements as $Movement)
                        {
                            $MovementClass = $Movement['operation']=="D"? "danger":"success";
                            $BalanceClass = $Movement['balance']<0? "danger":"success";
                            if($Movement['type_id']=="2")
                            {
                                $Movement['concept'] .= $Movement['payment_id']=="1"? " en Efectivo":" con Cheque";
                            }else{
                                $AdditionalText = "";
                            }
                    ?>
                    <tr>
                        <td><?php echo DBDate($Movement['creation_date']) ?></td>
                        <td><?php echo $Movement['type'] ?></td>
                        <td><?php echo $Movement['concept'] ?></td>
                        <td>
                            <span class="label label-<?php echo $MovementClass; ?>">$<?php echo $Movement['amount'] ?></span>
                        </td>
                        <td>
                            <span class="label label-<?php echo $BalanceClass; ?>">$<?php echo $Movement['balance'] ?></span>
                        </td>
                        <td>
                            <?php if($Movement['order_id']>0 && ($Movement['type_id']=="1" || $Movement['type_id']=="5")){ ?>
                            <a href="../customer_national_order/print.php?id=<?php echo $Movement['order_id']; ?>" target="_blank" ><button aria-label="Ver Orden" class="btn btn-github hint--bottom hint--bounce"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <!--
                    <tr>
                        <td>23/06/2017</td>
                        <td>Compra de Mercadería</td>
                        <td>Orden de Compra NºX</td>
                        <td>
                            <span class="label label-warning">$1230</span>
                        </td>
                        <td><button aria-label="Ver Orden" class="btn btn-github hint--bottom hint--bounce"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
                    </tr>
                    <tr>
                        <td>22/06/2017</td>
                        <td>Pago de Orden de Compra</td>
                        <td>Pago en Cheque<br>(127818712873)<br>Banco Santander Rio</td>
                        <td>
                            <span class="label label-info">$500</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>22/06/2017</td>
                        <td>Compra de Mercadería</td>
                        <td>Compra en Local</td>
                        <td>
                            <span class="label label-danger">$1500</span>
                        </td>
                        <td><button aria-label="Ver Orden" class="btn btn-github hint--bottom hint--bounce"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
                    </tr>
                    <tr>
                        <td>21/06/2017</td>
                        <td>Pago de Orden de Compra</td>
                        <td>Pago en Efectivo</td>
                        <td>
                            <span class="label label-success">$500</span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>20/06/2017</td>
                        <td>Compra de Mercadería</td>
                        <td>Orden de Compra NºX</td>
                        <td>
                            <span class="label label-danger">$560</span>
                        </td>
                        <td>
                            <button aria-label="Ver Orden" class="btn btn-github hint--bottom hint--bounce"><i class="fa fa-eye" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    -->
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer txC">
            <button class="btn btn-dropbox"><b>Cargar más</b></button>
        </div><!-- box-footer -->
    </div>
<?php
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
include('../../includes/inc.bottom.php');
?>