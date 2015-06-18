<?
/**
 *		$editor: Shuheng Liu, Jingyi Gao
 *      $Id: stocklevel.php 2014-09-23 $
 */
?>
	<div class='page-header'>
		<h1>Food Co-op Stock Level</h1>
		</div> 
		<form name="SearchForm" method="GET" action="index.php" onSubmit="return InputCheck(this)" style="float:left">
		<p>Search for: <input type="hidden" name="p" value="stocklevel"/>
		<input id="item_name" name="item_name" type="text" class="input" tabindex="1" />
		<input type="submit" value="Search" class="btn btn-sm btn-default"  tabindex="2"/></p>
		</form>
		<form name="Displayall" method="GET" action="index.php" onSubmit="return InputCheck(this)" style="float:left; padding-left: 5px">
		<input type="hidden" name="p" value="stocklevel"/>
		<input type="submit" value="Display All" class="btn btn-sm btn-default" tabindex="3"/>
		</form>
		
<?
	global $db_conn;
    openDatabase();
	if($_REQUEST["action"]){
		$id = intval($_GET['id']);
		if($_REQUEST["action"]=="delete")  delete($id);
		
	}
	if($_REQUEST["item_name"]){
		$search = $_REQUEST["item_name"];
		$result = mysqli_query($db_conn, "SELECT a.*,b.* FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT WHERE NAME LIKE '%".$search."%'");
	}else{
		$result = mysqli_query($db_conn, "SELECT a.*,b.* FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT");
	}

	echo "<table class='defaulttable'>";
	echo "<tr class='panel panel-default panel-title'><TH>ID</TH>
     <TH>NAME</TH>
     <TH>PRICEBUY</TH>
     <TH>PRICESELL</TH>
     <TH>MEMBERDISCOUNT</TH>
     <TH>STOCKVOLUME</TH>
     <TH>InCategory</TH>
     <TH>ModifyValue</TD></tr>";


	while ($row = mysqli_fetch_array($result)) {
                
                $PRODUCT = 0;
		echo "<TR>
		<TD>".$row[PRODUCT]."</TD>
                <TD>".$row[NAME]."</TD>
		<TD>".$row[PRICEBUY]."</TD>
                <TD>".$row[PRICESELL]."</TD>
                <TD>".$row[MEMBERDISCOUNT]."</TD>
                <TD>".$row[STOCKVOLUME]."</TD>
		<TD><a href=index.php?p=stocklevel&action=delete&id=".$row[PRODUCT].">delete</a></TD>
		<TD><a href=index.php?p=update_stock&id=".$row[PRODUCT].">changevolume</a></TD>
		</TR>";
  }
  echo "</TABLE>";
  closeDatabase();
  
function delete($id) {
	global $db_conn;
	$sql = "DELETE FROM products_cat WHERE product=$id";
	$result=mysqli_query($db_conn,$sql);
	if($result){
    ?>
		<div class="alert alert-success">
		<strong>Deleted!</strong> 
		</div>
		<script>location.href="../index.php?p=stocklevel";</script>
	<?
	}else{
    ?>
		<div class="alert alert-danger">
		<strong>Failed!</strong> 
		</div>
		<script>location.href="../index.php?p=stocklevel";</script>
	<?
	}
}
?>