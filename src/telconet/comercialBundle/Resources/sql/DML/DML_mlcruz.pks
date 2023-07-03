SET SERVEROUTPUT ON
--Creación de la asociación de características al producto TELCOHOME
DECLARE
  Ln_IdCaractTrafficTable       NUMBER(5,0);
  Ln_IdCaractGemPort            NUMBER(5,0);
  Ln_IdCaractLineProfileName    NUMBER(5,0);
  Ln_IdCaractServiceProfile     NUMBER(5,0);
  Ln_IdCaractVlan               NUMBER(5,0);
  Ln_IdCaractGrupoNegocio       NUMBER(5,0);
  Ln_IdCaractScope              NUMBER(5,0);
  Ln_IdCaractIndiceCliente	    NUMBER(5,0);
  Ln_IdCaractSpid               NUMBER(5,0);
  Ln_IdCaractSsid               NUMBER(5,0);
  Ln_IdCaractPasswSsid          NUMBER(5,0);
  Ln_IdCaractNumeroPc           NUMBER(5,0);
  Ln_IdCaractModoOperacion      NUMBER(5,0);
  Ln_IdCaractMacOnt             NUMBER(5,0);
  Ln_IdCaractPotencia           NUMBER(5,0);
  Ln_IdCaractMac                NUMBER(5,0);
  Ln_IdProdTelcoHome            NUMBER(5,0);
  Ln_IdCaractPerfil             NUMBER(5,0);
  Ln_IdCaractMacWifi            NUMBER(5,0);
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractTrafficTable
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='TRAFFIC-TABLE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractGemPort
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='GEM-PORT';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractLineProfileName
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='LINE-PROFILE-NAME';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractServiceProfile
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SERVICE-PROFILE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractVlan
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='VLAN';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractGrupoNegocio
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='Grupo Negocio';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractScope
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SCOPE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractIndiceCliente
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='INDICE CLIENTE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractSpid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SPID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractSsid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SSID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPasswSsid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='PASSWORD SSID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractNumeroPc
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='NUMERO PC';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractModoOperacion
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MODO OPERACION';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMacOnt
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MAC ONT';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPotencia
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='POTENCIA';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMac
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MAC';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPerfil
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='PERFIL';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMacWifi
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MAC WIFI';

  SELECT ID_PRODUCTO
  INTO Ln_IdProdTelcoHome
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='TELCOHOME';
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractTrafficTable,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica TRAFFIC-TABLE');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractGemPort,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica GEM-PORT');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractLineProfileName,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica LINE-PROFILE-NAME');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractServiceProfile,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica SERVICE-PROFILE');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractVlan,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica VLAN');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractGrupoNegocio,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'SI'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica Grupo Negocio');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractScope,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica SCOPE');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractIndiceCliente,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica INDICE CLIENTE');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractSpid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica SPID');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractSsid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica SSID');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractPasswSsid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica PASSWORD SSID');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractNumeroPc,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica NUMERO PC');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractModoOperacion,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica MODO OPERACION');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractMacOnt,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica MAC ONT');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractPotencia,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica POTENCIA');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractMac,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica MAC');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractPerfil,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica PERFIL');

  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdTelcoHome,
      Ln_IdCaractMacWifi,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto TELCOHOME Caracteristica MAC WIFI');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de la asociación de características al producto IP TelcoHome
DECLARE
  Ln_IdCaractRegistroUnitario   NUMBER(5,0);
  Ln_IdCaractScope              NUMBER(5,0);
  Ln_IdProdIpTelcoHome          NUMBER(5,0);
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractRegistroUnitario
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='REGISTRO_UNITARIO';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractScope
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SCOPE';
  SELECT ID_PRODUCTO
  INTO Ln_IdProdIpTelcoHome
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='IPTELCOHOME';
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdIpTelcoHome,
      Ln_IdCaractRegistroUnitario,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      NULL
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto IP TELCOHOME Caracteristica REGISTRO_UNITARIO');
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProdIpTelcoHome,
      Ln_IdCaractScope,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto IP TELCOHOME Caracteristica SCOPE');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
SET DEFINE OFF
--Se actualiza el grupo del producto TELCOHOME
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET GRUPO = 'INTERNET Y DATOS',
SOPORTE_MASIVO = 'N',
SUBGRUPO = 'TELCOHOME',
LINEA_NEGOCIO = 'CONNECTIVITY'
WHERE NOMBRE_TECNICO IN ('TELCOHOME', 'IPTELCOHOME');

--Se actualiza la velocidad creada para el producto TELCOHOME
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2        = 'MB',
  VALOR3          = 'SI',
  VALOR4          = 'https://telcos.telconet.ec/comercial/solicitud/solicitudes/'
WHERE DESCRIPCION = 'PROD_VELOCIDAD_TELCOHOME';

--Parámetros para PRODUCTOS_ESPECIALES_UM
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    755,
    'UM FTTX',
    'TELCOHOME',
    'FTTx',
    'MD' ,
    '18' ,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'PYMETN',
    '10'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    755,
    'UM FTTX',
    'IPTELCOHOME',
    'FTTx',
    'MD' ,
    '18' ,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
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
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'NUM_TOTAL_SERVICIOS_TELCOHOME',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'COMERCIAL'
  );

--Se agrega el número de cuentas actuales y el número de cuentas contratadas por el cliente al contenido de la plantilla
UPDATE DB_COMUNICACION.ADMI_PLANTILLA
SET PLANTILLA = TO_CLOB(
      '     
<html>    
<head>        
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">    
</head>    
<body>        
<table align="center" width="100%" cellspacing="0" cellpadding="5">            
<tr>                
<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">                    
<img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>                
</td>            
</tr>            
<tr>                
<td style="border:1px solid #6699CC;">                    
<table width="100%" cellspacing="0" cellpadding="5">                        
<tr>                            
<td colspan="2">Estimado Subgerente/Gerente Comercial,</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
Por el presente se notifica la creación de las cuentas con producto {{ nombreProducto }} correspondiente al servicio detallado a continuación:                             
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2" style="text-align: center;">                                
<strong>Datos Cliente</strong>                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Cliente:</strong>                            
</td>                            
<td>{{ cliente }}</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Login:</strong>                            
</td>                            
<td>{{ loginPuntoCliente }}</td>                        
</tr>'
      )
      || TO_CLOB(
      '                        
<tr>                            
<td>                                
<strong>Jurisdicción:</strong>                            
</td>                            
<td>                                
{{ nombreJurisdiccion }}                             
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Dirección:</strong>                            
</td>                            
<td>{{ direccionPuntoCliente }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Producto:</strong>                            
</td>                            
<td>{{ descripcionProducto }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Vendedor:</strong>                            
</td>                            
<td>{{ vendedor }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Subgerente:</strong>                            
</td>                            
<td>{{ subgerente }}</td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Tipo de Orden:</strong>                            
</td>                            
<td>    
{{ tipoOrden }}                              
</td>                        
</tr>
<tr>                            
<td>                                
<strong>Velocidad:</strong>                            
</td>                            
<td><strong><label style="color:red">{{ velocidadIsb }}MB</label></strong></td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Servicio:</strong>                            
</td>                            
<td>{{ estadoServicio }}</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Fecha de Creación del Servicio:</strong>                            
</td>                            
<td>{{ fechaCreacionServicio }}</td>                        
</tr>'
      )
      || TO_CLOB(' 
{% if muestraInfoSolicitud == ''SI'' %} 
<tr>                            
<td>                                
<strong>Tipo de Solicitud:</strong>                            
</td>                            
<td>{{ tipoSolicitud }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Fecha de Creación de Solicitud:</strong>                            
</td>                            
<td>{{ fechaCreacionSolicitud }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Solicitud:</strong>                            
</td>                            
<td><strong><label style="color:red">{{ estadoSolicitud }}</label></strong></td>                        
</tr>
{% endif %}
{% if observacion!='''' %}                        
<tr>                            
<td>                                
<strong>Observación:</strong>                            
</td>                            
<td>{{ observacion | raw }}</td>                        
</tr>       
{% endif %}
{% if numTotalCuentas > 0 %}
<tr>                            
<td>                                
<strong># Total de Cuentas:</strong>                            
</td>                            
<td>{{ numTotalCuentas }}</td>                        
</tr>
{% endif %}
{% if numMinCuentas > 0 %}
<tr>                            
<td>                                
<strong># M&iacute;nimo de Cuentas:</strong>                            
</td>                            
<td>{{ numMinCuentas }}</td>                        
</tr>
{% endif %}
{% if numCuentasIngresadas > 0 %}
<tr>                            
<td>                                
<strong># de Cuentas Ingresadas:</strong>                            
</td>                            
<td>{{ numCuentasIngresadas }}</td>                        
</tr>
{% endif %}
<td colspan="2"><br/></td>                        
</tr>                    
</table>                
</td>            
</tr>            
<tr>                
<td>
</td>            
</tr>            
<tr>   
{% if prefijoEmpresa == ''TN'' %}   
<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>   
{% endif %}   
</td>                  
</tr>          
</table>    
</body>
</html>    
'),
CODIGO = 'CREA_TELCOHOME'
WHERE CODIGO = 'CREASOL_APROBSB';

UPDATE DB_COMUNICACION.ADMI_PLANTILLA
SET PLANTILLA = TO_CLOB(
      '     
<html>    
<head>        
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">    
</head>    
<body>        
<table align="center" width="100%" cellspacing="0" cellpadding="5">            
<tr>                
<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">                    
<img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>                
</td>            
</tr>            
<tr>                
<td style="border:1px solid #6699CC;">                    
<table width="100%" cellspacing="0" cellpadding="5">                        
<tr>                            
<td colspan="2">Estimados,</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
Por el presente se notifica {{ accionMail }} de las cuentas con producto {{ nombreProducto }} correspondiente al servicio detallado a continuación:                             
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2" style="text-align: center;">                                
<strong>Datos Cliente</strong>                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Cliente:</strong>                            
</td>                            
<td>{{ cliente }}</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Login:</strong>                            
</td>                            
<td>{{ loginPuntoCliente }}</td>                        
</tr>'
      )
      || TO_CLOB(
      '                        
<tr>                            
<td>                                
<strong>Jurisdicción:</strong>                            
</td>                            
<td>                                
{{ nombreJurisdiccion }}                             
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Dirección:</strong>                            
</td>                            
<td>{{ direccionPuntoCliente }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Producto:</strong>                            
</td>                            
<td>{{ descripcionProducto }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Vendedor:</strong>                            
</td>                            
<td>{{ vendedor }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Subgerente:</strong>                            
</td>                            
<td>{{ subgerente }}</td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Tipo de Orden:</strong>                            
</td>                            
<td>    
{{ tipoOrden }}                              
</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado del Servicio:</strong>                            
</td>                            
<td><strong>{{ estadoServicio }}</strong></td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Fecha de Creación del Servicio:</strong>                            
</td>                            
<td>{{ fechaCreacionServicio }}</td>                        
</tr>'
      )
      || TO_CLOB(' 
<tr>                            
<td>                                
<strong>Tipo de Solicitud:</strong>                            
</td>                            
<td>{{ tipoSolicitud }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Velocidad:</strong>                            
</td>                            
<td>{{ velocidadIsb }}MB</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Solicitud:</strong>                            
</td>                            
<td><strong>{{ estadoSolicitud }}</strong></td>                        
</tr>
{% if observacion!='''' %}                        
<tr>                            
<td>                                
<strong>Observación:</strong>                            
</td>                            
<td>{{ observacion | raw }}</td>                        
</tr>       
{% endif %}
<tr>                            
<td>                                
<strong>Solicitud {{ accionUsuario }} por:</strong>                            
</td>                            
<td><strong>{{ nombreUsuarioGestion }}</strong></td>                        
</tr>
{% if numCuentasIngresadas > 0 %}
<tr>                            
<td>                                
<strong># de Cuentas Ingresadas:</strong>                            
</td>                            
<td>{{ numCuentasIngresadas }}</td>                        
</tr>
{% endif %}
<td colspan="2"><br/></td>                        
</tr>                    
</table>                
</td>            
</tr>            
<tr>                
<td>
</td>            
</tr>            
<tr>   
{% if prefijoEmpresa == ''TN'' %}   
<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>   
{% endif %}   
</td>                  
</tr>          
</table>    
</body>
</html>    
')
WHERE CODIGO = 'APRB_RCHZ_SOLSB';

COMMIT;

--Creación de parámetros para IP_MAX_PERMITIDAS_PRODUCTO con las nuevas velocidades
DECLARE
  Ln_IdParamMapeoIpMax NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamMapeoIpMax
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='IP_MAX_PERMITIDAS_PRODUCTO';
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMapeoIpMax,
    'Mapeo de Ips máximas permitidas por punto y por producto',
    'TELCOHOME',
    'IPTELCOHOME',
    '10',
    '3',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro IP_MAX_PERMITIDAS_PROD para flujo con producto IPTELCOHOME 10MB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
DECLARE
  Ln_IdProceso NUMBER(5,0);
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
      'TAREAS DE IPCCL2 - TELCOHOME',
      'TAREAS DE IPCCL2 - TELCOHOME',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE IPCCL2 - TELCOHOME';
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
      'APROVISIONAMIENTO DE IP TELCOHOME',
      'Tarea para la asignación de IPs TelcoHome con última milla FTTx',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE
    );
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
      FE_ULT_MOD,
      VISUALIZAR_MOVIL
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'CAMBIO DE EQUIPO TELCOHOME',
      'Tarea para la gestión del cambio de cpe wifi en un servicio TelcoHome',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE,
      'N'
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
      '10',
      'Activo',
      'mlcruz',
      SYSDATE
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de proceso y tarea ingresados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
DECLARE
  Ln_IdParamNotifIp NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'INFO_NOTIF_IPTELCOHOME',
      'Información general de la notificación que se enviará en un servicio IP TelcoHome',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );

  SELECT ID_PARAMETRO
  INTO Ln_IdParamNotifIp
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='INFO_NOTIF_IPTELCOHOME'
  AND ESTADO = 'Activo';
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamNotifIp,
    'Parámetro con el departamento y la tarea que se asignará al agregar un servicio IP TelcoHome',
    'PreAsignacionInfoTecnica',
    'IPCCL2',
    'APROVISIONAMIENTO DE IP TELCOHOME',
    'Tarea autom&aacute;tica por aprovisionamiento de servicio IP',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro INFO_NOTIF_IPTELCOHOME');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
DECLARE
  Ln_IdParam NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'NUM_MIN_SERVICIOS_TELCOHOME',
      'Parámetro con el número mínimo de servicios TelcoHome por cliente',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );

  SELECT ID_PARAMETRO
  INTO Ln_IdParam
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='NUM_MIN_SERVICIOS_TELCOHOME'
  AND ESTADO = 'Activo';
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParam,
    'Parámetro con el número mínimo de servicios TelcoHome por cliente',
    '20',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro NUM_MIN_SERVICIOS_TELCOHOME');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS
DECLARE
  Ln_IdParamProdsAsociados NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS',
      'Mapeo de producto preferencial con productos asociados',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamProdsAsociados
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS';
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamProdsAsociados,
    'Mapeo de producto preferencial con productos asociados',
    'INTERNET SMALL BUSINESS',
    'IPSB',
    'VELOCIDAD',
    'SI',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS para flujo con producto INTERNET SMALL BUSINESS');
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamProdsAsociados,
    'Mapeo de producto preferencial con productos asociados',
    'INTERNET SMALL BUSINESS',
    'IPSB',
    'VELOCIDAD',
    'SI',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '26'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS para flujo con producto INTERNET SMALL BUSINESS PANAMA');
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamProdsAsociados,
    'Mapeo de producto preferencial con productos asociados',
    'TELCOHOME',
    'IPTELCOHOME',
    'VELOCIDAD_TELCOHOME',
    'SI',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro PROD_PREFERENCIAL_Y_PRODS_ASOCIADOS para flujo con producto TELCOHOME');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de plantilla para gestionar el rechazo, anulación y eliminación de un servicio TelcoHome
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar el ingreso o activación de un servicio
  INSERT
  INTO DB_COMUNICACION.ADMI_PLANTILLA
    (
      ID_PLANTILLA,
      NOMBRE_PLANTILLA,
      CODIGO,
      MODULO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      PLANTILLA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
      'Notifica a los gerentes y subgerentes al rechazar, anular y eliminar un servicio TelcoHome',
      'NOTIF_TELCOHOME',
      'TECNICO',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz',
      TO_CLOB(
      '     
<html>    
<head>        
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">    
</head>    
<body>        
<table align="center" width="100%" cellspacing="0" cellpadding="5">            
<tr>                
<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">                    
<img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>                
</td>            
</tr>            
<tr>                
<td style="border:1px solid #6699CC;">                    
<table width="100%" cellspacing="0" cellpadding="5">                        
<tr>                            
<td colspan="2">Estimado Subgerente/Gerente Comercial,</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
Por el presente se notifica {{ accion }} del servicio detallado a continuación:                             
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2" style="text-align: center;">                                
<strong>Datos Cliente</strong>                            
</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
<hr />                            
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Cliente:</strong>                            
</td>                            
<td>{{ cliente }}</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Login:</strong>                            
</td>                            
<td>{{ loginPuntoCliente }}</td>                        
</tr>'
      )
      || TO_CLOB(
      '                        
<tr>                            
<td>                                
<strong>Jurisdicción:</strong>                            
</td>                            
<td>                                
{{ nombreJurisdiccion }}                             
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Dirección:</strong>                            
</td>                            
<td>{{ direccionPuntoCliente }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Producto:</strong>                            
</td>                            
<td>{{ descripcionProducto }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Vendedor:</strong>                            
</td>                            
<td>{{ vendedor }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Subgerente:</strong>                            
</td>                            
<td>{{ subgerente }}</td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Tipo de Orden:</strong>                            
</td>                            
<td>    
{{ tipoOrden }}                              
</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Servicio:</strong>                            
</td>                            
<td>{{ estadoServicio }}</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Fecha de Creación del Servicio:</strong>                            
</td>                            
<td>{{ fechaCreacionServicio }}</td>                        
</tr>'
      )
      || TO_CLOB(' 
{% if observacion!='''' %}                        
<tr>                            
<td>                                
<strong>Observación:</strong>                            
</td>                            
<td>{{ observacion | raw }}</td>                        
</tr>       
{% endif %}
{% if numTotalCuentas > 0 %}
<tr>                            
<td>                                
<strong># Total de Cuentas:</strong>                            
</td>                            
<td>{{ numTotalCuentas }}</td>                        
</tr>
{% endif %}
{% if numCuentasIngresadas > 0 %}
<tr>                            
<td>                                
<strong># de Cuentas Ingresadas:</strong>                            
</td>                            
<td>{{ numCuentasIngresadas }}</td>                        
</tr>
{% endif %}
<td colspan="2"><br/></td>                        
</tr>                    
</table>                
</td>            
</tr>            
<tr>                
<td>
</td>            
</tr>            
<tr>   
{% if prefijoEmpresa == ''TN'' %}   
<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>   
{% endif %}   
</td>                  
</tr>          
</table>    
</body>
</html>    
')
    );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='NOTIF_TELCOHOME';
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pvallejo@telconet.ec'
  AND ESTADO      = 'Activo'
  AND EMPRESA_COD = '10';
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
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      Ln_IdAlias,
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente NOTIF_TELCOHOME');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar la creación automática de solicitudes de agregar equipo con equipos dual band
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    PLANTILLA
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificación al crearse una solicitud de planificación por el servicio TelcoHome',
    'TELCOHOME_PYL',
    'TECNICO',
    'Activo',
    CURRENT_TIMESTAMP,
    'mlcruz',
    TO_CLOB('<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    </head>
    <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                    <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
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
                                El presente correo es para informarle que se ha creado una {{ descripcionTipoSolicitud }} pendiente de coordinar
                                del servicio detallado a continuaci&oacute;n: 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos Cliente</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ login }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ tipoPlanOProducto }}:</strong>
                            </td>
                            <td>
                                {{ nombrePlanOProducto }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td colspan="2"><br/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
            <tr> 
		<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='TELCOHOME_PYL';
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pyl_corporativo@telconet.ec'
  AND ESTADO      = 'Activo'
  AND EMPRESA_COD = '10';
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
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      Ln_IdAlias,
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente TELCOHOME_PYL');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/