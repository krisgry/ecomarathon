<?PHP
include 'config.incl';
include 'login.php';
include 'db.php';
$_SESSION['config'] = mysql_fetch_assoc(mysql_query('SELECT * FROM config'));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>DNV Fuelfighter, powered by Twitter Bootsrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="style/bootstrap.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 0px;
        padding-bottom: 40px;
      }
    </style>


    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyB6i1TDsf0RiuCKZ3Wxis1bjip0DEQltFE" type="text/javascript"></script>
    <script type="text/javascript" src="js/bindows_gauges.js"></script>
    <script type="text/javascript" src="js/stopwatch.js"></script>
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Fuelfigther</a>
          <form name="clock" class="pull-left">
	    <input class="input-small" style="margin-top: 5px;" type="text" name="stwa" value="00 : 00 : 00">
            <input class="btn" type="button" name="theButton" onClick="stopwatchButton(this.value);" value="Start">
            <input class="btn" type="button" onClick="stopwatchButton(this.value);reset();" value="Reset">
          </form>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#sensors">Sensors</a></li>
              <li><a href="#speed_header">Speed</a></li>
              <li><a id="config_btn" href="#">Configuration</a>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <script>
      $('#config_btn').click(function(e){
        $('#modal_div').load('config.php');
        $('#modal_div').modal('show');
      })
    </script>

    <div class="modal" id="modal_div"></div>
    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <!--Google maps-->
      <div id="map_canvas" style="padding-top: 50px; width: 480px; height: 400px;"></div>
      <?PHP
        include('maps.php');
      ?>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
            <table border="1" width="100%">
               <tr><th>Lap #</th><th>Actual Time</th><th>Planned time</th><th>Diff</th><th>Avg speed</th></tr>
               <?PHP $times = mysql_query("SELECT * FROM laps");
                       $totalplanned = 0;
                       $totaltime = 0;
               while($lap = mysql_fetch_assoc($times)){
                       $totalplanned += $lap['planned_time'];
                       $totaltime += $lap['time'];
                       echo "<tr>";
                       echo "<td>".$lap['id']."</td>";
                       echo "<td id=\"lap".$lap['id']."\">".(($lap['time'] != "")?floor($lap['time']/60).":".str_pad($lap['time']%60, 2, "0", STR_PAD_LEFT):"")."</td>";
                       echo "<td>".floor($lap['planned_time']/60).":".str_pad($lap['planned_time']%60, 2, "0", STR_PAD_LEFT)."</td>";
                       $diff = $lap['time']-$lap['planned_time'];
                       echo "<td id=\"lapdiff".$lap['id']."\">".(($lap['time'] != "")?(($diff < 0)?"-":"+").floor(abs($diff)/60).":".str_pad(abs($diff)%60, 2, "0", STR_PAD_LEFT):"")."</td>";
                       echo "<td id=\"avglap".$lap['id']."\">".(($lap['time'] != 0)?round((3173/$lap['time'])*3.6, 2)." km/h":"")."</td>";
                       echo "</tr>";
               }
               ?>
               <tr><td>Total</td>
                       <td id="totaltime"><?PHP echo floor($totaltime/60).":".str_pad($totaltime%60, 2, "0", STR_PAD_LEFT);?></td>
                       <td><?PHP echo floor($totalplanned/60).":".str_pad($totalplanned%60, 2, "0", STR_PAD_LEFT);?></td>
                       <?PHP $diff = $totaltime-$totalplanned;?>
                       <td id="totaldiff"><?PHP echo (($diff != 0)?(($diff < 0)?"-":"+").floor(abs($diff)/60).":".str_pad(abs($diff)%60, 2, "0", STR_PAD_LEFT):"");?> </td>
                       <td id="avgspeed">
                       <?PHP 
                               if($totaltime != 0){
                                       $totaldistance = mysql_fetch_assoc(mysql_query("SELECT max(distance) as max FROM realcps WHERE visited = 1"));
                                       echo round(($totaldistance[max]/$totaltime)*3.6, 2)." km/h";
                               }
                       ?>
                       </td>
               </tr>
            </table>
        </div>
        <div class="span4">
           <div id="sensors" class="accordion">
             <div class="accordion-group">
               <div class="accordion-heading">
                 <a href="#collapseOne" data-parent="#sensors" data-toggle="collapse" class="accordion-toggle">
                   Cell Voltages
                 </a>
               </div>
               <div class="accordion-body collapse" id="collapseOne" style="height: 0px;">
                 <div class="accordion-inner">
                 <table><tbody>
                     <tr>
                 <?PHP
                     for($i = 0; $i < 5; $i++){
                         $name = mysql_fetch_assoc(mysql_query("SELECT name FROM type_sensor WHERE type = 4 AND n = ".$i));
                         echo "<td>".$name['name']."</td>";
                     }
                     echo "</tr><tr>";
                     for($i = 0; $i < 5; $i++){
                         echo "<td><a class='' href=\"stat.php?type=4&n=".$i."\" style=\"text-decoration:  none;cursor: pointer\" id=\"outputvo".$i."\">GRAPH</a></td>";
                     }
                 ?>
                     </tr>
                 </tbody></table>
                 </div>
               </div>
             </div>
             <div class="accordion-group">
               <div class="accordion-heading">
                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#sensors" href="#collapseTwo">
                   Pressure
                 </a>
               </div>
               <div style="height: 0px;" id="collapseTwo" class="accordion-body collapse">
                 <div class="accordion-inner">
                 <table><tbody>
                     <tr>
                 <?PHP
                     for($i = 0; $i < 2; $i++){
                         $name = mysql_fetch_assoc(mysql_query("SELECT name FROM type_sensor WHERE type = 3 AND n = ".$i));
                         echo "<td>".$name['name']."</td>";
                     }
                     echo "</tr><tr>";
                     for($i = 0; $i < 2; $i++){
                         echo "<td><a class='' href=\"stat.php?type=3&n=".$i."\" style=\"text-decoration:  none;cursor: pointer\" id=\"outputvo".$i."\">GRAPH</a></td>";
                     }
                 ?>
                     </tr>
                 </tbody></table>
                 </div>
               </div>
             </div>
             <div class="accordion-group">
               <div class="accordion-heading">
                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#sensors" href="#collapseThree">
                   Temperature
                 </a>
               </div>
               <div style="height: 0px;" id="collapseThree" class="accordion-body collapse">
                 <div class="accordion-inner">
                 <table><tbody>
                     <tr>
                 <?PHP
                     for($i = 0; $i < 4; $i++){
                         $name = mysql_fetch_assoc(mysql_query("SELECT name FROM type_sensor WHERE type = 2 AND n = ".$i));
                         echo "<td>".$name['name']."</td>";
                     }
                     echo "</tr><tr>";
                     for($i = 0; $i < 4; $i++){
                         echo "<td><a class='' href=\"stat.php?type=2&n=".$i."\" style=\"text-decoration:  none;cursor: pointer\" id=\"outputvo".$i."\">GRAPH</a></td>";
                     }
                 ?>
                     </tr>
                 </tbody></table>
                 </div>
               </div>
             </div>
           </div>
        </div>
        <div class="span4">
          <h2 id="speed_header">Speed</h2>
            <div id="speed" style="width:150px;height:150px;"></div>
            <script type="text/javascript">
                var pos;
                var speed = 0.0;
                var visibleChart = 0;
                var visibleCount = 0;
                var speedg = bindows.loadGaugeIntoDiv("gauge.xml", "speed");

                <?PHP 
                if ($_SESSION['config']['time'] != "0000-00-00 00:00:00"){
                        $time = strtotime("now")-strtotime($_SESSION['config']['time']);
                        echo "var sec = ".($time%60).";";
                        echo "var min = ".(floor($time/60)%60).";";
                        echo "var hour = ".floor($time/3600).";";
                        if($_SESSION['config']['time_status'] == 1){
                                echo "stopwatch(\"Start\");";
                        }
                }else{  
                ?>
                        var sec = 0;
                        var min = 0;
                        var hour = 0;
                <?PHP
                }
                ?>
                <?PHP
                        $thre = mysql_query("SELECT * FROM type_sensor");
                        $thresholds = array();
                        while($t = mysql_fetch_assoc($thre)){
                                $thresholds[$t['type']][$t['n']] = array($t['min'],$t['max']);
                        }
                        $out = "";
                        foreach($thresholds as $o){
                                $out .= (($out == "")?"[":",[");
                                foreach($o as $i){
                                        if(substr($out, count($out)-1, strlen($out))=="[,"){
                                                $out = substr($out, 0, strlen($out)-1);
                                        }
                                        $out .= ((substr($out, strlen($out)-1, strlen($out)) == "[")?"[":",[").$i[0].",".$i[1]."]";
                                }
                                $out .= "]";
                        }
                        echo "var threshold = [".$out."];";
                ?>
                function showhide(div) {

                        if($("#"+div).css("display") == "none"){
                                $("#"+div).css("display", "block");
                        }else{
                                $("#"+div).css("display", "none");
                        }
                        $("#"+div+"but").css("background-color", "");
                }
                //<!--Get values-->
                $(function () {
                        updateValues();
                });
                function updateValues() {
                        $.getScript('<?PHP echo $_SESSION['config']['address_for_data'];?>', function(){updateUI();});
                }
                function updateUI(){
                        found = false;
                        for(i = 0; i < 46; i++){
                                $("#cell"+i).text(cell_voltage[i]+" V");
                                if(cell_voltage[i] < threshold[0][i][0] || cell_voltage[i] > threshold[0][i][1]){
                                        $("#cell"+i).css("color", "red");
                                        found = true;
                                }else if($("#cell"+i).css("color") == "red" || $("#cell"+i).css("color") == "rgb(255, 128, 64)"){
                                        $("#cell"+i).css("color", "#FF8040");
                                }else{
                                        $("#cell"+i).css("color", "green");
                                }
                        }
                        if(found){
                                $("#cellvoltbut").css("background-color", "red");
                        }
                        found = false;
                        for(i = 0; i < 12; i++){
                                $("#tempsens"+i).text(temperature[i]+" C");
                                if(temperature[i] < threshold[2][i][0] || temperature[i] > threshold[2][i][1]){
                                        $("#tempsens"+i).css("color", "red");
                                        found = true;
                                }else if($("#tempsens"+i).css("color") == "red" || $("#tempsens"+i).css("color") == "rgb(255, 128, 64)"){
                                        $("#tempsens"+i).css("color", "#FF8040");
                                }else{
                                        $("#tempsens"+i).css("color", "green");
                                }
                        }
                        if(found){
                                $("#temperaturebut").css("background-color", "red");
                        }
                        found = false;
                        for(i = 0; i < 5; i++){
                                $("#outputvo"+i).text(fuelcell_out[i]+" V");
                                if(fuelcell_out[i] < threshold[4][i][0] || fuelcell_out[i] > threshold[4][i][1]){
                                        $("#outputvo"+i).css("color", "red");
                                        found = true;
                                }else if($("#outputvo"+i).css("color") == "red" || $("#outputvo"+i).css("color") == "rgb(255, 128, 64)"){
                                        $("#outputvo"+i).css("color", "#FF8040");
                                }else{
                                        $("#outputvo"+i).css("color", "green");
                                }
                        }
                        if(found){
                                $("#outputvbut").css("background-color", "red");
                        }
                        found = false;
                        for(i = 0; i < 2; i++){
                                $("#pressures"+i).text(pressure[i]+" Pa");
                                if(pressure[i] < threshold[3][i][0] || pressure[i] > threshold[3][i][1] || pressure[i] == "err"){
                                        $("#pressures"+i).css("color", "red");
                                        found = true;
                                }else if($("#pressures"+i).css("color") == "red" || $("#pressures"+i).css("color") == "rgb(255, 128, 64)"){
                                        $("#pressures"+i).css("color", "#FF8040");
                                }else{
                                        $("#pressures"+i).css("color", "green");
                                }
                        }
                        if(found){
                                $("#pressurebut").css("background-color", "red");
                        }
                        found = false;
                        $("#sumcell").text(sumcell[0]+" V");
                        if(sumcell[0] < threshold[1][0][0] || sumcell[0] > threshold[1][0][1]){
                                $("#sumcell").css("color", "red");
                                found = true;
                        }else if($("#sumcell").css("color") == "red" || $("#sumcell").css("color") == "rgb(255, 128, 64)"){
                                $("#sumcell").css("color", "#FF8040");
                        }else{
                                $("#sumcell").css("color", "green");
                        }
                        if(found){
                                $("#sumcellvoltbut").css("background-color", "red");
                        }
                        setCarPos(pos[index][0], pos[index][1]);
                        speedg.needle.setValue(speed);
                        speedg.label.setText(speed);
                        $("#speed").text(speed);
                        setTimeout(updateValues, 2000);
                }
                function stopwatchButton(value){
                        if(value == 'Start'){
                                $.ajax({url: "<?PHP echo $site_prefix;?>config.php?action=start_clock"});
                                stopwatch(value);
                        }else if(value == 'Stop '){
                                $.ajax({url: "<?PHP echo $site_prefix;?>config.php?action=stop_clock"});
                                stopwatch(value);
                        }else if(value == 'Reset'){
                                $.ajax({url: "<?PHP echo $site_prefix;?>config.php?action=reset_clock"});
                                for(i = 1; i <= 6; i++){
                                        $("#lap"+i).text('');
                                        $("#lapdiff"+i).text('');
                                        $("#avglap"+i).text('');
                                }
                                $("#avgspeed").text('');
                                $("#totaldiff").text('');
                                $("#totaltime").text('');
                                resetIt();
                        }
                }
                speedg.needle.setValue(speed);
                speedg.label.setText(speed);
            </script>
        </div>
      </div><!-- end row 1 -->

      <div id="graph_div" class="modal" />
      </div>

      <hr />

      <footer>
        <p>&copy; EiT EcoMarathon 2012</p>
      </footer>
    </div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script src="js/bootstrap-modal.js"></script>

  </body>
</html>
