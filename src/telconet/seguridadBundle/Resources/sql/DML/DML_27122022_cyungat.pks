 /**
  * Se agregan los insert, en los se que agregan los perfiles faltantes para hacer las validaciones 
  * @version 1.0 27-12-2022
  * @author Christian Yunga <telcosReg@telconet.ec>
  */
 
 /**
 * BUSCAR CLIENTE DESDE TM COMERCIAL
 */
 INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 497
AND srs.ACCION_ID = 9017
AND sp.NOMBRE_PERFIL IN (
'Md_Abogado',
'Md_Abogado_Cobranzas',
'Md_Agente_Calidad_Retencion',
'Md_Agente_Calidad',
'MD_Analista_BI',
'Md_Analista_Producto',
'Md_Asesor_CallCenter_Ventas',
'Md_Asistente_Administracion_Contratos',
'Md_Asistente_Administrativa_Gerencial',
'Md_Asistente_Administrativa_Ventas',
'Md_Asistente_Cliente_Backoffice',
'Md_Asistente_Cobranzas_Bancario',
'Md_Asistente_Cobranzas_Jr',
'Md_Asistente_Cobranzas_Sr',
'Md_Asistente_Servicio_Cliente',
'Md_Auditor_Senior',
'Md_Cajero',
'Md_Coordinador_Calidad',
'Md_Coordinador_CallCenter_Ventas',
'Md_Coordinador_Cobranzas',
'Md_Coordinador_Facturacion',
'Md_Coordinador_IPCC',
'Md_Coordinador_Servicio_Cliente',
'Md_Coordinador_Tesoreria',
'Md_Coordinador_Ventas',
'Md_Distribuidor_AtencionCliente',
'Md_Distribuidor_Call',
'Md_Distribuidor_Ventas',
'Md_Ejecutivo_Ventas',
'Md_Ejecutivo_Ventas_Islas',
'Md_Gerente_Auditoria',
'Md_Gerente_Comercial',
'Md_Gerente_Marketing',
'Md_Gerente_Red_Acceso',
'Md_Gerente_SAI',
'Md_Ing_GDA',
'Md_Ing_Regulaciones',
'Md_Ing_TAP',
'Md_Jefatura_TAP',
'Md_Jefe_BigData',
'Md_Jefe_Cobranzas',
'Md_Jefe_Comunicacion_Publicidad',
'Md_Jefe_IPCC',
'Md_Jefe_Ventas',
'Md_Pasante_Comercial',
'MD_Soporte_Senior',
'Md_Tecnico_Mantenimiento_IPCC',
'Md_Tecnico_SoporteRemoto',
'Md_Tecnico_Telefonia');


 
 
/**
* BUSCAR LOGIN - GENERAL
*/
-- Buscar Login (entrada de texto, input)
  
INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 497
AND srs.ACCION_ID = 8997
AND sp.NOMBRE_PERFIL IN (
'Md_Abogado',
'Md_Abogado_Cobranzas',
'Md_Agente_Calidad',
'Md_Agente_Calidad_Retencion',
'MD_Analista_BI',
'Md_Analista_Producto',
'Md_Asesor_CallCenter_Ventas',
'Md_Asistente_Administracion_Contratos',
'Md_Asistente_Administrativa_Gerencial',
'Md_Asistente_Administrativa_Ventas',
'Md_Asistente_Cliente_Backoffice',
'Md_Asistente_Cobranzas_Bancario',
'Md_Asistente_Cobranzas_Jr',
'Md_Asistente_Cobranzas_Sr',
'Md_Asistente_Servicio_Cliente',
'Md_Auditor_Senior',
'Md_Cajero',
'Md_Coordinador_Calidad',
'Md_Coordinador_CallCenter_Ventas',
'Md_Coordinador_Cobranzas',
'Md_Coordinador_Facturacion',
'Md_Coordinador_IPCC',
'Md_Coordinador_Servicio_Cliente',
'Md_Coordinador_Tesoreria',
'Md_Coordinador_Ventas',
'Md_Distribuidor_AtencionCliente',
'Md_Distribuidor_Call',
'Md_Distribuidor_Ventas',
'Md_Ejecutivo_Ventas',
'Md_Ejecutivo_Ventas_Islas',
'Md_Gerente_Auditoria',
'Md_Gerente_Comercial',
'Md_Gerente_Marketing',
'Md_Gerente_Red_Acceso',
'Md_Gerente_SAI',
'Md_Ing_GDA',
'Md_Ing_Regulaciones',
'Md_Ing_TAP',
'Md_Jefatura_TAP',
'Md_Jefe_BigData',
'Md_Jefe_Cobranzas',
'Md_Jefe_Comunicacion_Publicidad',
'Md_Jefe_IPCC',
'Md_Jefe_Ventas',
'Md_Pasante_Comercial',
'MD_Soporte_Senior',
'Md_Tecnico_Mantenimiento_IPCC',
'Md_Tecnico_SoporteRemoto',
'Md_Tecnico_Telefonia'
);




/**
* CONSULTA DE CLIENTES - GENERAL
*/
-- Busqueda Avanzada
 
INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 497
AND srs.ACCION_ID = 8977
AND sp.NOMBRE_PERFIL IN (
'Md_Abogado',
'Md_Abogado_Cobranzas',
'Md_Agente_Calidad',
'Md_Agente_Calidad_Retencion',
'MD_Analista_BI',
'Md_Analista_Producto',
'Md_Asesor_CallCenter_Ventas',
'Md_Asistente_Administracion_Contratos',
'Md_Asistente_Administrativa_Gerencial',
'Md_Asistente_Administrativa_Ventas',
'Md_Asistente_Cliente_Backoffice',
'Md_Asistente_Cobranzas_Bancario',
'Md_Asistente_Cobranzas_Jr',
'Md_Asistente_Cobranzas_Sr',
'Md_Asistente_Servicio_Cliente',
'Md_Auditor_Senior',
'Md_Cajero',
'Md_Coordinador_Calidad',
'Md_Coordinador_CallCenter_Ventas',
'Md_Coordinador_Cobranzas',
'Md_Coordinador_Facturacion',
'Md_Coordinador_IPCC',
'Md_Coordinador_Servicio_Cliente',
'Md_Coordinador_Tesoreria',
'Md_Coordinador_Ventas',
'Md_Distribuidor_AtencionCliente',
'Md_Distribuidor_Call',
'Md_Distribuidor_Ventas',
'Md_Ejecutivo_Ventas',
'Md_Ejecutivo_Ventas_Islas',
'Md_Gerente_Auditoria',
'Md_Gerente_Comercial',
'Md_Ing_GDA',
'Md_Ing_Regulaciones',
'Md_Ing_TAP',
'Md_Jefatura_TAP',
'Md_Jefe_BigData',
'Md_Jefe_Cobranzas',
'Md_Jefe_Comunicacion_Publicidad',
'Md_Jefe_IPCC',
'Md_Jefe_Ventas',
'Md_Pasante_Comercial',
'MD_Soporte_Senior',
'Md_Tecnico_Mantenimiento_IPCC',
'Md_Tecnico_SoporteRemoto',
'Md_Tecnico_Telefonia'
);
  
  
/**
* CONSULTA DE CLIENTES – DATOS DEL PUNTO
*/
-- Datos del punto 
INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 9
AND srs.ACCION_ID = 6
AND sp.NOMBRE_PERFIL IN (
'Md_Jefe_Comunicacion_Publicidad'
);
  

/*
* CONSULTA DE CLIENTES – DATOS TÉCNICOS
*/
 

INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 151
AND srs.ACCION_ID = 8917
AND sp.NOMBRE_PERFIL IN (
'Md_Abogado',
'Md_Abogado_Cobranzas',
'Md_Agente_Calidad',
'Md_Agente_Calidad_Retencion',
'MD_Analista_BI',
'Md_Analista_Producto',
'Md_Asesor_CallCenter_Ventas',
'Md_Asistente_Administracion_Contratos',
'Md_Asistente_Administrativa_Gerencial',
'Md_Asistente_Administrativa_Ventas',
'Md_Asistente_Cliente_Backoffice',
'Md_Asistente_Cobranzas_Bancario',
'Md_Asistente_Cobranzas_Jr',
'Md_Asistente_Cobranzas_Sr',
'Md_Asistente_Servicio_Cliente',
'Md_Auditor_Senior',
'Md_Cajero',
'Md_Coordinador_Calidad',
'Md_Coordinador_CallCenter_Ventas',
'Md_Coordinador_Cobranzas',
'Md_Coordinador_Facturacion',
'Md_Coordinador_IPCC',
'Md_Coordinador_Servicio_Cliente',
'Md_Coordinador_Tesoreria',
'Md_Coordinador_Ventas',
'Md_Distribuidor_AtencionCliente',
'Md_Distribuidor_Call',
'Md_Distribuidor_Ventas',
'Md_Ejecutivo_Ventas',
'Md_Ejecutivo_Ventas_Islas',
'Md_Gerente_Auditoria',
'Md_Gerente_Comercial',
'Md_Ing_GDA',
'Md_Ing_Regulaciones',
'Md_Ing_TAP',
'Md_Jefatura_TAP',
'Md_Jefe_BigData',
'Md_Jefe_Cobranzas',
'Md_Jefe_Comunicacion_Publicidad',
'Md_Jefe_IPCC',
'Md_Jefe_Ventas',
'Md_Pasante_Comercial',
'MD_Soporte_Senior',
'Md_Tecnico_Mantenimiento_IPCC',
'Md_Tecnico_SoporteRemoto',
'Md_Tecnico_Telefonia'
);

/**
* CONSULTA DE CLIENTES – FACTURAS
*/
  
 INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 91
AND srs.ACCION_ID = 1
AND sp.NOMBRE_PERFIL IN (
'MD_Analista_BI',
'Md_Pasante_Comercial',
'Md_Ing_Regulaciones',
'Md_Analista_Producto',
'Md_Jefe_Comunicacion_Publicidad',
'Md_Distribuidor_Call',
'Md_Ing_GDA',
'Md_Jefatura_TAP',
'Md_Ing_TAP'
); 


 
 INSERT
	INTO
	DB_SEGURIDAD.SEGU_ASIGNACION
	(PERFIL_ID,
	RELACION_SISTEMA_ID,
	USR_CREACION,
	FE_CREACION,
	IP_CREACION)
SELECT sp.ID_PERFIL, srs.ID_RELACION_SISTEMA, 'telcosReg', SYSDATE, '172.0.0.1'
FROM DB_SEGURIDAD.SIST_PERFIL sp, DB_SEGURIDAD.SEGU_RELACION_SISTEMA srs 
WHERE srs.MODULO_ID = 498
AND srs.ACCION_ID = 1
AND sp.NOMBRE_PERFIL IN (
'Md_Abogado',
'Md_Auditor_Senior',
'Md_Gerente_Auditoria',
'Md_Seg_Informacion'
); 


COMMIT;
 / 
