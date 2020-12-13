<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('park', 'http://www.example.org/parkingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

    $pathClientSparql = 'http://10.0.2.2:3030/locations/sparql';
    $sparqlLocations = new EasyRdf\Sparql\Client($pathClientSparql);
?>
<html prefix="geo: http://www.w3.org/2003/01/geo/wgs84_pos#
              park: http://www.example.org/parkingontology#
              rdfs: http://www.w3.org/2000/01/rdf-schema#
              igeo: http://rdf.insee.fr/def/geo#
              dbp: http://dbpedia.org/property/
              xsd: http://www.w3.org/2001/XMLSchema#">
<head>
    <title>Parking locations</title>

    <!-- Here META -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <!-- Customs style scripts -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

    <!-- HERE API scripts -->
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-clustering.js"></script>

    <!-- Customs scripts -->
    <script type="text/javascript" src="map.js"></script>
    <script type="text/javascript" src="table_management.js"></script>
</head>
<body>
    <h1>Parkings</h1>

    <a href="./index.php" id="goBackButton">Return to main page</a>

    <table class="table" id="table_locations">
        <thead>
            <tr>
                <th>Parking</th>
                <th>Type</th>
                <th style="width: 110px;">Longitude</th>
                <th style="width: 110px;">Latitude</th>
                <th style="width: 110px;">Capacity</th>
                <th>Ville</th>
                <th style="width: 110px;">Code Postal</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $array2return = [];
                $result = $sparqlLocations->query(
                    'SELECT ?parking ?parkingLabel ?parkingType ?parkingTypeLabel ?capacity ?long ?lat ?zonePostale ?city
                    WHERE {
                      ?parking a park:Parking.
                      ?parking rdfs:label ?parkingLabel.
                      ?parking park:hasParkingType ?parkingType.
                      ?parking geo:long ?long.
                      ?parking geo:lat ?lat.
                      ?parking dbp:cityName ?city.
                      ?parking dbp:postalCode ?zonePostale.
                      ?parkingType rdfs:label ?parkingTypeLabel.


                      OPTIONAL{
                         ?parking park:hasCapacity ?capacity.
                      }


                    }');

                foreach ($result as $row) {
                    $capacity_ = "";
                    if(isset($row->capacity)){
                        $capacity_ = $row->capacity;
                    }
                    $temp = array(
                        utf8_encode($row->parkingLabel),
                        utf8_encode($row->parkingTypeLabel),
                        utf8_encode($capacity_),
                        utf8_encode($row->zonePostale),
                        utf8_encode($row->city),
                        utf8_encode($row->long) ,
                        utf8_encode($row->lat)
                    );
                    array_push($array2return, $temp);

                    unset($foo);

                    echo "<tr about=\"" . $row->parking . "\" typeof=\"park:Parking\">" .
                            "<td property=\"rdfs:label\">" . $row->parkingLabel . "</td>" .
                            "<td property=\"park:hasParkingType\" href=\"" . $row->parkingType . "\">" . $row->parkingTypeLabel . "</td>" .
                            "<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" .
                            "<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" .
                            "<td property=\"park:hasCapacity\" content=\"" . $capacity_ . "\" datatype=\"xsd:decimal\">" . $capacity_ . "</td>" .
                            "<td property=\"dbp:cityName\">" . $row->city . "</td>" .
                            "<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" .
                         "</tr>";
                }


            ?>
            <script>
                const coords = <?php echo json_encode($array2return); ?>; // Don't forget the extra semicolon!
                coords2map(coords);
            </script>
            <div id="map"></div>
        </tbody>
    </table>
    <p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
