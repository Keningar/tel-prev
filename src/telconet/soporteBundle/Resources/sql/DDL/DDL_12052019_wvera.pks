-- Add/modify columns 
   ALTER TABLE DB_GENERAL.ADMI_CANTON add JURISDICCION VARCHAR2(10) DEFAULT 'QUITO' NULL;
-- Add comments to the columns 
   COMMENT ON COLUMN DB_GENERAL.ADMI_CANTON.JURISDICCION   is 'Campo que sirve para identificar la jurisdiccion de cada canton.';
    
   UPDATE  DB_GENERAL.ADMI_CANTON AC SET AC.JURISDICCION = 'GUAYAQUIL' WHERE AC.REGION = 'R1';
COMMIT;
/
