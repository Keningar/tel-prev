/**
 * Usuario requerido para que Networking pueda consultar la vista DB_INFRAESTRUCTURA.V_PUERTO_CLIENTES
 *
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 11/06/2019
 *
 */
CREATE USER "DB_NETWORKING" IDENTIFIED BY "uwquuf.iiq" DEFAULT TABLESPACE "USERS";

GRANT "CONNECT" TO "DB_NETWORKING";
GRANT "RESOURCE" TO "DB_NETWORKING";

GRANT UNDER ANY VIEW TO "DB_NETWORKING";
GRANT CREATE ANY VIEW TO "DB_NETWORKING";
GRANT CREATE VIEW TO "DB_NETWORKING"; 
GRANT SELECT ANY TABLE TO "DB_NETWORKING";

GRANT MERGE VIEW ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT FLASHBACK ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT DEBUG ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT QUERY REWRITE ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT ON COMMIT REFRESH ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT REFERENCES ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT UPDATE ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT SELECT ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT INSERT ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";
GRANT DELETE ON "DB_INFRAESTRUCTURA"."V_PUERTO_CLIENTES" TO "DB_NETWORKING";  
GRANT SELECT ON "DB_COMERCIAL"."INFO_PERSONA" TO "DB_NETWORKING";
GRANT SELECT ON "DB_INFRAESTRUCTURA"."INFO_IP" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_SERVICIO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_SERVICIO_HISTORIAL" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_SERVICIO_TECNICO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."ADMI_PRODUCTO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_INFRAESTRUCTURA"."INFO_INTERFACE_ELEMENTO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_PUNTO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_SERVICIO_PROD_CARACT" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."ADMI_PRODUCTO_CARACTERISTICA" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."ADMI_CARACTERISTICA" TO "DB_NETWORKING";
GRANT SELECT ON "DB_INFRAESTRUCTURA"."INFO_ELEMENTO" TO "DB_NETWORKING";
GRANT SELECT ON "DB_COMERCIAL"."INFO_PERSONA_EMPRESA_ROL" TO "DB_NETWORKING";

/
