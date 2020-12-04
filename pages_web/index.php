<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');

    $sparql = new EasyRdf\Sparql\Client('http://10.0.2.2:3030/charging_station/sparql');
?>
<html>
<head>
  <title>EasyRdf Basic Sparql Example</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>EasyRdf Basic Sparql Example</h1>

<h2>List of countries</h2>
<table>
    <thead>
        <tr>
            <th>Station</th>
            <th>Longitude</th>
            <th>Latitude</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $result = $sparql->query(
                'SELECT ?station ?long ?lat WHERE { ?station a evcs:ChargingStation . ?station geo:long ?long . ?station geo:lat ?lat .}'
            );
            foreach ($result as $row) {
                echo "<tr>" . 
                        "<td>" . $row->station . "</td>" .
                        "<td>" . $row->long . "</td>" .
                        "<td>" . $row->lat . "</td>" .
                     "</tr>";
            }
        ?>
    </tbody>
</table>
<p>Total number of countries: <?= $result->numRows() ?></p>

</body>
</html>
