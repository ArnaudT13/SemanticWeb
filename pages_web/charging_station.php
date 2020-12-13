<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

    $pathClientSparql = 'http://10.0.2.2:3030/locations/sparql';
    $sparqlLocations = new EasyRdf\Sparql\Client($pathClientSparql);
?>
<html prefix="geo: http://www.w3.org/2003/01/geo/wgs84_pos#
              evcs: http://www.example.org/chargingontology#
              rdfs: http://www.w3.org/2000/01/rdf-schema#
              igeo: http://rdf.insee.fr/def/geo#
              dbp: http://dbpedia.org/property/
              xsd: http://www.w3.org/2001/XMLSchema#">
<head>
    <title>Charging station locations</title>

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
    <h1>Stations</h1>

    <a href="./index.php" id="goBackButton">Return to main page</a>

    <table class="table" id="table_locations">
        <thead>
            <tr>
                <th>Station</th>
                <th>Operateur</th>
                <th style="width: 110px;">Code INSEE</th>
                <th style="width: 110px;">Paiement</th>
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
                    }');

                foreach ($result as $row) {

                    $temp = array(
                        utf8_encode($row->stationLabel),
                        utf8_encode($row->operatorLabel),
                        utf8_encode($row->codeINSEE),
                        utf8_encode($row->paymentModeLabel),
                        utf8_encode($row->zonePostale),
                        utf8_encode($row->city),
                        utf8_encode($row->long),
                        utf8_encode($row->lat)
                    );
                    array_push($array2return, $temp);

                    unset($foo);

                    echo "<tr about=\"" . $row->station . "\" typeof=\"evcs:ChargingStation\">" .
                            "<td property=\"rdfs:label\">" . $row->stationLabel . "</td>" .
                            "<td property=\"evcs:hasOperator\" href=\"" . $row->operator . "\">" . $row->operatorLabel . "</td>" .
                            "<td property=\"igeo:codeINSEE\">" . $row->codeINSEE . "</td>" .
                            "<td property=\"evcs:hasPaymentMode\" href=\"" . $row->paymentMode . "\">" . $row->paymentModeLabel . "</td>" .
                            "<td property=\"dbp:cityName\">" . $row->city . "</td>" .
                            "<td property=\"dbp:postalCode\">" . $row->zonePostale . "</td>" .
                            "<td property=\"geo:long\" content=\"" . $row->long . "\" datatype=\"xsd:decimal\">" . $row->long . "</td>" .
                            "<td property=\"geo:lat\" content=\"" . $row->lat . "\" datatype=\"xsd:decimal\">" . $row->lat . "</td>" .
                         "</tr>";
                }


            ?>

            <script>
                var coords = <?php echo json_encode($array2return); ?>; // Don't forget the extra semicolon!
                coords2map(coords);
            </script>
            <div id="map"></div>
        </tbody>
    </table>
    <p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
