/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 15-01-2023
 * Se crean las sentencias DML para eliminación de perfiles asignados a nivel de tabla SEGU_ASIGNACION de perfiles que no deberian tener accesos.
 */

-- VER DATOS DEL PUNTO--

-- Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 11602;
-- Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 11783;
-- Md_Gerente_Marketing
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9286;
-- Md_Gerente_Red_Acceso
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9163;
-- Md_Gerente_SAI
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9137;

-- VER ESTADO DE CUENTA POR PUNTO--

-- Md_Asistente_Administrativa_Gerencial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9203;
-- Md_Asistente_Contable
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9294;
-- Md_Asistente_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9132;
-- Md_Asistente_Servicio_Cliente_Sr
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 11823;
-- MD_Cobranzas_Externo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 10363;
-- Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9144;
--Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 11783;
--Md_Gerente_SAI
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9137;
--Md_Ip_Contact_Center
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6243;
--Md_Recepcionista
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9330;
--Md_supervisor_ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 7962;
--Perfil: ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 2717;
--Perfil: ATC Quito
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8522;

-- VER CASOS--

-- Md_Analista_Manager_Redes
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9285;
-- Md_Analista_Marketing
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9290;
-- Md_Asistente_Administrativo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9295;
-- Md_Asistente_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9132;
-- Md_Asistente_Servicio_Cliente_Sr
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 11823;
--Md_Coordinador_Nomina
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9349;
--Md_Disenador_Grafico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9283;
--Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9144;
--Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 11783;
--Md_Gerente_Red_Acceso
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9163;
--Md_Gerente_SAI
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9137;
--Md_Seg_Informacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9963;
--Md_Web_Master
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9364;
--Perfil: ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2717;
--Perfil: ATC Quito
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8522;

COMMIT;
/




