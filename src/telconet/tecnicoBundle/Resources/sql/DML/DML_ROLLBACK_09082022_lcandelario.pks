/**
* Se eliminan PARÁMETROS para condicionar en la función  de INACTIVAR la UM
*
* @author Liseth Candelario <lcandelario@telconet.ec>
* @version 1.0 09-08-2022
*
*/
---------------------------------------------------------
--------CABECERA DE PARÁMETROS-----------
---------------------------------------------------------

  DELETE
  FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
  WHERE PARAMETRO_ID IN
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
    )
  AND DESCRIPCION='ESTADOS A CONSIDERAR PARA LA INACTIVACION';
  
  
  DELETE
  FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
  WHERE PARAMETRO_ID IN
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
    )
  AND DESCRIPCION='PRODUCTOS A CONSIDERAR PARA LA INACTIVACION';
  
  DELETE
  FROM "DB_GENERAL"."ADMI_PARAMETRO_CAB"
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM';
  COMMIT;
  
  SYS.DBMS_OUTPUT.PUT_LINE('Se eliminaron parámetros a considerar para el proceso de inactivar UM en ARCGIS');
  
  COMMIT;

/
