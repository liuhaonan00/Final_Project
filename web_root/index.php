<?
/**
 *   	Source code from Jonny
 *      $Id: index.php 2014-05-14 Jingyi Gao u5167972 $
 */

	session_start();
	// Check permission
	if(!$_SESSION['username'] && $_REQUEST["p"] !='login' && $_REQUEST["p"] !='about'&& $_REQUEST["p"] !='shopping'){
		header("Location: index.php?p=login");exit;
	}
	include 'functions.php';
    include 'MailChimp.class.php';
	include("header.html");
// BEGIN Main page switch (pf = 'page function')
$pf = $_REQUEST["p"]; 

if($pf == 'login'){
?>
<div class="login">
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="148" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="297" height="74" align="right"><img src="/image/logo.png" /></td>
      </tr>
      <tr>
        <td height="33" align="center" valign="top"><span id="tip"></span></td>
      </tr>
    </table></td>
    <td width="18" align="center"><img src="/image/bg_login.gif" /></td>
    <td colspan="2" align="center" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<form name="LoginForm" method="post" action="login.php?action=login" onSubmit="return InputCheck(this)">
      <tr>
        <td width="75" height="33" align="left" class="title">Username  </td>
        <td align="left"><input id="username" name="username" type="text" class="input" tabindex="1" /></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">Password  </td>
        <td align="left"><input id="password" name="password" type="password" class="input" tabindex="2"/></td>
      </tr>
      <tr>
        <td height="33" align="right">&nbsp;</td>
        <td align="left"><input type="submit" id="submit" name="submit" value="Login" class="btn btn-default"  tabindex="4"/></td>
      </tr>
	  </form>
    </table></td>
  </tr>
</table>
</div>
<?
}else if ($pf == 'members_to_be_approved') {

?>

      <div class="page-header">
			<h1>Members to be approved</h1>
	  </div>
       <? printMembersToBeApproved(); ?>


<?
} else if ($pf == 'print_member_approval_list') {
    
    createApprovalSheet();

} else if ($pf == 'batch_approve_members') {

    listApprovalSheets();

} else if ($pf == 'manage_approval_sheet') {

    manageApprovalSheet($_REQUEST["sheet_id"]);

} else if ($pf == 'approve_members') {

    approveMembers($_REQUEST["sheet_id"], 
                   $_REQUEST["signed_by"], 
                   $_REQUEST["date_signed"], 
                   $_REQUEST["approved_by"]);
//} else if ($pf == 'storage') {
//include 'strippedDown.html';
} else if ($pf == 'shopping') {
include 'shopping.php';

?>
		<div class='page-header'>
		<h1>Making a shopping list</h1>
		</div>
		<div class="row">
		<div class="col-lg-8">
			<div class="well">
			<p>You only need to enter the key word of the item.</p>
			</div>
			<form name="SearchForm" method="GET" action="index.php" onSubmit="return InputCheck(this)"style="float:left">
			<p>Search for item: 
			<input type="hidden" name="p" value="shopping"/>
			<input id="item_name" name="item_name" type="text" class="input" tabindex="1" />
			<input type="submit" value="Search" class="btn btn btn-default"  tabindex="2"/>
			</p>
			</form>
			<form name="Displayall" method="GET" action="index.php" onSubmit="return InputCheck(this)"style="float:left; padding-left: 5px">
			<input type="hidden" name="p" value="shopping"/>
			<input type="submit" value="Display All" class="btn btn btn-default"  />
			</form>
			</p>
			<? printStorage(); ?>
		</div>
		<div class="col-sm-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Shopping List</h3>
            </div>
            <div class="panel-body">
              <? shoppingList(); ?>
            </div>
          </div>
		</div>
	</div>
<?

} else if ($pf == 'about') {
?>
      <div class="theme-showcase">
      <div class="my-jumbo">
        <div class='page-header'>
			<h1>About</h1>
		</div>
        
        <div class="well">
			<p>This site queries the main.</p>
        </div>
       </div>
       </div>

<?
} else if ($pf == 'update_mailchimp') {
    updateMailchimp();
} else if ($pf == 'close_of_day') {
    closingFigures();
} else if ($pf == 'close_of_day_extra') {
    closingFiguresExtra();
} else if ($pf == 'cafe_sales') {
    cafeSales();
} else if ($pf == 'finance_consignment_payouts') {
    consignmentPayouts();
} else if ($pf == 'admin_list'){
	listAdmin();
} else if ($pf == 'delete_admin'){
	deleteAdmin();
} else if ($pf == 'stocklevel'){
include 'stocklevel.php';
} else if ($pf == 'update_stock'){
?>
	<div class='page-header'>
		<h1>Update Item</h1>
	</div>
    <div class="well">
       <p>To update item, remeber to select the department</p>
    </div>
<?
include 'modify.php';
} else if ($pf == 'change_password'){
?>
	<div class='page-header'>
		<h1>Change Password</h1>
	</div>
    <div class="well">
       <p>To update your password enter a new one below. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).</p>
    </div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<form name="LoginForm" method="post" action="login.php?action=password" onSubmit="return InputCheck(this)">
      <tr>
        <td width="120" height="33" align="left" class="title">New Password</td>
        <td align="left"><input id="password" name="password" type="password" class="input" tabindex="1" /></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">re-enter Password</td>
        <td align="left"><input id="repassword" name="repassword" type="password" class="input" tabindex="2"/></td>
      </tr>
      <tr>
        <td height="33" align="left">&nbsp;</td>
        <td align="left"><input type="submit" id="submit" name="submit" value="Confirm" class="btn btn btn-default"  tabindex="4"/></td>
      </tr>
	  </form>
	  </table>
<?} else if ($pf == 'add_new_user'){?>

	<div class='page-header'>
		<h1>Add New User</h1>
	</div>
    <div class="well">
       <p>To make your password stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).</p>
    </div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<form name="LoginForm" method="post" action="login.php?action=new_user" onSubmit="return InputCheck(this)">
      <tr>
        <td width="100" height="33" align="left" class="title">User Name</td>
        <td align="left"><input id="newuser" name="newuser" type="text" class="input" tabindex="1" /></td>
      </tr>
      <tr>
        <td height="33" align="left" class="title">Password</td>
        <td align="left"><input id="newpw" name="newpw" type="password" class="input" tabindex="2"/></td>
      </tr>
      <tr>
        <td height="33" align="left">&nbsp;</td>
        <td align="left"><input type="submit" id="submit" name="submit" value="Confirm" class="btn btn btn-default"  tabindex="4"/></td>
      </tr>
	  </form>
	  </table>	
<?} else {?>

 
      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="theme-showcase">
      <div class="my-jumbo">
		<?if($pf != 'sales'&&$_REQUEST["tsd"]==""){?>
		<div class='page-header'>
			<h1>The Food Co-op Shop POS Info</h1>
		</div>
		<div class="well">
			<p>This is the food co-op shop's internal website for looking at Point of Sale data. Please make sure you know what you are doing if you are using this site.</p>
		</div>
		<?}else{?>
        <div class='page-header'>
			<h1> Daily Sales </h1>
		</div>
		<div class="alert alert-warning" style="width: 45%;margin-left: auto;margin-right: auto;">
			<strong>Please enter a valid date.<strong>For example:2014-02-12
		</div>
		<form name="getSaleDigram" method="GET" action="">
		<p>Display the sales digram for: 
		<input type="date" name="tsd" class="input" data-rule-required="true" data-msg-required="Please enter a valid date" data-rule-dateISO="true" data-msg-dateISO="Please enter a valid date." tabindex="1"/>
		<input type="submit" value="Submit" class="btn btn btn-default"></form></p>
		<?}?>
        <h3><a href="index.php?tsd=<? printPreviousDaysDate($_REQUEST['tsd']); ?>"><img src="/image/left.png" /></a> &nbsp;&nbsp;<?printTotalSalesForDay($_REQUEST['tsd']);?> &nbsp;&nbsp;<a href="index.php?tsd=<? printNextDaysDate($_REQUEST['tsd']); ?>"><img src="/image/right.png" /></a></h3>
        
        <div id="sales-today-chart" style="height: 250px;"></div>

        <div id="sales-today-chart-sum" style="height: 250px;"></div>
        <!--
        <p><a class="btn btn-primary btn-lg">Learn more &raquo;</a></p>
        -->
       </div>
       </div>
<?

}
include("footer.html");
// END Main page switch
?>

