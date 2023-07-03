/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 03-04-2020    
 * Se crea DDL ROLLBACK para registros de parámetros en tablas DB_GENERAL.ADMI_PARAMETRO_CAB, DB_GENERAL.ADMI_PARAMETRO_DET. 
 */
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'ENMASCARA TARJETA CUENTA'
                        AND ESTADO             = 'Activo');
                        
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'ENMASCARA TARJETA CUENTA'
  AND ESTADO           = 'Activo';

 COMMIT;
/

