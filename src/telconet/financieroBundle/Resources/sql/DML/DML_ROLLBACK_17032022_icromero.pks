/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar parametro ACTIVACION_CICLOS_FACTURACION 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */

delete from db_general.ADMI_PARAMETRO_CAB where Nombre_Parametro ='ACTIVACION_CICLOS_FACTURACION';

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar parametros detalles de ACTIVACION_CICLOS_FACTURACION
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
-- parametros MD

delete from db_general.admi_parametro_det where Parametro_Id =(select ID_PARAMETRO from db_general.admi_parametro_Cab where nombre_parametro='ACTIVACION_CICLOS_FACTURACION');

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar los ciclos  de facturacion 3 y 4
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 18-03-2022 - Version Inicial.
 */
delete from DB_FINANCIERO.ADMI_CICLO where Usr_Creacion = 'icromero';

/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar historial de los ciclos 3 y 4
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 18-03-2022 - Version Inicial.
 */
delete from DB_FINANCIERO.ADMI_CICLO_HISTORIAL where Usr_Creacion = 'icromero';

commit;