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

import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVRecord;
import org.apache.jena.rdf.model.Literal;
import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.Property;
import org.apache.jena.rdf.model.Resource;
import org.apache.jena.vocabulary.RDF;
import org.apache.jena.vocabulary.RDFS;

import com.fasterxml.jackson.databind.JsonNode;

/**
 * Class used to manage charging station turtles creation
 */
public class ChargingStationTurtles {
    
    /**
     * Manage operator turtles creation
     * @param model The Jena Model
     * @param setOfOperators The set of operators to convert
     */
    public static void manageOperatorTurtles(Model model, Set<String> setOfOperators) {
    	for(String operator : setOfOperators) {

            // Create resources
            Resource resourceOperatorData = model.createResource(Constants.evcsData + "/operator#" + operator);
            Resource resourceOperatorOntology = model.createResource(Constants.evcsOnt + "Operator");

            // Create Literal
            Literal literalOperatorName = model.createTypedLiteral(operator);

            // Add triples to the model object
            model.add(resourceOperatorData,RDF.type,resourceOperatorOntology);
            model.add(resourceOperatorData, RDFS.label, literalOperatorName);
        }
    }
    
    
    /**
     * Manage payment turtles creation
     * @param model The Jena Model
     * @param setOfPayments The set of payments to convert
     */
    public static void managePaymentTurtles(Model model, Set<String> setOfPayments) {
    	for(String payment : setOfPayments) {

            // Create resources
            Resource resourcePaymentData = model.createResource(Constants.evcsData + "/payment#" + payment);
            Resource resourcePaymentOntology = model.createResource(Constants.evcsOnt + "Payment");

            // Create Literal
            Literal literalPaymentName = model.createTypedLiteral(payment);


            // Add triples to the model object
            model.add(resourcePaymentData,RDF.type,resourcePaymentOntology);
            model.add(resourcePaymentData, RDFS.label, literalPaymentName);
        }
    }
    
    
    
    /**
     * Manage charging station turtles creation
     * @param model The Jena Model
     * @param setOfPayments The list of charging stations to convert
     */
    public static void manageChargingStationTurtles(Model model, List<List<Object>> listOfChargingStation) {
    	
    	for(List<Object> chargingStation : listOfChargingStation) {
    		// Retrieve station properties
    		String evcsOperator = (String) chargingStation.get(0);
    		String evcsId = (String) chargingStation.get(1);
    		String evcsName = (String) chargingStation.get(2);
    		String evcsAddress = (String) chargingStation.get(3);
    		String evcsINSEE = (String) chargingStation.get(4);
    		
    		String evcsLongStr = ((String)chargingStation.get(5)).trim();
    		evcsLongStr = evcsLongStr.replace(',', '.');
    		String evcsLatStr = ((String)chargingStation.get(6)).trim();
    		evcsLatStr = evcsLatStr.replace(',', '.');
    		BigDecimal evcsLong = new BigDecimal(evcsLongStr);
    		BigDecimal evcsLat = new BigDecimal(evcsLatStr);
    		
    		String evcsPmax = (String) chargingStation.get(7);
    		String evcsPayment = (String) chargingStation.get(8);
    		String evcsObservation = (String) chargingStation.get(9);

            // Create resources
            Resource resourceChargingStationData = model.createResource(Constants.evcsData + "/chargingstation#" + evcsId);
            Resource resourceChargingStationOntology = model.createResource(Constants.evcsOnt + "ChargingStation");
            Resource resourceOperatorData = model.createResource(Constants.evcsData + "/operator#" + evcsOperator);
            Resource resourcePaymentData = model.createResource(Constants.evcsData + "/payment#" + evcsPayment);
            
            // Create properties
            Property propertyGeoLat = model.createProperty(Constants.geo + "lat");
            Property propertyGeoLong = model.createProperty(Constants.geo + "long");
            Property propertyChargingStationHasOperator = model.createProperty(Constants.evcsOnt + "hasOperator");
            Property propertyChargingStationHasPayementMode = model.createProperty(Constants.evcsOnt + "hasPaymentMode");
            Property propertyCodeINSEE = model.createProperty(Constants.igeo + "codeINSEE");
            Property propertyTownName = model.createProperty(Constants.dbp + "cityName");
            Property propertyPostalCode = model.createProperty(Constants.dbp + "postalCode");
            Property propertyChargingStationHasPowerMax = model.createProperty(Constants.evcsOnt + "hasPowerMax");

            // Create Literal
            Literal literalGeoLong = model.createTypedLiteral(evcsLong);
            Literal literalGeoLat = model.createTypedLiteral(evcsLat);
            Literal literalCodeINSEE = model.createLiteral(evcsINSEE);
            Literal literalPowerMax = model.createLiteral(evcsPmax);
            
            // Add triples to the model object
            model.add(resourceChargingStationData, RDF.type, resourceChargingStationOntology);
            model.add(resourceChargingStationData, RDFS.label, evcsName);
            model.add(resourceChargingStationData, RDFS.comment, evcsObservation);
            model.add(resourceChargingStationData, propertyChargingStationHasOperator, resourceOperatorData);
            model.add(resourceChargingStationData, propertyChargingStationHasPayementMode, resourcePaymentData);
            model.add(resourceChargingStationData, propertyChargingStationHasPowerMax, literalPowerMax);
            model.add(resourceChargingStationData, propertyGeoLong, literalGeoLong);
            model.add(resourceChargingStationData, propertyGeoLat, literalGeoLat);
            model.add(resourceChargingStationData, propertyCodeINSEE, literalCodeINSEE);

            
            // Get town name from API geo
            Literal literalTownNameINSEE = model.createLiteral("");
            Literal literalPostalCodeINSEE = model.createLiteral("");
			try {
				JsonNode jsonMap = InseeTownUtils.getTownWithInseeCode(evcsINSEE);
				literalTownNameINSEE = model.createLiteral(jsonMap.get("nom").toString().replaceAll("\"", ""));
				literalPostalCodeINSEE = model.createLiteral(jsonMap.get("codesPostaux").get(0).toString().replaceAll("\"", ""));
	            model.add(resourceChargingStationData, propertyTownName, literalTownNameINSEE);
	            model.add(resourceChargingStationData, propertyPostalCode, literalPostalCodeINSEE);
			} catch (FileNotFoundException e) {
				continue;
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
        }
    	
    	
    }



    /**
     * Get the stations list from the csv file
     *
     * @return List of stations (list of object)
     * @throws IOException
     */
    public static List<List<Object>> getChargingStations(String fileName) throws IOException{
    	List<List<Object>> objectList = new ArrayList<>();
        try (
                Reader reader = Files.newBufferedReader(Paths.get(fileName));
                CSVParser csvParser = new CSVParser(reader,  CSVFormat.EXCEL.withDelimiter(';').withFirstRecordAsHeader());
        ) {
            for (CSVRecord csvRecord : csvParser) {
                    // Get the values of the csv file
            		String evcsOperator    = csvRecord.get(1);
                    String evcsId          = csvRecord.get(3);
                    String evcsName        = csvRecord.get(4);
                    String evcsAddress     = csvRecord.get(5);
                    String evcsINSEE       = csvRecord.get(6);
                    String evcsLon         = csvRecord.get(7);
                    String evcsLat         = csvRecord.get(8);
                    String evcsPmax        = csvRecord.get(11);
                    String evcsPayment     = csvRecord.get(13);
                    String evcsObservation = csvRecord.get(15);

                    // Create the list of values for the station
                    List<Object> chargingStationList = new ArrayList<>();
                    chargingStationList.add(Utils.normalizeString(evcsOperator));
                    chargingStationList.add(evcsId);
                    chargingStationList.add(evcsName);
                    chargingStationList.add(evcsAddress);
                    chargingStationList.add(evcsINSEE);
                    chargingStationList.add(evcsLon);
                    chargingStationList.add(evcsLat);
                    chargingStationList.add(evcsPmax);
                    chargingStationList.add(Utils.normalizeString(evcsPayment).toLowerCase());
                    chargingStationList.add(evcsObservation);

                    // Add the station to the stations list
                    objectList.add(chargingStationList);
            }
        }
        return objectList;
    }

    
    /**
     * Retrieve the set of operator in the csv file
     * @return
     * @throws IOException
     */
    public static Set<String> getSetOfOperators(String fileName) throws IOException{
        HashSet<String> evcsSet = new HashSet<String>();

        
        try (
                Reader reader = Files.newBufferedReader(Paths.get(fileName));
                CSVParser csvParser = new CSVParser(reader, CSVFormat.EXCEL.withDelimiter(';').withFirstRecordAsHeader());
        ) {
            for (CSVRecord csvRecord : csvParser) {
                // Get the values of the csv file
                String evcsOperator = csvRecord.get(1);
                evcsSet.add(Utils.normalizeString(evcsOperator));
            }
        }

        return evcsSet;
    }
    
    

    /**
     * Retrieve the set of payment in the csv file
     * @return
     * @throws IOException
     */
    public static Set<String> getSetOfPayment(String fileName) throws IOException{
        HashSet<String> evcsSetPayment = new HashSet<String>();
        try (
                Reader reader = Files.newBufferedReader(Paths.get(fileName));
                CSVParser csvParser = new CSVParser(reader, CSVFormat.EXCEL.withDelimiter(';').withFirstRecordAsHeader());
        ) {
            for (CSVRecord csvRecord : csvParser) {
                // Get the values of the csv file
                String evcsPayment = csvRecord.get(13);
                if(evcsPayment != "") {
                    evcsSetPayment.add(Utils.normalizeString(evcsPayment).toLowerCase());
                }
            }
        }
        return evcsSetPayment;
    }
    
}
