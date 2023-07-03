/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML para insert de directorios
 */

INSERT 
INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  (
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
VALUES
  (
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    4,
    (select max(codigo_path)+1 from db_general.ADMI_GESTION_DIRECTORIOS where codigo_app = 4 and aplicacion = 'TelcosWeb'),
    'TelcosWeb',
    '593',
    'MD',
    'Financiero',
    'AnularPago',
    'Activo',
    sysdate,
    'wquinto');