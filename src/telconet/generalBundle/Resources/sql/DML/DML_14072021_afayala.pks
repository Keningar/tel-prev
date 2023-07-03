/**
 * Documentación para 'DML_14072021_afayala.pks'
 * Pks que permite la creación de parámetros para producto SECURE CPE
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 14-07-2021
 */

SET SERVEROUTPUT ON
--Creación de parámetro para el producto secure cpe
DECLARE
  Ln_IdParamCab    NUMBER;
BEGIN
  Ln_IdParamCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

  --INSERT ADMI_PARAMETRO_CAB
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
			(Ln_IdParamCab,
			'PROD_CPE FORTI',
			'PROD_CPE FORTI',
			'COMERCIAL',
			NULL,
			'Activo',
			'afayala',
			SYSDATE,
			'127.0.0.1',
			NULL,
			NULL,
			NULL
			);
 
  -- INSERT ADMI_PARAMETRO_DET
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
				        SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
								Ln_IdParamCab,
								'PROD_CPE FORTI',
								AP.VALOR1,
								AP.VALOR2,
								AP.VALOR3,
								AP.VALOR4,
								AP.ESTADO,
								'afayala',
								SYSDATE,
								'127.0.0.1',
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL
				FROM DB_GENERAL.ADMI_PARAMETRO_DET AP 
				WHERE AP.PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE 
							NOMBRE_PARAMETRO = 'PROD_SEC MODELO FIREWALL');

  
  Ln_IdParamCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
  
  --INSERT ADMI_PARAMETRO_CAB
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
			(Ln_IdParamCab,
			'PROD_SEC PLAN SECURE CPE',
			'PROD_SEC PLAN SECURE CPE',
			'COMERCIAL',
			NULL,
			'Activo',
			'afayala',
			SYSDATE,
			'127.0.0.1',
			NULL,
			NULL,
			NULL
			);
 
  -- INSERT ADMI_PARAMETRO_DET
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
				        SELECT DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
								Ln_IdParamCab,
								'PROD_SEC PLAN SECURE CPE',
								AP.VALOR1,
								AP.VALOR2,
								AP.VALOR3,
								AP.VALOR4,
								AP.ESTADO,
								'afayala',
								SYSDATE,
								'127.0.0.1',
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL,
								NULL
				FROM DB_GENERAL.ADMI_PARAMETRO_DET AP 
				WHERE AP.PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE 
							NOMBRE_PARAMETRO = 'PROD_SEC PLAN SECURE SDWAN');								

  SYS.DBMS_OUTPUT.PUT_LINE('Se creó parametro de CPE FORTI');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/

--INSERT PARAMETRO PARA NOMBRE TECNICO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'NOMBRE_TECNICO_PRODUCTOS',
        'Parametro de nombre tecnico para producto secure cpe',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'NOMBRE_TECNICO_PRODUCTOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
	VALOR3,
	VALOR4,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NOMBRE_TECNICO_PRODUCTOS'
            AND ESTADO = 'Activo'
        ),
        'Productos parametrizados para validación nombre tecnico',
	'SECURE CPE',
        'SECSALES',
        'FORTIGATE',
	NULL,
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'CREAR_TAREA_SEGURIDAD_CPE_L2',
'DATOS PARA LA CREACION DE LA TAREA A L2 DEL PRODUCTO SEGURIDAD SECURE CPE',
'COMERCIAL',
'Activo',
'afayala',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'CREAR_TAREA_SEGURIDAD_CPE_L2' and estado='Activo'),
'Parametro para crear tarea de Seguridad a IPCCL2',
'AsignadoTarea',
'IPCCL2',
'ACTIVACION SECURE CPE',
'Tarea automatica por creacion de servicio de Seguridad CPE',
'Activo',
'afayala',
sysdate,
'127.0.0.1',
10);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'DIAS_FIN_SEGURIDAD_CPE',
'CANTIDAD DE DIAS ANTES DE LA FINALIZACION DEL PRODUCTO DE SEGURIDAD CPE',
'COMERCIAL',
'Activo',
'afayala',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'DIAS_FIN_SEGURIDAD_CPE' and estado='Activo'),
'Numero de días antes de finalizar el producto de Secure Cpe',
'30',
'Activo',
'afayala',
sysdate,
'127.0.0.1',
10);


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'CORREO_GERENTE_SEGURIDAD_CPE',
'CORREO DE GERENTE DE SEGURIDAD PARA GESTION DEL PRODUCTO SEGURIDAD CPE',
'COMERCIAL',
'Activo',
'afayala',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'CORREO_GERENTE_SEGURIDAD_CPE' and estado='Activo'),
'Correo de la gerente de Seguridad para gestion de el Producto Secure Cpe',
'nsanjines@telconet.ec',
'Activo',
'afayala',
sysdate,
'127.0.0.1',
10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
values
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'PRODUCTO_RELACIONADO_SECURE_CPE',
'PRODUCTOS QUE SE RELACIONAN CON SECURE CPE ',
'COMERCIAL',
'Activo',
'afayala',
sysdate,
'127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'PRODUCTO_RELACIONADO_SECURE_CPE' and estado='Activo'),
'RELACIONAR EL PRODUCTO SECURE CPE',
'Internet MPLS',
'SECURE CPE',
'236',
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SECURE CPE' AND ESTADO = 'Activo'),
'Activo',
'afayala',
sysdate,
'127.0.0.1',
10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where nombre_parametro = 'PRODUCTO_RELACIONADO_SECURE_CPE' and estado='Activo'),
'RELACIONAR EL PRODUCTO SECURE CPE',
'L3MPLS',
'SECURE CPE',
'237',
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SECURE CPE' AND ESTADO = 'Activo'),
'Activo',
'afayala',
sysdate,
'127.0.0.1',
10);

COMMIT;
/
