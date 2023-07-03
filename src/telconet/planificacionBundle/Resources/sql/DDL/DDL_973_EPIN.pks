CREATE OR REPLACE PACKAGE DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS AS
    /**
      * Documentacion para el procedimiento P_SET_CUPOS_CUADRILLAS
      *
      * Método encargado de Generar cupos para planificacion en linea por un tiempo determinado
      *
      * @param Pd_FeInicio     IN  DATE     Recibe la fecha desde, para la generación de los cupos.
      * @param Pd_FeFin        IN  DATE     Recibe la fecha hasta, para la generación de los cupos.
      * @param Pn_Jurisdiccion IN NUMBER    Recibe el Id de la jurisdicción.
      * @param Pn_IdPlantilla  IN NUMBER    Recibe el Id de la cabecera de la agenda para el formato de los horarios
      * @param Pn_Resul        OUT NUMBER   Retorna un codigo de resultado (0=Exito, 1=Error)
      * @param Pv_Resul        OUT VARCHAR2 Retorna el mensaje de resultado de la transacción
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 16-03-2018
      *
      * @Modificación: Se reestructura el procedimiento para ser consumido desde el telcos
      * @Autor Juan Romero Aguilar <jromero@telconet.ec>
      * @Version 1.1 06/06/2018
      */
    PROCEDURE P_SET_CUPOS_CUADRILLAS (Pd_FeInicio     IN  DATE,
                                      Pd_FeFin        IN  DATE,
                                      Pn_Jurisdiccion IN NUMBER,
                                      Pn_IdPlantilla  IN NUMBER,
                                      Pn_Resul        OUT NUMBER,
                                      Pv_Resul        OUT VARCHAR2);

    /**
      * Documentacion para el procedimiento P_GENERA_CUPOS_CUADRILLA
      *
      * Procedimiento para la generación de cupos de un día específico
      *
      * @param Pt_FeRegistro   IN  DATE      Recibe la fecha para la cual se crearán los cupos
      * @param Pn_IdPlantilla  IN  NUMBER    Recibe el Id de la agenda para el formato de los horarios
      * @param Pn_Tiempo       IN  NUMBER    Recibe el número de minutos de intervalo para registrar (formato HAL)
      * @param Pn_Jurisdiccion IN  NUMBER    Recibe el Id de la jurisdicción.
      * @param Pn_Cupo         IN  NUMBER    Recibe total de cupos a generar por horario
      * @param Pv_UsrCreacion  IN  VARCHAR2  Recibe login del usuario que ejecuta la transacción
      * @param PV_IpCreacion   IN  VARCHAR2  Recibe Ip desde la cual se ejecuta la transacción
      * @param Pn_Resul        OUT NUMBER    Retorna un codigo de resultado (0=Exito, 1=Error)
      * @param Pv_Resul        OUT VARCHAR2  Retorna el mensaje de resultado de la transacción
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 28-03-2018
      *
      * @Modificación: Se reestructura el procedimiento para que genere los cupos segun la fecha y plantilla recibida.
      * @Autor Juan Romero Aguilar <jromero@telconet.ec>
      * @Version 1.1 06/06/2018
      */

    PROCEDURE P_GENERA_CUPOS_CUADRILLA (Pd_FeRegistro       IN DATE,
                                        Pn_IdPlantilla      IN NUMBER,
                                        Pn_Tiempo           IN NUMBER,
                                        Pn_IdJurisdiccion   IN NUMBER,
                                        Pv_UsrCreacion      IN VARCHAR2,
                                        Pv_IpCreacion       IN VARCHAR2,
                                        Pn_Resul            OUT NUMBER,
                                        Pv_Resul            OUT VARCHAR2);


    /**
      * Documentacion para el procedimiento P_INSERTA_CUPO_X_HORARIO
      *
      * Procedimiento para la generacion de N cupos por horario
      *
      * @param Pd_HoraInicio       IN DATE      Recibe Fecha y hora desde, del horario al cual se le crearan los cupos
      * @param Pd_HoraFin          IN DATE      REcibe Fecha y hora hasta, del horario al cual se le crearan los cupos
      * @param Pn_IdJurisdiccion   IN NUMBER    Recibe Id de la jurisdicción
      * @param Pn_CuposIngresar    IN NUMBER    Recibe número de Cupos a generar
      * @param Pn_TotalCupos       IN NUMBER    Recibe total de cupos para la plantilla
      * @param Pn_Resul            OUT NUMBER   Retorna un codigo de resultado (0=Exito, 1=Error)
      * @param Pv_Resul            OUT VARCHAR2 Retorna el mensaje de resultado de la transacción
      *
      * @author Juan Romero <jromero@telconet.ec>
      * @version 1.0 07-06-2018
      */


    PROCEDURE P_INSERTA_CUPO_X_HORARIO (Pd_HoraInicio       IN DATE,
                                       Pd_HoraFin          IN DATE,
                                       Pn_IdJurisdiccion   IN NUMBER,
                                       Pn_CuposIngresar    IN NUMBER,
                                       Pn_TotalCupos       IN NUMBER,
                                       Pn_Resul            OUT  NUMBER,
                                       Pv_Resul            OUT VARCHAR2);

    /**
      * Documentacion para el procedimiento P_INSERTA_CUPOS_CUADRILLA
      *
      * Método encargado de solicitar la inserción de los registros en la tabla DB_COMERCIAL.INFO_CUPO_PLANIFICACION
      *
      * @param Pd_HoraInicio        IN  DATE      Recibe la fecha desde, para la cual se crearán los cupos
      * @param Pd_HoraFin           IN  DATE      Recibe la fecha hasta, para la cual se crearán los cupos
      * @param Pn_Jurisdiccion      IN  NUMBER    Recibe el Id de la jurisdicción.
      * @param Pn_TotalCupos        IN  NUMBER    Recibe total de cupos a generar por horario
      * @param Pv_UsrCreacion       IN  VARCHAR2  Recibe login del usuario que ejecuta la transacción
      * @param PV_IpCreacion        IN  VARCHAR2  Recibe Ip desde la cual se ejecuta la transacción
      * @param Pb_ValidaInsercion   IN BOOLEAN    Recibe instrucción si debe o no validar el máximo de registros a insertar por horario
      * @param Pn_Resul             OUT NUMBER    Retorna un codigo de resultado (0=Exito, 1=Error)
      * @param Pv_Resul             OUT VARCHAR2  Retorna el mensaje de resultado de la transacción
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 28-03-2018
      *
      * @Modificación: Se cambia los parametros del procedimiento, de acuerdo a la nueva logica del resto de procedimientos.
      * @Autor Juan Romero Aguilar <jromero@telconet.ec>
      * @Version 1.1 06/06/2018
      */

    PROCEDURE P_INSERTA_CUPOS_CUADRILLA (Pd_HoraInicio       IN DATE,
                                        Pd_HoraFin          IN DATE,
                                        Pn_IdJurisdiccion   IN NUMBER,
                                        Pn_TotalCupos       IN NUMBER,
                                        Pv_UsrCreacion      IN VARCHAR2,
                                        Pv_IpCreacion       IN VARCHAR2,
                                        Pb_ValidaInsercion  IN BOOLEAN,
                                        Pn_Resul            OUT NUMBER,
                                        Pv_Resul            OUT VARCHAR2);

END CMKG_CUPOS_CUADRILLAS;

/


CREATE OR REPLACE PACKAGE BODY                           DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS
AS
    PROCEDURE P_SET_CUPOS_CUADRILLAS(Pd_FeInicio     IN DATE,
                                     Pd_FeFin        IN DATE,
                                     Pn_Jurisdiccion IN NUMBER,
                                     Pn_IdPlantilla  IN NUMBER,
                                     Pn_Resul        OUT NUMBER,
                                     Pv_Resul        OUT VARCHAR2) AS

    --***************Variables**************      
        Ld_FeBarrido       DATE;
        Ld_FeInicio        DATE;
        Ld_FeFin           DATE;
        Ln_Tiempo          NUMBER;
        Ln_IdPlantilla     NUMBER;
        Ln_CuposBarrido    NUMBER;
        Ln_Resul           NUMBER;
        Ln_jurisdiccion    NUMBER;
        Lv_ipLocal         VARCHAR2(50);
        Lv_Resul           VARCHAR2(1000);
        Lv_Programa        VARCHAR2(50) :='P_SET_CUPOS_CUADRILLAS';
    --**************************************
    --***************Cursores**************************
        CURSOR C_Jurisdiccion(Cn_jurisdiccion number) IS
            SELECT ID_JURISDICCION
            FROM DB_COMERCIAL.ADMI_JURISDICCION
            WHERE ID_JURISDICCION=Cn_jurisdiccion;

        CURSOR C_Parametros(Cv_Descripcion VARCHAR2) IS
            SELECT VALOR1
            FROM DB_GENERAL.ADMI_PARAMETRO_DET
            WHERE DESCRIPCION = Cv_Descripcion;
    --**************************************************
    BEGIN
    --************************Validación y obtencion de parámetros******************************     
        IF(Pn_Jurisdiccion IS NULL)THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_Jurisdiccion no puede ser nula';
            RETURN;        
        END IF;

        IF (C_Jurisdiccion%ISOPEN) THEN
            CLOSE C_Jurisdiccion;
        END IF;
        OPEN C_Jurisdiccion(Pn_Jurisdiccion);
            FETCH C_Jurisdiccion INTO Ln_jurisdiccion;
        CLOSE C_Jurisdiccion;
        IF(Ln_jurisdiccion IS NULL)THEN
            Pn_Resul :=1;
            Pv_Resul := 'No se encontró información para la jurisdicción: '||Pn_Jurisdiccion;
            RETURN;        
        END IF;

        IF (C_Parametros%ISOPEN) THEN
            CLOSE C_Parametros;
        END IF;
        OPEN C_Parametros('INTERVALOS');
            FETCH C_Parametros INTO Ln_Tiempo;
        CLOSE C_Parametros;
        IF(Ln_Tiempo IS NULL)THEN
            Pn_Resul :=1;
            Pv_Resul := 'No se encontró el parámetro Ln_Tiempo';
            RETURN;        
        END IF;   

        IF (Pd_FeInicio IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Fecha de Inicio no puede ser nula';
            RETURN;
        ELSE
            Ld_FeInicio :=Pd_FeInicio;
        END IF;

        IF (Pd_FeFin IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Fecha de Fin no puede ser nula';
            RETURN;
        ELSE
            Ld_FeFin :=Pd_FeFin;
        END IF;

        IF (Pn_IdPlantilla IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'El ID de la plantilla no puede ser nulo';
            RETURN;
        ELSE
            Ln_IdPlantilla:=Pn_IdPlantilla;
        END IF;

        IF (Ld_FeInicio > Ld_FeFin) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Fecha de Inicio no puede ser mayor a la fecha de finalizacion';
            RETURN;
        END IF;
    --************************************************************************************************
        select SYS_CONTEXT('USERENV', 'IP_ADDRESS', 15)into lv_IpLocal from dual;
        Ld_FeBarrido := Ld_FeInicio;
        WHILE (Ld_FeBarrido <= Ld_FeFin) LOOP
            --Para procesar para cada uno de los días
            DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_GENERA_CUPOS_CUADRILLA(Ld_FeBarrido,
                                                                            Ln_IdPlantilla,
                                                                            Ln_Tiempo,
                                                                            Ln_jurisdiccion,
                                                                            USER,
                                                                            nvl(lv_IpLocal,'127.0.0.1'),
                                                                            Ln_Resul,
                                                                            Lv_Resul);
            IF (nvl(Ln_Resul,1)=1) THEN
                ROLLBACK;
                Pn_Resul:=1;
                Pv_Resul:=Lv_Programa||' : '||Lv_Resul;
                RETURN;
            ELSE
                COMMIT;
            END IF;
            Ld_FeBarrido := Ld_FeBarrido + 1;
        END LOOP;
        Pn_Resul:=0;
        Pv_Resul:='OK';
    EXCEPTION 
        WHEN OTHERS THEN
            ROLLBACK;
            Pn_Resul:=1;
            Pv_Resul:=Lv_Programa||' : '||SUBSTR(SQLERRM, 1,500);            
    END P_SET_CUPOS_CUADRILLAS;

    PROCEDURE P_GENERA_CUPOS_CUADRILLA( Pd_FeRegistro       IN DATE,
                                        Pn_IdPlantilla      IN NUMBER,
                                        Pn_Tiempo           IN NUMBER,
                                        Pn_IdJurisdiccion   IN NUMBER,
                                        Pv_UsrCreacion      IN VARCHAR2,
                                        Pv_IpCreacion       IN VARCHAR2,
                                        Pn_Resul            OUT NUMBER,
                                        Pv_Resul            OUT VARCHAR2)AS

    --***************Variables**************
        Ld_HoraDesde        DATE;
        Ld_HoraHasta        DATE;   
        Ld_HoraDesdeAux     DATE;
        Ld_HoraHastaAux     DATE;        
        Ln_CuposBarrido     NUMBER;
        Ln_Resul            NUMBER;
        Ln_cupo             NUMBER;
        Ln_cupoInsertar     NUMBER;
        Ln_TotCupo          NUMBER;
        Ln_vueltas_cupos    NUMBER:=0;
        Ln_vueltas_horario  NUMBER:=0;        
        Lv_Resul            VARCHAR2(1000);
        Lv_Programa         VARCHAR2(50) :='P_GENERA_CUPOS_CUADRILLA'; 
    --*************************************
    --*************Cursores****************
    CURSOR C_Horarios(Cv_Fecha VARCHAR2, Cn_IdPlantilla NUMBER) IS
        SELECT Cv_Fecha||' '||to_char(hora_desde,'hh24:mi:ss') horarioIni,
           Cv_Fecha||' '||to_char(hora_hasta,'hh24:mi:ss') horarioFin,
           (nvl(cupos_web,0)+nvl(cupos_movil,0)) cupos
            FROM db_comercial.Info_Agenda_Cupo_Det 
        WHERE agenda_cupo_id=Cn_IdPlantilla;  
        
    CURSOR C_CuposTotales(Cd_Fecha VARCHAR2, C_HoraDesde VARCHAR2, C_HoraHasta VARCHAR2, Cn_jurisdiccion NUMBER) IS
        SELECT nvl(sum(nvl(cupos_web,0)+nvl(cupos_movil,0)),0)
            FROM DB_COMERCIAL.INFO_AGENDA_CUPO_CAB b,
                 db_comercial.Info_Agenda_Cupo_Det c
            WHERE b.jurisdiccion_id=Cn_jurisdiccion
            AND b.fecha_periodo=to_date(Cd_Fecha,'dd/mm/rrrr')
            AND c.agenda_cupo_id=b.id_agenda_cupos
            AND (c.hora_desde BETWEEN TO_DATE(Cd_Fecha||' '||C_HoraDesde,'DD/MM/RRRR HH24:MI:SS')+5/1440 
                                      AND TO_DATE(Cd_Fecha||' '||C_HoraHasta,'DD/MM/RRRR HH24:MI:SS')-5/1440
                or TO_DATE(Cd_Fecha||' '||C_HoraDesde,'DD/MM/RRRR HH24:MI:SS') between c.hora_desde and c.hora_hasta-5/1440                     
            );

    CURSOR C_CuposActual(Cd_FechaIni DATE, Cn_jurisdiccion NUMBER) IS
    SELECT COUNT(1) 
        FROM DB_COMERCIAL.info_cupo_planificacion
    WHERE to_char(fe_inicio,'dd/mm/rrrr hh24:mi:ss')= to_char(Cd_FechaIni,'dd/mm/rrrr hh24:mi:ss')
    AND jurisdiccion_id=Cn_jurisdiccion;    
    --*************************************

   BEGIN
    --*******************Validación de parametros****************
        IF (Pd_FeRegistro IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pd_FeRegistro no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_IdPlantilla IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_IdPlantilla no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_IdJurisdiccion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_IdJurisdiccion no puede ser nulo';
            RETURN;
        END IF;

        IF (Pv_UsrCreacion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_UsrCreacion no puede ser nulo';
            RETURN;
        END IF;

        IF (Pv_IpCreacion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_IpCreacion no puede ser nula';
            RETURN;
        END IF;
    --********************************************************
        FOR I IN C_Horarios(to_char(Pd_FeRegistro,'dd/mm/rrrr'), Pn_IdPlantilla)LOOP
            --Para recorrer los horarios configurados en la plantilla
            Ld_HoraDesde:=to_date(I.horarioIni,'dd/mm/rrrr hh24:mi:ss');
            Ld_HoraHasta:=to_date(I.horarioFin,'dd/mm/rrrr hh24:mi:ss');
            --Para Obtener los totales de cupos considerando todas las plantillas de la jurisdiccion
            IF (C_CuposTotales%ISOPEN) THEN
                CLOSE C_CuposTotales;
            END IF;
            OPEN C_CuposTotales(to_char(Ld_HoraDesde,'dd/mm/rrrr'), to_char(Ld_HoraDesde,'hh24:mi:ss'), to_char(Ld_HoraHasta,'hh24:mi:ss'),Pn_IdJurisdiccion);
                FETCH C_CuposTotales INTO Ln_TotCupo;
            CLOSE C_CuposTotales;
            IF (Ln_TotCupo<=0) THEN
                Pn_Resul :=1;
                Pv_Resul :=Lv_Programa||' : No se encontró agenda para la fecha y jurisdicción enviadas';
                RETURN;
            END IF;
            --------
            WHILE(Ld_HoraDesde<Ld_HoraHasta) LOOP
                --Para recorrer los intervalos de media hora en cada horario
                Ln_CuposBarrido := 1;
                Ld_HoraDesdeAux:=Ld_HoraDesde+(1/1440);
                Ld_HoraHastaAux:=Ld_HoraDesde+(Pn_Tiempo/1440);

                IF (C_CuposActual%ISOPEN) THEN
                    CLOSE C_CuposActual;
                END IF;
                OPEN C_CuposActual(Ld_HoraDesdeAux,Pn_IdJurisdiccion);
                    FETCH C_CuposActual INTO Ln_cupo;
                CLOSE C_CuposActual;

                WHILE (Ln_CuposBarrido <= I.cupos) LOOP
                    --Para recorrer el numero de cupos por cada horario
                    IF(Ln_TotCupo-(nvl(Ln_cupo,0))>0) THEN
                        DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_INSERTA_CUPOS_CUADRILLA(Ld_HoraDesdeAux,
                                                                                     Ld_HoraHastaAux,
                                                                                     Pn_IdJurisdiccion,
                                                                                     Ln_TotCupo,
                                                                                     Pv_UsrCreacion,
                                                                                     Pv_IpCreacion,
                                                                                     FALSE,
                                                                                     Ln_Resul,
                                                                                     Lv_Resul);
                        IF(NVL(Ln_Resul,1)=1)THEN
                            Pn_Resul :=1;
                            Pv_Resul :=Lv_Programa||' : '||Lv_Resul;
                            RETURN;
                        END IF;
                    END IF;
                    Ln_vueltas_cupos:=Ln_vueltas_cupos+1;
                    Ln_CuposBarrido :=Ln_CuposBarrido+1;                   
               END LOOP; 
               Ld_HoraDesde:=Ld_HoraDesde+(Pn_Tiempo/1440);
            END LOOP;
            Ln_vueltas_horario:=Ln_vueltas_horario+1;
        END LOOP;
        IF(Ln_vueltas_horario>0) THEN
            Pn_Resul :=0;
            Pv_Resul :='OK';
        ELSE
            Pn_Resul :=1;
            Pv_Resul :=Lv_Programa||' : '||'No se encuentra información de la agenda enviada!!!';        
        END IF;
    EXCEPTION
        WHEN OTHERS THEN
            Pn_Resul :=1;
            Pv_Resul :=Lv_Programa||' : '||SUBSTR(SQLERRM, 1,500);
    END P_GENERA_CUPOS_CUADRILLA;


    PROCEDURE P_INSERTA_CUPO_X_HORARIO(Pd_HoraInicio       IN DATE,
                                       Pd_HoraFin          IN DATE,
                                       Pn_IdJurisdiccion   IN NUMBER,
                                       Pn_CuposIngresar    IN NUMBER,
                                       Pn_TotalCupos       IN NUMBER,
                                       Pn_Resul            OUT  NUMBER,
                                       Pv_Resul            OUT VARCHAR2) AS

    --***********************Cursores**********************
        CURSOR C_Parametros(Cv_Descripcion VARCHAR2) IS
            SELECT VALOR1
            FROM DB_GENERAL.ADMI_PARAMETRO_DET
            WHERE DESCRIPCION = Cv_Descripcion;
            
        CURSOR C_CuposTotales(Cd_Fecha VARCHAR2, C_HoraDesde VARCHAR2, C_HoraHasta VARCHAR2, Cn_jurisdiccion NUMBER) IS
            SELECT nvl(sum(nvl(cupos_web,0)+nvl(cupos_movil,0)),0)
                FROM DB_COMERCIAL.INFO_AGENDA_CUPO_CAB b,
                     db_comercial.Info_Agenda_Cupo_Det c
                WHERE b.jurisdiccion_id=Cn_jurisdiccion
                AND b.fecha_periodo=to_date(Cd_Fecha,'dd/mm/rrrr')
                AND c.agenda_cupo_id=b.id_agenda_cupos
                AND (c.hora_desde BETWEEN TO_DATE(Cd_Fecha||' '||C_HoraDesde,'DD/MM/RRRR HH24:MI:SS')+5/1440 
                                      AND TO_DATE(Cd_Fecha||' '||C_HoraHasta,'DD/MM/RRRR HH24:MI:SS')-5/1440
                      or TO_DATE(Cd_Fecha||' '||C_HoraDesde,'DD/MM/RRRR HH24:MI:SS') between c.hora_desde and c.hora_hasta-5/1440                     
                );            
    --****************Varaibles****************************
    Ld_HoraDesde     DATE;
    Ld_HoraHasta     DATE;
    Ld_HoraDesdeAux  DATE;
    Ld_HoraHastaAux  DATE;
    Ln_Resul         NUMBER;
    Ln_Loop          NUMBER;
    Ln_Tiempo        NUMBER;
    Ln_TotCupo       NUMBER;
    Ln_vueltas_cupos NUMBER:=0;
    Lv_ipLocal       VARCHAR2(50);
    Lv_Resul         VARCHAR2(1000);
    Lv_Programa      VARCHAR2(50) :='P_INSERTA_CUPO_X_HORARIO';
    --*****************************************************  

    BEGIN       
    --*******************Validación de parámetros***********
        IF (Pd_HoraInicio IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pd_HoraInicio no puede ser nula';
            RETURN;
        END IF;

        IF (Pd_HoraFin IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pd_HoraFin no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_IdJurisdiccion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_IdJurisdiccion no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_CuposIngresar IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_CuposIngresar no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_TotalCupos IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_TotalCupos no puede ser nula';
            RETURN;
        END IF;        
    --******************************************************       
        IF (C_Parametros%ISOPEN) THEN
            CLOSE C_Parametros;
        END IF;
        OPEN C_Parametros('INTERVALOS');
            FETCH C_Parametros INTO Ln_Tiempo;
        CLOSE C_Parametros;
        IF(Ln_Tiempo IS NULL)THEN
            Pn_Resul :=1;
            Pv_Resul := 'No se encontró el parámetro Ln_Tiempo';
            RETURN;        
        END IF; 

        select SYS_CONTEXT('USERENV', 'IP_ADDRESS', 15)into lv_IpLocal from dual;
        Ld_HoraDesde:=to_date(to_char(Pd_HoraInicio,'dd/mm/rrrr hh24:mi:ss'),'dd/mm/rrrr hh24:mi:ss');
        Ld_HoraHasta:=to_date(to_char(Pd_HoraFin,'dd/mm/rrrr hh24:mi:ss'),'dd/mm/rrrr hh24:mi:ss');
        --Para Obtener los totales de cupos considerando todas las plantillas de la jurisdiccion
        IF (C_CuposTotales%ISOPEN) THEN
            CLOSE C_CuposTotales;
        END IF;
        OPEN C_CuposTotales(to_char(Ld_HoraDesde,'dd/mm/rrrr'), to_char(Ld_HoraDesde,'hh24:mi:ss'), to_char(Ld_HoraHasta,'hh24:mi:ss'),Pn_IdJurisdiccion);
            FETCH C_CuposTotales INTO Ln_TotCupo;
        CLOSE C_CuposTotales;
        IF (Ln_TotCupo<=0) THEN
            Pn_Resul :=1;
            Pv_Resul :=Lv_Programa||' : No se encontró agenda para la fecha y jurisdicción enviadas';
            RETURN;
        END IF;
        --------       
        WHILE(Ld_HoraDesde<Ld_HoraHasta) LOOP
            --Para recorrer los intervalos de media hora en cada horario
            Ln_Loop :=1;
            Ld_HoraDesdeAux:=Ld_HoraDesde+(1/1440);
            Ld_HoraHastaAux:=Ld_HoraDesde+(Ln_Tiempo/1440);
            WHILE(Ln_Loop<=Pn_CuposIngresar) LOOP
                DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_INSERTA_CUPOS_CUADRILLA(Ld_HoraDesdeAux,
                                                                              Ld_HoraHastaAux,
                                                                              Pn_IdJurisdiccion,
                                                                              Ln_TotCupo,
                                                                              USER,
                                                                              NVL(Lv_ipLocal,'127.0.0.1'),
                                                                              TRUE,
                                                                              Ln_Resul,
                                                                              Lv_Resul);
                IF(NVL(Ln_Resul,1)=1)THEN
                    Pn_Resul :=1;
                    Pv_Resul :=Lv_Programa||' : '||Lv_Resul;
                    RETURN;
                END IF;
                Ln_Loop :=Ln_Loop+1;
                Ln_vueltas_cupos:=Ln_vueltas_cupos+1;
            END LOOP;
            Ld_HoraDesde:=Ld_HoraDesde+(Ln_Tiempo/1440);
        END LOOP;
        Pn_Resul :=0;
        Pv_Resul :='OK';
    EXCEPTION
        WHEN OTHERS THEN
            Pn_Resul :=1;
            Pv_Resul :=Lv_Programa||' : '||SUBSTR(SQLERRM, 1,500);
    END P_INSERTA_CUPO_X_HORARIO;

    PROCEDURE P_INSERTA_CUPOS_CUADRILLA(Pd_HoraInicio       IN DATE,
                                        Pd_HoraFin          IN DATE,
                                        Pn_IdJurisdiccion   IN NUMBER,
                                        Pn_TotalCupos       IN NUMBER,
                                        Pv_UsrCreacion      IN VARCHAR2,
                                        Pv_IpCreacion       IN VARCHAR2,
                                        Pb_ValidaInsercion  IN BOOLEAN,
                                        Pn_Resul            OUT NUMBER,
                                        Pv_Resul            OUT VARCHAR2) AS

    --****************Varaibles****************************
    Lv_Programa VARCHAR2(50) :='P_INSERTA_CUPOS_CUADRILLA';
    Ln_cupo     NUMBER;
    --*****************************************************
    --***********************Cursores**********************
    CURSOR C_TOTALES IS
        SELECT COUNT(1) 
            FROM DB_COMERCIAL.info_cupo_planificacion
        WHERE to_char(fe_inicio,'dd/mm/rrrr hh24:mi:ss')= to_char(Pd_HoraInicio,'dd/mm/rrrr hh24:mi:ss')
        AND jurisdiccion_id=Pn_IdJurisdiccion;  
    --*****************************************************

    BEGIN
    --*******************Validación de parametros****************
        IF (Pd_HoraInicio IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_HoraInicio no puede ser nula';
            RETURN;
        END IF;

        IF (Pd_HoraFin IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_HoraFin no puede ser nula';
            RETURN;
        END IF;

        IF (Pn_IdJurisdiccion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_IdJurisdiccion no puede ser nulo';
            RETURN;
        END IF;

        IF (Pn_TotalCupos IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pn_TotalCupos no puede ser nulo';
            RETURN;
        END IF;        

        IF (Pv_UsrCreacion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_UsrCreacion no puede ser nulo';
            RETURN;
        END IF;

        IF (Pv_IpCreacion IS NULL) THEN
            Pn_Resul :=1;
            Pv_Resul := 'Pv_IpCreacion no puede ser nula';
            RETURN;
        END IF;
    --******************************************************** 
        IF(Pb_ValidaInsercion) THEN
            IF (C_TOTALES%ISOPEN) THEN
                CLOSE C_TOTALES;
            END IF;
            OPEN C_TOTALES;
                FETCH C_TOTALES INTO Ln_cupo;
            CLOSE C_TOTALES;
        ELSE
            Ln_cupo:=0;
        END IF;
        IF (nvl(Ln_cupo,0)<Pn_TotalCupos) THEN
            INSERT INTO DB_COMERCIAL.INFO_CUPO_PLANIFICACION (
                id_cupo_planificacion,
                fe_inicio,
                fe_fin,
                solicitud_id,
                cuadrilla_id,
                fe_creacion,
                usr_creacion,
                ip_creacion,
                fe_modificacion,
                usr_modificacion,
                jurisdiccion_id
            ) VALUES (
                DB_COMERCIAL.SEQ_INFO_CUPO_PLANIFICACION.NEXTVAL,
                Pd_HoraInicio,
                Pd_HoraFin,
                NULL,
                NULL,
                SYSDATE,
                Pv_UsrCreacion,
                Pv_IpCreacion,
                NULL,
                NULL,
                Pn_IdJurisdiccion
            );
        END IF;
        Pn_Resul :=0;
        Pv_Resul :='OK';
    EXCEPTION
        WHEN OTHERS THEN
            Pn_Resul :=1;
            Pv_Resul :=Lv_Programa||' : '||SUBSTR(SQLERRM, 1,500);
    END P_INSERTA_CUPOS_CUADRILLA;

END CMKG_CUPOS_CUADRILLAS;
/

