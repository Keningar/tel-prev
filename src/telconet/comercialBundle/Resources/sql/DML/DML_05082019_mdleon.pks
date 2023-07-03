
/*
* Se realiza la creación del producto L3MPLS SDWAN tomando como base el producto L3MPLS.
* @author David León <mdleon@telconet.ec>
* @version 1.0 16-07-2019
*/
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO 
SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
AP.EMPRESA_COD,
'L3SDWN',
'L3MPLS SDWAN',
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
'L3MPLS SDWAN',
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
AP.REQUIERE_COMISIONAR,AP.SUBGRUPO,AP.LINEA_NEGOCIO FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10; 

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
select DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
APC.CARACTERISTICA_ID,
sysdate,
APC.FE_ULT_MOD,
'mdleon',
APC.USR_ULT_MOD,
APC.ESTADO,
APC.VISIBLE_COMERCIAL
from DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC 
where producto_id=(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10)
and APC.ESTADO !='Eliminado';

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
values
(
DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
(select id_caracteristica from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA='SDWAN'
AND TIPO='TECNICA'),
sysdate,
'',
'mdleon',
'',
'Activo',
'NO'
);

INSERT INTO DB_COMERCIAL.INFO_PRODUCTO_NIVEL
SELECT DB_COMERCIAL.SEQ_INFO_PRODUCTO_NIVEL.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
IPN.EMPRESA_ROL_ID,
IPN.PORCENTAJE_DESCUENTO,
IPN.ESTADO FROM DB_COMERCIAL.INFO_PRODUCTO_NIVEL IPN
WHERE IPN.PRODUCTO_ID=(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10)
and IPN.ESTADO !='Eliminado';

INSERT INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
SELECT DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
IPI.IMPUESTO_ID,
IPI.PORCENTAJE_IMPUESTO,
sysdate,
'mdleon',
IPI.FE_ULT_MOD,
IPI.USR_ULT_MOD,
IPI.ESTADO
 FROM DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO IPI
WHERE PRODUCTO_ID=(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10)
and IPI.ESTADO !='Eliminado';

INSERT INTO DB_COMERCIAL.ADMI_COMISION_CAB 
SELECT DB_COMERCIAL.SEQ_ADMI_COMISION_CAB.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
ACCA.PLAN_ID,
sysdate,
'mdleon',
ACCA.IP_CREACION,
ACCA.ESTADO
 FROM DB_COMERCIAL.ADMI_COMISION_CAB ACCA
WHERE PRODUCTO_ID=(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10)
and ACCA.ESTADO !='Eliminado' and ACCA.ESTADO!='Inactivo';

INSERT INTO DB_COMERCIAL.ADMI_COMISION_DET
SELECT DB_COMERCIAL.SEQ_ADMI_COMISION_DET.NEXTVAL,
(SELECT ID_COMISION FROM DB_COMERCIAL.ADMI_COMISION_CAB AC, DB_COMERCIAL.ADMI_PRODUCTO AP
WHERE AP.ID_PRODUCTO=AC.PRODUCTO_ID AND
AP.DESCRIPCION_PRODUCTO='L3MPLS SDWAN' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS SDWAN' and AP.EMPRESA_COD=10),
ACD.PARAMETRO_DET_ID,
ACD.COMISION_VENTA,
sysdate,
'mdleon',
ACD.IP_CREACION,
ACD.ESTADO
FROM DB_COMERCIAL.ADMI_COMISION_DET ACD
WHERE ACD.COMISION_ID = (select ID_COMISION from DB_COMERCIAL.ADMI_COMISION_CAB where 
PRODUCTO_ID=(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO AP WHERE AP.DESCRIPCION_PRODUCTO='L3MPLS' and AP.ESTADO='Activo' and AP.NOMBRE_TECNICO='L3MPLS' and AP.EMPRESA_COD=10)
AND ESTADO='Activo');

Insert Into DB_COMERCIAL.Admi_Producto_Caracteristica 
VALUES 
(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='L3MPLS SDWAN'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='REGISTRO_UNITARIO'),
SYSDATE,
'',
'mdleon',
'',
'Activo',
'No'
);


COMMIT;

/

