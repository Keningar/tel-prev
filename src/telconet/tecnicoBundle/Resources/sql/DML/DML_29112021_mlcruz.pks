SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
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
    VALOR6,
    VALOR7,
    OBSERVACION,
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
    'Valor3:Origen,Valor4:Clase,Valor5:Proceso,Valor6:Tarea,Valor7:UsrCreación,Valor8:Observación',
    'FACT_CANCEL_SERVICIO_ADICIONAL_ECDF',
    'PARAMS_CREACION_TAREA',
    'Correo Electronico',
    'REQUERIMIENTO INTERNO',
    'PROCESOS TAREAS ATC',
    'Cancelar contrato',
    'factCancelEcdf',
    'Ha ocurrido un error al generar la facturación por cancelación de servicio. A continuación el detalle:',
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
    OBSERVACION,
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
    'Valor3:CodEmpresa Empleado,Valor4:Login Empleado,Valor5:Departamento Empleado,Valor6:Ciudad Empleado',
    'FACT_CANCEL_SERVICIO_ADICIONAL_ECDF',
    'PARAMS_ASIGNACION_TAREA',
    '18',
    'spulley',
    'Sistemas',
    'GUAYAQUIL',
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
    'Valor1: Nombre de parámetro, Valor2: Nombre de proceso, Valor3: Remitente, Valor4: Asunto',
    'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO',
    'CREACION_ACTIVIDAD_PARAMETRIZADA',
    'notificaciones_telcos@telconet.ec',
    'Nueva Tarea, Actividad #{{NUMERO_TAREA}}' || ' | PROCESO: {{NOMBRE_PROCESO}}',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros para la creación de tarea del ECDF');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET DESCRIPCION = 'Valor3:Origen,Valor4:Clase,Valor5:Proceso,Valor6:Tarea,Valor7:UsrCreación,Valor8:Observación',
USR_ULT_MOD = 'mlcruz',
FE_ULT_MOD = SYSDATE
WHERE PARAMETRO_ID = 1253
AND VALOR1 = 'CORTE_MASIVO'
AND VALOR2 = 'PARAMS_CREACION_TAREA_REPORTE';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET DESCRIPCION = 'Valor3:CodEmpresa Empleado,Valor4:Login Empleado,Valor5:Departamento Empleado,Valor6:Ciudad Empleado',
USR_ULT_MOD = 'mlcruz',
FE_ULT_MOD = SYSDATE
WHERE PARAMETRO_ID = 1253
AND VALOR1 = 'CORTE_MASIVO'
AND VALOR2 = 'PARAMS_ASIGNACION_TAREA_REPORTE';

COMMIT;
/
SET DEFINE OFF
--Creación de nueva plantilla para el envío de correo al crear la tarea por revisión de reporte
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar el rechazo automático de solicitudes de planificación
  INSERT
  INTO DB_COMUNICACION.ADMI_PLANTILLA
    (
      ID_PLANTILLA,
      NOMBRE_PLANTILLA,
      CODIGO,
      MODULO,
      PLANTILLA,
      ESTADO,
      FE_CREACION,
      USR_CREACION
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
      'NOTIFICACIÓN DE ASIGNACIÓN DE TAREA POR REVISIÓN DE REPORTE DE CORTE MASIVO POR LOTES',
      'TAREAAUTOPARAM',
      'TECNICO',
      '<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
  </head>
  <body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
      <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
        </td>
      </tr>
      <tr>
        <td style="border:1px solid #6699CC;">
          <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="2">Estimado personal,</td>
            </tr>
            <tr>
              <td colspan="2">
                El presente correo es para indicarle que se asigno la siguiente TAREA: 
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                <strong>Actividad # {{NUMERO_TAREA}}</strong>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>   
             <tr>
                <td>
                   <strong>Nombre Proceso:</strong>
                </td>
                <td>
		    {{NOMBRE_PROCESO}}
                </td>
            </tr>
            <tr>
                <td>
                   <strong>Nombre Tarea:</strong>
                </td>
                <td>
		    {{NOMBRE_TAREA}}
                </td>
            </tr>
             <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>                        
            <tr>
              <td>
                <strong>Fecha de asignacion:</strong>
              </td>
              <td>{{FECHA_ASIGNACION}}</td>
            </tr>
            <tr>
              <td>
                <strong>Usuario que asigna:</strong>
              </td>
              <td>{{USUARIO_ASIGNA}}</td>
            </tr>
            <tr>
              <td>
                <strong>Asignado:</strong>
              </td>
              <td>{{ASIGNADO}}</td>
            </tr>            
            <tr>
              <td>
                <strong>Observacion:</strong>
              </td>
              <td>{{OBSERVACION}}</td>
            </tr>
            <tr>
              <td colspan="2"><br/></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
          <td>&nbsp;
          </td>
      </tr>
      <tr>
          <td>Por favor revisar la Actividad <a href="https://telcos.telconet.ec/soporte/call_activity/{{NUMERO_TAREA}}/show">{{NUMERO_TAREA}}</a>
          </td>   
      </tr>
      <tr>
          <td><strong><font size="2" face="Tahoma">Telcos + Sistema del Grupo Telconet</font></strong></p>
          </td>   
      </tr>  
    </table>
  </body>
</html>',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz'
    );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='TAREAAUTOPARAM';
  INSERT
  INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
    (
      ID_ALIAS_PLANTILLA,
      ALIAS_ID,
      PLANTILLA_ID,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      ES_COPIA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
      242,--sistemas-soporte@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente TAREAAUTOPARAM');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
