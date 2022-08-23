<html>
    <?php
        $start = microtime(true);
        $link = new mysqli('aayllonans7t9g.ccf8x84cpdcb.us-east-2.rds.amazonaws.com', 'cs466', 'cs466final', 'ebdb', '3306');
        
        $statement = $link->query("SELECT * FROM warehouse;");
        $warehouses = $statement->num_rows;
        print "<script>var warehouseMax = " . $warehouses;
        $statement = $link->query("SELECT * FROM district;");
        $districts = $statement->num_rows;
        print "; var districtMax = " . $districts;
        $statement = $link->query("SELECT * FROM customer;");
        $customers = $statement->num_rows;
        print "; var customerMax = " . $customers;
        $statement = $link->query("SELECT * FROM item;");
        $items = $statement->num_rows;
        print "; var itemMax = " . $items . ";</script>";
        //ensure autocommit is off
        $statement = $link->query("SET autocommit = 0;");


        if($_SERVER["REQUEST_METHOD"] == "POST"){  //check if form has been submitted
                    
            //initialize variables and set as empty
            $W_ID = $_POST["W_ID"];
            $D_ID = $_POST["D_ID"];
            $C_ID = $_POST["C_ID"];
            $bad = false;
            $ALL_LOCAL = 1;
            $order_line = array();

            //check for header errors
            if(empty($W_ID)) {
                $warehouse_err = "Warehouse not set";
                $bad = true;
            }
            else if($W_ID != (integer)$W_ID) {
                $warehouse_err = "Warehouse must be an integer";
                $bad = true;
            }
            else if($W_ID < 1) {
                $warehouse_err = "Warehouse must be >= 1";
                $bad = true;
            }
            else if($W_ID > $warehouses) {
                $warehouse_err = "Warehouse must be <= " . $warehouses;
                $bad = true;
            }
            if(empty($D_ID)) {
                $district_err = "District not set";
                $bad = true;
            }
            else if($D_ID != (integer)$D_ID) {
                $district_err = "District must be an integer";
                $bad = true;
            }
            else if($D_ID < 1) {
                $district_err = "District must be >= 1";
                $bad = true;
            }
            else if($D_ID > 10) {
                $district_err = "District must be <= " . $districts;
                $bad = true;
            }
            if(empty($C_ID)) {
                $customer_err = "Customer not set";
                $bad = true;
            }
            else if($C_ID != (integer)$C_ID) {
                $customer_err = "Customer must be an integer";
                $bad = true;
            }
            else if($C_ID < 1) {
                $customer_err = "Customer must be >= 1";
                $bad = true;
            }
            else if($C_ID > $customers) {
                $customer_err = "Customer must be <= " . $customers;
                $bad = true;
            }

            //set up functions to verify fields, errors are passed by reference
            function verifyOL_I_ID($OL_I_ID, &$OL_I_ID_err) {
                global $items;
                if(empty($OL_I_ID)) {
                    $OL_I_ID_err = "OL_I_ID field must be filled";
                }
                else if($OL_I_ID != (integer)$OL_I_ID) {
                    $OL_I_ID_err = "OL_I_ID field must be an integer";
                    $bad = true;
                }
                else if($OL_I_ID < 1) {
                    $OL_I_ID_err = "OL_I_ID must be >= 1";
                    $bad = true;
                }
                else if($OL_I_ID > $items) {
                    $OL_I_ID_err = "OL_I_ID must be <= " . $items;
                    $bad = true;
                }
            }

            function verifyOL_SUPPLY_W_ID($OL_SUPPLY_W_ID, &$OL_SUPPLY_W_ID_err) {
                global $warehouses;
                global $W_ID;
                if(empty($OL_SUPPLY_W_ID)) {
                    $OL_SUPPLY_W_ID_err = "OL_SUPPLY_W_ID field must be filled";
                }
                else if($OL_SUPPLY_W_ID != (integer)$OL_SUPPLY_W_ID) {
                    $OL_SUPPLY_W_ID_err = "OL_SUPPLY_W_ID field must be an integer";
                    $bad = true;
                }
                else if($OL_SUPPLY_W_ID < 1) {
                    $OL_SUPPLY_W_ID_err = "OL_SUPPLY_W_ID must be >= 1";
                    $bad = true;
                }
                else if($OL_SUPPLY_W_ID > $warehouses) {
                    $OL_SUPPLY_W_ID_err = "OL_SUPPLY_W_ID must be <= " . $warehouses;
                    $bad = true;
                }
                else if($OL_SUPPLY_W_ID != $W_ID) {
                    $ALL_LOCAL = 0;
                }
            }

            function verifyOL_QUANTITY($OL_QUANTITY, &$OL_QUANTITY_err) {
                if(empty($OL_QUANTITY)) {
                    $OL_QUANTITY_err = "OL_QUANTITY field must be filled";
                }
                else if($OL_QUANTITY != (integer)$OL_QUANTITY) {
                    $OL_QUANTITY_err = "OL_QUANTITY field must be an integer";
                    $bad = true;
                }
                else if($OL_QUANTITY < 1) {
                    $OL_QUANTITY_err = "OL_QUANTITY must be >= 1";
                    $bad = true;
                }
                else if($OL_QUANTITY > 10) {
                    $OL_QUANTITY_err = "OL_QUANTITY must be <= 10";
                    $bad = true;
                }
            }


            //verify and set fields
            verifyOL_I_ID($_POST["OL_I_ID1"], $OL_I_ID1_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID1"], $OL_SUPPLY_W_ID1_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY1"], $OL_QUANTITY1_err);

            if(!empty($_POST["OL_I_ID1"]) && !empty($_POST["OL_SUPPLY_W_ID1"]) && !empty($_POST["OL_QUANTITY1"])) {
                if(empty($OL_I_ID1_err) && empty($OL_SUPPLY_W_ID1_err) && empty($OL_QUANTITY1_err)) {
                    $OL_I_ID1 = $_POST["OL_I_ID1"];
                    $OL_SUPPLY_W_ID1 = $_POST["OL_SUPPLY_W_ID1"];
                    $OL_QUANTITY1 = $_POST["OL_QUANTITY1"];
                    array_push($order_line, array($_POST["OL_I_ID1"], $_POST["OL_SUPPLY_W_ID1"], $_POST["OL_QUANTITY1"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID1"]) && empty($_POST["OL_SUPPLY_W_ID1"]) && empty($_POST["OL_QUANTITY1"])) {
                $OL_I_ID1_err = $OL_SUPPLY_W_ID1_err = $OL_QUANTITY1_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID2"], $OL_I_ID2_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID2"], $OL_SUPPLY_W_ID2_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY2"], $OL_QUANTITY2_err);

            if(!empty($_POST["OL_I_ID2"]) && !empty($_POST["OL_SUPPLY_W_ID2"]) && !empty($_POST["OL_QUANTITY2"])) {
                if(empty($OL_I_ID2_err) && empty($OL_SUPPLY_W_ID2_err) && empty($OL_QUANTITY2_err)) {
                    $OL_I_ID2 = $_POST["OL_I_ID2"];
                    $OL_SUPPLY_W_ID2 = $_POST["OL_SUPPLY_W_ID2"];
                    $OL_QUANTITY2 = $_POST["OL_QUANTITY2"];
                    array_push($order_line, array($_POST["OL_I_ID2"], $_POST["OL_SUPPLY_W_ID2"], $_POST["OL_QUANTITY2"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID2"]) && empty($_POST["OL_SUPPLY_W_ID2"]) && empty($_POST["OL_QUANTITY2"])) {
                $OL_I_ID2_err = $OL_SUPPLY_W_ID2_err = $OL_QUANTITY2_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID3"], $OL_I_ID3_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID3"], $OL_SUPPLY_W_ID3_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY3"], $OL_QUANTITY3_err);

            if(!empty($_POST["OL_I_ID3"]) && !empty($_POST["OL_SUPPLY_W_ID3"]) && !empty($_POST["OL_QUANTITY3"])) {
                if(empty($OL_I_ID3_err) && empty($OL_SUPPLY_W_ID3_err) && empty($OL_QUANTITY3_err)) {
                    $OL_I_ID3 = $_POST["OL_I_ID3"];
                    $OL_SUPPLY_W_ID3 = $_POST["OL_SUPPLY_W_ID3"];
                    $OL_QUANTITY3 = $_POST["OL_QUANTITY3"];
                    array_push($order_line, array($_POST["OL_I_ID3"], $_POST["OL_SUPPLY_W_ID3"], $_POST["OL_QUANTITY3"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID3"]) && empty($_POST["OL_SUPPLY_W_ID3"]) && empty($_POST["OL_QUANTITY3"])) {
                $OL_I_ID3_err = $OL_SUPPLY_W_ID3_err = $OL_QUANTITY3_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID4"], $OL_I_ID4_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID4"], $OL_SUPPLY_W_ID4_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY4"], $OL_QUANTITY4_err);

            if(!empty($_POST["OL_I_ID4"]) && !empty($_POST["OL_SUPPLY_W_ID4"]) && !empty($_POST["OL_QUANTITY4"])) {
                if(empty($OL_I_ID4_err) && empty($OL_SUPPLY_W_ID4_err) && empty($OL_QUANTITY4_err)) {
                    $OL_I_ID4 = $_POST["OL_I_ID4"];
                    $OL_SUPPLY_W_ID4 = $_POST["OL_SUPPLY_W_ID4"];
                    $OL_QUANTITY4 = $_POST["OL_QUANTITY4"];
                    array_push($order_line, array($_POST["OL_I_ID4"], $_POST["OL_SUPPLY_W_ID4"], $_POST["OL_QUANTITY4"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID4"]) && empty($_POST["OL_SUPPLY_W_ID4"]) && empty($_POST["OL_QUANTITY4"])) {
                $OL_I_ID4_err = $OL_SUPPLY_W_ID4_err = $OL_QUANTITY4_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID5"], $OL_I_ID5_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID5"], $OL_SUPPLY_W_ID5_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY5"], $OL_QUANTITY5_err);

            if(!empty($_POST["OL_I_ID5"]) && !empty($_POST["OL_SUPPLY_W_ID5"]) && !empty($_POST["OL_QUANTITY5"])) {
                if(empty($OL_I_ID5_err) && empty($OL_SUPPLY_W_ID5_err) && empty($OL_QUANTITY5_err)) {
                    $OL_I_ID5 = $_POST["OL_I_ID5"];
                    $OL_SUPPLY_W_ID5 = $_POST["OL_SUPPLY_W_ID5"];
                    $OL_QUANTITY5 = $_POST["OL_QUANTITY5"];
                    array_push($order_line, array($_POST["OL_I_ID5"], $_POST["OL_SUPPLY_W_ID5"], $_POST["OL_QUANTITY5"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID5"]) && empty($_POST["OL_SUPPLY_W_ID5"]) && empty($_POST["OL_QUANTITY5"])) {
                $OL_I_ID5_err = $OL_SUPPLY_W_ID5_err = $OL_QUANTITY5_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID6"], $OL_I_ID6_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID6"], $OL_SUPPLY_W_ID6_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY6"], $OL_QUANTITY6_err);

            if(!empty($_POST["OL_I_ID6"]) && !empty($_POST["OL_SUPPLY_W_ID6"]) && !empty($_POST["OL_QUANTITY6"])) {
                if(empty($OL_I_ID6_err) && empty($OL_SUPPLY_W_ID6_err) && empty($OL_QUANTITY6_err)) {
                    $OL_I_ID6 = $_POST["OL_I_ID6"];
                    $OL_SUPPLY_W_ID6 = $_POST["OL_SUPPLY_W_ID6"];
                    $OL_QUANTITY6 = $_POST["OL_QUANTITY6"];
                    array_push($order_line, array($_POST["OL_I_ID6"], $_POST["OL_SUPPLY_W_ID6"], $_POST["OL_QUANTITY6"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID6"]) && empty($_POST["OL_SUPPLY_W_ID6"]) && empty($_POST["OL_QUANTITY6"])) {
                $OL_I_ID6_err = $OL_SUPPLY_W_ID6_err = $OL_QUANTITY6_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID7"], $OL_I_ID7_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID7"], $OL_SUPPLY_W_ID7_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY7"], $OL_QUANTITY7_err);

            if(!empty($_POST["OL_I_ID7"]) && !empty($_POST["OL_SUPPLY_W_ID7"]) && !empty($_POST["OL_QUANTITY7"])) {
                if(empty($OL_I_ID7_err) && empty($OL_SUPPLY_W_ID7_err) && empty($OL_QUANTITY7_err)) {
                    $OL_I_ID7 = $_POST["OL_I_ID7"];
                    $OL_SUPPLY_W_ID7 = $_POST["OL_SUPPLY_W_ID7"];
                    $OL_QUANTITY7 = $_POST["OL_QUANTITY7"];
                    array_push($order_line, array($_POST["OL_I_ID7"], $_POST["OL_SUPPLY_W_ID7"], $_POST["OL_QUANTITY7"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID7"]) && empty($_POST["OL_SUPPLY_W_ID7"]) && empty($_POST["OL_QUANTITY7"])) {
                $OL_I_ID7_err = $OL_SUPPLY_W_ID7_err = $OL_QUANTITY7_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID8"], $OL_I_ID8_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID8"], $OL_SUPPLY_W_ID8_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY8"], $OL_QUANTITY8_err);

            if(!empty($_POST["OL_I_ID8"]) && !empty($_POST["OL_SUPPLY_W_ID8"]) && !empty($_POST["OL_QUANTITY8"])) {
                if(empty($OL_I_ID8_err) && empty($OL_SUPPLY_W_ID8_err) && empty($OL_QUANTITY8_err)) {
                    $OL_I_ID8 = $_POST["OL_I_ID8"];
                    $OL_SUPPLY_W_ID8 = $_POST["OL_SUPPLY_W_ID8"];
                    $OL_QUANTITY8 = $_POST["OL_QUANTITY8"];
                    array_push($order_line, array($_POST["OL_I_ID8"], $_POST["OL_SUPPLY_W_ID8"], $_POST["OL_QUANTITY8"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID8"]) && empty($_POST["OL_SUPPLY_W_ID8"]) && empty($_POST["OL_QUANTITY8"])) {
                $OL_I_ID8_err = $OL_SUPPLY_W_ID8_err = $OL_QUANTITY8_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID9"], $OL_I_ID9_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID9"], $OL_SUPPLY_W_ID9_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY9"], $OL_QUANTITY9_err);

            if(!empty($_POST["OL_I_ID9"]) && !empty($_POST["OL_SUPPLY_W_ID9"]) && !empty($_POST["OL_QUANTITY9"])) {
                if(empty($OL_I_ID9_err) && empty($OL_SUPPLY_W_ID9_err) && empty($OL_QUANTITY9_err)) {
                    $OL_I_ID9 = $_POST["OL_I_ID9"];
                    $OL_SUPPLY_W_ID9 = $_POST["OL_SUPPLY_W_ID9"];
                    $OL_QUANTITY9 = $_POST["OL_QUANTITY9"];
                    array_push($order_line, array($_POST["OL_I_ID9"], $_POST["OL_SUPPLY_W_ID9"], $_POST["OL_QUANTITY9"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID9"]) && empty($_POST["OL_SUPPLY_W_ID9"]) && empty($_POST["OL_QUANTITY9"])) {
                $OL_I_ID9_err = $OL_SUPPLY_W_ID9_err = $OL_QUANTITY9_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID10"], $OL_I_ID10_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID10"], $OL_SUPPLY_W_ID10_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY10"], $OL_QUANTITY10_err);

            if(!empty($_POST["OL_I_ID10"]) && !empty($_POST["OL_SUPPLY_W_ID10"]) && !empty($_POST["OL_QUANTITY10"])) {
                if(empty($OL_I_ID10_err) && empty($OL_SUPPLY_W_ID10_err) && empty($OL_QUANTITY10_err)) {
                    $OL_I_ID10 = $_POST["OL_I_ID10"];
                    $OL_SUPPLY_W_ID10 = $_POST["OL_SUPPLY_W_ID10"];
                    $OL_QUANTITY10 = $_POST["OL_QUANTITY10"];
                    array_push($order_line, array($_POST["OL_I_ID10"], $_POST["OL_SUPPLY_W_ID10"], $_POST["OL_QUANTITY10"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID10"]) && empty($_POST["OL_SUPPLY_W_ID10"]) && empty($_POST["OL_QUANTITY10"])) {
                $OL_I_ID10_err = $OL_SUPPLY_W_ID10_err = $OL_QUANTITY10_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID11"], $OL_I_ID11_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID11"], $OL_SUPPLY_W_ID11_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY11"], $OL_QUANTITY11_err);

            if(!empty($_POST["OL_I_ID11"]) && !empty($_POST["OL_SUPPLY_W_ID11"]) && !empty($_POST["OL_QUANTITY11"])) {
                if(empty($OL_I_ID11_err) && empty($OL_SUPPLY_W_ID11_err) && empty($OL_QUANTITY11_err)) {
                    $OL_I_ID11 = $_POST["OL_I_ID11"];
                    $OL_SUPPLY_W_ID11 = $_POST["OL_SUPPLY_W_ID11"];
                    $OL_QUANTITY11 = $_POST["OL_QUANTITY11"];
                    array_push($order_line, array($_POST["OL_I_ID11"], $_POST["OL_SUPPLY_W_ID11"], $_POST["OL_QUANTITY11"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID11"]) && empty($_POST["OL_SUPPLY_W_ID11"]) && empty($_POST["OL_QUANTITY11"])) {
                $OL_I_ID11_err = $OL_SUPPLY_W_ID11_err = $OL_QUANTITY11_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID12"], $OL_I_ID12_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID12"], $OL_SUPPLY_W_ID12_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY12"], $OL_QUANTITY12_err);

            if(!empty($_POST["OL_I_ID12"]) && !empty($_POST["OL_SUPPLY_W_ID12"]) && !empty($_POST["OL_QUANTITY12"])) {
                if(empty($OL_I_ID12_err) && empty($OL_SUPPLY_W_ID12_err) && empty($OL_QUANTITY12_err)) {
                    $OL_I_ID12 = $_POST["OL_I_ID12"];
                    $OL_SUPPLY_W_ID12 = $_POST["OL_SUPPLY_W_ID12"];
                    $OL_QUANTITY12 = $_POST["OL_QUANTITY12"];
                    array_push($order_line, array($_POST["OL_I_ID12"], $_POST["OL_SUPPLY_W_ID12"], $_POST["OL_QUANTITY12"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID12"]) && empty($_POST["OL_SUPPLY_W_ID12"]) && empty($_POST["OL_QUANTITY12"])) {
                $OL_I_ID12_err = $OL_SUPPLY_W_ID12_err = $OL_QUANTITY12_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID13"], $OL_I_ID13_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID13"], $OL_SUPPLY_W_ID13_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY13"], $OL_QUANTITY13_err);

            if(!empty($_POST["OL_I_ID13"]) && !empty($_POST["OL_SUPPLY_W_ID13"]) && !empty($_POST["OL_QUANTITY13"])) {
                if(empty($OL_I_ID13_err) && empty($OL_SUPPLY_W_ID13_err) && empty($OL_QUANTITY13_err)) {
                    $OL_I_ID13 = $_POST["OL_I_ID13"];
                    $OL_SUPPLY_W_ID13 = $_POST["OL_SUPPLY_W_ID13"];
                    $OL_QUANTITY13 = $_POST["OL_QUANTITY13"];
                    array_push($order_line, array($_POST["OL_I_ID13"], $_POST["OL_SUPPLY_W_ID13"], $_POST["OL_QUANTITY13"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID13"]) && empty($_POST["OL_SUPPLY_W_ID13"]) && empty($_POST["OL_QUANTITY13"])) {
                $OL_I_ID13_err = $OL_SUPPLY_W_ID13_err = $OL_QUANTITY13_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID14"], $OL_I_ID14_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID14"], $OL_SUPPLY_W_ID14_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY14"], $OL_QUANTITY14_err);

            if(!empty($_POST["OL_I_ID14"]) && !empty($_POST["OL_SUPPLY_W_ID14"]) && !empty($_POST["OL_QUANTITY14"])) {
                if(empty($OL_I_ID14_err) && empty($OL_SUPPLY_W_ID14_err) && empty($OL_QUANTITY14_err)) {
                    $OL_I_ID14 = $_POST["OL_I_ID14"];
                    $OL_SUPPLY_W_ID14 = $_POST["OL_SUPPLY_W_ID14"];
                    $OL_QUANTITY14 = $_POST["OL_QUANTITY14"];
                    array_push($order_line, array($_POST["OL_I_ID14"], $_POST["OL_SUPPLY_W_ID14"], $_POST["OL_QUANTITY14"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID14"]) && empty($_POST["OL_SUPPLY_W_ID14"]) && empty($_POST["OL_QUANTITY14"])) {
                $OL_I_ID14_err = $OL_SUPPLY_W_ID14_err = $OL_QUANTITY14_err = "";
            }
            else {
                $bad = true;
            }


            verifyOL_I_ID($_POST["OL_I_ID15"], $OL_I_ID15_err);
            verifyOL_SUPPLY_W_ID($_POST["OL_SUPPLY_W_ID15"], $OL_SUPPLY_W_ID15_err);
            verifyOL_QUANTITY($_POST["OL_QUANTITY15"], $OL_QUANTITY15_err);

            if(!empty($_POST["OL_I_ID15"]) && !empty($_POST["OL_SUPPLY_W_ID15"]) && !empty($_POST["OL_QUANTITY15"])) {
                if(empty($OL_I_ID15_err) && empty($OL_SUPPLY_W_ID15_err) && empty($OL_QUANTITY15_err)) {
                    $OL_I_ID15 = $_POST["OL_I_ID15"];
                    $OL_SUPPLY_W_ID15 = $_POST["OL_SUPPLY_W_ID15"];
                    $OL_QUANTITY15 = $_POST["OL_QUANTITY15"];
                    array_push($order_line, array($_POST["OL_I_ID15"], $_POST["OL_SUPPLY_W_ID15"], $_POST["OL_QUANTITY15"]));
                }
                else {
                    $bad = true;
                }
            }
            else if(empty($_POST["OL_I_ID15"]) && empty($_POST["OL_SUPPLY_W_ID15"]) && empty($_POST["OL_QUANTITY15"])) {
                $OL_I_ID15_err = $OL_SUPPLY_W_ID15_err = $OL_QUANTITY15_err = "";
            }
            else {
                $bad = true;
            }


            $OL_CNT = count($order_line);


            //if correct then start transaction
            if(!$bad && $OL_CNT >= 1) {

                $statement = $link->query("START TRANSACTION;");
                $statement = $link->query("SELECT W_TAX, W_ZIP FROM warehouse WHERE W_ID = $W_ID;");
                $row = $statement->fetch_assoc();
                $W_TAX = $row["W_TAX"];

                
                $statement = $link->query("SELECT D_TAX, D_NEXT_O_ID FROM district WHERE D_ID = $D_ID AND D_W_ID = $W_ID;");
                $row = $statement->fetch_assoc();
                $D_TAX = $row["D_TAX"];
                $O_ID = $row["D_NEXT_O_ID"];

                if($link->query("UPDATE district SET D_NEXT_O_ID=D_NEXT_O_ID+1 WHERE D_ID = $D_ID AND D_W_ID = $W_ID;") === TRUE) {
                    echo "<head><h1>$O_ID</h1></head>";
                }
                $statement = $link->query("SELECT C_DISCOUNT, C_LAST, C_CREDIT FROM customer WHERE C_W_ID = $W_ID AND C_D_ID = $D_ID AND C_ID = $C_ID;");
                $row = $statement->fetch_assoc();
                $C_DISCOUNT = $row["C_DISCOUNT"];
                $C_LAST = $row["C_LAST"];
                $C_CREDIT = $row["C_CREDIT"];

                $date = date('Y-m-d H:i:s');
                $link->query("INSERT INTO `order` (O_ID, O_D_ID, O_W_ID, O_C_ID, O_ENTRY_D, O_CARRIER_ID, O_OL_CNT, O_ALL_LOCAL) VALUES ($O_ID, $D_ID, $W_ID, $C_ID, $date, NULL, $OL_CNT, $ALL_LOCAL);");

                
                $link->query("INSERT INTO `new_order` (NO_O_ID, NO_D_ID, NO_W_ID) VALUES ($O_ID, $D_ID, $W_ID);");
                
                
                //create new order header
                $order_header = array($W_ID, $D_ID, $date, $C_ID, $C_LAST, $C_CREDIT, $C_DISCOUNT, $O_ID, $OL_CNT, $W_TAX, $D_TAX);

                $TOTAL = 0;
                $S_DIST = "S_DIST_" . str_pad($D_ID, 2, '0', STR_PAD_LEFT);

                $item_details = array();

                for($line = 0; $line < $OL_CNT; $line++) {
                    $OL_I_ID = $order_line[$line][0];
                    $OL_W_ID = $order_line[$line][1];
                    $OL_QTY = $order_line[$line][2];
                    $statement = $link->query("SELECT I_PRICE, I_NAME, I_DATA FROM item WHERE I_ID = $OL_I_ID;");
                    $row = $statement->fetch_assoc();
                    $I_PRICE = $row["I_PRICE"];
                    $I_NAME = $row["I_NAME"];
                    $I_DATA = $row["I_DATA"];
                    $OL_AMOUNT = $OL_QTY * $I_PRICE;
                    $TOTAL = $TOTAL + $OL_AMOUNT;

                    if($row == null || $row == false) {
                        $statement = $link->query("ROLLBACK TRANSACTION;");
                        header("LOCATION: index.php");
                        exit();
                    }


                    $link->query("UPDATE stock SET S_QUANTITY = IF(S_QUANTITY >= 10 + $OL_QTY, S_QUANTITY - $OL_QTY, S_QUANTITY + 91 - $OL_QTY), S_YTD = S_YTD + $OL_QTY, S_ORDER_CNT = S_ORDER_CNT + 1 WHERE S_I_ID = $OL_I_ID AND S_W_ID = $OL_W_ID;");
                    if($OL_W_ID != $W_ID) {
                        $link->query("UPDATE stock SET S_REMOTE_CNT = S_REMOTE_CNT + 1 WHERE S_I_ID = $OL_I_ID AND S_W_ID = $OL_W_ID;");
                    }

                    $statement = $link->query("SELECT S_QUANTITY, $S_DIST, S_DATA FROM stock WHERE S_I_ID = $OL_I_ID AND S_W_ID = $OL_W_ID; ");
                    $row = $statement->fetch_assoc();
                    $S_QUANTITY = $row["S_QUANTITY"];
                    $S_D = $row["$S_DIST"];
                    $S_DATA = $row["S_DATA"];

                    if($row == null || $row == false) {
                        $statement = $link->query("ROLLBACK TRANSACTION;");
                        header("LOCATION: index.php");
                        exit();
                    }
                    
                    $brand = "G";
                    if(strpos($I_DATA, "ORIGINAL") && strpos($S_DATA, "ORIGINAL")) {
                        $brand = "B";
                    }

                    $link->query("INSERT INTO `order_line` (OL_O_ID, OL_D_ID, OL_W_ID, OL_NUMBER, OL_I_ID, OL_SUPPLY_W_ID, OL_DELIVERY_D, OL_QUANTITY, OL_AMOUNT, OL_DIST_INFO) 
                            VALUES ($O_ID, $D_ID, $W_ID, $line, $OL_I_ID, $OL_W_ID, NULL, $OL_QTY, $OL_AMOUNT, $S_D);");

                    
                    array_push($item_details, array($OL_W_ID, $OL_I_ID, $I_NAME, $OL_QTY, $S_QUANTITY, $brand, $I_PRICE, $OL_AMOUNT));
                }
                $TOTAL = $TOTAL * (1-$C_DISCOUNT) * (1 + $W_TAX + $D_TAX);
                $statement = $link->query("COMMIT TRANSACTION;");
                session_start();
                $_SESSION["header"] = $order_header;
                $_SESSION["order"] = $item_details;
                $_SESSION["start"] = $start;
                header("LOCATION: results.php");
                exit();
            }
        }
    ?>

<head>
        
        <style>@import url(https://fonts.googleapis.com/css?family=Roboto:400,300,600,400italic);
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
      -webkit-font-smoothing: antialiased;
      -moz-font-smoothing: antialiased;
      -o-font-smoothing: antialiased;
      font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }
    
    body {
      font-family: "Roboto", Helvetica, Arial, sans-serif;
      font-weight: 100;
      font-size: 12px;
      line-height: 30px;
      color: rgb(119, 14, 14);
      background: #181b19;
    }
    
    .container {
      max-width: fit-content;
      width: 100%;
      margin: 0 auto;
      position: relative;
    }
    
    
    #contact button[type="submit"] {
      font: 400 12px/16px "Roboto", Helvetica, Arial, sans-serif;
    }
    
    #contact {
      background: #F9F9F9;
      padding: 25px;
          padding-top: 22px !important;
      margin: 99px 0;
      box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
    }
    
    
    
    #contact textarea {
      width: 100%;
      border: 1px solid #ccc;
      background: #FFF;
      margin: 0 0 5px;
      padding: 10px;
    }
    
    
    #contact textarea:hover {
      -webkit-transition: border-color 0.3s ease-in-out;
      -moz-transition: border-color 0.3s ease-in-out;
      transition: border-color 0.3s ease-in-out;
      border: 1px solid #aaa;
    }
    
    #contact textarea {
      height: 100px;
      max-width: 100%;
      resize: none;
    }
    
    #contact button[type="submit"] {
      cursor: pointer;
      width: 100%;
      border: none;
      background: #7e3809;
      color: #FFF;
      margin: 0 0 5px;
      padding: 10px;
      font-size: 15px;
    }
    
    #contact button[type="submit"]:hover {
      background: #43A047;
      -webkit-transition: background 0.3s ease-in-out;
      -moz-transition: background 0.3s ease-in-out;
      transition: background-color 0.3s ease-in-out;
    }
    
    #contact button[type="submit"]:active {
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
    }
    
    #contact input:focus,
    #contact textarea:focus {
      outline: 0;
      border: 1px solid #aaa;
    }
    
    ::-webkit-input-placeholder {
      color: #888;
    }
    
    :-moz-placeholder {
      color: #888;
    }
    
    ::-moz-placeholder {
      color: #888;
    }
    
    :-ms-input-placeholder {
      color: #888;
    }</style>
        </head>
        <body>
            <div class="container">  
      
            <form id="contact" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h1 style="
        text-align: center;
        padding-bottom: 2px;
        text-decoration: underline;
        font-family: initial;
    ">New Order</h1><br></br>
                <table>
                    <tr>
                        <td>Select Warehouse:</td>
                        <td><input required type="number" id="W_ID" name="W_ID"><span class="invalid-feedback"><br><?php echo $warehouse_err; ?></span></td>
                    </tr>
                    <tr>
                        <td>Select District:</td>
                        <td><input required type="number" id="D_ID" name="D_ID"><span class="invalid-feedback"><br><?php echo $district_err; ?></span></td>
                    </tr>
                    <tr>
                        <td>Select Customer:</td>
                        <td><input required type="number" id="C_ID" name="C_ID"><span class="invalid-feedback"><br><?php echo $customer_err; ?></span></td>
                    </tr>
                </table>
    <br/>
                <table>
                    <tr>
                        <td>OL_I_ID</td>
                        <td>OL_SUPPLY_W_ID</td>
                        <td>OL_QUANTITY</td>
                    </tr>
                    <tr>
                        <td><input required type="number" id="OL_I_ID1" name="OL_I_ID1"><span class="invalid-feedback"><br><?php echo $OL_I_ID1_err; ?></span></td>
                        <td><input required type="number" id="OL_SUPPLY_W_ID1" name="OL_SUPPLY_W_ID1"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID1_err; ?></span></td>
                        <td><input required type="number" id="OL_QUANTITY1" name="OL_QUANTITY1"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY1_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID2" name="OL_I_ID2"><span class="invalid-feedback"><br><?php echo $OL_I_ID2_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID2" name="OL_SUPPLY_W_ID2"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID2_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY2" name="OL_QUANTITY2"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY2_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID3" name="OL_I_ID3"><span class="invalid-feedback"><br><?php echo $OL_I_ID3_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID3" name="OL_SUPPLY_W_ID3"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID3_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY3" name="OL_QUANTITY3"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY3_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID4" name="OL_I_ID4"><span class="invalid-feedback"><br><?php echo $OL_I_ID4_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID4" name="OL_SUPPLY_W_ID4"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID4_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY4" name="OL_QUANTITY4"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY4_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID5" name="OL_I_ID5"><span class="invalid-feedback"><br><?php echo $OL_I_ID5_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID5" name="OL_SUPPLY_W_ID5"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID5_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY5" name="OL_QUANTITY5"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY5_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID6" name="OL_I_ID6"><span class="invalid-feedback"><br><?php echo $OL_I_ID6_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID6" name="OL_SUPPLY_W_ID6"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID6_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY6" name="OL_QUANTITY6"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY6_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID7" name="OL_I_ID7"><span class="invalid-feedback"><br><?php echo $OL_I_ID7_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID7" name="OL_SUPPLY_W_ID7"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID7_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY7" name="OL_QUANTITY7"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY7_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID8" name="OL_I_ID8"><span class="invalid-feedback"><br><?php echo $OL_I_ID8_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID8" name="OL_SUPPLY_W_ID8"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID8_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY8" name="OL_QUANTITY8"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY8_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID9" name="OL_I_ID9"><span class="invalid-feedback"><br><?php echo $OL_I_ID9_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID9" name="OL_SUPPLY_W_ID9"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID9_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY9" name="OL_QUANTITY9"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY9_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID10" name="OL_I_ID10"><span class="invalid-feedback"><br><?php echo $OL_I_ID10_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID10" name="OL_SUPPLY_W_ID10"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID10_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY10" name="OL_QUANTITY10"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY10_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID11" name="OL_I_ID11"><span class="invalid-feedback"><br><?php echo $OL_I_ID11_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID11" name="OL_SUPPLY_W_ID11"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID11_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY11" name="OL_QUANTITY11"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY11_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID12" name="OL_I_ID12"><span class="invalid-feedback"><br><?php echo $OL_I_ID12_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID12" name="OL_SUPPLY_W_ID12"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID12_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY12" name="OL_QUANTITY12"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY12_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID13" name="OL_I_ID13"><span class="invalid-feedback"><br><?php echo $OL_I_ID13_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID13" name="OL_SUPPLY_W_ID13"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID13_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY13" name="OL_QUANTITY13"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY13_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID14" name="OL_I_ID14"><span class="invalid-feedback"><br><?php echo $OL_I_ID14_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID14" name="OL_SUPPLY_W_ID14"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID14_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY14" name="OL_QUANTITY14"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY14_err; ?></span></td>
                    </tr>
                    <tr>
                        <td><input type="number" id="OL_I_ID15" name="OL_I_ID15"><span class="invalid-feedback"><br><?php echo $OL_I_ID15_err; ?></span></td>
                        <td><input type="number" id="OL_SUPPLY_W_ID15" name="OL_SUPPLY_W_ID15"><span class="invalid-feedback"><br><?php echo $OL_SUPPLY_W_ID15_err; ?></span></td>
                        <td><input type="number" id="OL_QUANTITY15" name="OL_QUANTITY15"><span class="invalid-feedback"><br><?php echo $OL_QUANTITY15_err; ?></span></td>
                    </tr>
                </table>
                <br/>
                 <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
            </form>
            </div>
        </body>
    </html>