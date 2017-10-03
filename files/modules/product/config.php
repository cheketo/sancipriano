<?php
    include("../../includes/inc.main.php");
    $Head->setTitle("Configuraci&oacute;n de Precios");
    $Head->setHead();
    
    
    $Config	= $DB->fetchAssoc('product_configuration','*',"status='A' AND company_id=".$_SESSION['company_id'],'creation_date DESC');
    $Config = $Config[0];
    include('../../includes/inc.top.php');
    
    // HIDDEN ELEMENTS
    echo insertElement("hidden","action",'config');
?>
  
  <div class="ProductDetails box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-md-6 ">
        <div class="innerContainer">
            
            <h4 class="subTitleB"><i class="fa fa-building"></i> Mayoristas</h4>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-5">
                Porcentaje Adicional:
              </div>
              <div class="col-xs-12 col-sm-6">
                <?php echo insertElement('text','wpercent',$Config['additional_percentage_wholesaler'],'form-control txC','placeholder="Porcentaje adicional" validateEmpty="Ingrese un procentaje."') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                Monto Adicional:
              </div>
              <div class="col-xs-12 col-sm-6">
                <?php echo insertElement('text','wadditional',$Config['additional_price_wholesaler'],'form-control txC','placeholder="Monto adicional" validateEmpty="Ingrese un monto."') ?>
              </div>
            </div>
            
            <h4 class="subTitleB"><i class="fa fa-home"></i> Minoristas</h4>
            <div class="row form-group inline-form-custom">
                <div class="col-xs-12 col-sm-5">
                    Porcentaje Adicional:
                </div>
              <div class="col-xs-12 col-sm-6">
                <?php echo insertElement('text','rpercent',$Config['additional_percentage_retailer'],'form-control txC','placeholder="Porcentaje adicional" validateEmpty="Ingrese un procentaje."') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                Monto Adicional:
              </div>
              <div class="col-xs-12 col-sm-6">
                <?php echo insertElement('text','radditional',$Config['additional_price_retailer'],'form-control txC','placeholder="Monto adicional" validateEmpty="Ingrese un monto."') ?>
              </div>
            </div>
            
            
            
            
            <div class="txC">
              <button type="button" class="btn btn-success btnGreen" id="BtnConfig"><i class="fa fa-check"></i> Guardar Configuraci&oacute;n</button>
            </div>
        </div>
        <!-- Description (Character Counter) -->
      </div>
    </div><!-- box -->
  </div><!-- box -->

  <!-- //////////////// END SECOND SCREEN /////////////// -->

  <!-- Help Modal -->
<?php
$Foot->setScript('../../../vendors/jquery-mask/src/jquery.mask.js');
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
include('../../includes/inc.bottom.php');
?>