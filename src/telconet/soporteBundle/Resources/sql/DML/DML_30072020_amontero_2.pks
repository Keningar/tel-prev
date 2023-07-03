--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO ELECTRICO
DECLARE 
   type niveltresarray IS VARRAY(500) OF VARCHAR2(1000); 
   tareanivel1 VARCHAR2(200);
   tareanivel2 VARCHAR2(200);
   tareanivel3 VARCHAR2(200);
   tareanivel4 VARCHAR2(200);
   tareanivel5 VARCHAR2(200);
   countn1 NUMBER;
   countn2 NUMBER;

   tareaCaracteristicaId NUMBER;

   idtareanueva NUMBER;
   idcaracrequirematerial NUMBER := 1311;
   transInsert NUMBER := 0;
   totalExiste NUMBER := 0;
   totalNoExisteAdmiTarea NUMBER := 0;
   niveltres niveltresarray; 

   total integer;

   iddepartamento          VARCHAR2(100) := '129';
   descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
   descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';

   imagen                  VARCHAR2(50);

   Le_Exception                EXCEPTION;
   Lv_MensajeError             VARCHAR2(4000);

BEGIN 

   niveltres := niveltresarray(
        'CLIENTE|NETLIFE|Daño de camaras de CLIENTE|6415|S',
        'CLIENTE|NETLIFE|Daño de Citofono|6414|S',
        'CLIENTE|NETLIFE|Daño de Internet por cobre|6417|S',
        'CLIENTE|NETLIFE|Daño de Lamparas|6418|N',
        'CLIENTE|NETLIFE|DAÑO DE LINEA TELEFÓNICA|6412|S',
        'CLIENTE|NETLIFE|Daño de Tomacorrientes|6419|N',
        'CLIENTE|NETLIFE|Daño de TV Cable|6413|S',
        'CLIENTE|NETLIFE|DAÑOS EN INSTALACIONES (CORTOCIRCUITOS)|6420|S',
        'CLIENTE|NETLIFE|Daños en la red interna del cliente|6416|N',
        'CLIENTE|PROYECTOS LAN|Auditoria de puntos de datos|6343|N',
        'CLIENTE|PROYECTOS LAN|Certificación de puntos de datos|6344|N',
        'CLIENTE|PROYECTOS LAN|Estandarización y peinado de rack|6345|S',
        'CLIENTE|PROYECTOS LAN|Identificación y testeo de puntos de voz y datos|6346|N',
        'CLIENTE|PROYECTOS LAN|Informes proyectos cableado estructurado|6340|N',
        'CLIENTE|PROYECTOS LAN|Inspección cableado estructurado|6336|N',
        'CLIENTE|PROYECTOS LAN|Instalacion de 1 punto de datos|6456|S',
        'CLIENTE|PROYECTOS LAN|Instalacion de camara|6457|S',
        'CLIENTE|PROYECTOS LAN|Instalación de cableado estructurado grande (11 en adelante)|6339|S',
        'CLIENTE|PROYECTOS LAN|Instalación de cableado estructurado mediano (2 a 10)|6338|S',
        'CLIENTE|PROYECTOS LAN|Instalación de central telefónicas IP|6347|S',
        'CLIENTE|PROYECTOS LAN|Instalación de rack|6342|S',
        'CLIENTE|PROYECTOS LAN|Instalación de rack con accesorios|6341|S',
        'CLIENTE|PROYECTOS LAN|Liquidacion de proyectos Lan|6458|N',
        'CLIENTE|PROYECTOS LAN|Migración centros de datos|6348|S',
        'CLIENTE|PROYECTOS LAN|Reunión proyectos cableado estructurado|6337|N',
        'CLIENTE|PROYECTOS LAN|Soporte LAN punto de datos|6351|S',
        'CLIENTE|PROYECTOS LAN|Soporte LAN puntos de cámara CCTV IP|6350|S',
        'CLIENTE|PROYECTOS LAN|Soporte LAN puntos de voz|6349|S',
        'CLIENTE|PROYECTOS LAN|Informes de Inspecciones|6310|N',
        'CLIENTE|PROYECTOS ELECTRICOS|Informe de Proyectos|6313|N',
        'CLIENTE|PROYECTOS ELECTRICOS|Informe de Trabajo|6312|N',
        'CLIENTE|PROYECTOS ELECTRICOS|Informes de Inspecciones|6310|N',
        'CLIENTE|PROYECTOS ELECTRICOS|Instalación de pararayos|4726|S',
        'CLIENTE|PROYECTOS ELECTRICOS|Implementación de Centros de Datos|4724|S',
        'CLIENTE|PROYECTOS ELECTRICOS|Instalación de protecciones eléctricas|4728|S',
        'CLIENTE|PROYECTOS ELECTRICOS|Instalación de puestas a tierra|4727|S',
        'CLIENTE|PROYECTOS ELECTRICOS|Inspeccion Proyecto|6513|N',
        'CLIENTE|PROYECTOS ELECTRICOS|Instalacion proyecto|6514|S',
        'CLIENTE|PROYECTOS ELECTRICOS|Planos y diagramas|4733|N',
        'NODO|ARREGLO|Estandarizar accesorios de Rack|2488|S',
        'NODO|ARREGLO|Inventario de Nodo|5374|N',
        'NODO|ARREGLO|Organización total rack por rack y limpieza|2524|S',
        'NODO|ARREGLO|Reubicar Racks|2487|S',
        'NODO|INSTALACION|ATS electronico|5262|S',
        'NODO|INSTALACION|Instalacion de Rack|6464|S',
        'NODO|INSTALACION|ATS convencional|5261|S',
        'NODO|INSTALACION|Baterias|5254|S',
        'NODO|INSTALACION|Cargador|5256|S',
        'NODO|INSTALACION|Concentrador|2518|S',
        'NODO|INSTALACION|Instalacion de Alarmas de intrusion|6459|S',
        'NODO|INSTALACION|Instalacion de Backup Electrico|6322|S',
        'NODO|INSTALACION|Instalacion de Telefono IP|6461|S',
        'NODO|INSTALACION|Instalacion de puntos wifi|6460|S',
        'NODO|INSTALACION|Instalación Tarjeta de Monitoreo|5257|S',
        'NODO|INSTALACION|Instalación de Alarmas NODO|6407|S',
        'NODO|INSTALACION|Instalación de Cámaras NODO|6406|S',
        'NODO|INSTALACION|Luminaria|5259|S',
        'NODO|INSTALACION|Multitomas|5260|S',
        'NODO|INSTALACION|Otras instalaciones|5264|S',
        'NODO|INSTALACION|Punto Electrico|5253|S',
        'NODO|INSTALACION|Router concentrador/agregador|2519|S',
        'NODO|INSTALACION|Sistemas de Iluminación|2514|S',
        'NODO|INSTALACION|Switch CISCO|2517|N',
        'NODO|INSTALACION|Tablero electrico|5263|S',
        'NODO|INSTALACION|UPS|5255|S',
        'NODO|CENTROS COMERCIALES|Entrega del nodo a Fiscalizacion|6382|N',
        'NODO|CENTROS COMERCIALES|Implementar nodo|6379|S',
        'NODO|CENTROS COMERCIALES|Inspeccion de Espacio Fisico|6381|N',
        'NODO|CENTROS COMERCIALES|Pedido de Materiales|6380|N',
        'NODO|CENTROS COMERCIALES|Instalacion de switch|6463|N',
        'NODO|CENTROS COMERCIALES|Realizar informe de nodo terminado|6378|N',
        'NODO|ESTANDAR|Buscar Nodo|2458|N',
        'NODO|ESTANDAR|Instalación de pararayos|4726|S',
        'NODO|ESTANDAR|Instalación de protecciones eléctricas|4728|S',
        'NODO|ESTANDAR|Instalación de puestas a tierra|4727|S',
        'NODO|ESTANDAR|Ampliación de capacidad de NODO|4736|S',
        'NODO|ESTANDAR|Instalación de Supresores de transientes|4729|S',
        'NODO|ESTANDAR|Estudios de carga|4734|N',
        'NODO|ESTANDAR|Implementación infraestructura, racks y accesorios|6354|S',
        'NODO|ESTANDAR|Instalación de cableado eléctrico, paneles de breakers|6355|S',
        'NODO|ESTANDAR|Instalación de respaldos eléctricos, bancos de baterías y tarjetas de monitoreo.|6356|S',
        'NODO|ESTANDAR|Levantamiento y digitalización de diagrama de planta espacio y ubicación de racks, UPS y accesorios|2460|N',
        'NODO|ESTANDAR|Instalacion de switch|6463|N',
        'NODO|ESTANDAR|Validación del nodo entre áreas|6357|N',
        'NODO|OTN/AGREGADOR|Buscar nodo y espacio para Generador|2432|N',
        'NODO|OTN/AGREGADOR|Instalación de protecciones eléctricas|4728|S',
        'NODO|OTN/AGREGADOR|Instalación de pararayos|4726|S',
        'NODO|OTN/AGREGADOR|Instalación de puestas a tierra|4727|S',
        'NODO|OTN/AGREGADOR|Instalación de Supresores de transientes|4729|S',
        'NODO|OTN/AGREGADOR|Ampliación de capacidad de NODO|4736|S',
        'NODO|OTN/AGREGADOR|Estudios de carga|4734|N',
        'NODO|OTN/AGREGADOR|Encendido inicial de UPS´s por contratista|4779|N',
        'NODO|OTN/AGREGADOR|Encendido inicial y pruebas de Generador con contratista|2451|N',
        'NODO|OTN/AGREGADOR|Implementación infraestructura, racks y accesorios|6354|S',
        'NODO|OTN/AGREGADOR|Instalación de AACC por contratista|6358|S',
        'NODO|OTN/AGREGADOR|Instalación de Tablero de Transferencia Automática|2442|S',
        'NODO|OTN/AGREGADOR|Instalación de cableado eléctrico, paneles de breakers|6355|S',
        'NODO|OTN/AGREGADOR|Instalación de respaldos eléctricos, bancos de baterías y tarjetas de monitoreo.|6356|S',
        'NODO|OTN/AGREGADOR|Levantamiento y digitalización de diagrama de planta espacio y ubicación de racks,UPS y accesorios|2434|N',
        'NODO|OTN/AGREGADOR|Retirar y transportar materiales|2438|N',
        'NODO|OTN/AGREGADOR|Supervisión de Obra Civil por contratista|6359|N',
        'NODO|OTN/AGREGADOR|Validación del nodo entre áreas|6357|N',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de ATS|5242|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Back Up completo|6427|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Back Up interactivo a online|6334|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Base Socket|5243|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Bateria de Generador|6319|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Baterias|5235|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Breaker|5239|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Cargador|5237|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Luminaria|5240|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Mantenedor de Carga|6318|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Multitomas|5241|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Tablero Electrico|5244|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de Tarjeta de Monitoreo|5238|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Cambio de UPS|6362|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Otros cambios|5245|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Revision de Generador Estacionario|6317|S',
        'NODO|MANTENIMIENTO CORRECTIVO|Revision de Tablero de Transferencia Automatica|6316|N',
        'NODO|MANTENIMIENTO CORRECTIVO|Revisión Voltaje Neutro Tierra|6306|S',
        'NODO|MANTENIMIENTO|Contingencia Eléctrica Nodo Agregador|2581|N',
        'NODO|MANTENIMIENTO|Contingencia Eléctrica Nodo Estándar|2579|N',
        'NODO|MANTENIMIENTO|Contingencia Eléctrica Nodo SDH|2583|N',
        'NODO|MANTENIMIENTO|Contingencia eléctrica nodo alta densidad|6209|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo Agregador|2580|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo Centros Comerciales y Edificios|6329|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo Estándar|2578|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo Metrovía|6330|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo Radio|6328|N',
        'NODO|MANTENIMIENTO|Preventivo Nodo SDH|2582|N',
        'NODO|MANTENIMIENTO|Preventivo nodo alta densidad|6208|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Coordinar Migración de nodo|4793|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Implementación nodo espejo|6393|S',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Levantamiento de información|6369|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Migración de equipos a nuevo nodo|6394|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Migración de nodo|6395|S',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Retiro de materiales del nodo deshabilitado|6371|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Reunión de migraciones|6370|N',
        'NODO|MIGRACION DE NODOS Y/O CLIENTES|Trabajos dentro de la migración|6372|N',
        'NODO|SOPORTE|Battery Status OFF|4783|S',
        'NODO|SOPORTE|Input Load Too High|6315|N',
        'NODO|SOPORTE|Nodo en Generador|6320|N',
        'NODO|SOPORTE|ON BATTERY|2588|N',
        'NODO|SOPORTE|REPLACE BATTERY|6314|S',
        'NODO|SOPORTE|Revisar mala calidad de energía de las Empresas Eléctricas|4785|N',
        'NODO|SOPORTE|Revisar temperatura alta|2592|N',
        'NODO|SOPORTE|Revision de acometida empresa electrica|3229|N',
        'NODO|SOPORTE|Revision de fuente de Router|6321|N',
        'NODO|SOPORTE|Revision de voltaje en Fuentes de -48VDC|2593|N',
        'NODO|SOPORTE|Revisión de UPS|2387|N',
        'NODO|SOPORTE|Soporte de Cámaras en nodo|6403|S',
        'NODO|SOPORTE|Soporte de teléfono en nodo|6405|N',
        'NODO|SOPORTE|Soportes de punto WIFI en NODO|6404|N',
        'NODO|SOPORTE|unavailable by ICMP|2591|S',
        'NODO|INFORMES|Informe de Migraciones|6311|N',
        'NODO|INFORMES|Informe de Trabajo|6312|N',
        'NODO|INFORMES|Informes de Mantenimiento|6309|N',
        'NODO|TELEPUERTO / GOSSEAL|Actualizar diagramas de planta, espacio y ubicación de racks|2536|N',
        'NODO|TELEPUERTO / GOSSEAL|Arreglar transceivers y patch cords de FO en racks|2544|S',
        'NODO|TELEPUERTO / GOSSEAL|Cambiar equipos optimus (E1)|2534|N',
        'NODO|TELEPUERTO / GOSSEAL|Cambiar transceiver o fuente de poder de transceiver|2535|N',
        'NODO|TELEPUERTO / GOSSEAL|Colocar atenuadores en interfaces|6392|N',
        'NODO|TELEPUERTO / GOSSEAL|Fabricar cables E1|6389|S',
        'NODO|TELEPUERTO / GOSSEAL|Ingreso de datos biometrico TN|6436|N',
        'NODO|TELEPUERTO / GOSSEAL|Instalar Interfaces|6391|N',
        'NODO|TELEPUERTO / GOSSEAL|Instalar patchcords de fibra entre equipos Backbone/clientes VIP|2541|N',
        'NODO|TELEPUERTO / GOSSEAL|Instalar rack de piso con circuitos eléctricos y accesorios|2538|S',
        'NODO|TELEPUERTO / GOSSEAL|Instalar tarjetas|2543|N',
        'NODO|TELEPUERTO / GOSSEAL|Mantenimiento de tableros eléctricos tele-puerto|6438|N',
        'NODO|TELEPUERTO / GOSSEAL|Mantenimiento piso falso|2547|N',
        'NODO|TELEPUERTO / GOSSEAL|Medir potencia en equipos E1|2533|N',
        'NODO|TELEPUERTO / GOSSEAL|Medir potencia equipos SDH/DWDM|2528|N',
        'NODO|TELEPUERTO / GOSSEAL|Medir y hacer análisis de aumento de equipos en rack|2537|N',
        'NODO|TELEPUERTO / GOSSEAL|Monitoreo de sistemas de seguridad electrónica|6437|N',
        'NODO|TELEPUERTO / GOSSEAL|Montar y desmontar equipos packetline de pruebas|2542|N',
        'NODO|TELEPUERTO / GOSSEAL|Prueba de Loop en Equipos /Tarjetas|6390|N',
        'NODO|TELEPUERTO / GOSSEAL|Realizar acometida eléctrica principal y backup para crecimiento de equipos|2539|S',
        'NODO|TELEPUERTO / GOSSEAL|Reinicio de Servidores|6388|N',
        'NODO|TELEPUERTO / GOSSEAL|Reporte - Check list|6352|N',
        'NODO|TELEPUERTO / GOSSEAL|Soporte Clientes Corporativos|2546|S',
        'NODO|TELEPUERTO / GOSSEAL|Soporte Clientes VIP|2545|S',
        'NODO|TELEPUERTO / GOSSEAL|Soporte Fyber Home|2532|N',
        'NODO|SUPERVISION A CONTRATISTAS|AIRE ACONDICIONADO|2573|N',
        'NODO|SUPERVISION A CONTRATISTAS|Albanileria|5250|N',
        'NODO|SUPERVISION A CONTRATISTAS|Generador|2554|N',
        'NODO|SUPERVISION A CONTRATISTAS|Metalicos|5251|N',
        'NODO|SUPERVISION A CONTRATISTAS|Otros trabajos|5252|N',
        'NODO|SUPERVISION A CONTRATISTAS|Transformadores|5248|N',
        'NODO|SUPERVISION A CONTRATISTAS|UPS|5255|N',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Correccion de cableado de datos|6384|S',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Correccion de cableado electrico|6387|S',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Instalacion de Bandejas|6383|S',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Instalacion de Multitomas y soportes|6386|S',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Instalacion de organizadores|6385|S',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Limpieza del Nodo|6432|N',
        'NODO|REQUERIMIENTOS DE FISCALIZACION|Revision de Luminarias|6431|S',
        'NODO|CAMARAS|REVISION DE EVENTOS|6424|N',
        'LOGISTICA|LOGISTICA|Retiro de materiales en bodega|5103|N',
        'LOGISTICA|LOGISTICA|Tanqueo de combustible de generador estacionario|6374|N',
        'LOGISTICA|LOGISTICA|Tanqueo de combustible de generador portátil|6373|N',
        'LOGISTICA|LOGISTICA|Transportar material localmente|6515|N',
        'LOGISTICA|LOGISTICA|Transporte a nodos/clientes otra sucursal|6516|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Accesos Biométricos|2570|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Aire Acondicionado|2573|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Cambio de UPS|6362|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Cuarto de equipos|2575|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Estaciones de trabajo (voz, datos y eléctrico)|6171|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Estética de puestos de trabajo por área|2577|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Generador|2554|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Iluminación por área|2569|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Instalación de UPS|2562|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Instalación de cámaras|6365|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Instalación de cámaras oficinas|6409|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Instalación de reloj biométrico|6366|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Instalar rack, accesorios, circuitos eléctricos, escalerila y etiquetado|2478|S',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Bodega Aceitunos|6223|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Bodega Arupos|6222|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Concorde|6219|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Gosseal|6377|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Oficina Armenia|6224|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Mantenimiento Preventivo Oficina Sur Dos|6225|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Movilización Personal Técnico|5805|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Punto de Iluminación|2561|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Racks y equipos|2572|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Revisión de UPS|2387|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Sala de Reuniones|2574|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Sistema de alimentación 220VAC iluminación externa|2576|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Soporte de Cámaras de oficina|6408|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Soportes de control de accesos biométricos|6410|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Soportes oficina|6368|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Tableros eléctricos|2568|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|Transformador de MT|4780|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|OBJETOS OLVIDADOS EN CASILLERO|6421|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|REVISION DE EVENTO DE DAÑOS EN VEHICULOS TN|6422|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|REVISION DE EVENTOS|6424|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|REVISION DE EVENTOS RRHH|6423|N',
        'EDIFICIOS / OFICINAS TN|MANTENIMIENTO|UPS|5255|N',
        'EDIFICIOS / OFICINAS TN|CAMARAS|OBJETOS OLVIDADOS EN CASILLERO|6421|N',
        'EDIFICIOS / OFICINAS TN|CAMARAS|REVISION DE EVENTO DE DAÑOS EN VEHICULOS TN|6422|N',
        'EDIFICIOS / OFICINAS TN|CAMARAS|REVISION DE EVENTOS|6424|N',
        'EDIFICIOS / OFICINAS TN|CAMARAS|REVISION DE EVENTOS RRHH|6423|N'
       ); 
   total := niveltres.count;

   FOR i in 1 .. total LOOP
      countn1:=0;
      countn2:=0;
      idtareanueva:=0;
      tareanivel1 := SUBSTR(niveltres(i),0,(INSTR(niveltres(i),'|')-1)); 
      tareanivel2 := SUBSTR(niveltres(i), (INSTR(niveltres(i),'|')+1), LENGTH(niveltres(i)) );
      tareanivel3 := SUBSTR(tareanivel2,(INSTR(tareanivel2,'|')+1),LENGTH(tareanivel2)); 
      tareanivel2 := SUBSTR(tareanivel2,0,(INSTR(tareanivel2,'|')-1));
      tareanivel4 := SUBSTR(tareanivel3,INSTR(tareanivel3,'|')+1,LENGTH(tareanivel3)); 
      tareanivel3 := SUBSTR(tareanivel3,0,(INSTR(tareanivel3,'|')-1)); 
      tareanivel5 := SUBSTR(tareanivel4,INSTR(tareanivel4,'|')+1,LENGTH(tareanivel4));
      tareanivel4 := SUBSTR(tareanivel4,0,(INSTR(tareanivel4,'|')-1));

      IF (UPPER(tareanivel1) = 'CLIENTE') THEN
        imagen := 'cliente.png';
      ELSIF (UPPER(tareanivel1) = 'NODO') THEN
        imagen := 'nodo.png';
      ELSIF (UPPER(tareanivel1) = 'LOGISTICA') THEN
        imagen := 'logistica.png';
      ELSIF (UPPER(tareanivel1) = 'EDIFICIOS / OFICINAS TN') THEN
        imagen := 'edificio.png';
      END IF;

      --CONSULTA TAREA SI EXISTE
      BEGIN
          IF tareanivel4 IS NOT NULL THEN
            SELECT ID_TAREA INTO idtareanueva  FROM DB_SOPORTE.ADMI_TAREA WHERE ID_TAREA = tareanivel4;
          END IF;
      EXCEPTION
          WHEN NO_DATA_FOUND THEN
            Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                   'MIGRACION ARBOL TAREAS - ELECTRICO',
                                                   '[ERROR AL CONSULTAR TAREA id:'||tareanivel4||'] => '||Lv_MensajeError,
                                                   NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                                   SYSDATE,
                                                   NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                                   '127.0.0.1')
                                                );
      END;

      --SE ACTUALIZA QUE SE VISUALIZA EN MOVIL
      IF idtareanueva IS NOT NULL THEN
              UPDATE DB_SOPORTE.ADMI_TAREA SET VISUALIZAR_MOVIL='S' WHERE ID_TAREA = idtareanueva;
      END IF;

      --SI REQUIERE MATERIAL VALIDA SI TIENE CARACTERISTICA DE LO CONTRARIO INSERTA LA CARACTERISTICA
      IF tareanivel5 IS NOT NULL AND tareanivel5 = 'S' THEN
          BEGIN
            SELECT NVL(MAX(ID_TAREA_CARACTERISTICA),0) INTO tareaCaracteristicaId FROM DB_SOPORTE.INFO_TAREA_CARACTERISTICA 
            WHERE TAREA_ID = idtareanueva AND CARACTERISTICA_ID =idcaracrequirematerial AND ESTADO= 'Activo';
          EXCEPTION
              WHEN NO_DATA_FOUND THEN

                IF idtareanueva IS NOT NULL THEN
                    INSERT INTO DB_SOPORTE.INFO_TAREA_CARACTERISTICA VALUES(
                        DB_SOPORTE.SEQ_INFO_TAREA_CARACTERISTICA.NEXTVAL,
                        idtareanueva,
                        NULL,
                        idcaracrequirematerial,
                        'S',
                        SYSTIMESTAMP,
                        'amontero',
                        '127.0.0.1',
                        NULL,
                        NULL,
                        NULL,
                        'Activo'
                    );
                END IF;

          END;
      END IF;

      --CONSULTA SI EXISTE YA EN EL ARBOL
      SELECT COUNT(ID_PARAMETRO_DET) INTO countn1 FROM DB_GENERAL.ADMI_PARAMETRO_DET 
      WHERE UPPER(DESCRIPCION) = UPPER(descripcionparametro) AND translate(UPPER(VALOR1),'áéíóúÁÉÍÓÚ','aeiouAEIOU')= translate(UPPER(tareanivel1),'áéíóúÁÉÍÓÚ','aeiouAEIOU') 
      AND translate(UPPER(VALOR2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = translate(UPPER(tareanivel2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') AND VALOR3 = TO_CHAR(idtareanueva) AND ESTADO = 'Activo';

      IF countn1 <= 0 THEN
        IF idtareanueva = 0 THEN
         totalNoExisteAdmiTarea := totalNoExisteAdmiTarea +1;
        ELSE
          INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
          (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
          descripcionparametro,UPPER(tareanivel1),UPPER(tareanivel2),idtareanueva,
          imagen,'Activo','amontero',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,
          'VALOR1=> NIVEL 1, VALOR2 => NIVEL 2, VALOR3 => NIVEL 3 (ID TAREA)');
        
          transInsert := transInsert +1;

        END IF;
      ELSE
        totalExiste := totalExiste + 1;
      END IF;
      


   END LOOP;

   DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                         'MIGRACION ARBOL TAREAS - ELECTRICO',
                                        ' [TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste ||
                                        ' [TOTAL REGISTRADAS]=> '||transInsert,
                                         NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                         SYSDATE,
                                         NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                         '127.0.0.1')
                                       );

   COMMIT;
  EXCEPTION
  WHEN OTHERS THEN
  --
  Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
  DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                        'MIGRACION ARBOL TAREAS - ELECTRICO',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/
