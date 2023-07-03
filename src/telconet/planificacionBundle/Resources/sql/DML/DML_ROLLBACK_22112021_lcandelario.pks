
/*
 *
 * Se eliminan los nuevos valores para el validador de excedentes de materiales
 *	 
 * @author  Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 17-11-2021
 *
 */


--------------------------------------------------------
--  ELIMINA LOS NUEVOS VALORES PARA EXCEDENTES DE MATERIALES EN ADMI_CARACTERISTICA
--------------------------------------------------------

DELETE
FROM "DB_COMERCIAL"."ADMI_CARACTERISTICA"
WHERE DESCRIPCION_CARACTERISTICA = 'COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE';
  
DELETE
FROM "DB_COMERCIAL"."ADMI_CARACTERISTICA"
WHERE DESCRIPCION_CARACTERISTICA = 'COPAGOS ASUME EL CLIENTE PRECIO';

DELETE
FROM "DB_COMERCIAL"."ADMI_CARACTERISTICA"
WHERE DESCRIPCION_CARACTERISTICA = 'COPAGOS ASUME LA EMPRESA PRECIO';




  -----------------------------------------------------------------------
  -- ELIMINA PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES 
  -----------------------------------------------------------------------

DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'ESTADO_EXCEDENTES'
  );
DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_CAB"
WHERE NOMBRE_PARAMETRO = 'ESTADO_EXCEDENTES';




  -----------------------------------------------------------------------------------------------------
  -- ELIMINA UN CODIGO DEL MATERIAL FIBRA OPTICA QUE SE NECESITA REGISTRAR EN LA INFO_SOLICITUD_MATERIAL
  -----------------------------------------------------------------------------------------------------      
DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CODIGO_MATERIAL'
  );

DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_CAB"
WHERE NOMBRE_PARAMETRO = 'CODIGO_MATERIAL';




  -------------------------------------------------------------------------------------
  -- ELIMINA MODIFICACIÓN DEL NOMBRE DE LA PLANTILLA PARA EL ENVÍO DE CORREO DE EXCEDENTE
  -------------------------------------------------------------------------------------

UPDATE DB_COMUNICACION.admi_plantilla
SET NOMBRE_PLANTILLA = 'NotificacionExcesoMaterial' ,
  plantilla          =  '<html>
<head>    
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
</head>

<body> 
<table align="center" width="100%" cellspacing="0" cellpadding="5">  
<tr>   
<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">    
<img src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>   
</td>  
</tr>  
<tr>   
<td style="border:1px solid #6699CC;">    
<table width="100%" cellspacing="0" cellpadding="5">     
<tr>      
<td colspan="2">       
<table cellspacing="0" cellpadding="2">        
<tr>         
<td colspan="2">Estimados,</td>        
</tr>        
<tr>         
<td></td>        
</tr>        
<tr>         
<td>A continuación información de la validación de excedente de material para el punto {{login}} con servicio {{producto}}.          

<br />         
</td>        
</tr>        
<tr>         
<td>          
<br/> <br/>  {{mensaje}}  <br/>  <br/>  <br/>         

</td>        
</tr>               
</table>       
</td>     
</tr>     
<tr>      
<td></td>     
</tr>     
<tr>      
<td colspan="2">Atentamente,</td>     
</tr>     
<tr>      
<td colspan="2">       
<strong>Sistema TelcoS+</strong>      
</td>     
</tr>    
</table>   
</td>  
</tr>  
<tr>   
<td colspan="2">    
<br/>   
</td>  
</tr> 
<tr>  
<td></td> 
</tr> 
</table>
</body>

</html>'
  ,
  FE_ULT_MOD        = TO_CHAR(sysdate, 'YYYY-MM-DD HH:MI:SS'),
  USR_ULT_MOD       = 'lcandelario'
WHERE lower(codigo) = lower('NOTIEXCMATASE')
AND estado         <> 'Eliminado';  






  -----------------------------------------------------------------------
  -- ELIMINAR LOS CORREOS PARA ENVIAR NOTIFICACIONES DE VALORES EXCEDENTES 
  -----------------------------------------------------------------------

DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES'
  );
  
DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES'
  ) AND DESCRIPCION = 'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES AL ALIAS PYL';
  
DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES'
  ) AND DESCRIPCION = 'CORREOS ADICIONALES QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES';

DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_CAB"
WHERE NOMBRE_PARAMETRO = 'CORREO_EXCEDENTES';





---------------------------------------------------------------------------------------------
-- ELIMINA LA PLANTILLA EN UN PARÁMETRO EL CUAL PERMITE ENVIAR CARACTERES ESPECIALES
---------------------------------------------------------------------------------------------
DELETE
FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PLANTILLAS_CON_CARACTERES_ESPECIALES'
  ) AND DESCRIPCION = 'PLANTILLAS CON CARACTERES ESPECIALES'
  AND VALOR1 = 'NOTIEXCMATASE'; 





--------------------------------------------------------------------------------------------
-- SE ELIMINA DEL HORARIO PARA FACTURACIÓN DE  Materiales Excedentes
---------------------------------------------------------------------------------------------

DELETE FROM "DB_GENERAL"."ADMI_PARAMETRO_DET"
WHERE VALOR1 = 'excedentes' AND DESCRIPCION = 'HORARIO MATERIALES EXCEDENTES TN';



------------------------------------------------------------------------------------------------
-- SE elimina valores que permiten calcular el valor del impuesto para Materiales Excedentes - TN
------------------------------------------------------------------------------------------------

  DELETE
FROM DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
WHERE PRODUCTO_ID =
  (SELECT id_producto
  FROM DB_comercial.admi_producto
  WHERE codigo_producto = 'MAT'
  AND empresa_cod       = '10'
  )
AND usr_creacion ='lcandelario';


DELETE
FROM DB_COMERCIAL.ADMI_PRODUCTO
WHERE EMPRESA_COD   = 10
AND codigo_producto = 'MAT'
AND usr_creacion    ='lcandelario';     

COMMIT;
/