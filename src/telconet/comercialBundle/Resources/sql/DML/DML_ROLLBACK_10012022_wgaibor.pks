/**
 * Documentación ROLLBACK en caso de reverso del pase.
 * DELETE de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 14-01-2022
 */
--##############################################################################
--#########################  ADMI_PARAMETRO_DET  ###############################
--##############################################################################

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'VALIDA_IDENTIFICACION_POR_EMPRESA');

--##############################################################################
--#########################  ADMI_PARAMETRO_CAB  ###############################
--##############################################################################

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'VALIDA_IDENTIFICACION_POR_EMPRESA';

COMMIT;
/