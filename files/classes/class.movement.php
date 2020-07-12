<?php
    class Movement
    {
        public function Connect()
        {
            $DB = new DataBase();
            $DB->Connect();
            return $DB;
        }

        public static function GetMovementsByCustomerID($ID)
        {
            $DB = self::Connect();
            return $DB->fetchAssoc('movement a INNER JOIN movement_type b ON (a.type_id=b.type_id) LEFT JOIN payment c ON (c.payment_id=a.payment_id)','a.*,b.title AS type,b.operation,c.title AS payment','customer_id='.$ID,'a.creation_date DESC,a.type_id DESC');
        }

        public static function InsertMovement($Amount=0,$CustomerID,$TypeID,$Concept="",$OrderID=0,$Status="P",$PaymentID=0,$ParentID=0,$CheckID=0)
        {
            $DB = self::Connect();
            if(!$Concept)
            {
                $Concept = "Sin concepto. Usuario N°".$_SESSION['admin_id'];
            }

            $Customer = $DB->fetchAssoc("customer","*","customer_id=".$CustomerID);
    		    $Customer = $Customer[0];
    		    $Balance = $Customer['balance'];

        		$Type = $DB->fetchAssoc("movement_type","*","type_id=".$TypeID);
        		if($Type[0]['operation']=="C")
            {
        		    $Balance = floatval($Balance) + floatval($Amount);
        		}else{
        		    $Balance = floatval($Balance) + floatval($Amount);
            }

            $DB->execQuery('UPDATE','customer','balance='.$Balance,"customer_id=".$CustomerID);
            $DB->execQuery('INSERT','movement','customer_id,type_id,order_id,payment_id,parent_id,check_id,amount,balance,concept,status,creation_date,created_by',$CustomerID.",".$TypeID.",".$OrderID.",".$PaymentID.",".$ParentID.",".$CheckID.",".$Amount.",".$Balance.",'".$Concept."','".$Status."',NOW(),".$_SESSION['admin_id']);
            //echo $DB->lastQuery();
            return $DB->GetInsertId();
        }

        public static function UpdateMovement($MovementID,$Amount=0,$CustomerID=0,$TypeID=0,$Concept="",$OrderID=0,$PaymentID=0,$Status="")
        {
            $DB = self::Connect();
            $Data = $DB->fetchAssoc('movement','*','movement_id='.$MovementID);
            $Data = $Data[0];
            if($CustomerID>0) $Data['customer_id'] = $CustomerID;
            if($TypeID>0) $Data['type_id'] = $TypeID;
            if($OrderID>0) $Data['order_id'] = $OrderID;
            if($PaymentID>0) $Data['payment_id'] = $PaymentID;
            if($AmountD>0) $Data['amount'] = $Amount;
            if($Concept!="") $Data['concept'] = $Concept;
            if($Status!="") $Data['status'] = $Status;

            $DB->execQuery('UPDATE','movement',
                "customer_id='.$CustomerID.',
                type_id=".$TypeID.",
                order_id=".$OrderID.",
                payment_id=".$PaymentID.",
                amount=".$Amount.",
                concept='".$Concept."',
                status='".$Status."',
                updated_by=".$_SESSION['admin_id'],
                "movement_id=".$MovementID);
        }

        public static function UpdateMovementByOrderID($Amount=0,$CustomerID=0,$TypeID=0,$Concept="",$OrderID)
        {
            $DB = self::Connect();
            $Data = $DB->fetchAssoc('movement','*',"status='P' AND order_id=".$OrderID);
            if(!empty($Data))
            {
                $Data = $Data[0];
                if($CustomerID>0) $Data['customer_id'] = $CustomerID;
                if($AmountD>0) $Data['amount'] = $Amount;

                $DB->execQuery('UPDATE','movement',
                    "customer_id='.$CustomerID.',
                    amount=".$Amount.",
                    updated_by=".$_SESSION['admin_id'],
                    "movement_id=".$Data['movement_id']);
            }else{
                self::InsertMovement($Amount,$CustomerID,$TypeID,$Concept,$OrderID);
            }
        }

        public function DeleteMovement($MovementID)
        {
            $DB = self::Connect();
            $DB->execQuery('UPDATE','movement',"status='I'",'movement_id='.$MovementID);
        }

        public function DeleteOrdersMovements($ID)
        {
            $DB = self::Connect();
            $DB->execQuery('UPDATE','movement',"status='I'",'order_id='.$ID);
        }

        public function ActivateOrdersMovements($ID)
        {
            $DB = self::Connect();
            $DB->execQuery('UPDATE','movement',"status='P'",'order_id='.$ID);
        }
    }
?>
