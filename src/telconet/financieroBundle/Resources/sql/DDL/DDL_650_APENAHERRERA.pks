SET DEFINE OFF
CREATE OR REPLACE AND RESOLVE JAVA SOURCE NAMED DB_FINANCIERO."GENERA_PASSWD_SHA256" AS

import java.security.MessageDigest;

/**
 * Documentación para Clase 'F_GENERA_PASSWD_SHA256'.
 * Genera password del Usuario a generarse en base a algoritmo java   
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 16-01-2018
 */         
public class Genera_Passwd_Sha256 {

   /**
    * Documentación para el metodo 'computeHashSHA256'
    * @param string   String
    * @return SHA-256 hash - 64 chars
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 16-01-2018
    */
    public static synchronized String computeHashSHA256(String string) {
        try {
            return byteArrayToHexString(computeHash(string, "SHA-256"));
        } catch (Exception e) {
            return null;
        }
    }
   /**
    * Documentación para el metodo 'computeHash'
    * @param  x          string
    * @param  algorithm  string
    * @return Bytes
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 16-01-2018
    */
    private static byte[] computeHash(String x, String algorithm) throws Exception {
        MessageDigest d = null;
        d = MessageDigest.getInstance(algorithm);
        d.reset();
        d.update(x.getBytes());
        return d.digest();
    }
   /**
    * Documentación para el metodo 'byteArrayToHexString'
    * @param  b          byte[]     
    * @return String
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 16-01-2018
    */
    private static String byteArrayToHexString(byte[] b) {
        StringBuffer sb = new StringBuffer(b.length * 2);
        for (int i = 0; i < b.length; i++) {
            int v = b[i] & 0xff;
            if (v < 16) {
                sb.append('0');
            }
            sb.append(Integer.toHexString(v));
        }
        return sb.toString();
    }
}
/
ALTER TABLE DB_FINANCIERO.INFO_ERROR  
MODIFY (DETALLE_ERROR VARCHAR2(4000 BYTE) );

ALTER TABLE DB_FINANCIERO.INFO_MENSAJE_COMP_ELEC  
MODIFY (INFORMACION_ADICIONAL VARCHAR2(4000 BYTE) );

ALTER TABLE DB_COMPROBANTES.INFO_MENSAJE  
MODIFY (INFOADICIONAL VARCHAR2(4000 BYTE) );


