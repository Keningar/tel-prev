--Cabecera para ids de tareas que requieren registro de activos
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'IDS_TAREAS_REASIGNACION_REG_ACTIVOS',
                'IDs de las tareas de instalación que requieren registro de activos',
                'SOPORTE',
				'IDS_TAREAS_REG_ACTIVOS',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--Detalle de los ids de tareas que requieren registro de activos
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='IDS_TAREAS_REASIGNACION_REG_ACTIVOS'),
                'IDs de las tareas de instalación que requieren registro de activos',
                '849,5151,313',
				'',
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


