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
        if($Data['payment_status']=='F')
        {
            $PaymentDate = $DB->fetchAssoc('movement','creation_date','type_id=2 AND order_id='.$ID);
            $Date = DBDate($PaymentDate[0]['creation_date']);
        }else{
            $Date = DBDate($Data['delivery_date']);
        }
        // print_r($Data);
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

    $s = "&nbsp";
    $br = "<br>";
    for($I=0;$I<42;$I++)
    {
        $hr.="-";
        $hd.="=";
        $ha.="*";
    }
    $hr .= $br;
    $hd .= $br;
    $ha .= $br;
    for($I=0;$I<15;$I++)
        $TitleSpace .= $s;
?>
<!--<button id="SelectAllButton">SELECCIONAR TODO</button>-->
<div id="SelectText">
<?php
echo $br;
echo $ha;
echo $TitleSpace."SAN CIPRIANO".$br;
echo $ha;
// ? > <img src="../../../skin/images/body/logos/printlogo.png"></img><?php
echo $br;
echo "Compra del d&iacute;a ".$Date.$br;
// echo "Repartidor: Alejandro Romero".$br;
echo "Cliente: ".strtoupper($Data['name']).$br;
echo $br;
foreach($Data['items'] as $Item)
{
    echo $hr;
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
        $ItemQuantity = $Item['decimal']=='Y'? number_format($ItemQuantity, 2, ',',''):number_format($ItemQuantity, 0);
        $ItemQuantity = $ItemQuantity. " ".$Item['size'];
        $ItemTitle = $Item['title']." - ".$Item['brand'];
        $ItemPrice =  "$".number_format($Item['price'], 2, ',', '');
        $ItemTotal = "$".number_format($TotalItem, 2, ',', '');
        echo $ItemQuantity." x ".$ItemPrice.$br;
        if(strlen($ItemTitle.$s.$ItemTotal)>42)
        {
            echo substr($ItemTitle,0,42).$br;
            if(strlen(substr($ItemTitle,43).$s.$ItemTotal)>42)
            {
                echo substr($ItemTitle,43,84).$br;
                $Times = 42-strlen($ItemTotal)-strlen(substr($ItemTitle,84));
                $Space = $s;
                for($I=1;$I<$Times;$I++)
                    $Space .= $s;
                echo substr($ItemTitle,84).$Space.$ItemTotal.$br;
            }else{
                $Times = 42-strlen($ItemTotal)-strlen(substr($ItemTitle,43));
                $Space=$s;
                for($I=1;$I<$Times;$I++)
                    $Space .= $s;
                echo substr($ItemTitle,43).$Space.$ItemTotal.$br;
            }
        }else{
            $Times = 42-strlen($ItemTotal)-strlen($ItemTitle);
            $Space=$s;
            for($I=1;$I<$Times;$I++)
                $Space .= $s;
            echo $ItemTitle.$Space.$ItemTotal.$br;
        }
    }
}

if($Data['merluza_delivered']>0)
{
    $ItemQuantity = number_format($Data['merluza_delivered'], 2, ',', '');
    $ItemTitle = "Merluza";
    $ItemPrice = "$".number_format($Data['merluza_price'], 2, ',', '');
    $ItemTotal = "$".number_format(($Data['merluza_price']*$Data['merluza_delivered']), 2, ',', '');

    echo $ItemQuantity." x ".$ItemPrice.$br;
    $Times = 42-strlen($ItemTotal)-strlen($ItemTitle);
    $Space=$s;
    for($I=1;$I<$Times;$I++)
        $Space .= $s;
    echo $ItemTitle.$Space.$ItemTotal.$br;

    $TotalOrder += $Data['merluza_price']*$Data['merluza_delivered'];
}
echo $hd;
echo "Total: $".number_format($TotalOrder, 2, ',', '.').$br;
echo $hd;
echo $br;
echo "Saldo Inicial: ".$InitialBalance.$br;
echo "Total a Pagar: $".number_format($TotalOrder, 2, ',', '.').$br;
if($Data['status']=='F')
{
    echo "Total Pagado: $".number_format($Data['total_paid'], 2, ',', '.').$br;
    echo "Saldo Final: $".$Balance.$br;
}
echo $br;
echo $br;
?>
</div>

<?php include("../../includes/inc.foot.php");?>
<script type="text/javascript">
$(document).ready(function(){
	SelectText('SelectText');
    // $("#SelectText").trigger("click");
});
</script>
