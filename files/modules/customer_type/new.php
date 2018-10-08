<?php
    include("../../includes/inc.main.php");
    $New = new CustomerType();
    $Head->setTitle($Menu->GetTitle());
    $Head->setHead();
    include('../../includes/inc.top.php');
?>
  <div class="box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-md-8 col-sm-12">
        
          <div class="innerContainer main_form">
            <form id="customer_type_form">
            <h4 class="subTitleB"><i class="fa fa-newspaper-o"></i> Datos del Tipo de Cliente</h4>
            <?php echo insertElement("hidden","action",'insert'); ?>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-users"></i></span>
                  <?php echo insertElement('text','name','','form-control',' placeholder="Nombre" validateEmpty="Ingrese un nombre." validateFromFile="../../library/processes/proc.common.php///El nombre ya existe///action:=validate///object:=CustomerType" autofocus'); ?>
                </span>
              </div>
            </div>
            
            <div id="additonal_configuration" class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-6">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                  <?php echo insertElement('text','percentage','','form-control',' placeholder="Aumento Porcentaje" validateOnlyNumbers="Ingrese n&uacute;meros &uacute;nicamente." validateEmpty="Ingrese un n&uacute;mero."'); ?>
                </span>
              </div>
              <div class="col-xs-12 col-sm-6">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                  <?php echo insertElement('text','amount','','form-control',' placeholder="Aumento Valor Fijo" validateEmpty="Ingrese un n&uacute;mero." validateOnlyNumbers="Ingrese n&uacute;meros &uacute;nicamente."'); ?>
                </span>
              </div>
            </div>
          </form>
          <hr>
          <div class="row txC">
            <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-plus"></i> Crear Tipo de Cliente</button>
            <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
          </div>
        </div>
        </div>
      </div>
    </div><!-- box -->
  </div><!-- box -->
<?php include('../../includes/inc.bottom.php'); ?>