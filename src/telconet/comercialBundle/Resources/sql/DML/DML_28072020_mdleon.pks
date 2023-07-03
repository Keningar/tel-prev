
Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'SERIE_EQUIPO_SDWAN',
'S',
'Activo',
sysdate,
'mdleon',
'TECNICA');

Insert Into DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
VALUES
(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(Select id_producto from DB_COMERCIAL.ADMI_PRODUCTO where descripcion_producto='SECURITY SECURE SDWAN'),
(Select id_caracteristica from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA='SERIE_EQUIPO_SDWAN'),
sysdate,
'mdleon',
'Activo',
'NO');

Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'ID_SERVICIO_SDWAN',
'S',
'Activo',
sysdate,
'mdleon',
'TECNICA');

Insert Into DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
VALUES
(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(Select id_producto from DB_COMERCIAL.ADMI_PRODUCTO where descripcion_producto='SECURITY SECURE SDWAN'),
(Select id_caracteristica from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA='ID_SERVICIO_SDWAN'),
sysdate,
'mdleon',
'Activo',
'NO');

Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'FECHA_EXPIRACION_SEGURIDAD',
'S',
'Activo',
sysdate,
'mdleon',
'TECNICA');

Insert Into DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
VALUES
(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(Select id_producto from DB_COMERCIAL.ADMI_PRODUCTO where descripcion_producto='SECURITY SECURE SDWAN'),
(Select id_caracteristica from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA='FECHA_EXPIRACION_SEGURIDAD'),
sysdate,
'mdleon',
'Activo',
'NO');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'CREAR_TAREA_SEGURIDAD_L2',
'DATOS PARA LA CREACION DE LA TAREA A L2 DEL PRODUCTO SEGURIDAD SDWAN',
'COMERCIAL',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'CREAR_TAREA_SEGURIDAD_L2' and estado='Activo'),
'Parametro para crear tarea de Seguridad a IPCCL2',
'AsignadoTarea',
'IPCCL2',
'ACTIVACION NG FIREWALL',
'Tarea automatica por creacion de servicio de Seguridad',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'DIAS_FIN_SEGURIDAD',
'CANTIDAD DE DIAS ANTES DE LA FINALIZACION DEL PRODUCTO DE SEGURIDAD',
'COMERCIAL',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'DIAS_FIN_SEGURIDAD' and estado='Activo'),
'Numero de días antes de finalizar el producto de Securyti Secure Sdwan',
'30',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'CORREO_GERENTE_SEGURIDAD',
'CORREO DE GENRENTE DE SEGURIDAD PARA GESTION DEL PRODUCTO SEGURIDAD SDWAN',
'COMERCIAL',
'Activo',
'mdleon',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'CORREO_GERENTE_SEGURIDAD' and estado='Activo'),
'Correo de la gerente de Seguridad para gestion de el Producto Securyti Secure Sdwan',
'nsanjines@telconet.ec',
'Activo',
'mdleon',
sysdate,
'127.0.0.1',
10);
COMMIT;

/