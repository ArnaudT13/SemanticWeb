<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('park', 'http://www.example.org/parkingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');

    $pathClientSparql = 'http://localhost:3030/locations/sparql';
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

    <meta name="Author" content="Arnaud Tavernier" />
    <meta name="Author" content="Cedric Gormond" />

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

    <!-- Map -->


    <div id="firstBlockContainer">

            <div id="map"></div>

        <!-- Retrive city with postal code -->
        <div id="mapDiv">
            <h2>Find the nearest station from parking coordinates </h2>

            <form action="" method="post" onSubmit="return checkCode(true);">

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="">(Lat, Lng)</span>
                  </div>
                  <input id="latInput" type="number" step="any" name="latInputName" class="form-control">
                  <input id="lonInput" type="number" step="any" name="longInputName" class="form-control" >
                </div>
                <button type="submit" class="btn btn-primary ">Valider</button>
            </form>

            <table id="inseeCodeTable">
                <?php
                    require_once "utils/distance.php";

                    $result = "";
                    if (isset($_REQUEST['latInputName']) && isset($_REQUEST['longInputName'])){

                        $result = $sparqlLocations->query(
                            'SELECT ?station ?stationLabel ?operator ?operatorLabel ?long ?lat ?codeINSEE ?zonePostale ?city ?paymentMode ?paymentModeLabel
                            WHERE {
                                ?station a evcs:ChargingStation.
                                ?station evcs:hasOperator ?operator.
                                ?operator rdfs:label ?operatorLabel.
                                ?station evcs:hasPaymentMode ?paymentMode.
                                ?paymentMode rdfs:label ?paymentModeLabel.
                                ?station rdfs:label ?stationLabel.
                                ?station geo:long ?long.
                                ?station geo:lat ?lat.
                                ?station igeo:codeINSEE ?codeINSEE.
                                ?station dbp:postalCode ?zonePostale.
                                ?station dbp:cityName ?city.
                            }'
                        );


                        $rowNumber = $result->numRows();

                        if($rowNumber == 0){
                            echo "<td colspan=\"4\" style=\"text-align: center;\">Aucune donnée ne correspond à \"" . $_REQUEST['longInputName'] . "\"</td>";
                        }
                        else{
                            $minStationDistance = "";
                            $minDistance = 999999999;

                            echo "<table>";
                            foreach ($result as $row) {
                                //echo  gettype((float)floatval($_REQUEST['longInputName'])).' ';
                                //echo (float) $row->lat->getValue();
                                //echo (float) $row->long->getValue();

                                $currentDistance = distance((float) $row->lat->getValue(), (float) $row->long->getValue(), floatval($_REQUEST['latInputName']),floatval($_REQUEST['longInputName']), "K");

                                if ($currentDistance <  $minDistance) {
                                    $minDistance = $currentDistance;
                                    $minStationDistance =
                                    "<tbody about=\"" . $row->station . "\" typeof=\"evcs:ChargingStation\">".
                                            "\t"."<td property=\"rdfs:label\">" . $row->stationLabel . "</td>" ."\n".
                                            "\t"."<td property=\"igeo:codeINSEE\">" . $row->codeINSEE . "</td>" . "\n".
                                            "\t"."<td property=\"evcs:hasPaymentMode\" href=\"" . $row->paymentMode . "\">" . $row->paymentModeLabel . "</td>" . "\n".
                                            "\t"."<td property=\"dbp:cityName\">" . $row->city . "</td>" . "\n".
                                            "\t"."<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" . "\n".
                                            "\t"."<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" . "\n".
                                            "\t"."<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" . "\n".

                                    "</tbody>";

                                }
                            }
                            echo $minStationDistance;
                            echo "</table>";
                            echo "<p> The nearest station is <b> ". $minDistance . " </b> km away from the coordinates (" . $_REQUEST['latInputName'] . ", ". $_REQUEST['longInputName'] . "). </p>";
                        }
                    }
                    else{
                        echo "<td colspan=\"4\" style=\"text-align: center;\">Empty</td>";
                    }
                ?>

            </table>
        </div>
    </div>



    <!-- Table of parkings -->
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

                    echo "<tr about=\"" . $row->parking . "\" typeof=\"park:Parking\">" ."\n".
                            "\t"."<td property=\"rdfs:label\">" . $row->parkingLabel . "</td>" ."\n".
                            "\t"."<td property=\"park:hasParkingType\" href=\"" . $row->parkingType . "\">" . $row->parkingTypeLabel . "</td>" ."\n".
                            "\t"."<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" ."\n".
                            "\t"."<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" ."\n".
                            "\t"."<td property=\"park:hasCapacity\" content=\"" . $capacity_ . "\" datatype=\"xsd:decimal\">" . $capacity_ . "</td>" ."\n".
                            "\t"."<td property=\"dbp:cityName\">" . $row->city . "</td>" ."\n".
                            "\t"."<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" ."\n".
                         "</tr>". "\n";
                }
            ?>
            <script>
                const coords = <?php echo json_encode($array2return); ?>; // Don't forget the extra semicolon!
                coords2map(coords);
            </script>
        </tbody>
    </table>
    <p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
