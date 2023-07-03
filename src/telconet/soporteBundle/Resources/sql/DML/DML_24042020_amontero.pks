DECLARE 
   type niveltresarray IS VARRAY(240) OF VARCHAR2(300); 
   hipotesisn1 VARCHAR2(200);
   hipotesisn2 VARCHAR2(200);
   hipotesisn3 VARCHAR2(200);

   countn1 NUMBER;
   countn2 NUMBER;
   countn3 NUMBER;
   countn3_1 NUMBER;
   transInsert NUMBER := 0;
   transUpdate NUMBER := 0;
   niveltres niveltresarray; 

   total integer; 
BEGIN 

   niveltres := niveltresarray(
         'Cambio de fuente de poder|ONT|PROBLEMA FÍSICO CPE',
         'Cambio por retención al modelo EG8145V5|ONT|PROBLEMA FÍSICO CPE',
         'Cambio por retención al modelo HS8245W|ONT|PROBLEMA FÍSICO CPE',
         'Cambio de orientación de antenas|ONT|PROBLEMA FÍSICO CPE',
         'Revisión de equipo en campo|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8045H por HG8045H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8045H por HG8245H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8045H por EG8145V5|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8045H por HS8245W|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8245H por HG8045H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8245H por HG8245H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8245H por EG8145V5|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HG8245H por HS8245W|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - EG8145V5 por EG8145V5|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - EG8145V5 por HS8245W|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - EG8145V5 por HG8245H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - EG8145V5 por HG8045H|ONT|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (ONT+WIFI) - HS8245W por HS8245W|ONT|PROBLEMA FÍSICO CPE',
         'Cambio ZTE (ONT+WIFI) - F670L por F670L|ONT|PROBLEMA FÍSICO CPE',
         'Cambio ZTE (ONT+WIFI) - F680 por F680|ONT|PROBLEMA FÍSICO CPE',
         'Cambio ONT Tellion|ONT|PROBLEMA FÍSICO CPE',
         'Desconexión/Conexión de cable UTP entre ONT y Wifi|ONT|PROBLEMA FÍSICO CPE',
         'Reubicación de equipos|ONT|PROBLEMA FÍSICO CPE',
         'Daño de equipo causado por el cliente|ONT|PROBLEMA FÍSICO CPE',
         'Cambio de E900 por E900|ROUTER INALÁMBRICO|PROBLEMA FÍSICO CPE',
         'Cambio de E900 por Nokia Beacon 3|ROUTER INALÁMBRICO|PROBLEMA FÍSICO CPE',
         'Reubicación de equipos|ROUTER INALÁMBRICO|PROBLEMA FÍSICO CPE',
         'Cambio de Nokia Beacon 3 por Beacon 3|ROUTER INALÁMBRICO|PROBLEMA FÍSICO CPE',
         'Cambio de fuente de poder/PoE CLIENTE|AP CISCO/RUCKUS|PROBLEMA FÍSICO CPE',
         'Cambio de  equipo WIFI AP CISCO por daño|AP CISCO/RUCKUS|PROBLEMA FÍSICO CPE',
         'Cambio de modelo con mejor cobertura|AP CISCO/RUCKUS|PROBLEMA FÍSICO CPE',
         'Revisión de equipo en campo|AP CISCO/RUCKUS|PROBLEMA FÍSICO CPE',
         'Daño de equipo causado por el cliente|AP CISCO/RUCKUS|PROBLEMA FÍSICO CPE',
         'Cambio Huawei (Extender) - WA8011V |EXTENDER|PROBLEMA FÍSICO CPE',
         'Cambio ZTE (Extender) - H196N|EXTENDER|PROBLEMA FÍSICO CPE',
         'Cambio de orientación de antenas|EXTENDER|PROBLEMA FÍSICO CPE',
         'Revisión de equipo en campo|EXTENDER|PROBLEMA FÍSICO CPE',
         'Daño de equipo causado por el cliente|EXTENDER|PROBLEMA FÍSICO CPE',
         'Reubicación de equipo por cobertura|EXTENDER|PROBLEMA FÍSICO CPE',
         'Inhibición de equipo|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Cargar VAS PROFILE|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Actualización de firmware|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Cambio de canal por saturación|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Mala configuración|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Reseteo a modo de fabrica|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Cargar Plantilla XML|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Realizar pruebas de velocidad|ONT+WIFI|PROBLEMA LÓGICOS CPE',
         'Inhibición de equipo|ROUTER INALÁMBRICO|PROBLEMA LÓGICOS CPE',
         'Actualización de firmware|ROUTER INALÁMBRICO|PROBLEMA LÓGICOS CPE',
         'Mala configuración|ROUTER INALÁMBRICO|PROBLEMA LÓGICOS CPE',
         'Reseteo a modo de fabrica|ROUTER INALÁMBRICO|PROBLEMA LÓGICOS CPE',
         'Reconfiguración de equipo WIFI AP CISCO|AP CISCO/RUCKUS|PROBLEMA LÓGICOS CPE',
         'Inhibición de equipo|AP CISCO/RUCKUS|PROBLEMA LÓGICOS CPE',
         'Actualización de firmware|AP CISCO/RUCKUS|PROBLEMA LÓGICOS CPE',
         'Sincronización de equipo|EXTENDER|PROBLEMA LÓGICOS CPE',
         'Inhibición de equipo|EXTENDER|PROBLEMA LÓGICOS CPE',
         'Daño de módulo SFP|OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Daño de fuente de poder|OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Daño de tarjeta de servicio - PIU|OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Daño de tarjeta controladora|OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Desconexión de puerto gestión |OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Daño de OLT|OLT PROBLEMA FÍSICO |BACKBONE-NODO',
         'Sin Gestión|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Sin salida a Internet|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'OLT con intermitecias|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Actualización de firmware|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Problemas de enrutamiento|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Falla en configuración de OLT|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Alto procesamiento|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'MACs conectadas no mostradas|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Clientes mal conectados|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Saturación o intermitencias por caída o atenuación en uplink |OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Saturación o intermitencias por falta de capacidad |OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Balancear tráfico en anillo|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'BUG en el Sistema Operativo |OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Borrado de configuración|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Scope compartido - Duplicación de registro ARP-MAC|OLT PROBLEMA LÓGICO|BACKBONE-NODO',
         'Problema eléctrico nodo - UPS|NODO|BACKBONE-NODO',
         'Problema eléctrico nodo - Generador|NODO|BACKBONE-NODO',
         'Problema eléctrico nodo - Rack o ATS|NODO|BACKBONE-NODO',
         'Problema eléctrico nodo - Falta de pago|NODO|BACKBONE-NODO',
         'Desconexión del OLT de la energía eléctrica - terceros|NODO|BACKBONE-NODO',
         'Cambio Modulo Dúplex ODF nodo|NODO|BACKBONE-NODO',
         'Cambio Modulo Dúplex ODF Splitters|NODO|BACKBONE-NODO',
         'Cambio Splitter 1er nivel Nodo|NODO|BACKBONE-NODO',
         'Desconexión de patchcord de fibra en nodo|NODO|BACKBONE-NODO',
         'Eliminar atenuación en enlace de FO de backbone|NODO|BACKBONE-NODO',
         'Corte de FO troncal / Hilos de backbone SW|NODO|BACKBONE-NODO',
         'Cambio de interfaz|NODO|BACKBONE-NODO',
         'Cambiar fuente de transceiver en nodo|NODO|BACKBONE-NODO',
         'Cambiar transceiver en nodo|NODO|BACKBONE-NODO',
         'Cambiar puerto del switch en nodo|NODO|BACKBONE-NODO',
         'Daño/Atenuación Pigtail en ODF|NODO|BACKBONE-NODO',
         'Problema de switch agregador/PE|NODO|BACKBONE-NODO',
         'Saturación en salida internacional - Desbalanceo de tráfico |SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Saturación en salida internacional - Caída de troncal internacional |SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Saturación por caída de interurbano|SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Trabajo de mantenimiento no notificado|SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Problema tarjeta CGNAT|SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Problema de DNS|SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Problema de CDN (Facebook, Netflix, Google, etc)|SALIDA INTERNACIONAL/PROVEEDOR|BACKBONE-NODO',
         'Sincronización de bases de datos DHCP|CNR/LDAP|BACKBONE-NODO',
         'Configuración manual de IP|CNR/LDAP|BACKBONE-NODO',
         'Corrección de Tag|CNR/LDAP|BACKBONE-NODO',
         'Cambio de IP privada a IP pública|CNR/LDAP|BACKBONE-NODO',
         'Cambio de IP pública dinámica o fija|CNR/LDAP|BACKBONE-NODO',
         'Scope saturado|CNR/LDAP|BACKBONE-NODO',
         'Falla de comunicación LDAP-CNR|CNR/LDAP|BACKBONE-NODO',
         'OSS caído - Problemas de WS de telcos+|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS caído - Problemas con máquina virtual DC|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS caído - Problemas de base de datos de telcos+|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS caído - Subida a producción con error|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Middleware caído - Subida a producción con error|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Middleware caído - Problemas de máquina virtual DC|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Middleware caído - Problemas de WS de telcos+|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS intermitencia - Mala instalación de librerias|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS intermitencia - Problemas en el balanceador|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'OSS caido, intermitencias, errores|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Error en clave de acceso al OLT|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Middleware no ejecuta procesos individuales/masivos|OSS/MIDDLEWARE/LC|BACKBONE-NODO',
         'Corte de ruta 144|DISTRIBUCIÓN|FIBRA',
         'Corte de ruta 96|DISTRIBUCIÓN|FIBRA',
         'Corte de ruta 48/24|DISTRIBUCIÓN|FIBRA',
         'Corte de hilo(s) de ruta (caja sin potencia)|DISTRIBUCIÓN|FIBRA',
         'Atenuación de hilo(s)|DISTRIBUCIÓN|FIBRA',
         'Atenuación en manga|DISTRIBUCIÓN|FIBRA',
         'Atenuación por manipulación en caja dispersión|DISTRIBUCIÓN|FIBRA',
         'Mantenimiento programado de caja|DISTRIBUCIÓN|FIBRA',
         'Mantenimiento programado de pedestal|DISTRIBUCIÓN|FIBRA',
         'Mantenimiento preventivo equipos terminales en cliente|DISTRIBUCIÓN|FIBRA',
         'Mantenimiento preventivo equipos terminales en nodo|DISTRIBUCIÓN|FIBRA',
         'Migración por cambio de línea PON|DISTRIBUCIÓN|FIBRA',
         'Corte de pigtail dentro de la caja Dispersión|DISTRIBUCIÓN|FIBRA',
         'Corte Externo Hilo MPLS/LDD|DISTRIBUCIÓN|FIBRA',
         'Cambio de dúplex en caja|DISTRIBUCIÓN|FIBRA',
         'Cambio de dúplex en pedestal|DISTRIBUCIÓN|FIBRA',
         'Daño de splitter L1 fuera de nodo (Clientes puerto PON caídos)|DISTRIBUCIÓN|FIBRA',
         'Atenuación/corte por manipulación en caja por inventario|DISTRIBUCIÓN|FIBRA',
         'Cliente desconectado de splitter para activación nuevo cliente|DISTRIBUCIÓN|FIBRA',
         'Splitter 1er nivel dañado|DISTRIBUCIÓN|FIBRA',
         'Splitter 2do nivel dañado|DISTRIBUCIÓN|FIBRA',
         'Cambio de poste|DISTRIBUCIÓN|FIBRA',
         'Caja en transformador|DISTRIBUCIÓN|FIBRA',
         'Corte Externo FO Acceso - caja por plagas|ACCESO|FIBRA',
         'Corte Externo FO Acceso - causa de terceros|ACCESO|FIBRA',
         'Corte externo FO acceso - pedestal por plagas|ACCESO|FIBRA',
         'Corte externo FO acceso - pozo|ACCESO|FIBRA',
         'Corte Externo FO Acceso - corte por roedores|ACCESO|FIBRA',
         'Corte Externo FO Acceso - mini manga de UM|ACCESO|FIBRA',
         'Corte Externo FO Acceso - tendido UM|ACCESO|FIBRA',
         'Corte externo FO acceso - ducteria|ACCESO|FIBRA',
         'Reparar fusión de hilo de FO en red de acceso|ACCESO|FIBRA',
         'Atenuación de fibra del lado del cliente|ACCESO|FIBRA',
         'Atenuación de fibra del lado de la caja|ACCESO|FIBRA',
         'Atenuación de fibra del lado del pedestal|ACCESO|FIBRA',
         'Atenuación de fibra del lado de la manga|ACCESO|FIBRA',
         'Roseta - Cambio de dúplex/simplex|ACCESO|FIBRA',
         'Roseta - Daño efectuado por terceros|ACCESO|FIBRA',
         'Roseta - Cambio de pigtail FO|ACCESO|FIBRA',
         'Roseta - Reparar fusión de hilo de FO|ACCESO|FIBRA',
         'Tellion - Daño de Pigtail en ONT|ACCESO|FIBRA',
         'Tellion - Reparar fusión de hilo de FO en ONT|ACCESO|FIBRA',
         'Cambio de patchcord de fibra|ACCESO|FIBRA',
         'Corte Interno Facturado|ACCESO|FIBRA',
         'Corte interno FO acceso|ACCESO|FIBRA',
         'Equipo repetidor/router del cliente|EQUIPOS DEL CLIENTE|CLIENTE',
         'Problema en computador/servidor del cliente|EQUIPOS DEL CLIENTE|CLIENTE',
         'Problema en equipo celular/Tablet del cliente - IOS|EQUIPOS DEL CLIENTE|CLIENTE',
         'Problema en equipo celular/Tablet del cliente - ANDROID|EQUIPOS DEL CLIENTE|CLIENTE',
         'Problema en Televisión / TVBOX|EQUIPOS DEL CLIENTE|CLIENTE',
         'Problema en Consola juegos del cliente (PS4/XBOX/COMPUTADOR)|EQUIPOS DEL CLIENTE|CLIENTE',
         'Cancelación por cliente no contactado|CANCELACIÓN DE VISITA|CLIENTE',
         'Cliente no desea la VT|CANCELACIÓN DE VISITA|CLIENTE',
         'Cliente suspendido por falta de pago|CANCELACIÓN DE VISITA|CLIENTE',
         'Cliente operativo sin intervención técnica|CANCELACIÓN DE VISITA|CLIENTE',
         'Cancelación por cliente no diferido|CANCELACIÓN DE VISITA|CLIENTE',
         'Cancelación servicio operativo confirmado con cliente|CANCELACIÓN DE VISITA|CLIENTE',
         'Posible Cyber/ISP|USO DEL SERVICIO|CLIENTE',
         'Monitoreo de tráfico del servicio|USO DEL SERVICIO|CLIENTE',
         'Problemas de acceso a páginas o servicios|SEGURIDAD DE LA INFORMACIÓN|CLIENTE',
         'IP/Subred enlistada|SEGURIDAD DE LA INFORMACIÓN|CLIENTE',
         'Cliente sin servicio por incidente de seguridad |SEGURIDAD DE LA INFORMACIÓN|CLIENTE',
         'Cliente sin servicio por incidente de seguridad - Ecucert|SEGURIDAD DE LA INFORMACIÓN|CLIENTE',
         'Caso duplicado/mal aperturado|CIERRE CASOS|CASOS',
         'Problema masivo - cierre de caso individual|CIERRE CASOS|CASOS',
         'Error en asignación de Zona|CIERRE CASOS|CASOS',
         'Cambio de fuente de poder|MODEM|ADSL',
         'Cambio de equipo por daño|MODEM|ADSL',
         'Daño de equipo causado por el cliente|MODEM|ADSL',
         'Revisión de equipo en campo|MODEM|ADSL',
         'Reubicación de equipos|ROUTER INALÁMBRICO|ADSL',
         'Cambio de canal por saturación|ROUTER INALÁMBRICO|ADSL',
         'Mala configuración|ROUTER INALÁMBRICO|ADSL',
         'Corte de ruta 144|DISTRIBUCIÓN|ADSL',
         'Corte de ruta 96|DISTRIBUCIÓN|ADSL',
         'Corte de ruta 48/24|DISTRIBUCIÓN|ADSL',
         'Problema eléctrico DSLAM|DISTRIBUCIÓN|ADSL',
         'Problema sistema operativo DSLAM gestión|DISTRIBUCIÓN|ADSL',
         'CORTE EXTERNO COBRE|ACCESO|ADSL',
         'CORTE INTERNO COBRE|ACCESO|ADSL',
         'Inhibición de equipo|DAÑO DE EQUIPO|RADIO',
         'Daño de cable UTP CLIENTE - PoE-CPE CLIENTE|DAÑO DE EQUIPO|RADIO',
         'Daño de cable UTP CLIENTE PoE-Radio|DAÑO DE EQUIPO|RADIO',
         'Daño de equipo ANTENA CLIENTE|DAÑO DE EQUIPO|RADIO',
         'Daño de equipo PoE CLIENTE|DAÑO DE EQUIPO|RADIO',
         'Daño en el pigtail de RF CLIENTE|DAÑO DE EQUIPO|RADIO',
         'Configuración Equipo de Radio cliente|DAÑO DE EQUIPO|RADIO',
         'Interferencia de frecuencia RADIO CLIENTE|ACCESO|RADIO',
         'Interferencia/Cortes por condiciones climáticas|ACCESO|RADIO',
         'Obstrucción línea de vista|ACCESO|RADIO',
         'Saturación de capacidad RADIO CLIENTE|ACCESO|RADIO',
         'Configuración Equipo de Radio Nodo|NODO|RADIO',
         'Daño de cable UTP NODO PoE-Radio|NODO|RADIO',
         'Daño de cable UTP NODO PoE-Switch|NODO|RADIO',
         'Daño de equipo ANTENA NODO|NODO|RADIO',
         'Daño de equipo PoE NODO|NODO|RADIO',
         'Daño en el pigtail de RF NODO|NODO|RADIO',
         'Daño en la interface LAN NODO|NODO|RADIO',
         'Desconexión de la fuente eléctrica PoE Nodo|NODO|RADIO',
         'Interferencia de frecuencia RADIO NODO|NODO|RADIO',
         'Saturación de capacidad RADIO NODO|NODO|RADIO',
         'Problema eléctrico nodo|NODO|RADIO'
       ); 
   total := niveltres.count; 
   dbms_output.put_line('Total '|| total || ' Hipotesis'); 
   FOR i in 1 .. total LOOP
      countn1:=0;
      countn2:=0;
      countn3:=0;
      hipotesisn3 := SUBSTR(niveltres(i),0,(INSTR(niveltres(i),'|')-1)); 
      hipotesisn2 := SUBSTR(niveltres(i), (INSTR(niveltres(i),'|')+1), LENGTH(niveltres(i)) );
      hipotesisn1 := SUBSTR(hipotesisn2,(INSTR(hipotesisn2,'|')+1),LENGTH(hipotesisn2)); 
      hipotesisn2 := SUBSTR(hipotesisn2,0,(INSTR(hipotesisn2,'|')-1));

      --INSERT NIVEL 1
      SELECT COUNT(ID_HIPOTESIS) INTO countn1 FROM DB_SOPORTE.ADMI_HIPOTESIS 
      WHERE NOMBRE_HIPOTESIS = hipotesisn1 AND HIPOTESIS_ID=0 AND ESTADO = 'Activo' AND EMPRESA_COD = '18';

      IF countn1 <= 0 THEN
            INSERT INTO DB_SOPORTE.ADMI_HIPOTESIS(ID_HIPOTESIS, NOMBRE_HIPOTESIS, DESCRIPCION_HIPOTESIS, ESTADO, USR_CREACION, FE_CREACION, USR_ULT_MOD, FE_ULT_MOD, EMPRESA_COD, HIPOTESIS_ID) 
            VALUES(DB_SOPORTE.SEQ_ADMI_HIPOTESIS.NEXTVAL,hipotesisn1,hipotesisn1,'Activo','amontero',SYSDATE,'amontero',SYSDATE,'18',0);
            dbms_output.put_line('Insert Hipotesis (nivel 1): ' || hipotesisn1);  
            transInsert:= transInsert + 1;
      END IF;

      --INSERT NIVEL 2
      SELECT COUNT(ID_HIPOTESIS) INTO countn2 FROM DB_SOPORTE.ADMI_HIPOTESIS 
      WHERE NOMBRE_HIPOTESIS = hipotesisn2 AND HIPOTESIS_ID=
      (SELECT ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS WHERE NOMBRE_HIPOTESIS= hipotesisn1 AND ESTADO='Activo' AND EMPRESA_COD = '18')
       AND ESTADO = 'Activo' AND EMPRESA_COD = '18';

      IF countn2 <= 0 THEN
         INSERT INTO DB_SOPORTE.ADMI_HIPOTESIS(ID_HIPOTESIS, NOMBRE_HIPOTESIS, 
         DESCRIPCION_HIPOTESIS, ESTADO, USR_CREACION, FE_CREACION,USR_ULT_MOD, FE_ULT_MOD, EMPRESA_COD, HIPOTESIS_ID) 
         VALUES(DB_SOPORTE.SEQ_ADMI_HIPOTESIS.NEXTVAL,hipotesisn2,hipotesisn2,
               'Activo','amontero',SYSDATE,'amontero',SYSDATE,'18',
               (SELECT ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS 
                WHERE NOMBRE_HIPOTESIS= hipotesisn1 AND ESTADO='Activo' AND EMPRESA_COD = '18'));
            dbms_output.put_line('Insert Hipotesis (nivel 2): ' || hipotesisn2);
            transInsert:= transInsert + 1;
      END IF;

      --INSERT NIVEL 3
      SELECT COUNT(ID_HIPOTESIS) INTO countn3 FROM DB_SOPORTE.ADMI_HIPOTESIS 
      WHERE NOMBRE_HIPOTESIS = hipotesisn3 AND ESTADO = 'Activo' AND EMPRESA_COD = '18' AND HIPOTESIS_ID IS NULL;

      IF countn3 <= 0 THEN

        SELECT COUNT(ID_HIPOTESIS) INTO countn3_1 FROM DB_SOPORTE.ADMI_HIPOTESIS 
        WHERE NOMBRE_HIPOTESIS = hipotesisn3 AND ESTADO = 'Activo' AND EMPRESA_COD = '18' AND
        HIPOTESIS_ID =  
                (SELECT h2.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h2 
                 WHERE h2.NOMBRE_HIPOTESIS= hipotesisn2 AND h2.HIPOTESIS_ID = 
                  (SELECT h1.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h1 WHERE h1.NOMBRE_HIPOTESIS = hipotesisn1
                   AND h1.ESTADO = 'Activo' AND h1.EMPRESA_COD = '18') 
                 AND h2.ESTADO='Activo' AND h2.EMPRESA_COD = '18');
    
        IF countn3_1 <= 0 THEN

          INSERT INTO DB_SOPORTE.ADMI_HIPOTESIS(ID_HIPOTESIS, NOMBRE_HIPOTESIS, 
          DESCRIPCION_HIPOTESIS, ESTADO, USR_CREACION, FE_CREACION,USR_ULT_MOD, FE_ULT_MOD, EMPRESA_COD, HIPOTESIS_ID) 
          VALUES(DB_SOPORTE.SEQ_ADMI_HIPOTESIS.NEXTVAL,hipotesisn3,hipotesisn3,
                'Activo','amontero',SYSDATE,'amontero',SYSDATE,'18',
                (SELECT h2.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h2 
                 WHERE h2.NOMBRE_HIPOTESIS= hipotesisn2 AND h2.HIPOTESIS_ID = 
                  (SELECT h1.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h1 WHERE h1.NOMBRE_HIPOTESIS = hipotesisn1
                   AND h1.ESTADO = 'Activo' AND h1.EMPRESA_COD = '18') 
                 AND h2.ESTADO='Activo' AND h2.EMPRESA_COD = '18')
               );
            dbms_output.put_line('Insert Hipotesis (nivel 3): ' || hipotesisn3);
            transInsert:= transInsert + 1;

        END IF;

      ELSE
         UPDATE DB_SOPORTE.ADMI_HIPOTESIS SET HIPOTESIS_ID =  
                (SELECT h2.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h2 
                 WHERE h2.NOMBRE_HIPOTESIS= hipotesisn2 AND h2.HIPOTESIS_ID = 
                  (SELECT h1.ID_HIPOTESIS FROM DB_SOPORTE.ADMI_HIPOTESIS h1 WHERE h1.NOMBRE_HIPOTESIS = hipotesisn1
                   AND h1.ESTADO = 'Activo' AND h1.EMPRESA_COD = '18') 
                 AND h2.ESTADO='Activo' AND h2.EMPRESA_COD = '18')
         WHERE
             NOMBRE_HIPOTESIS = hipotesisn3 AND ESTADO = 'Activo' AND EMPRESA_COD = '18';
        dbms_output.put_line('Update Hipotesis (nivel 3): ' || hipotesisn3);
        transUpdate:= transUpdate + 1;
             
      END IF;

      --dbms_output.put_line('Hipotesis: ' || hipotesisn3 || ' ** '|| hipotesisn2 || ' ** '|| hipotesisn1);  
   END LOOP;
   dbms_output.put_line('Total transacciones Insert => '||transInsert || ' Update =>' ||transUpdate );
   COMMIT;
END;