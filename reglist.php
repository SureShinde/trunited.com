<!DOCTYPE html>
<html>
<head>
	
	<style type="text/css">
		table, th, td {
    	border: 1px solid black;
		}
		td { white-space: nowrap; }
	</style>
</head>
<body>

<table style="width:100%">

 <tr>
    <th>CustomerID</th>
    <th>FirstName</th>
    <th>LastName</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>ReferralCode</th>
    <th>RegDate</th>
    <th>ReferredBy</th>
    <th>ParentId</th>
    <th>November_Order</th>
    <th>December_Order</th>
    <th>OrderTotal</th>
    <th>Address</th>
    <th>City</th>
    <th>State</th>
    <th>Zip</th>                     
 </tr>

<?php
$servername = "localhost";
$username = "root";
$password = "CpEUQD3f";
$dbname = "dev_trunited";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT ci.magentocustomerid as CustomerID, ci.FirstName, ci.LastName, ci.Email, CONCAT(SUBSTR(ci.phone_number,1,3), '-', SUBSTR(ci.phone_number,4,3), '-', SUBSTR(ci.phone_number,7,4)) as Mobile, ci.membershipid as ReferralCode, DATE_SUB(c.created_at, INTERVAL 7 HOUR) as RegDate, ci.ReferredBy, ci.ParentId, ci.orderid as November_Order, ci.suborderid as December_Order, o.grand_total as OrderTotal, oa.street as Address, oa.City, oa.region as State, oa.postcode as Zip
FROM ci_customer ci
JOIN customer_entity c ON ci.magentocustomerid = c.entity_id
LEFT JOIN sales_order o ON ci.orderid = o.entity_id
LEFT JOIN sales_order_address oa ON ci.orderid = oa.parent_id AND oa.address_type = 'shipping'
WHERE ci.magentocustomerid NOT IN (200, 210)
ORDER BY ci.magentocustomerid DESC;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {

     	
    	echo "<tr>";
    	echo "<td>" . $row["CustomerID"] . "</td>";
    	echo "<td>" . $row["FirstName"] . "</td>";
    	echo "<td>" . $row["LastName"] . "</td>";
    	echo "<td>" . $row["Email"] . "</td>";
    	echo "<td>" . $row["Mobile"] . "</td>";
    	echo "<td>" . $row["ReferralCode"] . "</td>";    	
    	echo "<td>" . $row["RegDate"] . "</td>";
    	echo "<td>" . $row["ReferredBy"] . "</td>";
    	echo "<td>" . $row["ParentId"] . "</td>";
    	echo "<td>" . $row["November_Order"] . "</td>";
    	echo "<td>" . $row["December_Order"] . "</td>";
    	echo "<td>" . $row["OrderTotal"] . "</td>";
    	echo "<td>" . $row["Address"] . "</td>";
    	echo "<td>" . $row["City"] . "</td>";    	
    	echo "<td>" . $row["State"] . "</td>";    	
    	echo "<td>" . $row["Zip"] . "</td>";    	
    	echo "</tr>";


     }
} else {
     echo "0 results";
}

$conn->close();
?>  
</table>
</body>
</html>