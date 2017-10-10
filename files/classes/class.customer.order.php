<?php

class CustomerOrder extends DataBase
{
	var	$ID;
	var $Data;
	var $Delivery;
	var $DeliveryMan;
	var $Items 			= array();
	var $Table			= "customer_order";
	var $TableID		= "order_id";
	var $MovementConcept = 'Orden de Compra Nº';
	
	const DEFAULTIMG	= "../../../skin/images/providers/default/order.png";

	public function __construct($ID=0)
	{
		$this->Connect();
		if($ID!=0)
		{
			$Data = $this->fetchAssoc($this->Table.' a LEFT JOIN customer b ON (a.customer_id=b.customer_id) LEFT JOIN customer_branch c ON (c.customer_id=b.customer_id)',"a.*,c.address,b.name",$this->TableID."=".$ID,'');
			$this->Data = $Data[0];
			$this->ID = $ID;
			$this->Data['items'] = $this->GetItems();
		}
	}
	
	public function GetItems()
	{
		if(empty($this->Items))
		{
			$this->Items = $this->fetchAssoc($this->Table."_item a LEFT JOIN product b ON (a.product_id = b.product_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id) INNER JOIN product_size d ON (d.size_id=b.size_id)","a.*,(a.price * a.quantity) AS total,b.title,d.prefix AS size,d.decimal,c.prefix as currency",$this->TableID."=".$this->ID,'a.item_id');
				//echo $this->lastQuery();
		}
		return $this->Items;
	}
	
	public function GetDelivery()
	{
		if(!$this->Delivery)
		{
			$this->Delivery = $this->fetchAssoc('customer_delivery','*','delivery_id='.$this->Data['delivery_id']);
			$this->Delivery = $this->Delivery[0];
		}
		return $this->Delivery;
	}
	
	public function GetDeliveryMan()
	{
		if($this->Data['delivery_id'])
		{
			$Delivery = $this->GetDelivery();
			if(!$this->DeliveryMan)
			{
				$this->DeliveryMan = $this->fetchAssoc('admin_user','*','admin_id='.$Delivery['delivery_man_id']);
				$this->DeliveryMan = $this->DeliveryMan[0];
				$this->Data['delivery_man'] = $this->DeliveryMan['first_name'].' '.$this->DeliveryMan['last_name'];
			}
		}
		return $this->DeliveryMan;
	}
	
	public function GetTotalFinal($OrderID)
	{
		if($OrderID>0)
		{
			$Total = $this->fetchAssoc("customer_order_item","SUM((price*quantity_delivered)) AS total","delivered='Y' AND order_id=".$OrderID);
			return $Total[0]['total'];
		}else{
			return 0;
		}
	}

	public function GetDefaultImg()
	{
		return self::DEFAULTIMG;
	}
	
	public function GetImg()
	{
		return $this->GetDefaultImg();
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// SEARCHLIST FUNCTIONS ///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function MakeRegs($Mode="List")
	{
		$Rows	= $this->GetRegs();
		//echo $this->lastQuery();
		for($i=0;$i<count($Rows);$i++)
		{
			
			$Row	=	new CustomerOrder($Rows[$i][$this->TableID]);
			$Actions	= 	'<span class="roundItemActionsGroup"><a><button type="button" class="btn btnGreen ExpandButton" title="Ver detalle" id="expand_'.$Row->ID.'"><i class="fa fa-plus"></i></button></a>';
			
			// if($Row->Data['status']=="P" || $Row->Data['status']=="I"){
			// 	$Actions	.= '<a class="associateElement" order="'.$Row->ID.'" id="activate_'.$Row->ID.'"><button type="button" class="btn btn-dropbox" title="Asociar a reparto"><i class="fa fa-sign-out"></i></button></a>';
			// }
			
			if($Row->Data['status']=="P" || $Row->Data['status']=="A")
			{
				if($Row->Data['delivery_status']=="P")
					$Actions	.= 	'<a href="edit.php?id='.$Row->ID.'"><button type="button" class="btn btnBlue" title="Editar orden"><i class="fa fa-pencil"></i></button></a>';
				
				if($Row->Data['status']=="A" && $Row->Data['type']=="Y")
					$Actions	.= 	'<a href="payment.php?id='.$Row->ID.'"><button type="button" class="btn btnGreen" title="Pagar Orden"><i class="fa fa-dollar"></i></button></a>';
				if($Row->Data['delivery_id']==0)
					$Actions	.= '<a class="deleteElement" process="../../library/processes/proc.common.php" title="Eliminar orden" id="delete_'.$Row->ID.'"><button type="button" class="btn btnRed"><i class="fa fa-trash"></i></button></a>';
				
			}elseif($Row->Data['status']=="V"){
				$Actions	.= 	'<a href="edit.php?id='.$Row->ID.'"><button type="button" class="btn btn-bitbucket" title="Reactivar orden"><i class="fa fa-refresh"></i></button></a>';
				$Actions	.= '<a class="deleteElement" process="../../library/processes/proc.common.php" title="Eliminar orden" id="delete_'.$Row->ID.'"><button type="button" class="btn btnRed"><i class="fa fa-trash"></i></button></a>';
			}elseif($Row->Data['status']=="F"){
				$Actions	.= 	'<a href="print.php?id='.$Row->ID.'" target="_blank"><button type="button" class="btn btn-info" title="Imprimir Recibo"><i class="fa fa-print"></i></button></a>';	
			}
			
			$Actions	.= '</span>';
			// echo '<pre>';
			// print_r($Row->Data['items']);
			// echo '</pre>';
			$Date = explode(" ",$Row->Data['delivery_date']);
			$OrderDate = implode("/",array_reverse(explode("-",$Date[0])));
			
			$Items = '<div style="margin-top:10px;">';
			$I=0;
			foreach($Row->Data['items'] as $Item)
			{
				$I++;
				$RowClass = $I % 2 != 0? 'bg-gray':'bg-gray-active';
				
				$Date = explode(" ",$Item['delivery_date']);
				$DeliveryDate = implode("/",array_reverse(explode("-",$Date[0])));
				$ItemTotal = $Item['currency']." ".$Item['total'];
				$ItemPrice = $Item['currency']." ".$Item['price'];
				
				$Items .= '
							<div class="row '.$RowClass.'" style="padding:5px;">
								<div class="col-md-3 col-sm-6">
									<div class="listRowInner">
										<span class="listTextStrong">'.$Item['title'].'</span>
									</div>
								</div>
								<div class="col-md-3 hideMobile990">
									<div class="listRowInner">
										<span class="listTextStrong">Precio</span>
										<span class="listTextStrong"><span class="label label-info">'.$ItemPrice.'</span></span>
									</div>
								</div>
								<div class="col-md-3 hideMobile990">
									<div class="listRowInner">
										<span class="listTextStrong">Cantidad</span>
										<span class="listTextStrong"><span class="label label-primary">'.$Item['quantity'].'</span></span>
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<div class="listRowInner">
										<span class="listTextStrong">Total Art.</span>
										<span class="listTextStrong"><span class="label label-success">'.$ItemTotal.'</span></span>
									</div>
								</div>
									
							</div>';
			}
			$Items .= '</div>';
			switch(strtolower($Mode))
			{
				case "list":
						$Extra = !$Row->Data['extra']? '': '<div class="col-lg-2 col-md-3 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="emailTextResp">'.$Row->Data['extra'].'</span>
										</div>
									</div>';
						
						if($Row->Data['delivery_id'])
						{
							$DeliveryMan	= $Row->GetDeliveryMan();
							$Col3Title = 'Repartidor';
							$Col3Value = $DeliveryMan['first_name']." ".$DeliveryMan['last_name'];
						}else{
							$Col3Title = 'Cant. Total';
							$Col3Value = $Rows[$i]['quantity'];
						}
									
					$RowBackground = $i % 2 == 0? '':' listRow2 ';
					
					$Regs	.= '<div class="row listRow'.$RowBackground.'" id="row_'.$Row->ID.'" title="una orden de compra">
									<div class="col-lg-3 col-md-5 col-sm-8 col-xs-10">
										<div class="listRowInner">
											<img class="img-circle" style="border-radius:0%!important;" src="'.$Row->GetImg().'" alt="'.$Row->Data['name'].'">
											<span class="listTextStrong">'.$Row->Data['name'].'</span>
											<span class="listTextStrong">
												<span class="label label-warning">
													<i class="fa fa-calendar"></i> '.$OrderDate.'
												</span>
											</span>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">'.$Col3Title.'</span>
											<span class="listTextStrong"><span class="label label-primary">'.$Col3Value.'</span></span>
										</div>
									</div>
									<div class="col-lg-2 col-md-3 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">Precio Total</span>
											<span class="emailTextResp"><span class="label label-success">'.$Row->Data['items'][0]['currency'].' '.$Row->Data['total'].'</span></span>
										</div>
									</div>
									'.$Extra.'
									<div class="animated DetailedInformation Hidden col-md-12">
										'.$Items.'
									</div>
									<div class="listActions flex-justify-center Hidden">
										<div>'.$Actions.'</div>
									</div>
									
								</div>';
				break;
				case "grid":
				$Regs	.= '<li id="grid_'.$Row->ID.'" class="RoundItemSelect roundItemBig'.$Restrict.'" title="'.$Row->Data['customer'].'">
						            <div class="flex-allCenter imgSelector">
						              <div class="imgSelectorInner">
						                <img src="'.$Row->GetImg().'" alt="'.$Row->Data['customer'].'" class="img-responsive">
						                <div class="imgSelectorContent">
						                  <div class="roundItemBigActions">
						                    '.$Actions.'
						                    <span class="roundItemCheckDiv"><a href="#"><button type="button" class="btn roundBtnIconGreen Hidden" name="button"><i class="fa fa-check"></i></button></a></span>
						                  </div>
						                </div>
						              </div>
						              <div class="roundItemText">
						                <p><b>'.$Row->Data['customer'].'</b></p>
						                <p>'.$Date.'</p>
						                <p>('.$Row->Data['quantity'].')</p>
						              </div>
						            </div>
						          </li>';
				break;
			}
        }
        if(!$Regs) $Regs.= '<div class="callout callout-info"><h4><i class="icon fa fa-info-circle"></i> No se encontraron ordenes de compras.</h4><p>Puede crear una orden haciendo click <a href="new.php?type='.$_GET['type'].'">aqui</a>.</p></div>';
		return $Regs;
	}
	
	protected function InsertSearchField()
	{
		if($_GET['delivery_date']=="today")
		{
			$Date = date("d/m/Y");
		}
		
		return '<!-- Provider -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="name" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','name','','form-control','placeholder="Cliente"').'
          </div>
          <!-- Title -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="title" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','title','','form-control','placeholder="Art&iacute;culo"').'
          </div>
          <!-- Delivery Date -->
          <div class="input-group">
            <span class="input-group-addon order-arrows sort-activated" order="delivery_date" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','delivery_date',$Date,'form-control delivery_date','placeholder="Entrega"').'
          </div>
          <!-- Extra -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="extra" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','extra','','form-control','placeholder="Info Extra"').'
          </div>
          ';
	}
	
	protected function InsertSearchButtons()
	{
		if($_REQUEST['status'])
			$Status = $_REQUEST['status'];
		else
			$Status = 'P';
		$HTML = '<!-- New Button -->
		    	<a href="new.php?type='.$_GET['type'].'" title="Crear nueva orden de compra"><button type="button" class="NewElementButton btn btnGreen animated fadeIn"><i class="fa fa-plus-square"></i></button></a>
		    	<!-- /New Button -->';
		if(($Status=='P' || $Status=='A') && $_GET['type']=='N') $HTML .= '<button type="button" title="Asociar a un repartidor" class="btn btnBlue animated fadeIn Hidden" id="Associate"><i class="fa fa-sign-out"></i></button>';
		return $HTML;
	}
	
	public function ConfigureSearchRequest()
	{
		$this->SetTable($this->Table.' a LEFT JOIN customer_order_item b ON (b.order_id=a.order_id) LEFT JOIN product c ON (b.product_id = c.product_id) LEFT JOIN customer d ON (d.customer_id=a.customer_id)');
		$this->SetFields('a.order_id,a.type,a.total,a.extra,a.status,a.payment_status,a.delivery_status,d.name as customer,SUM(b.quantity) as quantity');
		$this->SetWhere("a.company_id=".$_SESSION['company_id']);
		//$this->AddWhereString(" AND c.company_id = a.company_id");
		$this->SetGroupBy("a.".$this->TableID);
		
		foreach($_POST as $Key => $Value)
		{
			$_POST[$Key] = $Value;
		}
			
		if($_POST['name']) $this->SetWhereCondition("d.name","LIKE","%".$_POST['name']."%");
		if($_POST['title']) $this->SetWhereCondition("c.title","LIKE","%".$_POST['title']."%");
		if($_POST['extra']) $this->SetWhereCondition("a.extra","LIKE","%".$_POST['extra']."%");
		if($_REQUEST['delivery_date'])
		{
			if($_POST['delivery_date'])
			{
				$_POST['delivery_date'] = implode("-",array_reverse(explode("/",$_POST['delivery_date'])));
				$this->SetWhereCondition("a.delivery_date","=",$_POST['delivery_date']);
			}elseif($_GET['delivery_date']=="today"){
				$Date = date("Y-m-d");
				$this->SetWhereCondition("a.delivery_date","=",$Date);
			}
		}
		
		
		if($_REQUEST['status'])
		{
			if($_POST['status']) $this->SetWhereCondition("a.status","=", $_POST['status']);
			if($_GET['status']) $this->SetWhereCondition("a.status","=", $_GET['status']);	
		}else{
			$this->SetWhereCondition("a.status","=","P");
		}
		
		if($_REQUEST['type'])
		{
			if($_POST['type']) $this->SetWhereCondition("a.type","=", $_POST['type']);
			if($_GET['type']) $this->SetWhereCondition("a.type","=", $_GET['type']);	
		}
		
		if(strtolower($_POST['view_order_mode'])=="desc")
			$Mode = "DESC";
		else
			$Mode = $_POST['view_order_mode'];
		
		$Order = strtolower($_POST['view_order_field']);
		switch($Order)
		{
			case "name": 
				$Order = 'name';
				$Prefix = "d.";
			break;
			case "title": 
				$Order = 'title';
				$Prefix = "c.";
			break;
			default:
				$Order = 'delivery_date';
				$Prefix = "a.";	
			break;
		}
		$this->SetOrder($Prefix.$Order." ".$Mode);
		// }
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
		// ITEMS DATA
		$Items = array();
		for($I=1;$I<=$_POST['items'];$I++)
		{
			if($_POST['item_'.$I])
			{
				$Items[] = array('id'=>$_POST['item_'.$I],'price'=>$_POST['price_'.$I],'quantity'=>$_POST['quantity_'.$I]);
			}
		}
		
		// Basic Data
		$Type			= $_POST['order_type'];
		$BranchID		= $_POST['customer'];
		$CurrencyID		= $_POST['currency'];
		$Extra			= $_POST['extra'];
		$Total			= $_POST['total_price'];
		$Status			= $Type=="Y"? "A":"P";
		$Date			= implode("-",array_reverse(explode("/",$_POST['delivery_date'])));
		$Customer 		= $this->fetchAssoc('customer_branch','customer_id',"branch_id=".$BranchID);
		$CustomerID		= $Customer[0]['customer_id'];
		
		$Insert			= $this->execQuery('insert',$this->Table,'type,branch_id,customer_id,currency_id,extra,total,delivery_date,status,creation_date,created_by,company_id',"'".$Type."',".$BranchID.",".$CustomerID.",".$CurrencyID.",'".$Extra."',".$Total.",'".$Date."','".$Status."',NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id']);
		//echo $this->lastQuery();
		$NewID 		= $this->GetInsertId();
		$New 	= new CustomerOrder($NewID);
		
		// INSERT ITEMS
		foreach($Items as $Item)
		{
			if($Fields)
				$Fields .= "),(";
			$Fields .= $NewID.",".$CustomerID.",".$Item['id'].",".$Item['price'].",".$Item['quantity'].",'".$Date."',".$CurrencyID.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
		}
		
		$this->execQuery('insert','customer_order_item','order_id,customer_id,product_id,price,quantity,delivery_date,currency_id,creation_date,created_by,company_id',$Fields);
		//echo $this->lastQuery();
		if($Type=="N")
		{
			if($_POST['delivery_man'])
				$this->Associate($NewID,$_POST['delivery_man']);
			
			// INSERT MOVEMENT
			// Movement::InsertMovement($Total,$CustomerID,1,$this->MovementConcept.$NewID,$NewID);
		}else{
			// INSERT MOVEMENT
			// Movement::InsertMovement($Total,$CustomerID,1,'Compra en Local Nº'.$NewID,$NewID);
		}
	}
	
	public function Update()
	{
		$ID 	= $_POST['id'];
		$Edit	= new CustomerOrder($ID);
		
		// ITEMS DATA
		$Items = array();
		for($I=1;$I<=$_POST['items'];$I++)
		{
			if($_POST['item_'.$I])
			{
				$Items[] = array('id'=>$_POST['item_'.$I],'price'=>$_POST['price_'.$I],'quantity'=>$_POST['quantity_'.$I]);
			}
		}
		
		// Basic Data
		$Type			= $_POST['order_type'];
		$BranchID		= $_POST['customer'];
		$CurrencyID		= $_POST['currency']?$_POST['currency']:2;
		$Extra			= $_POST['extra'];
		$Total			= $_POST['total_price'];
		$Status			= "P";
		$Date			= implode("-",array_reverse(explode("/",$_POST['delivery_date'])));
		$Customer 		= $this->fetchAssoc('customer_branch','customer_id',"branch_id=".$BranchID);
		$CustomerID		= $Customer[0]['customer_id'];
		
		
		
		$Update		= $this->execQuery('update','customer_order',"type='".$Type."',branch_id=".$BranchID.",customer_id='".$CustomerID."',currency_id=".$CurrencyID.",extra='".$Extra."',total=".$Total.",delivery_date='".$Date."',status='".$Status."',updated_by=".$_SESSION['admin_id'],"order_id=".$ID);
		//echo $this->lastQuery();
		
		if($_POST['delivery_man'] && $Type=='N')
			$this->Associate($ID,$_POST['delivery_man']);
		
		// PROCESS AGENTS
		$Agents = array();
		for($i=1;$i<=$_POST['total_agents'];$i++)
		{
			if($_POST['agent_name_'.$i])
				$Agents[] = array('name'=>ucfirst($_POST['agent_name_'.$i]),'charge'=>ucfirst($_POST['agent_charge_'.$i]),'email'=>$_POST['agent_email_'.$i],'phone'=>$_POST['agent_phone_'.$i],'extra'=>$_POST['agent_extra_'.$i]);
		}
		
		// DELETE OLD ITEMS
		$this->execQuery('delete','customer_order_item',"order_id = ".$ID);
		
		// INSERT ITEMS
		foreach($Items as $Item)
		{
			if($Fields)
				$Fields .= "),(";
			$Fields .= $ID.",".$CustomerID.",".$Item['id'].",".$Item['price'].",".$Item['quantity'].",'".$Date."',".$CurrencyID.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
		}
		$this->execQuery('insert','customer_order_item','order_id,customer_id,product_id,price,quantity,delivery_date,currency_id,creation_date,created_by,company_id',$Fields);
		//echo $this->lastQuery();
		if($Type=="N")
		{
			// UPDATE MOVEMENT
			// Movement::UpdateMovementByOrderID($Total,$CustomerID,1,$this->MovementConcept.$ID,$ID);
		}else{
			// UPDATE MOVEMENT
			Movement::UpdateMovementByOrderID($Total,$CustomerID,1,'Compra en Local Nº'.$ID,$ID);
		}
	}
	
	public function Activate()
	{
		$ID	= $_POST['id'];
		$Order = new CustomerOrder($ID);
		$Status = $Order->Data['status'] == 'I'? 'P' : 'A';
		Movement::ActivateOrdersMovements($ID);
		$this->execQuery('update',$this->Table,"status = '".$Status."'",$this->TableID."=".$ID);
	}
	
	public function Delete()
	{
		// INSERT MOVEMENT??
		$ID	= $_POST['id'];
		Movement::DeleteOrdersMovements($ID);
		$this->execQuery('update',$this->Table,"status = 'I'",$this->TableID."=".$ID);
		$Order = $this->fetchAssoc($this->Table,'*','order_id = '.$ID);
		$Order = $Order[0];
		if($Order['delivery_id']>0)
		{
			$OldDelivery = $this->fetchAssoc($this->Table,'*'," order_id<>".$Order['order_id']." AND delivery_id=".$Order['delivery_id']);
			if(count($OldDelivery)<1)
			{
				$this->execQuery('DELETE','customer_delivery','delivery_id='.$Order['delivery_id']);
			}
		}
	}
	
	public function Search()
	{
		$this->ConfigureSearchRequest();
		echo $this->InsertSearchResults();
	}
	
	public function Newimage()
	{
		if(count($_FILES['image'])>0)
		{
			$ID	= $_POST['id'];
			if($ID)
			{
				$New = new ProviderPurchaseOrder($ID);
				if($_POST['newimage']!=$New->GetImg() && file_exists($_POST['newimage']))
					unlink($_POST['newimage']);
				$TempDir= $this->ImgGalDir;
				$Name	= "provider".intval(rand()*rand()/rand());
				$Img	= new FileData($_FILES['image'],$TempDir,$Name);
				echo $Img	-> BuildImage(100,100);
			}else{	
				if($_POST['newimage']!=$this->GetDefaultImg() && file_exists($_POST['newimage']))
					unlink($_POST['newimage']);
				$TempDir= $this->ImgGalDir;
				$Name	= "provider".intval(rand()*rand()/rand());
				$Img	= new FileData($_FILES['image'],$TempDir,$Name);
				echo $Img	-> BuildImage(100,100);
			}
		}
	}
	
	public function Validate()
	{
		$User 			= strtolower($_POST['name']);
		$ActualUser 	= strtolower($_POST['actualname']);

	    if($ActualUser)
	    	$TotalRegs  = $this->numRows($this->Table,'*',"name = '".$User."' AND name <> '".$ActualUser."'");
    	else
		    $TotalRegs  = $this->numRows($this->Table,'*',"name = '".$User."'");
		if($TotalRegs>0) echo $TotalRegs;
	}
	
	public function Fillagents()
	{
		$Provider = $_POST['provider'];
		$Agents = $this->fetchAssoc('product_provider_agent','agent_id,name',"provider_id=".$Provider,'name');
		if(count($Agents)>0)
		{
			$HTML = insertElement('select','agents','','form-control select2 selectTags',' style="width: 100%;height:auto!important;"',$Agents,'','Seleccione un Contacto');
		}else{
			//$HTML = insertElement('select','agents','','form-control select2 selectTags',' style="width: 100%;height:auto!important;"','','0','Sin Contacto');
		}
		echo $HTML;
	}
	
	public function Getitemprices()
	{
		if(intval($_POST['customer'])>0 && $_POST['items'])
		{
			$Config		= $this->fetchAssoc('product_configuration','*',"status='A' AND company_id=".$_SESSION['company_id'],'creation_date DESC');
			$Branch 	= $this->fetchAssoc('customer_branch','customer_id',"branch_id=".$_POST['customer']);
			
			$Customer	= new Customer($Branch[0]['customer_id']);
			//echo $Customer->Data['type_id'];die;
			$Prices = array();
			$Items = explode(",",$_POST['items']);
			foreach($Items as $Item)
			{
				$Product	= new Product($Item);
				$Cost		= $Product->Data['cost'];
				$Variation  = $Product->Data['variation_id']==1? "percentage":"price";
				
				switch(intval($Customer->Data['type_id']))
				{
					case 1:
						$Field = $Config[0]["additional_".$Variation."_retailer"];
					break;
					case 2:
						$Field = $Config[0]["additional_".$Variation."_wholesaler"];
					break;
					default:
						$Field	= $Customer->Data['additional_'.$Variation];
					break;
				}
				
				$AdditionalPrice = $Variation=="percentage"? ($Cost*$Field)/100 : $Field ;
				$Price = $Cost + $AdditionalPrice;
				$Prices[] = round($Price);
			}
		}
		echo implode(",",$Prices);
		die;
		
	}
	
	public function Addorderitem()
	{
		$ID = $_POST['item'];
		$TotalPrice = "$ 0.00";
		if($ID % 2 != 0)
			$BgClass = "bg-gray";
		else
			$BgClass = "bg-gray-active";
		$HTML = '
			<div id="item_row_'.$ID.'" item="'.$ID.'" class="row form-group inline-form-custom ItemRow '.$BgClass.'" style="margin-bottom:0px!important;padding:10px 0px!important;">
                <form id="item_form_'.$ID.'">
                <div class="col-xs-4 txC">
                	<span id="Item'.$ID.'" class="Hidden ItemText'.$ID.'"></span>
                  '.insertElement('select','item_'.$ID,'','ItemField'.$ID.' form-control selectChosen chosenSelect itemSelect','data-placeholder="Seleccione un Art&iacute;culo" validateEmpty="Seleccione un Art&iacute;culo" item="'.$ID.'"',$this->fetchAssoc('product a INNER JOIN product_brand b ON (a.brand_id=b.brand_id)',"a.product_id,CONCAT(a.title,' - ',b.name) AS title","a.status='A' AND a.company_id=".$_SESSION['company_id'],'title'),'',' ').'
                </div>
                <div class="col-xs-1 txC">
                	<span id="Price'.$ID.'" class="Hidden ItemText'.$ID.'"></span>
                  '.insertElement('text','price_'.$ID,'','ItemField'.$ID.' txC form-control calcable','data-inputmask="\'mask\': \'9{+}.99\'" placeholder="Precio" validateEmpty="Ingrese un precio"').'
                </div>
                <div class="col-xs-1 txC">
                	<span id="Quantity'.$ID.'" class="Hidden ItemText'.$ID.'"></span>
                  '.insertElement('text','quantity_'.$ID,'','ItemField'.$ID.' txC form-control calcable QuantityItem','data-inputmask="\'mask\': \'9{+}\'" placeholder="Cantidad" validateEmpty="Ingrese una cantidad"').'
                </div>
                
                <div  id="item_number_'.$ID.'" class="col-xs-3 txC item_number txC" total="0" item="'.$ID.'">'.$TotalPrice.'</div>
                <div class="col-xs-3 txC">
				  <button type="button" id="SaveItem'.$ID.'" class="btn btnGreen SaveItem" style="margin:0px;" item="'.$ID.'"><i class="fa fa-check"></i></button>
				  <button type="button" id="EditItem'.$ID.'" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="'.$ID.'"><i class="fa fa-pencil"></i></button>
				  <button type="button" id="DeleteItem'.$ID.'" class="btn btnRed DeleteItem" style="margin:0px;" item="'.$ID.'"><i class="fa fa-trash"></i></button>
				</div>
				</form>
            </div>';
            echo $HTML;
	}
	
	public function Customerdata()
	{
		$Branch = $_POST['id'];
		if($Branch>0)
		{
			$Customer = $this->fetchAssoc('customer a INNER JOIN customer_branch b ON (b.customer_id=a.customer_id) INNER JOIN customer_type c ON (a.type_id=c.type_id)',"a.balance,c.name AS type","b.branch_id=".$Branch);
			$Customer = $Customer[0];
			$Class = $Customer['balance']<0? 'danger':'success';
			$Customer['balance'] = $Customer['balance']? $Customer['balance']: '0.00';
			$HTML = '
			<h4 class="subTitleB"><i class="fa fa-info"></i> Informaci&oacute;n del Cliente</h4>
			<div class="row">
				<div class="col-sm-6 col-xs-12">
					Tipo de Cliente: <span class="label label-info"><i class="fa fa-briefcase"></i> '.$Customer['type'].'</span>
				</div>
				<div class="col-sm-6 col-xs-12">
					Balance: <span class="label label-'.$Class.'"><i class="fa fa-dollar"></i> '.$Customer['balance'].'</span>
				</div>
			</div>
			';
			echo $HTML;
		}
	}
	
	public function Associate($OrderID=0,$DeliveryManID=0)
	{
		$IDs = $OrderID==0? $_POST['selected']."0": $OrderID;	
		$DeliveryMan = $DeliveryManID==0? $_POST['user']: $DeliveryManID;
		if(intval($DeliveryMan)>0)
		{
			$Orders = $this->fetchAssoc($this->Table,'*','order_id IN ('.$IDs.')');
			foreach($Orders as $Order)
			{
				
				$DeliveryDate = explode(" ",$Order['delivery_date']);
				$DeliveryDate = $DeliveryDate[0];
				
				$Position = 1;
				
				$Delivery = $this->fetchAssoc('customer_delivery','*',"delivery_man_id = ".$DeliveryMan." AND delivery_date='".$DeliveryDate."' AND status='P'");
				if(!$Delivery[0]['delivery_id'])
				{
					$this->execQuery('insert','customer_delivery','delivery_man_id,delivery_date,company_id,created_by,creation_date',$DeliveryMan.",'".$DeliveryDate."',".$_SESSION['company_id'].",".$_SESSION['admin_id'].",NOW()");
					$DeliveryID = $this->GetInsertId();
				}else{
					$DeliveryID = $Delivery[0]['delivery_id'];
					$MaxPosition = $this->fetchAssoc($this->Table,'max(position)+1 AS position'," order_id<>".$Order['order_id']." AND delivery_id=".$DeliveryID);
					if($MaxPosition[0]['position']>1)
						$Position = $MaxPosition[0]['position'];
				}
				
				if($Order['delivery_id']>0)
				{
					//$this->execQuery('DELETE','customer_delivery',"delivery_id NOT IN (SELECT delivery_id FROM customer_order) AND delivery_id <> ".$DeliveryID);
					$OldDelivery = $this->fetchAssoc($this->Table,'*'," order_id<>".$Order['order_id']." AND delivery_id=".$Order['delivery_id']);
					if(!count($OldDelivery)>0)
					{
						$this->execQuery('DELETE','customer_delivery','delivery_id='.$Order['delivery_id']);
					}
				}
				$this->execQuery('update',$this->Table,"status='A', type='N', updated_by=".$_SESSION['admin_id'].", delivery_id=".$DeliveryID.",position=".$Position,'order_id='.$Order['order_id']);
				//echo '<br>'.$this->lastQuery();
			}
		}	
	}
	
	
	// LOCAL ORDER PAYMENT
	
	public function Payment()
	{
		$OrderID = $_POST['id'];
		$CustomerID = $_POST['cid'];
		$OrderData = $this->fetchAssoc("customer_order","*","order_id=".$OrderID);
		$Data = $OrderData[0];
		$DeliveryID = $OrderData['delivery_id'];
		$OrderStatus = $OrderData['status'];
		
		$TotalAmount = 0;
		if($OrderData['status'] = 'A')
		{
			if(intval($_POST['cash'])>0 || intval($_POST['checks'])>0)
				$MovementStatus = "F";
			else
				$MovementStatus = "A";
			
			// DEBIT MOVEMENT
			$LastMovementID = $DebitMovementID = Movement::InsertMovement($_POST['total_price'],$CustomerID,5,$this->MovementConcept.$OrderID,$OrderID,$MovementStatus);
			
			
			if(intval($_POST['cash'])>0 || intval($_POST['checks'])>0)
			{
				$TotalChecks = 0;
				if(intval($_POST['checks'])>0)
				{
					$Checks = array();
					for($I=1;$I<=intval($_POST['checks']);$I++)
					{
						$Checks[$I]['number'] = $_POST['check_number_'.$I];
						$Checks[$I]['amount'] = $_POST['check_amount_'.$I];
						$Checks[$I]['bank'] = ucfirst($_POST['check_bank_'.$I]);
						$Checks[$I]['from'] = ucfirst($_POST['check_from_'.$I]);
						$Checks[$I]['date'] = ToDBDate($_POST['check_date_'.$I]);
						$TotalChecks = $TotalChecks + $Checks[$I]['amount'];
						$InsertCheck = $Checks[$I]['number'].",".$Checks[$I]['amount'].",'".$Checks[$I]['bank']."','".$Checks[$I]['from']."','".$Checks[$I]['date']."',".$OrderID.",'P',NOW(),".$_SESSION['admin_id'];
						$this->execQuery("INSERT","payment_check","number,amount,bank,sender,due_date,order_id,status,creation_date,created_by",$InsertCheck);
						$CheckID = $this->GetInsertId();
						//$InsertChecks = $InsertChecks? '),('.$InsertCheck:$InsertCheck;
						$LastMovementID = Movement::InsertMovement($Checks[$I]['amount'],$CustomerID,2,"Pago de Orden N°".$OrderID,$OrderID,"F",2,$DebitMovementID,$CheckID);
					}
					
				}
				$TotalAmount = intval($_POST['cash']);
				
				if(intval($_POST['cash'])>0 && intval($_POST['checks'])>0)
					$PaymentID = 3;
				elseif(intval($_POST['cash'])>0)
					$PaymentID = 1;
				else
					$PaymentID = 2;
				$LastMovementID = Movement::InsertMovement($TotalAmount,$CustomerID,2,"Pago de Orden N°".$OrderID,$OrderID,"F",1,$DebitMovementID);
				$TotalAmount += $TotalChecks;
			}
			
			$Items = $_POST['items'];
			for($I=1;$I<=$Items;$I++)
			{
				if($_POST['selected_'.$I]=='Y')
				{
					$QuantityDelivered = $_POST['quantity_'.$I];
					$this->execQuery("UPDATE","customer_order_item","delivered='Y',status='F',quantity_delivered=".$QuantityDelivered.",payment_status='F'","item_id=".$_POST['item_'.$I]);
				}
			}
			
			//CUSTOMER BALANACE UNTIL NOW
			$MovementData = $this->fetchAssoc("movement","*","movement_id=".$LastMovementID);
			$FinalBalance = $MovementData[0]['balance'];
				
			$this->execQuery("UPDATE","customer_order","total_paid=".$TotalAmount.",balance=".$FinalBalance.",status='F',payment_status='F',delivery_status='F',updated_by=".$_SESSION['admin_id'],"order_id=".$OrderID);
			
			
			
		}else{
			echo "403";
		}
	}
}
?>
