--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO PAC
DECLARE

    type itemarray IS VARRAY(500) OF VARCHAR(1000);
    type departamentosarray IS VARRAY(500) OF itemarray;

    departamentos departamentosarray;
    pac itemarray;

    items itemarray;

    tareanivel1 VARCHAR2(200);
    tareanivel2 VARCHAR2(200);
    tareanivel3 VARCHAR2(200);
    tareanivel4 VARCHAR2(200);
    tareanivel5 VARCHAR2(200);

    countn1 NUMBER;

    tareaCaracteristicaId NUMBER;

    idtareanueva NUMBER;
    idcaracrequirematerial NUMBER := 1311;
    transInsert NUMBER := 0;
    totalExiste NUMBER := 0;
    totalTareasNuevas NUMBER := 0;

    total integer;
    nombredepartamento      VARCHAR2(100);
    iddepartamento          VARCHAR2(100);
    idproceso               VARCHAR2(20);
    descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
    descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';

    imagen                  VARCHAR2(50);

    Le_Exception                EXCEPTION;
    Lv_MensajeError             VARCHAR2(4000);

    Lv_proceso_ont      VARCHAR2(100);
    Ln_proceso_ont      NUMBER;
BEGIN
    pac := itemarray(    
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Elaboración de Informe|6096|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Canaleta Eléctrica|6080|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Nivel II Equipos Climatización|6111|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Equipos Eléctricos|6101|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Rotación Equipos Climatización|6087|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Aspirado Piso de Cuartos y Oficinas Datacenter|6124|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Análisis PH y Nitritos Sistemas Agua Helada|6085|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Cambio Lampara Fluorescente / Led|6083|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Rondas Perímetro Interno y Externo Datacenter|6125|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Aterrizamiento de Equipos|6079|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Revisión Luces de Emergencia|6116|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Pintura Áreas Datacenter|6123|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Implementación Energía Eléctrica Un Rack con materiales|6072|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Revisión de Extintores|6117|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Energización Equipos Racks TI|6075|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Equipos Infraestructura|6104|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Instalación Equipo Climatización|6070|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Revisión/ Reparación Filtraciones Infraestructura Datacenter|6112|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Solicitud Material Bodega|6076|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Actualización CMDB|6094|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Actualización Sistemas de Gestión|6114|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Configuracion/Calibracion Equipos Climatizacion|6066|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Elaboración de IT (Informe Técnico)|6097|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Supervisión Llenado Tanque Almacenamiento de Combustible|6099|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Instalación Equipo Eléctrico|6069|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Cambio Baldosas en Areas Datacenter|6100|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Implementacion Energía Eléctrica Más de un Rack|6073|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Cisterna de Agua|6122|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Revisión Puertas del Datacenter|6088|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Supervisión de Actualización Sistemas de Gestión|6113|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Revisión Eléctrica Solicitada Por Clientes|6089|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Cambio Tubos Fluorescente / Led|6082|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Limpieza Profunda Piso Falso Areas Datacenter|6118|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Tendido Cableado Eléctrico|6077|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Implementacion Energía Eléctrica Un Rack|6071|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Reinicio Sistemas de Monitoreo|6095|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Configuración/Calibración Equipos Eléctricos|6067|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Supervisión de Trabajos Proveedores|6090|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Análisis de Agua Ablandadores|6084|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Implementación Energía Eléctrica Jaula|6074|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Registro de Gestión de Cambio|6093|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Limpieza Parqueadero y Perímetro Datacenter|6120|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Rotación Equipos Eléctricos|6086|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Etiquetado de Equipos|6115|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Fumigación de Maleza|6121|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Equipos Climatización|6103|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Anclaje de Rack|6081|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Actividades Varias|6127|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Supervisión Llenado Cisterna Agua|6098|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Actividades de Apoyo Bodega Datacenter|6126|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Nivel II Equipos Eléctricos|6110|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Correctivo Equipos Eléctricos|6092|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Preventivo Equipos Mecánicos|6102|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Conexión Eléctrica Alimentaciones/Equipos|6078|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Limpieza Area de Equipos|6119|',
            'PAC - EJECUCION DE TAREAS|GESTION DE CAMBIOS HOUSING|Mantenimiento Correctivo Equipos Climatización|6091|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Evento por Falla Climatización|6147|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Perdida Suministro Eléctrico|6148|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Revisión de Equipos por Alarma en Sistema Gestión|6145|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Incidente por Falla de Climatización|6151|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Registro de Evento|6149|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Evento por Falla Eléctrica|6146|',
            'PAC - EJECUCION DE TAREAS|GESTION PROBLEMA HOUSING|Incidente por Falla Eléctrica|6150|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas Equipos Climatizacion|6128|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Indisponibilidad de Sub-sistema eléctrico|6140|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Pruebas de Contingencias de Climatización|6143|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Pruebas de Contingencias Eléctricas|6142|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Indisponibilidad de Equipo de Climatización|6141|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Prueba de Sistema Contra incendios Agua|6138|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas Equipos Eléctricos|6129|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Registro Resumen Mantenimiento|6136|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas PDU|6131|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas DC|6132|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Registro de Rotación de Equipos|6135|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas Térmicas de Racks y Alimentaciones Eléctricas|6133|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Indisponibilidad de Sub-sistema de Climatización|6144|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Registro de Disponibilidad|6134|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Rondas Grupos Electrógenos|6130|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Indisponibilidad de equipo eléctrico|6139|',
            'PAC - EJECUCION DE TAREAS|GESTIÓN DISPONIBILIDAD HOUSING|Registro Paso de Turno|6137|',
            'PAC - FACTIBILIDAD|GESTION CAPACIDAD HOUSING|Registro Capacidad Electica y AACC|6152|',
            'PAC - FACTIBILIDAD|GESTION CAPACIDAD HOUSING|Toma de Lectura de Consumo de Equipos|6156|',
            'PAC - FACTIBILIDAD|GESTION CAPACIDAD HOUSING|Registro de Consumo de Cliente|6153|',
            'PAC - FACTIBILIDAD|GESTION CAPACIDAD HOUSING|Registro Capacidad en Sala Principal|6154|',
            'PAC - FACTIBILIDAD|GESTION CAPACIDAD HOUSING|Registro Inventario Equipo|6155|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Análisis de Proformas|6157|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración / Actualización Instructivos|6164|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración de Formato de Registro de Cambio RFC|6162|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración / Actualización Diagramas - Formatos|6160|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración de Cronograma de Mantenimiento|6163|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración / Actualización Procedimientos|6159|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Elaboración de Plan de Trabajo|6161|',
            'PAC - PLANIFICACION DE TAREAS/MANTENIMIENTOS|PLANIFICACION DE CAMBIOS HOUSING|Reuniones / Capacitaciones|6158|'
       );
    departamentos := departamentosarray(pac);

    FOR d in 1 .. departamentos.count LOOP

        items             := departamentos(d);
        total             := items.count;
        totalTareasNuevas := 0;
        transInsert       := 0;
        totalExiste       := 0;

        IF(d = 1) THEN
            nombredepartamento := 'PAC'; --Data Center Pac
            iddepartamento     := 825;
        END IF;

        FOR i in 1 .. total LOOP
            countn1:=0;
            idtareanueva:=0;

            tareanivel1 := SUBSTR(items(i),0,(INSTR(items(i),'|')-1)); 
            tareanivel2 := SUBSTR(items(i), (INSTR(items(i),'|')+1), LENGTH(items(i)) );
            tareanivel3 := SUBSTR(tareanivel2,(INSTR(tareanivel2,'|')+1),LENGTH(tareanivel2)); 
            tareanivel2 := SUBSTR(tareanivel2,0,(INSTR(tareanivel2,'|')-1));
            tareanivel4 := SUBSTR(tareanivel3,INSTR(tareanivel3,'|')+1,LENGTH(tareanivel3)); 
            tareanivel3 := SUBSTR(tareanivel3,0,(INSTR(tareanivel3,'|')-1)); 
            tareanivel5 := SUBSTR(tareanivel4,INSTR(tareanivel4,'|')+1,LENGTH(tareanivel4));
            tareanivel4 := SUBSTR(tareanivel4,0,(INSTR(tareanivel4,'|')-1));

            --CONSULTA TAREA SI EXISTE
            BEGIN
                IF tareanivel4 IS NOT NULL THEN
                    --BUSCA TAREA POR ID
                    SELECT ID_TAREA INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA WHERE ID_TAREA = TRIM(tareanivel4);
                ELSE
                    --BUSCA TAREA POR EL NOMBRE
                    SELECT NVL(MAX(ID_TAREA),0) INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA 
                    WHERE translate(UPPER(NOMBRE_TAREA),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = TRIM(translate(UPPER(tareanivel3),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) 
                    AND ESTADO='Activo';

                    --SI NO ENCONTRO POR NOMBRE ENTONCES LA CREA
                    IF idtareanueva = 0 THEN
                        idtareanueva := DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL;

                        INSERT INTO DB_SOPORTE.ADMI_TAREA (ID_TAREA,PROCESO_ID,ROL_AUTORIZA_ID,TAREA_ANTERIOR_ID,
                                                        TAREA_SIGUIENTE_ID,PESO,ES_APROBADA,NOMBRE_TAREA,
                                                        DESCRIPCION_TAREA,TIEMPO_MAX,UNIDAD_MEDIDA_TIEMPO,COSTO,
                                                        PRECIO_PROMEDIO,ESTADO,USR_CREACION,FE_CREACION,
                                                        USR_ULT_MOD,FE_ULT_MOD,AUTOMATICA_WS,
                                                        CATEGORIA_TAREA_ID,PRIORIDAD,REQUIERE_FIBRA,VISUALIZAR_MOVIL) 
                        VALUES (idtareanueva,idproceso,null,null,null,'1','0',TRIM(UPPER(tareanivel3)),TRIM(UPPER(tareanivel3)),'3',
                                'HORAS','0','0','Activo','jobedon',sysdate,
                                'jobedon',sysdate,null,null,null,'N','S');

                        COMMIT;

                        totalTareasNuevas := totalTareasNuevas + 1;

                    END IF;

                END IF;

            EXCEPTION
                WHEN OTHERS THEN
                    Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
                    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                        'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                                        '[ERROR AL CREAR TAREA EN DB_SOPORTE.ADMI_TAREA] => '||Lv_MensajeError,
                                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                                        SYSDATE,
                                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                                        '127.0.0.1')
                                                        );
            END;

            --SE ACTUALIZA QUE SE VISUALIZA EN MOVIL
            IF idtareanueva IS NOT NULL THEN
                    UPDATE DB_SOPORTE.ADMI_TAREA SET VISUALIZAR_MOVIL='N' WHERE ID_TAREA = idtareanueva;
            END IF;

            --CONSULTA SI EXISTE YA EN EL ARBOL
            SELECT COUNT(ID_PARAMETRO_DET) INTO countn1 FROM DB_GENERAL.ADMI_PARAMETRO_DET 
            WHERE UPPER(DESCRIPCION) = UPPER(descripcionparametro) 
            AND translate(UPPER(VALOR1),'áéíóúÁÉÍÓÚ','aeiouAEIOU')= TRIM(translate(UPPER(tareanivel1),'áéíóúÁÉÍÓÚ','aeiouAEIOU'))
            AND translate(UPPER(VALOR2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = TRIM(translate(UPPER(tareanivel2),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) 
            AND VALOR3 = TO_CHAR(idtareanueva) AND ESTADO = 'Activo';

            IF countn1 <= 0 THEN

                INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
                descripcionparametro,TRIM(UPPER(tareanivel1)),TRIM(UPPER(tareanivel2)),idtareanueva,
                imagen,'Activo','jobedon',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,
                'VALOR1=> NIVEL 1, VALOR2 => NIVEL 2, VALOR3 => NIVEL 3 (ID TAREA)' );

                transInsert := transInsert +1;
            ELSE
        DBMS_OUTPUT.PUT_LINE('[NIVEL1]=>'||TRIM(UPPER(tareanivel1))||' [NIVEL2]=>'||TRIM(UPPER(tareanivel2))||' [NIVEL3]=>'||TRIM(UPPER(tareanivel3)));

                totalExiste := totalExiste + 1;
            END IF;

        END LOOP;

        DBMS_OUTPUT.PUT_LINE('[DEPARTAMENTO]=>'||nombredepartamento||' [TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste 
                                                    ||' [TOTAL TAREAS NUEVAS]=> '||totalTareasNuevas 
                                                    ||' [TOTAL REGISTRADAS]=> '||transInsert);

        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                            'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                            '[DEPARTAMENTO]=>'||nombredepartamento||
                                            ' [TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste 
                                            ||' [TOTAL TAREAS NUEVAS]=> '||totalTareasNuevas 
                                            ||' [TOTAL REGISTRADAS]=> '||transInsert,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                            '127.0.0.1')
                                            );
    COMMIT;

   END LOOP;

  EXCEPTION
  WHEN OTHERS THEN
  --
  Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
  DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                        'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/