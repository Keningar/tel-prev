--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO FIBRA
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

   iddepartamento          VARCHAR2(100) := '116';
   descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
   descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';

  imagen VARCHAR2(50);

   Le_Exception                EXCEPTION;
   Lv_MensajeError             VARCHAR2(4000);

BEGIN 

   niveltres := niveltresarray(
      'CONSTRUCCIÓN|RUTAS|Replanteo|3870|N',
      'CONSTRUCCIÓN|RUTAS|Corrección de ruta|3888|S',
      'CONSTRUCCIÓN|RUTAS|Entrega de rutas Telefónica|3892|N',
      'CONSTRUCCIÓN|RUTAS|Habilitación de segundo splitter L1|6708|S',
      'CONSTRUCCIÓN|RUTAS|Habilitación de segundo splitter L2|6709|S',
      'CONSTRUCCIÓN|RUTAS|Tendido de fibra|3885|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de ODF Reflejo 192H|6695|S',
      'CONSTRUCCIÓN|RUTAS|Sangrado y fusión CDD / CT / CP|6696|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de ODF Reflejo 96H|6698|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de Manga 144H|6699|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de Manga 48H|6700|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de Manga 24H|6703|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de ODF 144H|6704|S',
      'CONSTRUCCIÓN|RUTAS|Instalación de cajas CDD / CT|6705|S',
      'CONSTRUCCIÓN|RUTAS|Fusión de Pedestal FTTH|6706|S',
      'CONSTRUCCIÓN|RUTAS|Instalación de ODF Splitter|6710|S',
      'CONSTRUCCIÓN|RUTAS|Conexión entre ODFS de Rutas|6716|S',
      'CONSTRUCCIÓN|RUTAS|Inspección en Nodo|6692|N',
      'CONSTRUCCIÓN|RUTAS|Fusión de ODF 24H|6694|S',
      'CONSTRUCCIÓN|RUTAS|Elaboración de TSS Telefónica|6697|N',
      'CONSTRUCCIÓN|RUTAS|Toma de Potencia|6701|N',
      'CONSTRUCCIÓN|RUTAS|Elaboración de Estudio de Ingeniería para RBS|6702|N',
      'CONSTRUCCIÓN|RUTAS|Ingreso de fibra hacia el Nodo|6707|N',
      'CONSTRUCCIÓN|RUTAS|Medición / Certificación de hilos F.O.|6711|N',
      'CONSTRUCCIÓN|RUTAS|Inventario de caja CDD / CT / CP|6712|N',
      'CONSTRUCCIÓN|RUTAS|Retiro de patch cord en Nodo|6713|N',
      'CONSTRUCCIÓN|RUTAS|Activación de hilos oscuros|6714|N',
      'CONSTRUCCIÓN|RUTAS|Certificación de Ruta|6715|N',
      'CONSTRUCCIÓN|RUTAS|Orden de Trabajo|6719|N',
      'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|Corrección de ruta|3888|S',
      'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|Entrega de rutas Telefónica|3892|N',
      'CONSTRUCCIÓN|RUTAS ABIERTAS / FTTH / MIGRACIÓN / HILOS OSCUROS|Tendido de fibra|3885|S',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Inspección de Urbanización|3893|N',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Inspección de Centro Comercial|3895|N',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Sangrado y Fusión de Minipostes|3897|S',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Fusión de ODF 48H|6689|S',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Habilitación L1|6691|S',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Inspección de Edificio|6688|N',
      'CONSTRUCCIÓN|URBANIZACIONES-EDIFICIOS-CENTROS COMERCIALES|Corrección de etiquetas|6690|S',
      'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|Inspección de Urbanización|3893|N',
      'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|Inspección de Centro Comercial|3895|N',
      'CONSTRUCCIÓN|URBANIZACIÓN / CENTRO COMERCIAL / EDIFICIOS / REINGENERÍA|Sangrado y Fusión de Minipostes|3897|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Etiquetado|3921|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Colocación de herrajes|3923|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Levantamiento de mangas y reservas|3926|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Cambio de equipo|2075|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Etiquetado|2384|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Recorrido Interurbano|3917|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Desbroce de maleza|3918|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Poda de árboles|3919|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Transferencia de cables|3922|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Instalación de retenidas en postes|3930|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Toma de coordenadas de postes y mangas|3970|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Toma de Lecturas|4012|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Cambio de tramo|6332|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Corte de F.O.|6748|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Corrección de atenuaciones|3925|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Cambio de tramo|3924|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Templado de F.O.|6742|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Retiro de F.O. desactivada|6744|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Cambio de tipo de manga|6746|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Tarea programada|6747|S',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Asignación de hilos|6741|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Recuperación de hilos|6743|N',
      'INTERURBANO|MANTENIMIENTO DE RUTAS|Aumento de capacidad de hilos|6745|N',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Instalación de Clientes|6761|S',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Instalación de Equipos|6758|S',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Inspección de avance de obras|6756|N',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Toma de lecturas por ODF|6757|N',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Elaboración de Informe|6759|N',
      'LAN PROYECTOS|LAN / CENTRO COMERCIAL|Verificaciones de ducterías|6760|N',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Fase de Tendido de F.O.|6723|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Fase de Migración en sector Regenerado|6724|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Reubicación de caja CDD|6728|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Reubicación de Equipos|6736|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Fusión de Mini ODF|6721|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Fase de Desmontaje|6722|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Reubicación de F.O.|6725|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Cambio de línea Pon|6729|S',
      'REGENERACIÓN|INSTALACIONES-MIGRACIÓN-SOTERRAMIENTO|Fusión de Mini Manga|6730|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Empaquetado de F.O.|6731|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Identificación y etiquetado de F.O.|6733|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Instalación de Bajante|6734|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Ordenamiento de cableado|6737|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Fusión de Pedestal de Acceso|6739|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Fase de Levantamiento de Información|6720|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Retiro de Equipos|6727|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Reunión con Entes Reguladores|6732|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Revisión de infraestructura de Soterramiento|6735|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Seguimiento poste a poste de fibras|6738|S',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Inventario de ODF|6740|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Retiro de Equipos|3519|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Verificaciones de ductería|3933|N',
      'REGENERACIÓN|MIGRACIÓN / SOTERRAMIENTO / ADOSAMIENTO|Documentación GIS|3935|N'
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

      IF (UPPER(tareanivel1) = 'CONSTRUCCIÓN' OR UPPER(tareanivel1) = 'CONSTRUCCION') THEN
        imagen := 'construccion.png';
      ELSIF (UPPER(tareanivel1) = 'INTERURBANO') THEN
        imagen := 'interurbano.png';
      ELSIF (UPPER(tareanivel1) = 'LAN PROYECTOS') THEN
        imagen := 'fibra.png';
      ELSIF (UPPER(tareanivel1) = 'REGENERACIÓN' OR UPPER(tareanivel1) = 'REGENERACION') THEN
        imagen := 'regeneracion.png';
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
                                                   'MIGRACION ARBOL TAREAS - FIBRA',
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
      WHERE UPPER(DESCRIPCION) = UPPER(descripcionparametro) 
      AND translate(UPPER(VALOR1),'áéíóúÁÉÍÓÚ','aeiouAEIOU')= translate(UPPER(tareanivel1),'áéíóúÁÉÍÓÚ','aeiouAEIOU') 
      AND translate(UPPER(VALOR2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = translate(UPPER(tareanivel2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') 
      AND VALOR3 = TO_CHAR(idtareanueva) AND ESTADO = 'Activo';
     
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
                                         'MIGRACION ARBOL TAREAS - FIBRA',
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
                                        'MIGRACION ARBOL TAREAS - FIBRA',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/
