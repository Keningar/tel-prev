--Bloque anónimo para crear un nuevo proceso de derechos del titular Actualizacion y rectificacion de datos del cliente
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProceso NUMBER(5,0);
  Ln_IdParamsServiciosMd    NUMBER;
  Lc_plantilla CLOB;
  Lc_plantilla_portabilidad CLOB;
BEGIN
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO
    (
      ID_PROCESO,
      NOMBRE_PROCESO,
      DESCRIPCION_PROCESO,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD,
      VISIBLE
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,
      'PROCESOS DERECHOS DEL TITULAR',
      'PROCESOS DERECHOS DEL TITULAR',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='PROCESOS DERECHOS DEL TITULAR';
  INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'SOLICITUD DE ACTUALIZACION Y RECTIFICACION',
      'SOLICITUD DE ACTUALIZACION Y RECTIFICACION',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE
    );
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA
    (
      ID_PROCESO_EMPRESA,
      PROCESO_ID,
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,
      Ln_IdProceso,
      '18',
      'Activo',
      'jpiloso',
      SYSDATE
    );
 
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de proceso y tarea ingresados correctamente');
  --Ingreso de parametros
INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      PROCESO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PROCESOS_DERECHOS_DEL_TITULAR',
      'PARAMETRO PADRE PARA VALORES USADOS EN EL PROYECTO DE DERECHOS DEL TITULAR.',
      'COMERCIAL',
      'DERECHOS_DEL_TITULAR',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo'
      ),
      'TAREA_AUTOMATICA_ACTUALIZACION_Y_RECTIFICACION',
      NULL,--id persona rol
      'SOLICITUD DE ACTUALIZACION Y RECTIFICACION',
      'PROCESOS DERECHOS DEL TITULAR',
      'Registro Interno',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );
    
    --crear un nueva tarea de derechos del titular Solicitud de Portabilidad
    INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'SOLICITUD DE PORTABILIDAD',
      'SOLICITUD DE PORTABILIDAD',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'),
      'TAREA_AUTOMATICA_PORTABILIDAD',
      NULL,--id persona rol
      'SOLICITUD DE PORTABILIDAD',
      'PROCESOS DERECHOS DEL TITULAR',
      'Registro Interno',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );

--crear un nueva tarea de derechos del titular Solicitud de oposición
    INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'SOLICITUD DE OPOSICION',
      'SOLICITUD DE OPOSICION',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo'
      ),
      'TAREA_AUTOMATICA_OPOSICION',
      NULL,--id persona rol
      'SOLICITUD DE OPOSICION',
      'PROCESOS DERECHOS DEL TITULAR',
      'Registro Interno',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );

--crear un nueva tarea de derechos del titular Solicitud de suspensión de tratamiento
    INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'SOLICITUD DE SUSPENSION DE TRATAMIENTO',
      'SOLICITUD DE SUSPENSION DE TRATAMIENTO',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo'
      ),
      'TAREA_AUTOMATICA_SUSPENSION_TRATAMIENTO',
      NULL,--id persona rol
      'SOLICITUD DE SUSPENSION DE TRATAMIENTO',
      'PROCESOS DERECHOS DEL TITULAR',
      'Registro Interno',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );

--crear un nueva tarea de derechos del titular Solicitud de detención de la suspensión de tratamiento
    INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'SOLICITUD DE DETENCION DE SUSPENSION DE TRATAMIENTO',
      'SOLICITUD DE DETENCION DE SUSPENSION DE TRATAMIENTO',
      'Activo',
      'jpiloso',
      'jpiloso',
      SYSDATE,
      SYSDATE
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo'
      ),
      'TAREA_AUTOMATICA_DETENCION_SUSPENSION_TRATAMIENTO',
      NULL,--id persona rol
      'SOLICITUD DE DETENCION DE SUSPENSION DE TRATAMIENTO',
      'PROCESOS DERECHOS DEL TITULAR',
      'Registro Interno',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESOS_DERECHOS_DEL_TITULAR'
      AND estado            ='Activo'
      ),
      'TIPO PERSONA PERMITIDO PARA REGISTRAR BITACORA Y TAREA AUTOMATICA',
      NULL,--id persona rol
      'Cliente',
      'PROCESOS DERECHOS DEL TITULAR',
      '',
      'Activo',
      'jpiloso',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );

    INSERT INTO DB_DOCUMENTO.ADMI_DOCUMENTO (ID_DOCUMENTO,NOMBRE,CODIGO,DESCRIPCION,PROCESO_ID,ESTADO,USUARIO_CREACION,FECHA_CREACION,USUARIO_MODIFICACION,FECHA_MODIFICACION,LLENAR_TODO_DOCUMENTO) 
    values (DB_DOCUMENTO.SEQ_ADMI_DOCUMENTO.NEXTVAL,'Derechos del titular','Derdelti','Derechos del titular','1','Activo','jpiloso',sysdate,null,null,'N');


--insert para asunto de correo
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
    'Valor1: Nombre de parámetro, Valor2: Nombre de proceso, Valor3: Remitente, Valor4: Asunto',
    'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO',
    'PROCESOS_DERECHOS_DEL_TITULAR',
    'notificacionesnetlife@netlife.info.ec',
    'ACTUALIZACIÓN DE SOLICITUD DE EJERCICIO DE DERECHOS DEL TITULAR',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado los parámetros con los remitentes y asuntos de correos por proceso');
  
--Ingreso de plantilla para correo
		Lc_plantilla := '<html lang="en" style="margin:0;padding:0"><head><script async="false" type="text/javascript" src="chrome-extension://fnjhmkhhmkbjkkabndcnnogagogbneec/in-page.js"></script><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="format-detection" content="telephone=no"><title></title><style type="text/css"> @media screen and (max-width: 480px) {
            .mailpoet_button {width:100% !important;}
        }
 @media screen and (max-width: 599px) {
            .mailpoet_header {
                padding: 10px 20px;
            }
            .mailpoet_button {
                width: 100% !important;
                padding: 5px 0 !important;
                box-sizing:border-box !important;
            }
            div, .mailpoet_cols-two, .mailpoet_cols-three {
                max-width: 100% !important;
            }
        }
</style><meta id="dcngeagmmhegagicpcmpinaoklddcgon"></head><body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0;padding:0;background-color:#ffffff">
    <table class="mailpoet_template" border="0" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td class="mailpoet_preheader" style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none" height="1">
                
            </td>
        </tr><tr><td align="center" class="mailpoet-wrapper" valign="top" style="border-collapse:collapse;background-color:#ffffff"><!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]--><table class="mailpoet_content-wrapper" border="0" width="660" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background-color:#ffffff;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"><tbody><tr><td class="mailpoet_content" align="center" style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td style="border-collapse:collapse;padding-left:0;padding-right:0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOj44NTRrb2hsOWk6P29pOjU+aWlvP25oP2w8Pz9sOWg/Pms9NTk/NCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fbanner1LDOAP.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></td>
      </tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word">
          <h3 style="margin:0 0 6.6px;mso-ansi-font-size:22px;color:#333333;font-family:'|| chr(38) ||'#39;Trebuchet MS'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Grande'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans Unicode'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans'|| chr(38) ||'#39;,Tahoma,sans-serif;font-size:22px;line-height:35.2px;mso-line-height-alt:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">'|| chr(38) ||'iexcl;Hola {{CLIENTE}}, tu solicitud de atenci'|| chr(38) ||'oacute;n de derechos de datos personales fue procesada exitosamente. </h3>
        </td>
      </tr></tbody></table></td>
              </tr></tbody></table></td>
      </tr><tr><td class="mailpoet_content" align="center" style="border-collapse:collapse">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td style="border-collapse:collapse;padding-left:0;padding-right:0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPWw6NWs5ND49Om84Pm80aTs6OG81OT40NDQ0NThrPzw4Pj09O247bit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wNDorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fbit.ly%2f2XxXwAg" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPDhpaThoPj45bzxsP2xpOWk8bDs9aTs6bjU0OzU1a2w0ND00P25vayt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2f02_netlife_cam04.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></a>
        </td>
      </tr><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaDU+a284NDlpaDk4b287bzk5NWhsaW5pOW5rPDtrbmw/az1obm41bCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fonelink.to%2fvkxtda" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwP2s8aD46OTpuNWtoNWluPW8/PThpPT1razo1OTs6Pzo0PWtpPG8+aCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2f02_netlife_cam05.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></a>
        </td>
      </tr></tbody></table></td>
              </tr></tbody></table></td>
      </tr><tr style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff"><td align="center" style="border-collapse: collapse; font-size: 0;"><!--[if mso]>
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
    <td width="440" valign="top">
    <![endif]-->
    <div style="display: inline-block; max-width: 440px; vertical-align: top; width: 100%;">
    <table width="440" class="mailpoet_cols-two" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse: collapse; width: 100%; max-width: 440px; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; margin-left: auto; margin-right: auto; padding-left: 0; padding-right: 0;"><tbody><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; padding: 10px 20px 10px 20px;">
    <table style="border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0;" width="100%" cellpadding="0"><tbody><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; text-align: center; padding: 28px 20px 10px 20px;">
    <p style="margin: 0 0 9px; color: #ffffff; font-family: '|| chr(38) ||'#39;Trebuchet MS'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Grande'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans Unicode'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans'|| chr(38) ||'#39;,Tahoma,sans-serif; font-size: 12px; line-height: 15px; margin-bottom: 0; text-align: center; padding: 0; font-style: normal; font-weight: normal;"></p>
    <a target="_blank" id="41724291" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaG45bms0OWw1aGs1bD0+bz5vNDQ/Pmk+aT44bmhpPT5rOzo0bzQ7Oyt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaTQ4b2s0aD5uPWk7PDs/aTVpPmtraW80PGs6bjw0NW8/PTQ4PDRsPSt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ffb-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724292" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNW81P2hva2g8aWg8OjpvND0/aDVpOj8+bDloPjo0bDs5Omw1aDs6Oyt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2ftwitter.com%2fnetlifeecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOzs4Pmg7azluNDQ8bz87PWw8bDxoa2xvaD85OTtpODU8Oz81NGs0ayt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ftw-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724294" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOjw9NW5sOzQ6P2toPzU+bj8/bDw1ODw9PDs8OGk8bDQ+PD5pOmg0NCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.instagram.com%2fnetlife_ecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG4+azw8OzU9bjlrPzQ1bms7a2lrOzQ1ODRsPTxpP2tuP286PGs5byt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2figg-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724295" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND04Ozw1NDhoa28+PTQ6aW9saDQ6OTg4PGw8Pzs5OT8/NDloNGhpbit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOTQrZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.linkedin.com%2fcompany%2fnetlife-ecuador%2f" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG45azo/PDw/azg9a2g1aT81PjloPjQ0PDVoaDRuPjRvNWw5Om80bit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fin-blaack.png'|| chr(38) ||'fmlBlkTk" width="32"></img></a><a target="_blank" id="41724299" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPzRsPT1uPT09PzxsPmg/ND81aD5pOjg4Pz07PTQ+PDo9Ojs0aDU/OCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.tiktok.com%2f%40netlifeecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaD9uOz81NT5oPmw+PmhvPmhsPjVoNG9rOzRraWs9PT04aGg9PGtubCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ftk-blaack.png'|| chr(38) ||'fmlBlkTk" width="32"></img></a></td>
    </tr></tbody></table></td>
    </tr></tbody></table></div>
    <!--[if mso]>
    </td>
    <td width="220" valign="top">
    <![endif]-->
    <div style="display: inline-block; max-width: 220px; vertical-align: top; width: 100%;">
    <table width="220" class="mailpoet_cols-two" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse: collapse; width: 100%; max-width: 220px; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; margin-left: auto; margin-right: auto; padding-left: 0; padding-right: 0;"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse: collapse;"><br><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbzk8PWg5OW87Pjg6OTlvbDxpPmk1OW9uPGg6OTVvbzxrOmhpPTo8OCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fFOOTERUAV.png'|| chr(38) ||'fmlBlkTk" width="220" alt="Internet de Ultra Alta Velocidad" style="height: auto; max-width: 100%; -ms-interpolation-mode: bicubic; border: 0; display: block; outline: none; text-align: center; width: 100%;"></img></td>
    </tr></tbody></table></div>
    <!--[if mso]>
    </td>
            </tr>
          </tbody>
        </table>
      <![endif]--></td>
                        </tr></tbody></table><!--[if mso]>
                    </td>
                    </tr>
                    </table>
                    <![endif]--></td>
            </tr></tbody></table></body></html>
';
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
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
        'Plantilla de Solicitudes de Derechos del Titular.',
        'DERECHO_TITULAR',
        'COMERCIAL',
        Lc_plantilla,
        'Activo',
        SYSDATE,
        'jpiloso'
    );



Lc_plantilla_portabilidad := '<html lang="en" style="margin:0;padding:0"><head><script async="false" type="text/javascript" src="chrome-extension://fnjhmkhhmkbjkkabndcnnogagogbneec/in-page.js"></script><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="format-detection" content="telephone=no"><title>Copia de Copia de Copia de Copia de Copia de Copia de Copia de Asunto</title><style type="text/css"> @media screen and (max-width: 480px) {
            .mailpoet_button {width:100% !important;}
        }
 @media screen and (max-width: 599px) {
            .mailpoet_header {
                padding: 10px 20px;
            }
            .mailpoet_button {
                width: 100% !important;
                padding: 5px 0 !important;
                box-sizing:border-box !important;
            }
            div, .mailpoet_cols-two, .mailpoet_cols-three {
                max-width: 100% !important;
            }
        }
</style><meta id="dcngeagmmhegagicpcmpinaoklddcgon"></head><body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0;padding:0;background-color:#ffffff">
    <table class="mailpoet_template" border="0" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td class="mailpoet_preheader" style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none" height="1">
                
            </td>
        </tr><tr><td align="center" class="mailpoet-wrapper" valign="top" style="border-collapse:collapse;background-color:#ffffff"><!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]--><table class="mailpoet_content-wrapper" border="0" width="660" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background-color:#ffffff;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"><tbody><tr><td class="mailpoet_content" align="center" style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td style="border-collapse:collapse;padding-left:0;padding-right:0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOj44NTRrb2hsOWk6P29pOjU+aWlvP25oP2w8Pz9sOWg/Pms9NTk/NCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fbanner1LDOAP.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></td>
      </tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word">
          <h3 style="margin:0 0 6.6px;mso-ansi-font-size:22px;color:#333333;font-family:'|| chr(38) ||'#39;Trebuchet MS'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Grande'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans Unicode'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans'|| chr(38) ||'#39;,Tahoma,sans-serif;font-size:22px;line-height:35.2px;mso-line-height-alt:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">'|| chr(38) ||'iexcl;Hola {{CLIENTE}}, gracias por escribirnos! Con respecto a tu solicitud sobre PORTABILIDAD, hemos adjuntado un archivo con toda la informaci'|| chr(38) ||'oacute;n de tus datos personales.  </h3>
        </td>
      </tr></tbody></table></td>
              </tr></tbody></table></td>
      </tr><tr><td class="mailpoet_content" align="center" style="border-collapse:collapse">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"><tbody><tr><td style="border-collapse:collapse;padding-left:0;padding-right:0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPWw6NWs5ND49Om84Pm80aTs6OG81OT40NDQ0NThrPzw4Pj09O247bit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wNDorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fbit.ly%2f2XxXwAg" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPDhpaThoPj45bzxsP2xpOWk8bDs9aTs6bjU0OzU1a2w0ND00P25vayt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2f02_netlife_cam04.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></a>
        </td>
      </tr><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse:collapse">
          <a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaDU+a284NDlpaDk4b287bzk5NWhsaW5pOW5rPDtrbmw/az1obm41bCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fonelink.to%2fvkxtda" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwP2s8aD46OTpuNWtoNWluPW8/PThpPT1razo1OTs6Pzo0PWtpPG8+aCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2f02_netlife_cam05.jpg'|| chr(38) ||'fmlBlkTk" width="660" alt="" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"></img></a>
        </td>
      </tr></tbody></table></td>
              </tr></tbody></table></td>
      </tr><tr style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff"><td align="center" style="border-collapse: collapse; font-size: 0;"><!--[if mso]>
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
    <td width="440" valign="top">
    <![endif]-->
    <div style="display: inline-block; max-width: 440px; vertical-align: top; width: 100%;">
    <table width="440" class="mailpoet_cols-two" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse: collapse; width: 100%; max-width: 440px; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; margin-left: auto; margin-right: auto; padding-left: 0; padding-right: 0;"><tbody><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; padding: 10px 20px 10px 20px;">
    <table style="border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0;" width="100%" cellpadding="0"><tbody><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; text-align: center; padding: 28px 20px 10px 20px;">
    <p style="margin: 0 0 9px; color: #ffffff; font-family: '|| chr(38) ||'#39;Trebuchet MS'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Grande'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans Unicode'|| chr(38) ||'#39;,'|| chr(38) ||'#39;Lucida Sans'|| chr(38) ||'#39;,Tahoma,sans-serif; font-size: 12px; line-height: 15px; margin-bottom: 0; text-align: center; padding: 0; font-style: normal; font-weight: normal;"></p>
    <a target="_blank" id="41724291" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaG45bms0OWw1aGs1bD0+bz5vNDQ/Pmk+aT44bmhpPT5rOzo0bzQ7Oyt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaTQ4b2s0aD5uPWk7PDs/aTVpPmtraW80PGs6bjw0NW8/PTQ4PDRsPSt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ffb-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724292" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNW81P2hva2g8aWg8OjpvND0/aDVpOj8+bDloPjo0bDs5Omw1aDs6Oyt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2ftwitter.com%2fnetlifeecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOzs4Pmg7azluNDQ8bz87PWw8bDxoa2xvaD85OTtpODU8Oz81NGs0ayt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ftw-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724294" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOjw9NW5sOzQ6P2toPzU+bj8/bDw1ODw9PDs8OGk8bDQ+PD5pOmg0NCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.instagram.com%2fnetlife_ecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG4+azw8OzU9bjlrPzQ1bms7a2lrOzQ1ODRsPTxpP2tuP286PGs5byt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2figg-blaack.png'|| chr(38) ||'fmlBlkTk" width="30"></img></a> <a target="_blank" id="41724295" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND04Ozw1NDhoa28+PTQ6aW9saDQ6OTg4PGw8Pzs5OT8/NDloNGhpbit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOTQrZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.linkedin.com%2fcompany%2fnetlife-ecuador%2f" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG45azo/PDw/azg9a2g1aT81PjloPjQ0PDVoaDRuPjRvNWw5Om80bit5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fin-blaack.png'|| chr(38) ||'fmlBlkTk" width="32"></img></a><a target="_blank" id="41724299" href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPzRsPT1uPT09PzxsPmg/ND81aD5pOjg4Pz07PTQ+PDo9Ojs0aDU/OCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wPjorZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fwww.tiktok.com%2f%40netlifeecuador" rel="noopener noreferrer"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaD9uOz81NT5oPmw+PmhvPmhsPjVoNG9rOzRraWs9PT04aGg9PGtubCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2ftk-blaack.png'|| chr(38) ||'fmlBlkTk" width="32"></img></a></td>
    </tr></tbody></table></td>
    </tr></tbody></table></div>
    <!--[if mso]>
    </td>
    <td width="220" valign="top">
    <![endif]-->
    <div style="display: inline-block; max-width: 220px; vertical-align: top; width: 100%;">
    <table width="220" class="mailpoet_cols-two" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse: collapse; width: 100%; max-width: 220px; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; margin-left: auto; margin-right: auto; padding-left: 0; padding-right: 0;"><tbody><tr><td class="mailpoet_image " align="center" valign="top" style="border-collapse: collapse;"><br><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbzk8PWg5OW87Pjg6OTlvbDxpPmk1OW9uPGg6OTVvbzxrOmhpPTo8OCt5MDw7OzQ8OT48NDsrfGRpMD9MQER/Sk5kPT04ODg0ID9MQER/Sk5mPT04ODg0K39ufXkwK24wOD8rZWlhMD0='|| chr(38) ||'url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fFOOTERUAV.png'|| chr(38) ||'fmlBlkTk" width="220" alt="Internet de Ultra Alta Velocidad" style="height: auto; max-width: 100%; -ms-interpolation-mode: bicubic; border: 0; display: block; outline: none; text-align: center; width: 100%;"></img></td>
    </tr></tbody></table></div>
    <!--[if mso]>
    </td>
            </tr>
          </tbody>
        </table>
      <![endif]--></td>
                        </tr></tbody></table><!--[if mso]>
                    </td>
                    </tr>
                    </table>
                    <![endif]--></td>
            </tr></tbody></table></body></html>
';
    INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
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
        'Plantilla de Solicitudes de Portabilidad.',
        'PORTABILIDAD',
        'COMERCIAL',
        Lc_plantilla_portabilidad,
        'Activo',
        SYSDATE,
        'jpiloso'
    );
    COMMIT;
--
  DBMS_OUTPUT.PUT_LINE('Finalizo el proceso de ingreso de plantilla');  
    COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
