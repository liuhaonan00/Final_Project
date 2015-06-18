<?php
/**
 *      $Id: login.php 2014-05-14 Jingyi Gao u5167972 $
 */
session_start();
include("header.html");
include 'db.php';
if($_GET['action'] == "logout"){
    unset($_SESSION['userid']);
    unset($_SESSION['username']);
	
	?>
	<div class="alert alert-info">
        <strong>Sign out...</strong> 
     </div>
	<script>
	location.href="../index.php?p=login";
	</script>
	<?php
    exit;
}else{
	if(!isset($_POST['submit'])){
		//exit('Access Denied!');
	}
	//conection database
	openDatabase();
	if($_GET['action'] == "login"){
		$username = htmlspecialchars($_POST['username']);
		//$password = MD5($_POST['password']);
		$password = $_POST['password'];
		//check password
		$check_query = mysqli_query($db_conn,"SELECT * FROM administrator where username='$username' and password='$password' 
		limit 1");
		echo $check_query=="";
		if($result = mysqli_fetch_array($check_query)){
			//login succeed
			$_SESSION['username'] = $username;
			$_SESSION['uid'] = $result['uid'];
			closeDatabase();
		?>
			<div class="alert alert-success">
				<strong>Logging to Food Co-op, please be patient</strong> 
			</div>
			<script>location.href="../index.php";</script><?php
		} else {
			exit('<h3>Login Failed <a href="javascript:history.back(-1);">Return</a><h3>');
		}
	}else if($_GET['action'] == "password"){
		if($_POST['password']==$_POST['repassword']){
			//$password = MD5($_POST['password']);
			$password = $_POST['password'];
			$uid = $_SESSION['uid'];
			$check_query = mysqli_query($db_conn,"UPDATE administrator SET password='$password' where uid='$uid'"); 
		}
		closeDatabase();
		?><script>location.href="../index.php";</script><?php
	}else if($_GET['action'] =="new_user"){
		$username = htmlspecialchars($_POST['newuser']);
		$password = MD5($_POST['newpw']);
		if($username&&$password){
			$check_query = mysqli_query($db_conn,"INSERT INTO administrator (USERNAME,PASSWORD,PERMISSION) VALUES ('$username','$password','0')"); 
			echo "Successful added.";

			?><script>location.href="../index.php";</script><?php
		}
		closeDatabase();
		
	}

}
include("footer.html");
?>