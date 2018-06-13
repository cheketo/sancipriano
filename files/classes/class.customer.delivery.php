<?php

class CustomerDelivery extends DataBase
{
	var	$ID;
	var $Data;
	var $Table			= "customer_delivery";
	var $TableID		= "delivery_id";
	
	const DEFAULTIMG	= "../../../skin/images/delivery/default/delivery.png";

	public function __construct($ID=0)
	{
		$this->Connect();
		if($ID!=0)
		{
			$Data = $this->fetchAssoc($this->Table.' a LEFT JOIN admin_user b ON (a.delivery_man_id=b.admin_id)',"a.*,CONCAT(b.first_name,' ',b.last_name) AS delivery_man",$this->TableID."=".$ID);
			$this->Data = $Data[0];
			$this->ID = $ID;
			$this->GetOrders();
		}
	}
	
	public function GetOrders()
	{
		if(empty($this->Data['orders']))
		{
			$this->Data['orders'] = $this->fetchAssoc("relation_delivery_order p JOIN customer_order a ON (a.order_id=p.order_id) LEFT JOIN customer b ON (b.customer_id=a.customer_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id)","a.*,b.name,SUM(a.total) AS total_price,c.prefix AS currency","p.status<>'I' AND p.".$this->TableID."=".$this->ID,'a.position','a.order_id');
			$this->Data['orders'] = array_merge($this->Data['orders'],$this->fetchAssoc("relation_delivery_order p JOIN customer_delivery_order a ON (a.order_id=p.order_id AND a.delivery_id=".$this->ID.") LEFT JOIN customer b ON (b.customer_id=a.customer_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id)","a.*,b.name,SUM(a.total) AS total_price,c.prefix AS currency","p.status='I' AND p.".$this->TableID."=".$this->ID,'a.position','a.order_id'));
			//echo $this->lastQuery();
			$this->Data['total'] = count($this->Data['orders']);
			foreach($this->Data['orders'] as $Order)
			{
				if(!$this->Data['orders_ids'])
					$this->Data['orders_ids'] = $Order['order_id'];
				else
					$this->Data['orders_ids'] .= ','.$Order['order_id'];
				$this->Data['total_price'] += $Order['total_price'];	
			}
			
			// print_r($this->Data['orders']);
			// echo '<br><br>';
		}
		return $this->Data['orders'];
	}
	
	public function GetTotalProducts()
	{
		if(!$this->Data['products'])
		{
			$this->Data['products'] = $this->fetchAssoc("customer_order_item a INNER JOIN relation_delivery_order b ON (a.order_id=b.order_id AND b.status<>'I') INNER JOIN product c ON (c.product_id=a.product_id) INNER JOIN product_size d ON (c.size_id=d.size_id)",
			"a.product_id,b.delivery_id,c.title,SUM(a.quantity) as quantity, SUM(a.quantity * a.price) AS total,d.prefix AS unit",
			"b.delivery_id=".$this->ID,'',"a.product_id");	
		}
		return $this->Data['products'];
		
	}
	
	public function GetOrdersByDate($Date=0,$DeliveryID='-1')
	{
		if($Date)
		{
			$Where .= " AND a.delivery_date = '".$Date."'";
		}
		return $this->fetchAssoc("customer_order a LEFT JOIN customer_branch b ON (a.branch_id=b.branch_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id) LEFT JOIN customer_delivery d ON (d.delivery_id=a.delivery_id) LEFT JOIN admin_user e ON (d.delivery_man_id=e.admin_id) LEFT JOIN customer f ON (b.customer_id=f.customer_id)","f.zone,a.*,f.name,b.address,c.prefix AS currency,CONCAT(e.first_name,' ',e.last_name) as delivery_man","(a.status='A' OR a.status='P') AND (a.delivery_id=0 OR d.status = 'P') AND a.delivery_id<>".$DeliveryID.$Where,'a.position','a.order_id');
	}
	
	public function GetOrdersByID($ID=0)
	{
		if($ID)
			return $this->fetchAssoc("customer_order a LEFT JOIN customer_branch b ON (a.branch_id=b.branch_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id) LEFT JOIN customer_delivery d ON (d.delivery_id=a.delivery_id) LEFT JOIN admin_user e ON (d.delivery_man_id=e.admin_id) LEFT JOIN customer f ON (b.customer_id=f.customer_id)","f.name,f.zone,a.*,b.address,c.prefix AS currency,CONCAT(e.first_name,' ',e.last_name) as delivery_man","(a.status='A' OR a.status='P') AND d.status = 'P' AND a.delivery_id=".$ID,'a.position','a.order_id');
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
		// echo $this->lastQuery();
		for($i=0;$i<count($Rows);$i++)
		{
			$DeliveryTotal = 0;
			$Row	=	new CustomerDelivery($Rows[$i][$this->TableID]);
			$Actions	= 	'<span class="roundItemActionsGroup"><a><button type="button" class="btn btnGreen ExpandButton" title="Ver detalle" id="expand_'.$Row->ID.'"><i class="fa fa-plus"></i></button></a>';
			
			// if($Row->Data['status']=="P" || $Row->Data['status']=="I"){
			// 	$Actions	.= '<a class="associateElement" order="'.$Row->ID.'" id="activate_'.$Row->ID.'"><button type="button" class="btn btn-dropbox" title="Asociar a reparto"><i class="fa fa-sign-out"></i></button></a>';
			// }
			$Today		= $_GET['delivery_date']? '&delivery_date=today':'';
			if($Row->Data['status']!="V")
				$Actions	.= 	'<a href="print.php?id='.$Row->ID.$Today.'" target="_blank"><button type="button" class="btn btn-dropbox" title="Imprimir Reparto"><i class="fa fa-print"></i></button></a>';
			if($Row->Data['status']=="P")
			{
				$Actions	.= 	'<a href="edit.php?id='.$Row->ID.$Today.'"><button type="button" class="btn btnBlue" title="Editar Reparto"><i class="fa fa-pencil"></i></button></a>';
				$Actions	.= '<a class="deleteElement" process="../../library/processes/proc.common.php" title="Eliminar Reparto" id="delete_'.$Row->ID.'"><button type="button" class="btn btnRed"><i class="fa fa-trash"></i></button></a>';
				
			}
			$Actions	.= '</span>';
			// echo '<pre>';
			// print_r($Row->Data['items']);
			// echo '</pre>';
			$Date = explode(" ",$Row->Data['delivery_date']);
			$DeliveryDate = implode("/",array_reverse(explode("-",$Date[0])));
			
			$Orders = '<div style="margin-top:10px;">';
			$I=0;
			foreach($Row->Data['orders'] as $Order)
			{
				$I++;
				$RowClass = $I % 2 != 0? 'bg-gray':'bg-gray-active';
				
				$Date = explode(" ",$Order['delivery_date']);
				//$DeliveryDate = implode("/",array_reverse(explode("-",$Date[0])));
				
				$DeliveryTotalTitleFlag = false;
				if($Order['status']=='F')
				{
					$ObjOrder = new CustomerOrder();
					$OrderTotal = $ObjOrder->GetTotalFinal($Order['order_id']);
					if(!$OrderTotal) $OrderTotal = 0;
					$TotalTitle = 'Final';
					$DeliveryTotalTitleFlag = true;
					$PrintHTML = '<div class="col-md-3 col-sm-4">
									<div class="listRowInner">
										<a href="../customer_delivery_order/print.php?id='.$Order['order_id'].'" target="_blank"><button type="button" class="btn btn-dropbox" title="Imprimir Entrega"><i class="fa fa-file"></i></button></a>
									</div>
								</div>';
				}else{
					$OrderTotal = $Order['total'];
					$TotalTitle = 'Inicial';
					$PrintHTML = '';
				}
				if($Order['delivery_id']==$Row->Data['delivery_id'])
				{
					switch ($Order['status'])
					{
						case 'P':
							if($Row->Data['status']=='A')
								$OrderStatus = 'Pendiente de Carga';
							else
								$OrderStatus = 'Pendiente';
							$OrderLabel = 'primary';
						break;
						case 'A':
							$OrderStatus = 'Pendiente de Entrega';
							$OrderLabel = 'warning';
						break;
						case 'F':
							$OrderStatus = 'Entregado';
							$OrderLabel = 'success';
						break;
						case 'C':
							$OrderStatus = 'Cancelado';
							$OrderLabel = 'danger';
						break;
						case 'V':
							$OrderStatus = 'Vencido';
							$OrderLabel = 'danger';
						break;
					}
				}else{
					$OrderStatus = 'No Entregado';
					$OrderLabel = 'danger';
				}
				
				$DeliveryTotal += $OrderTotal;
				
				$Orders .= '
							<div class="row '.$RowClass.'" style="padding:5px;">
								<div class="col-md-3 col-sm-4">
									<div class="listRowInner">
										<span class="listTextStrong">'.$Order['name'].'</span>
										<span class="listTextStrong"><span class="label label-'.$OrderLabel.'">'.$OrderStatus.'</span></span>
									</div>
								</div>
								<div class="col-md-3 col-sm-4">
									<div class="listRowInner">
										<span class="listTextStrong">Total '.$TotalTitle.'</span>
										<span class="listTextStrong"><span class="label label-success">$'.$OrderTotal.'</span></span>
									</div>
								</div>
								'.$PrintHTML.'
									
							</div>';
			}
			$Orders .= '</div>';
			switch(strtolower($Mode))
			{
				case "list":
						$Extra = !$Row->Data['extra']? '': '<div class="col-lg-2 col-md-2 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="emailTextResp">'.$Row->Data['extra'].'</span>
										</div>
									</div>';
									
					$RowBackground = $i % 2 == 0? '':' listRow2 ';
					
					switch ($Row->Data['status']) {
						case 'P':
							if($Row->Data['delivery_date']==date('Y-m-d'))
							{
								$StatusLabel = 'primary';
								$DeliveryStatus = 'Pendiente de Carga';
							}else{
								$StatusLabel = 'info';
								$DeliveryStatus = 'Pendiente';
							}
						break;
						case 'A':
							$StatusLabel = 'warning';
							$DeliveryStatus = 'En Proceso';	
						break;
						case 'F':
							$StatusLabel = 'success';
							$DeliveryStatus = 'Finalizado';	
						break;
						case 'V':
							$StatusLabel = 'danger';
							$DeliveryStatus = 'Vencido';	
						break;
					}
					
					if($DeliveryTotalTitleFlag)
						$DeliveryTotalTitle = 'Recaudado';
					else
						$DeliveryTotalTitle = 'Proyectado';
					
					$Regs	.= '<div class="row listRow'.$RowBackground.'" id="row_'.$Row->ID.'" title="un reparto">
									<div class="col-lg-4 col-md-5 col-sm-4 col-xs-5">
										<div class="listRowInner">
											<img class="img-circle" style="border-radius:0%!important;" src="'.$Row->GetImg().'" alt="'.$Row->Data['delivery_man'].'" />
											<span class="listTextStrong">'.$Row->Data['delivery_man'].'</span>
											<span class="listTextStrong">
												<span class="label label-warning">
													<i class="fa fa-calendar"></i> '.$DeliveryDate.'
												</span>
											</span>
										</div>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="listTextStrong">Estado</span>
											<span class="listTextStrong"><span class="label label-'.$StatusLabel.'">'.$DeliveryStatus.'</span></span>
										</div>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
										<div class="listRowInner">
											<span class="listTextStrong">Monto '.$DeliveryTotalTitle.'</span>
											<span class="listTextStrong"><span class="label label-success">'.$Row->Data['orders'][0]['currency'].' '.$DeliveryTotal.'</span></span>
										</div>
									</div>
									
									'.$Extra.'
									<div class="animated DetailedInformation Hidden col-md-12">
										'.$Orders.'
									</div>
									<div class="listActions flex-justify-center Hidden">
										<div>'.$Actions.'</div>
									</div>
									
								</div>';
				break;
				case "grid":
				$Regs	.= '<li id="grid_'.$Row->ID.'" class="RoundItemSelect roundItemBig'.$Restrict.'" title="'.$Row->Data['delivery_man'].'">
						            <div class="flex-allCenter imgSelector">
						              <div class="imgSelectorInner">
						                <img src="'.$Row->GetImg().'" alt="'.$Row->Data['delivery_man'].'" class="img-responsive">
						                <div class="imgSelectorContent">
						                  <div class="roundItemBigActions">
						                    '.$Actions.'
						                    <span class="roundItemCheckDiv"><a href="#"><button type="button" class="btn roundBtnIconGreen Hidden" name="button"><i class="fa fa-check"></i></button></a></span>
						                  </div>
						                </div>
						              </div>
						              <div class="roundItemText">
						                <p><b>'.$Row->Data['delivery_man'].'</b></p>
						                <p>'.$DeliveryDate.'</p>
						              </div>
						            </div>
						          </li>';
				break;
			}
        }
        if(!$Regs) $Regs.= '<div class="callout callout-info"><h4><i class="icon fa fa-info-circle"></i> No se encontraron repartos.</h4><p>Puede crear un reparto haciendo click <a href="new.php">aqui</a>.</p></div>';
		return $Regs;
	}
	
	protected function InsertSearchField()
	{
		return '<!-- Customer Branch -->
          <div class="input-group">
            <span class="input-group-addon order-arrows" order="name" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','name','','form-control','placeholder="Cliente"').'
          </div>
          <!-- Delivery Date -->
          <div class="input-group">
            <span class="input-group-addon order-arrows sort-activated" order="delivery_date" mode="asc"><i class="fa fa-sort-alpha-asc"></i></span>
            '.insertElement('text','delivery_date',$_GET['delivery_date'],'form-control delivery_date','placeholder="Entrega"').'
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
		    	<a href="new.php" title="Crear nuevo reparto"><button type="button" class="NewElementButton btn btnGreen animated fadeIn"><i class="fa fa-plus-square"></i></button></a>
		    	<!-- /New Button -->';
		//if($Status=='P' || $Status=='W') $HTML .= '<button type="button" title="Asociar a un repartidor" class="btn btnBlue animated fadeIn Hidden" id="Associate"><i class="fa fa-sign-out"></i></button>';
		return $HTML;
	}
	
	public function ConfigureSearchRequest()
	{
		//$this->SetTable($this->Table.' a LEFT JOIN customer_order_item b ON (b.order_id=a.order_id) LEFT JOIN product c ON (b.product_id = c.product_id) LEFT JOIN customer_branch d ON (d.customer_id=a.customer_id)');
		$this->SetTable($this->Table.' a LEFT JOIN customer_order b ON (b.delivery_id=a.delivery_id) LEFT JOIN customer_order_item c ON (c.order_id=b.order_id) LEFT JOIN product d ON (c.product_id = d.product_id) LEFT JOIN customer e ON (e.customer_id=b.customer_id) LEFT JOIN customer_delivery_order f ON (f.delivery_id=a.delivery_id) LEFT JOIN customer_delivery_order_item g ON (g.order_id=b.order_id AND g.delivery_id=a.delivery_id)');
		$this->SetFields('a.delivery_id,b.order_id,b.type,b.total,b.extra,b.status,b.payment_status,b.delivery_status,e.name as customer');
		$this->SetWhere("a.company_id=".$_SESSION['company_id']);
		//$this->AddWhereString(" AND c.company_id = a.company_id");
		$this->SetGroupBy("a.".$this->TableID);
		
		foreach($_POST as $Key => $Value)
		{
			$_POST[$Key] = $Value;
		}
			
		if($_POST['name']) $this->SetWhereCondition("e.name","LIKE","%".$_POST['name']."%");
		//if($_POST['title']) $this->SetWhereCondition("c.title","LIKE","%".$_POST['title']."%");
		//if($_POST['extra']) $this->SetWhereCondition("a.extra","LIKE","%".$_POST['extra']."%");
		if($_REQUEST['delivery_date'])
		{
			$Date = strtolower($_REQUEST['delivery_date']);
			if($Date=='today' || $Date=='expired')
			{
				$Date = date("d/m/Y");
			}
			$Date = implode("-",array_reverse(explode("/",$Date)));
			if(strtolower($_REQUEST['delivery_date'])!='expired')
			{
				$this->SetWhereCondition("a.delivery_date",">=",$Date);
			}elseif($Date){
				$this->SetWhereCondition("a.delivery_date","<",$Date);
			}
			
		}
		// else{
		// 	$Date = date("Y-m-d");
		// 	$this->SetWhereCondition("a.delivery_date",">=",$Date);
		// }
		
		
		if($_REQUEST['status'])
		{
			if($_REQUEST['status']!='X')
			{
				if($_POST['status']) $this->SetWhereCondition("a.status","=", $_POST['status']);
				if($_GET['status']) $this->SetWhereCondition("a.status","=", $_GET['status']);
			}else{
				if($_POST['status']) $this->SetWhereCondition("a.status","<>", "I");
				if($_GET['status']) $this->SetWhereCondition("a.status","<>", "I");
			}
		}else{
			if($_REQUEST['delivery_date']!="expired")
				$this->SetWhereCondition("a.status","=","P");
			else
				$this->SetWhereCondition("a.status","=","V");
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
					$Prefix = "e.";
				break;
				// case "title": 
				// 	$Order = 'title';
				// 	$Prefix = "c.";
				// break;
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
		// Basic Data
		$DeliveryDate	= ToDBDate($_POST['delivery_date']);
		$DeliveryManID	= $_POST['delivery_man'];
		$Orders			= explode(",",$_POST['orders']);
		$Extra			= $_POST['extra'];
		$Status			= 'P';
		
		if(!empty($Orders))
		{
			//CREATE NEW DELIVERY
			$this->execQuery('insert',$this->Table,'delivery_man_id,delivery_date,extra,status,creation_date,created_by,company_id',$DeliveryManID.",'".$DeliveryDate."','".$Extra."','".$Status."',NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id']);
			//echo $this->lastQuery();
			$NewID 		= $this->GetInsertId();
			
			//SET DELIVERY ID TO ORDERS AND POSITION
			$Position = 1;
			$Delivery = new CustomerDeliveryOrder();
			foreach($Orders as $OrderID)
			{
				$OrderDelivery = $this -> fetchAssoc('customer_order','delivery_id','order_id='.$OrderID);
				if($OrderDelivery[0]['delivery_id']!=$ID)
					$Delivery -> RemoveOrderItemsToDelivery($OrderID,$OrderDelivery[0]['delivery_id']);
				$this->execQuery('UPDATE','customer_order',"delivery_date = '".$DeliveryDate."', position=".$Position.", status='A', delivery_id = ".$NewID,"order_id=".$OrderID);
				$Delivery->AddOrderItemsToDelivery($OrderID,$NewID);
				$Position++;
			}
			//DELETE REGS THAT DOESN'T HAVE ORDERS ASSOCIATED
			$this->execQuery("DELETE",$this->Table," delivery_id NOT IN (SELECT delivery_id FROM customer_order)");
		}
		
	}
	
	public function Update()
	{
		$ID 			= $_POST['id'];
		$DeliveryDate	= ToDBDate($_POST['delivery_date']);
		$DeliveryManID	= $_POST['delivery_man'];
		$Orders			= explode(",",$_POST['orders']);
		$Extra			= $_POST['extra'];
		
		
		if(!empty($Orders))
		{
			$Delivery = new CustomerDeliveryOrder();
			$Delivery -> RemoveAllOrdersFromDelivery($ID);
		
			//UPDATE DELIVERY
			$this->execQuery('UPDATE',$this->Table,"extra='".$Extra."', delivery_date='".$DeliveryDate."', delivery_man_id=".$DeliveryManID,"delivery_id=".$ID);
			
			//UPDATE ALL PERVIOUS ORDERS TO 'P' STATUS
			$this->execQuery('UPDATE','customer_order',"status='P', delivery_id=0","delivery_id=".$ID);
			
			//SET DELIVERY ID TO ORDERS AND POSITION
			$Position = 1;
			foreach($Orders as $OrderID)
			{
				$OrderDelivery = $this -> fetchAssoc('customer_order','delivery_id','order_id='.$OrderID);
				if($OrderDelivery[0]['delivery_id']!=$ID)
					$Delivery -> RemoveOrderItemsToDelivery($OrderID,$OrderDelivery[0]['delivery_id']);
				$this->execQuery('UPDATE','customer_order',"delivery_date = '".$DeliveryDate."', position=".$Position.", status='A', delivery_id = ".$ID,"order_id=".$OrderID);
				$Delivery->AddOrderItemsToDelivery($OrderID,$ID);
				$Position++;
			}
			//DELETE REGS THAT DOESN'T HAVE ORDERS ASSOCIATED
			$this->execQuery("DELETE",$this->Table," delivery_id NOT IN (SELECT delivery_id FROM relation_delivery_order WHERE (status='A' OR status='F')");
			
		}
	}
	
	public function Start()
	{
		$ID	= $_POST['id'];
		$Merluza = $_POST['merluza'];
		$Delivery = new CustomerDelivery($ID);
		
		if($Delivery->Data['status']=='P')
		{
			if($Merluza)
				$Filter = ",merluza = ".$Merluza;
			$this->execQuery('UPDATE','customer_delivery',"status='A'".$Filter,"delivery_id=".$ID);
			$this->execQuery('UPDATE','customer_order',"delivery_status='A'","delivery_id=".$ID);
		}else{
			echo 403;
		}
	}
	
	public function Activate()
	{
		$ID	= $_POST['id'];
		$Order = new ProviderPurchaseOrder($ID);
		$Status = $Order->Data['status'] == 'I'? 'P' : 'A';
		$this->execQuery('update',$this->Table,"status = '".$Status."'",$this->TableID."=".$ID);
	}
	
	public function Delete()
	{
		$ID	= $_POST['id'];
		$this->execQuery('update',$this->Table,"status = 'I'",$this->TableID."=".$ID);
		$this->execQuery('update','customer_order',"status = 'P',delivery_status='P',delivery_id=0",$this->TableID."=".$ID);
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
                  '.insertElement('select','item_'.$ID,'','ItemField'.$ID.' form-control selectChosen itemSelect','data-placeholder="Seleccione un Art&iacute;culo" validateEmpty="Seleccione un Art&iacute;culo" item="'.$ID.'"',$this->fetchAssoc('product','product_id,title',"status='A' AND company_id=".$_SESSION['company_id'],'title'),'',' ').'
                </div>
                <div class="col-xs-1 txC">
                	<span id="Price'.$ID.'" class="Hidden ItemText'.$ID.'"></span>
                  '.insertElement('text','price_'.$ID,'','ItemField'.$ID.' form-control calcable','data-inputmask="\'mask\': \'9{+}.99\'" placeholder="Precio" validateEmpty="Ingrese un precio"').'
                </div>
                <div class="col-xs-1 txC">
                	<span id="Quantity'.$ID.'" class="Hidden ItemText'.$ID.'"></span>
                  '.insertElement('text','quantity_'.$ID,'','ItemField'.$ID.' form-control calcable QuantityItem','data-inputmask="\'mask\': \'9{+}\'" placeholder="Cantidad" validateEmpty="Ingrese una cantidad"').'
                </div>
                
                <div  id="item_number_'.$ID.'" class="col-xs-3 txC item_number" total="0" item="'.$ID.'">'.$TotalPrice.'</div>
                <div class="col-xs-3 txC">
				  <button type="button" id="SaveItem'.$ID.'" class="btn btnGreen SaveItem" style="margin:0px;" item="'.$ID.'"><i class="fa fa-check"></i></button>
				  <button type="button" id="EditItem'.$ID.'" class="btn btnBlue EditItem Hidden" style="margin:0px;" item="'.$ID.'"><i class="fa fa-pencil"></i></button>
				  <button type="button" id="DeleteItem'.$ID.'" class="btn btnRed DeleteItem" style="margin:0px;" item="'.$ID.'"><i class="fa fa-trash"></i></button>
				</div>
				</form>
            </div>';
            echo $HTML;
	}
	
	public function Showpendingorderslist()
	{
		$ID = $_POST['id'];
		$Date = implode("-",array_reverse(explode("/",$_POST['date'])));
		if($ID>0)
			$Orders = $this->GetOrdersByDate($Date,$ID);
		else
			$Orders = $this->GetOrdersByDate($Date);
		// echo $this->lastQuery();
		foreach($Orders as $Order)
		{
			$Date = explode(" ",$Order['delivery_date']);
			$Date = implode("/",array_reverse(explode("-",$Date[0])));
			if($Order['delivery_man'])
			{
				$DeliveryMan = ' (<span class="">'.$Order['delivery_man'].'</span>)';
				$TextClass = 'text-red';
			}else{
				if($Order['type']=='N')
					$TextClass = 'text-green';
				else
					$TextClass = 'text-orange';
				$DeliveryMan = '';
			}
			$HTML .= '<li cli="'.$Order['order_id'].'"><span class="'.$TextClass.'"><i class="fa fa-map-marker"></i> <b>'.$Order['address'].'</b></span><span class="text-black"> - <b>Z '.$Order['zone'].'</b> - '.$Date.$DeliveryMan.'</span></li>';
		}
		if(!count($Order))
			$HTML = "No existen ordenes programadas para el ".$_POST['date'];
		
		return $HTML;
	}
	
	public function Showorderslist()
	{
		$ID = $_POST['id'];
		if($ID>0)
		{
			$this->ID = $ID;
			$Data = $this->fetchAssoc($this->Table.' a LEFT JOIN admin_user b ON (a.delivery_man_id=b.admin_id)',"a.*,CONCAT(b.first_name,' ',b.last_name) AS delivery_man",$this->TableID."=".$ID);
			$Date = ToDBDate($_POST['date']);
			$Orders = $this->GetOrdersByID($ID);
			// echo $this->lastQuery();
			// $I=0;
			$HTML = '';
			foreach($Orders as $Order)
			{
				$OrderDate = explode(" ",$Order['delivery_date']);
				$OrderDate = $OrderDate[0];
				
				if($Date==$OrderDate)
				{
					$OrderDate = explode(" ",$Order['delivery_date']);
					$OrderDate = implode("/",array_reverse(explode("-",$OrderDate[0])));
					//$DeliveryMan = ' (<span class="text-green">'.$Order['delivery_man'].'</span>)';
					$HTML .= '<li cli="'.$Order['order_id'].'"><span class="text-blue"><i class="fa fa-map-marker"></i> <b>'.$Order['address'].'</b></span><span class="text-black"> - <b>Z '.$Order['zone'].'</b> - '.$OrderDate.$DeliveryMan.'</span></li>';
				}
			}
		}
		// if($I==0)
		// 	echo "-";
		return $HTML;
	}
	
	public function Showorders()
	{
		echo $this->Showorderslist().'--///--'.$this->Showpendingorderslist();
	}
	
	public static function CheckExpired($DB)
	{
		self::InsertExpiredDeliveryOrders($DB);
		// UPDATE ORDERS AND DELIVERY
		$DB->execQuery('UPDATE','customer_delivery',"status='V'","delivery_date<'".date("Y-m-d")."' AND status IN ('A','P')");
		$DB->execQuery('UPDATE','customer_order',"status='V',delivery_status='P',delivery_id=0","delivery_date<'".date("Y-m-d")."' AND status IN ('A','P') AND type='N'");
	}
	
	public static function InsertExpiredDeliveryOrders($DB)
	{
		$Orders = $DB->fetchAssoc("customer_order a JOIN customer_delivery b ON (b.delivery_id = a.delivery_id AND b.delivery_date<'".date("Y-m-d")."' AND b.status IN ('A','P'))","a.*");
		foreach ($Orders as $Order)
		{
			if($Order['delivery_id'])
			{
				$Order['status'] = 'V';
				$Row = "NULL";
				foreach ($Order as $Col => $Field)
				{
					$Row .= ",'".$Order[$Col]."'";
				}
				$Rows .= $Rows? '),('.$Row:$Row;
				
				$Items = $DB->fetchAssoc('customer_order_item','*',"order_id = ".$Order['order_id']);
				foreach ($Items as $Item)
				{
					$Row = "NULL,".$Order['delivery_id'];
					foreach($Item as $Col => $Field)
					{
						$Row.= ",'".$Item[$Col]."'";
					}
					$ItemsRow .= $ItemsRow? '),('.$Row:$Row;
				}
				$DB->execQuery('UPDATE','relation_delivery_order',"status='I'","delivery_id=".$Order['delivery_id']."AND order_id = ".$Order['order_id']." AND status <> 'I'");
			}
		}
		// INSERT ORDER
		$DB->execQuery('INSERT','customer_delivery_order',"  ",$Rows);
		
		// INSERT ORDER PRODUCTS
		$DB->execQuery('INSERT','customer_delivery_order_item',"  ",$ItemsRows);
	}
	
	public static function InsertCancelledDeliveryOrder($DB,$Order)
	{
		if(!$Order['delivery_id']);
			$Order = $DB -> fetchAssoc('customer_order','*','order_id='.$OrderID);
		if($Order['delivery_id'])
		{
			$Order['status'] = 'C';
			$Row = "NULL";
			foreach ($Order as $Col => $Field)
			{
				$Row .= ",'".$Order[$Col]."'";
			}
			$Rows .= $Rows? '),('.$Row:$Row;
			
			$Items = $DB->fetchAssoc('customer_order_item','*',"order_id = ".$Order['order_id']);
			foreach ($Items as $Item)
			{
				$Row = "NULL,".$Order['delivery_id'];
				foreach($Item as $Col => $Field)
				{
					$Row.= ",'".$Item[$Col]."'";
				}
				$ItemsRow .= $ItemsRow? '),('.$Row:$Row;
			}
			$DB->execQuery('UPDATE','relation_delivery_order',"status='I'","delivery_id=".$Order['delivery_id']."AND order_id = ".$Order['order_id']." AND status <> 'I'");
		}
		// INSERT ORDER
		$DB->execQuery('INSERT','customer_delivery_order',"  ",$Rows);
		
		// INSERT ORDER PRODUCTS
		$DB->execQuery('INSERT','customer_delivery_order_item',"  ",$ItemsRows);
	}
}
?>
