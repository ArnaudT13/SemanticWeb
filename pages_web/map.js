
stationsCoordinates = []

var svgMarkup = '<svg width="24" height="24" ' +
'xmlns="http://www.w3.org/2000/svg">' +
'<rect stroke="white" fill="#1b468d" x="1" y="1" width="22" ' +
'height="22" /><text x="12" y="18" font-size="10pt" ' +
'font-family="Arial"  text-anchor="middle" ' +
'fill="white">{text}B</text></svg>';

/**
 *
 */
function coords2map(stationsFromPHP){
    stationsFromPHP.forEach((station, i) => {
        stationsCoordinates.push({
             'lat' : parseFloat(station[3]),
             'lng' : parseFloat(station[2])
         });
    });


}




document.addEventListener("DOMContentLoaded", function() {

    var platform = new H.service.Platform({
      'apikey': 'PmX7KUPNkev1ADjuAv4N8RMN_nUjUaVg-SDZXb-9RDg'
    });


    /**
     * Adds markers to the map highlighting the locations of the captials of
     * France, Italy, Germany, Spain and the United Kingdom.
     *
     * @param  {H.Map} map      A HERE Map instance within the application
     */
    function addMarkersToMap(map) {
        stationsCoordinates.forEach((stationC, i) => {
            var marker = new H.map.Marker(stationC);
            map.addObject(marker);
        });
    }


    // Custom clustering theme description object.
    // Object should implement H.clustering.ITheme interface
    var CUSTOM_THEME = {



      getClusterPresentation: function(cluster) {
        // Get random DataPoint from our cluster
        svgString = svgMarkup.replace('{text}', + cluster.getWeight());

        // Create a marker from a random point in the cluster
        var clusterMarker = new H.map.Marker(cluster.getPosition(), {
          icon: new H.map.Icon(svgString),

          // Set min/max zoom with values from the cluster,
          // otherwise clusters will be shown at all zoom levels:
          min: cluster.getMinZoom(),
          max: cluster.getMaxZoom()
        });

        return clusterMarker;
      },
      getNoisePresentation: function (noisePoint) {
        // Get a reference to data object our noise points
        var data = noisePoint.getData(),
          // Create a marker for the noisePoint
          noiseMarker = new H.map.Marker(noisePoint.getPosition(), {
            // Use min zoom from a noise point
            // to show it correctly at certain zoom levels:
            min: noisePoint.getMinZoom(),
            icon: new H.map.Icon(svgMarkup, {
              size: {w: 20, h: 20},
              anchor: {x: 10, y: 10}
            })
          });

        // Link a data from the point to the marker
        // to make it accessible inside onMarkerClick
        noiseMarker.setData(data);

        return noiseMarker;
      }
    };

    /**
     * Display clustered markers on a map
     *
     * Note that the maps clustering module https://js.api.here.com/v3/3.1/mapsjs-clustering.js
     * must be loaded to use the Clustering

     * @param {H.Map} map A HERE Map instance within the application
     * @param {Object[]} data Raw data that contains airports' coordinates
    */
    function startClustering(map) {
        data = stationsCoordinates;
      // First we need to create an array of DataPoint objects,
      // for the ClusterProvider
      var dataPoints = data.map(function (item) {
        return new H.clustering.DataPoint(item.lat, item.lng);
      });

      // Create a clustering provider with custom options for clusterizing the input
      var clusteredDataProvider = new H.clustering.Provider(dataPoints, {
        clusteringOptions: {
          // Maximum radius of the neighbourhood
          eps: 32,
          // minimum weight of points required to form a cluster
          minWeight: 2
       },
       //theme: CUSTOM_THEME
      });

      var clusteringLayer = new H.map.layer.ObjectLayer(clusteredDataProvider);


      map.addLayer(clusteringLayer);
    }


    var defaultLayers = platform.createDefaultLayers();

    //Step 2: initialize a map - this map is centered over Europe
    var map = new H.Map(document.getElementById('map'),
      defaultLayers.vector.normal.map,{
      center: {lat:50, lng:5},
      zoom: 5,
      pixelRatio: window.devicePixelRatio || 1
    });

    // add a resize listener to make sure that the map occupies the whole container
    window.addEventListener('resize', () => map.getViewPort().resize());

    //Step 3: make the map interactive
    var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

    // Create the default UI components
    var ui = H.ui.UI.createDefault(map, defaultLayers);

    startClustering(map);



});
