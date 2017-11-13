<?php
    include("../../includes/inc.main.php");
    $ID = $_GET['id'];
    $Edit = new Product($ID);
    $Data = $Edit->Data;
    
    $Head->setTitle($Data['title']);
    $Head->setSubTitle($Menu->GetTitle());
    $Head->setStyle('../../../vendors/bootstrap-switch/bootstrap-switch.css'); // Switch On Off
    $Head->setStyle('../../../vendors/select2/select2.min.css'); // Select Inputs With Tags
    $Head->setHead();
    
    $Variation = $DB->fetchAssoc('product_variation','variation_id,title');
    
    $AmountClass = $PercentClass = 'Hidden';
    
    if($Data['variation_id']==1) $PercentClass = '';
    if($Data['variation_id']==2) $AmountClass = '';
    
    $Category = new Category();
    include('../../includes/inc.top.php');
    
    // HIDDEN ELEMENTS
    echo insertElement("hidden","id",$ID);
    echo insertElement("hidden","action",'update');
    echo insertElement("hidden","category",$Data['category_id']);

?>
  <!-- ///////// FIRST SCREEN ////////// -->
  <div class="CategoryMain box Hidden">
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
                $CategoryName = '';
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
                    echo '<li class="'.$Class.'" level="'.$Level.'" category="'.$Parent.'"><select class="category_selector" name="category_'.$Parent.'" category="'.$Data['category_id'].'" id="category_'.$Parent.'" size="20">';
                  }
                  if($Cat['category_id']==$Data['category_id'])
                  {
                    $Selected = 'selected="selected"';
                    $CategoryName = $Cat['title'];
                  }else{
                    $Selected = '';  
                  }
                  
                  echo '<option value="'.$Cat['category_id'].'" '.$Selected.'>'.$Cat['title'].'</option>';
                }
                echo '</select></li>';
              ?>
              <li id="CountinueBtn" class="">
                <span>
                  <i class="fa fa-check"></i>
                  <button type="button" class="SelectCategory btn btnBlue categorySelectBtn">Continuar</button>
                </span>
              </li>
            </ul>
            <?php echo insertElement('hidden','maxlevel',$MaxLevel); ?>
          </div>
          <!-- / Item -->
        </div>
        <!-- Categories -->
      </div><!-- Firs Screen Row -->
    </div><!-- /.box-body -->
  </div><!-- /.box -->
  <!-- ///////// END FIRST SCREEN ////////// -->


  <!-- ////////// SECOND SCREEN ////////////////// -->
  <div class="ProductDetails box animated fadeIn">
    <div class="box-header flex-justify-center">
      <div class="col-md-6 ">
        <div class="innerContainer">
          <h4 class="subTitleB"><i class="fa fa-cube"></i> Detalles del Art&iacute;culo</h4>
          
            <div class="form-group">
              Categoría: <b><span id="category_selected"></span></b> <button type="button" class="BackToCategory btn btn-warning"><i class="fa fa-pencil"></i></button>
            </div>
            <!--<div class="form-group">-->
            <!--  <?php //echo insertElement('text','title','','form-control','placeholder="Nombre del Art&iacute;culo"') ?>-->
            <!--</div>-->
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-7">
                <label for="code">Nombre:</label>
                <?php echo insertElement('text','title',$Data['title'],'form-control','placeholder="Nombre" validateEmpty="Ingrese un Nombre."') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <label for="rack">Estanter&iacute;a:</label>
                <?php echo insertElement('text','rack',$Data['rack'],'form-control','placeholder="Estanter&iacute;a"') ?>
              </div>
            </div>
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-2">
                <label for="cost">Costo:</label>
                <?php echo insertElement('text','cost',$Data['cost'],'form-control priceInput','placeholder="Costo" validateEmpty="Ingrese un costo."  data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'prefix\': \'$\', \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <label for="variation">Variaci&oacute;n:</label>
                <?php echo insertElement('select','variation',$Data['variation_id'],'form-control','validateEmpty="Seleccione una variaci&oacute;n."',$Variation) ?>
              </div>
              <div class="col-xs-12 col-sm-5">
                <label for="variation">Medida:</label>
                <?php echo insertElement('select','size',$Data['size_id'],'form-control','validateEmpty="Seleccione una medida."',$DB->fetchAssoc("product_size","size_id,title")); ?>
              </div>
            </div>
            
      
            <div id="PricePercentage" class="row form-group inline-form-custom <?php echo $PercentClass; ?>">
              <div class="col-xs-12 col-sm-4 txR">
                Porcentaje Minorista:
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','percentage_retailer',$Data['additional_percentage_retailer'],'form-control priceInput ProductVariation PercentageField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-4 txR">
                Porcentaje Mayorista:
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','percentage_wholesaler',$Data['additional_percentage_wholesaler'],'form-control priceInput ProductVariation PercentageField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
            </div>
            
            <div id="PriceAmount" class="row form-group inline-form-custom <?php echo $AmountClass; ?>">
              <div class="col-xs-12 col-sm-4 txR">
                Adicional Minorista:
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','amount_retailer',$Data['additional_price_retailer'],'form-control priceInput ProductVariation AmountField' ,'placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
              <div class="col-xs-12 col-sm-4 txR">
                Adicional Mayorista:
              </div>
              <div class="col-xs-12 col-sm-2">
                <?php echo insertElement('text','amount_wholesaler',$Data['additional_price_wholesaler'],'form-control priceInput ProductVariation AmountField','placeholder="Sin Especificar" data-inputmask="\'alias\': \'numeric\', \'groupSeparator\': \'\', \'autoGroup\': true, \'digits\': 2, \'digitsOptional\': false, \'placeholder\': \'0\'"') ?>
              </div>
            </div>
            
            
            
            <div class="row form-group inline-form-custom">
              <!--<div class="col-xs-12 col-sm-4">-->
              <!--  <?php //echo insertElement('text','rack','','form-control','placeholder="Estanter&iacute;a"') ?>-->
              <!--</div>-->
              <div class="col-xs-12 col-sm-12">
                <label for="brand_select">Marca:</label>
                <?php echo insertElement('select','brand',$Data['brand_id'],'form-control selectTags','validateEmpty="Ingrese una marca." style="width:100%;height:auto!important;"',$DB->fetchAssoc("product_brand","brand_id,name","status='A' AND company_id=".$_SESSION['company_id']),'','Seleccionar Marca') ?>
              </div>
            </div>
            <!--<div class="form-group">-->
            <!--  <label for="size">Medidas:</label>-->
            <!--  <?php //echo insertElement('text','size',$Data['size'],'form-control','placeholder="Medidas"') ?>-->
            <!--</div>-->
            <div class="row form-group inline-form-custom">
              <div class="col-xs-12 col-sm-4">
                <label for="stock">Stock Actual:</label>
                <?php echo insertElement('text','stock',$Data['stock'],'form-control','placeholder="Stock Incial"') ?>
              </div>
              <div class="col-xs-12 col-sm-4">
                <label for="stock_min">Stock M&iacute;nimo:</label>
                <?php echo insertElement('text','stock_min',$Data['stock_min'],'form-control','placeholder="Stock M&iacute;nimo"') ?>
              </div>
              <div class="col-xs-12 col-sm-4">
                <label for="stock_max">Stock M&aacute;ximo:</label>
                <?php echo insertElement('text','stock_max',$Data['stock_max'],'form-control','placeholder="Stock M&aacute;ximo"') ?>
              </div>
            </div>
            
            <!-- Description (Character Counter)-->
            <label for="description">Descripci&oacute;n:</label>
            <div class="form-group textWithCounter">
              <?php echo insertElement('textarea','description',$Data['description'],'text-center','placeholder="Descripción" rows="4" maxlength="150"'); ?>
              <!--<textarea id="description" name="description" class="text-center" placeholder="Descripción" rows="4" maxlength="150"></textarea>-->
              <div class="indicator-wrapper">
                <p>Caracteres restantes</p>
                <div class="indicator"><span class="current-length">150</span></div>
              </div>
            </div>
            <div class="txC">
              <button type="button" class="btn btn-success btnGreen" id="BtnCreate"><i class="fa fa-check"></i> Finalizar Edici&oacute;n</button>
              <button type="button" class="btn btn-error btnRed" id="BtnCancel" name="BtnCancel"><i class="fa fa-times"></i> Cancelar</button>
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
$Foot->setScript('../../../vendors/select2/select2.min.js');
include('../../includes/inc.bottom.php');
?>