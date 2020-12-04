<?php
    /**
     * Making a SPARQL SELECT query
     *
     * This example creates a new SPARQL client, pointing at the
     * dbpedia.org endpoint. It then makes a SELECT query that
     * returns all of the countries in DBpedia along with an
     * english label.
     *
     * Note how the namespace prefix declarations are automatically
     * added to the query.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2020 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */

    require_once realpath(__DIR__.'/..')."/vendor/autoload.php";
    require_once __DIR__."/html_tag_helpers.php";

    // Setup some additional prefixes for DBpedia
    \EasyRdf\RdfNamespace::set('dbc', 'http://dbpedia.org/resource/Category:');
    \EasyRdf\RdfNamespace::set('dbpedia', 'http://dbpedia.org/resource/');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('evcs', 'http://www.example.org/chargingontology#');

    $sparql = new \EasyRdf\Sparql\Client('http://localhost:3030/charging_station/sparql');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Page Title</title>
    </head>
    <body>

        <h1>My First Heading</h1>
        <p>My first paragraph.</p>

        <?php
            $result = $sparql->query(
                "SELECT ?station ?long ?lat" .
                "WHERE {" .
                "    ?station a evcs:ChargingStation ." .
                "    ?station geo:long ?long." .
                "    ?station geo:lat ?lat." . 
                "}"
            );

            foreach ($result as $row) {
                echo "<li>".link_to($row->label, $row->country)."</li>\n";
            }
        ?>

    </body>
</html>