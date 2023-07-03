/**
 * Permisos de consulta de las tablas utilizadas en el paquete CMKG_NETCAM
 *
 * @author Marlon Plúas <mpluas@telconet.ec>
 * @version 1.0 23/12/2019
 */

grant select, insert, update, references on DB_INFRAESTRUCTURA.INFO_ELEMENTO to DB_COMERCIAL WITH GRANT OPTION;
grant select on DB_INFRAESTRUCTURA.SEQ_INFO_ELEMENTO to DB_COMERCIAL WITH GRANT OPTION;
grant select, insert, update, references on DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO to DB_COMERCIAL WITH GRANT OPTION;
grant select on DB_INFRAESTRUCTURA.SEQ_INFO_HISTORIAL_ELEMENTO to DB_COMERCIAL WITH GRANT OPTION;

/
