package chargingstation;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;

public class InseeTownUtils {
	
	
	/**
	 * Get the town name according to the INSEE code parameter
	 * @param codeInsee 
	 * @return The json object of the town
	 * @throws IOException
	 */
	public static JsonNode getTownWithInseeCode(String codeInsee) throws IOException {
		// Create URL
		URL url = new URL("https://geo.api.gouv.fr/communes/" + codeInsee);
		HttpURLConnection con = (HttpURLConnection) url.openConnection();
		con.setRequestProperty("accept", "application/json");

		// This line makes the request
		InputStream responseStream = con.getInputStream();

		// Manually converting the response body InputStream to APOD using Jackson
		ObjectMapper mapper = new ObjectMapper();
		JsonNode jsonMap = mapper.readTree(responseStream);

		return jsonMap;
	}
}
