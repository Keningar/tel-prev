--MIGRACION DE MOTIVOS PARA ARBOL DE TAREAS
DECLARE 
   type niveltresarray IS VARRAY(800) OF VARCHAR2(600); 
   type motivosnuevosarray IS VARRAY(800) OF VARCHAR2(600); 
   motivonivel1 VARCHAR2(200);
   motivonivel2 VARCHAR2(200);
   motivonivel3 VARCHAR2(200);
   motivonivel4 VARCHAR2(200);
   motivonivel5 VARCHAR2(200);

   countn1 NUMBER;
   countn2 NUMBER;

   iddepartamento NUMBER;
   idmotivonuevo NUMBER;
   idtareanueva NUMBER;
   transInsert NUMBER := 0;
   totalcreamotivo NUMBER := 0;
   totalNoExisteAdmiTarea NUMBER := 0;
   niveltres niveltresarray; 

   motivostareasnuevas motivosnuevosarray;

   total integer;
   totalmotivosnuevos integer;

   idrelacionsistema       VARCHAR2(20)  := '10951';
   descripcionparametro    VARCHAR2(100) := 'MOTIVOS DE CATEGORIAS DE LAS TAREAS';
   descripcionparametrocab VARCHAR2(100) := 'MOTIVOS_CATEGORIA_DE_TAREA';

   Le_Exception                EXCEPTION;
   Lv_MensajeError             VARCHAR2(4000);

BEGIN 
    --SE CREA CABECERA PARA MOTIVOS DE CATEGORIA DE TAREAS
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
    ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        descripcionparametrocab,
        'DEFINE MOTIVOS QUE ESTAN ASOCIADOS CON CATEGORIA DE TAREA',
        'SOPORTE',
        null,
        'Activo',
        'amontero',
        sysdate,
        '127.0.0.1');
    COMMIT;

   niveltres := niveltresarray(
        'CONSTRUCCIÓN|RUTAS|3870|Cobertura|FO',
        'CONSTRUCCIÓN|RUTAS|3870|Sector saturado|FO',
        'CONSTRUCCIÓN|RUTAS|3870|Cambio de tramo|FO',
        'CONSTRUCCIÓN|RUTAS|3870|Hilos Oscuros|FO',
        'CONSTRUCCIÓN|RUTAS|3870|Proyectos|FO',
        'CONSTRUCCIÓN|RUTAS|3870|Clientes TN|FO',
        'CONSTRUCCIÓN|RUTAS|3888|Atenuación|FO',
        'CONSTRUCCIÓN|RUTAS|3888|Potencia elevada|FO',
        'CONSTRUCCIÓN|RUTAS|3888|Puerto Pon cambiado|FO',
        'CONSTRUCCIÓN|RUTAS|3892|Certificacion de ruta|FO',
        'CONSTRUCCIÓN|RUTAS|3892|Levantamiento de información|FO',
        'CONSTRUCCIÓN|RUTAS|3892|Levantamiento de pendientes|FO',
        'CONSTRUCCIÓN|RUTAS|6708|Capacidad|FO',
        'CONSTRUCCIÓN|RUTAS|6709|Capacidad|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Fibra dañada|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Corte de fibra|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Cliente TN|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Habilitación de hilos oscuros|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Proyectos|FO',
        'CONSTRUCCIÓN|RUTAS|3885|Cobertura|FO',
        'CONSTRUCCIÓN|RUTAS|6695|Habilitación de OLT|FO',
        'CONSTRUCCIÓN|RUTAS|6695|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6695|Ruta Nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6695|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6695|Arreglo de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6696|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6696|Arreglo de caja|FO',
        'CONSTRUCCIÓN|RUTAS|6696|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6698|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6698|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6698|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6698|Arreglo de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6699|Construcción ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6699|Corte de fibra|FO',
        'CONSTRUCCIÓN|RUTAS|6699|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6699|Cambio de tramo|FO',
        'CONSTRUCCIÓN|RUTAS|6699|Fibra dañada|FO',
        'CONSTRUCCIÓN|RUTAS|6700|Construcción ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6700|Corte de fibra|FO',
        'CONSTRUCCIÓN|RUTAS|6700|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6700|Cambio de tramo|FO',
        'CONSTRUCCIÓN|RUTAS|6700|Fibra dañada|FO',
        'CONSTRUCCIÓN|RUTAS|6703|Construcción ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6703|Corte de fibra|FO',
        'CONSTRUCCIÓN|RUTAS|6703|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6703|Cambio de tramo|FO',
        'CONSTRUCCIÓN|RUTAS|6703|Fibra dañada|FO',
        'CONSTRUCCIÓN|RUTAS|6704|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6704|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6704|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6704|Cambio de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6704|Arreglo de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6705|Cambio de caja|FO',
        'CONSTRUCCIÓN|RUTAS|6705|Arreglo de caja|FO',
        'CONSTRUCCIÓN|RUTAS|6705|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6705|Daño de caja|FO',
        'CONSTRUCCIÓN|RUTAS|6706|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6706|Arreglo de pedestal|FO',
        'CONSTRUCCIÓN|RUTAS|6706|Cambio de Pedestal|FO',
        'CONSTRUCCIÓN|RUTAS|6706|Capacidad|FO',
        'CONSTRUCCIÓN|RUTAS|6710|Capacidad|FO',
        'CONSTRUCCIÓN|RUTAS|6710|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6710|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6716|Condenar hilos oscuros|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Estandarización de rack|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Acometida de Fibra|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Arreglo de rack|FO',
        'CONSTRUCCIÓN|RUTAS|6692|Verificación de espacios en racks|FO',
        'CONSTRUCCIÓN|RUTAS|6694|Ruta nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6694|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6694|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6694|Cambio de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6694|Arreglo de ODF|FO',
        'CONSTRUCCIÓN|RUTAS|6697|Requerimiento nuevo|FO',
        'CONSTRUCCIÓN|RUTAS|6697|Migración de RBS|FO',
        'CONSTRUCCIÓN|RUTAS|6697|Arreglo RBS|FO',
        'CONSTRUCCIÓN|RUTAS|6701|Certificación de spliter en caja|FO',
        'CONSTRUCCIÓN|RUTAS|6701|Certificación de odf reflejo de 48 H|FO',
        'CONSTRUCCIÓN|RUTAS|6701|Certificación de odf reflejo de 96 H|FO',
        'CONSTRUCCIÓN|RUTAS|6701|Certificación de odf reflejo de 192 H|FO',
        'CONSTRUCCIÓN|RUTAS|6701|SP de primer nivel|FO',
        'CONSTRUCCIÓN|RUTAS|6701|SP de segundo nivel|FO',
        'CONSTRUCCIÓN|RUTAS|6702|Migración de RBS|FO',
        'CONSTRUCCIÓN|RUTAS|6702|Capacidad|FO',
        'CONSTRUCCIÓN|RUTAS|6707|Ruta Nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6707|Migración de Nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6707|Proyecto|FO',
        'CONSTRUCCIÓN|RUTAS|6707|Hilos Obscuros|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Ruta Nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Tarea Programada|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Hilos Obscuros|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Proyecto|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6711|Cliente TN|FO',
        'CONSTRUCCIÓN|RUTAS|6712|Cliente TN|FO',
        'CONSTRUCCIÓN|RUTAS|6712|Puertos Spliter|FO',
        'CONSTRUCCIÓN|RUTAS|6712|Arreglo de caja|FO',
        'CONSTRUCCIÓN|RUTAS|6713|Reingenierías|FO',
        'CONSTRUCCIÓN|RUTAS|6713|Migración de nodos|FO',
        'CONSTRUCCIÓN|RUTAS|6713|Cliente TN|FO',
        'CONSTRUCCIÓN|RUTAS|6713|Estandarización de racks|FO',
        'CONSTRUCCIÓN|RUTAS|6713|Arreglo de Nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6714|Cliente TN|FO',
        'CONSTRUCCIÓN|RUTAS|6714|Enlaces Especiales|FO',
        'CONSTRUCCIÓN|RUTAS|6714|Proyecto|FO',
        'CONSTRUCCIÓN|RUTAS|6715|Ruta Nueva|FO',
        'CONSTRUCCIÓN|RUTAS|6715|Migración de nodo|FO',
        'CONSTRUCCIÓN|RUTAS|6719|Levantamiento de pendientes|FO',
        'CONSTRUCCIÓN|RUTAS|6719|Fiscalización|FO',
        'CONSTRUCCIÓN|RUTAS|6719|Aprobación|FO',
        'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|3888|Atenuación|FO',
        'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|3888|Potencia elevada|FO',
        'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|3888|Puerto Pon cambiado|FO',
        'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|3892|Culminacion Proyecto|FO',
        'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|3885|Cambio de trayectoria|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3893|Red Nueva|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3893|Cliente TN|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3895|Red Nueva|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3895|Cliente TN|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3897|Red Nueva|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|3897|Arreglo de miniposte|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6689|Ruta nueva|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6689|Arreglo de nodo|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6689|Migración de nodo|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6689|Cambio de ODF|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6689|Arreglo de ODF|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6691|Capacidad|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6691|Cobertura|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6688|Red Nueva|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6688|Cliente TN|FO',
        'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|6690|Fiscalización|FO',
        'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|3893|Capacidad|FO',
        'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|3893|Soterramiento|FO',
        'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|3895|Capacidad|FO',
        'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|3895|Cambio Infraestructura|FO',
        'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|3897|Requerimiento Cliente|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3921|Fiscalización|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3921|Tiempo de vida uútil|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3923|Trabajos de terceros|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3923|Tiempo de vida útil|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3926|Manipulación por terceros|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3926|Tiempo de vida útil amarras|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|2075|Tiempo de vida útil|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|2075|Daño|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|2384|Fiscalización|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|2384|Tiempo de vida uútil|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3917|Verificación de posible daño|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3917|Mantenimiento de ruta|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3917|Prevención de riesgo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3918|Prevención de riesgo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3918|Mantenimiento de ruta|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3919|Prevención de riesgo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3919|Mantenimiento de ruta|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3922|Retiro de posteria ampliación de vía|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3922|Retiro de postes|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3922|Cambio de tramo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3930|Prevención de riesgo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3930|Poste inclinado.|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3970|Actualización de ruta|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3970|Levantamiento de información|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|4012|Requerimiento Cliente|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|4012|Proyecto|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|4012|Hilos Obscuros|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|4012|Eliminación de atenuación|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6332|Eliminación de Mangas|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6332|Por fibra en mal estado|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6332|Por cambio de ruta|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6332|Por cambio de postería|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6332|Ente Regulatorio|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Trabajos de terceros|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Accidente de tránsito|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Descarga eléctrica|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por caida de árbol|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por paso de camión con carga elevada|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por ampliación de vía|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por tala de árboles|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por caída de poste|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por deslave|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por roedores|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Daño interno de la fibra|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por incendio|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6748|Por poda de árbol|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Daño en tramo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Patchcord de fibra|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Acopladores|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Pigtail|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Recogimiento de hilos de fibra|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Cambio de Poste|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Mala práctica técnico|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3925|Extrangulamiento de Cable|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|3924|Solicitud|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6742|Vano bajo|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6742|Cambio de poste|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6744|Cancelación de servicio|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6744|Ordenanza Municipal|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6744|Reutilización de FO|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6746|Tiempo de vida útil|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Certificación de hilos|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Cambio limpieza de acopladores|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Rehacer manga|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Rehacer ODF|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Corrección de atenuaciones mangas y postes|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6747|Solicitud|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6741|Capacidad|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6741|Migración de enlace|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6743|Fibra afectada|FO',
        'INTERURBANO|MANTENIMIENTO DE RUTAS|6745|Nueva cobertura|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6761|Traslado|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6761|Requerimiento|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6758|Daño|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6758|Requerimiento Cliente|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6756|Proyecto|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6757|Cliente|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6759|Culminacion Proyecto|FO',
        'LAN PROYECTOS|LAN / CENTRO COMERCIAL|6760|Ente Regulatorio|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6723|Soterramiento|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6723|Cambio de postes|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6724|Soterramiento|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6728|Movimiento de poste|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6728|Arreglo de caja|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6728|Mantenimiento|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6736|Cliente TN|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6736|Cliente NL|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6721|Cliente TN|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6722|Retiro fibra inactiva|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6722|Caída de postes|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6722|Cambio de tramo|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6725|Cambio de poste|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6725|Aplome de poste|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6725|Ampliacion de via|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6725|Retiro de poste|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Cliente NL|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Soterramiento|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Arreglo de Cajas|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Mantenimiento de caja|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Mantenimiento de ruta|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6729|Reingeniería|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Cliente NL|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Arreglo de caja|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Cliente TN|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Soterramiento|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Caída de postes|FO',
        'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|6730|Reubicaión de postes|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6731|Ente Regulatorio|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6731|Urbanización / Conjunto|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6731|Regeneracion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6733|Reordenamiento|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6733|Migración de Nodo|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6733|Ente Regulatorio|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6734|Soterramiento|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6734|Cambio de poste|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6734|Daño|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6734|Ente Regulatorio|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6737|Ente Regulatorio|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6737|Urbanización / Conjunto|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6739|Soterramiento|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6739|Construccion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6720|Inspeccion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6720|Soterramiento|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6727|Migración de Nodo|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6727|Daño|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6727|Cancelación de servicio|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6732|Proyecto|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6732|Novedades|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6732|Afectacion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6735|Inspeccion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6735|Culminacion Proyecto|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6735|Asignacion de Ductos|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6738|Migración de Nodo|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6738|Etiquetado|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|6740|Migracion|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3519|Migración de Nodo|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3519|Daño|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3519|Migración de plataforma|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3933|Identiifcación de ductos|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3933|Paso de guías|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3933|Estado de ductos|FO',
        'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|3935|Actualizacion|FO',



        'CLIENTES|NETLIFE|6415|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6415|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|NETLIFE|6415|Manipulación necesaria en equipos del cliente|ELECTRICO',
        'CLIENTES|NETLIFE|6414|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6414|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|NETLIFE|6414|Manipulación necesaria en equipos del cliente|ELECTRICO',
        'CLIENTES|NETLIFE|6417|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6417|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|NETLIFE|6418|Breaker tripeado por Corto circuito|ELECTRICO',
        'CLIENTES|NETLIFE|6418|Daño de lámpara por corto circuito|ELECTRICO',
        'CLIENTES|NETLIFE|6412|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6412|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|NETLIFE|6412|Desconexión en cajetín telefónico|ELECTRICO',
        'CLIENTES|NETLIFE|6412|Desconexión en regleta telefónica|ELECTRICO',
        'CLIENTES|NETLIFE|6419|Desconexión de cableado|ELECTRICO',
        'CLIENTES|NETLIFE|6419|Corto circuito|ELECTRICO',
        'CLIENTES|NETLIFE|6413|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6413|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|NETLIFE|6420|Daño en acometida eléctrica|ELECTRICO',
        'CLIENTES|NETLIFE|6416|Desconexión de puntos de datos|ELECTRICO',
        'CLIENTES|NETLIFE|6416|Daño de cableado por Obra Civil|ELECTRICO',
        'CLIENTES|NETLIFE|6416|Daño de cableado por paso de UM|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6351|Manipulación por el cliente|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6351|Manipulación por terceros|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Manipulación por el cliente|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Manipulación por terceros|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Variación de voltaje|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Falla de multitoma eléctrico|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Avería de Switch|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Avería en puerto de Switch|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Daño de cableado|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Daño de fuente|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Pérdida de configuraciones de cámara|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Pérdida de contraseñas|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6350|Manipulación de software de monitoreo|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6349|Manipulación por el cliente|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6349|Manipulación por terceros|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6349|Daño de teléfono|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6349|Daño de cableado|ELECTRICO',
        'CLIENTES|PROYECTOS LAN|6349|Pérdida de configuraciones|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5242|Relé interno quemado|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5242|Daño del interruptor|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6334|Aumento de capacidad / Mejorar tiempos de respaldo|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6334|Cumplen tiempo de vida útil, falla interna|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6334|Descontinuado, no existe en stock|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5243|Sobrecarga|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6319|Cumplen tiempo de vida útil|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6319|Bajo Voltaje|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5235|Cumplen tiempo de vida útil|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5235|Bajo Voltaje|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5239|Aumento de carga|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5239|Falla interna|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5237|Falla interna, no cumple funcion de carga|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6318|Falla interna, no cumple funcion de carga|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5241|Falla interna, terminales sulfatados|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5241|Daño del interruptor|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5238|Recalentamiento de aislante|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|5238|Falla en lectura de parámertros de configuración|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6362|Falla interna, causada por variaciones de voltaje|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6362|Falla banco de baterías interno|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Bajo nivel de combustible|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Paro de emergencia activado|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Bajo nivel de regrigerante, fuga|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Batería en mal estado|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Mantenedor de carga en mal estado|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6317|Breaker de salida de voltaje en estado OFF|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6316|Desconfiguración en parámetros de transferencia|ELECTRICO',
        'NODO|MANTENIMIENTO CORRECTIVO|6316|Mala manipulación en modo de trabajo Maual/Automático|ELECTRICO',
        'NODO|SOPORTE|4785|Ausencia de voltaje en lineas de alimentación|ELECTRICO',
        'NODO|SOPORTE|4785|Variaciones de voltaje en Red de alimentación|ELECTRICO',
        'NODO|SOPORTE|4785|Trabajos programados por la Empresa Eléctrica|ELECTRICO',
        'NODO|SOPORTE|4785|Corte de energía por falta de pago|ELECTRICO',
        'NODO|SOPORTE|4785|Manipulación de breaker del medidor por terceras personas|ELECTRICO',
        'NODO|SOPORTE|2592|Falla de aires acondicionados por daños eléctronicos|ELECTRICO',
        'NODO|SOPORTE|2592|Manipulación de control de temperatura por terceros|ELECTRICO',
        'NODO|SOPORTE|2592|Manipulación del breaker de alimentación de aires acondicionados|ELECTRICO',
        'NODO|SOPORTE|6321|Conexión floja, falla en contacto de alimentación|ELECTRICO',
        'NODO|SOPORTE|6321|Ausencia de voltaje en una de las fuentes de alimentación|ELECTRICO',
        'NODO|SOPORTE|2593|Bajo voltaje en banco de baterías|ELECTRICO',
        'NODO|SOPORTE|2593|Cumplen tiempo de vida útil|ELECTRICO',
        'NODO|SOPORTE|2593|Ausencia del cable de poder otorgado por Megadatos|ELECTRICO',
        'NODO|SOPORTE|2593|Manipulación del breaker de alimentación|ELECTRICO',
        'NODO|SOPORTE|2387|Estado inhibido|ELECTRICO',
        'NODO|SOPORTE|2387|Modo en baterías|ELECTRICO',
        'NODO|SOPORTE|2387|Falla baterías|ELECTRICO',
        'NODO|SOPORTE|2387|Sobregarga|ELECTRICO',
        'NODO|SOPORTE|2387|Temperatura alta|ELECTRICO',
        'NODO|SOPORTE|2387|Estado OFF|ELECTRICO',
        'NODO|TELEPUERTO / GOSSEAL|2546|Mala manipulación del técnico|ELECTRICO',
        'NODO|TELEPUERTO / GOSSEAL|2545|Cambio de fuente|ELECTRICO',
        'NODO|TELEPUERTO / GOSSEAL|2545|Cambio de patch cord|ELECTRICO',
        'NODO|TELEPUERTO / GOSSEAL|2545|Cambio de Transceiver|ELECTRICO',



        'Problema de UM RE|Equipo de Radio dañado en el nodo|5327|equipo dañado|RADIO',
        'Problema de UM RE|Saturacion de Capacidad del AP|5327|saturación de enlace|RADIO',
        'Problema de UM RE|Cable dañado en nodo|2629|cable dañado|RADIO',
        'Problema de UM RE|Interferencia de frecuencia|5303|Interferencia de RF|RADIO',
        'Problema de UM RE|Interferencia de frecuencia|5327|Interferencia de RF|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el nodo|5327|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el nodo|4286|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Saturacion de ancho de banda contratado|5327|saturación de enlace|RADIO',
        'Problema de UM RE|Cable UTP dañado en el cliente|2629|cable dañado|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el cliente|5327|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el cliente|4286|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Desmontaje de Antena en el cliente|4286|pérdida de linea de vista al nodo|RADIO',
        'Problemas servicio WIFI|Problemas con el portal cautivo|5281|configuración errónea|RADIO',
        'Problemas servicio WIFI|Problema de contraseña WIFI|5299|configuración errónea|RADIO',
        'Problemas servicio WIFI|Error de Activacion en la controladora WIFI|5298|configuración errónea|RADIO',
        'Inspección|Inspección para determinar factibilidad de RADIO.|3522|factibilidad nuevo servicio|RADIO',
        'Inspección|Inspeccion cobertura WIFI|5294|factibilidad nuevo servicio|RADIO',
        'Inspección|Inspeccion cobertura WIFI|5295|factibilidad nuevo servicio|RADIO',
        'Mantenimiento|Nodo de RADIO|2629|cable dañado|RADIO',
        'Mantenimiento|Nodo de RADIO|4001|mantenimiento de infraestructura|RADIO',
        'Mantenimiento|WIFI|2629|cable dañado|RADIO',
        'Mantenimiento|WIFI|5296|factibilidad nuevo servicio|RADIO',
        'Mantenimiento|WIFI|4114|factibilidad nuevo servicio|RADIO',
        'Retiro|RADIO/WIFI|3999|cancelación de servicios|RADIO',
        'Retiro|RADIO/WIFI|4000|cancelación de servicios|RADIO',


        'BACKBONE|ATENUACIÓN|2630|recojimiento de hilos en la manga|OPU',
        'BACKBONE|ATENUACIÓN|2630|Manga mal armada|OPU',
        'BACKBONE|ATENUACIÓN|2630|Fibra tensada|OPU',
        'BACKBONE|ATENUACIÓN|2630|Radio de curbatura muy pronunciada|OPU',
        'BACKBONE|ATENUACIÓN|2630|Trabajo de terceros|OPU',
        'BACKBONE|ATENUACIÓN|2650|recojimiento de hilos en la manga|OPU',
        'BACKBONE|ATENUACIÓN|2650|Manga mal armada|OPU',
        'BACKBONE|ATENUACIÓN|2650|Fibra tensada|OPU',
        'BACKBONE|ATENUACIÓN|2650|Radio de curbatura muy pronunciada|OPU',
        'BACKBONE|ATENUACIÓN|2650|Trabajo de terceros|OPU',
        'BACKBONE|CORTE FIBRA|2594|Trabajo de terceros|OPU',
        'BACKBONE|CORTE FIBRA|2594|Paso de vehiculo carga alta|OPU',
        'BACKBONE|CORTE FIBRA|2594|Cambio de posteria|OPU',
        'BACKBONE|CORTE FIBRA|2594|Fibra dañada por des carga electricidad|OPU',
        'BACKBONE|CORTE FIBRA|2594|Deslave|OPU',
        'BACKBONE|CORTE FIBRA|2594|Caida de Arbol|OPU',
        'BACKBONE|CORTE FIBRA|2594|Trabajo en caja BMX|OPU',
        'CLIENTE|MNIODF/ROSETA|2613|Manipulacion de cliente|OPU',
        'CLIENTE|MNIODF/ROSETA|2613|Daño de conector|OPU',
        'CLIENTE|MNIODF/ROSETA|2614|Manipulacion de cliente|OPU',
        'CLIENTE|MNIODF/ROSETA|2614|Sin caja multimedia|OPU',
        'CLIENTE|MNIODF/ROSETA|2615|Daño Modulo|OPU',
        'CLIENTE|MNIODF/ROSETA|2616|Daño de fusíon|OPU',
        'CLIENTE|MNIODF/ROSETA|2616|Recogimiento de hilos|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2626|Saturacion de espectro|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2649|Plantilla no pertenece al plan|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2649|Cargar nuevamente plantilla|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2649|Equipo Reseteado|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2649|Actualización de Firmware|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2672|Equipo desconfigurado|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|2672|Equipo sin gestión|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|3235|Equipo sin gestión|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|3235|Camara Reseteada|OPU',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|3235|Requerimiento del cliente|OPU',
        'CLIENTE|EQUIPOS|2618|Robo de Fuente|OPU',
        'CLIENTE|EQUIPOS|2618|Fuente incorrecta|OPU',
        'CLIENTE|EQUIPOS|2618|Daño de la fuente|OPU',
        'CLIENTE|EQUIPOS|2618|Fuente desconectada|OPU',
        'CLIENTE|EQUIPOS|2619|Robo transceiver|OPU',
        'CLIENTE|EQUIPOS|2619|Daño de transceiver|OPU',
        'CLIENTE|EQUIPOS|2621|Robo de Fuente|OPU',
        'CLIENTE|EQUIPOS|2621|Fuente incorrecta|OPU',
        'CLIENTE|EQUIPOS|2621|Daño de la fuente|OPU',
        'CLIENTE|EQUIPOS|2621|Fuente desconectada|OPU',
        'CLIENTE|EQUIPOS|2627|Daño del equipo|OPU',
        'CLIENTE|EQUIPOS|2627|Robo|OPU',
        'CLIENTE|EQUIPOS|2627|Cambio de modelo|OPU',
        'CLIENTE|EQUIPOS|2628|Manipulacion de cliente o terceros|OPU',
        'CLIENTE|EQUIPOS|2628|Robo|OPU',
        'CLIENTE|EQUIPOS|2628|Daño de la fuente|OPU',
        'CLIENTE|EQUIPOS|2646|Requerimiento del cliente|OPU',
        'CLIENTE|EQUIPOS|2646|Intermitencias|OPU',
        'CLIENTE|EQUIPOS|2647|Requerimiento del cliente|OPU',
        'CLIENTE|EQUIPOS|2671|Manipulacion de cliente o terceros|OPU',
        'CLIENTE|EQUIPOS|2671|Upgrade de enlace|OPU',
        'CLIENTE|EQUIPOS|2671|Robo de equipo|OPU',
        'CLIENTE|EQUIPOS|2671|Daño del equipo|OPU',
        'CLIENTE|EQUIPOS|2648|Manipulacion de cliente o terceros|OPU',
        'CLIENTE|EQUIPOS|3262|Requerimiento del cliente|OPU',
        'CLIENTE|EQUIPOS|3262|Problema de cobertura|OPU',
        'CLIENTE|PATCHCORD|2612|Daño en el conector|OPU',
        'CLIENTE|PATCHCORD|2612|Manipulacion de cliente o terceros|OPU',
        'CLIENTE|PATCHCORD|2617|Daño en el conector|OPU',
        'CLIENTE|PATCHCORD|2617|Manipulacion de cliente o terceros|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5000|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5001|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5002|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5006|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5010|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4739|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4740|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4741|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4742|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4746|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4998|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|4999|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5005|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5007|Mal trabajo del tecnico|OPU',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|5058|Mal trabajo del tecnico|OPU',
        'INSTALACIÓN|INSPECCIÓN|2673|Requerimiento del cliente|OPU',
        'INSTALACIÓN|INSPECCIÓN|3236|Requerimiento previo a instalacion|OPU',
        'INSTALACIÓN|INSPECCIÓN|3893|Requerimiento previo a instalacion|OPU',
        'INSTALACIÓN|INSPECCIÓN|3895|Requerimiento previo a instalacion|OPU',
        'INSTALACIÓN|INSPECCIÓN|3916|Requerimiento previo a instalacion|OPU',
        'INSTALACIÓN|INSTALACIÓN|3233|Requerimiento del cliente|OPU',
        'INSTALACIÓN|MIGRACIÓN CLIENTE|3513|Reingenieria|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|3220|Daños por terceros|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|4678|Trabajo de terceros|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3227|Requerimiento del cliente|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3228|Requerimiento del cliente|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3923|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|5794|Requerimiento del cliente|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3224|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3225|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|3226|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|4040|Daño en manga|OPU',
        'MANTENIMIENTO|TELEFÓNICA|3218|Mantenimiento de rutas Telefonica|OPU',
        'NODO|EQUIPOS|2076|Daño de la interfaz|OPU',
        'NODO|EQUIPOS|2294|Daño de la interfaz|OPU',
        'NODO|EQUIPOS|2601|Daño de la fuente|OPU',
        'NODO|EQUIPOS|2602|Transceiver antiguos|OPU',
        'NODO|EQUIPOS|2602|Sin Transceiver por retiro|OPU',
        'NODO|EQUIPOS|2602|Sin transceiver por activar otro cliente|OPU',
        'NODO|EQUIPOS|2602|Daño del equipo|OPU',
        'NODO|EQUIPOS|2623|Por migración|OPU',
        'NODO|EQUIPOS|2623|Daño de la interfaz|OPU',
        'NODO|ODF|2598|Fusión deteriorada|OPU',
        'NODO|ODF|2598|Recogimiento de hilos|OPU',
        'NODO|ODF|2600|Daño adpaptador|OPU',
        'NODO|ODF|2655|Daño por roedores|OPU',
        'NODO|ODF|2655|Daño por manipulacion|OPU',
        'NODO|ODF|2632|Requerimientos|OPU',
        'NODO|PATCHCORD|2651|Daño de conector|OPU',
        'NODO|PATCHCORD|2651|Daño por manipulacion|OPU',
        'NODO|PATCHCORD|2652|Daño en conector|OPU',
        'NODO|PATCHCORD|2652|Daño por manipulacion|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|recojimiento de hilos en la manga|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Manga mal armada|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Fibra tensada|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Radio de curbatura muy pronunciada|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Daño por terceros|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Paso de vehiculo carga alta|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Cambio de posteria|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Caida de Arbol|OPU',
        'RED DE DISTRIBUCIÓN|ATENUACION|2604|Daño en manga|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2607|Daño por terceros|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2607|Daño por roedores|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2607|Mala manipulación del técnico|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2607|Hilos Cristalizados|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2608|Degradación modulo|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|Mala manipulación del técnico|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|Daño por roedores|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2609|Hilos Cristalizados|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|7489|Daño en el splitter|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|7489|Regularizacion de splitter|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4991|Daño de posteria|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4991|Cambio de recorrido|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4991|Transformador en el poste|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4991|Requerimiento del cliente|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|Malas fusiones|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|Daño por inventario|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|Daño por instalación nueva|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|Daño por soporte|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|2660|Recogimiento de hilos|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4968|Requerimientos GIS|OPU',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|4968|Habilitacion clientes|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2605|Daño en la FO|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Daño por terceros|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Fibra dañada por descarga electricidad|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Deslave|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Caida de Arbol|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Daño en manga|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Paso de vehiculo carga alta|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Cambio de posteria|OPU',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|2603|Trabajo en caja BMX|OPU',
        'RETIRO|EQUIPOS|2897|Cancelacion del enlace|OPU',
        'RETIRO|EQUIPOS|2898|Cancelacion del enlace|OPU',
        'RETIRO|EQUIPOS|3251|Requerimientos control de activos|OPU',
        'RETIRO|FIBRA|3247|Cancelacion del enlace|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Daño por terceros|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Fibra mal tendica en la instalación|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Fibra mal tendica en el soporte|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Fibra pandeada|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Daño por roedores|OPU',
        'ULTIMA MILLA|CORTE FIBRA|2611|Paso de vehiculo carga alta|OPU',
        'ULTIMA MILLA|MINIMANGA|2610|Mala fusion de hilo de fibra|OPU',
        'ULTIMA MILLA|MINIMANGA|2610|Recogimiento de hilos|OPU'
       ); 

   motivostareasnuevas := motivosnuevosarray(
        'Problema de UM RE|Equipo de Radio dañado en el nodo|Cambio de equipo de Radio|equipo dañado|RADIO',
        'Problema de UM RE|Saturacion de Capacidad del AP|Control de Ancho de Banda en el radio|saturación de enlace|RADIO',
        'Problema de UM RE|Saturacion de Capacidad del AP|Control de Ancho de Banda en el CPE|saturación de enlace|RADIO',
        'Problema de UM RE|Antena Desalineada en el nodo|Alineacion de antena|antena desalineada|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el nodo|Incremento de torre|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el nodo|Eliminacion de la obstruccion a la linea de vista|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|PoE quemado en el nodo|Cambio de PoE|equipo dañado|RADIO',
        'Problema de UM RE|PoE Desconectado en el nodo|Conexión de PoE|manipulación erronea|RADIO',
        'Problema de UM RE|Radio dañado en el cliente|Cambio de equipo de Radio|equipo dañado|RADIO',
        'Problema de UM RE|Configuracion de Radio en el cliente|Cambio de configuracion|manipulación erronea|RADIO',
        'Problema de UM RE|Saturacion de ancho de banda contratado|Control de Ancho de Banda en el radio|saturación de enlace|RADIO',
        'Problema de UM RE|Saturacion de ancho de banda contratado|Control de Ancho de Banda en el CPE|saturación de enlace|RADIO',
        'Problema de UM RE|Antena Desalineada en el cliente|Alineacion de antena|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el cliente|Incremento de torre|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|Obstruccion de linea de vista en el cliente|Eliminacion de la obstruccion a la linea de vista|pérdida de linea de vista al nodo|RADIO',
        'Problema de UM RE|PoE dañado en el cliente|Cambio de PoE|equipo dañado|RADIO',
        'Problema de UM RE|PoE desconectado en el cliente|Conexión de PoE|manipulación erronea|RADIO',
        'Problema de UM RE|Desconexion de cables en el cliente|Conexión de cables UTP|manipulación erronea|RADIO',
        'Problemas servicio WIFI|Equipo WIFI dañado|Cambio de Equipo WIFI|equipo dañado|RADIO',
        'Problemas servicio WIFI|Problemas con el portal cautivo|Configuracion en la controladora WIFI|configuración errónea|RADIO',
        'Problemas servicio WIFI|Equipo WIFI apagado|Conexión a la red electrica del equipo WIFI|manipulación erronea|RADIO',
        'Problemas servicio WIFI|Cobertura red WIFI|Se requiere aumentar el numero de equipos WIFI|configuración errónea|RADIO',
        'Problemas servicio WIFI|Cobertura red WIFI|Aumento de potencia de tx en AP WiFI|configuración errónea|RADIO',
        'Problemas servicio WIFI|Cobertura red WIFI|Reubicacion de AP WIFI|configuración errónea|RADIO',
        'Problemas servicio WIFI|Daño de UM para red WIFI|Arreglo de UM para red WIFI|configuración errónea|RADIO',
        'Problemas servicio WIFI|Falla en la configuracion logica en el switch|Configuracion logica en el switch|configuración errónea|RADIO',
        'Problemas servicio WIFI|Falla en la configuracion Logica en el CPE|Configuracion logica en el CPE|configuración errónea|RADIO',
        'Problemas servicio WIFI|Cliente desconecta los equipos|Conexión de los equipos|manipulación erronea|RADIO',
        'Inspección|Inspección para determinar factibilidad de RADIO.|Se rechaza factibilidad|sin cobertura de nuevo servicio|RADIO',
        'Mantenimiento|Nodo de RADIO|Cambio de equipo de Radio|equipo dañado|RADIO',
        'Mantenimiento|Nodo de RADIO|Cambio de configuracion|mantenimiento de infraestructura|RADIO',
        'Mantenimiento|WIFI|Cambio de Equipo WIFI|equipo dañado|RADIO',


        'BACKBONE|ATENUACIÓN|Cambio de tarjetas SFP|Upgrade de enlace|OPU',
        'BACKBONE|ATENUACIÓN|Cambio de tarjetas SFP|Tarjeta Inhibida|OPU',
        'BACKBONE|ATENUACIÓN|Se arregla atenuacion en posteria|Movimiento de posteria|OPU',
        'BACKBONE|ATENUACIÓN|Se arregla atenuacion en posteria|Fibra pandeada|OPU',
        'BACKBONE|ATENUACIÓN|Se arregla atenuacion en posteria|Fibra presionada en preformado|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Trabajo de terceros|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Paso de vehiculo carga alta|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Cambio de posteria|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Fibra dañada por des carga electricidad|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Deslave|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Caida de Arbol|OPU',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica|Trabajo en caja BMX|OPU',
        'CLIENTE|EQUIPOS|Equipos del cliente|Problemas de Red interna|OPU',
        'CLIENTE|EQUIPOS|Equipos del cliente|No se encuentra el equipo|OPU',
        'CLIENTE|EQUIPOS|Reubicacion Equipos en Cliente Sin Fibra|Problema de cobertura|OPU',
        'CLIENTE|EQUIPOS|Reubicacion Equipos en Cliente Sin Fibra|Requerimiento del cliente|OPU',
        'CLIENTE|ATENUACIÓN|Eliminar atenuacion en cliente|Manipulacion de cliente o terceros|OPU',
        'CLIENTE|ATENUACIÓN|Eliminar atenuacion en cliente|Mala fusion de hilo de fibra|OPU',
        'CLIENTE|ATENUACIÓN|Eliminar atenuacion en cliente|Recogimiento de hilos|OPU',
        'INSTALACIÓN|INSTALACIÓN|Colocar Cajas para liberacion de conjunto|Habilitacion clientes|OPU',
        'INSTALACIÓN|INSTALACIÓN|Tendido de fibra|Habilitacion clientes|OPU',
        'INSTALACIÓN|INSTALACIÓN|Colocar Caja y Splitter|Habilitacion clientes|OPU',
        'MANTENIMIENTO|PEDESTAL|Arreglo de Pedestal|Trabajos de terceros|OPU',
        'MANTENIMIENTO|PEDESTAL|Arreglo de Pedestal|Cambio de Modelo|OPU',
        'MANTENIMIENTO|PEDESTAL|Arreglo de Pedestal|Daño de pedestal|OPU',
        'MANTENIMIENTO|PEDESTAL|Arreglo de Pedestal|Mala manipulacion tecnicos|OPU',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal|Trabajos de terceros|OPU',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal|Cambio de Modelo|OPU',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal|Daño de pedestal|OPU',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal|Mala manipulacion tecnicos|OPU',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Arreglo de caja MPLS|Cambio modelo caja|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Arreglo de caja MPLS|Mala manipulacion tecnicos|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Mantenimiento Preventivo Caja MPLS|Cambio modelo caja|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Mantenimiento Preventivo Caja MPLS|Mala manipulacion tecnicos|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Mantenimiento Preventivo Caja MPLS|Mantenimiento de rutas|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Colocar tapa en Caja BMX|Caja sin tapa|OPU',
        'MANTENIMIENTO|CAJA BMX/FTTH|Colocar tapa en Caja BMX|Tapa Rota|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|Recuperación de hilos|Manga sin termianar de fusionar|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|Recuperación de hilos|Daño interno de hilo|OPU',
        'MANTENIMIENTO|ENLACES URBANOS|Recuperación de hilos|Arreglo de rutas|OPU',
        'NODO|EQUIPOS|Cambio de switch|Requerimientos Networking|OPU',
        'NODO|EQUIPOS|Cambio de switch|Daño del equipo|OPU',
        'NODO|EQUIPOS|Revision equipos en nodo|Requerimientos NOC / Networking / IPCCL2|OPU',
        'NODO|ODF|Cambiar Splitter L1|Daño en el splitter|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuacion por daño en la FO|Daño de terceros|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuacion por daño en la FO|Atenuada por empaquetamiento|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuacion por daño en la FO|No usan kit de acometida|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuacion por daño en la FO|Fijador con gancho|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuación en el tramo de FO|Daño interno de hilo|OPU',
        'ULTIMA MILLA|ATENUACION|Atenuación en el tramo de FO|Mala manipulación de la fibra|OPU',
        'ADMINISTRATIVO|OPU|No se desmonta fibra|No se encuentra fibra en campo|OPU',
        'ADMINISTRATIVO|OPU|Registro de equipo en NAF|Requerimiento Control de activos|OPU',
        'ADMINISTRATIVO|OPU|Liberacion Edif/Conj/Urb/CC|Habilitacion clientes|OPU',
        'ADMINISTRATIVO|OPU|Regularizacion de FO|Requerimiento Control de activos|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Desconexíon por instalación|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Reingeniería|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Mal inventario|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Caja interna de edifico|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Desconexíon por soporte|OPU',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON|Caja mas Cercana|OPU',
        'ADMINISTRATIVO|OPU|Regularizacion de FO retirada|Requerimiento Control de activos|OPU',
        'ADMINISTRATIVO|OPU|Configuración CPE TN|Requerimiento IPCCL2|OPU',
        'ADMINISTRATIVO|OPU|Informe Telefónica|Requerimeinto del Cliente|OPU',
        'ADMINISTRATIVO|OPU|Enlace operativo sin interveción técnica TN|Enlace operativo|OPU'

   );

   total := niveltres.count;
   --dbms_output.put_line('Total '|| total || ' TAREAS'); 
   FOR i in 1 .. total LOOP
        countn1:=0;
        countn2:=0;
        idmotivonuevo:=0;
        motivonivel1 := SUBSTR(niveltres(i),0,(INSTR(niveltres(i),'|')-1)); 
        motivonivel2 := SUBSTR(niveltres(i), (INSTR(niveltres(i),'|')+1), LENGTH(niveltres(i)) );
        motivonivel3 := SUBSTR(motivonivel2,(INSTR(motivonivel2,'|')+1),LENGTH(motivonivel2)); 
        motivonivel2 := SUBSTR(motivonivel2,0,(INSTR(motivonivel2,'|')-1));
        motivonivel4 := SUBSTR(motivonivel3,INSTR(motivonivel3,'|')+1,LENGTH(motivonivel3)); 
        motivonivel3 := SUBSTR(motivonivel3,0,(INSTR(motivonivel3,'|')-1)); 
        motivonivel5 := SUBSTR(motivonivel4,INSTR(motivonivel4,'|')+1,LENGTH(motivonivel4));
        motivonivel4 := SUBSTR(motivonivel4,0,(INSTR(motivonivel4,'|')-1));

        IF (TRIM(motivonivel5) = 'RADIO') THEN
                iddepartamento     := 124;
        ELSIF(TRIM(motivonivel5) = 'ELECTRICO') THEN
                iddepartamento     := 129;
        ELSIF(TRIM(motivonivel5) = 'FO') THEN
                iddepartamento     := 116;
        ELSIF(TRIM(motivonivel5) = 'OPU') THEN
                iddepartamento     := 128;
        END IF;

            --CONSULTA MOTIVO SI EXISTE
        BEGIN
                IF motivonivel4 IS NOT NULL THEN
                SELECT ID_MOTIVO INTO idmotivonuevo  FROM DB_GENERAL.ADMI_MOTIVO WHERE UPPER(NOMBRE_MOTIVO) = UPPER(motivonivel4);
                
                END IF;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN

                    idmotivonuevo := DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL;
                    
                    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
                        ID_MOTIVO,
                        RELACION_SISTEMA_ID,
                        NOMBRE_MOTIVO, 
                        ESTADO, 
                        USR_CREACION,
                        FE_CREACION,
                        USR_ULT_MOD,
                        FE_ULT_MOD
                    ) 
                    VALUES (idmotivonuevo,idrelacionsistema, INITCAP(motivonivel4),'Activo','amontero',SYSDATE,'amontero',SYSDATE);
                    
                    COMMIT;
                    
                    totalcreamotivo := totalcreamotivo + 1;
        END;

        IF idmotivonuevo > 0 THEN
           
            INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
            descripcionparametro||' '||motivonivel5,UPPER(motivonivel1),UPPER(motivonivel2),motivonivel3,idmotivonuevo,
            'Activo','amontero',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,NULL);
            
            transInsert := transInsert +1;

        END IF;

      
   END LOOP;

dbms_output.put_line (
                                          ' [TOTAL A REGISTRAR]=> '    || total ||
                                          ' [TOTAL MOTIVOS CREADOS]=> '|| totalcreamotivo  ||
                                          ' [TOTAL REGISTRADAS]=> '    || transInsert

);
   DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'MIGRACION ARBOL MOTIVOS DE CATEGORIAS DE TAREAS',
                                          ' [TOTAL A REGISTRAR]=> '    || total ||
                                          ' [TOTAL MOTIVOS CREADOS]=> '|| totalcreamotivo  ||
                                          ' [TOTAL REGISTRADAS]=> '    || transInsert,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                          '127.0.0.1')
                                       );

  --INGRESA MOTIVOS DE TAREAS NUEVAS DE RADIO
  --transInsert := 0;
   totalmotivosnuevos      := motivostareasnuevas.count;
   totalcreamotivo         := 0;
   transInsert             := 0;
   FOR i in 1 .. totalmotivosnuevos LOOP
      countn1:=0;
      countn2:=0;
      idmotivonuevo:=0;
      motivonivel1 := SUBSTR(motivostareasnuevas(i),0,(INSTR(motivostareasnuevas(i),'|')-1)); 
      motivonivel2 := SUBSTR(motivostareasnuevas(i), (INSTR(motivostareasnuevas(i),'|')+1), LENGTH(motivostareasnuevas(i)) );
      motivonivel3 := SUBSTR(motivonivel2,(INSTR(motivonivel2,'|')+1),LENGTH(motivonivel2)); 
      motivonivel2 := SUBSTR(motivonivel2,0,(INSTR(motivonivel2,'|')-1));
      motivonivel4 := SUBSTR(motivonivel3,INSTR(motivonivel3,'|')+1,LENGTH(motivonivel3)); 
      motivonivel3 := SUBSTR(motivonivel3,0,(INSTR(motivonivel3,'|')-1)); 
      motivonivel5 := SUBSTR(motivonivel4,INSTR(motivonivel4,'|')+1,LENGTH(motivonivel4));
      motivonivel4 := SUBSTR(motivonivel4,0,(INSTR(motivonivel4,'|')-1));

      IF (TRIM(motivonivel5) = 'RADIO') THEN
        iddepartamento     := 124;
      ELSIF(TRIM(motivonivel5) = 'ELECTRICO') THEN
        iddepartamento     := 129;
      ELSIF(TRIM(motivonivel5) = 'FO') THEN
        iddepartamento     := 116;
      ELSIF(TRIM(motivonivel5) = 'OPU') THEN
        iddepartamento     := 128;
      END IF;

      --CONSULTA MOTIVO SI EXISTE
      BEGIN
            IF motivonivel4 IS NOT NULL THEN
              SELECT ID_MOTIVO INTO idmotivonuevo  FROM DB_GENERAL.ADMI_MOTIVO WHERE UPPER(NOMBRE_MOTIVO) = UPPER(motivonivel4);
            
            END IF;
      EXCEPTION
          WHEN NO_DATA_FOUND THEN

                    idmotivonuevo := DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL;
                    
                    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
                        ID_MOTIVO,
                        RELACION_SISTEMA_ID,
                        NOMBRE_MOTIVO, 
                        ESTADO, 
                        USR_CREACION,
                        FE_CREACION,
                        USR_ULT_MOD,
                        FE_ULT_MOD
                    ) 
                    VALUES (idmotivonuevo,idrelacionsistema,INITCAP(motivonivel4),'Activo','amontero',SYSDATE,'amontero',SYSDATE);

                    COMMIT;
                    totalcreamotivo := totalcreamotivo + 1;
      END;

      IF idmotivonuevo > 0 THEN
          SELECT NVL(MAX(ID_TAREA),0) INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA 
          WHERE UPPER (NOMBRE_TAREA) = UPPER(motivonivel3) AND ESTADO = 'Activo';

          IF idtareanueva > 0 THEN

              INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
              (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
              descripcionparametro||' '||motivonivel5,UPPER(motivonivel1),UPPER(motivonivel2),idtareanueva,idmotivonuevo,
              'Activo','amontero',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,NULL);

              transInsert := transInsert +1;
          END IF;
      END IF;

   END LOOP;

dbms_output.put_line (
                                          ' [TOTAL A REGISTRAR]=> '    || totalmotivosnuevos ||
                                          ' [TOTAL MOTIVOS CREADOS]=> '|| totalcreamotivo  ||
                                          ' [TOTAL REGISTRADAS]=> '    || transInsert

);

   DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'MIGRACION ARBOL MOTIVOS DE CATEGORIAS DE TAREAS',
                                          ' [TOTAL A REGISTRAR]=> '    || totalmotivosnuevos ||
                                          ' [TOTAL MOTIVOS CREADOS]=> '|| totalcreamotivo  ||
                                          ' [TOTAL REGISTRADAS]=> '    || transInsert,
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
                                        'MIGRACION ARBOL MOTIVOS DE CATEGORIAS DE TAREAS',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/
