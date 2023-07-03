/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 15-01-2023
 * Se crean las sentencias DML para eliminación de perfiles asignados a nivel de tabla SEGU_ASIGNACION de perfiles que no deberian tener acceso a opciones restringidas del sistema.
 */

-- VER DATOS DEL PUNTO--

-- Asesor_Comercial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 676;
-- Asistente_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 1238;
-- Asistente_Operaciones
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 685;
-- Asistente_Ventas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 682;
-- Auxiliar_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 684;
-- Cobranza_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 1239;
-- Cobranza_Agencias_sin_cobros
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 3037;
-- Cobranzas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 678;
-- Comercial_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 1237;
-- Consulta General
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 996;
-- Contabilidad
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 976;
-- Editar Punto Cliente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 418;
-- Ejecutivo_Sac_Comercial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 686;
-- Ejecutivo_Sac_Fact_Cob
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 683;
-- Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 677;
-- Jefe_Soporte_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 680;
-- Lider_Desarrollo_Financiero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6806;
-- Lider_Desarrollo_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6805;
-- Md_Analista_Producto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 10843;
-- Md_asesor_comercial_islas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6163;
-- Md_Asistente_Administrativa_Gerencial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9203;
-- Md_Asistente_Contable
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9294;
-- Md_Asistente_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9132;
-- Md_comercial_externo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 7182;
-- Md_Consultas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8462;
-- Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9144;
-- Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 11783;
-- Md_Ing_Red_Acceso
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9162;
-- Md_Ip_Contact_Center
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6243;
-- Md_Jefe_Administrativo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9331;
-- Md_Opu_Coordinador_Uio
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 7043;
-- Md_supervisor_ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 7962;
-- PAN_EjecutivoVentas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8004;
-- PAN_Financiero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8005;
-- Perfil Activadores Md
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 1837;
-- Perfil Activadores Md_con_excepcion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 3017;
-- Perfil: ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 2717;
-- Perfil: ATC Quito
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8522;
-- Perfil: Gepon
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 4678;
-- Perfil: JefeCobranzas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 2737;
-- Perfil: JefeIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 2677;
-- Perfil Sac Tecnico Ttco
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 679;
-- Perfil: SacTecnicoAgencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 2577;
-- Perfil: SupervisorIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 2637;
-- Tecnico_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 1256;
-- Tn_Boc_DC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5784;
-- Tn_Bodega_Asis_Ayud
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6747;
-- Tn_Cobranzas_Asistente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5871;
-- Tn_Comunicaciones_Unificadas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6702;
-- Tn_Consultas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5876;
-- Tn_Contabilidad_Tributacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6751;
-- Tn_Facturacion_Asis
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5796;
-- Tn_Facturacion_Jefe
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5867;
-- Tn_Facturacion_Provincias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8723;
-- Tn_Fiscalizacion_Fiscalizador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6802;
-- Tn_Gerente_Tecnico_Regional
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6783;
-- Tn_GestionISO_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6744;
-- Tn_Gis_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5869;
-- Tn_Gis_Dibujante
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5870;
-- Tn_IPCCL1
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5868;
-- Tn_Gis_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 9154;
-- Tn_IPCCL1
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5875;
-- Tn_IPCCL2_OTN_Asis
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5803;
-- TN_IT
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8063;
-- Tn_Noc
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5805;
-- Tn_ObrasCiviles_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8744;
-- Tn_Pac_DC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8442;
-- Tn_Procesos_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6807;
-- Tn_Procesos_Gerente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6808;
-- Tn_Procesos_Jefe
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 6809;
-- Tn_PyL_Asistente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5807;
-- Tn_PyL_CreaPuntos
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8162;
-- Tn_Radio
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5809;
-- Tn_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 7683;
-- Tn_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5812;
-- Tn_Ventas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 5791;
-- Tn_Ventas_Gerencia
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 8743;
-- Ver Punto Cliente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 866 AND perfil_id = 417;


-- VER ESTADO DE CUENTA POR PUNTO--

-- Asesor_Comercial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 676;
-- Asistente_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 1238;
-- Auxiliar_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 684;
-- Cobranza_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 1239;
-- Cobranzas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 678;
-- Consulta General
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 996;
--Contabilidad
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 976;
--Ejecutivo_Sac_Fact_Cob
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 683;
--Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 677;
--Lider_Desarrollo_Financiero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6806;
--Md_asesor_comercial_islas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6163;
--Md_Asistente_Administrativa_Gerencial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9203;
--Md_Asistente_Contable
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9294;
--Md_Asistente_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9132;
--Md_comercial_externo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 7182;
--Md_Consultas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8462;
--Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 9144;
--Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 11783;
--Md_Ip_Contact_Center
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6243;
--Md_supervisor_ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 7962;
--PAN_Financiero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8005;
--Perfil: ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 2717;
--Perfil: ATC Quito
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8522;
--Perfil: JefeIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 2677;
--Perfil Sac Tecnico Ttco
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 679;
--Perfil: SupervisorIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 2637;
--Tn_Cobranzas_Asistente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 5871;
--Tn_Consultas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 5876;
--Tn_Facturacion_Asis
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 5796;
--Tn_Facturacion_Provincias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8723;
--Tn_Gis_Dibujante
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 5870;
--Tn_Procesos_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6807;
--Tn_Procesos_Gerente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6808;
--Tn_Procesos_Jefe
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 6809;
--Tn_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 7683;
--Tn_Ventas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 5791;
--Tn_Ventas_Gerencia
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 8743;
--Ver estado de cuenta por punto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 602 AND perfil_id = 116;

-- VER CASOS--

-- Administrar Casos Todo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 413;
-- Asesor_Comercial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 676;
-- casos asignaciones
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 1957;
-- Cobranza_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 1239;
-- Consulta General
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 996;
--Ejecutivo_Sac_Fact_Cob
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 683;
-- Exportar Casos
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8803;
-- Jefe_Soporte_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 680;
--Lider_Desarrollo_Financiero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6806;
-- Lider_Desarrollo_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6805;
--Md_Asistente_Administrativa_Gerencial
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9203;
--Md_Asistente_Facturacion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9132;
--Md_Ejecutivo_Ventas_bloqueado_digital
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9144;
-- Md_Externo_SoporteRemoto
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 11783;
-- Md_Ing_Red_Acceso
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9162;
--Md_Ip_Contact_Center
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6243;
--Md_Opu_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6548;
--Md_supervisor_ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 7962;
--Md_tecnico_externo
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 7162;
--Perfil Activadores Md_con_excepcion
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 3017;
--Perfil: ATC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2717;
--Perfil: ATC Quito
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8522;
--Perfil Coordinador MD
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2017;
--Perfil: Gepon
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 4678;
--Perfil: JefeIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2677;
--Perfil Sac Tecnico Ttco
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 679;
--Perfil: SacTecnicoAgencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2577;
--Perfil: SupervisorIPCC
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 2637;
--Soporte-Casos
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 816;
--soporte_casos_tareas_actividades
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 1997;
--Tecnico_Agencias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 1256;
--Tn_Consultas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5876;
--Tn_Facturacion_Asis
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5796;
--Tn_Facturacion_Provincias
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8723;
--Tn_Gis_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5869;
--Tn_Gis_Dibujante
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5870;
--Tn_Gis_Jefe
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5868;
--Tn_Gis_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9154;
--Tn_Inicio_Soporte_Adm
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5800;
--Tn_Inicio_Soporte_Adm
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5801;
--Tn_IPCCL1
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5875;
--Tn_IPCCL2_OTN_Asis
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5803;
--Tn_Jefe_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 7873;
--Tn_Networking
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6143;
--Tn_Noc
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5805;
--Tn_ObrasCiviles_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8744;
--Tn_Procesos_Coordinador
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6807;
--Tn_Procesos_Gerente
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6808;
--Tn_Procesos_Jefe
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 6809;
--Tn_Radio
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5809;
--Tn_SeguridadLogica_Ingeniero
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9326;
--Tn_SeguridadLogica_Jefatura
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 9325;
--Tn_Tecnica_Sucursal
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 7683;
--Tn_Tecnico
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5812;
--Tn_Ventas
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 5791;
--Tn_Ventas_Gerencia
DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE relacion_sistema_id = 562 AND perfil_id = 8743;


COMMIT;
/





