<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('igeo', 'http://rdf.insee.fr/def/geo#');

    $sparqlINSEE = new EasyRdf\Sparql\Client('http://rdf.insee.fr/sparql');
?>
<html>
<head>
    <title>Parking locations</title>

    <!-- Here META -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <!-- Customs style scripts -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

</head>
<body>
    <h1>Insee data</h1>

    <form action="" method="post">
        <div id="wrapper">
            <p>
                <label> Please enter insee code </label>
                <input type="text" name="InseeCode">
                <input type="submit" value="Search">
            </p>
        </div>
    </form>

    <?php

        $result = "";

        if (isset($_REQUEST['InseeCode'])){
            $result = $sparqlINSEE->query(
                'SELECT DISTINCT ?nom WHERE {
                    ?commune igeo:codeINSEE "' . $_REQUEST['InseeCode'] . '".
                    ?commune igeo:nom ?nom .
                }'
        );


        }

        foreach ($result as $row) {
            echo '<p>' . $row->nom . '</p>';
        }
        
    ?>

</body>
</html>