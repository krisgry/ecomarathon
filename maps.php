<script>
var map;
var baseIcon;
var marker;
var icon;
var followcar = false;
var mapmodechanged = false;
$(document).ready(function() {
  if (GBrowserIsCompatible()) {
    map = new GMap2(document.getElementById("map_canvas"));
    <?PHP
    echo 'map.setCenter(new GLatLng('. $track_latitude . ','. $track_longitude . '), 15);'
    ?>
    map.setMapType(G_SATELLITE_MAP);
    map.setUIToDefault();

    // Create a base icon for all of our markers that specifies the
    // shadow, icon dimensions, etc.
    baseIcon = new GIcon(G_DEFAULT_ICON);
    baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
    baseIcon.iconSize = new GSize(34, 34);
    baseIcon.shadowSize = new GSize(37, 34);
    baseIcon.iconAnchor = new GPoint(9, 34);
    baseIcon.infoWindowAnchor = new GPoint(9, 2);

    icon = new GIcon(baseIcon);
    icon.image = "img/ff-logo-small.png";
  }
});
function setCarPos(latitude, longitude){
    if(typeof marker != "undefined" && typeof map != "undefined"){
        map.removeOverlay(marker);
    }
    var latlng = new GLatLng(latitude, longitude);
    // Set up our GMarkerOptions object
    markerOptions = { icon:icon };
    marker = new GMarker(latlng, markerOptions);
    if(typeof map != "undefined" ){
        if(mapmodechanged){
            <?PHP
            echo 'map.setCenter(new GLatLng('. $track_latitude . ','. $track_longitude . '), 15);'
            ?>
            mapmodechanged = false;
        }
        if(followcar){
            map.setCenter(latlng);
        }
        map.addOverlay(marker);
    }
}
function setFollow(){
    followcar = true;
}
function unsetFollow(){
    followcar = false;
    mapmodechanged = true;
}
</script>
