var stationsCoordinates = []
var stationsArray = null;

/**
* Parse a given array of stations and returns an dict with only coordinates.
*
*@param {array} stationsFromPHP A array of stations from json_encode
*@returns {dict} every coordinates (lng, lat) of each stations
*/
function coords2map(stationsFromPHP){
    stationsArray = stationsFromPHP.slice();
    stationsFromPHP.forEach((station, i) => {
        stationsCoordinates.push({
            'lng' : parseFloat(station[station.length - 2]),
            'lat' : parseFloat(station[station.length - 1])
        });
    });
}

document.addEventListener("DOMContentLoaded", function() {
    // Represents the reference of HERE's map layer
    reference = null;

    /**
    * Management of the stations checkboxs and therefore updates the map
    * depending events.
    *
    */
    var stationsRadioButton = document.getElementById('inlineRadioStations');
    if(stationsRadioButton){
        stationsRadioButton.onclick = function() {
            const coordsForStations = stationsArray.slice();
            for (var i = 0; i < coordsForStations.length; i++) {
                if(coordsForStations[i][2] !== ''){
                    coordsForStations.splice(i, 1);
                    i--;
                }
            }
            updateMap(coordsForStations);
        }
    }

    /**
    * Management of the parkings checkboxs and therefore updates the map
    * depending events.
    */
    var parkingsRadioButton = document.getElementById('inlineRadioParkings');
    if (parkingsRadioButton) {
        parkingsRadioButton.onclick = function() {
            const coordsForParkings = coords.slice();
            for (var i = 0; i < coordsForParkings.length; i++) {
                if(coordsForParkings[i][2] === ""){
                    coordsForParkings.splice(i, 1);
                    i--;
                }
            }
            updateMap(coordsForParkings);
        }
    }


    /**
    * Management of the locations  checkboxs and therefore updates the map
    * depending events.
    */
    var everythingRadioButton = document.getElementById('inlineRadioEverything');
    if (everythingRadioButton) {
        everythingRadioButton.onclick = function() {
            stationsArray = coords.slice();
            updateMap(coords);
        }
    }


    /**
    * Update bubblecontent according the page where the user is.
    */
    function getBubbleContent() {
        // Get page
        let parts = $(location).attr('pathname').split('/');
        let lastSegment = parts.pop() || parts.pop();

        let bubbleText;
        if(lastSegment === 'parking.php'){
            bubbleText = "Parking";
        }else if (lastSegment === 'charging_station.php') {
            bubbleText = "Station de charge";
        }else if (lastSegment === 'location.php') {
            bubbleText = "Station de charge (P)";
        } else{
            bubbleText = "Default";
        }
        return [
            '<div class="bubble">',
            '<p> ' + bubbleText + '</p>',
            '</div>'
        ].join('');
    }

    /**
    *  PLEASE DO NOT SHARE THIS API KEY TO ANYONE.
    *  THIS API KEY BELONGS TO Cédric Gormond.
    */
    var platform = new H.service.Platform({
        'apikey': 'Xqo0gaeBEnKoOkhZ1RahJ0fC6atU6TipvIoAdeiA0us'
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
    * This function manages when a user clicked on a location.
    *
    *@events click on a location
    *
    * WE DID NOT CREATE THIS CODE. IT HAS BEEN CREATED BY "HERE".
    * SOURCE : https://developer.here.com/documentation/examples/maps-js/clustering/custom-cluster-theme
    *
    *@author HERE
    */
    function onMarkerClick(e) {
        // Get position of the "clicked" marker
        var position = e.target.getGeometry(),
        data = "ex"

        // Merge default template with the data and get HTML
        bubbleContent = getBubbleContent(data),
        bubble = onMarkerClick.bubble;

        // For all markers create only one bubble, if not created yet
        if (!bubble) {
            bubble = new H.ui.InfoBubble(position, {
                content: bubbleContent
            });
            ui.addBubble(bubble);
            // Cache the bubble object
            onMarkerClick.bubble = bubble;
        } else {
            bubble.setPosition(position);
            bubble.setContent(bubbleContent);
            bubble.open();
        }

        // Move map's center to a clicked marker
        map.setCenter(position, true);
    }

    /**
    * Display clustered markers on a map
    *
    * Note that the maps clustering module https://js.api.here.com/v3/3.1/mapsjs-clustering.js
    * must be loaded to use the Clustering
    *
    *
    * WE DID NOT CREATE THIS CODE. IT HAS BEEN CREATED BY "HERE".
    * SOURCE : https://developer.here.com/documentation/examples/maps-js/clustering/custom-cluster-theme
    *
    * @author HERE
    * @param {H.Map} map A HERE Map instance within the application
    * @param {Object[]} data Raw data that contains airports' coordinates
    */
    function startClustering(map) {

        data = stationsCoordinates.slice();

        var dataPoints = data.map(function (item) {
            return new H.clustering.DataPoint(item.lat, item.lng);
        });

        var clusteredDataProvider = new H.clustering.Provider(dataPoints, {
            clusteringOptions: {
                // Maximum radius of the neighbourhood
                eps: 32,
                // minimum weight of points required to form a cluster
                minWeight: 2
            },
        });

        // When user clicks on a cluster
        clusteredDataProvider.addEventListener('tap', onMarkerClick);

        var clusteringLayer = new H.map.layer.ObjectLayer(clusteredDataProvider);

        // Update cluster layer reference
        ref = clusteringLayer;

        map.addLayer(clusteringLayer);
    }


    /** WE DID NOT CREATE THIS CODE. IT HAS BEEN CREATED BY "HERE".
    * SOURCE : https://developer.here.com/documentation/examples/maps-js/markers/markers-on-the-map
    *
    *@author HERE
    */
    var defaultLayers = platform.createDefaultLayers();
    var map = new H.Map(document.getElementById('map'),
    defaultLayers.vector.normal.map,{
        center: {lat:47, lng:2.5},
        zoom: 5,
        pixelRatio: window.devicePixelRatio || 1
    });

    // add a resize listener to make sure that the map occupies the whole container
    window.addEventListener('resize', () => map.getViewPort().resize());

    // make the map interactive
    var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

    // Create the default UI components
    var ui = H.ui.UI.createDefault(map, defaultLayers);

    startClustering(map);

    /**
    *
    * WE DID NOT CREATE THIS WHILE CODE. THE MAIN PART HAS BEEN CREATED BY "HERE".
    * SOURCE : https://developer.here.com/documentation/examples/maps-js/markers/markers-on-the-map
    *
    *@author HERE
    *@param {array} newCoordinates coords needed to update markers
    */
    function updateMap(newCoordinates){

        stationsCoordinates = [];
        newCoordinates.forEach((station, i) => {
            stationsCoordinates.push({
                'lng' : parseFloat(station[station.length - 2]),
                'lat' : parseFloat(station[station.length - 1])
            });
        });

        data = stationsCoordinates.slice();


        map.removeObjects(map.getObjects());

        map.removeLayer(ref);

        var dataPoints = data.map(function (item) {
            return new H.clustering.DataPoint(item.lat, item.lng);
        });

        // Create a clustering provider with custom options for clusterizing the input
        var clusteredDataProvider = new H.clustering.Provider(dataPoints, {
            clusteringOptions: {
                eps: 32,
                minWeight: 2
            },
        });

        clusteredDataProvider.addEventListener('tap', onMarkerClick);
        var clusteringLayer = new H.map.layer.ObjectLayer(clusteredDataProvider);
        ref=clusteringLayer;
        map.addLayer(clusteringLayer);
    }
});
