/**
 *
 * Estructura para registrar información de clientes a migrar de OLT ZTE
 *	 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 24-01-2021
 */


CREATE TABLE "DB_INFRAESTRUCTURA"."TMP_MIGRA_OLT_ZTE"
("ID_REGISTRO" NUMBER(10,2),
"TIPO_REGISTRO" VARCHAR2(20 BYTE),
"NOMBRE_ELEMENTO_ANTIGUO" VARCHAR2(200 BYTE),
"ID_PON_ANTIGUO" NUMBER(15,2),
"NOMBRE_PON_ANTIGUO" VARCHAR2(10 BYTE),
"NOMBRE_ELEMENTO_NUEVO" VARCHAR2(200 BYTE),
"ID_PON_NUEVO" NUMBER(15,2),
"NOMBRE_PON_NUEVO" VARCHAR2(10 BYTE),
"ONT_ID" VARCHAR2(20 BYTE),
"SERVICE_PROFILE" VARCHAR2(50 BYTE),
"SERIE" VARCHAR2(50 BYTE),
"LINE_PROFILE" VARCHAR2(25 BYTE),
"VLAN" VARCHAR2(10),
"LOGIN" VARCHAR2(50 BYTE),
"IP_OLT_ANTIGUO" VARCHAR2(50 BYTE),
"IP_OLT_NUEVO" VARCHAR2(50 BYTE),
"IP_ANTIGUA" VARCHAR2(50 BYTE),
"SCOPE_ANTIGUO" VARCHAR2(50 BYTE),
"IP_NUEVA" VARCHAR2(50 BYTE),
"SCOPE_NUEVO" VARCHAR2(50 BYTE),
"ESTADO" VARCHAR2(50 BYTE) NOT NULL ENABLE,
"OBSERVACION" VARCHAR2(4000 BYTE),
"USR_CREACION" VARCHAR2(25 BYTE) NOT NULL ENABLE,
"FE_CREACION" TIMESTAMP (6) DEFAULT CURRENT_TIMESTAMP,
"USR_ULT_MOD" VARCHAR2(25 BYTE),
"FE_ULT_MOD" TIMESTAMP (6),
PRIMARY KEY ("ID_REGISTRO")
);
/ 
