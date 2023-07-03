set define off

/**
 * Se crea dml para el roolback de la plantilla de envio de notificaciones por cambio de datos de facturacion.
 * @author Adrian Limones <alimonesr@telconet.ec>
 * @since 1.0 12-08-2020
 */
 
  DECLARE
  ln_Id_Admi_Plantilla number;
  BEGIN  
  SELECT ID_PLANTILLA into ln_Id_Admi_Plantilla
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'CAMBIODATOSFACT';
     
      DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA 
      WHERE PLANTILLA_ID= ln_Id_Admi_Plantilla;
      COMMIT;
      
      DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE ID_PLANTILLA=(SELECT ID_PLANTILLA 
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'CAMBIODATOSFACT');
      COMMIT;
  END;

/
