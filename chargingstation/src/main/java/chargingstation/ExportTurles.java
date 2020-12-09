package chargingstation;

import java.io.IOException;
import java.text.Normalizer;
import java.util.ArrayList;
import java.util.List;
import java.util.Set;

import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.ModelFactory;
import org.apache.jena.rdfconnection.RDFConnection;
import org.apache.jena.rdfconnection.RDFConnectionFactory;

public class ExportTurles {
	
	
	 public static void main(String[] args) {
	        
	        clearTurtlestoreInFuseki();

	        //manageChargingStationTurtlesCreation();
	        
	        manageParkingTurtlesCreation();
	      

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
            
        	for(String fileName : fileNameList) {
        		// Create payment turtles
                Set<String> setOfPayment = ChargingStationTurtles.getSetOfPayment(fileName);
                ChargingStationTurtles.managePaymentTurtles(model, setOfPayment);
             
                // Create operator turtles
                Set<String> setOfOperators = ChargingStationTurtles.getSetOfOperators(fileName);
                ChargingStationTurtles.manageOperatorTurtles(model, setOfOperators);
                
                //Create station turtles
                List<List<Object>> listOfStations = ChargingStationTurtles.getChargingStations(fileName);
                ChargingStationTurtles.manageChargingStationTurtles(model, listOfStations);
                
                exportTurtlesToFuseki(model, Constants.datasetNameChargingStation);
                System.out.println(fileName + " : DONE");
        	}
         
        } catch (IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
	}
	
	
	
	/**
	 * Manage parking turtles creation
	 */
	public static void manageParkingTurtlesCreation() {
		 try {
			 // Create model object
	         Model model = ModelFactory.createDefaultModel();

	         // Create parking types turtles
	         Set<String> setOfParkingtypes = ParkingTurtles.getSetOfParkingTypes("fichier-parking-2018.csv");
	         ParkingTurtles.manageParkingTypeTurtles(model, setOfParkingtypes);

			 //Create parking turtles
			 List<List<Object>> listOfParkings = ParkingTurtles.getParkings("fichier-parking-2018.csv");
			 ParkingTurtles.manageParkingTurtles(model, listOfParkings);

			 exportTurtlesToFuseki(model, Constants.datasetNameParkings);
			 
			 
		    } catch (IOException e) {
		        // TODO Auto-generated catch block
		    	e.printStackTrace();
		    }
	}
	 
	
	
    /**
     * Clear fuseki dataset
     */
    public static void clearTurtlestoreInFuseki() {
    	// Create connection with the dataset
        String datasetURL = "http://localhost:3030/" + Constants.datasetNameChargingStation;
		String sparqlEndpoint = datasetURL + "/sparql";
		String sparqlUpdate = datasetURL + "/update";
		String graphStore = datasetURL + "/data";
		RDFConnection conneg = RDFConnectionFactory.connect(sparqlEndpoint,sparqlUpdate,graphStore);
		
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
		RDFConnection conneg = RDFConnectionFactory.connect(sparqlEndpoint,sparqlUpdate,graphStore);

		// Import data model
		conneg.load(model); // add the content of model to the triplestore
		conneg.close();
    }

    
	
}
