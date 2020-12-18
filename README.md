# Semantic Web : Locations (EV Charging Stations and Parkings)

- **Arnaud Tavernier** 

- **Cédric Gormond**

![locations](https://i.ibb.co/1svch3V/locations.jpg)

Our project is composed of 3 folders:  **`ontology`**,  **`charginstation`**, **`pages_web`**.

- **`ontology`** includes two files : `chargingstation.owl` and `parking.owl` which describe respectively charging stations and parkings ontologies.
- **`charginstations`** contains the java code used to populate the triplestore by exporting directly the turtles to Fuseki server. The main class is `ExportTurltes` and manages all the others.
- **`pages_web`** contains all the pages that display stations, parkings, cities by querying datasests (for the query part, Easy Rdf has been integrated in the project)

## Technologies that we used

For this project, we used :

- [EasyRdf](https://www.easyrdf.org/) : A PHP library designed to make it easy to consume and produce *RDF*.
    - **SparQL client**

- [HERE API map](https://developer.here.com/) : High-quality location APIs from *HERE* Technologies, including documentation, code samples and developer support.
    - **Displaying a map**
- [DBpedia](https://wiki.dbpedia.org/) : Crowd-sourced community effort to extract structured content from the information created in various Wikimedia projects.
    - **SparQL Endpoint used to retrieve city information according to a ZIP code, INSEE code or partial city name.**
- [Distance function](https://www.geodatasource.com/developers/php) made by GeoDataSource.com :
    - **Distance function used to calculate a distance as the crow flies between two pairs of coordinates**

## Prerequisite

- You must have a LAMP/WAMP or PHP 5 (or above) directly installed on your OS or Virtual Machine.
- You must have Apache Jena Fuseki.
- You must have a Java IDE to set up your triple store. 

##  How to set up our project?

1. Launch the Apache Fuseki server service. For instance,  run the `fuseki-server.bat` script on Windows

2. Create the dataset `locations` with the Fuseki interface at `localhost:3030`.  
![fuseki-interface](https://i.ibb.co/nRryTLk/locations-fuseki.jpg) 

3. Import the Maven project `chargingstation` into your IDE and then run class `ExportTurle ` in order to build your triple store. You must see in the console the processing of the csv files.

	**Warning** : Our project parses only UTF-8 encoded csv files and it is only suitable for the csv files included in the project.

4. Make sure that the triple store is launched and initialized at `localhost:3030/locations`

5. If you are using a LAMP/WAMP, move the `pages_web` folder to the `www` folder of your server. Otherwise, if you are not using a LAMP/WAMP, your machine should be able to understand PHP files.

6. Finally, our project is accessible at `localhost:8080/pages_web/index.php` or directly at `localhost/pages_web/index.php`. 
![index](https://i.ibb.co/6wj2Hvy/index.jpg) 

	Feel free to browse our semantic web project.

	


