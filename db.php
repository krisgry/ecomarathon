<?PHP
	include('config.incl');
	$conn = mysql_connect($db_host, $db_user, $db_passwd);
	if (!$conn) {
	    die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('eco') or die(mysql_error());
?>
