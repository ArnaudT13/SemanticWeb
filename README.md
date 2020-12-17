# Semantic Web : Locations (EV Charging Stations and Parkings)

- **Arnaud Tavernier** 

- **Cédric Gormond**



Our project is composed of 3 folders:  **`ontology`**,  **`charginstations`**, **`pages_web`**.

- **`ontology`** includes two files : `chargingstation.owl` and `parking.owl` which describe respectively charging stations and parkings ontologies.
- **`charginstations`** contains the java code used to populate the triplestore by exporting directly the turtles to Fuseki server. The main class is `ExportTurltes` and manages all the others.
- **`pages_web`** contains all the pages that display stations, parkings, cities by querying datasests (for the query part, Easy Rdf has been integrated in the project)

## Prerequisite

- You must have a LAMP/WAMP or PHP 5 (or above) directly installed on your OS or Virtual Machine.
- You must have Apache Jena Fuseki.
- You must have a Java IDE to set up your triple store. 

##  How to set up our project?

1. Launch the Apache Fuseki server service. For instance,  run the `fuseki-server.bat` script on Windows

2. Create the dataset `locations` with the Fuseki interface at `localhost:3030`.  

3. Import the Maven project `chargingstation` into your IDE and then run class `ExportTurle ` in order to build your triple store. You must see in the console the processing of the csv files.

	**Warning** : Our project parses only UTF-8 encoded csv files and it is only suitable for the csv files included in the project.

4. Make sure that the triple store is launched and initialized at `localhost:3030/locations`

5. If you are using a LAMP/WAMP, move the `pages_web` folder to the `www` folder of your server. Otherwise, if you are not using a LAMP/WAMP, your machine should be able to understand PHP files.

6. Finally, our project is accessible at `localhost:8080/pages_web/index.php` or directly at `localhost/pages_web/index.php`. Feel free to browse our semantic web project.

	


