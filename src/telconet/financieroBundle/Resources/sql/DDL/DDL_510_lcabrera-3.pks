
/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 21-11-2017 - Se agrega la columna CICLO_ID referente al ciclo de facturación.
 *                         - Se crea comenta la columna.
 *                         - Se crea el contraint.
 */
    ALTER TABLE DB_FINANCIERO.INFO_DEBITO_GENERAL ADD CICLO_ID NUMBER;

    COMMENT ON COLUMN DB_FINANCIERO.INFO_DEBITO_GENERAL.CICLO_ID
      IS
        'CICLO AL QUE EL DÉBITO ESTÁ LIGADO. (DB_FINANCIERO.ADMI_CICLO)';

    ALTER TABLE DB_FINANCIERO.INFO_DEBITO_GENERAL 
      ADD CONSTRAINT FK_ADMI_CICLO_ID
      FOREIGN KEY (CICLO_ID)
      REFERENCES DB_FINANCIERO.ADMI_CICLO (ID_CICLO);
