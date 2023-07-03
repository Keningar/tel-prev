/**
* Se crean PARÁMETROS para condicionar en la función  de INACTIVAR la UM
*
* @author Liseth Candelario <lcandelario@telconet.ec>
* @version 1.0 09-08-2022
*
*/
SET SERVEROUTPUT ON
---------------------------------------------------------
--------CABECERA DE PARÁMETROS-----------
---------------------------------------------------------
DECLARE
  Ln_IdParametroCab NUMBER;
BEGIN
  Ln_IdParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      id_parametro,
      nombre_parametro,
      descripcion,
      modulo,
      proceso,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion
    )
    VALUES
    (
      Ln_IdParametroCab,
      'PARAMETROS_INACTIVAR_UM',
      'PARAMETROS QUE PERMITEN INACTIVAR LA UM EN ARCGIS',
      'TECNICO',
      'INACTIVAR_UM',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1'
    );
  -----------------------------------------------------------------------
  --------DETALLE DE ESTADOS DE PARÁMETROS-----------
  -----------------------------------------------------------------------
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'ESTADOS A CONSIDERAR PARA LA INACTIVACION',
      'Anulado',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'ESTADOS A CONSIDERAR PARA LA INACTIVACION',
      'Eliminado',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'ESTADOS A CONSIDERAR PARA LA INACTIVACION',
      'Rechazada',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'ESTADOS A CONSIDERAR PARA LA INACTIVACION',
      'Cancel',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );
  -----------------------------------------------------------------------
  --------DETALLE DE PRODUCTOS DE PARÁMETROS-----------
  -----------------------------------------------------------------------

  -- 1.- Internet Dedicado
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '242',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );

  -- 2.- Internet MPLS
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '236',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );

  -- 3.- L3MPLS
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '237',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );

  -- 4.- Concentrador L3MPLS
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '238',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );

  -- 5.- Internet SDWAN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '1246',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );
    
   -- 6.- L3MPLS SDWAN  
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      id_parametro_det,
      parametro_id,
      descripcion,
      valor1,
      estado,
      usr_creacion,
      fe_creacion,
      ip_creacion,
      empresa_cod
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_INACTIVAR_UM'
      ),
      'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
      '1258',
      'Activo',
      'lcandelario',
      SYSDATE,
      '127.0.0.1',
      '10'
    );

  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se creo parámetros a considerar para el proceso de inactivar UM en ARCGIS');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
