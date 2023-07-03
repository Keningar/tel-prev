/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 10-02-2020    
 * Se crea DML para agregar característica por edición de valores en nota de crédito.
 */

--SE CREA CARACTERÍSTICA 'EDICION_VALORES_NC'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
  ID_CARACTERISTICA,
  DESCRIPCION_CARACTERISTICA,
  TIPO_INGRESO,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  TIPO
) VALUES(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EDICION_VALORES_NC',
    'T',
    'Activo',
    SYSDATE,
    'atarreaga',
    'FINANCIERO'
  ); 

COMMIT;
/
