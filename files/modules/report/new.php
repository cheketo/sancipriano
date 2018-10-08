<?php
    include("../../includes/inc.main.php");
    $Head->setTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Select Inputs With Tags
    $Head->setStyle('../../../vendors/datepicker/datepicker3.css'); // Date Picker Calendar
    $Head->setHead();
    
    $PriceConfiguration = $DB->fetchAssoc("product_configuration","*","status='A'","creation_date");
    include('../../includes/inc.top.php');
    
?>
  <div class="box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-md-8 col-sm-12">
        
          <div class="innerContainer main_form">
            <form id="new_report">
            <h4 class="subTitleB"><i class="fa fa-history"></i> Int&eacute;rvalo del An&aacute;lisis</h4>
            <?php echo insertElement("hidden","action",'report'); ?>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-6">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <?php echo insertElement('text','from','','form-control calendar_date',' placeholder="Desde el primer d&iacute;a"'); ?>
                </span>
              </div>
              <div class="col-xs-12 col-sm-6">
                <span class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <?php echo insertElement('text','to','','form-control calendar_date',' placeholder="Hasta hoy"'); ?>
                </span>
              </div>
            </div>
            </form>
            <hr>
            <div class="row txC">
              <button type="button" class="btn btn-success btnGreen" id="BtnReport"><i class="fa fa-universal-access"></i> Generar An&aacute;lisis</button>
              <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
            </div>
          </div>
      
          
          
        </div>
      </div>
    </div><!-- box -->
  </div><!-- box -->
  
<?php
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js');
$Foot->setScript('../../../vendors/datepicker/bootstrap-datepicker.js');

include('../../includes/inc.bottom.php');
?>