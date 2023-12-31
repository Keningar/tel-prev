grant EXECUTE on NAF47_TNET.GEKG_TRANSACCION to DB_INFRAESTRUCTURA;

   CREATE TABLE "DB_INFRAESTRUCTURA"."TMP_MIGRA_TELCOGRAPH" 
   ("ID_MIGRA_TELCOGRAPH" NUMBER(11,2), 
	"LOGIN_AUX" VARCHAR2(200) NOT NULL ENABLE,
	"CORREO_TECNICO" VARCHAR2(200 BYTE) NOT NULL ENABLE, 
	"URL_TELCOGRAPH" VARCHAR2(200 BYTE) NOT NULL ENABLE, 
        "ORGANIZACION" VARCHAR2(300 BYTE) NOT NULL ENABLE, 
        "ZABBIX_ID" VARCHAR2(300 BYTE) NOT NULL ENABLE, 
	"VALOR1" VARCHAR2(200 BYTE), 
	"VALOR2" VARCHAR2(200 BYTE), 
	"VALOR3" VARCHAR2(200 BYTE), 
	"VALOR4" VARCHAR2(200 BYTE), 
	"ESTADO" VARCHAR2(15 BYTE), 
	"OBSERVACION" VARCHAR2(4000), 
	"USR_CREACION" VARCHAR2(15 BYTE) NOT NULL ENABLE, 
	"FE_CREACION" TIMESTAMP (6) DEFAULT CURRENT_TIMESTAMP, 
	"IP_CREACION" VARCHAR2(16 BYTE) NOT NULL ENABLE, 
	"USR_ULT_MOD" VARCHAR2(15 BYTE), 
	"FE_ULT_MOD" TIMESTAMP (6),
    PRIMARY KEY ("ID_MIGRA_TELCOGRAPH")
   );
