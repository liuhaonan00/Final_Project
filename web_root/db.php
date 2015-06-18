<?
/**
 *		$editor: Jingyi Gao u5167972$
 *      $Id: storage.php 2014-08-12 $
 */
ini_set('max_execution_time', 300);

$appVersion = "2.0";
// Define global variable to represent connection to the database
$db_conn = null;
function openDatabase() {
	//conection:
    global $db_conn;
    $db_conn = mysqli_connect("127.0.0.1:3306", "root", "pass", "openbravo-pos");
    if (!$db_conn) {
        echo "Failed to connect to DB" . mysqli_connect_error();
    }
}

function closeDatabase() {
    global $db_conn;
    mysqli_close($db_conn);
}

?>