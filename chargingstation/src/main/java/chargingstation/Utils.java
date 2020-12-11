package chargingstation;

import java.text.Normalizer;

/**
 * Class of utils methods
 */
public class Utils {
	   
    /**
     * Normalize string by deleting accents and replace space by underscore
     * @param stringToNormalize
     * @return the normalized string
     */
    public static String normalizeString(String stringToNormalize) {
    	stringToNormalize = stringToNormalize.replaceAll(" ", "_");
    	stringToNormalize = stringToNormalize.replaceAll("\n", "");
    	stringToNormalize = stringToNormalize.replaceAll("\"", "");
    	stringToNormalize = stringToNormalize.replaceAll("	", "");
    	stringToNormalize = Normalizer.normalize(stringToNormalize, Normalizer.Form.NFD);
    	return stringToNormalize.trim();
    }
    
}
