<?php
    include("../../includes/inc.main.php");
    $ID = $_GET['id'];
    $Customer = new Customer($ID);
    $Data = $Customer->Data;
    
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setTitle($Menu->GetTitle());
    $Head->setHead();
    
    $ProductsQuery = $DB->fetchAssoc('product','product_id,title',"status='A'",'title');
    
    // $PriceConfiguration = $DB->fetchAssoc("product_configuration","*","status='A'","creation_date");
    include('../../includes/inc.top.php');
    echo insertElement('hidden','total',0)
?>
<div class="box box-primary animated fadeIn">
    <div class="box-header flex-justify-center no-padding">
        <h4>Administrar Precios de <?php echo $Data['name'] ?></h4>
    </div>
    <div class="box-body no-padding">
        <hr class="no-padding">
        <div class="row">
            <div class="col-xs-12 col-sm-4 text-right">
                Art&iacute;culo:
            </div>
            <div class="col-xs-12 col-sm-4">
                <?php echo insertElement('select','product','','form-control chosenSelect','data-placeholder="Seleccionar Art&iacute;culo"',$ProductsQuery,' ',''); ?>
            </div>
            <div class="col-xs-12 col-sm-2">
                <button class="btn btn-primary" id="asoc"><strong><i class="fa fa-exchange"></i></strong></button>
            </div>
        </div>
        <div id="relations" class="Hidden">
            <hr>
            <div class="row txC">
                <div class="col-xs-12 col-sm-3 col-md-2 col-md-offset-3">
                    <strong>Producto</strong>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-2">
                    <strong>Adicional</strong>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-2">
                    <strong>Valor</strong>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-1">
                </div>
            </div>
            <!--<div class="row txC" row="0">-->
            <!--    <div class="col-xs-12 col-sm-3 col-md-2 col-md-offset-3">-->
            <!--        Atun re loco-->
            <!--        <input type="hidden" name="id0" id="id0" value="" />-->
            <!--    </div>-->
            <!--    <div class="col-xs-12 col-sm-3 col-md-2">-->
            <!--        <select name="variation0" id="variation0" class="form-control">-->
            <!--            <option value="1">Fijo</option>-->
            <!--            <option value="2">Porcentual</option>-->
            <!--        </select>-->
            <!--    </div>-->
            <!--    <div class="col-xs-12 col-sm-3 col-md-2">-->
            <!--        <input type="text" name="value0" id="value0" class="form-control" placeholder="Valor" validateOnlyNumbers="Solo se pueden ingresar n&uacute;meros." />-->
            <!--    </div>-->
            <!--    <div class="col-xs-12 col-sm-3 col-md-1">-->
            <!--        <span class="btn btn-danger" aria-label="Borrar" row="0" class="hint--bottom hint--bounce hint--error"><i class="fa fa-trash"></i></span>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
        <br>
    </div>
    <div class="box-footer flex-justify-center">
        <button type="button" class="btn btn-success btnGreen" id="BtnEdit"><i class="fa fa-check"></i> Guardar Cambios</button>
        <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
    </div>
</div><!-- box -->
  
  
<?php
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
include('../../includes/inc.bottom.php');
?>