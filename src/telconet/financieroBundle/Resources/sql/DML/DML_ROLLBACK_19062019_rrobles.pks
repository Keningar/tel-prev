/**
 * @author Ricardo Robles <rrobles@telconet.ec>
 * @version 1.0
 * @since 19-06-2019    
 * Se crea las sentencias DML para la eliminación del parámetro 'DIAS FECHA MAXIMA PAGO' y sus detalles.
 */

--ELIMINACIÓN DE DETALLE CARACTERÍSTICA 'EFECTIVO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET WHERE DESCRIPCION = 'EFECTIVO';

--ELIMINACIÓN DE DETALLE CARACTERÍSTICA 'TARJETA DE CREDITO-DEBITO BANCARIO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET WHERE DESCRIPCION = 'TARJETA DE CREDITO-DEBITO BANCARIO';

--ELIMINACIÓN DE LA CARACTERÍSTICA 'DIAS FECHA MAXIMA PAGO'
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DIAS FECHA MAXIMA PAGO';

  COMMIT;
/  
