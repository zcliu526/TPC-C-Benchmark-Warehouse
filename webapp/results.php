<?php session_start();?>
<html>
    <head>
        <style>
          body {
            background: #181b19;
          }
          table {
            border-collapse: collapse;
            width: 80%;
            margin-left: 10%;
            background-color: #262626;
            margin-top: 4%;
          }

          th, td {
            padding-left: 8px;
            padding-right: 8px;
            padding-top: 15px;
            padding-bottom: 15px;
            text-align: left;
            border: 2px solid #181b19;
            color: white;
            text-align: center;
            font-family: Arial;
          }
          h1 {        
            font-family: Arial;
            color: white; 
            margin-top: 5%;
          }
          .empho {
            border-right: 4px solid #181b19 !important;
          }
        </style>        
    </head>    
    <body>
        <h1><center>Order Result<center></h1>
        <table>
            <tbody>
                <tr>
                    <td></td>
                    <td class="empho"></td>
                    <td colspan="2">New Order</td>
                </tr>
                <tr>
                    <td >Warehouse: <?php echo str_pad($_SESSION["header"][0], 4, '0', STR_PAD_LEFT);?></td>
                    <td class="empho">District: <?php echo str_pad($_SESSION["header"][1], 2, '0', STR_PAD_LEFT);?></td>
                    <td colspan="2">Date: <?php echo $_SESSION["header"][2];?></td>
                </tr>
                <tr>
                    <td>Customer: <?php echo str_pad($_SESSION["header"][3], 4, '0', STR_PAD_LEFT);?></td>
                    <td class="empho">Name: <?php echo $_SESSION["header"][4];?></td>
                    <td>Credit: <?php echo $_SESSION["header"][5];?></td>
                    <td>Disc: <?php echo $_SESSION["header"][6];?></td>                
                </tr>
                <tr>
                    <td>Order Number: <?php echo str_pad($_SESSION["header"][7], 8, '0', STR_PAD_LEFT);?></td>
                    <td class="empho">Number of Lines: <?php echo str_pad($_SESSION["header"][8], 2, '0', STR_PAD_LEFT);?></td>
                    <td>W_Tax: <?php echo $_SESSION["header"][9];?></td>
                    <td>D_Tax: <?php echo $_SESSION["header"][10];?></td>                
                </tr>
            </tbody>
        </table>
        <br>
        <table>
            <thead>        
                <tr>
                    <th>Supp_W</th>
                    <th>Item_Id</th>
                    <th>Item_Name</th>
                    <th>Qty</th>
                    <th>Stock</th>
                    <th>B/G</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>                
                <?php
                    for($row = 0; $row < count($_SESSION["order"]); $row++) {
                        echo "\t\t\t<tr>\n";
                        echo "\t\t\t\t<td class='empho'>" . str_pad($_SESSION["order"][$row][0], 4, '0', STR_PAD_LEFT) . "</td>\n";
                        echo "\t\t\t\t<td class='empho'>" . str_pad($_SESSION["order"][$row][1], 6, '0', STR_PAD_LEFT) . "</td>\n";
                        echo "\t\t\t\t<td>" . $_SESSION["order"][$row][2] . "</td>\n";
                        echo "\t\t\t\t<td>" . str_pad($_SESSION["order"][$row][3], 2, '0', STR_PAD_LEFT) . "</td>\n";
                        echo "\t\t\t\t<td class='empho'>" . str_pad($_SESSION["order"][$row][4], 3, '0', STR_PAD_LEFT) . "</td>\n";
                        echo "\t\t\t\t<td>" . $_SESSION["order"][$row][5] . "</td>\n";
                        echo "\t\t\t\t<td>" . $_SESSION["order"][$row][6] . "</td>\n";
                        echo "\t\t\t\t<td>" . $_SESSION["order"][$row][7] . "</td>\n";
                        echo "\t\t\t</tr>\n";
                    }
                ?>

            </tbody>    
        </table>

        <table>
            <tbody>
                <tr>                    
                    <th>Execution Time</th>
                    <th>Options</th>
                </tr>
                <tr>
                    <td><?php $time_elapsed_secs = microtime(true) - $_SESSION["start"]; echo $time_elapsed_secs;?></td>                    
                    <td><a href="index.php"><button>Start New Transaction</button></a></td>
                </tr>
            </tbody>
        </table>
         
        
    </body>
</html>