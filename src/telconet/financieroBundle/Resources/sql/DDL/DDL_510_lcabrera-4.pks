/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 30-11-2017 Se agrega la columna CODIGO para la generación del archivo ZIP de los débitos.
 */
    ALTER TABLE DB_FINANCIERO.ADMI_CICLO ADD CODIGO VARCHAR2(10);

    COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO.CODIGO
      IS
        'CÓDIGO DEL CICLO (APLICA EN LA GENERACIÓN DE ARCHIVOS PARA LOS DÉBITOS CREADOS).';
