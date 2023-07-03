
/*
* Se realiza la creación del producto DATOS DC SDWAN tomando como base el producto DATOS DC.
* @author David León <mdleon@telconet.ec>
* @version 1.0 28-08-2019
*/

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO 
    SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
        AP.EMPRESA_COD,
        'DCDSDWAN',
        'DATOS DC SDWAN',
        AP.FUNCION_COSTO,
        AP.INSTALACION,
        AP.ESTADO,
        sysdate,
        'mdleon',
        AP.IP_CREACION,
        AP.CTA_CONTABLE_PROD,
        AP.CTA_CONTABLE_PROD_NC,
        AP.ES_PREFERENCIA,
        AP.ES_ENLACE,
        AP.REQUIERE_PLANIFICACION,
        AP.REQUIERE_INFO_TECNICA,
        'DATOS DC SDWAN',
        AP.CTA_CONTABLE_DESC,
        AP.TIPO,
        AP.ES_CONCENTRADOR,
        AP.FUNCION_PRECIO,
        AP.SOPORTE_MASIVO,
        AP.ESTADO_INICIAL,
        AP.GRUPO,
        AP.COMISION_VENTA,
        AP.COMISION_MANTENIMIENTO,
        AP.USR_GERENTE,
        AP.CLASIFICACION,
        AP.REQUIERE_COMISIONAR,AP.SUBGRUPO,AP.LINEA_NEGOCIO 
    FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
    WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
        and AP.ESTADO='Activo' 
        and AP.NOMBRE_TECNICO='DATOSDC' 
        and AP.EMPRESA_COD=10; 


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DATOS DC SDWAN'),
        APC.CARACTERISTICA_ID,
        sysdate,
        APC.FE_ULT_MOD,
        'mdleon',
        APC.USR_ULT_MOD,
        APC.ESTADO,
        APC.VISIBLE_COMERCIAL
    FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC 
    WHERE producto_id=(SELECT ID_PRODUCTO 
                       FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
                       WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
                            and AP.ESTADO='Activo' 
                            and AP.NOMBRE_TECNICO='DATOSDC' 
                            and AP.EMPRESA_COD=10)
        and APC.ESTADO !='Eliminado';


INSERT INTO DB_COMERCIAL.INFO_PRODUCTO_NIVEL
    SELECT DB_COMERCIAL.SEQ_INFO_PRODUCTO_NIVEL.NEXTVAL,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DATOS DC SDWAN'),
        IPN.EMPRESA_ROL_ID,
        IPN.PORCENTAJE_DESCUENTO,
        IPN.ESTADO 
    FROM DB_COMERCIAL.INFO_PRODUCTO_NIVEL IPN
    WHERE IPN.PRODUCTO_ID=(SELECT ID_PRODUCTO 
                           FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
                           WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
                                and AP.ESTADO='Activo' 
                                and AP.NOMBRE_TECNICO='DATOSDC' 
                                and AP.EMPRESA_COD=10)
        and IPN.ESTADO !='Eliminado';

INSERT INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
    SELECT DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DATOS DC SDWAN'),
        IPI.IMPUESTO_ID,
        IPI.PORCENTAJE_IMPUESTO,
        sysdate,
        'mdleon',
        IPI.FE_ULT_MOD,
        IPI.USR_ULT_MOD,
        IPI.ESTADO
    FROM DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO IPI
    WHERE PRODUCTO_ID=(SELECT ID_PRODUCTO 
                        FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
                        WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
                            and AP.ESTADO='Activo' 
                            and AP.NOMBRE_TECNICO='DATOSDC' 
                            and AP.EMPRESA_COD=10)
        and IPI.ESTADO !='Eliminado';


INSERT INTO DB_COMERCIAL.ADMI_COMISION_CAB 
    SELECT DB_COMERCIAL.SEQ_ADMI_COMISION_CAB.NEXTVAL,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DATOS DC SDWAN'),
        ACCA.PLAN_ID,
        sysdate,
        'mdleon',
        ACCA.IP_CREACION,
        ACCA.ESTADO
    FROM DB_COMERCIAL.ADMI_COMISION_CAB ACCA
    WHERE PRODUCTO_ID=(SELECT ID_PRODUCTO 
                       FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
                       WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
                            and AP.ESTADO='Activo' 
                            and AP.NOMBRE_TECNICO='DATOSDC'
                            and AP.EMPRESA_COD=10)
        and ACCA.ESTADO !='Eliminado' 
        and ACCA.ESTADO!='Inactivo';


INSERT INTO DB_COMERCIAL.ADMI_COMISION_DET
    SELECT DB_COMERCIAL.SEQ_ADMI_COMISION_DET.NEXTVAL,
        (SELECT ID_COMISION 
        FROM DB_COMERCIAL.ADMI_COMISION_CAB AC, DB_COMERCIAL.ADMI_PRODUCTO AP
        WHERE AP.ID_PRODUCTO=AC.PRODUCTO_ID 
            AND AP.DESCRIPCION_PRODUCTO='DATOS DC SDWAN' 
            AND AP.ESTADO='Activo' 
            AND AP.NOMBRE_TECNICO='DATOS DC SDWAN' 
            AND AP.EMPRESA_COD=10),
        ACD.PARAMETRO_DET_ID,
        ACD.COMISION_VENTA,
        sysdate,
        'mdleon',
        ACD.IP_CREACION,
        ACD.ESTADO
    FROM DB_COMERCIAL.ADMI_COMISION_DET ACD
    WHERE ACD.COMISION_ID = (select ID_COMISION 
                             from DB_COMERCIAL.ADMI_COMISION_CAB 
                             where PRODUCTO_ID=(SELECT ID_PRODUCTO 
                                                FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
                                                WHERE AP.DESCRIPCION_PRODUCTO='DATOS DC' 
                                                    and AP.ESTADO='Activo' 
                                                    and AP.NOMBRE_TECNICO='DATOSDC' 
                                                    and AP.EMPRESA_COD=10)
                                AND ESTADO='Activo');


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        APD.PARAMETRO_ID,
        APD.DESCRIPCION,
        'DATOS DC SDWAN',
        APD.VALOR2,
        APD.VALOR3,
        APD.VALOR4,
        APD.ESTADO,
        'mdleon',
        sysdate,
        APD.IP_CREACION,
        APD.USR_ULT_MOD,
        APD.FE_ULT_MOD,
        APD.IP_ULT_MOD,
        APD.VALOR5,
        APD.EMPRESA_COD,
        APD.VALOR6,
        APD.VALOR7,
        APD.OBSERVACION
    FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE PARAMETRO_ID=(SELECT DISTINCT ID_PARAMETRO 
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO='EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES') 
        AND (VALOR1='DATOS DC' OR VALOR2='DATOS DC');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        APD.PARAMETRO_ID,
        APD.DESCRIPCION,
        APD.VALOR1,
        'DATOS DC SDWAN',
        APD.VALOR3,
        APD.VALOR4,
        APD.ESTADO,
        'mdleon',
        sysdate,
        APD.IP_CREACION,
        APD.USR_ULT_MOD,
        APD.FE_ULT_MOD,
        APD.IP_ULT_MOD,
        APD.VALOR5,
        APD.EMPRESA_COD,
        APD.VALOR6,
        APD.VALOR7,
        APD.OBSERVACION
    FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE PARAMETRO_ID=(SELECT DISTINCT ID_PARAMETRO 
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO='VISUALIZACION DATOS POR DEPARTAMENTO') 
        AND (VALOR1='DATOS DC' OR VALOR2='DATOS DC');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        APD.PARAMETRO_ID,
        'DATOS DC SDWAN',
        APD.VALOR1,
        APD.VALOR2,
        APD.VALOR3,
        APD.VALOR4,
        APD.ESTADO,
        'mdleon',
        sysdate,
        APD.IP_CREACION,
        APD.USR_ULT_MOD,
        APD.FE_ULT_MOD,
        APD.IP_ULT_MOD,
        APD.VALOR5,
        APD.EMPRESA_COD,
        APD.VALOR6,
        APD.VALOR7,
        APD.OBSERVACION
    FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE PARAMETRO_ID=(SELECT DISTINCT ID_PARAMETRO 
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO='ULTIMAS MILLAS INTERNET Y DATOS') 
        AND (DESCRIPCION='DATOS DC');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        APD.PARAMETRO_ID,
        APD.DESCRIPCION,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DATOS DC SDWAN'),
        'DATOS DC SDWAN',
        APD.VALOR3,
        APD.VALOR4,
        APD.ESTADO,
        'mdleon',
        sysdate,
        APD.IP_CREACION,
        APD.USR_ULT_MOD,
        APD.FE_ULT_MOD,
        APD.IP_ULT_MOD,
        APD.VALOR5,
        APD.EMPRESA_COD,
        APD.VALOR6,
        APD.VALOR7,
        APD.OBSERVACION
    FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE PARAMETRO_ID=(SELECT DISTINCT ID_PARAMETRO 
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO='GRUPO DE SERVICIOS CON PRODUCTO REFERENCIAL') 
        AND (VALOR1='DATOS DC' OR VALOR2='DATOS DC');


UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SET estado='Inactivo'
WHERE producto_id=(SELECT ID_PRODUCTO 
                   FROM DB_COMERCIAL.ADMI_PRODUCTO 
                   WHERE DESCRIPCION_PRODUCTO='DATOS DC'
                        AND NOMBRE_TECNICO='DATOSDC') 
    and caracteristica_id=(select id_caracteristica 
                           from DB_COMERCIAL.ADMI_CARACTERISTICA 
                           where DESCRIPCION_CARACTERISTICA='SDWAN'
                                and TIPO='TECNICA');
	
COMMIT;

/