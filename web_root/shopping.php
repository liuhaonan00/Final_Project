<?
/**
 *		$editor: Jingyi Gao u5167972$
 *      $Id: shopping.php 2014-09-23 $
 */
ini_set('max_execution_time', 300);

$appVersion = "2.0";
function printStorage(){
	global $db_conn;
    openDatabase();
	if($_REQUEST["action"] == 'add_to_list'){
		addToList($_REQUEST['name'],$_REQUEST['unit'],$_REQUEST['price']);
	}else if($_REQUEST["action"] == 'delete'){
		delete($_REQUEST['name']);
	}else if($_REQUEST["action"] == 'deleteAll'){
		session_start();
		$arr = $_SESSION['mylist'];
		unset($arr);
		$_SESSION['mylist']=$arr;
	}
	if($_REQUEST["item_name"]){
		$search = $_REQUEST["item_name"];
		$result = mysqli_query($db_conn, "SELECT a.NAME,a.SALEUNIT,a.PRICESELL,a.STOCKVOLUME FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT WHERE NAME LIKE '%".$search."%'");
	}else{
		$result = mysqli_query($db_conn, "SELECT a.NAME,a.SALEUNIT,a.PRICESELL,a.STOCKVOLUME FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT");
	}

	echo "<table class='defaulttable'>";
	echo "<tr class='panel panel-default panel-title'><th>Name</th><th>Stock volume</th><th>Price</th><th>Units</th><th></th></tr>";
	while ($row = mysqli_fetch_array($result)) {
		if($row['STOCKVOLUME']){
			echo "<form action='index.php' method = 'POST'>";
			echo "<input type='hidden' name='p' id='p' value='shopping'>";
			echo "<input type='hidden' name='action' id='action' value='add_to_list'>";
			echo "<input type='hidden' name='name' id='item_name' value='". $row['NAME']."'>";
			echo "<input type='hidden' name='price' id='item_price' value='". $row['PRICESELL']."'>";
			echo "<tr>";
			echo "<td>" . $row['NAME'] . "</td>";
			echo "<td align='right'>" . $row['STOCKVOLUME'] . "</td>";
			echo "<td align='right'>$" . number_format($row['PRICESELL'], 2, '.', '') . "/".$row['SALEUNIT']."</td>";
			echo "<td><input type = 'number' name='unit' id ='unit'/></td> ";
			echo "<td><input class='btn btn-sm btn-default' type='submit' name='add' value='Add'/></td>";
			echo "</tr>";
			echo "</form>";
		}
    }   
    echo "</table>";
	echo "</br>";
    
	closeDatabase();
}
function shoppingList(){
	session_start();
	$arr =  $_SESSION['mylist'];
	$totalPrice = 0;
	if(is_array($arr)){
		foreach($arr as $a){
			$price = $a['unit']*$a['price'];
			echo "<p>".$a['name']." X ".$a['unit']."   $" . number_format($price, 2, '.', '') . "   <a href='index.php?p=shopping&action=delete&name=".$a['name']."'>Delete</a></p>";
			$totalPrice+=$price;
		}
	echo "<p><b>Total Price:</b> $" . number_format($totalPrice, 2, '.', '')."</p>";
	echo "<a href='index.php?p=shopping&action=deleteAll'>Delete All</a>";
	}
}
function addToList($name, $unit,$price){
	session_start();
	ob_start(); 
	$name = trim($name);
	if($unit>0){
	$arr = $_SESSION['mylist'];
	if(is_array($arr)){
		if(key_exists($name,$arr)){
			$item = $arr[$name];
			$item['unit'] += $unit;
			$arr[$name] = $item;
		}else{
			$arr[$name] = array("name"=>$name,"unit"=>$unit,"price"=>$price);
		}
	}else{
		$arr[$name] = array("name"=>$name,"unit"=>$unit,"price"=>$price);
	}
	$_SESSION['mylist'] = $arr;
	}
	ob_clean();
}
function delete($name){
	session_start();
	ob_start(); 
	$name = trim($name);
	$arr = $_SESSION['mylist'];
	foreach($arr as $a){
		if($a['name']==$name){
			unset($arr[$name]);
		}
	}
	$_SESSION['mylist'] = $arr;
	ob_clean();
}

?>