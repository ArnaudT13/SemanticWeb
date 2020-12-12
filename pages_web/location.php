<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('park', 'http://www.example.org/parkingontology#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');

    $pathClientSparql = 'http://localhost:3030/locations/sparql';
    $sparqlLocations = new EasyRdf\Sparql\Client($pathClientSparql);
?>
<html>
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
    <h1>Locations (chargers & parking)</h1>



    <div class="container">
        <div class="row">
            <div class="col-sm">
                <div id="map"></div>
            </div>
            <div class="sol-sm">

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




    <table class="table" id="table_locations">
        <thead>
            <tr>
                <th>Location</th>
                <th>Operator</th>
                <th>Parking Type</th>
                <th style="width: 110px;">Capacity</th>
                <th style="width: 110px;">Paiement</th>
                <th style="width: 110px;">Code INSEE</th>
                <th>Ville</th>
                <th style="width: 110px;">Code Postal</th>
                <th style="width: 110px;">Longitude</th>
                <th style="width: 110px;">Latitude</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $array2return = [];
                
                $result = $sparqlLocations->query(
                    'SELECT
                        ?locationLabel
                        ?operatorLabel
                        ?parkingTypeLabel
                        ?codeINSEE
                        ?zonePostale
                        ?city
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

                          ?parking igeo:Commune ?city.
                          ?parking igeo:ZonePostale ?zonePostale.

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
                          ?station igeo:ZonePostale ?zonePostale.
                          ?station igeo:Commune ?city.
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

                    echo "<tr >" .
                            "<td>" . $row->locationLabel . "</td>" .
                            "<td>" . $operatorLabel_ . "</td>" .
                            "<td>" . $parkingTypeLabel_ . "</td>" .
                            "<td>" . $capacity_ . "</td>" .
                            "<td>" . $paymentModeLabel_ . "</td>" .
                            "<td>" . $codeINSEE_ . "</td>" .
                            "<td>" . $row->city . "</td>" .
                            "<td>" . $row->zonePostale . "</td>" .
                            "<td>" . $row->long . "</td>" .
                            "<td>" . $row->lat . "</td>" .
                         "</tr>";
                }


            ?>
            <script>
                const coords = <?php echo json_encode($array2return); ?>; // Don't forget the extra semicolon!

                coords2map(coords);


                /*
                document.addEventListener("DOMContentLoaded", function() {

                }); */
            </script>
        </tbody>
    </table>


    <p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
