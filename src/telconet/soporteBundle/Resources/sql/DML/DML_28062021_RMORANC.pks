--Cabecera de PREFIJOS_EMPRESA
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'PREFIJOS_EMPRESA',
                'Información de empresas del grupo Telconet',
                'TECNICO',
				'INSTALACION_SOPORTE',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--Detalle de la empresa MEGADATOS.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PREFIJOS_EMPRESA'),
                'MEGADATOS',
                '18',
				'MD',
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

--Detalle de la empresa TELCONET.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PREFIJOS_EMPRESA'),
                'TELCONET',
                '10',
				'TN',
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

COMMIT;

