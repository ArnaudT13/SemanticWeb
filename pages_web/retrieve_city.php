<?php
    set_include_path("./lib/");

    require_once "EasyRdf.php";

    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

    $sparqlDbpedia = new EasyRdf\Sparql\Client('http://dbpedia.org/sparql');

    // Source : http://dbpedia.org/ontology/
?>
<html>
<head>
    <title>City data</title>

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
    <h1>Retrieve cities</h1>

    <a href="./index.php" id="goBackButton">Return to main page</a>

    <div id="firstBlockContainer">

        <!-- Retrive city with insee code -->
        <div id="inseeCodeDiv">
            <h2>Retrive city with insee code</h2>
            <form action="" method="post" onSubmit="return checkCode(true);">
                <div>
                    <p>
                        <label> Please enter insee code </label>
                        <input type="text" id="inputInseeCode" name="InseeCode"  placeholder="ex: 01004, ex: 42095" /Required>
                        <input type="submit" id="submitInseeCode" value="Search">
                        <button id="clearInseeTable">Clear</button>
                    </p>
                </div>
            </form>
            <table id="inseeCodeTable">
                <thead>
                    <tr>
                        <th>City</th>
                        <th style="width: 110px;">Longitude</th>
                        <th style="width: 110px;">Latitude</th>
                        <th style="width: 110px;">Postal Code</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                    $result = "";
                    if (isset($_REQUEST['InseeCode'])){
                        $result = $sparqlDbpedia->query(
                            "SELECT DISTINCT ?settlementLabel ?lat ?long ?postalCode
                            WHERE
                            {
                                ?settlement a dbo:Settlement.
                                ?settlement dbo:inseeCode '" . $_REQUEST['InseeCode'] . "'.
                                ?settlement rdfs:label ?settlementLabel.
                                ?settlement geo:lat ?lat.
                                ?settlement geo:long ?long.
                                ?settlement dbo:postalCode ?postalCode.
                                FILTER (lang(?settlementLabel) = 'fr')
                            }"
                        );

                        $rowNumber = $result->numRows();

                        if($rowNumber == 0){
                            echo "<td colspan=\"4\" style=\"text-align: center;\">Aucune donnée ne correspond à \"" . $_REQUEST['InseeCode'] . "\"</td>";
                        }
                        else{
                            foreach ($result as $row) {
                                echo "<tr >" .
                                        "<td>" . $row->settlementLabel . "</td>" .
                                        "<td>" . $row->long . "</td>" .
                                        "<td>" . $row->lat . "</td>" .
                                        "<td>" . $row->postalCode . "</td>" .
                                     "</tr>";
                            }
                        }
                    }
                    else{
                        echo "<td colspan=\"4\" style=\"text-align: center;\">Empty</td>";
                    }
                ?>
                </tbody>
            </table>
        </div> 

        <div id="borderDiv">
        </div>


        <!-- Retrive city with postal code -->
        <div id="postalCodeDiv">
            <h2>Retrive cities with postal code</h2>
            <form action="" method="post" onSubmit="return checkCode(false);">
                <div>
                    <p>
                        <label> Please enter insee code </label>
                        <input type="text" id="inputPostalCode" name="PostalCode" placeholder="ex: 01000, ex: 42000" /Required>
                        <input type="submit" id="submitPostalCode" value="Search">
                        <button id="clearPostalTable">Clear</button>
                    </p>
                </div>
            </form>

            <table id="postalCodeTable">
                <thead>
                    <tr>
                        <th>City</th>
                        <th style="width: 110px;">Longitude</th>
                        <th style="width: 110px;">Latitude</th>
                        <th style="width: 110px;">Insee Code</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                    $result = "";
                    if (isset($_REQUEST['PostalCode'])){
                        $result = $sparqlDbpedia->query(
                            "SELECT DISTINCT ?settlementLabel ?lat ?long ?inseeCode
                            WHERE
                            {
                                ?settlement a dbo:Settlement.
                                ?settlement dbo:inseeCode ?inseeCode.
                                ?settlement rdfs:label ?settlementLabel.
                                ?settlement geo:lat ?lat.
                                ?settlement geo:long ?long.
                                ?settlement dbo:postalCode ?postalCode.
                                FILTER (lang(?settlementLabel) = 'fr' && contains(lcase(str(?postalCode)),'" . $_REQUEST['PostalCode'] . "'))
                            }"
                        );

                        $rowNumber = $result->numRows();

                        if($rowNumber == 0){
                            echo "<td colspan=\"4\" style=\"text-align: center;\">Aucune donnée ne correspond à \"" . $_REQUEST['PostalCode'] . "\"</td>";
                        }
                        else{
                            foreach ($result as $row) {
                                echo "<tr >" .
                                        "<td>" . $row->settlementLabel . "</td>" .
                                        "<td>" . $row->long . "</td>" .
                                        "<td>" . $row->lat . "</td>" .
                                        "<td>" . $row->inseeCode . "</td>" .
                                     "</tr>";
                            }
                        }
                    }
                    else{
                        echo "<td colspan=\"4\" style=\"text-align: center;\">Empty</td>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>


    <div id="secondBlockContainer">

        <!-- Retrive city with name -->
        <div id="cityNameLikeDiv">
                <h2>Retrive cities with partial name</h2>
                <form action="" method="post">
                    <div>
                        <p>
                            <label> Please enter city name </label>
                            <input type="text" name="CityNameLike" placeholder="ex: saint-étienne, ex: paris" /Required>
                            <input type="submit" value="Search">
                            <button id="clearCityNameLikeTable">Clear</button>
                        </p>
                    </div>
                </form>
                <table id="cityNameLikeTable">
                    <thead>
                        <tr>
                            <th style="width: 400px;">City</th>
                            <th style="width: 110px;">Longitude</th>
                            <th style="width: 110px;">Latitude</th>
                            <th style="width: 110px;">Postal Code</th>
                            <th style="width: 110px;">Insee Code</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php

                        $result = "";
                        if (isset($_REQUEST['CityNameLike'])){
                            $result = $sparqlDbpedia->query(
                                "SELECT DISTINCT ?settlementLabel ?lat ?long ?postalCode ?inseeCode
                                WHERE
                                {
                                    ?settlement a dbo:Settlement.
                                    ?settlement dbo:inseeCode ?inseeCode.
                                    ?settlement rdfs:label ?settlementLabel.
                                    ?settlement geo:lat ?lat.
                                    ?settlement geo:long ?long.
                                    ?settlement dbo:postalCode ?postalCode.
                                    FILTER (lang(?settlementLabel) = 'fr' && contains(lcase(str(?settlementLabel)), '" . strtolower($_REQUEST['CityNameLike']) . "'))
                                }"
                            );

                            $rowNumber = $result->numRows();

                            if($rowNumber == 0){
                                echo "<td colspan=\"5\" style=\"text-align: center;\">Aucune donnée ne correspond à \"" . $_REQUEST['CityNameLike'] . "\"</td>";
                            }
                            else{
                                foreach ($result as $row) {
                                    echo "<tr >" .
                                            "<td>" . $row->settlementLabel . "</td>" .
                                            "<td>" . $row->long . "</td>" .
                                            "<td>" . $row->lat . "</td>" .
                                            "<td>" . $row->postalCode . "</td>" .
                                            "<td>" . $row->inseeCode . "</td>" .
                                         "</tr>";
                                }
                            }
                        }
                        else{
                            echo "<td colspan=\"5\" style=\"text-align: center;\">Empty</td>";
                        }
                    ?>
                    </tbody>
                </table>
            </div> 
        </div>
</body>




    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <script>
        $('#clearInseeTable').click(function() {
            $('#inseeCodeTable tbody').empty();
            $('#inseeCodeTable tbody').html("<td colspan=\"5\" style=\"text-align: center;\">Empty</td>");
        });
        $('#clearPostalTable').click(function() {
            $('#postalCodeTable tbody').empty();
            $('#postalCodeTable tbody').html("<td colspan=\"5\" style=\"text-align: center;\">Empty</td>");
        });
        $('#clearCityNameLikeTable').click(function() {
            $('#cityNameLikeTable tbody').empty();
            $('#cityNameLikeTable tbody').html("<td colspan=\"5\" style=\"text-align: center;\">Empty</td>");
        });

        function checkCode(isCodeInsee){

            let code;
            if(isCodeInsee === true){
                code = $('#inputInseeCode').val();
            }
            else{
                code = $('#inputPostalCode').val();
            }

            let reg = new RegExp(/[0-9]{5}/, 'g');
            if (reg.test(code)) {
                return true;
            } else {
                if(isCodeInsee === true){
                    alert('Error submitted insee code. Accepted format : 01005')
                }
                else{
                    alert('Error submitted postal code. Accepted format : 01000')
                }
                return false;
            }
        }


    </script>
</html>