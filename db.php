<?PHP
	include('config.incl');
	$conn = mysql_connect('localhost:3307', $db_user, $db_passwd);
	if (!$conn) {
	    die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('eco');
?>
