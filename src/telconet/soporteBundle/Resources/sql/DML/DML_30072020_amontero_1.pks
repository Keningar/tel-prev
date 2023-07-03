--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO RADIO
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
   totalTareasNuevas NUMBER := 0;
   niveltres niveltresarray; 

   total integer;

   iddepartamento          VARCHAR2(100) := '124';
   idproceso               VARCHAR2(20);
   descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
   descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';

   imagen                  VARCHAR2(50);

   Le_Exception                EXCEPTION;
   Lv_MensajeError             VARCHAR2(4000);

BEGIN 

   niveltres := niveltresarray(
          'Problema de UM RE|Equipo de Radio dañado en el nodo|Cambio de equipo de Radio||S',
          'Problema de UM RE|Equipo de Radio dañado en el nodo|Cambio de ultima milla cliente.|5327|S',
          'Problema de UM RE|Saturacion de Capacidad del AP|Cambio de ultima milla cliente.|5327|N',
          'Problema de UM RE|Saturacion de Capacidad del AP|Control de Ancho de Banda en el radio||N',
          'Problema de UM RE|Saturacion de Capacidad del AP|Control de Ancho de Banda en el CPE||N',
          'Problema de UM RE|Cable dañado en nodo|Cambiar patch cord UTP|2629|S',
          'Problema de UM RE|Interferencia de frecuencia|Configuración RF|5303|N',
          'Problema de UM RE|Interferencia de frecuencia|Cambio de ultima milla cliente.|5327|N',
          'Problema de UM RE|Antena Desalineada en el nodo|Alineacion de antena||N',
          'Problema de UM RE|Obstruccion de linea de vista en el nodo|Cambio de ultima milla cliente.|5327|N',
          'Problema de UM RE|Obstruccion de linea de vista en el nodo|Reubicacion de equipos y antena|4286|N',
          'Problema de UM RE|Obstruccion de linea de vista en el nodo|Incremento de torre||S',
          'Problema de UM RE|Obstruccion de linea de vista en el nodo|Eliminacion de la obstruccion a la linea de vista||S',
          'Problema de UM RE|PoE quemado en el nodo|Cambio de PoE||S',
          'Problema de UM RE|PoE Desconectado en el nodo|Conexión de PoE||N',
          'Problema de UM RE|Radio dañado en el cliente|Cambio de equipo de Radio||S',
          'Problema de UM RE|Configuracion de Radio en el cliente|Cambio de configuracion||N',
          'Problema de UM RE|Saturacion de ancho de banda contratado|Cambio de ultima milla cliente.|5327|N',
          'Problema de UM RE|Saturacion de ancho de banda contratado|Control de Ancho de Banda en el radio||N',
          'Problema de UM RE|Saturacion de ancho de banda contratado|Control de Ancho de Banda en el CPE||N',
          'Problema de UM RE|Cable UTP dañado en el cliente|Cambiar patch cord UTP|2629|S',
          'Problema de UM RE|Antena Desalineada en el cliente|Alineacion de antena||N',
          'Problema de UM RE|Obstruccion de linea de vista en el cliente|Cambio de ultima milla cliente.|5327|N',
          'Problema de UM RE|Obstruccion de linea de vista en el cliente|Incremento de torre||S',
          'Problema de UM RE|Obstruccion de linea de vista en el cliente|Reubicacion de equipos y antena|4286|S',
          'Problema de UM RE|Obstruccion de linea de vista en el cliente|Eliminacion de la obstruccion a la linea de vista||S',
          'Problema de UM RE|Desmontaje de Antena en el cliente|Reubicacion de equipos y antena|4286|S',
          'Problema de UM RE|PoE dañado en el cliente|Cambio de PoE||S',
          'Problema de UM RE|PoE desconectado en el cliente|Conexión de PoE||N',
          'Problema de UM RE|Desconexion de cables en el cliente|Conexión de cables UTP||N',
          'Problemas servicio WIFI|Equipo WIFI dañado|Cambio de Equipo WIFI||S',
          'Problemas servicio WIFI|Problemas con el portal cautivo|Configuración de Servidores|5281|N',
          'Problemas servicio WIFI|Problemas con el portal cautivo|Configuracion en la controladora WIFI||N',
          'Problemas servicio WIFI|Equipo WIFI apagado|Conexión a la red electrica del equipo WIFI||N',
          'Problemas servicio WIFI|Problema de contraseña WIFI|Cambio de Clave|5299|N',
          'Problemas servicio WIFI|Error de Activacion en la controladora WIFI|Aprovisionamiento de AP|5298|N',
          'Problemas servicio WIFI|Cobertura red WIFI|Se requiere aumentar el numero de equipos WIFI||N',
          'Problemas servicio WIFI|Cobertura red WIFI|Aumento de potencia de tx en AP WiFI||N',
          'Problemas servicio WIFI|Cobertura red WIFI|Reubicacion de AP WIFI||N',
          'Problemas servicio WIFI|Daño de UM para red WIFI|Arreglo de UM para red WIFI||N',
          'Problemas servicio WIFI|Falla en la configuracion logica en el switch|Configuracion logica en el switch||N',
          'Problemas servicio WIFI|Falla en la configuracion Logica en el CPE|Configuracion logica en el CPE||N',
          'Problemas servicio WIFI|Cliente desconecta los equipos|Conexión de los equipos||N',
          'Inspección|Inspección para determinar factibilidad de RADIO|TAREA DE RADIOENLACE - APROBAR PREFACTIBILIDAD|3522|N',
          'Inspección|Inspección para determinar factibilidad de RADIO|Se rechaza factibilidad||N',
          'Inspección|Inspeccion cobertura WIFI|Inspección realizada|5294|N',
          'Inspección|Inspeccion cobertura WIFI|Diseño de Cobertura|5295|N',
          'Mantenimiento|Nodo de RADIO|Cambiar patch cord UTP|2629|S',
          'Mantenimiento|Nodo de RADIO|Cambio de equipo de Radio||S',
          'Mantenimiento|Nodo de RADIO|Mantenimiento de Torre en mal estado|4001|S',
          'Mantenimiento|Nodo de RADIO|Cambio de configuracion||N',
          'Mantenimiento|WIFI|Cambiar patch cord UTP|2629|S',
          'Mantenimiento|WIFI|Cambio de Equipo WIFI||S',
          'Mantenimiento|WIFI|Revisión Cobertura Wifi|5296|N',
          'Mantenimiento|WIFI|Actualización puntos wifi en Google Earth|4114|N',
          'Retiro|RADIO/WIFI|Retiro de equipos por cancelación del servicio|3999|N',
          'Retiro|RADIO/WIFI|Retiro de equipos por migración de UM|4000|N'
       ); 
   total := niveltres.count;
   idproceso := DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL;
   --
   --CREAMOS PROCESO PARA TAREAS NUEVAS
    INSERT INTO DB_SOPORTE.ADMI_PROCESO VALUES(
      idproceso,
      883,
      'TAREAS DE RADIOENLACE - OTROS',
      'TAREAS DE RADIOENLACE TN',
      NULL,
      'Activo',
      'amontero',
      SYSDATE,
      'amontero',
      SYSDATE,
      'NO',
      'N'
    );
    COMMIT;
   --
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

      IF (UPPER(tareanivel1) = 'Problema de UM RE' OR UPPER(tareanivel1) = 'PROBLEMA DE UM RE') THEN
        imagen := 'ultimaMilla.png';
      ELSIF (UPPER(tareanivel1) = 'Problemas servicio WIFI' OR UPPER(tareanivel1) = 'PROBLEMAS SERVICIO WIFI') THEN
        imagen := 'wifi.png';
      ELSIF (UPPER(tareanivel1) = 'Inspección' OR UPPER(tareanivel1) = 'INSPECCIÓN' OR UPPER(tareanivel1) = 'INSPECCION') THEN
        imagen := 'inspeccion.png';
      ELSIF (tareanivel1 = 'Mantenimiento' OR UPPER(tareanivel1) = 'MANTENIMIENTO') THEN
        imagen := 'mantenimiento.png';
      ELSIF (tareanivel1 = 'Retiro' OR UPPER(tareanivel1) = 'RETIRO') THEN
        imagen := 'retiro.png';
      END IF;
      --CONSULTA TAREA SI EXISTE
      BEGIN
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
                            'amontero',sysdate,null,null,null,'N','S');


                    COMMIT;

                    totalTareasNuevas := totalTareasNuevas + 1;

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

      --SE ACTUALIZA QUE SE VISUALIZA EN MOVIL
      IF idtareanueva IS NOT NULL THEN
              UPDATE DB_SOPORTE.ADMI_TAREA SET VISUALIZAR_MOVIL='S' WHERE ID_TAREA = idtareanueva;
      END IF;

      --SI REQUIERE MATERIAL INSERTA LA CARACTERISTICA
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
 
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
        descripcionparametro,UPPER(tareanivel1),UPPER(tareanivel2),idtareanueva,
        imagen,'Activo','amontero',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,
        'VALOR1=> NIVEL 1, VALOR2 => NIVEL 2, VALOR3 => NIVEL 3 (ID TAREA)' );
        
        transInsert := transInsert +1;
      ELSE
        totalExiste := totalExiste + 1;
      END IF;

   END LOOP;

    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'MIGRACION ARBOL TAREAS - RADIO',
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
                                        'MIGRACION ARBOL TAREAS - RADIO',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/
