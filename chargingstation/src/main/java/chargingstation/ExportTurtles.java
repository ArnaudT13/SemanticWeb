package chargingstation;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Set;

import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.ModelFactory;
import org.apache.jena.rdfconnection.RDFConnection;
import org.apache.jena.rdfconnection.RDFConnectionFactory;

/**
 * Class used to export the turles to fuseki triplestore
 */
public class ExportTurtles {
	
	 public static void main(String[] args) {
	        // Clear triplestore
	        clearTurtlestoreInFuseki(Constants.datasetName);	
	        
	        // Export charging station turtles
	        manageChargingStationTurtlesCreation();
	        
	        // Export parking turtles
	        manageParkingTurtlesCreation();
	      
	        System.out.println("Export process : DONE");
	 }
	 
	 
	 
	/**
	 * Manage charging station turtles creation
	 */
	public static void manageChargingStationTurtlesCreation() {
		List<String> fileNameList = new ArrayList<String>();
        fileNameList.add("irve-sem-20200420.csv");
        fileNameList.add("irve-mamp-20201007.csv");
        fileNameList.add("irve-lyon.csv");
        fileNameList.add("irve-capg.csv");
        fileNameList.add("irve-alize.csv");
        
	        
        try {
            // Create model object
            Model model = ModelFactory.createDefaultModel();
            
        	for(String filename : fileNameList) {
        		// Create payment turtles
                Set<String> setOfPayment = ChargingStationTurtles.getSetOfPayment(filename);
                ChargingStationTurtles.managePaymentTurtles(model, setOfPayment);
             
                // Create operator turtles
                Set<String> setOfOperators = ChargingStationTurtles.getSetOfOperators(filename);
                ChargingStationTurtles.manageOperatorTurtles(model, setOfOperators);
                
                //Create station turtles
                List<List<Object>> listOfStations = ChargingStationTurtles.getChargingStations(filename);
                ChargingStationTurtles.manageChargingStationTurtles(model, listOfStations);
                
                exportTurtlesToFuseki(model, Constants.datasetName);
                System.out.println(filename + " : DONE");
        	}
         
        } catch (IOException e) {
            e.printStackTrace();
        }
	}
	
	
	
	/**
	 * Manage parking turtles creation
	 */
	public static void manageParkingTurtlesCreation() {
		String filename = "fichier-parking-2018.csv";
		
		try {
			 // Create model object
	         Model model = ModelFactory.createDefaultModel();

	         // Create parking types turtles
	         Set<String> setOfParkingtypes = ParkingTurtles.getSetOfParkingTypes(filename);
	         ParkingTurtles.manageParkingTypeTurtles(model, setOfParkingtypes);

			 //Create parking turtles
			 List<List<Object>> listOfParkings = ParkingTurtles.getParkings(filename);
			 ParkingTurtles.manageParkingTurtles(model, listOfParkings);

			 exportTurtlesToFuseki(model, Constants.datasetName);
             System.out.println(filename + " : DONE");
			 
			 
		    } catch (IOException e) {
		    	e.printStackTrace();
		    }
	}
	 
	
	
    /**
     * Clear fuseki triplestore
     */
    public static void clearTurtlestoreInFuseki(String datasetName) {
    	// Create connection with the dataset
        String datasetURL = "http://localhost:3030/" + datasetName;
		String sparqlEndpoint = datasetURL + "/sparql";
		String sparqlUpdate = datasetURL + "/update";
		String graphStore = datasetURL + "/data";
		RDFConnection conneg = RDFConnectionFactory.connect(sparqlEndpoint, sparqlUpdate, graphStore);
		
		// Delete content
		conneg.delete();
		conneg.close();
    }
    
    
    
    /**
     * Export turles model to fuseki server
     * @param model The Jena Model
     */
    public static void exportTurtlesToFuseki(Model model, String datasetName) {
        // Create connection with the dataset
        String datasetURL = "http://localhost:3030/" + datasetName;
		String sparqlEndpoint = datasetURL + "/sparql";
		String sparqlUpdate = datasetURL + "/update";
		String graphStore = datasetURL + "/data";
		RDFConnection conneg = RDFConnectionFactory.connect(sparqlEndpoint, sparqlUpdate, graphStore);

		// Import data model
		conneg.load(model); // add the content of model to the triplestore
		conneg.close();
    }

    
	
}
