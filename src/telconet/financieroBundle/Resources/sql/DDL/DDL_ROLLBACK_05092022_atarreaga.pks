/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 05-09-2022
 * Se crea script para eliminar tabla utilizada para el proceso generación de archivos débitos.
 */
  
--SE ELIMINA INDICES
DROP INDEX DB_FINANCIERO.INFO_DEB_GENERAL_CARACT_IDX1;
DROP INDEX DB_FINANCIERO.INFO_DEB_GENERAL_CARACT_IDX2;

--SE ELIMINA LA TABLA 'DB_FINANCIERO.INFO_DEBITO_GENERAL_CARACT'.
DROP TABLE DB_FINANCIERO.INFO_DEBITO_GENERAL_CARACT;

--SE ELIMINA LA SECUENCIA.
DROP SEQUENCE DB_FINANCIERO.SEQ_INFO_DEBITO_GENERAL_CARACT;

--Reverso del tamaño campo PARAMETROS_PLANIFICADO.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_GENERAL MODIFY PARAMETROS_PLANIFICADO VARCHAR2(600);

DROP INDEX DB_FINANCIERO.INFO_DEBITO_DET_IDX_6;

COMMIT;
/