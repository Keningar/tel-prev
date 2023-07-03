CREATE OR REPLACE PACKAGE NAF47_TNET.AFK_REGULARIZA_CUSTODIO 
AS

/**
* Documentacion para NAF47_TNET.AFK_REGULARIZA_CUSTODIO
* Paquete que contiene procedimientos para regularizar el custodio
* @author jmujica <jmujica@telconet.ec>
* @version 1.0 21/10/2022
*
*/

-- Declaracion de Constantes --
  Gc_EstadoAsignado CONSTANT VARCHAR2(08) := 'Asignado';
  Gc_EstadoGenerado CONSTANT VARCHAR2(08) := 'Generado';


/**
*Documentacion para P_CAMBIO_CUSTODIO
*Procedimiento que actualizacion/insercion de custodio
*
*@author  Jenniffer Mujica Meneses <jmujica@telconet.ec>
*@version 1.0  21/10/2022
*
* @param Pv_NumeroSerie            IN ARAF_CONTROL_CUSTODIO.ARTICULO_ID%TYPE         Numero de serie
* @param Pn_IdCustodioEnt          IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE         Id persona empresa rol que traspasa articulo
* @param Pn_CantidadEnt            IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE            Cantidad a procesar
* @param Pn_IdCustodioRec          IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE         Id persona empresa rol que recibe articulo
* @param Pn_CantidadRec            IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE            Cantidad a procesar
* @param Pv_LoginProcesa           IN ARAF_CONTROL_CUSTODIO.TAREA_ID%TYPE            Login usuario procesa
* @param Pv_MensajeError           IN OUT VARCHAR2                                   Retorna mensaje error
* @param Pn_IdControl              IN ARAF_CONTROL_CUSTODIO.ID_CONTROL%TYPE DEFAULT 0,
* @param Pb_Commit                 IN BOOLEAN DEFAULT TRUE
**/
  PROCEDURE P_CAMBIO_CUSTODIO(Pv_NumeroSerie      IN ARAF_CONTROL_CUSTODIO.ARTICULO_ID%TYPE,
                          Pn_IdCustodioEnt    IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE, 
                          Pn_CantidadEnt      IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE,
                          Pn_IdCustodioRec    IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE, 
                          Pn_CantidadRec      IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE, 
                          Pv_LoginProcesa     IN ARAF_CONTROL_CUSTODIO.LOGIN%TYPE,
                          Pv_MensajeError     IN OUT VARCHAR2,
                          Pv_status           OUT VARCHAR2,
                          Pn_IdControl        IN ARAF_CONTROL_CUSTODIO.ID_CONTROL%TYPE DEFAULT 0,
                          Pb_Commit	          IN BOOLEAN DEFAULT TRUE);


END AFK_REGULARIZA_CUSTODIO;
/

CREATE OR REPLACE PACKAGE BODY NAF47_TNET.AFK_REGULARIZA_CUSTODIO as

  PROCEDURE P_CAMBIO_CUSTODIO(Pv_NumeroSerie      IN ARAF_CONTROL_CUSTODIO.ARTICULO_ID%TYPE,
                          Pn_IdCustodioEnt    IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE, 
                          Pn_CantidadEnt      IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE, 
                          Pn_IdCustodioRec    IN ARAF_CONTROL_CUSTODIO.CUSTODIO_ID%TYPE, 
                          Pn_CantidadRec      IN ARAF_CONTROL_CUSTODIO.CANTIDAD%TYPE, 
                          Pv_LoginProcesa     IN ARAF_CONTROL_CUSTODIO.LOGIN%TYPE, 
                          Pv_MensajeError     IN OUT VARCHAR2,
                          Pv_status           OUT VARCHAR2,
                          Pn_IdControl        IN ARAF_CONTROL_CUSTODIO.ID_CONTROL%TYPE DEFAULT 0,
                          Pb_Commit	          IN BOOLEAN DEFAULT TRUE) IS
    --------------------------------------------------------------------------
    Ln_IdControlCustodio      ARAF_CONTROL_CUSTODIO.ID_CONTROL%type         := 0;
    Ln_Movimiento             ARAF_CONTROL_CUSTODIO.MOVIMIENTO%type         := 0;
    Ld_FechaInicio            ARAF_CONTROL_CUSTODIO.FECHA_INICIO%type       := TRUNC(SYSDATE);
    Ln_IdTarea                ARAF_CONTROL_CUSTODIO.TAREA_ID%TYPE            := NULL;

    Lv_EstadoArf              ARAF_CONTROL_CUSTODIO.ESTADO%type             := 'Asignado';
    Lv_TipoCustodio           ARAF_CONTROL_CUSTODIO.TIPO_CUSTODIO%type      := 'Empleado';
    Lv_EstadoIn               IN_ARTICULOS_INSTALACION.ESTADO%type          := 'PI';
    Lv_TipoArticulo           IN_ARTICULOS_INSTALACION.TIPO_ARTICULO%type   := 'AF';
    Pv_TipoArticulo           ARAF_CONTROL_CUSTODIO.TIPO_ARTICULO%TYPE      := 'Equipos';
    

    Lv_TipoTransaccion        ARAF_CONTROL_CUSTODIO.TIPO_TRANSACCION_ID%TYPE := 'Tarea';
    Lv_IdTransaccion          ARAF_CONTROL_CUSTODIO.TRANSACCION_ID%TYPE      := 0;
    Lv_TipoActividad          ARAF_CONTROL_CUSTODIO.TIPO_ACTIVIDAD%TYPE      := 'Actualizacion Custodio';
    -------------------------------------------------------------------------
    Ln_CantRegistroAcc NUMBER;
    Ln_CantRegistroIai NUMBER;
    Ln_IdCustodioIai   NUMBER;
    Ln_IdEmpresaRol    NUMBER;  
    Ln_Secuencia       NUMBER := 0;
    Ln_IdInstalacion   NUMBER := 0;

    Le_Error EXCEPTION;
    -----------------------------------------------------------

    CURSOR C_DATOS_PERSONA(Cn_IdPersonaEmpresaRol NUMBER) IS
      SELECT ATR.DESCRIPCION_TIPO_ROL,
             IER.EMPRESA_COD,
             P.LOGIN,
             P.IDENTIFICACION_CLIENTE
      FROM DB_GENERAL.INFO_PERSONA             P,
           DB_GENERAL.INFO_PERSONA_EMPRESA_ROL IPER,
           DB_GENERAL.INFO_EMPRESA_ROL         IER,
           DB_GENERAL.ADMI_ROL                 AR,
           DB_GENERAL.ADMI_TIPO_ROL            ATR
      WHERE IPER.ID_PERSONA_ROL = Cn_IdPersonaEmpresaRol
      AND P.ID_PERSONA = IPER.PERSONA_ID
      AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
      AND IER.ROL_ID = AR.ID_ROL
      AND AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL;
    --------------------------------------------------------------------------

    CURSOR C_CONTROL_ENTREGA_POR_ID ( Cn_IdCustodio NUMBER) IS
      SELECT ACC.CUSTODIO_ID,
             ACC.EMPRESA_CUSTODIO_ID,
             ACC.TIPO_CUSTODIO,
             ACC.ARTICULO_ID,
             ACC.TIPO_ARTICULO,
             ACC.CANTIDAD,
             ACC.TIPO_TRANSACCION_ID,
             ACC.TRANSACCION_ID,
             ACC.CASO_ID,
             ACC.TAREA_ID,
             ACC.TIPO_ACTIVIDAD,
             ACC.CARACTERISTICA_ID,
             ACC.EMPRESA_ID,
             ACC.ID_CONTROL,
             ACC.LOGIN,
             ACC.FE_ASIGNACION,
             ACC.FECHA_INICIO,
             ACC.FECHA_FIN,
             ACC.NO_ARTICULO,
             ACC.OBSERVACION,
             ACC.ESTADO
      FROM NAF47_TNET.ARAF_CONTROL_CUSTODIO ACC
      WHERE ACC.CUSTODIO_ID = Cn_IdCustodio;

      --------------------------------------------------------------
      CURSOR C_DATOS_CUSTODIO_ENTREGA IS
          SELECT ACC.CUSTODIO_ID, 
                 ACC.ID_CONTROL,  
                 ACC.TIPO_CUSTODIO, 
                 ACC.ARTICULO_ID , 
                 ACC.CANTIDAD, 
                 ACC.ESTADO, 
                 ACC.ID_CONTROL_ORIGEN,
                 ACC.EMPRESA_CUSTODIO_ID
          FROM  NAF47_TNET.ARAF_CONTROL_CUSTODIO ACC   
          WHERE ACC.ESTADO         = Lv_EstadoArf
                AND ACC.CANTIDAD       > 0 
                AND ACC.TIPO_ARTICULO  = Pv_TipoArticulo
                AND ACC.TIPO_CUSTODIO  = Lv_TipoCustodio
                AND ACC.ARTICULO_ID    = Pv_NumeroSerie;

     -----------------------------------------------------------------
     CURSOR C_DATOS_CUSTODIO_IAI IS
        SELECT IAI.ID_CUSTODIO
               --IAI.CEDULA,
               --IAI.NUMERO_SERIE, 
               --IAI.MODELO,     
               --IAI.MAC,      
               --IAI.ESTADO,     
               --IAI.TIPO_PROCESO  
        FROM NAF47_TNET.IN_ARTICULOS_INSTALACION IAI
        WHERE IAI.CANTIDAD       > 0    
                AND IAI.ESTADO         = Lv_EstadoIn    
                AND IAI.NUMERO_SERIE   = Pv_NumeroSerie
                AND IAI.TIPO_ARTICULO  = Lv_TipoArticulo;

      ----------------------------------------------------------------

      CURSOR C_EMPLEADO (Cv_Login VARCHAR2) IS
        SELECT  LEP.LOGIN LOGIN_EMPLE, ARP.NO_EMPLE, LEP.CEDULA
          FROM NAF47_TNET.ARPLME ARP, NAF47_TNET.LOGIN_EMPLEADO LEP
        WHERE ARP.NO_CIA = LEP.NO_CIA
          AND ARP.NO_EMPLE = LEP.NO_EMPLE
          AND LEP.LOGIN = Cv_Login
          AND ARP.ESTADO = 'A';

    -----------------------------------------------------------------------
    
     CURSOR C_ID_INSTALACION (Cv_NumeroSerie VARCHAR2) IS
        SELECT A.ID_INSTALACION, A.ID_ARTICULO
          FROM NAF47_TNET.in_articulos_instalacion A
         WHERE A.NUMERO_SERIE = Cv_NumeroSerie
         ORDER BY FECHA DESC, ID_INSTALACION DESC;
         
         
    CURSOR C_ID_CONTROL_CUSTODIO (Cv_NumeroSerie VARCHAR2) IS
        SELECT A.ID_CONTROL, A.ARTICULO_ID,A.CUSTODIO_ID, A.NO_ARTICULO, A.EMPRESA_CUSTODIO_ID
          FROM NAF47_TNET.ARAF_CONTROL_CUSTODIO A
         WHERE A.ARTICULO_ID = Cv_NumeroSerie
         ORDER BY FECHA_FIN DESC, ID_CONTROL DESC;
         
    CURSOR C_INV_NUM_SERIE (Cv_NumeroSerie VARCHAR2) IS
         SELECT INVS.NO_ARTICULO,INVS.SERIE, INVS.COMPANIA
            FROM  NAF47_TNET.INV_NUMERO_SERIE INVS
         WHERE SERIE = Cv_NumeroSerie;
                            
    ----------------------------------------------------------------------


    Lr_PersonaEntrega         C_DATOS_PERSONA%ROWTYPE             := NULL;
    Lr_PersonaRecibe          C_DATOS_PERSONA%ROWTYPE             := NULL;
    Lr_ControlCustodioEntrega C_DATOS_CUSTODIO_ENTREGA%ROWTYPE    := NULL;
    Lv_IdInstalacion          C_ID_INSTALACION%ROWTYPE            := NULL;
    Lr_IdControlCustodio      C_ID_CONTROL_CUSTODIO%ROWTYPE       := NULL;
    Lv_IdEmpleadoArplme       C_EMPLEADO%ROWTYPE                  := NULL;
    Lr_InvNumSerie            C_INV_NUM_SERIE%ROWTYPE             := NULL;

    ----------------------------------------------------------------------

    BEGIN
      IF Pn_IdControl IS NULL OR Pn_IdControl = 0 THEN
        Ln_IdControlCustodio := NAF47_TNET.SEQ_ARAF_CONTROL_CUSTODIO.NEXTVAL;
      ELSE
        Ln_IdControlCustodio := Pn_IdControl;
      END IF;

  ----------------------------------------------------
  -- Conteo de serie en araf_control_custodio
  ----------------------------------------------------

      SELECT COUNT(ACC.ARTICULO_ID) INTO Ln_CantRegistroAcc
                          FROM 
                            NAF47_TNET.ARAF_CONTROL_CUSTODIO ACC   
                          WHERE ACC.ESTADO         = Lv_EstadoArf
                            AND ACC.CANTIDAD       > 0 
                            AND ACC.TIPO_ARTICULO  = Pv_TipoArticulo
                            AND ACC.TIPO_CUSTODIO  = Lv_TipoCustodio
                            AND ACC.ARTICULO_ID    = Pv_NumeroSerie;

  ----------------------------------------------------
  -- Conteo de serie en in_articulo_instalacion
  ----------------------------------------------------

      SELECT COUNT(IAI.NUMERO_SERIE) INTO Ln_CantRegistroIai
                         FROM 
                           NAF47_TNET.IN_ARTICULOS_INSTALACION IAI
                         WHERE IAI.CANTIDAD       > 0    
                           AND IAI.ESTADO         = Lv_EstadoIn       
                           AND IAI.NUMERO_SERIE   = Pv_NumeroSerie
                           AND IAI.TIPO_ARTICULO = Lv_TipoArticulo;

  ----------------------------------------------------------
  -- Creacion o actualizacion de custodio
  ----------------------------------------------------------
  -- Caso 1: Si registro esta en in_articulos_instalacion y no en araf_control_custodio
  -- Se inserta registro en araf_control_custodio 
  ----------------------------------------------------
      Ln_Movimiento := Pn_CantidadRec * -1;

      -- Recupera datos de persona que será nuevo custodio
          IF C_DATOS_PERSONA%ISOPEN THEN
            CLOSE C_DATOS_PERSONA;
          END IF;
          --
          OPEN C_DATOS_PERSONA(Pn_IdCustodioRec);
          FETCH C_DATOS_PERSONA INTO Lr_PersonaRecibe;
          IF C_DATOS_PERSONA%NOTFOUND THEN
            Lr_PersonaRecibe := NULL;
          END IF;
          CLOSE C_DATOS_PERSONA;

      IF Ln_CantRegistroAcc = 0 AND Ln_CantRegistroIai > 0 THEN   
      
    ------- Insertar registro en ARAF_CONTROL_CUSTODIO--------
    
    -- Obtiene ultimo id_instalacion y id_articulo
          IF C_ID_INSTALACION%ISOPEN THEN CLOSE C_ID_INSTALACION; END IF;
          OPEN C_ID_INSTALACION (Pv_NumeroSerie);
          FETCH C_ID_INSTALACION INTO Lv_IdInstalacion;
          IF C_ID_INSTALACION%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro registro ';
            RAISE Le_Error;
          END IF;
          CLOSE C_ID_INSTALACION;
          
    --
        INSERT INTO NAF47_TNET.ARAF_CONTROL_CUSTODIO
          (ID_CONTROL,
           CUSTODIO_ID,
           EMPRESA_CUSTODIO_ID,
           TIPO_CUSTODIO,
           ARTICULO_ID,
           TIPO_ARTICULO,
           FECHA_INICIO,
           FECHA_FIN,
           FE_ASIGNACION,
           CANTIDAD,
           MOVIMIENTO,
           TIPO_TRANSACCION_ID,
           TRANSACCION_ID,
           TIPO_ACTIVIDAD,
           CASO_ID,
           TAREA_ID,
           EMPRESA_ID,
           ID_CONTROL_ORIGEN,
           CARACTERISTICA_ID,
           VALOR_BASE,
           NO_ARTICULO,
           ESTADO,
           USR_CREACION,
           FE_CREACION)
        VALUES
          (Ln_IdControlCustodio,
           Pn_IdCustodioRec,
           Lr_PersonaRecibe.Empresa_Cod,--empresa de custodio
           Lv_TipoCustodio,
           Pv_NumeroSerie,
           Pv_TipoArticulo,
           TRUNC(SYSDATE),
           ADD_MONTHS(LAST_DAY(TRUNC(SYSDATE)),1200),
           TRUNC(SYSDATE),
           Pn_CantidadRec,
           Pn_CantidadEnt ,
           NVL(Lv_TipoTransaccion,'SinTipo'),
           Lv_IdTransaccion,
           NVL(Lv_TipoActividad,'Regularizacion'),
           0,
           NVL(Ln_IdTarea,0),
           Lr_PersonaRecibe.Empresa_Cod,
           Pn_IdControl,
           0,
           0,
           Lv_IdInstalacion.Id_Articulo,
           Lv_EstadoArf,
           NVL(Pv_LoginProcesa,USER),
           SYSDATE);

    ---------Actualiza id_custodio y cedula en in_articulo_instalacion----

    -- Recupera datos de persona que será nuevo custodio
          IF C_DATOS_PERSONA%ISOPEN THEN
            CLOSE C_DATOS_PERSONA;
          END IF;
          --
          OPEN C_DATOS_PERSONA(Pn_IdCustodioRec);
          FETCH C_DATOS_PERSONA INTO Lr_PersonaRecibe;
          IF C_DATOS_PERSONA%NOTFOUND THEN
            Lr_PersonaRecibe := NULL;
          END IF;
          CLOSE C_DATOS_PERSONA;

      -- Obtiene en nro_emple
          IF C_EMPLEADO%ISOPEN THEN CLOSE C_EMPLEADO; END IF;
          OPEN C_EMPLEADO (Lr_PersonaRecibe.Login);
          FETCH C_EMPLEADO INTO Lv_IdEmpleadoArplme;

          IF C_EMPLEADO%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro empleado definido en ARPLME, Login: ' 
                            || Lr_PersonaRecibe.Login || ', Cedula: '
                            || Lr_PersonaRecibe.Identificacion_cliente || ', favor revisar';
            RAISE Le_Error;
          END IF;
          CLOSE C_EMPLEADO;
          

          UPDATE IN_ARTICULOS_INSTALACION IAI
             SET IAI.ID_CUSTODIO  = Lv_IdEmpleadoArplme.No_Emple,
                 IAI.CEDULA       = Lr_PersonaRecibe.Identificacion_Cliente,
                 IAI.SALDO        = 1,
                 IAI.USR_ULT_MOD  = Pv_LoginProcesa,
                 IAI.FE_ULT_MOD   = SYSDATE
             WHERE IAI.NUMERO_SERIE = Pv_NumeroSerie
             AND ID_INSTALACION = Lv_IdInstalacion.Id_Instalacion;
            
      END IF;

  ----------------------------------------------------------
  -- Caso 2: Si registro esta en in_articulos_instalacion y tambien en araf_control_custodio
  -- pero los custodio no coinciden, actualiza/inserta registro en araf_control_custodio 
  -- y se actualiza en in_articulos_instalacion
  ----------------------------------------------------

     IF Ln_CantRegistroAcc > 0 AND Ln_CantRegistroIai > 0 THEN

    ------- Actualizar registro del custodio anterior --------

    -- Recupera datos de persona que entrega por id_control
       IF C_DATOS_CUSTODIO_ENTREGA%ISOPEN THEN
         CLOSE C_DATOS_CUSTODIO_ENTREGA;
       END IF;
       OPEN C_DATOS_CUSTODIO_ENTREGA;
       FETCH C_DATOS_CUSTODIO_ENTREGA INTO Lr_ControlCustodioEntrega;
       IF C_DATOS_CUSTODIO_ENTREGA%NOTFOUND THEN
         Lr_ControlCustodioEntrega := NULL;
       END IF;
       CLOSE C_DATOS_CUSTODIO_ENTREGA;

    
       UPDATE ARAF_CONTROL_CUSTODIO ACC
         SET ACC.FECHA_FIN  = Ld_FechaInicio - 1,
           ACC.ESTADO       = AFK_CONTROL_CUSTODIO.Gc_EstadoProcesado,
           ACC.USR_ULT_MOD  = Pv_LoginProcesa,
           ACC.OBSERVACION  = 'Actualizacion Custodio',
           ACC.FE_ULT_MOD   = SYSDATE
       WHERE ACC.ID_CONTROL = Lr_ControlCustodioEntrega.Id_Control;

       ------- Insertar registro en ARAF_CONTROL_CUSTODIO--------
       
       -- Obtiene ultimo id_instalacion y id_articulo
          IF C_ID_INSTALACION%ISOPEN THEN CLOSE C_ID_INSTALACION; END IF;
          OPEN C_ID_INSTALACION (Pv_NumeroSerie);
          FETCH C_ID_INSTALACION INTO Lv_IdInstalacion;
          IF C_ID_INSTALACION%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro la serie:  ' || Pv_NumeroSerie;
            RAISE Le_Error;
          END IF;
          CLOSE C_ID_INSTALACION;
          
       --como es un nuevo registro

       Ln_IdControlCustodio := NAF47_TNET.SEQ_ARAF_CONTROL_CUSTODIO.NEXTVAL;

        INSERT INTO NAF47_TNET.ARAF_CONTROL_CUSTODIO
          (ID_CONTROL,
           CUSTODIO_ID,
           EMPRESA_CUSTODIO_ID,
           TIPO_CUSTODIO,
           ARTICULO_ID,
           TIPO_ARTICULO,
           FECHA_INICIO,
           FECHA_FIN,
           FE_ASIGNACION,
           CANTIDAD,
           MOVIMIENTO,
           TIPO_TRANSACCION_ID,
           TRANSACCION_ID,
           TIPO_ACTIVIDAD,
           CASO_ID,
           TAREA_ID,
           EMPRESA_ID,
           ID_CONTROL_ORIGEN,
           CARACTERISTICA_ID,
           VALOR_BASE,
           NO_ARTICULO,
           ESTADO,
           USR_CREACION,
           FE_CREACION)
        VALUES
          (Ln_IdControlCustodio,
           Pn_IdCustodioRec,
           Lr_ControlCustodioEntrega.Empresa_Custodio_Id,--empresa de custodio
           Lv_TipoCustodio,
           Pv_NumeroSerie,
           Pv_TipoArticulo,
           TRUNC(SYSDATE),
           ADD_MONTHS(LAST_DAY(TRUNC(SYSDATE)),1200),
           TRUNC(SYSDATE),
           Pn_CantidadRec,
           Pn_CantidadEnt,
           NVL(Lv_TipoTransaccion,'SinTipo'),
           Lv_IdTransaccion,
           NVL(Lv_TipoActividad,'Regularizacion'),
           0,
           NVL(Ln_IdTarea,0),
           Lr_PersonaRecibe.Empresa_Cod,
           Pn_IdControl,
           0,
           0,
           Lv_IdInstalacion.Id_Articulo,
           Lv_EstadoArf,
           NVL(Pv_LoginProcesa,USER),
           SYSDATE);

    ---------Actualiza id_custodio y cedula en in_articulo_instalacion----

    -- Recupera datos de persona que será nuevo custodio
          IF C_DATOS_PERSONA%ISOPEN THEN
            CLOSE C_DATOS_PERSONA;
          END IF;
          --
          OPEN C_DATOS_PERSONA(Pn_IdCustodioRec);
          FETCH C_DATOS_PERSONA INTO Lr_PersonaRecibe;
          IF C_DATOS_PERSONA%NOTFOUND THEN
            Lr_PersonaRecibe := NULL;
          END IF;
          CLOSE C_DATOS_PERSONA;

      -- Obtiene en nro_emple
          IF C_EMPLEADO%ISOPEN THEN CLOSE C_EMPLEADO; END IF;
          OPEN C_EMPLEADO (Lr_PersonaRecibe.Login);
          FETCH C_EMPLEADO INTO Lv_IdEmpleadoArplme;
          IF C_EMPLEADO%NOTFOUND THEN
            Pv_Status := 'OK';
            Pv_MensajeError := 'No se encontro empleado definido en ARPLME, Login: ' 
                            || Lr_PersonaRecibe.Login || ', Cedula: '
                            || Lr_PersonaRecibe.Identificacion_cliente || ', favor revisar';
            RAISE Le_Error;
          END IF;
          CLOSE C_EMPLEADO;

          UPDATE IN_ARTICULOS_INSTALACION IAI
            SET IAI.ID_CUSTODIO  = Lv_IdEmpleadoArplme.No_Emple,
                IAI.CEDULA       = Lr_PersonaRecibe.Identificacion_Cliente,
                IAI.SALDO        = 1,
                IAI.USR_ULT_MOD  = Pv_LoginProcesa,
                IAI.FE_ULT_MOD   = SYSDATE
            WHERE IAI.NUMERO_SERIE = Pv_NumeroSerie
            AND ID_INSTALACION = Lv_IdInstalacion.Id_Instalacion;

      END IF;
      
  ----------------------------------------------------------
  -- Caso 3: Si registro no esta en in_articulos_instalacion y esta en araf_control_custodio
  --  actualiza/inserta registro en araf_control_custodio 
  -- y se inserta registro en in_articulos_instalacion
  ----------------------------------------------------
     IF Ln_CantRegistroAcc > 1 AND Ln_CantRegistroIai = 0 THEN     
     
        --- Actualiza custodio anterior-----
        
          IF C_ID_CONTROL_CUSTODIO%ISOPEN THEN CLOSE C_ID_CONTROL_CUSTODIO; END IF;
          OPEN C_ID_CONTROL_CUSTODIO (Pv_NumeroSerie);
          FETCH C_ID_CONTROL_CUSTODIO INTO Lr_IdControlCustodio;
          IF C_ID_CONTROL_CUSTODIO%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro custodio anterior para serie:  ' || Pv_NumeroSerie;
            RAISE Le_Error;
          END IF;
          CLOSE C_ID_CONTROL_CUSTODIO;

          UPDATE ARAF_CONTROL_CUSTODIO ACC
          SET ACC.FECHA_FIN   = Ld_FechaInicio - 1,
              ACC.ESTADO = AFK_CONTROL_CUSTODIO.Gc_EstadoProcesado,
              ACC.USR_ULT_MOD = Pv_LoginProcesa,
              ACC.OBSERVACION  = 'Actualizacion Custodio',
              ACC.FE_ULT_MOD  = SYSDATE
          WHERE ACC.ID_CONTROL = Lr_IdControlCustodio.Id_Control;
          
        
        ----Inserta Nuevo custodio --------
        
          IF C_ID_INSTALACION%ISOPEN THEN CLOSE C_ID_INSTALACION; END IF;
          OPEN C_ID_INSTALACION (Pv_NumeroSerie);
          FETCH C_ID_INSTALACION INTO Lv_IdInstalacion;
          IF C_ID_INSTALACION%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro la serie:  ' || Pv_NumeroSerie;
            RAISE Le_Error;
          END IF;
          CLOSE C_ID_INSTALACION;

          Ln_IdControlCustodio := NAF47_TNET.SEQ_ARAF_CONTROL_CUSTODIO.NEXTVAL;

           INSERT INTO NAF47_TNET.ARAF_CONTROL_CUSTODIO
              (ID_CONTROL,
               CUSTODIO_ID,
               EMPRESA_CUSTODIO_ID,
               TIPO_CUSTODIO,
               ARTICULO_ID,
               TIPO_ARTICULO,
               FECHA_INICIO,
               FECHA_FIN,
               FE_ASIGNACION,
               CANTIDAD,
               MOVIMIENTO,
               TIPO_TRANSACCION_ID,
               TRANSACCION_ID,
               TIPO_ACTIVIDAD,
               CASO_ID,
               TAREA_ID,
               EMPRESA_ID,
               ID_CONTROL_ORIGEN,
               CARACTERISTICA_ID,
               VALOR_BASE,
               NO_ARTICULO,
               ESTADO,
               USR_CREACION,
               FE_CREACION)
            VALUES
              (Ln_IdControlCustodio,
               Pn_IdCustodioRec,
               Lr_ControlCustodioEntrega.Empresa_Custodio_Id,--empresa de custodio
               Lv_TipoCustodio,
               Pv_NumeroSerie,
               Pv_TipoArticulo,
               TRUNC(SYSDATE),
               ADD_MONTHS(LAST_DAY(TRUNC(SYSDATE)),1200),
               TRUNC(SYSDATE),
               Pn_CantidadRec,
               Pn_CantidadEnt,
               NVL(Lv_TipoTransaccion,'SinTipo'),
               Lv_IdTransaccion,
               NVL(Lv_TipoActividad,'Regularizacion'),
               0,
               NVL(Ln_IdTarea,0),
               Lr_PersonaRecibe.Empresa_Cod,
               Pn_IdControl,
               0,
               0,
               Lv_IdInstalacion.Id_Articulo,
               AFK_CONTROL_CUSTODIO.Gc_EstadoAsignado,
               NVL(Pv_LoginProcesa,USER),
               SYSDATE);
      
     
     -------Insertar nuevo registro en IN_ARTICULOS_INSTALACION----------
     
     -- Recupera datos de persona que será nuevo custodio
          IF C_DATOS_PERSONA%ISOPEN THEN
            CLOSE C_DATOS_PERSONA;
          END IF;
          --
          OPEN C_DATOS_PERSONA(Pn_IdCustodioRec);
          FETCH C_DATOS_PERSONA INTO Lr_PersonaRecibe;
          IF C_DATOS_PERSONA%NOTFOUND THEN
            Lr_PersonaRecibe := NULL;
          END IF;
          CLOSE C_DATOS_PERSONA;

      -- Obtiene en nro_emple
          IF C_EMPLEADO%ISOPEN THEN CLOSE C_EMPLEADO; END IF;
          OPEN C_EMPLEADO (Lr_PersonaRecibe.Login);
          FETCH C_EMPLEADO INTO Lv_IdEmpleadoArplme;
          IF C_EMPLEADO%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro empleado definido en ARPLME, Login: ' 
                            || Lr_PersonaRecibe.Login || ', Cedula: '
                            || Lr_PersonaRecibe.Identificacion_cliente || ', favor revisar';
            RAISE Le_Error;
          END IF;
          CLOSE C_EMPLEADO;
          
          IF C_INV_NUM_SERIE%ISOPEN THEN CLOSE C_INV_NUM_SERIE; END IF;
          OPEN C_INV_NUM_SERIE (Pv_NumeroSerie);
          FETCH C_INV_NUM_SERIE INTO Lr_InvNumSerie;
          IF C_INV_NUM_SERIE%NOTFOUND THEN
            Pv_Status := 'ERROR';
            Pv_MensajeError := 'No se encontro registro en base para serie: ' 
                            || Pv_NumeroSerie;
            RAISE Le_Error;
          END IF;
          CLOSE C_INV_NUM_SERIE;
          
         Ln_Secuencia := Ln_Secuencia + 1;
         Ln_IdInstalacion := 1;
          
          INSERT INTO IN_ARTICULOS_INSTALACION
              (ID_COMPANIA,
               ID_CENTRO,
               ID_INSTALACION,
               SECUENCIA,
               ID_ARTICULO,
               NUMERO_SERIE,
               TIPO_ARTICULO,
               CANTIDAD,
               SALDO,
               COSTO,
               PRECIO_VENTA,
               FECHA,
               ID_EMPRESA_CUSTODIO,
               ID_CUSTODIO,
               CEDULA,
               ID_BODEGA,
               NOMBRE_BODEGA,
               ESTADO,
               USR_CREACION,
               FE_CREACION,
               TIPO_PROCESO)
        VALUES
              (10,
               '02',
               Ln_IdInstalacion,
               Ln_Secuencia,
               Lr_InvNumSerie.No_Articulo,
               Pv_NumeroSerie,
               'AF',
               Pn_CantidadRec,
               1,
               0,
               0,
               SYSDATE,
               Lr_ControlCustodioEntrega.Empresa_Custodio_Id,--empresa de custodio
               Lv_IdEmpleadoArplme.No_Emple,
               Lr_PersonaRecibe.Identificacion_Cliente,
               'GUAY',
               'BODEGA GUAYAQUIL',
               Gc_EstadoAsignado,
               NVL(Pv_LoginProcesa,USER),
               SYSDATE,
               'IN');    
        
      END IF;     
         
      Pv_Status := 'OK';
      Pv_MensajeError := 'Proceso realizado con exito!';
   EXCEPTION
      WHEN Le_Error THEN
        ROLLBACK;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                           'AFK_REGULARIZA_CUSTODIO.P_CAMBIO_CUSTODIO',
                                           Pv_MensajeError,
                                           NVL(SYS_CONTEXT('USERENV','HOST'),'NAF47_TNET'), SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),'127.0.0.1'));
      WHEN OTHERS THEN
        Pv_Status := 'ERROR';
        Pv_MensajeError := Pn_IdControl || ' - ' || Ln_IdControlCustodio || ' - ' || SQLERRM || ' - ' ||
                         DBMS_UTILITY.FORMAT_ERROR_BACKTRACE;
        ROLLBACK;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+' ,
                                           'AFK_CONTROL_CUSTODIO.P_CAMBIO_CUSTODIO',
                                           Pv_MensajeError,
                                           NVL(SYS_CONTEXT('USERENV','HOST'), 'NAF47_TNET'), SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1')); 
  END P_CAMBIO_CUSTODIO;

END AFK_REGULARIZA_CUSTODIO;
/