DECLARE
  ln_id_param NUMBER := 0;
BEGIN



INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO REPORTE TAREAS TRACE',
    'PARAMETROS UTILIZADOS EN EL REPORTE TAREAS TRACE',
    'SOPORTE',
    'REPORTES_TRACE',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );    
    
                
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO REPORTE TAREAS TRACE';
    

    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'CORREO_REMITENTE',
      'notificaciones_telcos@telconet.ec',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );  
    
    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'CORREO_DESTINATARIO',
      'rcabrera@telconet.ec,fbermeo@telconet.ec,vrodriguez@telconet.ec,',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    ); 
    
    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'DIRECTORIO_REPORTES_TAREAS_TRACE',
      '/app/telcos/reportes/TN/tecnico/arcotel/casos/',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL   
    );     
    
    
    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'COMANDO_REPORTE',
      'gzip',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );      
    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'EXTENSION_REPORTE',
      '.gz',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );    
        
    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'PLANTILLA_NOTIFICACION_REPORTE_TRACE',
      '<html><head><meta http-equiv=Content-Type content="text/html; charset=UTF-8"></head><body>                             
        <table align="center" width="100%" cellspacing="0" cellpadding="5">                             
        <tr><td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">                              
        <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/></td></tr><tr>                                     
        <td style="border:1px solid #6699CC;">                              
        <table width="100%" cellspacing="0" cellpadding="5">                                     
        <tr><td colspan="2"><table cellspacing="0" cellpadding="2"><tr>                             
        <td colspan="2">Estimado usuario,</td></tr>                             
        <tr><td></td></tr><tr>                              
        <td>Se ejecut&#243; con &#233;xito la generaci&#243;n y envio del Reporte de Tareas - Trace : 
        <<lv_nombre_archivo_comprimir>> , con fecha de                                 
        ejecuci&#243;n desde: <<pv_fecha_inicio>>  hasta: <<pv_fecha_fin>> .</td></tr><tr>                             
        <tr><td><br><br></td></tr>                             
        <tr><td></td></tr>                             
        <td colspan="2">Atentamente,</td></tr>                             
        <tr><td></td></tr><tr>                             
        <td colspan="2"><strong>Sistema TelcoS+</strong></td></tr>                             
        </table></td></tr>                             
        <tr><td colspan="2"><br></td></tr></table>                             
        </td></tr><tr><td></td></tr>                             
        <tr><td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p></td></tr>                             
        </table></body></html>',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );        
    
    
    
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
END;
