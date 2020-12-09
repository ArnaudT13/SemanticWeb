package chargingstation;

import java.text.Normalizer;

import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdfconnection.RDFConnection;
import org.apache.jena.rdfconnection.RDFConnectionFactory;

public class Utils {
	   
    /**
     * Normalize string by deleting accents and replace space by underscore
     * @param stringToNormalize
     * @return the normalized string
     */
    public static String normalizeString(String stringToNormalize) {
    	stringToNormalize = stringToNormalize.replaceAll(" ", "_");
    	stringToNormalize = stringToNormalize.replaceAll("\n", "");
    	stringToNormalize = Normalizer.normalize(stringToNormalize, Normalizer.Form.NFD);
    	return stringToNormalize;
    }
    
}
