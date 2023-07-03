/*
 *
 * Se crean nuevos valores para el validador de excedentes de materiales
 *	 
 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 17-11-2021
 *
 */


--------------------------------------------------------
--  CREAMOS LOS VALORES DE COPAGOS PARA EXCEDENTES DE MATERIALES EN ADMI_CARACTERISTICA
--------------------------------------------------------


-- CARACTERISTICA COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO,
    DETALLE_CARACTERISTICA
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE',
    'T',
    'Activo',
    SYSDATE,
    'lcandelario',
    NULL ,
    NULL,
    'TECNICA',
    NULL
  );

-- CARACTERISTICA COPAGOS ASUME EL CLIENTE PRECIO

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO,
    DETALLE_CARACTERISTICA
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'COPAGOS ASUME EL CLIENTE PRECIO',
    'T',
    'Activo',
    SYSDATE,
    'lcandelario',
    NULL ,
    NULL,
    'TECNICA',
    NULL
  );

-- CARACTERISTICA COPAGOS ASUME LA EMPRESA PRECIO
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO,
    DETALLE_CARACTERISTICA
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.nextval,
    'COPAGOS ASUME LA EMPRESA PRECIO',
    'T',
    'Activo',
    SYSDATE,
    'lcandelario',
    NULL ,
    NULL,
    'TECNICA',
    NULL
  );



  ----------------------------------------------------------------------------
  -- CREAMOS LOS PARAMETROS PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES 
  ----------------------------------------------------------------------------
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
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ESTADO_EXCEDENTES',
    'ESTADO PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES',
    'COMERCIAL',
    'MATERIALES_EXCEDENTES',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1'
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
    WHERE NOMBRE_PARAMETRO = 'ESTADO_EXCEDENTES'
    ),
    'CONDICIONAR PREPLANIFICACION EXCEDENTES',
    'Detenido',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1',
    '10'
  );


  -------------------------------------------------------------------------------------------------------
  -- CREAMOS UN CODIGO DEL MATERIAL FIBRA OPTICA QUE SE NECESITA REGISTRAR EN LA INFO_DETALLE_SOL_MATERIAL
  -------------------------------------------------------------------------------------------------------
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
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'CODIGO_MATERIAL',
    'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN',
    'COMERCIAL',
    'MATERIALES_EXCEDENTES',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1'
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
    WHERE NOMBRE_PARAMETRO = 'CODIGO_MATERIAL'
    ),
    'CODIGO DE MATERIAL DE FIBRA OPTICA',
    '10-08-01-021',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1',
    '10'
  );




  -----------------------------------------------------------------------
  -- MODIFICACIÓN DEL NOMBRE DE LA PLANTILLA PARA EL ENVÍO DE CORREO DE EXCEDENTE
  -----------------------------------------------------------------------

set define off;
UPDATE DB_COMUNICACION.admi_plantilla
SET NOMBRE_PLANTILLA = 'NotificacionExcesoMaterial' ,
  plantilla          =
  '<html>                        
<head>                        
</head>                        
<body>                           
<table width="100%" border="0" bgcolor="#ffffff">                            
<tr>                            
<td>                                
<table width="auto" style="border:1px solid #000000;border-color:#A9E2F3;" cellpadding="10">                              
<tr>                                 
<td align="center" style="background-color:#e5f2ff;border:1px solid #000000;border-color:#A9E2F3;">                                  
<img src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>                                
</td>                              
</tr>                                  
<tr>                                 
<td style="border:1px solid #000000;border-color:#A9E2F3;">                                  
Estimados(as),<br/><br/>                                  
A continuaci&oacute;n informaci&oacute;n de la validaci&oacute;n de excedente de material para el punto {{login}} con servicio {{producto}}.                                  
<br/> <br/> <b>                                    
{{mensaje}}                                                                           

</b> <br/>  <br/> <br/><br/>                                           
Atentamente,                                      
<br/><br/> <br/><br/>                                  
<strong>Sistema TelcoS+ </strong>                                        
</td>                              
</tr>                                
</table>                          
</td>                            
</tr>                          
</table>                        
</body>                      
</html>'  ,
  FE_ULT_MOD        = TO_CHAR(sysdate, 'YYYY-MM-DD HH:MI:SS'),
  USR_ULT_MOD       = 'lcandelario'
WHERE lower(codigo) = lower('NOTIEXCMATASE')
AND estado         <> 'Eliminado';







  ---------------------------------------------------------------------------
  -- REGISTRO DE LOS CORREOS PARA ENVIAR NOTIFICACIONES DE VALORES EXCEDENTES 
  ---------------------------------------------------------------------------

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
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'CORREO_EXCEDENTES',
    'CORREOS A ENVIAR LOS VALORES DE EXCEDENTES',
    'COMERCIAL',
    'MATERIALES_EXCEDENTES',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1'
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
    WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES'
    ),
    'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES AL ALIAS PYL',
    'pyl_corporativo@telconet.ec',
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
    WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES'
    ),
    'CORREOS ADICIONALES QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES',
    'pyl_uio@telconet.ec',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1',
    '10'
  );  



----------------------------------------------------------------------------------------
-- SE INSERTA LA PLANTILLA EN UN PARÁMETRO EL CUAL PERMITE ENVIAR CARACTERES ESPECIALES
----------------------------------------------------------------------------------------

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    WHERE NOMBRE_PARAMETRO = 'PLANTILLAS_CON_CARACTERES_ESPECIALES'
    ),
    'PLANTILLAS CON CARACTERES ESPECIALES',
    'NOTIEXCMATASE',
    'Activo',
    'lcandelario',
    SYSDATE,
    '127.0.0.1',
    ''
  );




----------------------------------------------------------------------
-- SE INSERTA AL HORARIO PARA FACTURACIÓN DE  Materiales Excedentes
----------------------------------------------------------------------
INSERT
INTO DB_GENERAL.admi_parametro_det DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'HORARIO DE PROCESAMIENTO PROPORCIONAL' ),
    'HORARIO MATERIALES EXCEDENTES TN',
    'excedentes',
    'TN',
    '23',
    '50',
    'Activo',
    'lcandelario',
    sysdate,
    '172.17.0.1',
    'lcandelario',
    sysdate,
    '172.17.0.1',
    '00',
    '18',
    NULL,
    NULL,
    NULL
  ); 
  



------------------------------------------------------------------------------------------------
-- SE INSERTA valores que permiten calcular el valor del impuesto para Materiales Excedentes - TN
------------------------------------------------------------------------------------------------

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    FUNCION_PRECIO,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    LINEA_NEGOCIO,
    FRECUENCIA
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.nextval,
    '10',
    'MAT',
    'MATERIALES',
    NULL,
    '0',
    'Activo',
    sysdate,
    'lcandelario',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    NULL,
    NULL,
    NULL,
    'S',
    'NO',
    'PRECIO=0.00',
    'N',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'OTROS',
    'OTROS',
    NULL
  );


INSERT
INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.nextval,
    (SELECT id_producto
    FROM DB_comercial.admi_producto
    WHERE codigo_producto = 'MAT'
    AND empresa_cod       = '10'
    ),
    '1',
    '12',
    sysdate,
    'lcandelario',
    sysdate,
    'lcandelario',
    'Activo'
  ) ;

  

  COMMIT;

  /
