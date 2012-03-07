<?PHP
	$conn = mysql_connect('localhost:3307', 'eco', 'ecomarathon');
	if (!$conn) {
	    die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('eco');
?>
