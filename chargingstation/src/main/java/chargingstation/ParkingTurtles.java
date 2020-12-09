package chargingstation;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.Reader;
import java.math.BigDecimal;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import com.fasterxml.jackson.databind.JsonNode;
import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVRecord;
import org.apache.jena.rdf.model.Literal;
import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.Property;
import org.apache.jena.rdf.model.Resource;
import org.apache.jena.vocabulary.RDF;
import org.apache.jena.vocabulary.RDFS;

public class ParkingTurtles {

    /**
     * Manage parking type turtles creation
     * @param model The Jena Model
     * @param listOfParking The set of parking types to convert
     */
    public static void manageParkingTurtles(Model model, List<List<Object>>  listOfParking) {


        for(List<Object> parking : listOfParking) {
            // Retrieve station properties
            String parkingName = (String) parking.get(0);
            String parkingAddress = (String) parking.get(1);
            String parkingCodePostal = (String) parking.get(2);
            String parkingCommune = (String) parking.get(3);

            String parkingLatStr = ((String)parking.get(4)).trim();
            parkingLatStr = parkingLatStr.replace(',', '.');


            String parkingLongStr = ((String)parking.get(5)).trim();
            parkingLongStr = parkingLongStr.replace(',', '.');


            BigDecimal parkingLong = new BigDecimal(parkingLongStr);
            BigDecimal parkingLat = new BigDecimal(parkingLatStr);

            String parkingCapacityStr = ((String)parking.get(6)).trim();

            BigDecimal parkingCapacity = new BigDecimal("0");
            if(!parkingCapacityStr.isEmpty()){
                parkingCapacity = new BigDecimal(parkingCapacityStr);
            }

                    // Create resources
            Resource resourceParkingData = model.createResource(Constants.parkData + "/parking#" + parkingName); // TODO On n'a pas d'ID donc j'ai pris le nom
            Resource resourceParkingOntology = model.createResource(Constants.parkOnt + "Parking");

            // Create properties
            Property propertyGeoLat = model.createProperty(Constants.geo + "lat");
            Property propertyGeoLong = model.createProperty(Constants.geo + "long");
            Property propertyParkingHasCapacity = model.createProperty(Constants.evcsOnt + "hasCapacity");
            Property propertyTownNameINSEE = model.createProperty(Constants.igeo + "Commune");
            Property propertyPostalCodeINSEE = model.createProperty(Constants.igeo + "ZonePostale");

            // Create Literal xsd:decimal
            Literal literalGeoLong = model.createTypedLiteral(parkingLong);
            Literal literalGeoLat = model.createTypedLiteral(parkingLat);
            Literal literalCapacity = model.createLiteral(parkingCapacityStr); // TODO A revoir avec BigDecimal
            Literal literalTownNameINSEE = model.createLiteral(parkingCommune);
            Literal literalPostalCodeINSEE = model.createLiteral(parkingCodePostal);


            // Add triples to the model object
            model.add(resourceParkingData, RDF.type, resourceParkingOntology);
            model.add(resourceParkingData, RDFS.label, parkingName);
            model.add(resourceParkingData, propertyGeoLong, literalGeoLong);
            model.add(resourceParkingData, propertyGeoLat, literalGeoLat);
            model.add(resourceParkingData, propertyTownNameINSEE, literalTownNameINSEE);
            model.add(resourceParkingData, propertyPostalCodeINSEE, literalPostalCodeINSEE);
            model.add(resourceParkingData, propertyParkingHasCapacity, literalCapacity);
        }
    }
	
    /**
     * Manage parking type turtles creation
     * @param model The Jena Model
     * @param setOfParkingTypes The set of parking types to convert
     */
    public static void manageParkingTypeTurtles(Model model, Set<String> setOfParkingTypes) {
    	for(String parkingType : setOfParkingTypes) {

            // Create resources
            Resource resourceParkingTypeData = model.createResource(Constants.parkData + "/parking#" + parkingType);
            Resource resourceParkingTypeOntology = model.createResource(Constants.parkOnt + "ParkingType");

            // Create Literal
            Literal literalParkingTypeName = model.createTypedLiteral(parkingType);

            // Add triples to the model object
            model.add(resourceParkingTypeData,RDF.type,resourceParkingTypeOntology);
            model.add(resourceParkingTypeData, RDFS.label, literalParkingTypeName);
        }
    }
    
    
    /**
     * Retrieve the set of parking types in the csv file
     * @return
     * @throws IOException
     */
    public static Set<String> getSetOfParkingTypes(String fileName) throws IOException{
        HashSet<String> parkingTypesSet = new HashSet<String>();

        
        try (
                Reader reader = Files.newBufferedReader(Paths.get(fileName));
                CSVParser csvParser = new CSVParser(reader, CSVFormat.EXCEL.withDelimiter(';').withFirstRecordAsHeader());
        ) {
            for (CSVRecord csvRecord : csvParser) {
                // Get the values of the csv file
                String evcsParkingType = csvRecord.get(6);
                parkingTypesSet.add(Utils.normalizeString(evcsParkingType).replaceAll("-", "_"));
            }
        }

        return parkingTypesSet;
    }

    /**
     * Get the stations list from the csv file
     *
     * @return List of stations (list of object)
     * @throws IOException
     */
    public static List<List<Object>> getParkings(String fileName) throws IOException{
        List<List<Object>> objectList = new ArrayList<>();
        try (
                Reader reader = Files.newBufferedReader(Paths.get(fileName));
                CSVParser csvParser = new CSVParser(reader,  CSVFormat.EXCEL.withDelimiter(';').withFirstRecordAsHeader());
        ) {
            for (CSVRecord csvRecord : csvParser) {
                // Get the values of the csv file
                String parkingName        = csvRecord.get(0);
                String parkingAddress     = csvRecord.get(1);
                String parkingCodePostal  = csvRecord.get(2);
                String parkingCommune     = csvRecord.get(3);
                String parkingLon         = csvRecord.get(4);
                String parkingLat         = csvRecord.get(5);
                String parkingCapacity    = csvRecord.get(8);

                // Create the list of values for the parking
                List<Object> chargingStationList = new ArrayList<>();
                chargingStationList.add(Utils.normalizeString(parkingName));
                chargingStationList.add(parkingAddress);
                chargingStationList.add(parkingCodePostal);
                chargingStationList.add(parkingCommune);
                chargingStationList.add(parkingLon);
                chargingStationList.add(parkingLat);
                chargingStationList.add(parkingCapacity);

                // Add the station to the stations list
                objectList.add(chargingStationList);
            }
        }
        return objectList;
    }

}
