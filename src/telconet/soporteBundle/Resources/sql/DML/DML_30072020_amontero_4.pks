--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO OPU
DECLARE 
   type niveltresarray IS VARRAY(500) OF VARCHAR2(1000); 
   tareanivel1 VARCHAR2(200);
   tareanivel2 VARCHAR2(200);
   tareanivel3 VARCHAR2(200);
   tareanivel4 VARCHAR2(200);
   tareanivel5 VARCHAR2(200);
   tareanivel6 VARCHAR2(200);
   tareanivel7 VARCHAR2(200);
   countn1 NUMBER;
   countn2 NUMBER;

   idtareanueva NUMBER;
   idtareacaracteristica NUMBER;
   idcaracrequirematerial NUMBER := 1311;
   idcaracrequirerutafibra NUMBER := 1312;
   transInsert NUMBER := 0;
   totalExiste NUMBER := 0;
   totalTareasNuevas NUMBER := 0;
   niveltres niveltresarray; 
   requiereFibra VARCHAR2(1);
   total integer;

   iddepartamento          VARCHAR2(100) := '128';
   idproceso               VARCHAR2(20);
   descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
   descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';
   visiblemovil            VARCHAR2(1)   := 'S';

   imagen                  VARCHAR2(50);

   Le_Exception                EXCEPTION;
   Lv_MensajeError             VARCHAR2(4000);

BEGIN 

   niveltres := niveltresarray(
        'BACKBONE|ATENUACIÓN|Cambio de tarjetas SFP||S|N|N',
        'BACKBONE|ATENUACIÓN|Se arregla atenuacion en posteria||S|N|N',
        'BACKBONE|ATENUACIÓN|Reparar atenuacion enlace FO Telefonica|2630|S|N|N',
        'BACKBONE|ATENUACIÓN|Eliminar atenuación en enlace de FO de backbone|2650|S|N|N',
        'BACKBONE|CORTE FIBRA|Reparar enlace de FO backbone|2594|S|S|N',
        'BACKBONE|CORTE FIBRA|Reparar enlace FO Telefonica||S|S|N',
        'CLIENTE|MINI ODF/ROSETA|Cambiar pigtail FO en caja multimedia|2613|S|N|N',
        'CLIENTE|MINI ODF/ROSETA|Cambiar caja multimedia|2614|||',
        'CLIENTE|MINI ODF/ROSETA|Cambiar adaptador SC en caja multimedia|2615|||',
        'CLIENTE|MINI ODF/ROSETA|Reparar fusion de hilo de FO en caja multimedia|2616|S|N|N',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|Cambiar canal WiFi|2626|N|N|N',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|Subir plantilla de configuracion en CPE cliente|2649|N|N|N',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|Cambiar configuración en CPE cliente|2672|||',
        'CLIENTE|CONFIGURACIÓN EQUIPOS|CONFIGURACION DE CAMARA CCTV|3235|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar fuente de transceiver en cliente|2618|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar transceiver en cliente|2619|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar fuente de equipo CPE|2621|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar equipo WiFi|2627|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar fuente de equipo WiFi|2628|N|N|N',
        'CLIENTE|EQUIPOS|Realizar pruebas de saturacion directas|2646|N|N|N',
        'CLIENTE|EQUIPOS|Realizar pruebas de saturacion con equipos de cliente|2647|N|N|N',
        'CLIENTE|EQUIPOS|Cambiar CPE cliente|2671|N|N|N',
        'CLIENTE|EQUIPOS|Revisar conexiones en CPE cliente|2648|N|N|N',
        'CLIENTE|EQUIPOS|Reubicacion Equipos en Cliente|3262|S|S|N',
        'CLIENTE|EQUIPOS|Equipos del cliente||||',
        'CLIENTE|EQUIPOS|Reubicacion Equipos en Cliente Sin Fibra||N|N|N',
        'CLIENTE|ATENUACIÓN|Eliminar atenuacion en cliente||S|N|N',
        'CLIENTE|PATCHCORD|Cambiar patch cord de FO en cliente|2612|||',
        'CLIENTE|PATCHCORD|Cambiar patch cord UTP en cliente|2617|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Mal etiquetado|5000|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Patch cord mal pienado|5001|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Patch cord mal ponchado|5002|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Patch cord sin etiqueta|5006|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|No etiqueta acometida de FO|5010|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Trabajo mal realizado en caja BMX por instalacion|4739|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Trabajo mal realizado en caja BMX por soporte|4740|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Trabajo mal realizado por arreglo de caja BMX|4741|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Trabajo mal realizado en nodo instalacion cliente|4742|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Trabajo mal realizado en nodo por soporte|4746|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Patch cord mal pasado|4998|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Equipo mal ubicado|4999|S|S|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|No usa kit de acometida|5005|S|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|Fibra sujeta con mensajero|5007|N|N|N',
        'FISCALIZACIÓN|TRABAJOS MAL REALIZADOS|No cumple con arreglo y estandarizacion|5058|N|N|N',
        'INSTALACIÓN|INSPECCIÓN|Realizar inspección previa a trabajos|2673|N|N|N',
        'INSTALACIÓN|INSPECCIÓN|INSPECCIONES PARA UBICACION DE CAMARAS CCTV|3236|N|N|N',
        'INSTALACIÓN|INSPECCIÓN|Inspección de Urbanización|3893|N|N|N',
        'INSTALACIÓN|INSPECCIÓN|Inspección de Centro Comercial|3895|N|N|N',
        'INSTALACIÓN|INSPECCIÓN|Inspección de Edificio|3916|N|N|N',
        'INSTALACIÓN|INSTALACIÓN|Colocar Cajas para liberacion de conjunto||S|S|S',
        'INSTALACIÓN|INSTALACIÓN|Tendido de fibra||S|S|S',
        'INSTALACIÓN|INSTALACIÓN|Colocar Caja y Splitter||S|N|N',
        'INSTALACIÓN|INSTALACIÓN|INSTALACION DE CAMARAS CCTV|3233|S|N|N',
        'INSTALACIÓN|MIGRACIÓN CLIENTE|Coordinacion Migracion a Recableado / Nodo|3513|S|S|S',
        'MANTENIMIENTO|PEDESTAL|Arreglo de Pedestal||S|N|N',
        'MANTENIMIENTO|PEDESTAL|Mantenimiento Preventivo Pedestal||||',
        'MANTENIMIENTO|CAJA BMX/FTTH|Arreglo de caja MPLS|3220|S|N|N',
        'MANTENIMIENTO|CAJA BMX/FTTH|Mantenimiento Preventivo Caja MPLS|4678|||',
        'MANTENIMIENTO|CAJA BMX/FTTH|Colocar tapa en Caja BMX||S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|MANTENIMIENTO PREVENTIVO EQUIPOS TERMINALES EN CLIENTE|3227|S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|MANTENIMIENTO PREVENTIVO EQUIPOS TERMINALES EN NODO|3228|S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|Colocación de herrajes|3923|S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|Certificar Fibra Optica|5794|N|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|MANTENIMIENTO PREVENTIVO ENLACE DE BACKBONE|3224|S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|MANTENIMIENTO PREVENTIVO ENLACE DE DISTRIBUCION|3225|S|S|N',
        'MANTENIMIENTO|ENLACES URBANOS|MANTENIMIENTO PREVENTIVO ENLACE DE FIBRA CLIENTE|3226|S|N|N',
        'MANTENIMIENTO|ENLACES URBANOS|Recuperación de hilos|4040|||',
        'MANTENIMIENTO|TELEFÓNICA|Mantenimiento Preventivo Telefónica|3218|N|N|N',
        'NODO|EQUIPOS|Cambio de switch||S|N|N',
        'NODO|EQUIPOS|Cambio de interfaz|2076|N|N|N',
        'NODO|EQUIPOS|Daño en la interface LAN|2294|N|N|N',
        'NODO|EQUIPOS|Cambiar fuente de transceiver en nodo|2601|N|N|N',
        'NODO|EQUIPOS|Cambiar transceiver en nodo|2602|S|N|N',
        'NODO|EQUIPOS|Revision equipos en nodo||N|N|N',
        'NODO|EQUIPOS|Cambiar puerto del switch en nodo|2623|N|N|N',
        'NODO|ODF|Cambiar Splitter L1||S|N|N',
        'NODO|ODF|Reparar ODF en nodo|2598|||',
        'NODO|ODF|Cambiar adaptador SC en nodo|2600|S|N|N',
        'NODO|ODF|Cambiar pigtail FO de ODF en nodo|2655|||',
        'NODO|ODF|Revisar ODF en nodo|2632|N|N|N',
        'NODO|PATCHCORD|Cambiar patch cord FO en nodo|2651|||',
        'NODO|PATCHCORD|Cambiar patch cord UTP en nodo|2652|||',
        'RED DE DISTRIBUCIÓN|ATENUACION|Eliminar atenuación en enlace de FO de distribucion|2604|||',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Cambiar patch cord FO en caja de dispersion|2607|S|N|N',
        'RED DE DISTRIBUCION|CAJA BMX/FTTH|Cambiar adaptador SC en caja de dispersion|2608|S|N|N',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Cambiar pigtail FO en caja de dispersion|2609|||',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Cambio de Splitter|7489|S|N|N',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Reubicacion de Caja BMX|4991|S|N|N',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Reparar fusion de hilo de FO en red de distribucion|2660|S|N|N',
        'RED DE DISTRIBUCIÓN|CAJA BMX/FTTH|Habilitar linea de derivacion|4968|S|S|S',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|Migrar hilo por daño interno|2605|S|N|N',
        'RED DE DISTRIBUCIÓN|CORTE FIBRA|Reparar enlace de FO de distribución|2603|S|S|N',
        'RETIRO|EQUIPOS|Retirar equipos en cliente|2897|N|N|N',
        'RETIRO|EQUIPOS|Retirar equipos en Nodo|2898|N|N|N',
        'RETIRO|EQUIPOS|Verificacion de Series de equipos Nodo/Cliente|3251|N|N|N',
        'RETIRO|FIBRA|Desmontaje de Fibra Optica Cliente|3247|N|N|N',
        'ULTIMA MILLA|CORTE FIBRA|Reparar enlace de FO de acceso|2611|S|S|N',
        'ULTIMA MILLA|MINIMANGA|Reparar fusion de hilo de FO en red de acceso|2610|S|N|N',
        'ULTIMA MILLA|ATENUACION|Atenuacion por daño en la FO||S|S|S',
        'ULTIMA MILLA|ATENUACION|Atenuación en el tramo de FO||S|N|N',
        'ADMINISTRATIVO|OPU|No se desmonta fibra||N|N|N',
        'ADMINISTRATIVO|OPU|Registro de equipo en NAF||N|N|N',
        'ADMINISTRATIVO|OPU|Liberacion Edif/Conj/Urb/CC||N|N|N',
        'ADMINISTRATIVO|OPU|Regularizacion de FO||N|N|N',
        'ADMINISTRATIVO|OPU|Cambiar de Línea PON||||',
        'ADMINISTRATIVO|OPU|Regularizacion de FO retirada||N|N|N',
        'ADMINISTRATIVO|OPU|Configuración CPE TN||N|N|N',
        'ADMINISTRATIVO|OPU|Informe Telefónica||||',
        'ADMINISTRATIVO|OPU|Enlace operativo sin interveción técnica TN||N|N|N'
       ); 
   total := niveltres.count;
   idproceso := 628;
   --
   --
   FOR i in 1 .. total LOOP
      countn1:=0;
      countn2:=0;
      idtareanueva:=0;
      requiereFibra:='N';
      visiblemovil :='S';
      tareanivel1 := SUBSTR(niveltres(i),0,(INSTR(niveltres(i),'|')-1)); 
      tareanivel2 := SUBSTR(niveltres(i), (INSTR(niveltres(i),'|')+1), LENGTH(niveltres(i)) );
      tareanivel3 := SUBSTR(tareanivel2,(INSTR(tareanivel2,'|')+1),LENGTH(tareanivel2)); 
      tareanivel2 := SUBSTR(tareanivel2,0,(INSTR(tareanivel2,'|')-1));
      tareanivel4 := SUBSTR(tareanivel3,INSTR(tareanivel3,'|')+1,LENGTH(tareanivel3)); 
      tareanivel3 := SUBSTR(tareanivel3,0,(INSTR(tareanivel3,'|')-1)); 
      tareanivel5 := SUBSTR(tareanivel4,INSTR(tareanivel4,'|')+1,LENGTH(tareanivel4));
      tareanivel4 := SUBSTR(tareanivel4,0,(INSTR(tareanivel4,'|')-1));
      tareanivel6 := SUBSTR(tareanivel5,INSTR(tareanivel5,'|')+1,LENGTH(tareanivel5));
      tareanivel5 := SUBSTR(tareanivel5,0,(INSTR(tareanivel5,'|')-1));
      tareanivel7 := SUBSTR(tareanivel6,INSTR(tareanivel6,'|')+1,LENGTH(tareanivel6));
      tareanivel6 := SUBSTR(tareanivel6,0,(INSTR(tareanivel6,'|')-1)); 

      IF (UPPER(tareanivel1) = 'MANTENIMIENTO') THEN
        imagen := 'mantenimiento.png';
      ELSIF (UPPER(tareanivel1) = 'ADMINISTRATIVO') THEN
        imagen       := 'mantenimiento.png';
        visiblemovil := 'N';
      ELSIF (UPPER(tareanivel1) = 'BACKBONE') THEN
        imagen := 'backbone.png';
      ELSIF (UPPER(tareanivel1) = 'CLIENTE') THEN
        imagen := 'cliente.png';
      ELSIF (UPPER(tareanivel1) = 'FISCALIZACIÓN' OR UPPER(tareanivel1) = 'FISCALIZACION') THEN
        imagen := 'fiscalizacion.png';
      ELSIF (UPPER(tareanivel1) = 'INSTALACIÓN' OR UPPER(tareanivel1) = 'INSTALACION') THEN
        imagen := 'instalacion.png';
      ELSIF (UPPER(tareanivel1) = 'NODO' OR UPPER(tareanivel1) = 'NODO') THEN
        imagen := 'nodo.png';
      ELSIF (UPPER(tareanivel1) = 'RED DE DISTRIBUCIÓN' OR UPPER(tareanivel1) = 'RED DE DISTRIBUCION') THEN
        imagen := 'redDistribucion.png';
      ELSIF (UPPER(tareanivel1) = 'RETIRO') THEN
        imagen := 'retiro.png';
      ELSIF (UPPER(tareanivel1) = 'ULTIMA MILLA') THEN
        imagen := 'ultimaMilla.png';
      END IF;
      --CONSULTA TAREA SI EXISTE
      BEGIN
          --SI TIENE requiereFibra
          IF tareanivel6 IS NOT NULL AND tareanivel6 = 'S' THEN
              requiereFibra := 'S';
          END IF;

          IF tareanivel4 IS NOT NULL THEN
                  SELECT ID_TAREA INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA WHERE ID_TAREA = tareanivel4;
          ELSE

                  SELECT NVL(MAX(ID_TAREA),0) INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA 
                  WHERE translate(UPPER(NOMBRE_TAREA),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = translate(UPPER(tareanivel3),'áéíóúÁÉÍÓÚ','aeiouAEIOU') 
                  AND ESTADO='Activo';

                  IF idtareanueva = 0 THEN
                    idtareanueva := DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL;




                    INSERT INTO DB_SOPORTE.ADMI_TAREA (ID_TAREA,PROCESO_ID,ROL_AUTORIZA_ID,TAREA_ANTERIOR_ID,
                                                      TAREA_SIGUIENTE_ID,PESO,ES_APROBADA,NOMBRE_TAREA,
                                                      DESCRIPCION_TAREA,TIEMPO_MAX,UNIDAD_MEDIDA_TIEMPO,COSTO,
                                                      PRECIO_PROMEDIO,ESTADO,USR_CREACION,FE_CREACION,
                                                      USR_ULT_MOD,FE_ULT_MOD,AUTOMATICA_WS,
                                                      CATEGORIA_TAREA_ID,PRIORIDAD,REQUIERE_FIBRA,VISUALIZAR_MOVIL) 
                    VALUES (idtareanueva,idproceso,null,null,null,'1','0',tareanivel3,tareanivel3,'3',
                            'HORAS','0','0','Activo','amontero',sysdate,
                            'amontero',sysdate,null,null,null,requiereFibra,visiblemovil);

                    COMMIT;
 
                    totalTareasNuevas := totalTareasNuevas + 1;

                  END IF;

          END IF;

          --SI TIENE requiereFibra ACTUALIZA
          IF idtareanueva IS NOT NULL THEN
              UPDATE DB_SOPORTE.ADMI_TAREA SET REQUIERE_FIBRA = requiereFibra, VISUALIZAR_MOVIL=visiblemovil WHERE ID_TAREA = idtareanueva;
          END IF;

          --SI TIENE requiereMaterial
          IF tareanivel5 IS NOT NULL AND tareanivel5 = 'S' THEN

             SELECT NVL(MAX(ID_TAREA_CARACTERISTICA),0) INTO idtareacaracteristica FROM DB_SOPORTE.INFO_TAREA_CARACTERISTICA
                    WHERE TAREA_ID = idtareanueva AND CARACTERISTICA_ID = idcaracrequirematerial  AND ESTADO= 'Activo';

              IF idtareacaracteristica <= 0 THEN

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

          END IF;

          --SI TIENE requiereRutaFibra
          IF tareanivel7 IS NOT NULL AND tareanivel7 = 'S' THEN


             SELECT NVL(MAX(ID_TAREA_CARACTERISTICA),0) INTO idtareacaracteristica  FROM DB_SOPORTE.INFO_TAREA_CARACTERISTICA
                    WHERE TAREA_ID = idtareanueva AND CARACTERISTICA_ID = idcaracrequirerutafibra;

              IF idtareacaracteristica <= 0 THEN

                  INSERT INTO DB_SOPORTE.INFO_TAREA_CARACTERISTICA VALUES(
                      DB_SOPORTE.SEQ_INFO_TAREA_CARACTERISTICA.NEXTVAL,
                      idtareanueva,
                      NULL,
                      idcaracrequirerutafibra,
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

          END IF;

      EXCEPTION
          WHEN OTHERS THEN
            Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                   'MIGRACION ARBOL TAREAS - RADIO',
                                                   '[ERROR AL CREAR TAREA EN DB_SOPORTE.ADMI_TAREA] => '||Lv_MensajeError,
                                                   NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                                   SYSDATE,
                                                   NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                                   '127.0.0.1')
                                                );
      END;

      --CONSULTA SI EXISTE YA EN EL ARBOL
      SELECT COUNT(ID_PARAMETRO_DET) INTO countn1 FROM DB_GENERAL.ADMI_PARAMETRO_DET 
      WHERE UPPER(DESCRIPCION) = UPPER(descripcionparametro) AND translate(UPPER(VALOR1),'áéíóúÁÉÍÓÚ','aeiouAEIOU')= translate(UPPER(tareanivel1),'áéíóúÁÉÍÓÚ','aeiouAEIOU') 
      AND translate(UPPER(VALOR2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = translate(UPPER(tareanivel2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') AND VALOR3 = TO_CHAR(idtareanueva) AND ESTADO = 'Activo';

      IF countn1 <= 0 THEN

        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
        descripcionparametro,UPPER(tareanivel1),UPPER(tareanivel2),idtareanueva,
        imagen,'Activo','amontero',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,
        'VALOR1=> NIVEL 1, VALOR2 => NIVEL 2, VALOR3 => NIVEL 3 (ID TAREA)');
  
        transInsert := transInsert +1;
      ELSE
        totalExiste := totalExiste + 1;
      END IF;

   END LOOP;

    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'MIGRACION ARBOL TAREAS - OPU',
                                          '[TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste 
                                          ||' [TOTAL TAREAS NUEVAS]=> '||totalTareasNuevas 
                                          ||' [TOTAL REGISTRADAS]=> '||transInsert,
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
                                        'MIGRACION ARBOL TAREAS - OPU',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/
