/*
* Reverso del tipo de solicitud correspondiente al cambio de puerto para servicios GPON.
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0 10-11-2021
*
*/

DELETE DB_COMERCIAL.ADMI_TIPO_SOLICITUD WHERE DESCRIPCION_SOLICITUD = 'SOLICITUD CAMBIO PUERTO';

COMMIT;
/
