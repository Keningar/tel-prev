  /**
   * crear sinonimo de tabla ADMI_GESTION_DIRECTORIOS en el esquema DB_GENERAL hacia DB_FINANCIERO
   * @author Carlos Caguana <ccaguana@telconet.ec>
   * @version 1.0
   * @since 23-03-20201
   */
CREATE OR REPLACE SYNONYM DB_FINANCIERO.ADMI_GESTION_DIRECTORIOS FOR DB_GENERAL.ADMI_GESTION_DIRECTORIOS;
grant select on DB_FINANCIERO.ADMI_GESTION_DIRECTORIOS to db_financiero;


/

CREATE OR REPLACE AND RESOLVE JAVA SOURCE NAMED DB_FINANCIERO."HttpPostMultipart" AS 
import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;
import java.util.Iterator;
import java.util.UUID;
import java.util.HashMap;
import java.util.Map;
import java.util.Iterator;
import java.util.Set;

public class HttpPostMultipart { 
    private final String boundary;
    private static final String LINE = "\r\n";
    private HttpURLConnection httpConn;
    private String charset;
    private OutputStream outputStream;
    private PrintWriter writer;

    public HttpPostMultipart(String requestURL, String charset, Map<String, String> headers) throws IOException {
        this.charset = charset;
        boundary = UUID.randomUUID().toString();
        URL url = new URL(requestURL);
        httpConn = (HttpURLConnection) url.openConnection();
        httpConn.setUseCaches(false);
        httpConn.setDoOutput(true);    
        httpConn.setDoInput(true);
        httpConn.setRequestProperty("Content-Type", "multipart/form-data; boundary=" + boundary);
            Iterator<String> it = headers.keySet().iterator();
            while (it.hasNext()) {
                String key = it.next();
                String value = headers.get(key);
                httpConn.setRequestProperty(key, value);
            }
        outputStream = httpConn.getOutputStream();
        writer = new PrintWriter(new OutputStreamWriter(outputStream, charset), true);
    }


      public void addFormField(String name, String value) {
        writer.append("--" + boundary).append(LINE);
        writer.append("Content-Disposition: form-data; name=\"" + name + "\"").append(LINE);
        writer.append("Content-Type: text/plain; charset=" + charset).append(LINE);
        writer.append(LINE);
        writer.append(value).append(LINE);
        writer.flush();
     }

      public void addFilePart(String fieldName, File uploadFile)
            throws IOException {
        String fileName = uploadFile.getName();
        writer.append("--" + boundary).append(LINE);
        writer.append("Content-Disposition: form-data; name=\"" + fieldName + "\"; filename=\"" + fileName + "\"").append(LINE);
        writer.append("Content-Type: " + URLConnection.guessContentTypeFromName(fileName)).append(LINE);
        writer.append("Content-Transfer-Encoding: binary").append(LINE);
        writer.append(LINE);
        writer.flush();

        FileInputStream inputStream = new FileInputStream(uploadFile);
        byte[] buffer = new byte[4096];
        int bytesRead = -1;
        while ((bytesRead = inputStream.read(buffer)) != -1) {
            outputStream.write(buffer, 0, bytesRead);
        }
        outputStream.flush();
        inputStream.close();
        writer.append(LINE);
        writer.flush();
    }

     public String finish() throws IOException {
        String response = "";
        writer.flush();
        writer.append("--" + boundary + "--").append(LINE);
        writer.close();
        int status = httpConn.getResponseCode();
        if (status == HttpURLConnection.HTTP_OK) {
            ByteArrayOutputStream result = new ByteArrayOutputStream();
            byte[] buffer = new byte[1024];
            int length;
            while ((length = httpConn.getInputStream().read(buffer)) != -1) {
                result.write(buffer, 0, length);
            }
            response = result.toString(this.charset);
            httpConn.disconnect();
        } else {
            throw new IOException("Server returned non-OK status: " + status);
        }
        return response;
    }


    public static  synchronized String consumer(String urlMicroService,String fileName,String nombreArchivo,String pathAdicional,String codigoApp,String codigoPath)
    {
    String respuesta="";
        try{

        Map<String, String> cabecera = new HashMap<String,String>();
        cabecera.put("User-Agent", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36");
        HttpPostMultipart multipart = new HttpPostMultipart(urlMicroService, "utf-8", cabecera);
        multipart.addFilePart("file", new File(fileName));
        multipart.addFormField("request","{\n" +
                "  \"data\": [\n" +
                "    {\n" +
                "      \"codigoApp\":"+codigoApp+",\n" +
                "      \"codigoPath\": "+codigoPath+",\n" +
                "      \"fileBase64\": \"\",\n" +
                "      \"nombreArchivo\":"+
                "      \""+nombreArchivo+"\",\n"+ 
                "      \"pathAdicional\": [\n" +
                "        {\n" +
                "          \"key\": \""+pathAdicional+"\"\n" +
                "        }\n" +
                "      ]\n" +
                "    }\n" +
                "  ],\n" +
                "  \"op\": \"guardarArchivo\",\n" +
                "  \"user\": \"telcos\"\n" +
                "}");


        String response = multipart.finish();
                        respuesta=response;    

        } catch (Exception e) {
         respuesta= e.getMessage();
        }
        return respuesta;
    }


     public static synchronized  String containsString(String texto,String  comparar){
    
            if(texto.contains(comparar)){
                return"OK";
            }else{
                return "False";
            }
      }

}
