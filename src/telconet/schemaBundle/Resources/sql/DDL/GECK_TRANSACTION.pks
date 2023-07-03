create or replace package DB_GENERAL.GECK_TRANSACTION is

  -- Author  : SFERNANDEZ
  -- Created : 14/09/2016 15:22:02
  -- Purpose : 
    PROCEDURE P_TRIGGER_DEPARTAMENTO (Pv_Nnocia  IN Varchar2,
                                      Pv_Onocia  IN Varchar2,
                                      Pv_NnoArea IN Varchar2,
                                      Pv_OnoArea IN Varchar2,
                                      Pv_NDescri IN Varchar2,
                                      Pv_ODescri IN Varchar2);

    /**
    * Documentacion para el procedimiento P_INSERT_PARAMETRO_HIST
    *
    * Método que inserta registros en la tabla ADMI_PARAMETRO_HIST
    *
    * @param Pr_AdmiParamtrosHist IN DB_GENERAL.ADMI_PARAMETRO_HIST%ROWTYPE Objecto con la información que se debe ingresar
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 18-07-2017
    */
    PROCEDURE P_INSERT_PARAMETRO_HIST(
      Pr_AdmiParamtrosHist IN DB_GENERAL.ADMI_PARAMETRO_HIST%ROWTYPE);

    /**
    * Documentacion para el procedimiento P_INSERT_INFO_TRANSACCIONES
    *
    * Método que inserta registros en la tabla INFO_TRANSACCIONES
    *
    * @param Pr_InfoTransacciones IN DB_SEGURIDAD.INFO_TRANSACCIONES%ROWTYPE Objecto con la información que se debe ingresar
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 08-08-2017
    */
    PROCEDURE P_INSERT_INFO_TRANSACCIONES(
      Pr_InfoTransacciones IN DB_SEGURIDAD.INFO_TRANSACCIONES%ROWTYPE);

    /**
    * Documentacion para el procedimiento P_UPDATE_INFO_TRANSACCIONES
    *
    * Método que actualiza un registro en la tabla INFO_TRANSACCIONES según el id enviado como parámetro.
    *
    * @param Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE Id de la transacción
    * @param Pv_UsrUltMod     IN DB_SEGURIDAD.INFO_TRANSACCIONES.USR_ULT_MOD%TYPE    Usuario que modifica
    * @param Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.IP_ULT_MO%TYPE   Ip del usuario que modifica.
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 08-08-2017
    */
    PROCEDURE P_UPDATE_INFO_TRANSACCIONES(
      Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE,
      Pv_UsrUltMod     IN DB_SEGURIDAD.INFO_TRANSACCIONES.USR_ULT_MOD%TYPE,
      Pv_IpUltMod      IN DB_SEGURIDAD.INFO_TRANSACCIONES.IP_ULT_MOD%TYPE);

    /* Documentacion para el procedimiento P_UPDATE_INFO_TRANSACCIONES
    *
    * Método que actualiza la url del zip en la tabla INFO_TRANSACCIONES según el id enviado como parámetro.
    *
    * @param Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE      Id de la transacción
    * @param Pv_PathNFS       IN DB_SEGURIDAD.INFO_TRANSACCIONES.NOMBRE_TRANSACCION%TYPE  Url del reporte zip  
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.0 25-02-2022
    */
    PROCEDURE P_UPDATE_URL_INFOTRANSAC(
      Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE,
      Pv_PathNFS     IN DB_SEGURIDAD.INFO_TRANSACCIONES.NOMBRE_TRANSACCION%TYPE);

    /**
    * Documentacion para el procedimiento P_INSERT_PARAMETRO_DET
    *
    * Método que inserta registros en la tabla ADMI_PARAMETRO_DET
    *
    * @param Pr_AdmiParamtrosDet IN DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE Objecto con la información que se debe ingresar
    *
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 25-10-2018
    */
    PROCEDURE P_INSERT_PARAMETRO_DET(
      Pr_AdmiParamtrosDet IN DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE);
      
end GECK_TRANSACTION;
/
create or replace package body DB_GENERAL.GECK_TRANSACTION is
     
  /**
  * Documentacion para el procedimiento P_TRIGGER_DEPARTAMENTO
  * Procedimiento que reemplaza trigger de sincronizacion naf departamentos con telcos departamentos
  * @author Sof�a Fern�ndez <sfernandez@telconet.ec>
  * @version 1.0 14-09-2016
  */
    PROCEDURE P_TRIGGER_DEPARTAMENTO (Pv_Nnocia  IN Varchar2,
                                      Pv_Onocia  IN Varchar2,
                                      Pv_NnoArea IN Varchar2,
                                      Pv_OnoArea IN Varchar2,
                                      Pv_NDescri IN Varchar2,
                                      Pv_ODescri IN Varchar2)IS

        v_cod_empresa               db_comercial.info_empresa_grupo.cod_empresa%type;
        v_id_departamento           db_general.admi_departamento.id_departamento%TYPE;
        v_area_id                   db_general.admi_departamento.area_id%TYPE;
        v_nombre_area               db_general.admi_area.nombre_area%TYPE;
        v_usr_ult_mod               db_general.admi_departamento.usr_ult_mod%TYPE;
        v_fe_ult_mod                db_general.admi_departamento.fe_ult_mod%TYPE;
      
        CURSOR c_existe_empresa(v_no_cia naf47_tnet.arcgmc.no_cia%TYPE) IS
          SELECT cod_empresa
          FROM db_comercial.info_empresa_grupo
          WHERE cod_empresa = v_no_cia;

        CURSOR c_existe_departamento(v_departamento naf47_tnet.arpldp.descri%type, v_cia naf47_tnet.arplar.no_cia%type) IS
          SELECT id_departamento
            FROM db_general.admi_departamento
           WHERE UPPER(nombre_departamento) = UPPER(v_departamento)
             AND empresa_cod = v_cia;
           
        CURSOR c_nombre_area(v_cia naf47_tnet.arplar.no_cia%type, v_area naf47_tnet.arplar.area%TYPE) IS
          SELECT DESCRI
            FROM ARPLAR
           WHERE AREA = v_area
             AND NO_CIA = v_cia;

        CURSOR c_existe_area(v_cia naf47_tnet.arplar.no_cia%type, v_area naf47_tnet.arplar.area%TYPE) IS
          SELECT id_area
            FROM db_general.admi_area
           WHERE empresa_cod = v_cia
             AND UPPER(nombre_area) = UPPER((SELECT DESCRI
                                  FROM ARPLAR
                                 WHERE AREA = v_area
                                   AND NO_CIA = v_cia));
      BEGIN
        
      CASE
          WHEN INSERTING THEN
            OPEN  c_existe_empresa(Pv_Nnocia);
            FETCH c_existe_empresa INTO v_cod_empresa;
            IF c_existe_empresa%NOTFOUND THEN 
              v_cod_empresa := NULL;
            END IF;
            CLOSE c_existe_empresa;
          WHEN UPDATING THEN
            OPEN  c_existe_empresa(Pv_Nnocia);
            FETCH c_existe_empresa INTO v_cod_empresa;
            IF c_existe_empresa%NOTFOUND THEN 
              v_cod_empresa := NULL;
            END IF;
            CLOSE c_existe_empresa;
          WHEN DELETING THEN
            OPEN  c_existe_empresa(Pv_Onocia);
            FETCH c_existe_empresa INTO v_cod_empresa;
            IF c_existe_empresa%NOTFOUND THEN 
              v_cod_empresa := NULL;
            END IF;
            CLOSE c_existe_empresa;
        END CASE;
        
        IF v_cod_empresa IS NOT NULL THEN
          v_usr_ult_mod := LOWER(USER);
          v_fe_ult_mod  := CURRENT_TIMESTAMP;

          CASE
            WHEN INSERTING THEN
              OPEN c_existe_area(Pv_Nnocia, Pv_NnoArea);
              FETCH c_existe_area
                INTO v_area_id;
              IF c_existe_area%NOTFOUND THEN
                v_area_id := NULL;
              END IF;
              CLOSE c_existe_area;
          
              IF v_area_id IS NOT NULL THEN
                OPEN c_existe_departamento(Pv_NDescri, v_cod_empresa);
                FETCH c_existe_departamento INTO v_id_departamento;
                IF c_existe_departamento%NOTFOUND THEN
                  v_id_departamento := NULL;
                END IF;
                CLOSE c_existe_departamento;
          
                IF v_id_departamento IS NOT NULL THEN
                  UPDATE DB_GENERAL.ADMI_DEPARTAMENTO SET NOMBRE_DEPARTAMENTO = INITCAP(Pv_NDescri), AREA_ID = v_area_id, ESTADO = 'Modificado', USR_ULT_MOD = v_usr_ult_mod, FE_ULT_MOD = v_fe_ult_mod, EMPRESA_COD = v_cod_empresa WHERE ID_DEPARTAMENTO = v_id_departamento;
                ELSE
                  INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod,empresa_cod)
                                                    VALUES(DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_NDescri),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod,v_cod_empresa);
                END IF;
              ELSE
                OPEN c_nombre_area(Pv_Nnocia,Pv_NnoArea);
                FETCH c_nombre_area INTO v_nombre_area;
                CLOSE c_nombre_area;
              
                v_area_id := DB_GENERAL.SEQ_ADMI_AREA.NEXTVAL;
            
                INSERT INTO DB_GENERAL.ADMI_AREA(id_area,nombre_area,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                          VALUES(v_area_id,INITCAP(v_nombre_area),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
            
                INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                                  VALUES(DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_NDescri),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
              END IF;
            WHEN UPDATING THEN
              OPEN c_existe_departamento(Pv_ODescri, v_cod_empresa);
              FETCH c_existe_departamento INTO v_id_departamento;
              IF c_existe_departamento%NOTFOUND THEN
                v_id_departamento := NULL;
              END IF;
              CLOSE c_existe_departamento;
          
              IF v_id_departamento IS NOT NULL THEN
                OPEN c_existe_area(Pv_Nnocia,Pv_NnoArea);
                FETCH c_existe_area INTO v_area_id;
                IF c_existe_area%NOTFOUND THEN
                  v_area_id := NULL;
                END IF;
                CLOSE c_existe_area;
            
                IF v_area_id IS NOT NULL THEN
                  UPDATE DB_GENERAL.ADMI_DEPARTAMENTO SET AREA_ID = v_area_id, NOMBRE_DEPARTAMENTO = INITCAP(Pv_NDescri), USR_ULT_MOD = v_usr_ult_mod, FE_ULT_MOD = v_fe_ult_mod, ESTADO = 'Modificado', EMPRESA_COD = v_cod_empresa WHERE ID_DEPARTAMENTO = v_id_departamento;
                ELSE
                  OPEN c_nombre_area(Pv_Nnocia, Pv_NnoArea);
                  FETCH c_nombre_area INTO v_nombre_area;
                  CLOSE c_nombre_area;
              
                  v_area_id := DB_GENERAL.SEQ_ADMI_AREA.NEXTVAL;
              
                  INSERT INTO DB_GENERAL.ADMI_AREA(id_area,nombre_area,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                            VALUES(v_area_id,INITCAP(v_nombre_area),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                  UPDATE DB_GENERAL.ADMI_DEPARTAMENTO SET AREA_ID = v_area_id, NOMBRE_DEPARTAMENTO = INITCAP(Pv_NDescri), USR_ULT_MOD = v_usr_ult_mod, FE_ULT_MOD = v_fe_ult_mod, ESTADO = 'Modificado', EMPRESA_COD = v_cod_empresa WHERE ID_DEPARTAMENTO = v_id_departamento;
                END IF;
              ELSE
                OPEN c_existe_area(Pv_Nnocia,Pv_NnoArea);
                FETCH c_existe_area INTO v_area_id;
                IF c_existe_area%NOTFOUND THEN
                  v_area_id := NULL;
                END IF;
                CLOSE c_existe_area;
            
                IF v_area_id IS NOT NULL THEN 
                  INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                                   VALUES (DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_NDescri),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                ELSE
                  OPEN c_nombre_area(Pv_Nnocia,Pv_NnoArea);
                  FETCH c_nombre_area INTO v_nombre_area;
                  IF c_nombre_area%NOTFOUND THEN
                    v_nombre_area := NULL;
                  END IF;
                  CLOSE c_nombre_area;
              
                  v_area_id := DB_GENERAL.SEQ_ADMI_AREA.NEXTVAL;
              
                  INSERT INTO DB_GENERAL.ADMI_AREA(id_area,nombre_area,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                            VALUES(v_area_id,INITCAP(v_nombre_area),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                  INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                                    VALUES(DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_NDescri),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                END IF;
              END IF;
            WHEN DELETING THEN
              OPEN c_existe_departamento(Pv_ODescri, v_cod_empresa);
              FETCH c_existe_departamento INTO v_id_departamento;
              IF c_existe_departamento%NOTFOUND THEN
                v_id_departamento := NULL;
              END IF;
              CLOSE c_existe_departamento;
          
              IF v_id_departamento IS NOT NULL THEN
                UPDATE DB_GENERAL.ADMI_DEPARTAMENTO SET USR_ULT_MOD = v_usr_ult_mod, FE_ULT_MOD = v_fe_ult_mod, ESTADO = 'Eliminado', EMPRESA_COD = v_cod_empresa WHERE ID_DEPARTAMENTO = v_id_departamento;
              ELSE
                OPEN c_existe_area(Pv_Onocia,Pv_OnoArea);
                FETCH c_existe_area INTO v_area_id;
                IF c_existe_area%NOTFOUND THEN
                  v_area_id := NULL;
                END IF;
                CLOSE c_existe_area;
            
                IF v_area_id IS NOT NULL THEN
                  INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                                    VALUES(DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_NDescri),'Eliminado',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                ELSE
                  OPEN c_nombre_area(Pv_Onocia,Pv_OnoArea);
                  FETCH c_nombre_area INTO v_nombre_area;
                  CLOSE c_nombre_area;
              
                  v_area_id := DB_GENERAL.SEQ_ADMI_AREA.NEXTVAL;
              
                  INSERT INTO DB_GENERAL.ADMI_AREA(id_area,nombre_area,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                            VALUES(v_area_id,INITCAP(v_nombre_area),'Activo',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod, v_cod_empresa);
                  INSERT INTO DB_GENERAL.ADMI_DEPARTAMENTO(id_departamento,area_id,nombre_departamento,estado,usr_creacion,fe_creacion,usr_ult_mod,fe_ult_mod, empresa_cod)
                                                    VALUES(DB_GENERAL.SEQ_ADMI_DEPARTAMENTO.NEXTVAL,v_area_id,INITCAP(Pv_ODescri),'Eliminado',v_usr_ult_mod,v_fe_ult_mod,v_usr_ult_mod,v_fe_ult_mod,v_cod_empresa);
                END IF;
              END IF;
            ELSE
              NULL;
          END CASE;
        END IF;     
    END P_TRIGGER_DEPARTAMENTO;

    PROCEDURE P_INSERT_PARAMETRO_HIST(
      Pr_AdmiParamtrosHist IN DB_GENERAL.ADMI_PARAMETRO_HIST%ROWTYPE)
    IS
    BEGIN
      --
      --
      INSERT
      INTO DB_GENERAL.ADMI_PARAMETRO_HIST
        (
          ID_PARAMETRO_HIST, 
          ID_PARAMETRO_DET, 
          PARAMETRO_ID, 
          DESCRIPCION, 
          VALOR1, 
          VALOR2, 
          VALOR3, 
          VALOR4, 
          ESTADO, 
          USR_CREACION, 
          FE_CREACION, 
          IP_CREACION, 
          USR_ULT_MOD, 
          FE_ULT_MOD, 
          IP_ULT_MOD, 
          VALOR5, 
          EMPRESA_COD,
          USR_CREACION_HIST, 
          FE_CREACION_HIST, 
          IP_CREACION_HIST,
          HOST_CREACION_HIST,
          OBSERVACION 
        )
        VALUES
        (
          DB_GENERAL.SEQ_ADMI_PARAMETRO_HIST.NEXTVAL,
          Pr_AdmiParamtrosHist.ID_PARAMETRO_DET,
          Pr_AdmiParamtrosHist.PARAMETRO_ID,
          Pr_AdmiParamtrosHist.DESCRIPCION,
          Pr_AdmiParamtrosHist.VALOR1,
          Pr_AdmiParamtrosHist.VALOR2,
          Pr_AdmiParamtrosHist.VALOR3,
          Pr_AdmiParamtrosHist.VALOR4,
          Pr_AdmiParamtrosHist.ESTADO,
          Pr_AdmiParamtrosHist.USR_CREACION,
          Pr_AdmiParamtrosHist.FE_CREACION,
          Pr_AdmiParamtrosHist.IP_CREACION,
          Pr_AdmiParamtrosHist.USR_ULT_MOD,
          Pr_AdmiParamtrosHist.FE_ULT_MOD,
          Pr_AdmiParamtrosHist.IP_ULT_MOD,
          Pr_AdmiParamtrosHist.VALOR5,
          Pr_AdmiParamtrosHist.EMPRESA_COD,
          Pr_AdmiParamtrosHist.USR_CREACION_HIST,
          Pr_AdmiParamtrosHist.FE_CREACION_HIST,
          Pr_AdmiParamtrosHist.IP_CREACION_HIST,
          Pr_AdmiParamtrosHist.HOST_CREACION_HIST,
          Pr_AdmiParamtrosHist.OBSERVACION
        );

    END P_INSERT_PARAMETRO_HIST;

    PROCEDURE P_INSERT_INFO_TRANSACCIONES(
      Pr_InfoTransacciones IN DB_SEGURIDAD.INFO_TRANSACCIONES%ROWTYPE)
    IS
    BEGIN
      --
      --
      INSERT
      INTO DB_SEGURIDAD.INFO_TRANSACCIONES
        (
          ID_TRANSACCION, 
          NOMBRE_TRANSACCION, 
          TIPO_TRANSACCION, 
          ESTADO, 
          EMPRESA_ID, 
          RELACION_SISTEMA_ID,
          FE_CREACION,
          USR_CREACION,
          IP_CREACION, 
          FE_ULT_MOD, 
          USR_ULT_MOD, 
          IP_ULT_MOD
        )
        VALUES
        (
          Pr_InfoTransacciones.ID_TRANSACCION,
          Pr_InfoTransacciones.NOMBRE_TRANSACCION,
          Pr_InfoTransacciones.TIPO_TRANSACCION,
          Pr_InfoTransacciones.ESTADO,
          Pr_InfoTransacciones.EMPRESA_ID,
          Pr_InfoTransacciones.RELACION_SISTEMA_ID,
          Pr_InfoTransacciones.FE_CREACION,
          Pr_InfoTransacciones.USR_CREACION,
          Pr_InfoTransacciones.IP_CREACION,
          Pr_InfoTransacciones.FE_ULT_MOD,
          Pr_InfoTransacciones.USR_ULT_MOD,
          Pr_InfoTransacciones.IP_ULT_MOD
        );

    END P_INSERT_INFO_TRANSACCIONES;


    PROCEDURE P_UPDATE_INFO_TRANSACCIONES(
      Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE,
      Pv_UsrUltMod     IN DB_SEGURIDAD.INFO_TRANSACCIONES.USR_ULT_MOD%TYPE,
      Pv_IpUltMod      IN DB_SEGURIDAD.INFO_TRANSACCIONES.IP_ULT_MOD%TYPE)
    IS
    BEGIN
      --
      --
      UPDATE
        DB_SEGURIDAD.INFO_TRANSACCIONES
      SET 
        ESTADO      = 'Activo',
        FE_ULT_MOD  = SYSDATE,
        USR_ULT_MOD = Pv_UsrUltMod,
        IP_ULT_MOD  = Pv_IpUltMod
      WHERE ID_TRANSACCION =  Pn_IdTransaccion
      AND ESTADO = 'Pendiente';

    END P_UPDATE_INFO_TRANSACCIONES;

    PROCEDURE P_UPDATE_URL_INFOTRANSAC(
      Pn_IdTransaccion IN DB_SEGURIDAD.INFO_TRANSACCIONES.ID_TRANSACCION%TYPE,
      Pv_PathNFS     IN DB_SEGURIDAD.INFO_TRANSACCIONES.NOMBRE_TRANSACCION%TYPE)
    IS
    BEGIN
      --
      UPDATE
        DB_SEGURIDAD.INFO_TRANSACCIONES
      SET 
        NOMBRE_TRANSACCION = Pv_PathNFS
      WHERE ID_TRANSACCION = Pn_IdTransaccion;

    END P_UPDATE_URL_INFOTRANSAC;

    PROCEDURE P_INSERT_PARAMETRO_DET(
      Pr_AdmiParamtrosDet IN DB_GENERAL.ADMI_PARAMETRO_DET%ROWTYPE)
    IS
    BEGIN
      --
      --
      INSERT
      INTO DB_GENERAL.ADMI_PARAMETRO_DET
        (
            ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            VALOR3,
            VALOR4,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION,
            USR_ULT_MOD,
            FE_ULT_MOD,
            IP_ULT_MOD,
            VALOR5,
            EMPRESA_COD,
            VALOR6,
            VALOR7
        )
        VALUES
        (
            DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            Pr_AdmiParamtrosDet.PARAMETRO_ID,
            Pr_AdmiParamtrosDet.DESCRIPCION,
            Pr_AdmiParamtrosDet.VALOR1,
            Pr_AdmiParamtrosDet.VALOR2,
            Pr_AdmiParamtrosDet.VALOR3,
            Pr_AdmiParamtrosDet.VALOR4,
            Pr_AdmiParamtrosDet.ESTADO,
            Pr_AdmiParamtrosDet.USR_CREACION,
            Pr_AdmiParamtrosDet.FE_CREACION,
            Pr_AdmiParamtrosDet.IP_CREACION,
            Pr_AdmiParamtrosDet.USR_ULT_MOD,
            Pr_AdmiParamtrosDet.FE_ULT_MOD,
            Pr_AdmiParamtrosDet.IP_ULT_MOD,
            Pr_AdmiParamtrosDet.VALOR5,
            Pr_AdmiParamtrosDet.EMPRESA_COD,
            Pr_AdmiParamtrosDet.VALOR6,
            Pr_AdmiParamtrosDet.VALOR7
        );

    END P_INSERT_PARAMETRO_DET;

end GECK_TRANSACTION;
/
