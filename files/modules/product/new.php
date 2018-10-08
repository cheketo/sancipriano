<?php
    include("../../includes/inc.main.php");
    $Head->setTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/bootstrap-switch/bootstrap-switch.css'); // Switch On Off
    $Head->setStyle('../../../vendors/chosen-js/bootstrap-chosen.css'); // Bootstrap Select Input
    $Head->setHead();
    
    $Category = new Category();
    include('../../includes/inc.top.php');
    
    $Variation = $DB->fetchAssoc('product_variation','variation_id,title');
    $CustomerTypes = $DB->fetchAssoc("customer_type","*","status='A'","type_id");
    
    // HIDDEN ELEMENTS
    echo insertElement("hidden","action",'insert');
    echo insertElement("hidden","category");

?>
  <!-- ///////// FIRST SCREEN ////////// -->
  <div class="CategoryMain box">
    <!--box-success-->
    <!-- <div class="box-header with-border">
      <h3 class="box-title">Complete el formulario</h3>
    </div>
    <! .box-header -->
    <div class="box-body categoryBoxBody">
      <div class="row">
        <!-- First Screen Row -->
        <!-- Categories -->
        <div class="container productCategory2 animated fadeIn">
          <!-- Item -->
          <div class="categoryList">
            <div class="categoryTitle"><span><b>Categorías</b> | Seleccione una Categoría</span></div>
            <ul>
              <?php 
                $Categories = $Category->GetAllCategories();
                
                foreach($Categories as $Cat)
                {
                  if($Parent!=$Cat['parent_id'])
                  {
                    $Level = $Category->CalculateCategoryLevel($Cat['category_id']);
                    if($Level>$MaxLevel) $MaxLevel = $Level;
                    $Parent = $Cat['parent_id'];
                    if($Parent!=0)
                    {
                      $Class = 'Hidden';
                      echo '</select></li>';
                    }
                    echo '<li class="'.$Class.'" level="'.$Level.'" category="'.$Parent.'"><select class="category_selector" name="category_'.$Parent.'" id="category_'.$Parent.'" size="20">';
                  }
                  echo '<option value="'.$Cat['category_id'].'">'.$Cat['title'].'</option>';
                }
                echo '</select></li>';
              ?>
              <li id="CountinueBtn" class="Hidden">
                <span>
                  <i class="fa fa-check"></i>
                  <button type="button" class="SelectCategory btn btnBlue categorySelectBtn">Continuar</button>
                </span>
              </li>
            </ul>
            <?php echo insertElement('hidden','maxlevel',$MaxLevel); ?>
          <div class="txC">
            <button type="button" class="btn btn-error btnRed" id="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
          </div>
          </div>
          <!-- / Item -->
        </div>
        <!-- Categories -->
      </div><!-- Firs Screen Row -->
    </div><!-- /.box-body -->
  </div><!-- /.box -->
  <!-- ///////// END FIRST SCREEN ////////// -->


  <!-- ////////// SECOND SCREEN ////////////////// -->
  <div class="ProductDetails box animated fadeIn Hidden">
    <div class="box-header flex-justify-center">
      <div class="col-xs-12">
        <div class="innerContainer">
          <h4 class="subTitleB"><i class="fa fa-cube"></i> Detalles del Art&iacute;culo</h4>
          
            <div class="form-group">
              Categoría: <b><span id="category_selected"></span></b>
            </div>
            <!--<div class="form-group">-->
            <!--  <?php //echo insertElement('text','title','','form-control','placeholder="Nombre del Art&iacute;culo"') ?>-->
            <!--</div>-->
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-7">
                <?php //echo insertElement('text','short_title','','form-control','placeholder="Nombre Corto"') ?>
                <?php echo insertElement('text','title','','form-control','placeholder="Nombre" validateEmpty="Ingrese un nombre."') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <?php echo insertElement('text','rack','','form-control','placeholder="Estanter&iacute;a"') ?>
              </div>
            </div>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','cost','','form-control priceInput','placeholder="Costo" validateEmpty="Ingrese un costo." data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'prefix\': \'$\', \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <?php echo insertElement('select','variation','','form-control','validateEmpty="Seleccione una variaci&oacute;n."',$Variation,'','Seleccionar Variaci&oacute;n') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <?php echo insertElement('select','size','','form-control','data-placeholder="Seleccionar Medida" validateEmpty="Seleccione una medida."',$DB->fetchAssoc("product_size","size_id,title"),'',' ') ?>
              </div>
            </div>
            
            <div id="PricePercentage" class="row form-group inline-form-custom Hidden">
              <?php
                $I=0;
                foreach($CustomerTypes as $CustomerType)
                {
                    $I++;
              ?>
              <div class="col-xs-12 col-sm-3 txR">
                Porcentaje <?php echo $CustomerType['name'];?>:
                <?php echo insertElement("hidden","type".$I,$CustomerType['type_id']);?>
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','additional_percentage'.$I,'','form-control priceInput ProductVariation PercentageField','placeholder="Heredar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"'); ?>
              </div>
              <?php }  ?>
              <?php echo insertElement("hidden","total_types",$I);?>
            </div>
            
            <div id="PriceAmount" class="row form-group inline-form-custom Hidden">
              <?php 
                  $I=0;
                  foreach($CustomerTypes as $CustomerType)
                  {  
                    $I++;
              ?>
              <div class="col-xs-12 col-sm-3 txR">
                Adicional <?php echo $CustomerType['name'];?>:
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','additional_amount'.$I,'','form-control priceInput ProductVariation AmountField' ,'placeholder="Heredar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              <?php }  ?>
            </div>
            
            <div class="row form-group inline-form-custom">
              <!--<div class="col-xs-12 col-sm-4">-->
              <!--  <?php //echo insertElement('text','rack','','form-control','placeholder="Estanter&iacute;a"') ?>-->
              <!--</div>-->
              <div class="col-xs-12 col-sm-12">
                <?php echo insertElement('select','brand','','form-control  selectTags','validateEmpty="Ingrese una marca." style="width:100%;height:auto!important;"',$DB->fetchAssoc("product_brand","brand_id,name","status='A' AND company_id=".$_SESSION['company_id']),'','Seleccionar Marca') ?>
              </div>
            </div>
            <!--<div class="form-group">-->
            <!--  <?php echo insertElement('text','size','','form-control','placeholder="Medidas"') ?>-->
            <!--</div>-->
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-4">
                <?php echo insertElement('text','stock','','form-control','placeholder="Stock Incial"') ?>
              </div>
              <div class="col-xs-12 col-sm-4">
                <?php echo insertElement('text','stock_min','','form-control','placeholder="Stock M&iacute;nimo"') ?>
              </div>
              <div class="col-xs-12 col-sm-4">
                <?php echo insertElement('text','stock_max','','form-control','placeholder="Stock M&aacute;ximo"') ?>
              </div>
            </div>
            <!--<div class="form-group">-->
            <!--  <?php echo insertElement('button','dispatch_data','Agregar datos de &uacute;ltima importaci&oacute;n','btn btn-warning','style="width:100%;"') ?>-->
            <!--</div>-->
            <!--<div class="row form-group inline-form-custom Hidden Dispatch animated fadeIn">-->
            <!--  <div class="col-md-12">-->
            <!--    <?php echo insertElement('text','dispatch','','form-control','placeholder="Desp. Aduana"') ?>-->
            <!--  </div>-->
            <!--</div>-->
            <!--<div class="row form-group inline-form-custom Hidden Dispatch animated fadeIn">-->
            <!--  <div class="col-xs-12 col-sm-6">-->
            <!--    <?php echo insertElement('text','price_fob','','form-control','placeholder="Costo Fob"') ?>-->
            <!--  </div>-->
            <!--  <div class="col-xs-12 col-sm-6">-->
            <!--    <?php echo insertElement('text','price_dispatch','','form-control','placeholder="Costo Desp."') ?>-->
            <!--  </div>-->
            <!--</div>-->
            <!-- Description (Character Counter)-->
            <div class="form-group textWithCounter">
              <textarea id="description" name="description" class="text-center" placeholder="Descripción" rows="4" maxlength="150"></textarea>
              <div class="indicator-wrapper">
                <p>Caracteres restantes</p>
                <div class="indicator"><span class="current-length">150</span></div>
              </div>
            </div>
            <div class="txC">
              <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check"></i> Finalizar</button>
              <button type="button" class="btn btn-success btnBlue" id="BtnCreateNext"><i class="fa fa-plus"></i> Finalizar y Crear Otro</button>
              <button type="button" class="BackToCategory btn btnRed">Regresar</button>
            </div>
        </div>
        <!-- Description (Character Counter) -->
      </div>
    </div><!-- box -->
  </div><!-- box -->

  <!-- //////////////// END SECOND SCREEN /////////////// -->

  <!-- Help Modal -->
<?php
$Foot->setScript('../../../vendors/bootstrap-switch/script.bootstrap-switch.min.js');
$Foot->setScript('../../../vendors/jquery-mask/src/jquery.mask.js');
$Foot->setScript('../../../vendors/inputmask3/jquery.inputmask.bundle.min.js');
$Foot->setScript('../../../vendors/chosen-js/chosen.jquery.js'); // Bootstrap Select Input
include('../../includes/inc.bottom.php');
?>