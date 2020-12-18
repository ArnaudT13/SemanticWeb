<?php
set_include_path("./lib/");

require_once "EasyRdf.php";

\EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
\EasyRdf\RdfNamespace::set('park', 'http://www.example.org/parkingontology#');
\EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
\EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
\EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');
\EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

$pathClientSparql = 'http://localhost:3030/locations/sparql';
$sparqlLocations = new EasyRdf\Sparql\Client($pathClientSparql);
?>
<html prefix="geo: http://www.w3.org/2003/01/geo/wgs84_pos#
park: http://www.example.org/parkingontology#
evcs: http://www.example.org/chargingontology#
rdfs: http://www.w3.org/2000/01/rdf-schema#
igeo: http://rdf.insee.fr/def/geo#
dbp: http://dbpedia.org/property/
xsd: http://www.w3.org/2001/XMLSchema#">
<head>
    <title>Locations</title>

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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

    <!-- HERE API scripts -->
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-clustering.js"></script>

    <!-- Customs scripts -->
    <script type="text/javascript" src="js/map.js"></script>
    <script type="text/javascript" src="js/table_management.js"></script>
</head>
<body>
    <h1>Locations (chargers & parkings)</h1>

    <a href="./index.php" id="goBackButton">Return to main page</a>

    <!-- Map & checkbox -->
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div id="map"></div>
            </div>
            <div class="sol-sm">
                <p> It may take time to load the markers on the HERE map. </p>
                <div class="custom-control custom-checkbox pl-0">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEverything" checked>
                        <label class="form-check-label" for="inlineRadio1">Display everything</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioStations">
                        <label class="form-check-label" for="inlineRadio2">Display stations</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioParkings">
                        <label class="form-check-label" for="inlineRadio3">Display parkings</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Table of locations -->
    <table class="table" id="table_locations">
        <thead>
            <tr>
                <th>Location</th>
                <th>Operator</th>
                <th>Parking Type</th>
                <th style="width: 110px;">Capacity</th>
                <th style="width: 110px;">Payment Mode</th>
                <th style="width: 110px;">INSEE Code</th>
                <th>City</th>
                <th style="width: 110px;">Zip Code</th>
                <th style="width: 110px;">Longitude</th>
                <th style="width: 110px;">Latitude</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $array2return = [];

            $result = $sparqlLocations->query(
                'SELECT
                ?parking
                ?station
                ?locationLabel
                ?operator
                ?operatorLabel
                ?parkingType
                ?parkingTypeLabel
                ?codeINSEE
                ?zonePostale
                ?city
                ?paymentMode
                ?paymentModeLabel
                ?capacity
                ?long
                ?lat

                WHERE {
                {
                # Label
                ?parking a park:Parking.
                ?parking rdfs:label ?locationLabel.

                # Type
                ?parking park:hasParkingType ?parkingType.
                ?parkingType rdfs:label ?parkingTypeLabel.

                ?parking geo:long ?long.
                ?parking geo:lat ?lat.

                ?parking dbp:cityName ?city.
                ?parking dbp:postalCode ?zonePostale.

                OPTIONAL{
                ?parking park:hasCapacity ?capacity.
                }

                }
                UNION
                {
                # Label
                ?station a evcs:ChargingStation.
                ?station rdfs:label ?locationLabel.

                # Operator
                ?station evcs:hasOperator ?operator.
                ?operator rdfs:label ?operatorLabel.

                # Coords
                ?station geo:long ?long.
                ?station geo:lat ?lat.

                # Payment mode
                ?station evcs:hasPaymentMode ?paymentMode.
                ?paymentMode rdfs:label ?paymentModeLabel.

                # City
                ?station igeo:codeINSEE ?codeINSEE.
                ?station dbp:postalCode ?zonePostale.
                ?station dbp:cityName ?city.
                }
                }');

                foreach ($result as $row) {
                    /*
                    These multiple "if" blocks avoid PHP errors with empty
                    strings
                    */
                    $capacity_ = "";
                    if(isset($row->capacity)){
                        $capacity_ = $row->capacity;
                    }

                    $codeINSEE_ = "";
                    if(isset($row->codeINSEE)){
                        $codeINSEE_ = $row->codeINSEE;
                    }

                    $paymentModeLabel_ = "";
                    if(isset($row->paymentModeLabel)){
                        $paymentModeLabel_ = $row->paymentModeLabel;
                    }

                    $operatorLabel_ = "";
                    if(isset($row->operatorLabel)){
                        $operatorLabel_ = $row->operatorLabel;
                    }

                    $parkingTypeLabel_ = "";
                    if(isset($row->parkingTypeLabel)){
                        $parkingTypeLabel_ = $row->parkingTypeLabel;
                    }

                    $temp = array(
                        utf8_encode($row->locationLabel),
                        utf8_encode($operatorLabel_),
                        utf8_encode($parkingTypeLabel_),
                        utf8_encode($codeINSEE_),
                        utf8_encode($row->zonePostale),
                        utf8_encode($row->city),
                        utf8_encode($paymentModeLabel_),
                        utf8_encode($capacity_),
                        utf8_encode($row->long) ,
                        utf8_encode($row->lat)
                    );
                    array_push($array2return, $temp);

                    unset($foo);

                    if(isset($row->parking)){
                        echo "<tr about=\"" . $row->parking . "\" typeof=\"park:Parking\">" . "\n".
                        "\t"."<td property=\"rdfs:label\">" . $row->locationLabel . "</td>" . "\n".
                        "\t"."<td>" . $operatorLabel_ . "</td>" . "\n".
                        "\t"."<td property=\"park:hasParkingType\" href=\"" . $row->parkingType . "\">" . $parkingTypeLabel_ . "</td>" . "\n".
                        "\t"."<td property=\"park:hasCapacity\" content=\"" . $capacity_ . "\" datatype=\"xsd:decimal\">" . $capacity_ . "</td>" . "\n".
                        "\t"."<td>" . $paymentModeLabel_ . "</td>" . "\n".
                        "\t"."<td>" . $codeINSEE_ . "</td>" . "\n".
                        "\t"."<td property=\"dbp:cityName\">" . $row->city . "</td>" . "\n".
                        "\t"."<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" . "\n".
                        "\t"."<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" . "\n".
                        "\t"."<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" . "\n".
                        "</tr>" . "\n";
                    }
                    else{
                        echo "<tr about=\"" . $row->station . "\" typeof=\"evcs:ChargingStation\">" . "\n".
                        "\t"."<td property=\"rdfs:label\">" . $row->locationLabel . "</td>" . "\n".
                        "\t"."<td property=\"evcs:hasOperator\" href=\"" . $row->operator . "\">" . $operatorLabel_ . "</td>" . "\n".
                        "\t"."<td>" . $parkingTypeLabel_ . "</td>" . "\n".
                        "\t"."<td>" . $capacity_ . "</td>" . "\n".
                        "\t"."<td property=\"evcs:hasPaymentMode\" href=\"" . $row->paymentMode . "\">" . $paymentModeLabel_ . "</td>" . "\n".
                        "\t"."<td property=\"igeo:codeINSEE\">" . $codeINSEE_ . "</td>" . "\n".
                        "\t"."<td property=\"dbp:cityName\">" . $row->city . "</td>" . "\n".
                        "\t"."<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" . "\n".
                        "\t"."<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" . "\n".
                        "\t"."<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" . "\n".
                        "</tr>" . "\n";
                    }
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
