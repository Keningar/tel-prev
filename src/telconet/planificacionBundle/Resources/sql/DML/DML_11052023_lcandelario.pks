/*
 *
 * Se crean nuevos estados para la validaciòn
    cuando GTN autoriza una solicitud de excedentes de materiales
 *	 
 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.1 11-05-2023
 */

  ----------------------------------------------------------------------------
  -- CREAMOS LOS PARAMETROS PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES 
  ----------------------------------------------------------------------------

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
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
    WHERE NOMBRE_PARAMETRO = 'ESTADO_EXCEDENTES'
    ),
    'CONDICIONAR LA PREPLANIFICACION POR EL ESTADO DEL SERVICIO - EXCEDENTES',
    'Detenido',
    'PrePlanificada',
    'Replanificada',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1',
    '10'
  );  

  COMMIT;

  /
