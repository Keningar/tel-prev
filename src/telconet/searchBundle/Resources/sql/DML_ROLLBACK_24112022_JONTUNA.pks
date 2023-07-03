/*
 * @author Byron Pibaque <bpibaque@telconet.ec>
 * @version 1.0
 * @since 28-02-2023
 * Se crean las sentencias DML para rollback en caso de conflictos parámetros  relacionados con el modelo predictivo.
 */
DECLARE
  --
  Ln_ParametroId NUMBER := 0;
  --
BEGIN
  ----------------------------------------------------------------------------------
  -- ELIMINAR PARAMETRO PARA PERFILES Y TIEMPO DE ANTIGÜEDAD DE MODELO PREDICTIVO --
  ----------------------------------------------------------------------------------
  SELECT
    NVL(
      (
        SELECT
          ID_PARAMETRO
        FROM
          DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
          NOMBRE_PARAMETRO = 'PARAMETROS_MODELO_PREDICTIVO'
      ),
      0
    ) INTO Ln_ParametroId
  FROM
    DUAL;

  --
  --
  IF Ln_ParametroId != 0 THEN 

    -- Se elimina los detalles del parametro del modelo predictivo referentes a
    -- a los perfiles y al tiempo de antigüedad de la tarea de retención
        DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
      WHERE PARAMETRO_ID = Ln_ParametroId
      AND EMPRESA_COD = '18'
      AND (DESCRIPCION = 'PERFILES' OR DESCRIPCION = 'TIEMPO_RETENCION_MESES' OR DESCRIPCION = 'ESTADOS_TAREAS' OR DESCRIPCION = 'SEMAFORO_PARAMETRO' ); 

   
      
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE ID_PARAMETRO = Ln_ParametroId;

    COMMIT;

  END IF;
  
  EXCEPTION
    WHEN OTHERS THEN DBMS_OUTPUT.PUT_LINE (SQLERRM);

END;

/
