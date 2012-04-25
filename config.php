<?PHP
  include 'db.php';
  if(isset($_GET['action'])){
    $action = $_GET['action'];
    $a = mysql_fetch_assoc(mysql_query("SELECT * FROM config"));
    if($action == "start_clock"){
      if($a[time] == "0000-00-00 00:00:00"){
        mysql_query("UPDATE config SET time = CURRENT_TIMESTAMP, time_status = 1, time_stopped_at = '0000-00-00 00:00:00'");
      }else if($a[time_stopped_at] != "0000-00-00 00:00:00"){
        $now = date("U");
        $last_start = strtotime($a[time]);
        $stopped_at = strtotime($a[time_stopped_at]);
        $new_date = date("Y-m-d H:i:s", $last_start+($now-$stopped_at));
        mysql_query("UPDATE config SET time_status = 1, time_stopped_at = '0000-00-00 00:00:00', time = '".$new_date."'");
      }else{
        mysql_query("UPDATE config SET time_status = 1, time_stopped_at = '0000-00-00 00:00:00'");
      }
    }else if($action == "stop_clock"){
      if($a[time] != "0000-00-00 00:00:00"){
        mysql_query("UPDATE config SET time_status = 0, time_stopped_at = CURRENT_TIMESTAMP");
      }
    }else if($action == "reset_clock"){
      mysql_query("UPDATE config SET time_status = 0, time_stopped_at = '0000-00-00 00:00:00', time = '0000-00-00 00:00:00'");
      mysql_query("UPDATE laps SET time = NULL");
      mysql_query("UPDATE realcps SET visited = 0, visited_at = '0000-00-00 00:00:00'");
    }
    return;
  }
  if(isset($_GET['emptylog'])){
    mysql_query("TRUNCATE table log");
  }
?>
 <div class="modal-header">
    <h3>Site configuration</h3>
    <a href="#" class="close" onclick="$('#modal_div').modal('hide')">X</a>
    <form method="get">
      <input class="input-small" type="submit" value="Empty log" name="emptylog">
    </form>
  </div>
<?php
include 'db.php';
if(isset($_POST['config'])){
  foreach($_POST['config'] as $key => $val){
    mysql_query("UPDATE config SET ".$key." = '".$val."'");
  }
}
if(isset($_POST['laps'])){
  foreach($_POST['laps'] as $key => $val){
    mysql_query("UPDATE laps SET planned_time = ".$val." WHERE id = ".$key);
  }
}
if(isset($_POST['sensor'])){
  foreach($_POST['sensor'] as $key => $val){
    foreach($_POST['sensor'][$key] as $n => $name){
      mysql_query("UPDATE type_sensor SET name='".$name[0]."', min =".$name[1].", max=".$name[2]." WHERE n = ".$n." AND type = ".$key);
    }
  }
}
$config = mysql_query("SELECT * FROM config");
$config = mysql_fetch_assoc($config);
$laps = mysql_query("SELECT * FROM laps");
?>
   <form method="post">
     <div class="modal-body">
     <table class="table">
       <thead>
       <tr><th colspan="2">Cell voltages:</th></tr>
       <tr><td>#</td><td>Text</td><td>Min</td><td>Max</td></tr>
       </thead>
       <tbody>
       <?PHP
       $names = mysql_query("SELECT * FROM type_sensor WHERE type = 0");
       while($name = mysql_fetch_assoc($names)){
         echo "<tr>";
         echo "<td>".$name[n]."</td><td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][0]\" value=\"".$name[name]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" class=\"input-small\" name=\"sensor[".$name[type]."][".$name[n]."][1]\" value=\"".$name[min]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" class=\"input-small\" name=\"sensor[".$name[type]."][".$name[n]."][2]\" value=\"".$name[max]."\"></td>";
         echo "</tr>";
       }
       ?>
       </tbody>
     </table>
     <h3>Sum Cell Voltage:</h3>
     <table class="table">
       <tr><td>#</td><td>Text</td><td>Min</td><td>Max</td></tr>
       <?PHP
       $names = mysql_query("SELECT * FROM type_sensor WHERE type = 1");
       while($name = mysql_fetch_assoc($names)){
         echo "<tr>";
         echo "<td>".$name[n]."</td><td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][0]\" value=\"".$name[name]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][1]\" value=\"".$name[min]."\" ></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][2]\" value=\"".$name[max]."\" ></td>";
         echo "</tr>";
       }
       ?>
       <tr><th colspan="2">Temperature:</th></tr>
       <tr><td>#</td><td>Text</td><td>Min</td><td>Max</td></tr>
       <?PHP
       $names = mysql_query("SELECT * FROM type_sensor WHERE type = 2");
       while($name = mysql_fetch_assoc($names)){
         echo "<tr>";
         echo "<td>".$name[n]."</td><td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][0]\" value=\"".$name[name]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][1]\" value=\"".$name[min]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][2]\" value=\"".$name[max]."\"></td>";
         echo "</tr>";
       }
       ?>
       <tr><th colspan="2">Pressure:</th></tr>
       <tr><td>#</td><td>Text</td><td>Min</td><td>Max</td></tr>
       <?PHP
       $names = mysql_query("SELECT * FROM type_sensor WHERE type = 3");
       while($name = mysql_fetch_assoc($names)){
         echo "<tr>";
         echo "<td>".$name[n]."</td><td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][0]\" value=\"".$name[name]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][1]\" value=\"".$name[min]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][2]\" value=\"".$name[max]."\"></td>";
         echo "</tr>";
       }
       ?>
       <tr><th colspan="2">Output:</th></tr>
       <tr><td>#</td><td>Text</td><td>Min</td><td>Max</td></tr>
       <?PHP
       $names = mysql_query("SELECT * FROM type_sensor WHERE type = 4");
       while($name = mysql_fetch_assoc($names)){
         echo "<tr>";
         echo "<td>".$name[n]."</td><td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][0]\" value=\"".$name[name]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][1]\" value=\"".$name[min]."\"></td>";
         echo "<td><input class=\"input-small\" type=\"text\" name=\"sensor[".$name[type]."][".$name[n]."][2]\" value=\"".$name[max]."\"></td>";
         echo "</tr>";
       }
       ?>  
     </table>
     <table class="table">
       <tr><th>Lap #</th><th>Planned time (in seconds)</th></tr>
       <?PHP 
         while($l = mysql_fetch_assoc($laps)){
       ?>
       <tr><td><?PHP echo $l[id];?></td><td><input class="input-small" type="text" name="laps[<?PHP echo $l[id];?>]" value="<?PHP echo $l[planned_time];?>"></td></tr>
       <?PHP
         }
       ?>
     </table>
     <h3>Other stuff:</h3>
     <table>
       <tr>
         <td>Adress:</td>
         <td><input type="text" name="config[address_for_data]" value="<?PHP echo $config['address_for_data'];?>"></td>
       </tr>
     </table>
     <div class="modal-footer">
       <div class="btn" onclick="$('#modal_div').modal('hide')">Cancel</div>
       <input class="btn btn-primary" type="submit" value="Save">
     </div>
    </div>
   </form>
<?PHP mysql_close($conn);?>
