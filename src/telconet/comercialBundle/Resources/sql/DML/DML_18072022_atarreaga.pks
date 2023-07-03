/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 18-07-2022
 * Se crea DML de parámetro mensaje para validación en la clonación de promociones.
 */
 
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_PARAMETROS'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_VALIDACION_CLONA_PROMO',    
    'Existe(n) grupo(s) promocional(es) en proceso de Clonación: <br> strGruposPromocionesPma',    
    NULL,    
    NULL,    
    NULL,    
    NULL,
    NULL,    
    NULL,    
    'Activo',    
    'atarreaga',    
    SYSDATE,    
    '127.0.0.1',    
    '18',    
    'VALOR1: Nombre de grupo(s) promocional(es) en ejecución del proceso de Clonación');
 
COMMIT;
/
