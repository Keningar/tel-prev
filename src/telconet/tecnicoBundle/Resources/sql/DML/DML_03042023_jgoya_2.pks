DECLARE
  	Lv_Json VARCHAR2(4000);
 	Lv_IdMarcaCarac NUMBER;
 	Lv_IdIpCarac NUMBER;
 	Lv_IdPropiedadCarac NUMBER;
 	Lv_IdAdministracionCarac NUMBER;
BEGIN
	
	SELECT ID_CARACTERISTICA INTO Lv_IdMarcaCarac FROM DB_COMERCIAL.ADMI_CARACTERISTICA ap WHERE DESCRIPCION_CARACTERISTICA = 'MARCA ELEMENTO';
	SELECT ID_CARACTERISTICA INTO Lv_IdIpCarac FROM DB_COMERCIAL.ADMI_CARACTERISTICA ap WHERE DESCRIPCION_CARACTERISTICA = 'IP WAN';
	SELECT ID_CARACTERISTICA INTO Lv_IdPropiedadCarac FROM DB_COMERCIAL.ADMI_CARACTERISTICA ap WHERE DESCRIPCION_CARACTERISTICA = 'PROPIETARIO DEL EQUIPO';
	SELECT ID_CARACTERISTICA INTO Lv_IdAdministracionCarac FROM DB_COMERCIAL.ADMI_CARACTERISTICA ap 
	WHERE DESCRIPCION_CARACTERISTICA = 'ADMINISTRADOR DE EQUIPO';
	
  -- Assign the value to the CLOB variable
  Lv_Json := '[{"PRODUCTO_ID":1117,"DESCRIPCION_PRODUCTO":"Networking LAN","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":
"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true, "HELPER":"Instalación simultánea Networking LAN"
,"REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":null },
{"PRODUCTO_ID":1290,"DESCRIPCION_PRODUCTO":"TELCOHOME MG1","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA",
"OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":false,"HELPER":"Instalación simultánea TELCOHOME MG1","REQUIERE_REGISTRO":true,
"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":12424,"DESCRIPCION_CARACTERISTICA":"TIPO ELEMENTO","LABEL":"TIPO ELEMENTO"},
{"ID_PRODUCTO_CARACTERISITICA":12425,"DESCRIPCION_CARACTERISTICA":"MARCA ELEMENTO","LABEL":"MARCA ELEMENTO"}]},
{"PRODUCTO_ID":1281,"DESCRIPCION_PRODUCTO":"SAFE CAM","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA","OS_AGRUPADAS":
true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea SAFE CAM","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":
[{"ID_PRODUCTO_CARACTERISITICA":12420,"DESCRIPCION_CARACTERISTICA":"IP WAN","LABEL":"IP WAN"},{"ID_PRODUCTO_CARACTERISITICA":12416,
"DESCRIPCION_CARACTERISTICA":"FIRMWARE","LABEL":"FIRMWARE"}]},
{"PRODUCTO_ID":1116,"DESCRIPCION_PRODUCTO":"Cableado Estructurado","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":
"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"HELPER":"Instalación simultánea Cableado Estructurado","REQUIERE_REGISTRO":
false,"CARACTERISTICAS_ADICIONALES":null},
{"PRODUCTO_ID":289,"DESCRIPCION_PRODUCTO":"RENTA DE ROUTER","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA",
"OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea Renta de router","REQUIERE_REGISTRO":true,
"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdMarcaCarac||',"DESCRIPCION_CARACTERISTICA":"MARCA ELEMENTO","LABEL":"MARCA"},
{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdIpCarac||',"DESCRIPCION_CARACTERISTICA":"IP WAN","LABEL":"IP","ALLOW_BLANK":true},{"ID_PRODUCTO_CARACTERISITICA":
'||Lv_IdPropiedadCarac||',"DESCRIPCION_CARACTERISTICA":"PROPIETARIO DEL EQUIPO","LABEL":"PROPIEDAD","VALORES_SELECCIONABLES":"TELCONET,CLIENTE",
"VALOR_DEFECTO":"TELCONET"},{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdAdministracionCarac||',"DESCRIPCION_CARACTERISTICA":"ADMINISTRADOR DE EQUIPO",
"LABEL":"ADMINISTRACIÓN","VALORES_SELECCIONABLES":"TELCONET,CLIENTE","VALOR_DEFECTO":"TELCONET"}]},
{"PRODUCTO_ID":1239,"DESCRIPCION_PRODUCTO":"SDWAN","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA",
"OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea Sdwan","REQUIERE_REGISTRO":true,
"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdMarcaCarac||',"DESCRIPCION_CARACTERISTICA":"MARCA ELEMENTO","LABEL":"MARCA"},
{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdIpCarac||',"DESCRIPCION_CARACTERISTICA":"IP WAN","LABEL":"IP","ALLOW_BLANK":true},{"ID_PRODUCTO_CARACTERISITICA":
'||Lv_IdPropiedadCarac||',"DESCRIPCION_CARACTERISTICA":"PROPIETARIO DEL EQUIPO","LABEL":"PROPIEDAD","VALORES_SELECCIONABLES":"TELCONET,CLIENTE",
"VALOR_DEFECTO":"TELCONET"},{"ID_PRODUCTO_CARACTERISITICA":'||Lv_IdAdministracionCarac||',"DESCRIPCION_CARACTERISTICA":"ADMINISTRADOR DE EQUIPO",
"LABEL":"ADMINISTRACIÓN","VALORES_SELECCIONABLES":"TELCONET,CLIENTE","VALOR_DEFECTO":"TELCONET"}]}]';

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = Lv_Json WHERE DESCRIPCION = 'CARACTERISTICAS_SERVICIOS_SIMULTANEOS';

  COMMIT;
END;

/