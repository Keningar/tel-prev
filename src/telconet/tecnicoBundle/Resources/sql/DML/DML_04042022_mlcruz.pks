SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valor2:Param de perfiles,Valor3:Nombres de perfiles,Valor4:Permite solicitudes simultáneas',
    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION',
    'NOMBRES_PERFILES',
    'Md_Tecnico_SoporteRemoto',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valor3:Proc ejec,Valor4:Nombre perfil,Valor5:Tipo sol,Valor6:Nombre tecn prod',
    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION',
    'FILTROS_CONSULTA_COORDINAR_SOL_Y_PRODUCTO',
    'GESTION_COORDINAR',
    'Md_Tecnico_SoporteRemoto',
    'SOLICITUD AGREGAR EQUIPO',
    'EXTENDER_DUAL_BAND',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valor3:Proc ejec,Valor4:Perfil,Valor5:Opción,Valor6:Asign perm,Valor7:Perfil personas para asignar',
    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION',
    'PERSONALIZACION_OPCIONES_GRID_COORDINAR',
    'GESTION_COORDINAR',
    'Md_Tecnico_SoporteRemoto',
    'PROGRAMAR-SOLICITUD AGREGAR EQUIPO-EXTENDER_DUAL_BAND',
    'empleado-Empleado',
    'Md_Tecnico_SoporteRemoto',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valor3:Proc ejec,Valor4:Perfil,Valor5:Opción,Valor6:Asign perm,Valor7:Perfil personas para asignar',
    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION',
    'PERSONALIZACION_OPCIONES_GRID_COORDINAR',
    'GESTION_COORDINAR',
    'Md_Tecnico_SoporteRemoto',
    'ANULAR-SOLICITUD AGREGAR EQUIPO-EXTENDER_DUAL_BAND',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valor3:Proc ejec,Valor4:Perfil usr sesión,Valor5:Rol,Valor6:Opción grid,Valor7:Nombre técnico prod',
    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION',
    'PERSONALIZACION_OPCIONES_GRID_TECNICO',
    'GESTION_TECNICA',
    'Md_Tecnico_SoporteRemoto',
    'ROLE_151-847',
    'CONFIRMAR_SERVICIO',
    'EXTENDER_DUAL_BAND',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha ingresado correctamente los parámetros para los perfiles parametrizados de opciones de COORDINACION y ACTIVACION');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Perfil Md_Tecnico_SoporteRemoto
UPDATE DB_SEGURIDAD.SIST_PERFIL
SET ESTADO = 'Modificado',
USR_ULT_MOD = 'mlcruz',
FE_ULT_MOD = SYSDATE
WHERE ID_PERFIL = 11543;

--Asignar del módulo coordinar 137 con la acción anularAjax 225 
INSERT
INTO DB_SEGURIDAD.SEGU_ASIGNACION
( 
  PERFIL_ID,
  RELACION_SISTEMA_ID,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION
)
VALUES
(
  11543,
  1034,
  'mlcruz',
  sysdate,
  '127.0.0.1'
);
COMMIT;
/