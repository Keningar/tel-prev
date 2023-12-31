-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION 849 
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'849', 						-- CODIGO_TAREA 
'INSTALACION', 				-- NOMBRE_TAREA 
'Tareas de Instalación', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);

-- INGRESANDO PROGRESOS DE TAREA DE RETIRO_EQUIPO
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'674', 						-- CODIGO_TAREA 
'RETIRO_EQUIPO', 			-- NOMBRE_TAREA 
'Tareas de Retiro de equipo', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA DE CASO_ACTA
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'0', 						-- CODIGO_TAREA 
'CASO_ACTA', 			-- NOMBRE_TAREA 
'Tareas de Soporte con Acta', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);

-- INGRESANDO PROGRESOS DE TAREA DE CASO
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'-1', 						-- CODIGO_TAREA 
'CASO', 			-- NOMBRE_TAREA 
'Tareas de Soporte sin Acta', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA INTERDEPARTAMENTAL
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'-1', 						-- CODIGO_TAREA 
'INTERDEPARTAMENTAL', 			-- NOMBRE_TAREA 
'Tareas Interdepartamentales', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_OTROS
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'850', 						-- CODIGO_TAREA 
'INSTALACION_OTROS', 			-- NOMBRE_TAREA 
'Tareas de Instalación otros', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA NETVOICE
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'-2', 						-- CODIGO_TAREA 
'NETVOICE', 			-- NOMBRE_TAREA 
'Tareas de Instalación Netvoice', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_RADIO
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'313', 						-- CODIGO_TAREA 
'INSTALACION_RADIO', 			-- NOMBRE_TAREA 
'Tareas de Instalación con última milla Radio', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);

-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_FTTX
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'5151', 						-- CODIGO_TAREA 
'INSTALACION_FTTX', 			-- NOMBRE_TAREA 
'Tareas de Instalación Telconet con última milla Fttx', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);


-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_CABLEADO
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'7661', 						-- CODIGO_TAREA 
'INSTALACION_CABLEADO', 			-- NOMBRE_TAREA 
'Tareas de Instalación de Cableado Telconet', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);



-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_WIFI_AP
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'-3', 						-- CODIGO_TAREA 
'INSTALACION_WIFI_AP', 			-- NOMBRE_TAREA 
'Tareas de Instalación de Producto Wifi DB Premium + Extender DB', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);



-- INGRESANDO PROGRESOS DE TAREA DE INSTALACION_MD_CABLEADO_ETHERNET
Insert 
into DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
ID_PROGRESOS_TAREA
, CODIGO_TAREA 
, NOMBRE_TAREA 
, DESCRIPCION_TAREA 
, ESTADO
, USR_CREACION
, FE_CREACION 
, IP_CREACION 
, USR_ULT_MOD 
, FE_ULT_MOD
) 
values 	
(
DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
'-4', 						-- CODIGO_TAREA 
'INSTALACION_MD_CABLEADO_ETHERNET', 			-- NOMBRE_TAREA 
'Tareas de Instalación de Producto Cableado Ethernet', 	-- DESCRIPCION_TAREA 
'Activo', 					-- ESTADO
'rmoranc', 					-- USR_CREACION
SYSDATE, 					-- FE_CREACION 
'127.0.0.1', 				-- IP_CREACION 
NULL, 						-- USR_ULT_MOD 
NULL 						-- FE_ULT_MOD
);



COMMIT ;

