create or replace PACKAGE DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS
AS
    /**
      * Documentacion para el procedimiento P_SET_CUPOS_CUADRILLAS
      *
      * Método encargado de Generar cupos para planificacion en linea por un tiempo determinado
      *
      * @param Pt_FeInicio        IN  TIMESTAMP Recibe la fecha de Inicio de proceso
      * @param Pt_FeFin           IN  TIMESTAMP Recibe la fecha de fin de proceso
      * @param Fv_Error           OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 16-03-2018
      */
    PROCEDURE P_SET_CUPOS_CUADRILLAS(Pt_FeInicio IN  TIMESTAMP,
                                     Pt_FeFin    IN  TIMESTAMP,
                                     Pv_Error    OUT VARCHAR2);

    /**
      * Documentacion para el procedimiento P_GENERA_CUPOS_CUADRILLA
      *
      * Método encargado de Generar de intervalos de tiempos en un rango de fechas especifico
      *
      * @param Pt_FeRegistro        IN  TIMESTAMP Recibe la fecha para crear el intervalo de tiempo
      * @param Pv_HoraInicio        IN  VARCHAR2  Recibe la Hora inicial desde que hora se crearan los intervalos de tiempo
      * @param Pv_HoraFin           IN  VARCHAR2  Recibe la Hora Fin hasta que hora se crearan los intervalos de tiempo
      * @param Pn_Tiempo            IN  NUMBER    salto de tiempo para cada intervalo
      * @param Pv_UsrCreacion       IN  VARCHAR2  login del usuario que crea los intervalos
      * @param PV_IpCreacion        IN  VARCHAR2  ip de donde se crearon los intervalos
      * @param Fv_Error             OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 28-03-2018
      */

    PROCEDURE P_GENERA_CUPOS_CUADRILLA(Pt_FeRegistro  IN  TIMESTAMP,
                                       Pv_HoraInicio  IN  VARCHAR2,
                                       Pv_HoraFin     IN  VARCHAR2,
                                       Pn_Tiempo      IN  NUMBER,
                                       Pn_IdJurisdiccion  IN NUMBER,
                                       Pv_UsrCreacion IN  VARCHAR2,
                                       Pv_IpCreacion  IN  VARCHAR2,
                                       Pv_Error       OUT VARCHAR2);

    /**
      * Documentacion para el procedimiento P_INSERTA_CUPOS_CUADRILLA
      *
      * Método encargado de insertar los registros en la tabla DB_COMERCIAL.INFO_CUPO_PLANIFICACION
      *
      * @param Pt_FeInicio        IN  TIMESTAMP Recibe la fecha de Inicio de proceso
      * @param Pt_FeFin           IN  TIMESTAMP Recibe la fecha de fin de proceso
      * @param Pv_UsrCreacion     IN  VARCHAR2  login del usuario que crea los intervalos
      * @param PV_IpCreacion      IN  VARCHAR2  ip de donde se crearon los intervalos
      * @param Fv_Error           OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 28-03-2018
      */
    PROCEDURE P_INSERTA_CUPOS_CUADRILLA(Pt_FeInicio    IN  TIMESTAMP,
                                        Pt_FeFin       IN  TIMESTAMP,
                                        Pn_IdJurisdiccion  IN NUMBER,
                                        Pv_UsrCreacion IN  VARCHAR2,
                                        Pv_IpCreacion  IN  VARCHAR2,
                                        Pv_Error       OUT VARCHAR2);


    /**
      * Documentacion para el procedimiento P_ACTUALIZA_FECHA_GENERACION
      *
      * Método encargado de actualizar la fecha de ultima insercion de registros en la tabla DB_GENERAL.ADMI_PARAMETROS
      *
      * @param Pt_Fecha        IN  TIMESTAMP Fecha con la que se actualizará el parámetro
      * @param Pt_Descripcion  IN  TIMESTAMP Descripcion del parámetro que se va a actualizar
      * @param Fv_Error        OUT VARCHAR2 Retorna un mensaje de error en caso de existir
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 28-03-2018
      */
    PROCEDURE P_ACTUALIZA_FECHA_GENERACION(Pt_Fecha       IN  TIMESTAMP,
                                           Pv_Descripcion IN  VARCHAR2,
                                           Pv_Error       OUT VARCHAR2);


END CMKG_CUPOS_CUADRILLAS;
/
create or replace PACKAGE BODY DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS
AS
    PROCEDURE P_SET_CUPOS_CUADRILLAS(Pt_FeInicio IN TIMESTAMP,
                                     Pt_FeFin    IN TIMESTAMP,
                                     Pv_Error OUT VARCHAR2) AS

        Ln_Tiempo          NUMBER;
        Lv_HoraInicio      VARCHAR2(15);
        Lv_HoraFin         VARCHAR2(15);
        Ln_Cupos           NUMBER;
        Lt_FeBarrido       TIMESTAMP;
        Lt_FeInicio        TIMESTAMP;
        Lt_FeFin           TIMESTAMP;
        Lv_FeConvertida    VARCHAR2(20);
        Ln_CuposBarrido    NUMBER;
        Ln_CuposError      NUMBER := 0;
        Lv_Descripcion     VARCHAR2(30) := 'INTERVALOS';

        CURSOR C_Jurisdiccion IS
            SELECT ID_JURISDICCION, CUPO
            FROM DB_INFRAESTRUCTURA.ADMI_JURISDICCION
            WHERE CUPO IS NOT NULL;

        CURSOR C_Parametros(Cv_Descripcion VARCHAR2) IS
            SELECT VALOR1, VALOR2, VALOR3, VALOR4, VALOR5
            FROM DB_GENERAL.ADMI_PARAMETRO_DET
            WHERE DESCRIPCION = Cv_Descripcion;
    BEGIN

        IF (C_Parametros%ISOPEN) THEN
            CLOSE C_Parametros;
        END IF;
        OPEN C_Parametros(Lv_Descripcion);
            FETCH C_Parametros INTO Ln_Tiempo, Lv_HoraInicio, Lv_HoraFin, Ln_Cupos, Lt_FeInicio;
        CLOSE C_Parametros;
        IF (C_Jurisdiccion%ISOPEN) THEN
            CLOSE C_Jurisdiccion;
        END IF;

        Lt_FeInicio := Lt_FeInicio + 1;

        IF (Pt_FeInicio IS NULL AND Pt_FeFin IS NOT NULL) THEN
            Pv_Error := 'Fecha de Inicio no puede ser nula';
            RETURN;
        END IF;

        IF (Pt_FeInicio IS NOT NULL AND Pt_FeFin IS NULL) THEN
            Pv_Error := 'Fecha de Fin no puede ser nula';
            RETURN;
        END IF;

        IF (Pt_FeInicio IS NOT NULL AND Pt_FeFin IS NOT NULL) THEN
            Lt_FeInicio := Pt_FeInicio;
            Lt_FeFin    := Pt_FeFin;
        ELSE
            Lt_FeFin    := Lt_FeInicio;
        END IF;

        IF (Lt_FeInicio > Lt_FeFin) THEN
            Pv_Error := 'Fecha de Inicio no puede ser mayor a la fecha de finalizacion';
            RETURN;
        END IF;

        FOR reg in C_Jurisdiccion
        LOOP
            Ln_CuposBarrido := 1;

            WHILE (Ln_CuposBarrido <= TO_NUMBER(reg.cupo)) LOOP
                Lt_FeBarrido := Lt_FeInicio;
                WHILE (Lt_FeBarrido <= Lt_FeFin) LOOP
                    DBMS_OUTPUT.PUT_LINE (Ln_CuposBarrido);
                    DBMS_OUTPUT.PUT_LINE (Lt_FeFin);
                    DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_GENERA_CUPOS_CUADRILLA(Lt_FeBarrido,
                                                                                Lv_HoraInicio,
                                                                                Lv_HoraFin,
                                                                                Ln_Tiempo,
                                                                                reg.id_jurisdiccion,
                                                                                'epin',
                                                                                '127.0.0.1',
                                                                                Pv_Error);
                    IF (Pv_Error IS NULL) THEN
                        COMMIT;
                    ELSE
                        ROLLBACK;
                        Ln_CuposError := Ln_CuposError + 1;
                    END IF;
                    Lt_FeBarrido := Lt_FeBarrido + 1;
                END LOOP;
                Ln_CuposBarrido := Ln_CuposBarrido + 1;
            END LOOP;

        END LOOP;

        --actualizo la fecha en el parametro;
        DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_ACTUALIZA_FECHA_GENERACION(Lt_FeFin,
                                                                        Lv_Descripcion,
                                                                        Pv_Error);

        IF (Ln_CuposError > 0) THEN
            Pv_Error := 'No se generaron ' || Ln_CuposError || ' Cupos';
        END IF;
    END P_SET_CUPOS_CUADRILLAS;

    PROCEDURE P_GENERA_CUPOS_CUADRILLA(Pt_FeRegistro      IN TIMESTAMP,
                                       Pv_HoraInicio      IN VARCHAR2,
                                       Pv_HoraFin         IN VARCHAR2,
                                       Pn_Tiempo          IN NUMBER,
                                       Pn_IdJurisdiccion  IN NUMBER,
                                       Pv_UsrCreacion     IN VARCHAR2,
                                       Pv_IpCreacion      IN VARCHAR2,
                                       Pv_Error          OUT VARCHAR2)AS
        Lv_FeConvertida   VARCHAR2(20);
        Lt_HoraBarrido    TIMESTAMP;
        Lt_FeFin          TIMESTAMP;
        Lt_HoraBarridoFin TIMESTAMP;

   BEGIN
        Lv_FeConvertida := TO_CHAR(Pt_FeRegistro, 'DD/MM/YYYY') || ' ' || Pv_HoraInicio;
        Lt_HoraBarrido := TO_TIMESTAMP(Lv_FeConvertida, 'DD/MM/YYYY HH24:MI:SS');
        Lv_FeConvertida := TO_CHAR(Pt_FeRegistro, 'DD/MM/YYYY') || ' ' || Pv_HoraFin;
        Lt_FeFin := TO_TIMESTAMP(Lv_FeConvertida, 'DD/MM/YYYY HH24:MI:SS');

        DBMS_OUTPUT.PUT_LINE (Lt_HoraBarrido);
        DBMS_OUTPUT.PUT_LINE (Lt_FeFin);
        WHILE (Lt_HoraBarrido < Lt_FeFin) LOOP
            Lt_HoraBarridoFin := Lt_HoraBarrido + TO_NUMBER(Pn_Tiempo)/24/60;
            Lt_HoraBarrido := Lt_HoraBarrido + 1/24/60;
            DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_INSERTA_CUPOS_CUADRILLA(Lt_HoraBarrido,
                                                                         Lt_HoraBarridoFin,
                                                                         Pn_IdJurisdiccion,
                                                                         Pv_UsrCreacion,
                                                                         Pv_IpCreacion,
                                                                         Pv_Error);
            Lt_HoraBarrido := Lt_HoraBarridoFin;

        END LOOP;

    END P_GENERA_CUPOS_CUADRILLA;

    PROCEDURE P_INSERTA_CUPOS_CUADRILLA(Pt_FeInicio        IN TIMESTAMP,
                                        Pt_FeFin           IN TIMESTAMP,
                                        Pn_IdJurisdiccion  IN NUMBER,
                                        Pv_UsrCreacion     IN VARCHAR2,
                                        Pv_IpCreacion      IN VARCHAR2,
                                        Pv_Error          OUT VARCHAR2) AS
    BEGIN
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
            Pt_FeInicio,
            Pt_FeFin,
            NULL,
            NULL,
            SYSDATE,
            Pv_UsrCreacion,
            Pv_IpCreacion,
            NULL,
            NULL,
            Pn_IdJurisdiccion
        );
    EXCEPTION
        WHEN OTHERS THEN
            Pv_Error:= 'No se pudo grabar el registro ' || SQLERRM;
    END P_INSERTA_CUPOS_CUADRILLA;

    PROCEDURE P_ACTUALIZA_FECHA_GENERACION(Pt_Fecha       IN  TIMESTAMP,
                                           Pv_Descripcion IN  VARCHAR2,
                                           Pv_Error       OUT VARCHAR2) AS
    BEGIN
        UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
        SET VALOR5 = Pt_Fecha
        WHERE DESCRIPCION = Pv_Descripcion;
    EXCEPTION
        WHEN OTHERS THEN
            Pv_Error:= 'No se pudo actualizar la fecha ' || SQLERRM;
    END P_ACTUALIZA_FECHA_GENERACION;


END CMKG_CUPOS_CUADRILLAS;

