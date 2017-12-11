<?php
    include("../../includes/inc.main.php");
    $ID     = $_GET['id'];
    $Print  = new CustomerOrder($ID);
    $Data   = $Print->GetData();
    if($Data['type']=='N')
    {
        $Print  ->GetDeliveryMan();
        $Data   = $Print->GetData();
        $Date = DBDate($Data['delivery_date']);
    }else{
        // $Date = DBDate($Data['modification_date']);
         $Date = DBDate($Data['delivery_date']);
    }
    
    ValidateID($Data['order_id']);
    $TitleText = $Data['type']=='N'? 'Entrega de Orden para ':'Compra de Mercader&iacute;a de ';
    $Head->setTitle($TitleText.$Data['name'].' - '.$Date);
    $Head->setStyle('../../../skin/css/print.css'); // Select Inputs With Tags
    $Head->setHead();
    
    if($Data['status']=='A' && $Data['type']=='Y')
    {
        $Customer = $DB->fetchAssoc("customer","balance","customer_id=".$Data['customer_id']);
        $Data['initial_balance'] = $Customer[0]['balance'];
    }else{
        //CUSTOMER BALANACE UNTIL NOW
		$MovementData = $DB->fetchAssoc("movement","*","order_id=".$ID." AND type_id=5");
		$Data['initial_balance'] = floatval($MovementData[0]['balance'])+floatval($MovementData[0]['amount']);
    }
    $InitialBalance = floatval($Data['initial_balance'])<floatval(0.00)? "(".number_format($Data['initial_balance']*-1, 2, ',', '.').")":number_format($Data['initial_balance']*-1, 2, ',', '.');
    $Balance = floatval($Data['balance'])<floatval(0.00)? "(".number_format($Data['balance']*-1, 2, ',', '.').")":number_format($Data['balance']*-1, 2, ',', '.');
    
    $TotalOrder=0;
?>
    <div class="PrintPage">
        <div class="PageHeader">
            <h2><b>San Cipriano</b></h2>
            <hr>
            <?php if($Data['type']=='N'){ ?>
            <h3>Reparto del d&iacute;a <b><?php echo $Date ?></b> - <b><?php echo $Data['delivery_man'] ?></b></h3>
            <?php }else{ ?>
            <h3>Compra del d&iacute;a <b><?php echo $Date ?></b></h3>
            <?php } ?>
            <hr>
            <h4>Cliente: <b><?php echo $Data['name'] ?></b></h4>
        </div>  
        <div class="PageBody">
            <div class="PageCol ItemTitle MainCol">
                Producto
            </div>
            <div class="PageCol ItemPrice MainCol">
                Precio
            </div>
            <div class="PageCol ItemQuantity MainCol">
                Cantidad
            </div>
            <div class="PageCol ItemTotal MainCol">
                Total
            </div>
            <hr>
            <?php 
                foreach($Data['items'] as $Item)
                {
                    if($Item['delivered']=='Y' || ($Data['status']=='A' && $Data['type']=='Y'))
                    {
                        if($Data['status']=='A' && $Data['type']=='Y')
                        {
                            $TotalItem = $Item['price'] * $Item['quantity'];
                            $ItemQuantity = $Item['quantity']; 
                        }else{
                            $TotalItem = $Item['price'] * $Item['quantity_delivered'];
                            $ItemQuantity = $Item['quantity_delivered'];   
                        }
                            
                        $TotalOrder += $TotalItem;
            ?>
            <div class="PageCol ItemTitle">
                <?php echo $Item['title'] ?>
            </div>
            <div class="PageCol ItemPrice">
                $<?php echo number_format($Item['price'], 2, ',', '.') ?>
            </div>
            <div class="PageCol ItemQuantity">
                <?php $ItemQuantity = $Item['decimal']=='Y'? number_format($ItemQuantity, 2, ',', '.'):number_format($ItemQuantity, 0, ',', '.'); ?>
                <?php echo $ItemQuantity. " ".$Item['size'] ?>
            </div>
            <div class="PageCol ItemTotal">
                $<?php echo number_format($TotalItem, 2, ',', '.'); ?>
            </div>
            <?php }} ?>
            
            <?php if($Data['merluza_delivered']>0){ ?>
            <div class="PageCol ItemTitle">
                Merluza
            </div>
            <div class="PageCol ItemPrice">
                $<?php echo number_format($Data['merluza_price'], 2, ',', '.') ?>
            </div>
            <div class="PageCol ItemQuantity">
                <?php echo  number_format($Data['merluza_delivered'], 2, ',', '.') ?> Kgs
            </div>
            <div class="PageCol ItemTotal">
                $<?php echo number_format(($Data['merluza_price']*$Data['merluza_delivered']), 2, ',', '.') ?>
            </div>
            <?php $TotalOrder += $Data['merluza_price']*$Data['merluza_delivered'];} ?>
            
            <hr>
            <h4>Saldo Inicial: <b>$<?php echo $InitialBalance; ?></b></h4>
            <hr>
            <h4>Total a Pagar: <b>$<?php echo number_format($TotalOrder, 2, ',', '.') ?></b></h4>
            <?php if($Data['status']=='F'){ ?>
            <hr>
            <h4>Total Pagado: <b>$<?php echo number_format($Data['total_paid'], 2, ',', '.') ?></b></h4>
            <hr>
            <h4>Saldo Final: <b>$<?php echo $Balance; ?></b></h4>
            <?php } ?>
        </div>
        <div class="PageFooter txC">
            
        </div>
    </div>