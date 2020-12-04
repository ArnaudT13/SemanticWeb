<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');
    

    $sparqlChargingStation = new EasyRdf\Sparql\Client('http://10.0.2.2:3030/charging_station/sparql');
    $sparqlINSEE = new EasyRdf\Sparql\Client('http://rdf.insee.fr/sparql');
?>
<html>
<head>
  <title>EasyRdf Basic Sparql Example</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Stations</h1>

<table>
    <thead>
        <tr>
            <th>Station</th>
            <th>Operateur</th>
            <th>Longitude</th>
            <th>Latitude</th>
            <th>Code INSEE</th>
            <th>Paiement</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $result = $sparqlChargingStation->query(
                'SELECT ?stationLabel ?operatorLabel ?long ?lat ?codeINSEE ?paymentModeLabel WHERE { ?station a evcs:ChargingStation. ?station evcs:hasOperator ?operator. ?operator rdfs:label ?operatorLabel. ?station evcs:hasPaymentMode ?paymentMode. ?paymentMode rdfs:label ?paymentModeLabel. ?station rdfs:label ?stationLabel. ?station geo:long ?long. ?station geo:lat ?lat. ?station igeo:codeINSEE ?codeINSEE.}');

            foreach ($result as $row) {
                echo "<tr>" . 
                        "<td>" . $row->stationLabel . "</td>" .
                        "<td>" . $row->operatorLabel . "</td>" .
                        "<td>" . $row->long . "</td>" .
                        "<td>" . $row->lat . "</td>" .
                        "<td>" . $row->codeINSEE . "</td>" .
                        "<td>" . $row->paymentModeLabel . "</td>" .
                     "</tr>";

                /*
                $codeINSEE = $sparqlINSEE->query('SELECT DISTINCT ?name WHERE {?town igeo:codeINSEE "' . $row->codeINSEE . '". ?town igeo:nom ?name. }');
                
                echo    "<td>" . $codeINSEE[0]->name  . "</td>" . 
                    "</tr>";
                */
            }
        ?>
    </tbody>
</table>
<p>Total number of rows: <?= $result->numRows() ?></p>

</body>
</html>
