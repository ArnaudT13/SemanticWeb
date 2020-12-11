<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('park', 'http://www.example.org/parkingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');

    $pathClientSparql = 'http://localhost:3030/parkings/sparql';
    $sparqlChargingStation = new EasyRdf\Sparql\Client($pathClientSparql);
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
    <h1>Parkings</h1>


    <table class="table" id="table_locations">
        <thead>
            <tr>
                <th>Parking</th>
                <th>Type</th>
                <th style="width: 110px;">Longitude</th>
                <th style="width: 110px;">Latitude</th>
                <th style="width: 110px;">Code INSEE</th>
                <th style="width: 110px;">Paiement</th>
                <th>Ville</th>
                <th style="width: 110px;">Code Postal</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $array2return = [];
                $result = $sparqlChargingStation->query(
                    'SELECT 
                    WHERE {
                        ?parking a park:Parking.
                        ?parking park:hasCapacity ?capacity.
                        ?parking rdfs:label ?parkingLabel.
                        ?parking park:hasParkingType ?parkingType.
                        ?parkingType rdfs:label ?parkingTypeLabel.
                        ?parking geo:long ?long.
                        ?parking geo:lat ?lat.
                        ?parking igeo:codeINSEE ?codeINSEE.
                        ?parking igeo:ZonePostale ?zonePostale.
                    }');

                foreach ($result as $row) {

                    $temp = array(
                        utf8_encode($row->stationLabel),
                        utf8_encode($row->operatorLabel),
                        utf8_encode($row->long) ,
                        utf8_encode($row->lat) ,
                        utf8_encode($row->codeINSEE),
                        utf8_encode($row->paymentModeLabel),
                        utf8_encode($row->zonePostale),
                        utf8_encode($row->city)
                    );
                    array_push($array2return, $temp);

                    unset($foo);

                    echo "<tr >" .
                            "<td>" . $row->stationLabel . "</td>" .
                            "<td>" . $row->operatorLabel . "</td>" .
                            "<td>" . $row->long . "</td>" .
                            "<td>" . $row->lat . "</td>" .
                            "<td>" . $row->codeINSEE . "</td>" .
                            "<td>" . $row->paymentModeLabel . "</td>" .
                            "<td>" . $row->city . "</td>" .
                            "<td>" . $row->zonePostale . "</td>" .
                         "</tr>";
                }

                $test = ($array2return);


            ?>
            <script>
                var coords = <?php echo json_encode($test); ?>; // Don't forget the extra semicolon!
                coords2map(coords);
            </script>
            <div style="width: 640px; height: 480px" id="map"></div>
        </tbody>
    </table>
    <p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
