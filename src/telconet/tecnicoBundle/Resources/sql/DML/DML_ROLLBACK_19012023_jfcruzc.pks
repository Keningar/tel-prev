/** 
 * @author José Cruz <jfcruzc@telconet.ec>
 * @version 1.0 
 * @since 19-01-2023
 * Se crea DML de reverso de configuraciones cambio plan.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB AMT
WHERE AMT.USR_CREACION = 'jfcruzc' AND AMT.NOMBRE_PARAMETRO = 'USR_CAMBIO_PLAN_TAREA_AUTO';
COMMIT;

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET AMT
WHERE AMT.USR_CREACION = 'jfcruzc' AND 
( AMT.DESCRIPCION = 'ORIGEN_DEFAULT_CAMBIO_PLAN'
OR (AMT.DESCRIPCION = 'USR_CAMBIO_PLAN_TAREA_AUTO' AND AMT.VALOR1 IN (
                                                        'chatbot',
                                                        'appMovil',
                                                        'extranet'
                                                    ))
                                                    
    OR AMT.DESCRIPCION = 'VALOR_TOP_DOWN_CAMBIO_PLAN_F_A1'
    );
COMMIT;