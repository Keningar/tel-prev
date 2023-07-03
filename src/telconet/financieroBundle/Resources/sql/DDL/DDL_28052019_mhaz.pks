/**
 * Se modifica tipo de dato de columna FE_CREACION para llevar el monitoreo 
 * por tiempos del log insertado en el trigger AFTER_DML_INFO_COMP_ELEC.
 * @author Madeline Haz  <mhaz@telconet.ec>
 * @version 1.0 28-05-2019 
 */
    ALTER TABLE DB_FINANCIERO.INFO_ERROR  
    MODIFY (FE_CREACION TIMESTAMP(6));
    
    COMMIT;