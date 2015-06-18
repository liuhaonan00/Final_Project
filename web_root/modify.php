
<?php

 global $db_conn;
 openDatabase();
	if($_GET['id']){
		$id =  intval($_GET['id']);
		$sql="select * from products where ID = $id";
		$result=mysqli_query($db_conn,$sql);
		$row=mysqli_fetch_array($result);
	}
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<form name="ModifyForm" method="post" action="update.php" onSubmit="return InputCheck(this)">
      <tr>
        <td width="120" height="33" align="left" class="title">ID</td>
        <td align="left">
		<? if($_GET['id']){ echo  $row['ID'];?> <input type="hidden" name="ID" value="<?php echo $row['ID'];?>">
		<? }else{?><input type="text" name="ID" tabindex="1"/><? }?>
		</td>
      </tr>
      <tr>
        <td width="120" height="33" align="left" class="title">NAME</td>
        <td align="left">
		<? if($_GET['id']){ echo  $row['NAME'];?> <input type="hidden" name="NAME" value="<?php echo $row['NAME'];?>">
		<? }else{?><input type="text" name="NAME" tabindex="1"/><? }?>
		</td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">PriceBuy</td>
        <td align="left"><input type="text" name="PRICEBUY" value="<?php  echo $row['PRICEBUY'];?>" tabindex="1"/></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">PriceSell</td>
        <td align="left"><input type="text" name="PRICESELL" value="<?php  echo $row['PRICESELL'];?>" tabindex="2"/></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">MemberDiscount</td>
        <td align="left"><input type="text" name="MEMBERDISCOUNT" value="<?php  echo $row['MEMBERDISCOUNT'];?>" tabindex="3"/></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">Stockvolume</td>
        <td align="left"><input type="text" name="STOCKVOLUME"   value="<?php echo $row['STOCKVOLUME'];?>" tabindex="4"/></td>
      </tr>
      <tr>
        <td height="33" align="left">&nbsp;</td>
        <td align="left"><input type="submit" id="submit" name="submit" value="Confirm" class="btn btn btn-default"  tabindex="5"/></td>
      </tr>
	  </form>
	  </table>

<?
closeDatabase();
?>
