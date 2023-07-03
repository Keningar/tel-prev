SET define OFF
/**
* dml para realizar el roolback de la creacion de solicitudes de descuento por regularizacion
* @author JOSE CRUZ <jfcruzc@telconet.ec>
* @since 1.0 26-08-2022
*/

BEGIN
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_REG_DESCUENTO_DIARIA_T"',
                          defer    => false,
                          force    => true);
  


EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB DB_COMERCIAL.JOB_REG_DESCUENTO_DIARIA_T');
END;
/

DROP package  DB_COMERCIAL.CMKG_REGULARIZACION_SOL_DES;
COMMIT;
