<?php

class CustomerDeliveryOrder extends DataBase
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
			$Data = $this->fetchAssoc($this->Table.' a INNER JOIN customer b ON (a.customer_id=b.customer_id) INNER JOIN customer_branch c ON (c.customer_id=a.customer_id) INNER JOIN customer_delivery d ON (a.delivery_id=d.delivery_id) LEFT JOIN geolocation_zone z ON (z.zone_id=c.zone_id)',"a.*,c.address,d.extra as delivery_extra,d.status as full_delivery_status,b.name,d.merluza,d.merluza_delivered,(d.merluza-d.merluza_delivered) as merluza_left,c.zone_id,z.short_name as zone",$this->TableID."=".$ID,'');
			$this->Data = $Data[0];
			$this->ID = $ID;
			$this->Data['items'] = $this->GetItems();
		}
	}
	
	public function GetItems()
	{
		if(empty($this->Items))
		{
			$Items = $this->fetchAssoc($this->Table."_item a INNER JOIN product b ON (a.product_id = b.product_id) INNER JOIN product_size s ON (b.size_id=s.size_id) LEFT JOIN currency c ON (a.currency_id=c.currency_id)","a.*,(a.price * a.quantity) AS total,b.title,s.prefix as size,c.prefix as currency",$this->TableID."=".$this->ID,'a.item_id');
			$Restrictions = $this->fetchAssoc($this->Table."_item a INNER JOIN customer_order b ON (a.order_id = b.order_id) INNER JOIN customer_delivery c ON (b.delivery_id=c.delivery_id)","a.item_id,a.product_id,a.order_id,c.delivery_id,(SUM(a.quantity)-SUM(a.quantity_delivered)) AS restriction","c.delivery_id=".$this->Data['delivery_id'],'c.delivery_id,a.order_id','a.product_id');
			// echo $this->lastQuery();
			foreach($Items as $Item)
			{
				foreach ($Restrictions as $Restriction)
				{
					if($Item['product_id'] == $Restriction['product_id'])
					{
						$Item['restriction'] =$Restriction['restriction'];
						$this->Items[] = $Item;
					}
				}
			}
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
		$Delivery = $this->GetDelivery();
		if(!$this->DeliveryMan)
		{
			$this->DeliveryMan = $this->fetchAssoc('admin_user','*','admin_id='.$Delivery['delivery_man_id']);
			$this->DeliveryMan = $this->DeliveryMan[0];
		}
		return $this->DeliveryMan;
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
public function InsertSearchList()
	{
		return '<div class="box">
    		<div class="box-body">
    			<!-- Search Filters -->
		    	<div class="SearchFilters searchFiltersHorizontal animated fadeIn Hidden" style="margin-bottom:10px;">
			        <div class="form-inline" id="SearchFieldsForm">
			        	'.insertElement('hidden','view_type','list').'
			        	'.insertElement('hidden','view_page','1').'
			        	'.insertElement('hidden','view_order_field',$this->GetOrder()).'
			        	'.insertElement('hidden','view_order_mode','asc').'
			        	'.$this->InsertSearchField().'
			          <!-- Submit Button -->
			          <button type="button" class="btn btnGreen searchButton">Buscar</button>
			          <button type="button" class="btn btnGrey" id="ClearSearchFields">Limpiar</button>
			          <!-- Decoration Arrow -->
			          <div class="arrow-right-border">
			            <div class="arrow-right-sf"></div>
			          </div>
			        </div>
			      </div>
			      <!-- /Search Filters -->
    			'.$this->InsertDefaultSearchButtons().$this->InsertSearchButtons().'
			      '.insertElement('hidden','selected_ids','').'
			      <div class="changeView Hidden">
			        <button class="ShowFilters SearchElement btn"><i class="fa fa-search"></i></button>
			        <button class="ShowList GridElement btn Hidden"><i class="fa fa-list"></i></button>
			        <button class="ShowGrid ListElement btn"><i class="fa fa-th-large"></i></button>
			      </div>
			      '.$this->InsertSearchResults().'
			    </div><!-- /.box-body -->
			    <div class="box-footer Hidden clearfix">
			      <!-- Paginator -->
			      <div class="pull-left form-inline paginationLeft">
			          <label for="RegsPerView" class="control-label">Mostrar </label>
			          '.insertElement('select','regsperview',$this->GetRegsPerView(),'form-control','',array("5"=>"5","10"=>"10","25"=>"25","50"=>"50","100"=>"100")).'
			          de <b><span id="TotalRegs">'.$this->GetTotalRegs().'</span></b>
			      </div>
			      <ul class="paginationRight pagination no-margin pull-right">
			      </ul>
			      <!-- Paginator -->
			    </div>
			  </div><!-- /.box -->
			  ';
	}

public function InsertDefaultSearchButtons(){}


public function MakeRegs($Mode="List")
	{
		$Rows	= $this->GetRegs();
		// echo $this->lastQuery();
		// echo '<pre>';
		// print_r($Rows);
		// echo '</pre>';
        $Deliverys = 0;
        $ShowDeliverys = false;
        foreach($Rows as $Row)
        {
            if($DID!=$Row['delivery_id'])
            {
                $DID=$Row['delivery_id'];
                $Deliverys++;
                if($Deliverys>1)
                    $ShowDeliverys = true;
            }
                
        }
        $Deliverys = 1;
        $Orders = 1;
		for($i=0;$i<count($Rows);$i++)
		{
		    
		    
			$Row	=	new CustomerDeliveryOrder($Rows[$i][$this->TableID]);
			// echo '<pre>';
			// print_r($Row);
			// echo '</pre>';
			$Actions	= 	'<span class="roundItemActionsGroup"><a><button type="button" class="btn btnGreen ExpandButton" title="Ver detalle" id="expand_'.$Row->ID.'"><i class="fa fa-plus"></i></button></a>';
			//$Actions	.= 	'<a href="../customer_national_order/view.php?id='.$Row->ID.'"><button type="button" class="btn btn-github" title="Ver Entrega"><i class="fa fa-eye"></i></button></a> ';
			
    			if($Row->Data['delivery_id']!=$DeliveryID)
    			{
    				
    			    $Orders = 1;
    			    if($ShowDeliverys)
					{$Counter = 'N&deg;'.$Deliverys;}
					
					
					switch ($Row->Data['full_delivery_status']) {
						case 'P':
							$DStatus = "Pendiente de Carga";
						    $DClass = 'vk';
						    $LClass = 'danger';
						    $DLink = 'config';
						    $DText = 'sign-out';
						break;
						
						case 'A':
							$DStatus = "En Proceso";
						    $DClass = 'vk';
						    $LClass = 'warning';
						    $DLink = 'view';
						    $DText = 'eye';
						break;
						
						case 'F':
							$DStatus = "Finalizado";
						    $DClass = 'vk';
						    $LClass = 'success';
						    $DLink = 'view';
						    $DText = 'eye';
						break;
					}
					
    			    
    			    if($Deliverys!=1)
    			    {
    			        $Regs	.= '<br><br>';
    			    }
    			    $DeliveryID=$Row->Data['delivery_id'];
    			    $Regs	.= '<div class="bg-blue row" style="margin:0px;padding:0px;padding-top:5px;min-height:40px;">
    			    	<div class="col-xs-3"><b><i class="fa fa-truck"></i> Reparto '.$Counter.'</b></div>
    			    	<div class="col-xs-3"><span class="label label-'.$LClass.'">'.$DStatus.'</span></div>
    			    	<div class="col-xs-3">Merluza:<span class="label label-primary">'.$Row->Data['merluza_left'].'/'.$Row->Data['merluza'].'</span></div>
    			    	<div class="col-xs-3" style=""><a href="'.$DLink.'.php?id='.$Row->Data['delivery_id'].'" class="btn btn-'.$DClass.'"><i class="fa fa-'.$DText.'"></i></a></div>
    			    </div>';
    			    // $Regs	.= '<div class="bg-blue"><b><i class="fa fa-truck"></i> Reparto '.$Counter.'</b><span class="label label-'.$DClass.'">'.$DStatus.'</span></div>';
    			  //  $Regs	.= '<div class="bg-light-blue txC">
									// <br>'.$DActions.'<br><br>
    			  //  			</div>';
    			    if($Row->Data['delivery_extra'])
    			    {
                        $Regs	.= '<div class="bg-red">'.$Row->Data['delivery_extra'].'</div>';
    			    }
    			    $Deliverys++;
    			}else{
    			    $Orders++;
    			}
			
			// if($Row->Data['status']=="P" || $Row->Data['status']=="I"){
			// 	$Actions	.= '<a class="associateElement" order="'.$Row->ID.'" id="activate_'.$Row->ID.'"><button type="button" class="btn btn-dropbox" title="Asociar a reparto"><i class="fa fa-sign-out"></i></button></a>';
			// }
			
			
				switch ($Row->Data['delivery_status']) {
					case 'P':
						$Status = "Esperando Carga";
					    $SClass = 'info';
					break;
					
					case 'A':
						$Status = "Pendiente de Entrega";
					    $SClass = 'danger';
					    if($Rows[$i]['full_delivery_status']=='A')
					    	$Actions	.= 	'<a href="new.php?id='.$Row->ID.'"><button type="button" class="btn btn-dropbox" title="Entregar Mercader&iacute;a"><i class="fa fa-dropbox"></i></button></a>';
					break;
					
					case 'F':
						$Status = "Entregado";
					    $SClass = 'primary';
					    $Actions	.= 	'<a href="print.php?id='.$Row->ID.'" target="_blank"><button type="button" class="btn btn-info" title="Imprimir Recibo"><i class="fa fa-print"></i></button></a>';
					break;
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
			 //   print_r($Item);
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
										<span class="listTextStrong"><i class="fa fa-cube"></i> '.$Item['title'].'</span>
									</div>
								</div>
								<div class="col-md-5">
									<div class="listRowInner">
										<span class="listTextStrong">Precio</span>
										<span class="listTextStrong"><span class="label label-info">'.$ItemPrice.'</span></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="listRowInner">
										<span class="listTextStrong">Cantidad</span>
										<span class="listTextStrong"><span class="label label-primary">'.$Item['quantity'].' '.$Item['size'].'</span></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="listRowInner">
										<span class="listTextStrong">Total</span>
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
						
									
					$RowBackground = $i % 2 == 0? '':' listRow2 ';
					
					$Regs	.= '<div class="row listRow'.$RowBackground.'" id="row_'.$Row->ID.'" title="una orden de compra">
									
									<div class="col-xs-1">
									    '.$Orders.'.
									</div>
									<div class="col-lg-3 col-md-5 col-sm-8 col-xs-10">
										<div class="listRowInner">
											<span class="listTextStrong"><i class="fa fa-cubes"></i> '.$Row->Data['name'].' ('.$Row->Data['address'].', '.$Row->Data['zone'].') </span>
										</div>
									</div>
									<div class="col-lg-2 col-md-3 col-sm-2 hideMobile990">
										<div class="listRowInner">
											<span class="emailTextResp"><span class="label label-'.$SClass.'">'.$Status.'</span></span>
										</div>
									</div>
									<div class="col-lg-2 col-md-3 col-sm-2 hideMobile990">
										<div class="listRowInner">
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
				// case "grid":
				// $Regs	.= '<li id="grid_'.$Row->ID.'" class="RoundItemSelect roundItemBig'.$Restrict.'" title="'.$Row->Data['customer'].'">
				// 		            <div class="flex-allCenter imgSelector">
				// 		              <div class="imgSelectorInner">
				// 		                <img src="'.$Row->GetImg().'" alt="'.$Row->Data['customer'].'" class="img-responsive">
				// 		                <div class="imgSelectorContent">
				// 		                  <div class="roundItemBigActions">
				// 		                    '.$Actions.'
				// 		                    <span class="roundItemCheckDiv"><a href="#"><button type="button" class="btn roundBtnIconGreen Hidden" name="button"><i class="fa fa-check"></i></button></a></span>
				// 		                  </div>
				// 		                </div>
				// 		              </div>
				// 		              <div class="roundItemText">
				// 		                <p><b>'.$Row->Data['customer'].'</b></p>
				// 		                <p>'.$Date.'</p>
				// 		                <p>('.$Row->Data['quantity'].')</p>
				// 		              </div>
				// 		            </div>
				// 		          </li>';
				// break;
			}
        }
        
        if(!$Regs) $Regs.= '<div class="callout callout-danger"><h4><i class="icon fa fa-info-circle"></i> No se encontraron repartos de '.$_SESSION['first_name'].' '.$_SESSION['last_name'].' para el d&iacute;a de hoy.</h4></div>';
		return $Regs;
	}
	
	protected function InsertSearchField()
	{
		return '<!-- Provider -->
          <div class="input-group">
            
            '.insertElement('text','name','','form-control','placeholder="Cliente"').'
          </div>
          <!-- Title -->
          <div class="input-group">
            
            '.insertElement('text','title','','form-control','placeholder="Art&iacute;culo"').'
          </div>
          <!-- Extra -->
          <div class="input-group">
            
            '.insertElement('text','extra','','form-control','placeholder="Info Extra"').'
          </div>
          ';
	}
	
	protected function InsertSearchButtons()
	{
// 		if($_REQUEST['status'])
// 			$Status = $_REQUEST['status'];
// 		else
// 			$Status = 'P';
// 		$HTML = '<!-- New Button -->
// 		    	<a href="new.php" title="Crear nueva orden de compra"><button type="button" class="NewElementButton btn btnGreen animated fadeIn"><i class="fa fa-plus-square"></i></button></a>
// 		    	<!-- /New Button -->';
// 		if($Status=='P' || $Status=='A') $HTML .= '<button type="button" title="Asociar a un repartidor" class="btn btnBlue animated fadeIn Hidden" id="Associate"><i class="fa fa-sign-out"></i></button>';
		return $HTML;
	}
	
	public function ConfigureSearchRequest()
	{
		$this->SetTable($this->Table.' a LEFT JOIN customer_order_item b ON (b.order_id=a.order_id) LEFT JOIN product c ON (b.product_id = c.product_id) LEFT JOIN customer d ON (d.customer_id=a.customer_id) INNER JOIN customer_delivery e ON (a.delivery_id=e.delivery_id)');
		$this->SetFields('a.order_id,a.delivery_id,a.type,a.total,a.extra,a.status,a.payment_status,a.delivery_status,d.name as customer,SUM(b.quantity) as quantity,e.status as full_delivery_status,e.merluza,e.merluza_delivered');
		$this->SetWhere("e.delivery_man_id = ".$_SESSION['admin_id']." AND a.delivery_date='".date("Y-m-d")."' AND a.company_id=".$_SESSION['company_id']);
		//$this->AddWhereString(" AND c.company_id = a.company_id");
		$this->SetGroupBy("a.".$this->TableID);
			
// 		if($_POST['name']) $this->SetWhereCondition("d.address","LIKE","%".$_POST['name']."%");
// 		if($_POST['title']) $this->SetWhereCondition("c.title","LIKE","%".$_POST['title']."%");
// 		if($_POST['extra']) $this->SetWhereCondition("a.extra","LIKE","%".$_POST['extra']."%");
		
		
		
// 		if($_REQUEST['status'])
// 		{
// 			if($_POST['status']) $this->SetWhereCondition("a.status","=", $_POST['status']);
// 			if($_GET['status']) $this->SetWhereCondition("a.status","=", $_GET['status']);	
// 		}else{
// 			$this->SetWhereCondition("a.status","=","P");
// 		}
		$this->SetOrder("e.delivery_id,a.position");
		// }
// 		if($_POST['regsperview'])
// 		{
// 			$this->SetRegsPerView($_POST['regsperview']);
// 		}
// 		if(intval($_POST['view_page'])>0)
// 			$this->SetPage($_POST['view_page']);
	}

	public function MakeList()
	{
		return $this->MakeRegs("List");
	}

	public function MakeGrid()
	{
// 		return $this->MakeRegs("Grid");
	}

	public function GetData()
	{
		return $this->Data;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////// PROCESS METHODS ///////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// public function Insert()
	// {
	// 	// ITEMS DATA
	// 	$Items = array();
	// 	for($I=1;$I<=$_POST['items'];$I++)
	// 	{
	// 		if($_POST['item_'.$I])
	// 		{
	// 			$Items[] = array('id'=>$_POST['item_'.$I],'price'=>$_POST['price_'.$I],'quantity'=>$_POST['quantity_'.$I]);
	// 		}
	// 	}
		
	// 	// Basic Data
	// 	$Type			= $_POST['order_type'];
	// 	$BranchID		= $_POST['customer'];
	// 	$CurrencyID		= $_POST['currency'];
	// 	$Extra			= $_POST['extra'];
	// 	$Total			= $_POST['total_price'];
	// 	$Status			= "P";
	// 	$Date			= implode("-",array_reverse(explode("/",$_POST['delivery_date'])));
	// 	$Customer 		= $this->fetchAssoc('customer_branch','customer_id',"branch_id=".$BranchID);
	// 	$CustomerID		= $Customer[0]['customer_id'];
		
	// 	$Insert			= $this->execQuery('insert',$this->Table,'type,branch_id,customer_id,currency_id,extra,total,delivery_date,status,creation_date,created_by,company_id',"'".$Type."',".$BranchID.",".$CustomerID.",".$CurrencyID.",'".$Extra."',".$Total.",'".$Date."','".$Status."',NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id']);
	// 	//echo $this->lastQuery();
	// 	$NewID 		= $this->GetInsertId();
	// 	$New 	= new CustomerOrder($NewID);
		
	// 	// INSERT ITEMS
	// 	foreach($Items as $Item)
	// 	{
	// 		if($Fields)
	// 			$Fields .= "),(";
	// 		$Fields .= $NewID.",".$CustomerID.",".$Item['id'].",".$Item['price'].",".$Item['quantity'].",'".$Date."',".$CurrencyID.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
	// 	}
		
	// 	$this->execQuery('insert','customer_order_item','order_id,customer_id,product_id,price,quantity,delivery_date,currency_id,creation_date,created_by,company_id',$Fields);
	// 	//echo $this->lastQuery();
	// 	if($Type=="N")
	// 	{
	// 		if($_POST['delivery_man'])
	// 			$this->Associate($NewID,$_POST['delivery_man']);
			
	// 		// INSERT MOVEMENT
	// 		Movement::InsertMovement($Total,$CustomerID,1,$this->MovementConcept.$NewID,$NewID);
	// 	}else{
	// 		// INSERT MOVEMENT
	// 		Movement::InsertMovement($Total,$CustomerID,1,'Compra en Local Nº'.$NewID,$NewID);
	// 	}
	// }
	
	// public function Update()
	// {
	// 	$ID 	= $_POST['id'];
	// 	$Edit	= new CustomerOrder($ID);
		
	// 	// ITEMS DATA
	// 	$Items = array();
	// 	for($I=1;$I<=$_POST['items'];$I++)
	// 	{
	// 		if($_POST['item_'.$I])
	// 		{
	// 			$Items[] = array('id'=>$_POST['item_'.$I],'price'=>$_POST['price_'.$I],'quantity'=>$_POST['quantity_'.$I]);
	// 		}
	// 	}
		
	// 	// Basic Data
	// 	$Type			= $_POST['order_type'];
	// 	$BranchID		= $_POST['customer'];
	// 	$CurrencyID		= $_POST['currency']?$_POST['currency']:2;
	// 	$Extra			= $_POST['extra'];
	// 	$Total			= $_POST['total_price'];
	// 	$Status			= "P";
	// 	$Date			= implode("-",array_reverse(explode("/",$_POST['delivery_date'])));
	// 	$Customer 		= $this->fetchAssoc('customer_branch','customer_id',"branch_id=".$BranchID);
	// 	$CustomerID		= $Customer[0]['customer_id'];
		
		
		
	// 	$Update		= $this->execQuery('update','customer_order',"type='".$Type."',branch_id=".$BranchID.",customer_id='".$CustomerID."',currency_id=".$CurrencyID.",extra='".$Extra."',total=".$Total.",delivery_date='".$Date."',status='".$Status."',updated_by=".$_SESSION['admin_id'],"order_id=".$ID);
	// 	//echo $this->lastQuery();
		
	// 	if($_POST['delivery_man'] && $Type=='N')
	// 		$this->Associate($ID,$_POST['delivery_man']);
		
	// 	// PROCESS AGENTS
	// 	$Agents = array();
	// 	for($i=1;$i<=$_POST['total_agents'];$i++)
	// 	{
	// 		if($_POST['agent_name_'.$i])
	// 			$Agents[] = array('name'=>ucfirst($_POST['agent_name_'.$i]),'charge'=>ucfirst($_POST['agent_charge_'.$i]),'email'=>$_POST['agent_email_'.$i],'phone'=>$_POST['agent_phone_'.$i],'extra'=>$_POST['agent_extra_'.$i]);
	// 	}
		
	// 	// DELETE OLD ITEMS
	// 	$this->execQuery('delete','customer_order_item',"order_id = ".$ID);
		
	// 	// INSERT ITEMS
	// 	foreach($Items as $Item)
	// 	{
	// 		if($Fields)
	// 			$Fields .= "),(";
	// 		$Fields .= $ID.",".$CustomerID.",".$Item['id'].",".$Item['price'].",".$Item['quantity'].",'".$Date."',".$CurrencyID.",NOW(),".$_SESSION['admin_id'].",".$_SESSION['company_id'];
	// 	}
	// 	$this->execQuery('insert','customer_order_item','order_id,customer_id,product_id,price,quantity,delivery_date,currency_id,creation_date,created_by,company_id',$Fields);
	// 	//echo $this->lastQuery();
	// 	if($Type=="N")
	// 	{
	// 		// UPDATE MOVEMENT
	// 		Movement::UpdateMovementByOrderID($Total,$CustomerID,1,$this->MovementConcept.$ID,$ID);
	// 	}else{
	// 		// UPDATE MOVEMENT
	// 		Movement::UpdateMovementByOrderID($Total,$CustomerID,1,'Compra en Local Nº'.$ID,$ID);
	// 	}
	// }
	
	// public function Activate()
	// {
	// 	$ID	= $_POST['id'];
	// 	$Order = new CustomerOrder($ID);
	// 	$Status = $Order->Data['status'] == 'I'? 'P' : 'A';
	// 	Movement::ActivateOrdersMovements($ID);
	// 	$this->execQuery('update',$this->Table,"status = '".$Status."'",$this->TableID."=".$ID);
	// }
	
	// public function Delete()
	// {
	// 	// INSERT MOVEMENT
	// 	$ID	= $_POST['id'];
	// 	Movement::DeleteOrdersMovements($ID);
	// 	$this->execQuery('update',$this->Table,"status = 'I'",$this->TableID."=".$ID);
	// }
	
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
	// 			$New = new ProviderPurchaseOrder($ID);
	// 			if($_POST['newimage']!=$New->GetImg() && file_exists($_POST['newimage']))
	// 				unlink($_POST['newimage']);
	// 			$TempDir= $this->ImgGalDir;
	// 			$Name	= "provider".intval(rand()*rand()/rand());
	// 			$Img	= new FileData($_FILES['image'],$TempDir,$Name);
	// 			echo $Img	-> BuildImage(100,100);
	// 		}else{	
	// 			if($_POST['newimage']!=$this->GetDefaultImg() && file_exists($_POST['newimage']))
	// 				unlink($_POST['newimage']);
	// 			$TempDir= $this->ImgGalDir;
	// 			$Name	= "provider".intval(rand()*rand()/rand());
	// 			$Img	= new FileData($_FILES['image'],$TempDir,$Name);
	// 			echo $Img	-> BuildImage(100,100);
	// 		}
	// 	}
	// }
	
	public function Delivery()
	{
		$OrderID = $_POST['id'];
		$CustomerID = $_POST['cid'];
		
		
		
		$OrderStatus = $this->fetchAssoc("customer_order","delivery_id,status","order_id=".$OrderID);
		$DeliveryID = $OrderStatus[0]['delivery_id'];
		$OrderStatus = $OrderStatus[0]['status'];
		$TotalAmount = 0;
		if($OrderStatus = 'A')
		{
			if(intval($_POST['cash'])>0 || intval($_POST['checks'])>0)
				$MovementStatus = "F";
			else
				$MovementStatus = "A";
			
			// DEBIT MOVEMENT
			$LastMovementID = $DebitMovementID = Movement::InsertMovement($_POST['total_price'],$CustomerID,1,$this->MovementConcept.$OrderID,$OrderID,$MovementStatus);
			
			
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
			
			
			
			//MERLUZA
			if($_POST['selected_'.$Items]=='Y')
				$MerluzaDelivered = $_POST['quantity_'.$Items];
			else
				$MerluzaDelivered = 0;
				
			//CUSTOMER BALANACE UNTIL NOW
			$MovementData = $this->fetchAssoc("movement","*","movement_id=".$LastMovementID);
			$FinalBalance = $MovementData[0]['balance'];
				
			$this->execQuery("UPDATE","customer_order","total_paid=".$TotalAmount.",balance=".$FinalBalance.",merluza_price=".$_POST['merluza_price'].",merluza_delivered=".$MerluzaDelivered.",status='F',payment_status='F',delivery_status='F',updated_by=".$_SESSION['admin_id'],"order_id=".$OrderID);
			
			$OrdersLeft = $this->numRows("customer_order","order_id","status = 'A' AND delivery_id=".$DeliveryID);
			
			if($OrdersLeft<1)
				$FinalDeliveryStatus = ",status='F'";
			
			$this->execQuery("UPDATE","customer_delivery","merluza_delivered=merluza_delivered+".$MerluzaDelivered.",total_paid=total_paid+".$TotalAmount.",updated_by=".$_SESSION['admin_id'].$FinalDeliveryStatus,"delivery_id=".$DeliveryID);
			
		}else{
			echo "403";
		}
	}
}
?>
