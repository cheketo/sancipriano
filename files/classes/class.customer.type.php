<?php

class CustomerType extends DataBase
{
	var	$ID;
	var $Data;
	var $Products		= array();
	var $ImgGalDir		= '../../../skin/images/customer_types/';
	var $Table			= "customer_type";
	var $TableID		= "type_id";

	const DEFAULTIMG	= "../../../skin/images/customer_types/default/default.png";

	public function __construct($ID=0)
	{
		$this->Connect();
		$this->ID = $ID;
		if($ID!=0)
		{
			$Data = $this->fetchAssoc($this->Table,"*",$this->TableID."=".$ID);
			$this->Data = $Data[0];
			// $this->Data['products'] = $this->GetProducts();
		}
	}

	// public function GetProducts()
	// {
	// 	if(empty($this->Products))
	// 	{
	// 		$this->Products = $this->fetchAssoc(
	// 			"relation_product_customer_type a
	// 			JOIN product b ON (b.product_id=a.product_id)
	// 			JOIN product_brand b ON (c.brand_id=b.brand_id)
	// 			",//,b.amount,b.percentage
	// 			"a.*,b.title as product,b.variation,b.cost,c.name as brand",
	// 			$this->TableID."=".$this->ID);
	// 	}
	// 	return $this->Products;
	// }

	public function GetDefaultImg()
	{
		return self::DEFAULTIMG;
	}

	public function GetImg()
	{
		if(!$this->Data['logo'] || !file_exists($this->Data['logo']))
			return $this->GetDefaultImg();
		else
			return $this->Data['logo'];
	}

	public function ImgGalDir()
	{
		$Dir = $this->ImgGalDir.'/'.$this->ID;
		if(!file_exists($Dir))
			mkdir($Dir);
		return $Dir;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// SEARCHLIST FUNCTIONS ///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function MakeRegs($Mode="List")
	{
		$Rows	= $this->GetRegs();
		// echo $this->lastQuery();
		for($i=0;$i<count($Rows);$i++)
		{
			$Row	=	new CustomerType($Rows[$i][$this->TableID]);
			//var_dump($Row);
			// $UserGroups = $Row->GetGroups();
			// $Groups='';
			// foreach($UserGroups as $Group)
			// {
			// 	$Groups .= '<span class="label label-warning">'.$Group['title'].'</span> ';
			// }
			// if(!$Groups) $Groups = 'Ninguno';
			$Actions	= 	'<span class="roundItemActionsGroup"><a><button type="button" class="btn bg-navy ExpandButton" id="expand_'.$Row->ID.'"><i class="fa fa-plus"></i></button></a> ';
			// $Actions	.= 	'<a aria-label="Cuenta Corriente" class="hint--bottom hint--bounce hint--success" href="view.php?id='.$Row->ID.'"><button type="button" class="btn btn-success"><i class="fa fa-dollar"></i></button></a>';
			// if($Row->Data['type_id']==4)
			// {
				// $Actions	.= 	' <a aria-label="Administrar Precios" class="hint--bottom hint--bounce hint--info" href="products.php?id='.$Row->ID.'"><button type="button" class="btn btn-info"><i class="fa fa-exchange"></i></button></a>';
			// }
			$Actions	.= 	'<a aria-label="Editar" class="hint--bottom hint--bounce hint--info" href="edit.php?id='.$Row->ID.'"><button type="button" class="btn btnBlue"><i class="fa fa-pencil"></i></button></a>';
			if($Row->Data['status']=="A")
			{
				$Actions	.= '<a aria-label="Eliminar" class="hint--bottom hint--bounce hint--error deleteElement" process="../../library/processes/proc.common.php" id="delete_'.$Row->ID.'"><button type="button" class="btn btnRed"><i class="fa fa-trash"></i></button></a>';
			}else{
				$Actions	.= '<a aria-label="Activar" class="hint--bottom hint--bounce hint--success activateElement" process="../../library/processes/proc.common.php" id="activate_'.$Row->ID.'"><button type="button" class="btn btnGreen"><i class="fa fa-check-circle"></i></button></a>';
			}
			$Actions	.= '</span>';
			// echo '<pre>';
			// print_r($Row->Data['branches']);
			// echo '</pre>';

			$Products = '';
			$I=0;
			// foreach($Row->Data['products'] as $Product)
			// {
			// 	$I++;
			// 	$RowClass = $I % 2 != 0? 'bg-gray':'';

				// $BranchAddress = $Branch['address'].', '.$Branch['zone_short'].', '.$Branch['region_short'].', '.$Branch['province_short'].', '.$Branch['country'];
				// $BranchDataFields = '';
				// $BranchDataFields .= $Branch['phone']? '<span class="smallDetails"><i class="fa fa-phone"></i> '.$Branch['phone'].'</span>':'';
				// $BranchDataFields .= $Branch['email']? '<span class="smallDetails"><i class="fa fa-envelope"></i> '.$Branch['email'].'</span>':'';
				// $BranchDataFields .= $Branch['fax']? '<span class="smallDetails"><i class="fa fa-fax"></i> '.$Branch['fax'].'</span>':'';
				// $BranchDataFields .= $Branch['website']? '<span class="smallDetails"><i class="fa fa-desktop"></i> '.$Branch['website'].'</span>':'';
				// $BranchData = $BranchDataFields? '<div class="col-md-4 col-md-6 col-sm-12"><div class="listRowInner"><span class="listTextStrong">Datos de Contacto</span>'.$BranchDataFields.'</div></div>':'';

			// 	$Products .= '
			// 				<div class="row '.$RowClass.'">
			// 					<div class="col-md-4 col-md-6 col-sm-12">
			// 						<div class="listRowInner">
			// 							<span class="listTextStrong">'.$Branch['product'].'</span>
			// 							<span class="smallDetails">'.$Branch['brand'].'</span>
			// 						</div>
			// 					</div>
			// 					'.$ProductData.'

			// 				</div>';
			// }
			
			// $BalanceClass = $Row->Data['balance']>=0?'success':'danger';
			// $Row->Data['balance'] = $Row->Data['balance']? $Row->Data['balance']: '0.00';
			
			switch(strtolower($Mode))
			{
				case "list":
					$RowBackground = $i % 2 == 0? '':' listRow2 ';
					// $Phone = $Row->Data['branches'][0]['phone']? '<span class="smallDetails"><i class="fa fa-phone"></i> '.$Row->Data['branches'][0]['phone'].'</span>':'';

					$Regs	.= '<div class="row listRow'.$RowBackground.$Restrict.'" id="row_'.$Row->ID.'" title="'.$Row->Data['name'].'">
									<div class="col-lg-3 col-md-5 col-sm-8 col-xs-10">
										<div class="listRowInner">
											<img class="img-circle" src="'.$Row->GetImg().'" alt="'.$Row->Data['name'].'">
											<span class="listTextStrong">'.$Row->Data['name'].'</span>
										</div>
									</div>
									
									<div class="col-lg-2 col-md-3 col-sm-2 col-xs-6">
										<div class="listRowInner">
											<span class="listTextStrong">Adicional Fijo</span>
											<span class="emailTextResp"><span class="label label-success">$ '.$Row->Data['amount'].'</span></span>
										</div>
									</div>
									
									<div class="col-lg-2 col-md-3 col-sm-2 col-xs-6">
										<div class="listRowInner">
											<span class="listTextStrong">Adicional Porcentaje</span>
											<span class="emailTextResp"><span class="label label-primary">'.$Row->Data['percentage'].' %</span></span>
										</div>
									</div>
									
									<div class="animated DetailedInformation Hidden col-md-12">
										'.$Products.'
									</div>
									<div class="listActions flex-justify-center Hidden">
										<div>'.$Actions.'</div>
									</div>

								</div>';
				break;
				case "grid":
				$Regs	.= '<li id="grid_'.$Row->ID.'" class="RoundItemSelect roundItemBig'.$Restrict.'" title="'.$Row->Data['name'].'">
						            <div class="flex-allCenter imgSelector">
						              <div class="imgSelectorInner">
						                <img src="'.$Row->GetImg().'" alt="'.$Row->Data['name'].'" class="img-responsive">
						                <div class="imgSelectorContent">
						                  <div class="roundItemBigActions">
						                    '.$Actions.'
						                    <span class="roundItemCheckDiv"><a href="#"><button type="button" class="btn roundBtnIconGreen Hidden" name="button"><i class="fa fa-check"></i></button></a></span>
						                  </div>
						                </div>
						              </div>
						              <div class="roundItemText">
						                <p><b>'.$Row->Data['name'].'</b></p>
						              </div>
						            </div>
						          </li>';
				break;
			}
        }
        if(!$Regs) $Regs.= '<div class="callout callout-info"><h4><i class="icon fa fa-info-circle"></i> No se encontraron tipos de clientes.</h4><p>Puede crear un tipo de cliente haciendo click <a href="new.php">aqui</a>.</p></div>';
		return $Regs;
	}

	protected function InsertSearchField()
	{
		return '
			
          <div class="input-group">
            <span class="input-group-addon order-arrows sort-activated" order="name" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','name','','form-control','placeholder="Nombre"').'
          </div>
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="cuit" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','title','','form-control','placeholder="Producto"').'
          </div>
          ';
	}

	protected function InsertSearchButtons()
	{
		return '<!-- New Button -->
		    	<a aria-label="Nuevo Tipo de Cliente" class="hint--bottom hint--bounce hint--success" href="new.php"><button type="button" class="NewElementButton btn btnGreen animated fadeIn"><i class="fa fa-user-plus"></i></button></a>
		    	<!-- /New Button -->';
	}

	public function ConfigureSearchRequest()
	{
		$this->SetTable(
			$this->Table.' a
		');
		$this->SetFields('
			a.*
			
			');
			// b.name as branch_name,
			// b.phone as branch_phone,
			// b.email as branch_email
			// b.website as branch_website
			// b.postal_code as branch_postal_code
			// g.name as branch_country
			// h.name as branch_province
			// i.name as branch_region
			// j.name as branch_zone
		$this->SetWhere("a.company_id=".$_SESSION['company_id']);
		//$this->AddWhereString(" AND c.company_id = a.company_id");
		$this->SetOrder('name');
		$this->SetGroupBy("a.".$this->TableID);

		foreach($_POST as $Key => $Value)
		{
			$_POST[$Key] = $Value;
		}

		if($_POST['name']) $this->SetWhereCondition("a.name","LIKE","%".$_POST['name']."%");
		// if($_POST['title']) $this->SetWhereCondition("c.title","LIKE","%".$_POST['title']."%");
		
		if($_REQUEST['status'])
		{
			if($_POST['status']) $this->SetWhereCondition("a.status","=", $_POST['status']);
			if($_GET['status']) $this->SetWhereCondition("a.status","=", $_GET['status']);
		}else{
			$this->SetWhereCondition("a.status","=","A");
		}
		if($_POST['view_order_field'])
		{
			if(strtolower($_POST['view_order_mode'])=="desc")
				$Mode = "DESC";
			else
				$Mode = $_POST['view_order_mode'];

			$Order = strtolower($_POST['view_order_field']);
			switch($Order)
			{
				// case "title":
				// 	$Order = 'title';
				// 	$Prefix = "c.";
				// break;
				default:
					$Prefix = "a.";
				break;
			}
			$this->SetOrder($Prefix.$Order." ".$Mode);
		}
		if($_POST['regsperview'])
		{
			$this->SetRegsPerView($_POST['regsperview']);
		}
		if(intval($_POST['view_page'])>0)
			$this->SetPage($_POST['view_page']);
	}

	public function MakeList()
	{
		return $this->MakeRegs("List");
	}

	public function MakeGrid()
	{
		return $this->MakeRegs("Grid");
	}

	public function GetData()
	{
		return $this->Data;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// PROCESS METHODS ///////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function Insert()
	{
		$Name		= $_POST['name'];
		$Amount		= $_POST['amount'];
		$Percentage	= $_POST['percentage'];

		//VALIDATIONS
		if(!$Name) echo 'Falta Nombre';
		if(!$Amount) echo 'Falta Aumento Valor Fijo';
		if(!$Percentage) echo 'Falta Aumento Porcentual';
		$this->execQuery('INSERT',$this->Table,'name,amount,percentage,creation_date,created_by,company_id',"'".$Name."',".$Amount.",".$Percentage.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id']);
		//echo $this->lastQuery();
		
	}

	public function Update()
	{
		$ID 		= $_POST['id'];
		$Name		= $_POST['name'];
		$Amount		= $_POST['amount'];
		$Percentage	= $_POST['percentage'];
		
		//VALIDATIONS
		if(!$Name) echo 'Falta Nombre';
		if(!$Amount) echo 'Falta Aumento Valor Fijo';
		if(!$Percentage) echo 'Falta Aumento Porcentual';
		$this->execQuery('update',$this->Table,"name='".$Name."',amount=".$Amount.",percentage=".$Percentage.",updated_by=".$_SESSION['admin_id'],$this->TableID."=".$ID);
		// echo $this->lastQuery();
	}

	public function Activate()
	{
		$ID	= $_POST['id'];
		$this->execQuery('update',$this->Table,"status = 'A'",$this->TableID."=".$ID);
	}

	public function Delete()
	{
		$ID	= $_POST['id'];
		$this->execQuery('update',$this->Table,"status = 'I'",$this->TableID."=".$ID);
	}

	public function Search()
	{
		$this->ConfigureSearchRequest();
		echo $this->InsertSearchResults();
	}

	// public function Newimage()
	// {
	// 	if(count($_FILES['image'])>0)
	// 	{
	// 		$ID	= $_POST['id'];
	// 		if($ID)
	// 		{
	// 			$New = new Customer($ID);
	// 			if($_POST['newimage']!=$New->GetImg() && file_exists($_POST['newimage']))
	// 				unlink($_POST['newimage']);
	// 			$TempDir= $this->ImgGalDir;
	// 			$Name	= "customer".intval(rand()*rand()/rand());
	// 			$Img	= new FileData($_FILES['image'],$TempDir,$Name);
	// 			echo $Img	-> BuildImage(100,100);
	// 		}else{
	// 			if($_POST['newimage']!=$this->GetDefaultImg() && file_exists($_POST['newimage']))
	// 				unlink($_POST['newimage']);
	// 			$TempDir= $this->ImgGalDir;
	// 			$Name	= "customer".intval(rand()*rand()/rand());
	// 			$Img	= new FileData($_FILES['image'],$TempDir,$Name);
	// 			echo $Img	-> BuildImage(100,100);
	// 		}
	// 	}
	// }

	public function Validate()
	{
		$Name 			= strtolower($_POST['name']);
		$ActualName 	= strtolower($_POST['actualname']);

	    if($ActualName)
	    	$TotalRegs  = $this->numRows($this->Table,'*',"name = '".$Name."' AND name <> '".$ActualName."'");
    	else
		    $TotalRegs  = $this->numRows($this->Table,'*',"name = '".$Name."'");
		if($TotalRegs>0) echo $TotalRegs;
	}
}
?>
