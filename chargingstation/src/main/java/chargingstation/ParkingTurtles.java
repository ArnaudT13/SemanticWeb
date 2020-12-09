package chargingstation;

import java.io.IOException;
import java.io.Reader;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.HashSet;
import java.util.Set;

import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVRecord;
import org.apache.jena.rdf.model.Literal;
import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.Resource;
import org.apache.jena.vocabulary.RDF;
import org.apache.jena.vocabulary.RDFS;

public class ParkingTurtles {
	
	
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

}
