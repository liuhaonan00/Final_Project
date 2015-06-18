    <!-- Functions php provided by Co-OP and extended for extra functionality. -->
<?

ini_set('max_execution_time', 300);

$appVersion = "2.0";
include 'db.php';
// Define a global for javascript fragments that go after the main page content
$javascript_tail = "";

function printetc($_tsd) {
	print $_tsd;
}
//For Member approval list
function printMembersToBeApproved() {
    global $db_conn;
    openDatabase();
    $result = mysqli_query($db_conn, "SELECT * FROM Customers c, Members_extra e where c.id = e.id and e.isapproved = 0");
    
    echo "<form name='memberform' method='post' action='index.php'>";
    echo "<table class='member-table'>";
    echo "<tr><th><input type='checkbox' name='selectall' id='selectall' value='sel' checked /></th><th>Name</th><th>Address</th><th>ID</th></tr>";
    
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td><input type='checkbox' name='checkbox[]' id='checkbox[]' value='" . $row['ID'] . "' checked/></td>";
        echo "<td>" . $row['NAME'] . "</td>";
        echo "<td>" . $row['ADDRESS'] . " " . $row['CITY'] ."</td>";
        echo "<td>" . $row['ID'] . "</td>";
        echo "</tr>";
        $count++;
    }    

    echo "</table>";
    echo "</br>";
    echo "<input type='hidden' name='p' id='p' value='print_member_approval_list'>";
    echo "<input type='submit' class= 'btn btn-info' name='delete' id='delete' value='Create Approval Form'/>";
    echo "</form>";

    closeDatabase();
}

function createApprovalSheet() {
    global $db_conn;
    openDatabase();

    $checkboxes = $_REQUEST["checkbox"];
    $count = count($checkboxes);
    // Create approval sheet in database
    mysqli_query($db_conn, "UPDATE MEMBERS_APPROVAL_SHEETS_NUM SET ID = LAST_INSERT_ID(ID + 1)");
    $result = mysqli_query($db_conn, "SELECT LAST_INSERT_ID() AS ID");
    $row = mysqli_fetch_assoc($result);
    $sheet_id = $row['ID'];
    $result = mysqli_query($db_conn, "INSERT INTO MEMBERS_APPROVAL_SHEETS (ID, DATECREATED, NUMMEMBERS, NOTES) VALUES ('" . $sheet_id . "', NOW(), " . $count . ", 'Pending')");
    $records = "";

    foreach ($checkboxes as $checkbox) {
        // Add member to the sheet in database
       $records .= "(UUID(), '" . $checkbox . "', '" . $sheet_id . " '), ";
    }
    $records = substr($records, 0, -2); // Take off last comma
    mysqli_query($db_conn, "INSERT INTO MEMBERS_APPROVAL_SHEETS_MEMBERS (ID, MEMBER, APPROVALSHEET) VALUES " . $records . ""); 
  
    // Now display approval sheet in HTML ready for printing
    echo "<div class='page-header'><h1>Food Co-op Membership</h1></div>";
    echo "<h3>Approval Sheet #" . $sheet_id . "</h3>";

    echo "<table class='member-table'>";
    echo "<tr><th>Name</th><th>Address</th><th>Member number</th></tr>";

    $result = mysqli_query($db_conn, "SELECT c.NAME, c.ADDRESS, c.CITY, c.ID FROM MEMBERS_APPROVAL_SHEETS_MEMBERS asm, CUSTOMERS c WHERE asm.MEMBER = c.ID AND APPROVALSHEET = '" . $sheet_id . "'");
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        // Print out table row with member information
        echo "<tr>";
        echo "<td>" . $row['NAME'] . "</td>";
        echo "<td>" . $row['ADDRESS'] . " " . $row['CITY'] ."</td>";
        echo "<td>" . $row['ID'] . "</td>";
        echo "</tr>";
        $count++;
    }

   
    echo "</table>";
    echo "<br>";
    echo "<b>We the undersigned heartily approve the applications of the " . $count . " persons listed to become members of the Food Cooperative Shop.</b>";
    echo "<h2>Signed:</h2>";
    echo "<br>";
    echo "<h2>Date:</h2>";
    echo "<br><br>";
    echo "<b>Sheet approved in POS system by:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date: </b><br><br><br>";
    echo '<button type="button" class="btn btn-lg btn-default hidden-print" id="print-button">Print</button>';
    closeDatabase();
}

function listApprovalSheets() {
    global $db_conn;
    openDatabase();
    echo "<div class='page-header'><h1>Batch approve members</h1></div>";

    $result = mysqli_query($db_conn, "SELECT ID, DATE_FORMAT(DATECREATED, '%e/%c/%Y') AS DC, NUMMEMBERS FROM MEMBERS_APPROVAL_SHEETS WHERE ISNULL(DATEAPPROVED) ORDER BY DATECREATED");
    
    echo "<table class='member-table'>";
    echo "<tr><th>Approval Sheet</th><th>Date Created</th><th>Number of Members</th><th></th></tr>";
    
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>Approval Sheet #" . $row['ID'] . "</td>";
        echo "<td>" . $row['DC'] . "</td>";
        echo "<td>" . $row['NUMMEMBERS'] . "</td>";
        echo "<td><a href='index.php?p=manage_approval_sheet&sheet_id=" . $row['ID'] . "'><button type='button' class='btn btn-lg btn-default'>Approve</button></a></td>";
        echo "</tr>";
        $count++;
    }    

    echo "</table>";

    if ($count == 0) {
        echo "<h3>No approval sheets awaiting signing at the moment.</h3>";
    }

    closeDatabase();
}

function manageApprovalSheet($sheet_id) {
    global $db_conn;
    openDatabase();
    $result = mysqli_query($db_conn, "SELECT c.NAME, c.ADDRESS, c.CITY, c.ID FROM MEMBERS_APPROVAL_SHEETS_MEMBERS asm, CUSTOMERS c WHERE asm.MEMBER = c.ID AND APPROVALSHEET = '" . $sheet_id . "'");
    echo "<div class='page-header'><h1>Confirm Approval</h1></div>";
    echo "<h3>Approval Sheet #" . $sheet_id . "</h3>";
    
 
    echo "<table class='member-table'>";
    echo "<tr><th>Name</th><th>Address</th><th>ID</th></tr>";
    
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['NAME'] . "</td>";
        echo "<td>" . $row['ADDRESS'] . " " . $row['CITY'] ."</td>";
        echo "<td>" . $row['ID'] . "</td>";
        echo "</tr>";
        $count++;
    }    

    echo "</table>";
    echo "<br>";
    echo "<form name='memberform' id='memberform' class='form-horizontal' method='post' action='index.php'>";
    echo "<input type='hidden' name='p' id='p' value='approve_members'>";
    echo "<input type='hidden' name='sheet_id' id='sheet_id' value='" . $sheet_id . "'>";
    echo "<div class='form-group'>";
    echo "<label class='col-sm-2 control-label' for='signed_by'>Signed by:</label>";
    echo "<div class='col-sm-3'><input type='text' class='form-control' name='signed_by' id='signed_by' placeholder='MC members names'
            data-rule-required='true' data-rule-min-length='2'></div>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<label class='col-sm-2 control-label' for='date_signed'>Date signed:</label>";
    echo "<div class='col-sm-3'><input type='date'class='form-control' name='date_signed' id='date_signed'
            data-rule-required='true' data-msg-required='Please enter a valid date' data-rule-dateISO='true' data-msg-dateISO='Please enter a valid date.'></div>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<label class='col-sm-2 control-label' for='approved_by'>Approved by:</label>";
    echo "<div class='col-sm-3'><input type='text' class='form-control' name='approved_by' id='approved_by' placeholder='Your name'
            data-rule-required='true' data-rule-min-length='2'></div>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<div class='col-sm-offset-2 col-sm-6'>";
    echo "<button type='submit' class='btn btn-lg btn-default' name='approve' id='approve'>Approve Members</button>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<button type='button' class='btn btn-lg btn-default' id='cancel-approve-button'>Cancel</button>"; 
    echo "</div>";
    echo "</div>";
    echo "</form>";

    closeDatabase(); 
}

function approveMembers($sheet_id, $signed_by, $date_signed, $approved_by) {
    global $db_conn;
    openDatabase();

    $result = mysqli_query($db_conn, "SELECT c.NAME, c.ADDRESS, c.CITY, c.ID FROM MEMBERS_APPROVAL_SHEETS_MEMBERS asm, CUSTOMERS c WHERE asm.MEMBER = c.ID AND APPROVALSHEET = '" . $sheet_id . "'");
    
    // Find current time so we can add a time to the date_signed
    date_default_timezone_set("Australia/ACT");
    $today = getdate();
    $h = $today[hours] + 0;
    $m = $today[minutes] + 0;
    $s = $today[seconds] + 0;
    if ($h > 0) {
       $h -= 1;
    }
    if ($m > 0) {
        $m -= 1;
    }
    if ($s > 0) {
       $s -= 1;
    }
    // Add the created time to the date_signed
    $date_signed_time = $date_signed . " " . str_pad($h, 2, '0', STR_PAD_LEFT) . ":" . str_pad($m, 2, '0', STR_PAD_LEFT) . ":" . str_pad($s, 2, '0', STR_PAD_LEFT);
    
    // Mark each member as approved
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        mysqli_query($db_conn, "UPDATE MEMBERS_EXTRA SET ISAPPROVED=b'1' WHERE  ID='" . $row['ID'] . "'");
        mysqli_query($db_conn, "INSERT INTO MEMBERS_HISTORY (ID, DATENEW, MEMBER, ACTION, NOTES) VALUES (UUID(), '" . $date_signed_time . "', '" . $row['ID'] . "', 'Approved', 'Approval sheet #" . $sheet_id . "')");
        $count++;
    } 
    
    // Mark the sheet as signed and approved
    mysqli_query($db_conn, "UPDATE MEMBERS_APPROVAL_SHEETS SET SIGNEDBY='" . $signed_by . "', DATESIGNED='" . $date_signed_time . "', APPROVEDBY='" . $approved_by . "', DATEAPPROVED=NOW(), NOTES='Approved' WHERE  ID='" . $sheet_id . "'");

    echo "<div class='alert alert-success'><strong>Well done!</strong> You successfully approved the " . $count . " members on Approval Sheet #" . $sheet_id . "</div>";

    closeDatabase();
}

function exportMembersToCSV() {

    global $db_conn;
    openDatabase();
    $result = mysqli_query($db_conn, "SELECT c.FIRSTNAME, c.LASTNAME, c.EMAIL, c.ID FROM Customers c, Members_extra e where c.ID = e.ID and e.SENDEMAILS = true");
    

    // CSV export code from Stephen Morley
    date_default_timezone_set("Australia/ACT");
    $d = date("Y_m_d");
    header('Content-type: text/csv; charset=utf-8');
    header('Content-disposition: attachment; filename="MemberExport_'.$d.'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Membership number'));
    while ($row = mysqli_fetch_array($result)) {
        fputcsv($output, array($row[EMAIL], $row[FIRSTNAME], $row[LASTNAME], $row[ID]));
    }

    closeDatabase();
}

function updateMailchimp() {
    global $db_conn;
    openDatabase();

    echo "<div class='page-header'><h1>Mailchimp update results</h1></div>";

    // Set up connection to mailchimp
    $MailChimp = new MailChimp('a41a14a898050e2789b879eff576a3f1-us4'); // REMOVE IF OPEN SOURCE!!
    $mailchimp_ListID = 'f09fcf501d'; // REMOVE IF OPEN SOURCE!!


     // Firstly do the unsubscribes...
    echo "<h2>Unsubscribes</h2>";
    echo "Unsubscribing...";
    echo "<ul>";
    $result = mysqli_query($db_conn, "SELECT EMAIL FROM mailchimp_unsubscribe");
    $countUnsubscribers = 0;
    $batchUnsubscribers = array();
    while ($row = mysqli_fetch_array($result)) {
        $el = array('email' => $row['EMAIL']);
        array_push($batchUnsubscribers, $el);
        mysqli_query($db_conn, "DELETE FROM mailchimp_unsubscribe WHERE EMAIL = '" . $row['EMAIL'] . "'");
        $countUnsubscribers++;
        echo "<li>" . $row[EMAIL] . "</li>";
    }
    echo "</ul>";

    // Add the extra stuff and perform the batch unsubscribe
    $mc_unsubscribe_result = $MailChimp->call('lists/batch-unsubscribe', array(
                'id' => $mailchimp_ListID,
                'batch' => $batchUnsubscribers,
                'delete_member' => false,
                'send_goodbye' => false,
                'send_notify' => true                               
            ));

	if($mc_unsubscribe_result[success_count]>0)
		echo "There were " . $mc_unsubscribe_result[success_count] . " successful unsubsribes<br>";
	if($mc_unsubscribe_result[error_count]>0){
		echo "There were " . $mc_unsubscribe_result[error_count] . " unsubscribe errors...";
		echo "<ul>";
		foreach ($mc_unsubscribe_result[errors] as $e) {
			echo "<li>" . $e[error] . "</li>";
		}
		echo "</ul>";
	}
     //print_r($mc_unsubscribe_result);

     

    // Secondly, batch subscribe the members in the subscribe list
    echo "<h2>Subscribes</h2>";
    echo "Subscribing...";
    echo "<ul>";
    $result = mysqli_query($db_conn, "SELECT EMAIL, FIRSTNAME, LASTNAME, MEMBERNUMBER FROM mailchimp_subscribe");
    $countSubscribers = 0;
    $batchSubscribers = array();
    while ($row = mysqli_fetch_array($result)) {
        $el = array('email' => array ('email' => $row['EMAIL']),
                    'merge_vars' => array ('FNAME' => $row['FIRSTNAME'],
                                        'LNAME' => $row['LASTNAME'],
                                        'MMERGE3' => $row['MEMBERNUMBER'])
                    );
        array_push($batchSubscribers, $el);
        mysqli_query($db_conn, "DELETE FROM mailchimp_subscribe WHERE EMAIL = '" . $row['EMAIL'] . "'");
        $countSubscribers++;
        echo "<li>" . $row[EMAIL] . "</li>";
    }
    echo "</ul>";

    // Add the extra stuff and perform the batch subscribe
    $mc_subscribe_result = $MailChimp->call('lists/batch-subscribe', array(
                'id' => $mailchimp_ListID,
                'batch' => $batchSubscribers,
                'double_optin' => false,
                'update_existing' => true,
                'repalce_interests' => false,
                'send_welcome' => false                                
            ));
	if($mc_subscribe_result[add_count]>0)
		echo "There were " . $mc_subscribe_result[add_count] . " successful subscribes<br>";
	else if($mc_subscribe_result[error_count]>0){
		echo "There were " . $mc_subscribe_result[error_count] . " subscribe errors";
		echo "<ul>";
		foreach ($mc_subscribe_result[errors] as $se) {
			echo "<li>" . $se[error] . "</li>";
		}
		echo "</ul>";
	}
     //print_r($mc_subscribe_result);
  


    closeDatabase();
}



//Prints the sales for the main register for a given day
function printTotalSalesForDay($dayStr) {
    global $db_conn;
    openDatabase();

    date_default_timezone_set("Australia/ACT");
    $thisDay = new DateTime($dayStr);
    $nextDay = clone $thisDay;
    $nextDay->add(new DateInterval('P1D'));
    $result = mysqli_query($db_conn, 
        "select SUM(p.total) AS TOTAL from payments p, receipts r, closedcash c 
                    where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                    and r.datenew > '" . $thisDay->format('Y-m-d') . "' 
                    and r.datenew < '" . $nextDay->format('Y-m-d') . "' 
                    and (p.PAYMENT = 'cash' or p.payment = 'EFT') 
                    and c.HOST = 'Haonan'"
                );

    $today = new DateTime();
    $fDay = "Sales on " . $thisDay->format('d-m-Y');
    if ((strlen($dayStr) == 0) || ($dayStr === $today->format('Y-m-d'))) {
        $fDay = "Today's sales";
    }

    while ($row = mysqli_fetch_array($result)) {
        echo $fDay . ": $" . number_format($row['TOTAL']);
    } 

    closeDatabase();
}

//Prints the cafe sales on a given day $dayStr
function printTotalCafeSalesForDay($dayStr) {
    global $db_conn;
    openDatabase();

    date_default_timezone_set("Australia/ACT");
    $thisDay = new DateTime($dayStr);
    $nextDay = clone $thisDay;
    $nextDay->add(new DateInterval('P1D'));
    $result = mysqli_query($db_conn, 
        "select SUM(p.total) AS TOTAL from payments p, receipts r, closedcash c 
                    where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                    and r.datenew > '" . $thisDay->format('Y-m-d') . "' 
                    and r.datenew < '" . $nextDay->format('Y-m-d') . "' 
                    and (p.PAYMENT = 'cash' or p.payment = 'EFT') 
                    and c.HOST = 'Cafe'"
                );

    $today = new DateTime();
    $fDay = "Sales on " . $thisDay->format('d-m-Y');
    if ((strlen($dayStr) == 0) || ($dayStr === $today->format('Y-m-d'))) {
        $fDay = "Today's sales";
    }

    while ($row = mysqli_fetch_array($result)) {
        echo $fDay . ": $" . number_format($row['TOTAL']);
    } 

    closeDatabase();
}

function printSalesTodayJSON() {
    date_default_timezone_set("Australia/ACT");
    $today = new DateTime();
    printSalesForDayJSON($today);
}

function printSalesForDayJSON($dayStr) {
    global $db_conn;
    openDatabase();

    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $today = clone $d;
    $tomorrow = $d->add(new DateInterval('P1D'));

    $result = mysqli_query(
                $db_conn, 
                "select p.total,  r.datenew from payments p, receipts r, closedcash c 
                where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                and r.datenew > '" . $today->format('Y-m-d') . "' 
                and r.datenew < '" . $tomorrow->format('Y-m-d') . "' 
                and (p.PAYMENT = 'cash' or p.payment = 'EFT') 
                and c.HOST = 'Haonan'"
                );
    echo "[";
    $count = 0;
    $sum = 0;
    while ($row = mysqli_fetch_array($result)) {
        $sum += $row['total'];
        echo "{ time: '" . $row['datenew'] . "', amount: '" . $row['total'] . "', sum: '" . $sum . "' },";
        $count++;
    }   
    echo "]";
   

    closeDatabase(); 
}

//Graphing for Bar Graph to Search a list of products
function find($str,$toCount){

	
	
	$pieces = explode(",",$str);
	$leng = sizeof($pieces);
	$x = 0;
	while($x<$leng){
		if($pieces[$x]!=""){
			$pieces[$x]=ltrim($pieces[$x]);
		}else{
			$pieces[$x]="null";
		}

		$x = $x+1;
	}

	global $db_conn;
    openDatabase();

	echo "[";
	$c = 0;
	if($toCount=="true"){
		while($c<$leng){
			$result = mysqli_query($db_conn,"SELECT COUNT(a.STOCKVOLUME) FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT WHERE NAME LIKE '%".$pieces[$c]."%'");
			$row = mysqli_fetch_array($result);
			$h = $row['COUNT(a.STOCKVOLUME)'];
			if(empty($h)){$h=0;};
			echo "{ Product: '".$pieces[$c]."', value: ".$h."},";
			$c=$c+1;
		}
	}else{
		while($c<$leng){
			$result = mysqli_query($db_conn,"SELECT SUM(a.STOCKVOLUME) FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT WHERE NAME LIKE '%".$pieces[$c]."%'");
			$row = mysqli_fetch_array($result);
			$h = $row['SUM(a.STOCKVOLUME)'];
			if(empty($h)){$h=0;};
			echo "{ Product: '".$pieces[$c]."', value: ".$h."},";
			$c=$c+1;
		}
	}
	
	
	
	echo "]";

	closeDatabase(); 	
	

}

function printPie($str){

	
	
	$pieces = explode(",",$str);
	$leng = sizeof($pieces);
	$x = 0;
	while($x<$leng){
		$pieces[$x]=ltrim($pieces[$x]);
		if($pieces[$x]==""){
			$pieces[$x]="null";
		}

		$x = $x+1;
	}

	global $db_conn;
    openDatabase();

	echo "[";
	$c = 0;

		while($c<$leng){
			$result = mysqli_query($db_conn,"SELECT SUM(a.STOCKVOLUME) FROM PRODUCTS_CAT b LEFT JOIN PRODUCTS a ON a.ID = b.PRODUCT WHERE NAME LIKE '%".$pieces[$c]."%'");

			$row = mysqli_fetch_array($result);
			$h = $row['SUM(a.STOCKVOLUME)'];
			if(empty($h)){$h=0;};
			echo "{ label: '".$pieces[$c]."', value: ".$h."},";
			$c=$c+1;
	}
	
	
	
	echo "]";

	closeDatabase(); 
}


//Bar Graph MySQL connector
function printGSTvsFRE(){
	global $db_conn;
	openDatabase();
	$result = mysqli_query($db_conn,"SELECT TAXCODE, COUNT(*) FROM myob_stock GROUP BY TAXCODE");
	$row = mysqli_fetch_array($result);
	$fre = $row['COUNT(*)'];
	$row = mysqli_fetch_array($result);
	$gst = $row['COUNT(*)'];
	closeDatabase();
	echo "[";
	echo "{ label: 'Free', value: ".$fre."}, { label: 'GST', value: ".$gst." }]";

}

//Sales for the day
function getCafeSalesForDayJSON($dayStr) {
    global $db_conn;
    openDatabase();
    $out = "";

    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $today = clone $d;
    $tomorrow = $d->add(new DateInterval('P1D'));

    $result = mysqli_query(
                $db_conn, 
                "select p.total,  r.datenew from payments p, receipts r, closedcash c 
                where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                and r.datenew > '" . $today->format('Y-m-d') . "' 
                and r.datenew < '" . $tomorrow->format('Y-m-d') . "' 
                and (p.PAYMENT = 'cash' or p.payment = 'EFT') 
                and c.HOST = 'Cafe'"
                );
    $out .= "[";
    $count = 0;
    $sum = 0;
    while ($row = mysqli_fetch_array($result)) {
        $sum += $row['total'];
        $out .= "{ time: '" . $row['datenew'] . "', amount: '" . $row['total'] . "', sum: '" . $sum . "' },";
        $count++;
    }   
    $out .= "]";
   

    closeDatabase(); 

    return $out;
}

//Consignment Sales for A Month
function getConsignmentSalesForMonthJSON($dayStr) {
    global $db_conn;
    openDatabase();
    $out = "";

    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $firstOfMonth = clone $d;
    $firstOfMonth->modify('first day of this month');
    $lastOfMonth = clone $d;
    $lastOfMonth->modify('last day of this month');

    $result = mysqli_query(
                $db_conn, 
                "select p.total,  r.datenew from payments p, receipts r, closedcash c 
                where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                and r.datenew > '" . $firstOfMonth->format('Y-m-d') . "' 
                and r.datenew < '" . $lastOfMonth->format('Y-m-d') . "' 
                and (p.PAYMENT = 'consignmentpayout')"
                );
    $out .= "[";
    $count = 0;
    $sum = 0;
    while ($row = mysqli_fetch_array($result)) {
        $sum += $row['total'];
        $out .= "{ time: '" . $row['datenew'] . "', amount: '" . $row['total'] . "', sum: '" . $sum . "' },";
        $count++;
    }   
    $out .= "]";
   
    

    closeDatabase(); 

    return $out;
}

//Returns the day before daystr
function printPreviousDaysDate($dayStr) {
    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $d->sub(new DateInterval("P1D"));
    echo $d->format('Y-m-d');
}

//Returns the day after daystr
function printNextDaysDate($dayStr) {
    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $d->add(new DateInterval("P1D"));
    echo $d->format('Y-m-d');
}

function printLabelForCurrentMonth($dayStr) {
    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    echo $d->format('F Y');    
}

function printPreviousMonthsDate($dayStr) {
    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $d->modify('first day of previous month');
    echo $d->format('Y-m-d');
}

function printNextMonthsDate($dayStr) {
    date_default_timezone_set("Australia/ACT");
    $d = new DateTime($dayStr);
    $d->modify('first day of next month');
    echo $d->format('Y-m-d');
}


//Prints Monthly Figures
function printClosingFiguresForMonth($dayStr) {
    global $db_conn;
    openDatabase();

    date_default_timezone_set("Australia/ACT");
    $firstDay = new DateTime($dayStr);
    $firstDay->modify('first day of this month');
    $lastDay = new DateTime($dayStr);
    $lastDay->modify('first day of next month');
    echo " between " . $firstDay->format('Y-m-d') . " and " . $lastDay->format('Y-m-d');

    $d1 = clone $firstDay;
    $d2 = clone $firstDay;
    $d2->modify('+1 day');
 
    echo "<table class='finance-table'>";
    echo "<tr><th>Day</th><th>Cash</th><th>EFT</th><th>Cheque</th><th>Voucher</th><th>Consign</th><th>Petty Cash</th><th>Cash Refund</th></tr>";

    while ($d1 < $lastDay) {
        $result = mysqli_query(
                    $db_conn, 
                    "select sum(p.total) as t,  p.PAYMENT as pt from payments p, receipts r, closedcash c 
                    where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                    and r.datenew > '" . $d1->format('Y-m-d') . "' 
                    and r.datenew < '" . $d2->format('Y-m-d') . "' 
                    and (c.HOST = 'Haonan' or c.HOST = 'Cafe') group by pt"
                    );

        $pCash = 0;
        $pEFT = 0;
        $pCheque = 0;
        $pVoucher = 0;
        $pConsign = 0;
        $pPettyCash = 0;
        $pCashRefund = 0;

        while ($row = mysqli_fetch_array($result)) {
            if ($row['pt'] == 'cash') {
                $pCash = $row['t'];
            } else if ($row['pt'] == 'EFT') {
                $pEFT = $row['t'];
            } else if ($row['pt'] == 'cheque') {
                $pCheque = $row['t'];
            } else if ($row['pt'] == 'voucher') {
                $pVoucher = $row['t'];
            } else if ($row['pt'] == 'consignmentpayout') {
                $pConsign = $row['t'];
            } else if ($row['pt'] == 'pettycashout') {
                $pPettyCash = $row['t'];
            } else if ($row['pt'] == 'cashrefund') {
                $pCashRefund = $row['t'];
            }

        }
        $pCash += $pConsign + $pPettyCash + $pCashRefund;

        echo "<tr>";
        echo "<td>" . $d1->format('Y-m-d') . "</td>";
        echo "<td>" . number_format($pCash, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pEFT, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pCheque, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pVoucher, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pConsign, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pPettyCash, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pCashRefund, 2, '.', '') . "</td>";
        echo "</tr>";

        $d1->modify('+1 day');
        $d2->modify('+1 day');
    }
    echo "</table>";

    closeDatabase();

}

//Prints Cafe Figures
function printCafeFiguresForMonth($dayStr) {
    global $db_conn;
    openDatabase();

    date_default_timezone_set("Australia/ACT");
    $firstDay = new DateTime($dayStr);
    $firstDay->modify('first day of this month');
    $lastDay = new DateTime($dayStr);
    $lastDay->modify('first day of next month');
    echo " between " . $firstDay->format('Y-m-d') . " and " . $lastDay->format('Y-m-d');

    $d1 = clone $firstDay;
    $d2 = clone $firstDay;
    $d2->modify('+1 day');
 
    echo "<table class='finance-table'>";
    echo "<tr>
            <th>Day</th>
            <th>Cash</th>
            <th>EFT</th>
            <th>Cheque</th>
            <th>Total</th>
            <th>Voucher</th>
            <th>Coffees</th>
            <th>Cafe</th>
            <th>Lunches</th>
            <th>Lunch</th>
            <th>Soups</th>
            <th>Soup</th>
        </tr>";
   
    $pTCash = 0;
    $pTEFT = 0;
    $pTCheque = 0;
    $pTVoucher = 0;
    $pTTotalMoney = 0;
    $pTCoffeesSold = 0;
    $pTCafeSales = 0;
    $pTLunchesSold = 0;
    $pTLunchSales = 0;
    $pTSoupsSold = 0;
    $pTSoupSales = 0;

    while ($d1 < $lastDay) {
        $result = mysqli_query(
                    $db_conn, 
                    "select sum(p.total) as t,  p.PAYMENT as pt from payments p, receipts r, closedcash c 
                    where p.RECEIPT = r.ID and c.MONEY = r.MONEY 
                    and r.datenew > '" . $d1->format('Y-m-d') . "' 
                    and r.datenew < '" . $d2->format('Y-m-d') . "' 
                    and c.HOST = 'Haonan' group by pt"
                    );

        $pCash = 0; 
        $pEFT = 0; 
        $pCheque = 0; 
        $pVoucher = 0; 
        $pTotalMoney = 0; 

        while ($row = mysqli_fetch_array($result)) {
            if ($row['pt'] == 'cash') {
                $pCash = $row['t'];
            } else if ($row['pt'] == 'EFT') {
                $pEFT = $row['t'];
            } else if ($row['pt'] == 'cheque') {
                $pCheque = $row['t'];
            } else if ($row['pt'] == 'voucher') {
                $pVoucher = $row['t'];
            }
        }
        
        $pTotalMoney = $pCash + $pEFT + $pCheque;


        $pCoffeesSold = 0; 
        $pCafeSales = 0; 

        $query2 = "select sum(tl.units) as numcof, 
                    sum(tl.PRICE * tl.units) as totcof from ticketlines tl 
                    join products p on p.id = tl.product 
                    join receipts r on r.id = tl.TICKET 
                    where (p.name like '%coffee + %' or p.name like '%coffee short black%')  
                    and r.datenew > '" . $d1->format('Y-m-d') . "' 
                    and r.datenew < '" . $d2->format('Y-m-d') . "'";

        $result2 = mysqli_query($db_conn, $query2);
        
        while ($row2 = mysqli_fetch_array($result2)) {
            $pCoffeesSold = $row2['numcof'];
            $pCafeSales = $row2['totcof'];
        }

        $query3 = "select sum(tl.units) as numlun, 
                    sum(tl.PRICE * tl.units) as totlun from ticketlines tl 
                    join products p on p.id = tl.product 
                    join receipts r on r.id = tl.TICKET 
                    where p.name like '%LUNCH %' 
                    and r.datenew > '" . $d1->format('Y-m-d') . "' 
                    and r.datenew < '" . $d2->format('Y-m-d') . "'";

        $result3 = mysqli_query($db_conn, $query3);

        while ($row3 = mysqli_fetch_array($result3)) {
            $pLunchesSold = $row3['numlun'];
            $pLunchSales = $row3['totlun'];
        }

        $query4 = "select sum(tl.units) as numsoup, 
                    sum(tl.PRICE * tl.units) as totsoup from ticketlines tl 
                    join products p on p.id = tl.product 
                    join receipts r on r.id = tl.TICKET 
                    where p.name like '%ACOUSTIC %' 
                    and r.datenew > '" . $d1->format('Y-m-d') . "' 
                    and r.datenew < '" . $d2->format('Y-m-d') . "'";

        $result4 = mysqli_query($db_conn, $query4);

        while ($row4 = mysqli_fetch_array($result4)) {
            $pSoupsSold = $row4['numsoup'];
            $pSoupSales = $row4['totsoup'];
        }

        echo "<tr>";
        echo "<td>" . $d1->format('Y-m-d') . "</td>";
        echo "<td>" . number_format($pCash, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pEFT, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pCheque, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pTotalMoney, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pVoucher, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pCoffeesSold, 0, '.', '') . "</td>";
        echo "<td>" . number_format($pCafeSales, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pLunchesSold, 0, '.', '') . "</td>";
        echo "<td>" . number_format($pLunchSales, 2, '.', '') . "</td>";
        echo "<td>" . number_format($pSoupsSold, 0, '.', '') . "</td>";
        echo "<td>" . number_format($pSoupSales, 2, '.', '') . "</td>";

        echo "</tr>";

        $d1->modify('+1 day');
        $d2->modify('+1 day');

        $pTCash += $pCash;
        $pTEFT += $pEFT;
        $pTCheque += $pCheque;
        $pTTotalMoney += $pTotalMoney;
        $pTVoucher += $pVoucher;
        $pTCoffeesSold += $pCoffeesSold;
        $pTCafeSales += $pCafeSales;
        $pTLunchesSold += $pLunchesSold;
        $pTLunchSales += $pLunchSales;
        $pTSoupsSold += $pSoupsSold;
        $pTSoupSales += $pSoupSales;
    }

        echo "<tr>";
        echo "<th>Totals</th>";
        echo "<th>" . number_format($pTCash, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTEFT, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTCheque, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTTotalMoney, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTVoucher, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTCoffeesSold, 0, '.', '') . "</th>";
        echo "<th>" . number_format($pTCafeSales, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTLunchesSold, 0, '.', '') . "</th>";
        echo "<th>" . number_format($pTLunchSales, 2, '.', '') . "</th>";
        echo "<th>" . number_format($pTSoupsSold, 0, '.', '') . "</th>";
        echo "<th>" . number_format($pTSoupSales, 2, '.', '') . "</th>";

        echo "</tr>";

    echo "</table>";

    closeDatabase();

}

//Prints ClosingFigures 
function closingFigures() {
    echo "<div class='my-jumbo'>";
    echo "<div class='page-header'><h1>Closing Figures</h1></div>";
	?>
	<div class="alert alert-warning" style="width: 52%;margin-left: auto;margin-right: auto;">
			<strong>Please enter a valid date.<strong>For example:2014-02-12
		</div>
		<form name="getSaleDigram" method="GET" action="index.php">
		<p>Display the closing figures for: 
		<input type="hidden" name="p" value="close_of_day"/>
		<input type="date" name="tsd" class="input" data-rule-required="true" data-msg-required="Please enter a valid date" data-rule-dateISO="true" data-msg-dateISO="Please enter a valid date." tabindex="1"/>
		<input type="submit" value="Submit" class="btn btn-sm btn-default"></p>
	<?	
    echo '<h3><a href="index.php?p=close_of_day&tsd=';
    printPreviousMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/left.png" /></a> &nbsp;&nbsp;';
    printLabelForCurrentMonth($_REQUEST['tsd']);
    echo '&nbsp;&nbsp;<a href="index.php?p=close_of_day&tsd=';
    printNextMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/right.png" /></a></h3>';

    printClosingFiguresForMonth($_REQUEST['tsd']);
    echo "</div>";

}

function closingFiguresExtra() {
    echo "<div class='my-jumbo'>";
    echo "<div class='page-header'><h1>Closing Figures Extra</h1></div>";
	?>
	<div class="alert alert-warning" style="width: 52%;margin-left: auto;margin-right: auto;">
			<strong>Please enter a valid date.<strong>For example:2014-02-12
		</div>
		<form name="getSaleDigram" method="GET" action="index.php">
		<p>Display the closing figures extra for: 
		<input type="hidden" name="p" value="close_of_day_extra"/>
		<input type="date" name="tsd" class="input" data-rule-required="true" data-msg-required="Please enter a valid date" data-rule-dateISO="true" data-msg-dateISO="Please enter a valid date." tabindex="1"/>
		<input type="submit" value="Submit" class="btn btn-sm btn-default"></p>
	<?	
    echo '<h3><a href="index.php?p=close_of_day_extra&tsd=';
    printPreviousMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/left.png" /></a> &nbsp;&nbsp;';
    printLabelForCurrentMonth($_REQUEST['tsd']);
    echo '&nbsp;&nbsp;<a href="index.php?p=close_of_day_extra&tsd=';
    printNextMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/right.png" /></a></h3>';

    printCafeFiguresForMonth($_REQUEST['tsd']);
    echo "</div>";

}

function cafeSales() {
    echo "<div class='my-jumbo'>";
    echo "<div class='page-header'><h1>Cafe Sales</h1></div>";
	?>
	<div class="alert alert-warning" style="width: 45%;margin-left: auto;margin-right: auto;">
			<strong>Please enter a valid date.<strong>For example:2014-02-12
		</div>
		<form name="getSaleDigram" method="GET" action="index.php">
		<p>Display the cafe sales digram for: 
		<input type="hidden" name="p" value="cafe_sales"/>
		<input type="date" name="tsd" class="input" data-rule-required="true" data-msg-required="Please enter a valid date" data-rule-dateISO="true" data-msg-dateISO="Please enter a valid date." tabindex="1"/>
		<input type="submit" value="Submit" class="btn btn-sm btn-default"></p>
	<?
    echo '<h3><a href="index.php?p=cafe_sales&tsd=';
    printPreviousDaysDate($_REQUEST['tsd']);
    echo '"><img src="/image/left.png" /></a> &nbsp;&nbsp;';
    printTotalCafeSalesForDay($_REQUEST['tsd']);
    echo '&nbsp;&nbsp;<a href="index.php?p=cafe_sales&tsd=';
    printNextDaysDate($_REQUEST['tsd']);
    echo '"><img src="/image/right.png" /></a></h3>';

    printCafeGraphForDay($_REQUEST['tsd']);
    echo "</div>";
    
}

function printCafeGraphForDay($dayStr) {
    echo '<div id="cafe-chart" style="height: 250px;"></div>
        <div id="cafe-chart-sum" style="height: 250px;"></div>';

  addJavascriptToTail("new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'cafe-chart-sum',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: " . getCafeSalesForDayJSON($_REQUEST['tsd']) . ",
      // The name of the data record attribute that contains x-values.
      xkey: 'time',
      // A list of names of data record attributes that contain y-values.
      ykeys: ['sum'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Amount', 'sum']
    });

    new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'cafe-chart',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: " . getCafeSalesForDayJSON($_REQUEST['tsd']) . ",
      // The name of the data record attribute that contains x-values.
      xkey: 'time',
      // A list of names of data record attributes that contain y-values.
      ykeys: ['amount'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Amount']
    });");

}

//Functions for JavaScript Tail to retrun in index.php
function getJavascriptTail() {
    global $javascript_tail;
    return $javascript_tail;
}

function addJavascriptToTail($str) {
    global $javascript_tail;
    $javascript_tail .= $str;
}

function consignmentPayouts() {
    echo "<div class='my-jumbo'>";
    echo "<div class='page-header'><h1>Consignment Payouts</h1></div>";
	?>
	<div class="alert alert-warning" style="width: 45%;margin-left: auto;margin-right: auto;">
			<strong>Please enter a valid date.<strong>For example:2014-02-12
		</div>
		<form name="getSaleDigram" method="GET" action="index.php">
		<p>Display the consignment payouts for: 
		<input type="hidden" name="p" value="finance_consignment_payouts"/>
		<input type="date" name="tsd" class="input" data-rule-required="true" data-msg-required="Please enter a valid date" data-rule-dateISO="true" data-msg-dateISO="Please enter a valid date." tabindex="1"/>
		<input type="submit" value="Submit" class="btn btn-sm btn-default"></p>
	<?
    echo '<h3><a href="index.php?p=finance_consignment_payouts&tsd=';
    printPreviousMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/left.png" /></a> &nbsp;&nbsp;';
    printLabelForCurrentMonth($_REQUEST['tsd']);
    echo '&nbsp;&nbsp;<a href="index.php?p=finance_consignment_payouts&tsd=';
    printNextMonthsDate($_REQUEST['tsd']);
    echo '"><img src="/image/right.png" /></a></h3>';

    printConsignmentGraphForDay($_REQUEST['tsd']);
    echo "</div>";
    
}

function printConsignmentGraphForDay($dayStr) {
    echo '<div id="consignment-chart" style="height: 250px;"></div>
        <div id="consignment-chart-sum" style="height: 250px;"></div>';

  addJavascriptToTail("new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'consignment-chart-sum',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: " . getConsignmentSalesForMonthJSON($_REQUEST['tsd']) . ",
      // The name of the data record attribute that contains x-values.
      xkey: 'time',
      // A list of names of data record attributes that contain y-values.
      ykeys: ['sum'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Amount', 'sum']
    });

    new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'consignment-chart',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: " . getConsignmentSalesForMonthJSON($_REQUEST['tsd']) . ",
      // The name of the data record attribute that contains x-values.
      xkey: 'time',
      // A list of names of data record attributes that contain y-values.
      ykeys: ['amount'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Amount']
    });");

}
function listAdmin() {
    global $db_conn;
    openDatabase();
	$result = mysqli_query($db_conn, "SELECT UID, USERNAME FROM ADMINISTRATOR ORDER BY UID");

	?>
	<div class='page-header'>
		<h1>Admin List</h1>
	</div>

	<form name='adminform' method='post' action='index.php'>
	<table class="defaulttable">
	<tr class="panel panel-default panel-title"><th><input type='checkbox' name='selectall' id='selectall' value='sel' checked /></th><th>UID</th><th>Username</th></tr>
	<?
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td><input type='checkbox' name='checkbox[]' id='checkbox[]' value='" . $row['UID'] . "' checked/></td>";
        echo "<td>" . $row['UID'] . "</td>";
        echo "<td>" . $row['USERNAME'] . "</td>";
        echo "</tr>";
        $count++;
    }    
    echo "</table>";
	echo "</br>";
    echo "<input type='hidden' name='p' id='p' value='delete_admin'>";
    echo "<input class='btn btn-sm btn-danger' type='submit' name='delete' id='delete' value='Delete'/>";
    echo "</form>";

    if ($count == 0) {
        echo "<div class='alert alert-danger'>No ADMINISTRATOR at the moment.</div>";
    }

    closeDatabase();
}
function deleteAdmin() {
    global $db_conn;
    openDatabase();

    $checkboxes = $_REQUEST["checkbox"];
    foreach ($checkboxes as $checkbox) {
        $result = mysqli_query($db_conn, "DELETE FROM administrator WHERE uid=$checkbox");
    }
	echo "<div class='alert alert-success'> Successed </div>";
    closeDatabase();
	echo "	<script>	location.href='../index.php?p=admin_list';	</script>";
}

?>