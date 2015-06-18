<?php
error_reporting(0);
include("header.html");
include 'db.php';
	global $db_conn;
if($_POST[PRICEBUY]>0&&$_POST[PRICESELL]>0&&$_POST[STOCKVOLUME]>0){
	openDatabase();
    $sql = "UPDATE products set PRICEBUY='".$_POST[PRICEBUY]."',  PRICESELL='".$_POST[PRICESELL]."',  MEMBERDISCOUNT='".$_POST[MEMBERDISCOUNT]."',  STOCKVOLUME='".$_POST[STOCKVOLUME]."' WHERE ID = '".$_POST[ID]."'";
    $result = mysqli_query($db_conn, $sql);
}
    if($result){
	?>
		<div class="alert alert-success">
			<strong>Succeed!</strong> 
		</div>
		<script>
			location.href="../index.php?p=stocklevel";
		</script>
	<?
    }
    else{
    ?>
		<div class="alert alert-danger">
		<strong>Failed!</strong> 
		</div>
		<script>location.href="../index.php?p=stocklevel";</script>
	<?
    }
    closeDatabase();

?>