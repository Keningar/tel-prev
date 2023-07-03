
----Rollback para quitar permiso de consulta para la tabla ADMI_PUNTO_ATENCION desde el esquema DB_SOPORTE

revoke SELECT on "DB_COMERCIAL"."ADMI_PUNTO_ATENCION" from "DB_SOPORTE" ;

/
