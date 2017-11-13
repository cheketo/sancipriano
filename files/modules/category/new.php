<?php
    include("../../includes/inc.main.php");
    $Head->setTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/select2/select2.min.css'); // Select Inputs With Tags
    $Head->setHead();
    include('../../includes/inc.top.php');
    $New = new Category();
?>
  <?php echo insertElement("hidden","action",'insert'); ?>
  <?php echo insertElement("hidden","parent"); ?>
  <div class="box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-lg-12 col-sm-12">
        <div class="innerContainer">
          <h4 class="subTitleB"><i class="fa fa-plus-circle"></i> Complete los campos para agregar una nueva Categoría</h4>
            <div class="row form-group inline-form-custom-2">
              <div class="col-xs-12 inner">
                <label>Nombre</label>
                <?php echo insertElement('text','title','','form-control','placeholder="Ingrese un Nombre" validateEmpty="Ingrese un nombre." validateFromFile="../../library/processes/proc.common.php///El nombre ya existe///action:=validate///object:=Category" autofocus'); ?>
              </div>
            </div><!-- inline-form -->
            <div class="row form-group inline-form-custom-2">
              <div class="col-xs-12 col-sm-6 inner">
                <label>Nombre Corto</label>
                <?php echo insertElement('text','short_title','','form-control','placeholder="Ingrese un Nombre Corto" validateEmpty="Ingrese un nombre."'); ?>
              </div>
              <div class="col-xs-12 col-sm-6 inner">
                <label>Ubicaci&oacute;n</label>
                <?php echo insertElement('select','parent_select','','form-control select2 selectTags',' style="width: 100%;height:auto!important;"',$DB->fetchAssoc("product_category","category_id,title","status='A' AND company_id=".$_SESSION['company_id']),'0','L&iacute;nea Principal'); ?>
              </div>
            </div><!-- inline-form -->
            <hr>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-2 txR">
                Porcentaje Minorista:
              </div>
              <div class="col-xs-12 col-sm-1">
                <?php echo insertElement('text','percentage_retailer','','form-control priceInput ProductVariation PercentageField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-2 txR">
                Porcentaje Mayorista:
              </div>
              <div class="col-xs-12 col-sm-1">
                <?php echo insertElement('text','percentage_wholesaler','','form-control priceInput ProductVariation PercentageField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              
              <div class="col-xs-12 col-sm-2 txR">
                Adicional Minorista:
              </div>
              <div class="col-xs-12 col-sm-1">
                <?php echo insertElement('text','amount_retailer','','form-control priceInput ProductVariation AmountField' ,'placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-2 txR">
                Adicional Mayorista:
              </div>
              <div class="col-xs-12 col-sm-1">
                <?php echo insertElement('text','amount_wholesaler','','form-control priceInput ProductVariation AmountField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
            </div>
            <hr>
            <div class="txC">
              <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-plus"></i> Crear Categoría</button>
              <button type="button" class="btn btn-success btnBlue" id="BtnCreateNext"><i class="fa fa-plus"></i> Crear y Agregar Otra</button>
              <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
            </div>
        </div>
      </div>
    </div>
  </div>
<?php
  $Foot->setScript('../../../vendors/select2/select2.min.js');
  include('../../includes/inc.bottom.php');
?>
