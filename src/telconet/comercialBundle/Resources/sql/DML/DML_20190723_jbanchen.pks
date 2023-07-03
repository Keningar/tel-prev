--=======================================================================
--   Se crea plantilla para el envio de notificacion
--   cuando se modifique los datos de envio.
--=======================================================================
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
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Actualizacion de informacion',
    'ACT-INFORMACION',
    'COMERCIAL',
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
<td colspan="3">Estimado personal,</td>            
</tr>            
<tr>              
<td colspan="3">                  
El presente correo es para indicarle que se MODIFICARON los datos de contacto de envío de facturación, mostrados a continuación:
</td>            
</tr>            
<tr> <td colspan="3"> <hr /> </td> </tr>            
<tr>                
<td colspan="3" style="text-align: center;"> <strong>Cliente {{ nombreClienteRazonSocial }}</strong> </td>            
</tr>            
<tr>                 
<td> <strong>Items</strong>   </td> <td>   <strong>Dato Actual</strong> </td> <td>   <strong>Dato Anterior</strong>   </td>            
</tr>
{% if nombreEnvio != "" or nombreEnvioAnt != ""  %}            
<tr>                
<td>  <strong>Nombre de envío:</strong> </td> <td> {{ nombreEnvio }} </td> <td> {{ nombreEnvioAnt }} </td>            
</tr>
{% endif %}
{% if actCiudad != "" or antCiudad != ""  %}
<tr>                
<td> <strong>Ciudad:</strong> </td> <td> {{ actCiudad }} </td> <td> {{ antCiudad }} </td>            
</tr>
{% endif %}
{% if actParroquia != "" or antParroquia != ""  %}
<tr>                
<td> <strong>Parroquia:</strong> </td> <td> {{ actParroquia }} </td> <td> {{ antParroquia }} </td>            
</tr>  
{% endif %}
{% if actSector != "" or antSector != ""  %}
<tr>                
<td> <strong>Sector:</strong> </td> <td> {{ actSector }} </td> <td> {{ antSector }} </td>            
</tr>
{% endif %}
{% if direccionEnvio != "" or direccionEnvioAnt != ""  %}            
<tr>                
<td> <strong>Dirección de envío:</strong> </td> <td> {{ direccionEnvio }} </td> <td> {{ direccionEnvioAnt }} </td>            
</tr>
{% endif %}
{% if emailEnvio != "" or emailEnvioAnt != ""  %}                        
<tr>                
<td> <strong>Dato correo electrónico:</strong> </td> <td> {{ emailEnvio }} </td> <td> {{ emailEnvioAnt }} </td>            
</tr>
{% endif %}
{% if telefonoEnvio != "" or telefonoEnvioAnt != ""  %}             
<tr>                
<td> <strong>Dato Teléfono:</strong> </td> <td> {{ telefonoEnvio }} </td> <td> {{ telefonoEnvioAnt }} </td>            
</tr>
{% endif %}            
<tr>                
<td colspan="3"><br/></td>            
</tr>            
<tr>                
<td colspan="3"> Se detalla el nombre de la persona y la fecha en que se realizó la modificación.</td>            
</tr>            
<tr>                 
<td> <strong>Usuario que modifico:</strong> </td> <td>  {{ nombreUsuario }} </td>            
</tr>            
<tr>                
<td> <strong>Fecha de modificación:</strong> </td> <td> {{ horaFechaMod }} </td>            
</tr>            
<tr>                
<td colspan="3"><br/></td>            
</tr>          
</table>        
</td>      
</tr>      
<tr>          
<td> </td>      
</tr>           

<tr>          
{% if empresa == ''TTCO'' %}          
<td><strong><font size=''2'' face=''Tahoma''>TransTelco S.A.</font></strong></p>          
{% elseif empresa == ''MD'' %}          
<td><strong><font size=''2'' face=''Tahoma''>MegaDatos S.A.</font></strong></p>          
{% else %}          
<td><strong><font size=''2'' face=''Tahoma''>Telconet S.A.</font></strong></p>          
{% endif %}          
</td>         
</tr>      
</table>   
</body>   
</html>'
    ,
    'Activo',
    sysdate,
    'jbanchen',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea los siguientes parametros para el envio de notificacion
--   cuando se modifique los datos de envio.
--=======================================================================

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
    'MODI_DATO_ENV_FACT',
    'ENVIO DE LA MODIFICACION DE DATOS DE ENVIO',
    'COMERCIAL',
    'AGREGAR_PADRE_FACTURACION',
    'Activo',
    'jbanchen',
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
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'MODI_DATO_ENV_FACT'
    ),
    'ASIGNA CORREO DE ENVIO FACTURACION',
    'R1',
    'facturacion_gye@telconet.ec',
    NULL,
    NULL,
    'Activo',
    'jbanchen',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
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
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'MODI_DATO_ENV_FACT'
    ),
    'ASIGNA CORREO DE ENVIO FACTURACION',
    'R2',
    'facturacion_uio@telconet.ec',
    NULL,
    NULL,
    'Activo',
    'jbanchen',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );


COMMIT;
/