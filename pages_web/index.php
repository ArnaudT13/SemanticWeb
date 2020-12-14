<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');

    $pathClientSparql = 'http://localhost:3030/locations/sparql';
    $sparqlChargingStation = new EasyRdf\Sparql\Client($pathClientSparql);
    $sparqlINSEE = new EasyRdf\Sparql\Client('http://rdf.insee.fr/sparql');
?>
<html>
<head>
    <title>Main location page</title>

    <meta name="Author" content="Arnaud Tavernier" />
    <meta name="Author" content="Cedric Gormond" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <!-- Here META -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <!-- Customs style scripts -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Main page</h1>
    <div id="divNavigation">
        <ul id="navigation">
            <li><a href="location.php">Location Page</a></li>
            <li><a href="charging_station.php">Charging stations Page</a></li>
            <li><a href="parking.php">Parking Page</a></li>
            <li><a href="operator.php">Station operators Page</a></li>
            <li><a href="retrieve_city.php">Retrieve city Page</a></li>
        </ul>
    </div>
</body>
<script>
    $('#navigation a').hover( function() {
        $(this).css('font-size', '25px');
    },function() {
        $(this).css('font-size', '18px');
    });
</script>
</html>
