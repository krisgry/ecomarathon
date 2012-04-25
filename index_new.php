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
    <title>DNV Fuelfigther, powered by Twitter Bootsrap</title>
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
          <form name="clock" class="navbar-search pull-right"><input class="input-small" type="text" name="stwa" value="00 : 00 : 00"><input class="btn" type="button" name="theButton" onClick="stopwatchButton(this.value);" value="Start"><input class="btn" type="button" onClick="stopwatchButton(this.value);reset();" value="Reset"></form>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#sensors">Sensors</a></li>
              <li><a href="#speed">About</a></li>
              <li><a id="config_btn" href="#">Config</a>
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
      <div class="hero-unit">
        <!--Google maps-->
        <div id="map_canvas" style="width: 480px; height: 400px;"></div>
        <?PHP
          include('maps.php');
        ?>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
           <div id="sensors" class="accordion">
             <div class="accordion-group">
               <div class="accordion-heading">
                 <a href="#collapseOne" data-parent="#sensors" data-toggle="collapse" class="accordion-toggle">
                   Cell Voltages
                   <span class="label label-warning right">Warning</span>
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
                   Temperature
                 </a>
               </div>
               <div style="height: 0px;" id="collapseTwo" class="accordion-body collapse">
                 <div class="accordion-inner">
                   Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                 </div>
               </div>
             </div>
             <div class="accordion-group">
               <div class="accordion-heading">
                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#sensors" href="#collapseThree">
                   Output voltage
                 </a>
               </div>
               <div style="height: 0px;" id="collapseThree" class="accordion-body collapse">
                 <div class="accordion-inner">
                   Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                 </div>
               </div>
             </div>
           </div>
        </div>
        <div class="span4">
          <h2>Gauge</h2>
            <div id="speed" style="width:150px;height:150px;"></div>
            <script type="text/javascript">
                var pos;
                var speed = 0.0;
                var visibleChart = 0;
                var visibleCount = 0;
                var speedg = bindows.loadGaugeIntoDiv("gauge.xml", "speed");

                speedg.needle.setValue(speed);
                speedg.label.setText(speed);
            </script>
        </div>
        <div class="span4">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
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
