/**
 *
 * Se realiza el script de reverso de los parametros.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2021
 */

--Eliminamos el detalle de los parametros
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_SECURITY_DATA'
    );
--Eliminamos la cabecera.
DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'PARAMETROS_SECURITY_DATA';

--Eliminamos las características
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA IN (
        'REFERENCIA_SOLICITUD_SD'
    )
    AND ESTADO = 'Activo';
--Eliminamos los tipos de documentos de TelcoS+
DELETE FROM DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL
WHERE
    DESCRIPCION_TIPO_DOCUMENTO IN ( 'ORDEN DE SERVICIO',
                                    'ADEMDUM', 'ESCRITURA',
                                    'CARTA DE COMPROMISO',
                                    'CÉDULA REPRESENTANTE',
                                    'CÓDIGO DE CONDUCTA');
--Eliminamos los filtros de búsqueda para los tipos de documentos
DELETE FROM DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA
WHERE
    TIPO_DOCUMENTO_ID IN (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION IN ( 'Escritura', 'Ruc', 'Carta De Compromiso',
                             'Código De Conducta','Cédula Representante',
                             'Nombramiento','Ademdum','Orden de Servicio')
    )
    AND USR_CREACION = 'kbaque';
--Eliminamos los tipos de documentos de Gestor Documental
DELETE FROM DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
WHERE
    DESCRIPCION IN ( 'Escritura',
                     'Ruc', 
                     'Carta De Compromiso',
                     'Código De Conducta',
                     'Cédula Representante')
    AND USR_CREACION = 'kbaque';
--Eliminamos configuración de token
DELETE FROM DB_TOKENSECURITY.WEB_SERVICE
WHERE
        SERVICE = 'SoporteWSController'
    AND ID_APPLICATION IN (
        SELECT
            ID_APPLICATION
        FROM
            DB_TOKENSECURITY.APPLICATION
        WHERE
            NAME = 'Portal-SD'
    );
COMMIT;
/