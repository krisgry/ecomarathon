<?PHP
exit;
include "db.php";
session_start();
$values = fgets(fopen("<?PHP echo $site_prefix;?>/values.php", "r"));
$gps = fopen("http://org.ntnu.no/eitecov11/out.txt", "r");
$gpsarray = array();

while($line = fgets($gps)){
	$gpsarray[] = preg_split("/,/", $line);
}
if(!isset($_SESSION[gpsi]) || $_SESSION[gpsi] == count($gpsarray)){
	$_SESSION[gpsi] = 0;
}
$values = preg_split("/,/",$values, -1, PREG_SPLIT_NO_EMPTY);
$out = "";
$current = "NA";
$current_n = 0;
$maxdate = mysql_fetch_assoc(mysql_query("SELECT max(time) as time FROM log"));
$save_db = (strtotime("now")-strtotime($maxdate[time]))>10;
$save_db = false;
foreach($values as $v){
	if($v == "V"){
		$current = $v;
		$out .= "cell_voltage = [";
		$current_n = 0;
	}else if($v == "S"){
		$current = $v;
		$out = substr($out,0,strlen($out)-1)."];\n pressure_sensor = [";
		$current_n = 0;
	}else if($v == "T"){
		$current = $v;
		$out = substr($out,0,strlen($out)-1)."];\n temperature = [";
		$current_n = 0;
	}else if($v == "P"){
		$current = $v;
		$out = substr($out,0,strlen($out)-1)."];\n pressure = [";
		$current_n = 0;
	}else if($v == "O"){
		$current = $v;
		$out = substr($out,0,strlen($out)-1)."];\n fuelcell_out = [";
		$current_n = 0;
	}else if($v == "err"){
		$out .= "'".$v."',";
	}else if($v != "\n"){
		$out .= $v.",";
		if($save_db){
			save_to_db($current,$current_n++,$v);
		}
	}
}

function save_to_db($t,$n,$v){
	$type = -1;
	if($t == "V"){
		$type = 0;
	}else if($t == "S"){
		$type = 1;
	}else if($t == "T"){
		$type = 2;
	}else if($t == "P"){
		$type = 3;
	}else if($t == "O"){
		$type = 4;
	}
	mysql_query("INSERT INTO log (type,n,value) VALUES (".$type.",".$n.",".$v.")");
}
$out = substr($out,0,strlen($out)-1)."];";
echo $out;
$cp = mysql_fetch_assoc(mysql_query("SELECT r.id as rid, r.*, c.* FROM realcps r JOIN cps c ON c.id = r.cp_id WHERE visited = 0 ORDER BY r.id ASC LIMIT 1"));
$last_cp = mysql_query("SELECT * FROM realcps r JOIN cps c ON c.id = r.cp_id WHERE r.visited = 1 AND r.id < ".$cp[rid]." AND c.finish = 1 ORDER BY r.id DESC LIMIT 1");
if($last_cp){
	$last_cp = mysql_fetch_assoc($last_cp);
}

$lat = $gpsarray[$_SESSION[gpsi]][0];
$long = $gpsarray[$_SESSION[gpsi]][1];

$started = mysql_fetch_assoc(mysql_query("SELECT * FROM config"));

if($cp[visited] == 0 && $started[time_status] == 1){
	$ok = false;
	if($cp[direction] == 1 && ($long < $cp[p1lo] || $long < $cp[p2lo]) && $lat < $cp[p1la] && $lat > $cp[p2la]){
		$ok = true;
	}else if($cp[direction] == 2 && ($long > $cp[p1lo] || $long > $cp[p2lo]) && $lat < $cp[p1la] && $lat > $cp[p2la]){
		$ok = true;
	}
	if($ok){
		mysql_query("UPDATE realcps SET visited = 1, visited_at = CURRENT_TIMESTAMP WHERE id = ".$cp[rid]);
		if($cp[finish] == 1){
			$id = mysql_fetch_assoc(mysql_query("SELECT min(id) as id, laps.* FROM laps WHERE time IS NULL"));
			$time = strtotime("now")-((!$last_cp)?strtotime($started[time]):strtotime($last_cp[visited_at]));
			mysql_query("UPDATE laps SET time = ".$time." WHERE id = ".$id[id]);
			echo "$('#lap".$id[id]."').text(\"".floor($time/60).":".str_pad($time%60, 2, "0", STR_PAD_LEFT)."\");";
			$diff = $time -$id[planned_time] ;
			echo "$('#lapdiff".$id[id]."').text(\"".(($diff < 0)?"-":"+").floor(abs($diff)/60).":".str_pad(abs($diff)%60, 2, "0", STR_PAD_LEFT)."\");";
			$avgspeed = (3173/$time)*3.6;
			echo "$('#avglap".$id[id]."').text(\"".round($avgspeed,1)." km/h\");";
			$totaltime = mysql_fetch_assoc(mysql_query("SELECT SUM(time) as sum FROM laps"));
			echo "$('#totaltime').text(\"".floor($totaltime[sum]/60).":".str_pad($totaltime[sum]%60, 2, "0", STR_PAD_LEFT)."\");";
			$diff = mysql_fetch_assoc(mysql_query("SELECT SUM(planned_time) as sum FROM laps"));
			$diff = $totaltime[sum]-$diff[sum];
			echo "$('#totaldiff').text(\"".(($diff < 0)?"-":"+").floor(abs($diff)/60).":".str_pad(abs($diff)%60, 2, "0", STR_PAD_LEFT)."\");";
			$avgspeed = ($cp[distance]/$totaltime[sum])*3.6;
			echo "$('#avgspeed').text(\"".round($avgspeed,1)." km/h\");";
		}
	}
	}
?>
pos = [[<?PHP echo $gpsarray[$_SESSION[gpsi]][0];?>,<?PHP echo $gpsarray[$_SESSION[gpsi]++][1];?>]];
index = 0;
<?PHP mysql_close($conn); ?>
