--Cabecera para parámetros de los ids de los progresos de tareas 
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'IDS_PROGRESOS_TAREAS',
                'IDs de los progresos para tareas de soporte e instalación de TN y MD',
                'SOPORTE',
				'INGRESAR_PROGRESOS_TAREAS',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--IDs de progresos de materiales para tareas de soporte MD
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de materiales para tareas de soporte MD',
                'PROG_SOPORTE_MD_MATERIALES',
				'71,72',
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

--IDs de progresos de materiales para tareas de soporte TN
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de materiales para tareas de soporte TN',
                'PROG_SOPORTE_TN_MATERIALES',
				'70,73',
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

--IDs de progresos de fibra para tareas de instalación MD
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de fibra para tareas de instalación MD',
                'PROG_INSTALACION_MD_RUTA',
				'3',
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


--IDs de progresos de materiales para tareas de instalación MD
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de materiales para tareas de instalación MD',
                'PROG_INSTALACION_MD_MATERIALES',
				'5',
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


--IDs de progresos de acta para tareas de instalación TN
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de acta para tareas de instalación TN',
                'PROG_INSTALACION_TN_ACTA',
				'50,27',
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

--IDs de progresos de fibra para tareas de instalación TN
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de fibra para tareas de instalación TN',
                'PROG_INSTALACION_TN_RUTA',
				'46,74',
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

--IDs de progresos de materiales para tareas de instalación TN
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
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_PROGRESOS_TAREAS'),
                'IDs de progresos de materiales para tareas de instalación TN',
                'PROG_INSTALACION_TN_MATERIALES',
				'59,60',
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


