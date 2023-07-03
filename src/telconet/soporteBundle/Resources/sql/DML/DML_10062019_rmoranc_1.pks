--SE INSERTAN PARAMETROS DE CABECERA DEL NOMBRE DE LA TAREA NETVOICE
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
(
                ID_PARAMETRO,
				NOMBRE_PARAMETRO,
				DESCRIPCION,
				MODULO,
				PROCESO,
				ESTADO,
				USR_CREACION,
				FE_CREACION,
				IP_CREACION,
				USR_ULT_MOD,
				FE_ULT_MOD,
				IP_ULT_MOD
) 
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'TAREA_NETVOICE',
                'NOMBRE DE LA TAREA DE INSTALACIÓN NETVOICE',
                'SOPORTE',
				'INSTALACION_NV',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--SE INSERTAN PARAMETROS DEL DETALLE DE LA TAREA NETVOICE

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
				USR_ULT_MOD,
				FE_ULT_MOD,
				IP_ULT_MOD,
				VALOR5,
				EMPRESA_COD,
				VALOR6,
				VALOR7,
				OBSERVACION
) 
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='TAREA_NETVOICE'),
                'NOMBRE DE LA TAREA DE INSTALACIÓN NETVOICE',
                'NETVOICE',
				NULL,
				NULL,
				NULL,
				'Activo',
				'rmoranc',
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
);


commit;

/