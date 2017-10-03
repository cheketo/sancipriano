<?php
    include("../../includes/inc.main.php");
    $ID     = $_GET['id'];
    $Print   = new CustomerDelivery($ID);
    $Print->GetOrders();
    $Print->GetTotalProducts();
    $Data   = $Print->GetData();
    ValidateID($ID);
    $Head->setTitle('Reparto del '.DBDate($Data['delivery_date']));
    $Head->setStyle('../../../skin/css/print.css'); // Select Inputs With Tags
    $Head->setHead();
    
    $Page = 0;
    $TotalPages = count($Data['orders'])+1;
    foreach($Data['orders'] as $Order)
    {
        $Page++;
        $Ord = new CustomerOrder($Order['order_id']);
        $Items = $Ord->GetItems();
?>
    <div class="PrintPage">
        <div class="PageHeader">
            <h3>Reparto del d&iacute;a <b><?php echo DBDate($Data['delivery_date']) ?></b> - <b><?php echo $Data['delivery_man'] ?></b> (Entrega incial)</h3>
            <hr>
            <h4>Cliente: <b><?php echo $Order['address'] ?></b></h4>
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
            <?php foreach($Items as $Item){ ?>
            <div class="PageCol ItemTitle">
                <?php echo $Item['title'] ?>
            </div>
            <div class="PageCol ItemPrice">
                <?php echo $Item['currency'].$Item['price'] ?>
            </div>
            <div class="PageCol ItemQuantity">
                <?php echo $Item['quantity'] ?>
            </div>
            <div class="PageCol ItemTotal">
                <?php echo $Item['currency'].$Item['total'] ?>
            </div>
            <?php } ?>
            <hr>
            <div class="PageCol ItemTitle">
                
            </div>
            <div class="PageCol ItemPrice">
                
            </div>
            <div class="PageCol ItemQuantity">
                <div class="txR">Total a pagar:&nbsp;</div>
            </div>
            <div class="PageCol ItemTotal">
                <div class="txL"><b><?php echo $Item['currency'].$Order['total'] ?></b></div>
            </div>
        </div>
        <div class="PageFooter txC">
            <!--<span class="txC ClientSign">Firma del Cliente</span>-->
            <!--P&aacute;gina <?php echo $Page.'/'.$TotalPages; ?>-->
        </div>
    </div>
<?php } ?>
    <div class="PrintPage">
        <div class="PageHeader">
            <h3>Reparto del d&iacute;a <b><?php echo DBDate($Data['delivery_date']) ?></b> - <b><?php echo $Data['delivery_man'] ?></b></h3>
            <hr>
            <h4><b>Productos Cargados</b></h4>
        </div>
        <div class="PageBody">
            <div class="PageCol TotalTitle MainCol">
                Producto
            </div>
            <div class="PageCol TotalQuantity MainCol">
                Cantidad
            </div>
            <div class="PageCol TotalTotal MainCol">
                Total
            </div>
            <hr>
            <?php 
                $Total = 0;
                foreach($Data['products'] as $Product){ 
                $Total += $Product['total'];
            ?>
            <div class="PageCol TotalTitle">
                <?php echo $Product['title'] ?>
            </div>
            <div class="PageCol TotalQuantity">
                <?php echo $Product['quantity'].' '.$Product['unit'] ?>
            </div>
            <div class="PageCol TotalTotal">
                <?php echo $Items[0]['currency'].$Product['total'] ?>
            </div>
            <?php } ?>
            <hr>
            <div class="PageCol TotalTitle">
                
            </div>
            <div class="PageCol TotalQuantity">
                <div class="txR">Total a cobrar:&nbsp;</div>
            </div>
            <div class="PageCol TotalTotal">
                <div class="txL"><b><?php echo $Items[0]['currency'].$Total ?></b></div>
            </div>
        </div>
        <div class="PageFooter txC">
            <span class="txC ClientSign">Firma del Repartidor</span>
        </div>
    </div>
    