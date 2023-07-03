/**
 * @author Kevin Baque <kbaque@telconet.ec>
 * @version 1.0 31-10-2018 Se agrega la columna VALOR6.
 */
 
    ALTER TABLE DB_GENERAL.ADMI_PARAMETRO_DET ADD VALOR6 VARCHAR2(300);
        
    ALTER TABLE DB_GENERAL.ADMI_PARAMETRO_DET ADD VALOR7 VARCHAR2(300);

    COMMENT ON COLUMN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR6
        IS
            'Valor del parámetro';    
        
    COMMENT ON COLUMN DB_GENERAL.ADMI_PARAMETRO_DET.VALOR7
        IS
            'Valor del parámetro';
