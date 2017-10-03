<?php
    include("../../includes/inc.main.php");
    $ID = $_GET['id'];
    $View = new Customer($ID);
    $Movements = Movement::GetMovementsByCustomerID($ID);
    
    $Head->setTitle($Menu->GetTitle());
    $Head->setHead();
    include('../../includes/inc.top.php');
?>
  
    <div class="box box-success animated fadeIn">
        <div class="box-header with-border">
            <!--<h3 class="box-title">Default Box Example</h3>-->
            <h3 class=" txC">Saldo</h4>
            <!--<div class="box-tools pull-right">-->
            <!--    <i class="fa fa-id-card" aria-hidden="true"></i>-->
            <!--</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6 col-xs-12 txC">
                    <h4>Saldo sin confirmar:<span class="label label-warning">$2290</span></h4>
                </div>
                <div class="col-sm-6 col-xs-12 txC">
                    <h4>Saldo confirmado:<span class="label label-danger">$1560</span></h4>
                </div>
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
            <!--The footer of the box-->
            <!--<div class="box-tools pull-right">-->
            <!--    <i class="fa fa-vcard-o" aria-hidden="true"></i>-->
            <!--</div><!-- /.box-tools -->
        </div><!-- box-footer -->
    </div><!-- /.box -->
 
    <div class="box box-info animated fadeIn">
        <div class="box-header">
            <h4 class=" txC">Cuenta Corriente</h4>
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
                        <th class="txC">Acciones</th>
                    </tr>
                    <?php foreach($Movements as $Movement){ ?>
                    <tr>
                        <td><?php echo DBDate($Movement['creation_date']) ?></td>
                        <td><?php echo $Movement['type'] ?></td>
                        <td><?php echo $Movement['concept'] ?></td>
                        <td>
                            <span class="label label-warning">$<?php echo $Movement['amount'] ?></span>
                        </td>
                        <td>
                            <?php if($Movement['order_id']>0){ ?>
                            <a href="../customer_national_order/view.php?id=<?php echo $Movement['order_id']; ?>"><button aria-label="Ver Orden" class="btn btn-github hint--bottom hint--bounce"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
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