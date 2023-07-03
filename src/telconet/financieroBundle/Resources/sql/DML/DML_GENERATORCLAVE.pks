CREATE OR REPLACE AND RESOLVE JAVA SOURCE NAMED DB_FINANCIERO."GENERATORCLAVE" AS 
import java.io.*;
import java.util.*;  
import java.util.Random;

public class GeneratorClave { 

public static  synchronized String generarClaveAcceso(String fechaEmision, String tipoComprobante, String numeroRuc, String ambiente, String serie, String secuencial, String codNumerico, String tipoEmision) {
       int valMod = 11;
        int mod = 0;
        int digitoVerificador = 0;
        // Se obtiene las horas, minutos, segundos, milisegundos para ser parte
        // del codigo numerico
        Calendar cal = Calendar.getInstance();
        cal.setTime(new Date());
        int hh = cal.get(Calendar.HOUR_OF_DAY);
        int mm = cal.get(Calendar.MINUTE);
        int ss = cal.get(Calendar.SECOND);
        int millis = cal.get(Calendar.MILLISECOND);
        Random rand = new Random();
        // Se obtiene un numero aleatorio entre 0 y 1956000 lo cual es la
        // diferencia del mayor numero posible para el codigo numerico 99999999
        int randomNum = rand.nextInt(1956001);
        // Se genera el codigo numerico binario y se rellena con ceros a la
        // izquierda en caso de faltar digitos
        // hh:00000-10111(0-23) * 2^12
        // mm:000000-111011(0-59) * 2^6
        // ss:000000-111011(0-59) * 2^0
        String codigoNum = String.format("%08d", ((((4096 * hh) | (64 * mm) | ss) * 1000) + millis) + randomNum);

        String cadenaInicial = fechaEmision + tipoComprobante + numeroRuc + ambiente + serie + secuencial + codigoNum + tipoEmision;
        String cadenaFinal = "";
        int multiplicador = 2;
        int total = 0;
        for (int i = cadenaInicial.length() - 1; i >= 0; i--) {
            int valor = Integer.parseInt(String.valueOf(cadenaInicial.charAt(i)));
            multiplicador = (multiplicador > 7) ? 2 : multiplicador;
            total += (valor * multiplicador);
            multiplicador++;
        }
        mod = (total % valMod);
        digitoVerificador = valMod - mod;
        if (digitoVerificador == 11)
            digitoVerificador = 0;
        if (digitoVerificador == 10)
            digitoVerificador = 1;
        cadenaFinal = cadenaInicial + digitoVerificador;
        return cadenaFinal;
    }
}
