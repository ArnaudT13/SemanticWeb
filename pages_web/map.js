
stationsCoordinates = []

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
        }
      });

      // Create a layer tha will consume objects from our clustering provider
      var clusteringLayer = new H.map.layer.ObjectLayer(clusteredDataProvider);

      // To make objects from clustering provder visible,
      // we need to add our layer to the map
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
    // MapEvents enables the event system
    // Behavior implements default interactions for pan/zoom (also on mobile touch environments)
    var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

    // Create the default UI components
    var ui = H.ui.UI.createDefault(map, defaultLayers);

    startClustering(map);

    // Now use the map as required...
    window.onload = function () {
      //addMarkersToMap(map);
    }


});
