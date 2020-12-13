<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

    $pathClientSparql = 'http://10.0.2.2:3030/locations/sparql';
    $sparqlLocations = new EasyRdf\Sparql\Client($pathClientSparql);
?>
<html prefix="evcs: http://www.example.org/chargingontology#
              rdfs: http://www.w3.org/2000/01/rdf-schema#
              dbp: http://dbpedia.org/property/
              xsd: http://www.w3.org/2001/XMLSchema#">
<head>
    <title>Station operators</title>

    <!-- Here META -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <!-- Customs style scripts -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

    <!-- Customs scripts -->
    <script type="text/javascript" src="map.js"></script>
    <script type="text/javascript" src="table_management.js"></script>
</head>
<body>
    <h1>Station operators</h1>

    <a href="./index.php" id="goBackButton">Return to main page</a>

    <div id="firstOperatorDiv">
        <?php
            // Get the most frequent operator IRI and the number of occurence
            $result = $sparqlLocations->query(
                'SELECT (COUNT(?operator) as ?nb) ?operator
                WHERE {
                  ?station a evcs:ChargingStation. 
                  ?operator a evcs:Operator.
                  ?station evcs:hasOperator ?operator.
                }
                GROUP BY (?operator)
                ORDER BY DESC (?nb)
                LIMIT 1
            ');

            $operator = "";
            $nb = "";
            foreach ($result as $row) {
                $operator = $row->operator;
                $nb = $row->nb;
            }

            // Get the label of the operator IRI
            $result = $sparqlLocations->query(
                'SELECT  ?operatorLabel
                WHERE {
                  <' . $operator . '> a evcs:Operator.
                  <' . $operator . '> rdfs:label ?operatorLabel.
                }');
            
            $operatorLabel = "";
            foreach ($result as $row) {
                $operatorLabel = $row->operatorLabel;
            }

            echo "<p about=\"" . $operator . "\" property=\"dbp:frequency\" content=\"$nb\" datatype=\"xsd:integer\" >The most frequent operator is <b>$operatorLabel</b> : present <b>$nb times</b>.</p>";
        ?>
        
    </div>

    <div id="tableOperatorsDiv">
        <table class="table" id="tableOperators">
            <thead>
                <tr>
                    <th>Operators</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $result = $sparqlLocations->query(
                        'SELECT DISTINCT ?operator ?operatorLabel
                        WHERE {
                          ?operator a evcs:Operator.
                          ?operator rdfs:label ?operatorLabel.
                        }');

                    foreach ($result as $row) {
                        echo "<tr about=\"" . $row->operator . "\" typeof=\"evcs:Operator\">" .
                                "<td property=\"rdfs:label\">" . $row->operatorLabel . "</td>" .
                             "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <p id="operatorNumRows">Total number of rows: <?= $result->numRows() ?></p>
    
</body>
</html>
