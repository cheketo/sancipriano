<?php

class Product extends DataBase
{
	var	$ID;
	var $Data;
	var $Providers = array();
	var $DefaultImgURL = '../../../skin/images/products/default/default.png';
	var $Table = 'product';
	var $TableID = 'product_id';

	public function __construct($ID=0)
	{
		$this->Connect();
		if($ID!=0)
		{
			$Data = $this->fetchAssoc($this->Table." a LEFT JOIN product_category b ON (a.category_id = b.category_id) LEFT JOIN product_brand c ON (a.brand_id = c.brand_id) LEFT JOIN product_size d ON (a.size_id=d.size_id) LEFT JOIN product_variation e ON (a.variation_id=e.variation_id) ","a.*,b.title as category,c.name as brand,d.title as size,d.prefix as size_prefix,d.decimal as is_decimal, e.title as variation",$this->TableID."=".$ID);
			$this->Data = $Data[0];
			$this->ID = $ID;
			$this->SetStockSize();
			$this->SetPrice();
			
		}
	}
	
	public function SetPrice()
	{
		// $Coeficients = $this->fetchAssoc('product_configuration','*',"status='A'","creation_date");
		// print_r($Coeficients); die;
		if($this->Data['variation_id']=="1")
		{
			
			$Type ="percentage";
			
			// $this->Data['price']			= $this->Data['additional_percentage_wholesaler']? $this->Data['cost'] + (($this->Data['cost']*$this->Data['additional_'.$Type.'_wholesaler'])/100) :$this->Data['cost'] + (($this->Data['cost']*$Coeficients[0]['additional_'.$Type.'_wholesaler'])/100);
			// $this->Data['price_retailer']	= $this->Data['additional_percentage_retailer']? $this->Data['cost'] + (($this->Data['cost']*$this->Data['additional_'.$Type.'_retailer'])/100):$this->Data['cost'] + (($this->Data['cost']*$Coeficients[0]['additional_'.$Type.'_retailer'])/100);
		}else{
			$Type ="price";
			// $this->Data['price']			= $this->Data['additional_price_wholesaler']? $this->Data['cost']+$this->Data['additional_'.$Type.'_wholesaler']:$this->Data['cost']+$Coeficients[0]['additional_'.$Type.'_wholesaler'];
			// $this->Data['price_retailer']	= $this->Data['additional_price_retailer']? $this->Data['cost']+$this->Data['additional_'.$Type.'_retailer']:$this->Data['cost']+$Coeficients[0]['additional_'.$Type.'_retailer'];
		}
		// $this->Data['price'] = round($this->Data['price']);
		// $this->Data['price_retailer'] = round($this->Data['price_retailer']);
		
		// $this->Data['prices'] = $this->fetchAssoc(
		// 	"customer_type a LEFT JOIN relation_product_customer_type b ON (b.type_id=a.type_id)",
		// 	"a.name,ROUND(IF(1=".$this->Data['variation_id'].",(IF(b.additional_percentage IS NULL,(".$this->Data['cost']."+((".$this->Data['cost']." * a.percentage) / 100)),(".$this->Data['cost']."+((".$this->Data['cost']." * b.additional_percentage) / 100)))),(IF(b.additional_amount IS NULL,(".$this->Data['cost']."+a.amount),(".$this->Data['cost']."+b.additional_amount)))),2) AS  price",
		// 	"a.status = 'A' OR (a.status = 'A' AND b.status='A' AND product_id=".$this->Data['product_id'].")"
		// 	);
		
		
		$Types = $this->fetchAssoc('customer_type','*',"status='A'");
		foreach($Types as $Type)
		{
			$Price = array();
			$Price['name'] = $Type['name'];
			$Price['type_id'] = $Type['type_id'];
			$Relation = $this->fetchAssoc('relation_product_customer_type','*',"type_id=".$Type['type_id']." AND product_id=".$this->Data['product_id']);
			if(isset($this->Data['variation_id']) && $this->Data['variation_id']=="1")
			{
				if(isset($Relation[0]) && $Relation[0]['additional_percentage'])
					$Price['price'] = round($this->Data['cost'] + (($this->Data['cost']*$Relation[0]['additional_percentage'])/100));
				else
					$Price['price'] = round($this->Data['cost'] + (($this->Data['cost']*$Type['percentage'])/100));
			}else{
				if(isset($Relation[0]) && $Relation[0]['additional_amount'])
					$Price['price'] = round($this->Data['cost']+$Relation[0]['additional_amount']);
				else
					$Price['price'] = round($this->Data['cost']+$Type['amount']);
			}
			$this->Data['prices'][] = $Price;
		}
	}
	
	public function GetProductPrice($CID,$CT)
	{
		$Relation = $this->fetchAssoc("relation_product_customer","price","status = 'A' AND customer_id=".$CID." AND product_id=".$this->ID);
		//echo $this->lastQuery();
		if($Relation[0]['price'])
		{
			return $Relation[0]['price'];
		}else{
			foreach($this->Data['prices'] as $Type)
			{
				if($Type['type_id']==$CT)
					return $Type['price'];
			}
		
		}
	}
	
	public function SetStockSize()
	{
		if($this->Data['is_decimal']=="N")
		{
			
			$Stock = explode(".",$this->Data['stock']);
			$this->Data['stock'] = $Stock[0];
			$Stock = explode(".",$this->Data['stock_min']);
			$this->Data['stock_min'] = $Stock[0];
			$Stock = explode(".",$this->Data['stock_max']);
			$this->Data['stock_max'] = $Stock[0];
		}
	}
	
	public function GetProviders()
	{
		if(!$this->Providers)
		{
			$Providers = $this->fetchAssoc("relation_product_provider",'provider_id',$this->TableID." =".$this->ID);
			foreach($Providers as $Provider)
			{
				if($ProvidersID) $ProvidersID .= ',';
				$ProvidersID .= $Provider['provider_id'];
			}
			$this->Providers = $this->fetchAssoc('provider','*',"status='A' AND provider_id IN ('.$ProvidersID.')");
		}
		return $this->Providers;
	}
	
	// public function GetCategoryTree()
	// {
		
	// }
	
	public function GetImg()
	{
		return $this->DefaultImgURL;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// SEARCHLIST FUNCTIONS ///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function MakeRegs($Mode="List")
	{
		$Rows	= $this->GetRegs();
		$Regs = '';
		$Restrict = '';
		//echo $this->lastQuery();
		for($i=0;$i<count($Rows);$i++)
		{
			$Row	=	new Product($Rows[$i][$this->TableID]);
			$TypePrices = '';
			foreach($Row->Data['prices'] as $PriceType)
			{
				$TypePrices .= '
							<div class="row hideMobile990" style="padding:5px 0px;">
								<div class="col-xs-12 hideMobile990">
									<div class="listRowInner">
										<span class="listTextStrong">'.$PriceType['name'].'</span>
										<span class="listTextStrong"><span class="label label-success">$ '.$PriceType['price'].'</span></span>
									</div>
								</div>
							</div>
				';
			}
			
			
			$Items = '<div style="margin-top:10px;">';
				
				$Items .= '
							<div class="row" style="padding:5px 0px;">
								<div class="col-md-3 col-sm-6">
									<div class="listRowInner">
										<span class="listTextStrong">Categor&iacute;a</span>
										<span class="listTextStrong"><span class="label label-primary">'.ucfirst($Row->Data['category']).'</span></span>
									</div>
								</div>
								<div class="col-md-3 hideMobile990">
									<div class="listRowInner">
										<span class="listTextStrong">Estanter&iacute;a</span>
										<span class="listTextStrong"><span class="label label-warning">'.strtoupper($Row->Data['rack']).'</span></span>
									</div>
								</div>
								<div class="col-md-2 col-sm-6">
									<div class="listRowInner">
										<span class="listTextStrong">Variaci&oacute;n Precio</span>
										<span class="listTextStrong"><span class="label label-info">'.ucfirst($Row->Data['variation']).'</span></span>
									</div>
								</div>
							</div>
							'.$TypePrices.'
							<div class="row hideMobile990" style="padding:5px 0px;">
								<div class="col-xs-12 hideMobile990">
									<div class="listRowInner">
										<span class="listTextStrong">'.$Row->Data['description'].'</span>
									</div>
								</div>
							</div>
							';
			$Items .='</div>';
			$Price = isset($Row->Data['price'])?"$ ".$Row->Data['price']:'';
			$PriceRetail = isset($Row->Data['price_retailer'])?"$ ".$Row->Data['price_retailer']:'';
			
			$Actions= '';
			$Actions	.= 	'<span class="roundItemActionsGroup"><a><button type="button" class="btn btnGreen ExpandButton" id="expand_'.$Row->ID.'"><i class="fa fa-plus"></i></button></a>';
			$Actions	.= 	'<a href="edit.php?id='.$Row->ID.'"><button type="button" class="btn btnBlue"><i class="fa fa-pencil"></i></button></a>';
			if($Row->Data['status']=="A")
			{
				$Actions	.= '<a class="deleteElement" process="../../library/processes/proc.common.php" id="delete_'.$Row->ID.'"><button type="button" class="btn btnRed"><i class="fa fa-trash"></i></button></a>';
			}else{
				$Actions	.= '<a class="activateElement" process="../../library/processes/proc.common.php" id="activate_'.$Row->ID.'"><button type="button" class="btn btnGreen"><i class="fa fa-check-circle"></i></button></a>';
			}
			$Actions	.= '</span>';
			switch(strtolower($Mode))
			{
				case "list":
					$RowBackground = $i % 2 == 0? '':' listRow2 ';
					$Row->Data['cod'] = isset($Row->Data['cod'])? $Row->Data['cod']:'';
					$Regs	.= '<div class="row listRow'.$RowBackground.$Restrict.'" id="row_'.$Row->ID.'" title="'.$Row->Data['title'].'">
									<div class="col-lg-3 col-md-3 col-sm-10 col-xs-10">
										<div class="listRowInner" style="text-align:left!important;">
											<img class="img-circle hideMobile990" style="margin-right:1em!important;" src="'.$Row->GetImg().'" alt="'.$Row->Data['cod'].'">
											<span class="listTextStrong">'.$Row->Data['title'].'</span>
											<span class="listTextStrong"><span class="label label-info">'.$Row->Data['brand'].'</span></span>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-3 col-sm-3 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">Mayorista</span>
											<span class="listTextStrong"><span class="label label-success">'.$Price.'</span></span>
										</div>
									</div>
									<div class="col-lg-1 col-md-1 col-sm-1 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">Minorista</span>
											<span class="listTextStrong"><span class="label label-success">'.$PriceRetail.'</span></span>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-3 col-sm-3 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">Stock</span>
											<span class="listTextStrong"><span class="label label-warning">'.$Row->Data['stock'].' '.$Row->Data['size_prefix'].'</span></span>
										</div>
									</div>
									
									<div class="animated DetailedInformation Hidden col-md-12">
										'.$Items.'
									</div>
									<div class="listActions flex-justify-center Hidden">
										<div>'.$Actions.'</div>
									</div>
									
								</div>';
				break;
				case "grid":
				$Regs	.= '<li id="grid_'.$Row->ID.'" class="RoundItemSelect roundItemBig'.$Restrict.'" title="'.$Row->Data['title'].'">
						            <div class="flex-allCenter imgSelector">
						              <div class="imgSelectorInner">
						                <img src="'.$Row->GetImg().'" alt="'.$Row->Data['title'].'" class="img-responsive">
						                <div class="imgSelectorContent">
						                  <div class="roundItemBigActions">
						                    '.$Actions.'
						                    <span class="roundItemCheckDiv"><a href="#"><button type="button" class="btn roundBtnIconGreen Hidden" name="button"><i class="fa fa-check"></i></button></a></span>
						                  </div>
						                </div>
						              </div>
						              <div class="roundItemText">
						                <p><b>'.$Row->Data['title'].'</b></p>
						                <p><span class="label label-primary">'.ucfirst($Row->Data['category']).'</span></p>
						              </div>
						            </div>
						          </li>';
				break;
			}
        }
        if(!isset($Regs) || !$Regs) $Regs.= '<div class="callout callout-info"><h4><i class="icon fa fa-info-circle"></i> No se encontraron art&iacute;culos.</h4><p>Puede crear un art&iacute;culo haciendo click <a href="new.php">aqui</a>.</p></div>';
		return $Regs;
	}
	
	protected function InsertSearchField()
	{
		return '<!-- Name -->
          <div class="input-group">
            <span class="input-group-addon order-arrows sort-activated" order="title" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','title','','form-control','placeholder="Nombre"').'
          </div>
          <!-- Categories -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="category" mode="desc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('select','category','','form-control','',$this->fetchAssoc('product_category','category_id,title',"status = 'A' AND company_id = ".$_SESSION['company_id'],"title"),'', 'Categor&iacute;a').'
          </div>
          <!-- Brands -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="brand" mode="desc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('select','brand','','form-control','',$this->fetchAssoc('product_brand','brand_id,name',"status = 'A' AND company_id = ".$_SESSION['company_id'],"name"),'', 'Marcas').'
          </div>
          ';
	}
	
	protected function InsertSearchButtons()
	{
		// return '<!-- New Button -->
		//     	<a href="new.php" title="Crear nuevo producto"><button type="button" class="NewElementButton btn bg-purple animated fadeIn"><i class="fa fa-plus-square"></i></button></a>
		//     	<!-- /New Button -->
		//     	<!-- Actions -->
		//     	<button title="M&aacute;s acciones" type="button" id="MoreActions" class="btn bg-purple animated fadeIn Hidden NewElementButton"><i class="fa fa-gear"></i></button>
		//     	<!-- /Actions -->
		//     	<div id="MoreActionsBody" class="Hidden">
		//     		<div class="row">
		//     			<div class="col-xs-12 col-sm-6">
		//     				<h4 class="subTitleB"><i class="fa fa-dollar"></i> Modificar Precios Mayorista</h4>
		// 		    		<div class="row">
		// 			    		<div class="col-xs-6 col-sm-2 col-md-2">
		// 			    			'.insertElement('text','mayorist_price','','form-control imput-sm','placeholder="Valor"').'
		// 		                </div>
		// 		                <div class="col-xs-6 col-sm-8 col-md-6" style="padding-left:0px!important;">
		// 				    		<button type="button" class="btn btnGreen animated fadeIn updatePrice" price_type="m" operation="add" mode="%" title="Aumentar en procentaje"><i class="fa fa-plus-square"></i> %</button>
		// 				    		<button type="button" class="btn btnGreen animated fadeIn updatePrice" price_type="m" operation="add" mode="#" title="Aumentar en monto fijo"><i class="fa fa-plus-square"></i> #</button>
		// 				    		<button type="button" class="btn btn-warning animated fadeIn updatePrice" price_type="m" operation="sub" mode="%" title="Disminuír en procentaje"><i class="fa fa-minus-square"></i> %</button>
		// 				    		<button type="button" class="btn btn-warning animated fadeIn updatePrice" price_type="m" operation="sub" mode="#" title="Disminuír en monto fijo"><i class="fa fa-minus-square"></i> #</button>
		// 				    	</div>
		// 				    </div>
		//     			</div>
		//     			<div class="col-xs-12 col-sm-6">
		//     				<h4 class="subTitleB"><i class="fa fa-dollar"></i> Modificar Precios Minorista</h4>
		// 			    	<div class="row">
		// 			    		<div class="col-xs-6 col-sm-2 col-md-2">
		// 			    			'.insertElement('text','retail_price','','form-control imput-sm','placeholder="Valor"').'
		// 		                </div>
		// 		                <div class="col-xs-6 col-sm-8 col-md-6" style="padding-left:0px!important;">
		// 				    		<button type="button" class="btn btnGreen animated fadeIn updatePrice" price_type="r" operation="add" mode="%"><i class="fa fa-plus-square"></i> %</button>
		// 				    		<button type="button" class="btn btnGreen animated fadeIn updatePrice" price_type="r" operation="add" mode="#"><i class="fa fa-plus-square"></i> #</button>
		// 				    		<button type="button" class="btn btn-warning animated fadeIn updatePrice" price_type="r" operation="sub" mode="%"><i class="fa fa-minus-square"></i> %</button>
		// 				    		<button type="button" class="btn btn-warning animated fadeIn updatePrice" price_type="r" operation="sub" mode="#"><i class="fa fa-minus-square"></i> #</button>
		// 				    	</div>
		// 				    </div>
		//     			</div>
		//     		</div>
		//     	</div>
		//     	';
		return '<a href="new.php" title="Crear nuevo producto"><button type="button" class="NewElementButton btn bg-purple animated fadeIn"><i class="fa fa-plus-square"></i></button></a>';
		    	
		    	
	}
	
	public function ConfigureSearchRequest()
	{
		$this->SetTable($this->Table.' a LEFT JOIN product_category b ON (a.category_id=b.category_id) LEFT JOIN product_brand c ON (a.brand_id=c.brand_id) LEFT JOIN product_variation d ON (a.variation_id = d.variation_id)');
		$this->SetFields('a.*');
		$this->SetWhere("a.company_id = ".$_SESSION['company_id']);
		//$this->AddWhereString(" AND c.company_id = a.company_id");
		//$this->SetOrder('title');
		$this->SetGroupBy("a.".$this->TableID);
		
		foreach($_POST as $Key => $Value)
		{
			$_POST[$Key] = $Value;
		}
			
		if(isset($_POST['title']) && $_POST['title']) $this->SetWhereCondition("a.title","LIKE","%".$_POST['title']."%");
		if(isset($_POST['category']) && $_POST['category']) $this->SetWhereCondition("a.category_id","=",$_POST['category']);
		if(isset($_POST['brand']) && $_POST['brand']) $this->SetWhereCondition("a.brand_id","=",$_POST['brand']);
		// if($_POST['country']){
		// 	$this->SetWhereCondition("c.title","LIKE","%".$_POST['country']."%");
		// 	$this->AddWhereString(" AND b.country_id = c.country_id");
		// }
		
		//if($_POST['agent_email']) $this->SetWhereCondition("a.email","LIKE","%".$_POST['agent_email']."%");
		//if($_POST['agent_charge']) $this->SetWhereCondition("a.charge","LIKE", "%".$_POST['agent_charge']."%");
		// if($_POST['phone']) $this->AddWhereString(" AND (c.phone LIKE '%".$_POST['phone']."%' OR a.phone LIKE '%".$_POST['phone']."%')");
		//if($_POST['parent']) $this->SetWhereCondition("c.parent_id","=",$_POST['parent']);
		// if($_POST['agent_name'])
		// {
		// 	$this->AddWhereString(" AND c.company_id = a.company_id");
		// }
		if(isset($_REQUEST['status']) && $_REQUEST['status'])
		{
			if(isset($_POST['status']) && $_POST['status']) $this->SetWhereCondition("a.status","=", $_POST['status']);
			if(isset($_GET['status']) && $_GET['status']) $this->SetWhereCondition("a.status","=", $_GET['status']);	
		}else{
			$this->SetWhereCondition("a.status","=","A");
		}
		
		//$Prefix = "a.";
		$Order = 'title';
		//$Mode = 'ASC';
		$this->SetOrder($Order);
		if(isset($_POST['view_order_field']) && $_POST['view_order_field'])
		{
			$Mode = "DESC";
			if(isset($_POST['view_order_mode']) && strtolower($_POST['view_order_mode'])!="desc")
				$Mode = $_POST['view_order_mode'];
			
			$Order = strtolower($_POST['view_order_field']);
			switch($Order)
			{
				case "category":
					$Order = 'title';
					$Prefix = "b.";
				break;
				case "category":
					$Order = 'name';
					$Prefix = "c.";
				break;
			}
			$this->SetOrder($Prefix.$Order." ".$Mode);
		}
		
		
		
		
		if(isset($_POST['regsperview']) && $_POST['regsperview'])
		{
			$this->SetRegsPerView($_POST['regsperview']);
		}
		if(isset($_POST['view_page']) && intval($_POST['view_page'])>0)
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
	
	private function ChangeCost($Cost,$Date,$ID=0)
	{
		if($ID==0) $ID=$this->ID;
		$this->execQuery('INSERT','product_cost_history','product_id,cost,cost_date',$ID.",".$Cost.",".$Date);
		
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// PROCESS METHODS ///////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function InsertCustomerPrices($ID)
	{
		if($ID)
		{
			if(isset($_POST['variation']) && $_POST['variation']==1)
			{
				$Variation = 'percentage';
			}else{
				$Variation = 'amount';
			}
			$Fields = '';
			$TotalTypes = isset($_POST['total_types'])? $_POST['total_types']:0;
			for($I=1;$I<=$TotalTypes;$I++)
			{
				if(isset($_POST['additional_'.$Variation.$I]) && $_POST['additional_'.$Variation.$I]>0)
				{
					//$Prices[] = array("type_id"=>$_POST['type'.$I],"additional_".$Variation => $_POST['additional_'.$Variation.$I]);
					$Field = $_POST['type'.$I].",".$ID.",".$_POST['additional_'.$Variation.$I].",'A',NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
					$Fields .= $Fields? "),(".$Field:$Field;
				}
			}
			if($Fields)
			{
				$this->execQuery('DELETE','relation_product_customer_type',"product_id=".$ID);
				$this->execQuery('INSERT','relation_product_customer_type','type_id,product_id,additional_'.$Variation.',status,creation_date,created_by,company_id',$Fields);
				// print_r($_POST);
				// echo $this->lastQuery();
				
			}
		}
	}
	
	
	public function Insert()
	{
		$Title		= $_POST['title'];
		$Category	= $_POST['category'];
		//$Price		= str_replace('$','',$_POST['price']);
		$Cost		= str_replace('$','',$_POST['cost']);
		$Brand		= $_POST['brand'];
		$Rack		= $_POST['rack'];
		$Size		= $_POST['size'];
		$Variation	= $_POST['variation'];
		// $PRetailer	= $_POST['percentage_retailer']?$_POST['percentage_retailer']:0;
		// $PWholesaler= $_POST['percentage_wholesaler']?$_POST['percentage_wholesaler']:0;
		// $ARetailer	= $_POST['amount_retailer']?$_POST['amount_retailer']:0;
		// $AWholesaler= $_POST['amount_wholesaler']?$_POST['amount_wholesaler']:0;
		
		$Stock		= isset($_POST['stock'])? $_POST['stock']:0;
		$StockMin	= isset($_POST['stock_min'])? $_POST['stock_min']:0;
		$StockMax	= isset($_POST['stock_max'])? $_POST['stock_max']:0;
		$Description= isset($_POST['description'])? $_POST['description']:'';
		// $Dispatch	= $_POST['dispatch'];
		// $PriceRetail	= str_replace('$','',$_POST['price_retailer']);
		//$PriceDispatch	= $_POST['price_dispatch'];
		// if(!$Stock) $Stock = 0;
		// if(!$StockMin) $StockMin = 0;
		// if(!$StockMax) $StockMax = 0;
		// if(!$PriceFob) $PriceFob = 0;
		// if(!$PriceDispatch) $PriceDispatch = 0;
		$Insert	= $this->execQuery('insert',$this->Table,'title,category_id,cost,variation_id,brand_id,rack,size_id,stock,stock_min,stock_max,description,creation_date,company_id,created_by',"'".$Title."',".$Category.",".$Cost.",".$Variation.",".$Brand.",'".$Rack."',".$Size.",".$Stock.",".$StockMin.",".$StockMax.",'".$Description."',NOW(),".$_SESSION['company_id'].",".$_SESSION['admin_id']);
		$ID = $this->GetInsertId();
		$this->ChangeCost($Cost,'NOW()',$ID);
		$this->InsertCustomerPrices($ID);
		//echo $this->lastQuery();
	}	
	
	public function Update()
	{
		$ID 		= $_POST['id'];
		$Edit		= new Product($ID);
		
		$Title		= $_POST['title'];
		$Category	= isset($_POST['category'])?$_POST['category']:0;
		// $Price		= str_replace('$','',$_POST['price']);
		// $PriceRetail= str_replace('$','',$_POST['price_retailer']);
		$Cost		= str_replace('$','',$_POST['cost']);
		$Brand		= isset($_POST['brand'])?$_POST['brand']:0;
		$Rack		= $_POST['rack'];
		$Size		= $_POST['size'];
		$Variation	= isset($_POST['variation'])? $_POST['variation']:0;
		// $PRetailer	= $_POST['percentage_retailer']?$_POST['percentage_retailer']:0;
		// $PWholesaler= $_POST['percentage_wholesaler']?$_POST['percentage_wholesaler']:0;
		// $ARetailer	= $_POST['amount_retailer']?$_POST['amount_retailer']:0;
		// $AWholesaler= $_POST['amount_wholesaler']?$_POST['amount_wholesaler']:0;
		$StockMin	= isset($_POST['stock_min'])? $_POST['stock_min']:0;
		$StockMax	= isset($_POST['stock_max'])? $_POST['stock_max']:0;
		$Description= isset($_POST['description'])? $_POST['description']:'';
		
		$Update		= $this->execQuery('update',$this->Table,"title='".$Title."',category_id=".$Category.",brand_id=".$Brand.",cost=".$Cost.",variation_id=".$Variation.",rack='".$Rack."',size_id='".$Size."',stock_min='".$StockMin."',stock_max='".$StockMax."',description='".$Description."',updated_by=".$_SESSION['admin_id'],$this->TableID."=".$ID);
		//echo $this->lastQuery();
		$this->ChangeCost($Cost,'NOW()',$ID);
		$this->InsertCustomerPrices($ID);
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
		//echo $this->lastQuery();
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
	// 		if($_POST['newimage']!=$this->GetDefaultImg() && file_exists($_POST['newimage']))
	// 			unlink($_POST['newimage']);
	// 		$TempDir = $this->ImgGalDir;
	// 		$Name	= "company".intval(rand()*rand()/rand());
	// 		$Img	= new FileData($_FILES['image'],$TempDir,$Name);
	// 		echo $Img	-> BuildImage(365,110);
	// 	}
	// }
	
	public function Validate()
	{
		$Name 			= isset($_POST['title'])? strtolower($_POST['title']):null;
		$ActualName 	= isset($_POST['actualtitle'])? strtolower($_POST['actualtitle']):null;
		$TotalRegs		= 0;
		if($Name)
		{
			if($ActualName)
				$TotalRegs  = $this->numRows($this->Table,'*',"title = '".$Name."' AND title<> '".$ActualName."'");
			else
				$TotalRegs  = $this->numRows($this->Table,'*',"title = '".$Name."'");
		}
		if($TotalRegs>0) echo $TotalRegs;
	}
	
	// public function Update_price()
	// {
	// 	$IDS		= $_POST['ids'].'0';
	// 	$Operation	= $_POST['operation'];
	// 	$Value		= $_POST['value'];
	// 	$Mode		= $_POST['mode'];
	// 	$Type		= $_POST['type'];
		
	// 	$Field = $Type=='r'? 'price_retailer':'price';
		
	// 	$Operation = $Operation=="add"? '+':'-';
		
	// 	if($Mode=="%")
	// 	{
	// 		$Value = '(('.$Value.'*'.$Field.')/100)';
	// 		//$this->execQuery('UPDATE','product',$Field.'='.$Field.$Operation.$Percentage);
	// 	}
	// 	$this->execQuery('UPDATE','product',$Field.'='.$Field.$Operation.$Value,'product_id IN ('.$IDS.')');
	// 	//echo $this->lastQuery();
	// }
	
	public function Config()
	{
		$Wpercent		= isset($_POST['wpercent']) && floatval($_POST['wpercent'])>=0? floatval($_POST['wpercent']):-1;
		$Wadditional	= isset($_POST['wadditional']) && floatval($_POST['wadditional'])>=0? floatval($_POST['wadditional']):-1;
		$Rpercent		= isset($_POST['rpercent']) && floatval($_POST['rpercent'])>=0? floatval($_POST['rpercent']):-1;
		$Radditional	= isset($_POST['radditional']) && floatval($_POST['radditional'])>=0? floatval($_POST['radditional']):-1;
		
		if($Wpercent>-1 && $Wadditional>-1 && $Rpercent>-1 && $Radditional>-1)
		{
			$this->execQuery('INSERT','product_configuration','additional_percentage_wholesaler,additional_percentage_retailer,additional_price_wholesaler,additional_price_retailer,created_by,status,company_id,creation_date',$Wpercent.",".$Rpercent.",".$Wadditional.",".$Radditional.",".$_SESSION['admin_id'].",'A',1,NOW()");
			$ID = $this->GetInsertId();
			if($ID>0)
			{
				$this->execQuery('UPDATE','product_configuration',"status='I'","configuration_id<>".$ID);
			}
		}else{
			echo 401;
		}
	}
	
	public function Relationcustomer()
	{
		$CustomerID = $_POST['cid'];
		if($CustomerID>0)
		{
			$this->execQuery("UPDATE","relation_product_customer","status='I',updated_by=".$_SESSION['admin_id'],"customer_id=".$CustomerID);
			$Products = $_POST['total'];
			$Fields = '';
			for($I=1;$I<=$Products;$I++)
			{
				$ProductID = $_POST['id'.$I];
				$Price = $_POST['value'.$I];
				$Values = $CustomerID.",".$ProductID.",".$Price.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
				$Fields .= $Fields? '),('.$Values:$Values;
			}
			$this->execQuery("INSERT","relation_product_customer","customer_id,product_id,price,creation_date,created_by,company_id",$Fields);
			//echo $this->lastQuery();
		}
	}
	
	public function UpdateRelationByCustomer($CustomerID=0,$Products=array())
	{
		if($CustomerID)
		{
			$_POST['cid'] = $CustomerID;
			// $IsDistributor = $this->numRows("customer","*","type_id=4 AND customer_id=".$CustomerID);
			$I=0;
			// if(!$IsDistributor)
			// {
				$Relations = $this->fetchAssoc("relation_product_customer","product_id","status='A' AND customer_id=".$CustomerID);
				foreach($Relations as $Relation)
				{
					$ProductsRelation[] = $Relation['product_id'];
				}
				if(is_array($ProductsRelation))
				{
					foreach($Products as $Key=>$Product)
					{
						if(in_array($Product[0],$ProductsRelation))
						{
							$I++;
							$_POST['id'.$I] = $Product[0];
							$_POST['value'.$I] = $Product[1];
						}
					}
				}
			// }else{
			// 	foreach($Products as $Key=>$Product)
			// 	{	
			// 		$I++;
			// 		$_POST['id'.$I] = $Product[0];
			// 		$_POST['value'.$I] = $Product[1];
			// 	}
			// }
			$_POST['total'] = $I;
			$this->Relationcustomer();
		}
	}
}
?>
