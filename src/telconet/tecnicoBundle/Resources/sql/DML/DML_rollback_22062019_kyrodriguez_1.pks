/*DDL que reversa la ejecución exitosa del DML_07062019_1.pks*/

Delete from DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
where  USR_CREACION='REGULARIZA_DC';
COMMIT;
/