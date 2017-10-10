<?php
    include("../../includes/inc.main.php");
    $ID     = $_GET['id'];
    $Print  = new CustomerOrder($ID);
    $Print  ->GetDeliveryMan();
    $Data   = $Print->GetData();
    ValidateID($Data['order_id']);
    $Date = DBDate($Data['delivery_date']);
    $Head->setTitle('Entrega de Orden para '.$Data['name'].' - '.$Date);
    $Head->setStyle('../../../skin/css/print.css'); // Select Inputs With Tags
    $Head->setHead();
    
    $Data['initial_balance'] = $Data['balance'] - $Data['total_paid'] + $Data['total'];
    
    for($I=0;$I<2;$I++)
    {
    $TotalOrder=0;
        
?>
    <div class="PrintPage">
        <div class="PageHeader">
            <h2>San Cipriano</h2>
            <hr>
            <h3>Reparto del d&iacute;a <b><?php echo $Date ?></b> - <b><?php echo $Data['delivery_man'] ?></b></h3>
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
                    if($Item['delivered']=='Y')
                    {
                        $TotalItem = $Item['price'] * $Item['quantity_delivered'];
                        $TotalOrder += $TotalItem;
            ?>
            <div class="PageCol ItemTitle">
                <?php echo $Item['title'] ?>
            </div>
            <div class="PageCol ItemPrice">
                $<?php echo number_format($Item['price'], 2, ',', '.') ?>
            </div>
            <div class="PageCol ItemQuantity">
                <?php $ItemQuantity = $Item['decimal']=='Y'? number_format($Item['quantity'], 2, ',', '.'):$Item['quantity']; ?>
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
            <h4>Saldo Inicial: <b>$<?php echo number_format($Data['initial_balance'], 2, ',', '.'); ?></b></h4>
            <hr>
            <h4>Total a Pagar: <b>$<?php echo number_format($TotalOrder, 2, ',', '.') ?></b></h4>
            <hr>
            <h4>Total Pagado: <b>$<?php echo number_format($Data['total_paid'], 2, ',', '.') ?></b></h4>
            <hr>
            <h4>Saldo Final: <b>$<?php echo number_format($Data['balance'], 2, ',', '.'); ?></b></h4>
            
            <!--<div class="PageCol ItemTitle">-->
                
            <!--</div>-->
            <!--<div class="PageCol ItemPrice">-->
                
            <!--</div>-->
            <!--<div class="PageCol ItemQuantity">-->
            <!--    <div class="txR">Total a pagar:&nbsp;</div>-->
            <!--</div>-->
            <!--<div class="PageCol ItemTotal">-->
            <!--    <div class="txL"><b>$<?php echo $TotalOrder ?></b></div>-->
            <!--</div>-->
        </div>
        <div class="PageFooter txC">
            <?php if($I==1){ ?>
            <span class="txC ClientSign">Firma del Cliente</span>
            <!--P&aacute;gina <?php echo $Page.'/'.$TotalPages; ?>-->
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <!--<pre>-->
    <?php //print_r($Data) ?>
    <!--</pre>-->