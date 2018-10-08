<?php
    include("../../includes/inc.main.php");
    
    $DB->execQuery('DELETE','relation_product_customer_type',"status='A'");
    $Products = $DB->fetchAssoc('product',"*","status='A'");
    foreach($Products as $P)
    {
        $PID = $P['product_id'];
        if($P['variation_id']==1)
        {
            if($P['additional_percentage_wholesaler']>0)
            {
                $Field = "2,".$PID.",0,".$P['additional_percentage_wholesaler'].",NOW(),28,1";
                $Fields .= $Fields? "),(".$Field:$Field;
            }
            if($P['additional_percentage_retailer']>0)
            {
                $Field = "1,".$PID.",0,".$P['additional_percentage_retailer'].",NOW(),28,1";
                $Fields .= $Fields? "),(".$Field:$Field;
            }
        }else{
            if($P['additional_price_wholesaler']>0)
            {
                $Field = "2,".$PID.",".$P['additional_price_wholesaler'].",0,NOW(),28,1";
                $Fields .= $Fields? "),(".$Field:$Field;
            }
            if($P['additional_price_retailer']>0)
            {
                $Field = "1,".$PID.",".$P['additional_price_retailer'].",0,NOW(),28,1";
                $Fields .= $Fields? "),(".$Field:$Field;
            }
        }
    }
    if($Fields)
        $DB->execQuery('INSERT','relation_product_customer_type',"type_id,product_id,additional_amount,additional_percentage,creation_date,created_by,company_id",$Fields);
    echo $DB->lastQuery();
?>