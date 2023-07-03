
/*
* Se modifica tipo_enlace de la tabla DB_COMERCIAL.INFO_SERVICIO_TECNICO, permitiendo insertar datos de mayor tamaño.
* @author David León <mdleon@telconet.ec>
* @version 1.0 05-07-2019
*/

ALTER TABLE DB_COMERCIAL.INFO_SERVICIO_TECNICO MODIFY tipo_enlace varchar2 (12);

/