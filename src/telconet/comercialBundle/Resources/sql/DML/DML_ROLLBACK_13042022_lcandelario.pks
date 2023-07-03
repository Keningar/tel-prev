/*
* 
* @author Liceth Candelario <lcandelario@telconet.ec>
* @version 1.0 13-04-2022
*/


SET DEFINE OFF;

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAM_CARACT_VELOCIDAD_X_PRODUCTO');

-- Eliminar en la tabla ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PARAM_CARACT_VELOCIDAD_X_PRODUCTO';



-- Se elimina la característica VELOCIDAD para el producto INTERNET SAFE: PROCESO DE SOLICITUD MASIVA
DELETE
  FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  WHERE PRODUCTO_ID =
    (SELECT id_producto
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'INTERNET SAFE'
    )
  AND CARACTERISTICA_ID=
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.admi_caracteristica
    WHERE DESCRIPCION_CARACTERISTICA = 'VELOCIDAD'
    )
  AND USR_CREACION = 'lcandelario';


--Volvemos al estado Activo de la vizualización del boton de cambio de velocidad
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET estado = 'Activo', fe_ult_mod= sysdate WHERE parametro_id =
                              (SELECT DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                    WHERE nombre_parametro = 'NO_VISUALIZAR_BOTON_DE_CAMBIO_VELOCIDAD');


COMMIT;

-- Se elimina los UPDATE para los valores de cambio de plan
UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '54'
        WHERE DETALLE_NOMBRE = 'LINE-PROFILE-ID'
        AND DETALLE_VALOR    = '16'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '54'
        WHERE DETALLE_NOMBRE = 'GEM-PORT'
        AND DETALLE_VALOR    = '16'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '54'
        WHERE DETALLE_NOMBRE = 'TRAFFIC-TABLE'
        AND DETALLE_VALOR    = '16'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '55'
        WHERE DETALLE_NOMBRE = 'LINE-PROFILE-ID'
        AND DETALLE_VALOR    = '25'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '55'
        WHERE DETALLE_NOMBRE = 'GEM-PORT'
        AND DETALLE_VALOR    = '25'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '55'
        WHERE DETALLE_NOMBRE = 'TRAFFIC-TABLE'
        AND DETALLE_VALOR    = '25'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '56'
        WHERE DETALLE_NOMBRE = 'LINE-PROFILE-ID'
        AND DETALLE_VALOR    = '31'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '56'
        WHERE DETALLE_NOMBRE = 'GEM-PORT'
        AND DETALLE_VALOR    = '31'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '56'
        WHERE DETALLE_NOMBRE = 'TRAFFIC-TABLE'
        AND DETALLE_VALOR    = '31'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '57'
        WHERE DETALLE_NOMBRE = 'LINE-PROFILE-ID'
        AND DETALLE_VALOR    = '39'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '57'
        WHERE DETALLE_NOMBRE = 'GEM-PORT'
        AND DETALLE_VALOR    = '39'
        AND USR_CREACION     = 'afayala';

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        SET DETALLE_VALOR    = '57'
        WHERE DETALLE_NOMBRE = 'TRAFFIC-TABLE'
        AND DETALLE_VALOR    = '39'
        AND USR_CREACION     = 'afayala';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
        SET VALOR3         = '54'
        WHERE PARAMETRO_ID =
        (SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'CNR_PERFIL_CLIENT_PCK'
          AND ESTADO             = 'Activo'
        )
        AND DESCRIPCION = 'TN_PLAN_15M'
        AND VALOR1      = 'TN_PLAN_15M'
        AND VALOR2      = 'TN_PLAN_15M';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
        SET VALOR3         = '55'
        WHERE PARAMETRO_ID =
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CNR_PERFIL_CLIENT_PCK'
         AND ESTADO             = 'Activo'
        )
        AND DESCRIPCION = 'TN_PLAN_25M'
        AND VALOR1      = 'TN_PLAN_25M'
        AND VALOR2      = 'TN_PLAN_25M';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
        SET VALOR3         = '56'
        WHERE PARAMETRO_ID =
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CNR_PERFIL_CLIENT_PCK'
         AND ESTADO             = 'Activo'
        )
        AND DESCRIPCION = 'TN_PLAN_30M'
        AND VALOR1      = 'TN_PLAN_30M'
        AND VALOR2      = 'TN_PLAN_30M';


UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
        SET VALOR3         = '57'
        WHERE PARAMETRO_ID =
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CNR_PERFIL_CLIENT_PCK'
         AND ESTADO             = 'Activo'
        )
        AND DESCRIPCION = 'TN_PLAN_40M'
        AND VALOR1      = 'TN_PLAN_40M'
        AND VALOR2      = 'TN_PLAN_40';

/