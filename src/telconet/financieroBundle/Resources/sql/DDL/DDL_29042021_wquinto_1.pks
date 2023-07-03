/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea paquete DB_FINANCIERO.FNCK_ANULAR_PAGO para anulacion de pagos.
 *
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.1 05-07-2021 - Se modifica paquete para proceso P_OBTENER_PAGO_EXCEL y remplazar url base de consulta de archivo excel al NFS.
 */

 create or replace PACKAGE DB_FINANCIERO.FNCK_ANULAR_PAGO AS 

  /**
  * Documentación para P_OBTENER_PAGO_EXCEL
  * Procedimiento que realiza la obtencion de lista de pagos de archivo excel
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdEmpresa IN NUMBER Id de empresa que esta relacionada a la lista de pagos
  * @param Pv_NombreArchivo IN VARCHAR2 Recibe lel nombre de archivo
  * @param Prf_Pagos OUT SYS_REFCURSOR Lista de pagos obtenidos en excel
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_OBTENER_PAGO_EXCEL(Pn_IdEmpresa NUMBER,Pv_UrlFile VARCHAR2,Prf_Pagos OUT SYS_REFCURSOR,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  
  /**
  * Documentación para P_MASIVO_ANULACION
  * Procedimiento de anulacion masivo
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_MASIVO_ANULACION(Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  
  /**
  * Documentación para P_MASIVO_ANULACION_CORREO
  * Procedimiento de envio correo de anulacion masivo
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_MASIVO_ANULACION_CORREO(Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  
  /**
  * Documentación para P_ANULACION_PAGO
  * Procedimiento de anulacion masivo
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdMasivoPago IN NUMBER Id de proceso masivo
  * @param Pr_InfoPagoCab IN OUT ROWTYPE objecto de pago cab
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_ANULACION_PAGO(Pn_IdMasivoPagoCab NUMBER,Pr_InfoPagoCab IN OUT DB_FINANCIERO.info_pago_cab%ROWTYPE,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_ORIGEN_PAGO
  * Procedimiento de anulacion masivo
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdPago IN NUMBER Id de pago
  * @param Pn_IdPagoOrigen OUT NUMBER Id de pago de origen
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_ORIGEN_PAGO(Pn_IdPago NUMBER,Pn_IdPagoOrigen OUT NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_REGULARIZAR_PAGO
  * Procedimiento de regularizacion de pago
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdMasivoPago IN NUMBER Id de proceso masivo
  * @param Pr_InfoPagoCab IN OUT ROWTYPE objecto de pago cab
  * @param Pn_Tipo IN NUMBER Tipo de pago 0 origen, 2 hijo
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_REGULARIZAR_PAGO(Pn_IdMasivoPagoCab NUMBER,Pr_IdPagoCab NUMBER,Pv_MsjRef VARCHAR2,Pn_Tipo NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_CAMBIO_ESTADO_DOCUMENTO
  * Procedimiento de regularizacion de pago
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdDocumento IN NUMBER Id de documento
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_CAMBIO_ESTADO_DOCUMENTO(Pn_IdDocumento NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_ENVIAR_CORREO_PAGOS
  * Procedimiento que realiza el envio de pagos
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdMasivoPagoCab IN VARCHAR2 Id Cabecera masiva procesada
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */
  PROCEDURE P_ENVIAR_CORREO_PAGOS(Pn_IdMasivoPagoCab NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_REVERSA_CONT_PAL
  * Procedimiento que realiza el reverso contable
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pv_IdPagoDet IN VARCHAR2 Id Detalle pago info
  * @param Pv_NoCia IN VARCHAR2 Id de empresa
  * @param Pv_UsrCreacion IN VARCHAR2 usuario
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */ 
  PROCEDURE P_REVERSA_CONT_PAL(Pv_IdPagoDet IN VARCHAR2, Pv_NoCia IN VARCHAR2,Pv_UsrCreacion IN VARCHAR2,Pn_CodSalida OUT NUMBER,Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_CORTAR_SERVICIO
  * Procedimiento que realiza el corte a servicio
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pv_IdPago IN VARCHAR2 Id pago info
  * @param Pn_CodSalida OUT NUMBER Devuelve el codigo de respuesta
  * @param Pv_MsjSalida OUT VARCHAR2 Devuelve el mensaje de respuesta
  */ 
  PROCEDURE P_CORTAR_SERVICIO(Pv_IdPago IN VARCHAR2,Pn_CodSalida OUT NUMBER,Pv_MsjSalida OUT VARCHAR2);
  
  /**
  * Documentación para P_CORTAR_SERVICIO
  * Procedimiento que realiza el corte a servicio
  * 
  * @author Wilson Quinto <wquinto@telcos.ec>
  * @version 1.0 19/04/2021
  * 
  * @param Pn_IdPunto IN VARCHAR2 Id de punto
  * @return VARCHAR2 medio de ultima milla
  */ 
  FUNCTION F_GET_MEDIO_POR_PUNTO(Pn_IdPunto IN NUMBER)  RETURN VARCHAR2;

END FNCK_ANULAR_PAGO;

/

create or replace PACKAGE BODY DB_FINANCIERO.FNCK_ANULAR_PAGO AS

  PROCEDURE P_OBTENER_PAGO_EXCEL(Pn_IdEmpresa NUMBER,Pv_UrlFile VARCHAR2,Prf_Pagos OUT SYS_REFCURSOR,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  CURSOR C_PagoExcel (Cn_IdEmpresa NUMBER,Cv_NumPago VARCHAR2,Cn_valor NUMBER,Cv_Fecha VARCHAR2)
      IS
      SELECT ipc.ID_PAGO,ipc.NUMERO_PAGO,ipd.VALOR_PAGO as VALOR_TOTAL
            FROM DB_FINANCIERO.INFO_PAGO_CAB ipc 
            LEFT JOIN DB_FINANCIERO.INFO_PAGO_DET ipd ON ipd.PAGO_ID=ipc.ID_PAGO
            WHERE ipc.EMPRESA_ID=Cn_IdEmpresa and ipc.NUMERO_PAGO=Cv_NumPago and ipd.VALOR_PAGO=Cn_valor
            and TO_CHAR(ipc.FE_CREACION , 'DD-MM-YYYY')= Cv_Fecha;
  CURSOR C_GetParametro(Cv_NombreParamCab VARCHAR2)
  IS
    SELECT APD.ID_PARAMETRO_DET, APD.VALOR1, APD.VALOR2, APD.VALOR3, APD.VALOR4,APD.VALOR5
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC,
      DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE APC.ID_PARAMETRO   = APD.PARAMETRO_ID
    AND APC.ESTADO           = 'Activo'
    AND APD.ESTADO           = 'Activo'
    AND APC.NOMBRE_PARAMETRO = 'PARAM_ANULACION_PAGOS'
    AND APD.VALOR1           = Cv_NombreParamCab;
  Lr_Parametro C_GetParametro%ROWTYPE;
  Lrf_DatosExcel    SYS_REFCURSOR;
  Ln_IdProceso NUMBER(38,0);
  Lv_Url VARCHAR2(800);
  Lv_UrlContext VARCHAR2(600);
  LV_ID_PAGO NUMBER(38,0);
  LV_NUMERO_PAGO VARCHAR2(100);
  LV_VALOR VARCHAR2(100);
  LBL_EXCEL BLOB;
  Luh_http_request   UTL_HTTP.req;
  Luh_http_response  UTL_HTTP.resp;
  LRW_raw RAW(32767);
  Ln_itemLoop number;
  
  Lv_MsjResultado VARCHAR2(500);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  LV_ERROR VARCHAR2(3000);
  LE_ERROR EXCEPTION; 
BEGIN

  --Se obtiene los parámetros para enviar el correo de anulacion de pagos
  OPEN C_GetParametro('FILE-HTTP-HOST');
  FETCH C_GetParametro INTO Lr_Parametro;
  CLOSE C_GetParametro;
  DBMS_OUTPUT.PUT_LINE(Pv_UrlFile);
  SELECT REGEXP_SUBSTR(Pv_UrlFile, '^([^:]*://)?([^/]+)(/.*)?', 1, 1, null, 3) into Lv_UrlContext from dual;
  Lv_Url:=Lr_Parametro.VALOR2||Lv_UrlContext;
  DBMS_OUTPUT.PUT_LINE(Lv_Url);
  -- Initialize the BLOB.
  DBMS_LOB.createtemporary(LBL_EXCEL, FALSE);

  -- Make a HTTP request and get the response.
  Luh_http_request  := UTL_HTTP.begin_request(Lv_Url);
  Luh_http_response := UTL_HTTP.get_response(Luh_http_request);
  -- Copy the response into the BLOB.
  BEGIN
    LOOP
      UTL_HTTP.read_raw(Luh_http_response, LRW_raw, 32767);
      DBMS_LOB.writeappend (LBL_EXCEL, UTL_RAW.length(LRW_raw), LRW_raw);
    END LOOP;
  EXCEPTION
    WHEN UTL_HTTP.end_of_body THEN
      UTL_HTTP.end_response(Luh_http_response);
  END;

  DBMS_OUTPUT.PUT_LINE('lectura P');
  Ln_itemLoop:=0;
  Pv_MsjSalida := 'OK';
  Pn_CodSalida := 0;
    
    SELECT COALESCE(MAX(ID_PROCESO)+1,1) INTO Ln_IdProceso FROM DB_FINANCIERO.INFO_TEMP_ANULAR_PAGO;
    FOR pagoArchivo in (SELECT NUMERO_PAGO,VALOR,FECHA FROM  (SELECT row_nr, col_nr,
            CASE cell_type
              WHEN 'S'
              THEN string_val
              WHEN 'N'
              THEN to_char(number_val)
              WHEN 'D'
              THEN to_char(date_val,'DD-MM-YYYY')
              ELSE ''
            END cell_val 
          FROM
            (
              SELECT
                *
              FROM
                TABLE( as_read_xlsx.read( LBL_EXCEL))
            )
			) pivot ( MAX(cell_val) FOR col_nr IN (1 AS numero_pago,2 AS valor,3 AS fecha)) WHERE row_nr >1)
    LOOP
    BEGIN
      IF pagoArchivo.NUMERO_PAGO IS NULL OR pagoArchivo.VALOR IS NULL THEN
        INSERT INTO DB_FINANCIERO.INFO_TEMP_ANULAR_PAGO (ID_PROCESO,PAGO_NUMERO,PAGO_VALOR,ERROR,FE_CREACION,USR_CREACION)
        VALUES (Ln_IdProceso,pagoArchivo.NUMERO_PAGO,pagoArchivo.VALOR,1,CURRENT_DATE,'panular');
      ELSE
        FOR pagos in C_PagoExcel(Pn_IdEmpresa,pagoArchivo.NUMERO_PAGO,pagoArchivo.VALOR,pagoArchivo.FECHA)
        LOOP
          Ln_itemLoop:=1;
          INSERT INTO DB_FINANCIERO.INFO_TEMP_ANULAR_PAGO (ID_PROCESO,PAGO_ID,PAGO_NUMERO,PAGO_VALOR,ERROR,FE_CREACION,USR_CREACION)
          VALUES (Ln_IdProceso,pagos.ID_PAGO,pagos.NUMERO_PAGO,pagos.VALOR_TOTAL,0,CURRENT_DATE,'panular');
        END LOOP;
        IF Ln_itemLoop=0 THEN
          INSERT INTO DB_FINANCIERO.INFO_TEMP_ANULAR_PAGO (ID_PROCESO,PAGO_NUMERO,PAGO_VALOR,ERROR,FE_CREACION,USR_CREACION)
          VALUES (Ln_IdProceso,pagoArchivo.NUMERO_PAGO,pagoArchivo.VALOR,2,CURRENT_DATE,'panular');
        END IF;
        Ln_itemLoop:=0;
      END IF;
    END;
  END LOOP;
  DBMS_LOB.freetemporary(LBL_EXCEL);
  
  IF Prf_Pagos%ISOPEN THEN
      CLOSE Prf_Pagos;
  END IF;
  
  OPEN Prf_Pagos FOR WITH TMP_PAGOS AS (
        SELECT ipc.ID_PAGO, ipc.PUNTO_ID, ipc.NUMERO_PAGO, ipc.TIPO_DOCUMENTO_ID, ipc.USR_CREACION, ipc.FE_CREACION,
        ipc.ESTADO_PAGO, ipc.PAGO_LINEA_ID, ipd.VALOR_PAGO AS VALOR_TOTAL, ipd.FORMA_PAGO_ID, ipd.BANCO_TIPO_CUENTA_ID,
        atdf.NOMBRE_TIPO_DOCUMENTO,itap.ERROR
        FROM DB_FINANCIERO.INFO_TEMP_ANULAR_PAGO itap
        JOIN DB_FINANCIERO.INFO_PAGO_CAB ipc ON itap.PAGO_ID=ipc.ID_PAGO
        JOIN DB_FINANCIERO.INFO_PAGO_DET ipd ON ipd.PAGO_ID = ipc.ID_PAGO
        JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO atdf ON atdf.ID_TIPO_DOCUMENTO = ipc.TIPO_DOCUMENTO_ID
        WHERE itap.ID_PROCESO=Ln_IdProceso)
          SELECT tip.*,
            (SELECT acpn.NOMBRE_CANAL_PAGO_LINEA FROM DB_FINANCIERO.INFO_PAGO_LINEA  ipn
            LEFT JOIN DB_FINANCIERO.ADMI_CANAL_PAGO_LINEA acpn ON acpn.ID_CANAL_PAGO_LINEA = ipn.CANAL_PAGO_LINEA_ID
            WHERE ipn.ID_PAGO_LINEA = tip.PAGO_LINEA_ID) AS NOMBRE_CANAL_PAGO_LINEA,
            (SELECT ab.DESCRIPCION_BANCO FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA abtc
            LEFT JOIN DB_GENERAL.ADMI_BANCO ab ON ab.ID_BANCO=abtc.BANCO_ID
            WHERE abtc.ID_BANCO_TIPO_CUENTA=tip.BANCO_TIPO_CUENTA_ID) AS DESCRIPCION_BANCO,
            (SELECT afp.DESCRIPCION_FORMA_PAGO FROM DB_GENERAL.ADMI_FORMA_PAGO afp 
          WHERE afp.ID_FORMA_PAGO=tip.FORMA_PAGO_ID) AS DESCRIPCION_FORMA_PAGO,
            ipa.LOGIN,ip.IDENTIFICACION_CLIENTE,COALESCE(ip.RAZON_SOCIAL,CONCAT(CONCAT(ip.NOMBRES,' '),ip.APELLIDOS)) as NOMBRE_COMPLETO
          FROM TMP_PAGOS tip
            LEFT JOIN DB_COMERCIAL.INFO_PUNTO ipa on ipa.ID_PUNTO=tip.PUNTO_ID 
            LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper on iper.ID_PERSONA_ROL=ipa.PERSONA_EMPRESA_ROL_ID 
            LEFT JOIN DB_COMERCIAL.INFO_PERSONA ip on ip.ID_PERSONA=iper.PERSONA_ID;
            
  DELETE FROM INFO_TEMP_ANULAR_PAGO WHERE ID_PROCESO=Ln_IdProceso;
  DBMS_OUTPUT.PUT_LINE('cierre P');
  Pn_CodSalida:=0;
  Pv_MsjSalida:='OK';
EXCEPTION
      WHEN LE_ERROR THEN
        DELETE FROM INFO_TEMP_ANULAR_PAGO WHERE ID_PROCESO=Ln_IdProceso;
        DBMS_LOB.freetemporary(LBL_EXCEL);
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso obtener pago excel.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado; 
       DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_OBTENER_PAGO_EXCEL', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        DELETE FROM INFO_TEMP_ANULAR_PAGO WHERE ID_PROCESO=Ln_IdProceso;
        DBMS_LOB.freetemporary(LBL_EXCEL);
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso obtener pago excel.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_OBTENER_PAGO_EXCEL', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_OBTENER_PAGO_EXCEL;
  
  PROCEDURE P_MASIVO_ANULACION(Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
    CURSOR Lc_MasPagoCab
      IS
      select ID_PROCESO_MASIVO_CAB,EMPRESA_ID from db_infraestructura.info_proceso_masivo_Cab where ESTADO='Pendiente' and TIPO_PROCESO='AnulacionPagoCliente' ORDER BY ID_PROCESO_MASIVO_CAB ASC;
    CURSOR Lc_MasPagoDet (Cn_IdProcesCab NUMBER)
      IS
      SELECT max(ID_PROCESO_MASIVO_DET) as ID_PROCESO_MASIVO_DET,max(ESTADO) as ESTADO,PAGO_ID FROM db_infraestructura.info_proceso_masivo_det where PROCESO_MASIVO_CAB_ID=Cn_IdProcesCab group by PAGO_ID;
    Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
    Lv_EstadoPago VARCHAR2(15);
    Lv_UsrCreacion VARCHAR2(20);
    Lr_InfoPagoCab  DB_FINANCIERO.info_pago_cab%ROWTYPE;
    Lv_SpMensajeSalida VARCHAR2(500);
    Lv_SpCodigoSalida NUMBER(8,2);
    Lv_Error VARCHAR2(16) DEFAULT 'false';
    Le_Exception EXCEPTION; 
    Lv_MsjResultado VARCHAR2(500);
  BEGIN
    
    FOR pagoMasCab IN Lc_MasPagoCab
    LOOP
        FOR pagoMasDet IN Lc_MasPagoDet(pagoMasCab.ID_PROCESO_MASIVO_CAB)
        LOOP
          IF pagoMasDet.ESTADO = 'Pendiente' THEN
            BEGIN
              SELECT tipc.* INTO Lr_InfoPagoCab FROM DB_FINANCIERO.INFO_PAGO_CAB tipc where tipc.ID_PAGO=pagoMasDet.PAGO_ID and tipc.EMPRESA_ID=pagoMasCab.EMPRESA_ID;
              EXCEPTION WHEN NO_DATA_FOUND THEN Lv_Error := 'true';
            END;
           IF Lv_Error = 'true' THEN
              Update db_infraestructura.info_proceso_masivo_det set OBSERVACION ='Id no fue encontrado en pagos: '||pagoMasDet.PAGO_ID, ESTADO='Error', USR_ULT_MOD='pmasivo',FE_ULT_MOD=CURRENT_DATE where PROCESO_MASIVO_CAB_ID=pagoMasCab.ID_PROCESO_MASIVO_CAB and PAGO_ID=pagoMasDet.PAGO_ID;
              commit;
              Lv_Error := 'false';
            ELSE
              BEGIN
                P_ANULACION_PAGO(pagoMasCab.ID_PROCESO_MASIVO_CAB,Lr_InfoPagoCab,Lv_SpCodigoSalida,Lv_SpMensajeSalida);
              END;
              IF Lv_SpCodigoSalida =0 THEN
                Update db_infraestructura.info_proceso_masivo_det set OBSERVACION =Lv_SpMensajeSalida, ESTADO='Finalizado', USR_ULT_MOD='pmasivo',FE_ULT_MOD=CURRENT_DATE where PROCESO_MASIVO_CAB_ID=pagoMasCab.ID_PROCESO_MASIVO_CAB and PAGO_ID=pagoMasDet.PAGO_ID;
                INSERT INTO DB_FINANCIERO.INFO_PAGO_HISTORIAL 
                  (ID_PAGO_HISTORIAL, PAGO_ID, MOTIVO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION)
                  VALUES
                  (DB_FINANCIERO.SEQ_INFO_PAGO_HISTORIAL.nextval, pagoMasDet.PAGO_ID, null, SYSDATE, 'pmasivo', 'Anulado', '[Proceso anulacion pago OK]');  
              ELSE
                Update db_infraestructura.info_proceso_masivo_det set OBSERVACION =Lv_SpMensajeSalida, ESTADO='Error', USR_ULT_MOD='pmasivo',FE_ULT_MOD=CURRENT_DATE where PROCESO_MASIVO_CAB_ID=pagoMasCab.ID_PROCESO_MASIVO_CAB and PAGO_ID=pagoMasDet.PAGO_ID;
                INSERT INTO DB_FINANCIERO.INFO_PAGO_HISTORIAL 
                  (ID_PAGO_HISTORIAL, PAGO_ID, MOTIVO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION)
                  VALUES
                  (DB_FINANCIERO.SEQ_INFO_PAGO_HISTORIAL.nextval, pagoMasDet.PAGO_ID, null, SYSDATE, 'pmasivo', Lr_InfoPagoCab.ESTADO_PAGO, '[Proceso anulacion pago ERROR]');  
              END IF;
              commit;
            END IF;
          END IF;
        END LOOP;
      Update db_infraestructura.info_proceso_masivo_cab set ESTADO='Finalizado', USR_ULT_MOD='pmasivo',FE_ULT_MOD=CURRENT_DATE where ID_PROCESO_MASIVO_CAB=pagoMasCab.ID_PROCESO_MASIVO_CAB;
    END LOOP;
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';
    EXCEPTION
      WHEN Le_Exception THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso Masivo de anuluacion pagos.' ||
                          ' - ' || Lv_MsjResultado|| SQLERRM;
        Pv_MsjSalida := Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_MASIVO_ANULACION', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso Masivo de anuluacion pagos.' ||
                          ' - ' || Lv_MsjResultado|| SQLERRM;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_MASIVO_ANULACION', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_MASIVO_ANULACION;
  
  
  PROCEDURE P_MASIVO_ANULACION_CORREO(Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
    CURSOR Lc_MasPagoCab
      IS
      select ID_PROCESO_MASIVO_CAB,EMPRESA_ID from db_infraestructura.info_proceso_masivo_Cab where ESTADO='Finalizado' and TIPO_PROCESO='AnulacionPagoCliente' ORDER BY ID_PROCESO_MASIVO_CAB ASC;
    Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
    Le_Exception EXCEPTION; 
    Lv_MsjResultado VARCHAR2(500);
  BEGIN
    
    FOR pagoMasCab IN Lc_MasPagoCab
    LOOP
      P_ENVIAR_CORREO_PAGOS(pagoMasCab.ID_PROCESO_MASIVO_CAB,Pn_CodSalida,Pv_MsjSalida);
      Update db_infraestructura.info_proceso_masivo_cab set ESTADO='Enviado', USR_ULT_MOD='pmasivo',FE_ULT_MOD=CURRENT_DATE where ID_PROCESO_MASIVO_CAB=pagoMasCab.ID_PROCESO_MASIVO_CAB;
    END LOOP;
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';
    EXCEPTION
      WHEN Le_Exception THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso Masivo de correo de anuluacion pagos.' ||
                          ' - ' || Lv_MsjResultado|| SQLERRM;
        Pv_MsjSalida := Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_MASIVO_ANULACION_CORREO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso Masivo de correo de anuluacion pagos.' ||
                          ' - ' || Lv_MsjResultado|| SQLERRM;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_MASIVO_ANULACION_CORREO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_MASIVO_ANULACION_CORREO;
  
  PROCEDURE P_ANULACION_PAGO(Pn_IdMasivoPagoCab NUMBER,Pr_InfoPagoCab IN OUT DB_FINANCIERO.info_pago_cab%ROWTYPE,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  Ln_IdPagoOrigen NUMBER(38,0);
  Lv_MsjResultado VARCHAR2(500);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  LV_ERROR VARCHAR2(3000);
  LE_ERROR EXCEPTION; 
  LE_EXTERNAL EXCEPTION;
  BEGIN
  
  Lv_MsjResultado:='pago '||Pr_InfoPagoCab.ID_PAGO;
  IF(Pr_InfoPagoCab.ANTICIPO_ID is not null)THEN
    P_ORIGEN_PAGO(Pr_InfoPagoCab.ANTICIPO_ID,Ln_IdPagoOrigen,Pn_CodSalida, Pv_MsjSalida);
    BEGIN
      SELECT tipc.* INTO Pr_InfoPagoCab FROM DB_FINANCIERO.INFO_PAGO_CAB tipc where tipc.ID_PAGO=Ln_IdPagoOrigen;
      EXCEPTION WHEN NO_DATA_FOUND THEN Lv_MsjResultado := 'No se encontro datos de pago origen - '||Ln_IdPagoOrigen; RAISE LE_ERROR;
    END;
  END IF;
  P_REGULARIZAR_PAGO(Pn_IdMasivoPagoCab,Pr_InfoPagoCab.ID_PAGO,Lv_MsjResultado,0,Pn_CodSalida,Pv_MsjSalida);
  IF(Pn_CodSalida>1) THEN
    Lv_MsjResultado:=Pv_MsjSalida;
    RAISE LE_EXTERNAL;
  ELSIF (Pn_CodSalida>0) THEN
  Pn_CodSalida:=0;
  Pv_MsjSalida :='Proceso finalizado '||Lv_MsjResultado||' - '||Pv_MsjSalida;
  ELSE
  Pn_CodSalida:=0;
  Pv_MsjSalida :='Proceso finalizado '||Lv_MsjResultado;
  END IF;
  
  EXCEPTION
    WHEN LE_EXTERNAL THEN
       ROLLBACK;
       Pv_MsjSalida := Lv_MsjResultado; 
    WHEN LE_ERROR THEN
      ROLLBACK;
      Pn_CodSalida:=999;
      Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso anuluacion pagos.' ||
                        ' - ' || Lv_MsjResultado;
      Pv_MsjSalida := Lv_MsjResultado; 
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                           'FNCK_ANULAR_PAGO.P_ANULACION_PAGO', 
                                           Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                           'telcos_store',
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
    WHEN OTHERS THEN
      ROLLBACK;
      Pn_CodSalida:=999;
      Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso anuluacion pagos.' ||
                        ' - ' || Lv_MsjResultado;
      Pv_MsjSalida := Lv_MsjResultado;
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                           'FNCK_ANULAR_PAGO.P_ANULACION_PAGO', 
                                           Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                           'telcos_store',
                                           SYSDATE,
                                           NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_ANULACION_PAGO;
  
  PROCEDURE P_ORIGEN_PAGO(Pn_IdPago NUMBER,Pn_IdPagoOrigen OUT NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  Lv_AnticipoId NUMBER(38,0);
  Lv_MsjResultado VARCHAR2(500);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  LV_ERROR VARCHAR2(3000);
  LE_ERROR EXCEPTION; 
  BEGIN
    BEGIN
      SELECT ID_PAGO,ANTICIPO_ID INTO Pn_IdPagoOrigen,Lv_AnticipoId FROM DB_FINANCIERO.INFO_PAGO_CAB WHERE ID_PAGO=Pn_IdPago;
      EXCEPTION WHEN NO_DATA_FOUND THEN Lv_AnticipoId := null;Pn_IdPagoOrigen:=Pn_IdPago;
    END;
    IF(Lv_AnticipoId is not null) THEN
      P_ORIGEN_PAGO(Lv_AnticipoId,Pn_IdPagoOrigen,Pn_CodSalida,Pv_MsjSalida);
    END IF;
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';
  EXCEPTION
      WHEN LE_ERROR THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso obtener pago origen.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado; 
       DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_ORIGEN_PAGO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso obtener pago origen.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_ORIGEN_PAGO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_ORIGEN_PAGO;
  
  PROCEDURE P_REGULARIZAR_PAGO(Pn_IdMasivoPagoCab NUMBER,Pr_IdPagoCab NUMBER,Pv_MsjRef VARCHAR2,Pn_Tipo NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  CURSOR Lc_PagoCab (Cn_IdPagoCab NUMBER)
      IS
      SELECT tipc.* FROM DB_FINANCIERO.INFO_PAGO_CAB tipc WHERE tipc.ANTICIPO_ID=Cn_IdPagoCab;
  CURSOR Lc_PagoDet (Cn_IdPagoCab NUMBER)
      IS
      SELECT tipd.* FROM DB_FINANCIERO.INFO_PAGO_DET tipd WHERE tipd.PAGO_ID=Cn_IdPagoCab;
  Lr_InfoPagoCab DB_FINANCIERO.info_pago_cab%ROWTYPE;
  Lr_InfoPagoDet DB_FINANCIERO.info_pago_det%ROWTYPE;
  Lr_InfoDocDet DB_FINANCIERO.info_documento_financiero_det%ROWTYPE;
  Ln_InfoMasivoDet NUMBER;
  Ln_IdMasivoDet NUMBER(38,0);
  Ln_IdPagoCat NUMBER(38,0);
  Ln_IdPunto NUMBER(38,0);
  Lv_MsjResultado VARCHAR2(500);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  LV_ERROR VARCHAR2(3000);
  LE_ERROR EXCEPTION; 
  LE_EXTERNAL EXCEPTION;
  BEGIN
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';
    SELECT tipc.* INTO Lr_InfoPagoCab FROM DB_FINANCIERO.INFO_PAGO_CAB tipc WHERE tipc.ID_PAGO=Pr_IdPagoCab;
    
    update DB_FINANCIERO.INFO_PAGO_CAB set ESTADO_PAGO='Anulado' where ID_PAGO=Pr_IdPagoCab;

    IF(Lr_InfoPagoCab.ESTADO_PAGO='Asignado')THEN
      FOR pago IN Lc_PagoCab(Lr_InfoPagoCab.ID_PAGO)
      LOOP
        P_REGULARIZAR_PAGO(Pn_IdMasivoPagoCab,pago.ID_PAGO,Pv_MsjRef,1,Pn_CodSalida,Pv_MsjSalida);
        IF(Pn_CodSalida>0)THEN
          Lv_MsjResultado:=Pv_MsjSalida;
          RAISE LE_ERROR;
        END IF;
      END LOOP;
    END IF;
    
    IF(Lr_InfoPagoCab.ESTADO_PAGO IN ('Asignado','Cerrado','Pendiente')) THEN
        BEGIN
          Select count(*) into Ln_InfoMasivoDet from db_infraestructura.info_proceso_masivo_det where PROCESO_MASIVO_CAB_ID=Pn_IdMasivoPagoCab and PAGO_ID=Pr_IdPagoCab;
          EXCEPTION WHEN NO_DATA_FOUND THEN Ln_InfoMasivoDet := 0;
        END;
        IF (Ln_InfoMasivoDet < 1) THEN
          Ln_IdPunto:=0;
          IF (Lr_InfoPagoCab.PUNTO_ID is not null) THEN
            Ln_IdPunto:=Lr_InfoPagoCab.PUNTO_ID;
          END IF;
          Insert into db_infraestructura.info_proceso_masivo_det (ID_PROCESO_MASIVO_DET,PROCESO_MASIVO_CAB_ID,PUNTO_ID,ESTADO,FE_CREACION,USR_CREACION,OBSERVACION,PAGO_ID) 
          values (DB_INFRAESTRUCTURA.SEQ_INFO_PROCESO_MASIVO_DET.NEXTVAL,Pn_IdMasivoPagoCab,Ln_IdPunto,'Finalizado',SYSDATE,'pmasivo','Anulado por '||Pv_MsjRef,Pr_IdPagoCab);
        END IF;
        FOR Lr_InfoPagoDet IN Lc_PagoDet(Lr_InfoPagoCab.ID_PAGO)
        LOOP
          IF(Lr_InfoPagoDet.REFERENCIA_ID is not null) THEN
            P_CAMBIO_ESTADO_DOCUMENTO(Lr_InfoPagoDet.REFERENCIA_ID,Pn_CodSalida, Pv_MsjSalida);
            IF(Pn_CodSalida>0)THEN
              Lv_MsjResultado:=Pv_MsjSalida;
              RAISE LE_EXTERNAL;
            END IF;
          END IF;
          BEGIN
            SELECT tidfd.* into Lr_InfoDocDet FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET tidfd where tidfd.PAGO_DET_ID=Lr_InfoPagoDet.ID_PAGO_DET;
            EXCEPTION WHEN NO_DATA_FOUND THEN Lr_InfoDocDet := null;
          END;
          IF(Lr_InfoDocDet.DOCUMENTO_ID is not null)THEN
            P_CAMBIO_ESTADO_DOCUMENTO(Lr_InfoDocDet.DOCUMENTO_ID,Pn_CodSalida, Pv_MsjSalida);
            IF(Pn_CodSalida>0)THEN
              Lv_MsjResultado:=Pv_MsjSalida;
              RAISE LE_EXTERNAL;
            END IF;
          END IF;
          BEGIN 
              update DB_FINANCIERO.INFO_PAGO_DET set ESTADO='Anulado', REFERENCIA_ID=null where ID_PAGO_DET=Lr_InfoPagoDet.ID_PAGO_DET;
          END;
        END LOOP;
        IF Lr_InfoPagoCab.PAGO_LINEA_ID is not null THEN
          UPDATE DB_FINANCIERO.INFO_PAGO_LINEA SET REVERSADO='S',ESTADO_PAGO_LINEA='Eliminado',FE_ULT_MOD=sysdate,USR_ULT_MOD='pmasivo' where ID_PAGO_LINEA=Lr_InfoPagoCab.PAGO_LINEA_ID;
        END IF;
        IF Pn_Tipo=0 THEN
          BEGIN
            P_CORTAR_SERVICIO(Lr_InfoPagoCab.ID_PAGO,Pn_CodSalida,Pv_MsjSalida);
            IF Pn_CodSalida > 0 THEN
                  Pv_MsjSalida:='Error al realizar corte - '|| Lr_InfoPagoCab.ID_PAGO||' - '||Pv_MsjSalida;
                  Pn_CodSalida:=1;
                  --RAISE LE_EXTERNAL;
            END IF;
          END;
        END IF;
        
    ELSE
      Lv_MsjResultado :='El pago '||Pr_IdPagoCab||' se encuentra con estado '||Lr_InfoPagoCab.ESTADO_PAGO; 
      RAISE LE_ERROR;
    END IF;
  
    EXCEPTION
      WHEN LE_EXTERNAL THEN
        ROLLBACK;
        Pv_MsjSalida := Lv_MsjResultado; 
      WHEN LE_ERROR THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Pv_MsjSalida := 'Ocurrio un error al ejecutar el Proceso regularizar pagos.' ||
                          ' - ' ||Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_REGULARIZAR_PAGO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso regularizar pagos.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_REGULARIZAR_PAGO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_REGULARIZAR_PAGO;
  
  PROCEDURE P_CAMBIO_ESTADO_DOCUMENTO(Pn_IdDocumento NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  Lr_InfoDocumentoCab info_documento_financiero_cab%ROWTYPE;
  Lr_TipoDocumento admi_tipo_documento_financiero%ROWTYPE;
  Ln_IdCaracteristica NUMBER(38,0);
  Ln_IdInfoDoc NUMBER(38,0);
  Lv_MsjResultado VARCHAR2(500);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  LV_ERROR VARCHAR2(3000);
  LE_ERROR EXCEPTION; 
  BEGIN

  BEGIN
    SELECT tidf.* into Lr_InfoDocumentoCab FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB tidf WHERE tidf.ID_DOCUMENTO=Pn_IdDocumento;
    EXCEPTION WHEN NO_DATA_FOUND THEN Lr_InfoDocumentoCab := null;
  END;
  IF(Lr_InfoDocumentoCab.TIPO_DOCUMENTO_ID is null)THEN
    Lv_MsjResultado:='No se encontro documento finaciero - '||Pn_IdDocumento;
    RAISE  LE_ERROR;
  END IF;
  
  BEGIN
    SELECT tatdf.* into Lr_TipoDocumento FROM DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO tatdf where tatdf.ID_TIPO_DOCUMENTO=Lr_InfoDocumentoCab.TIPO_DOCUMENTO_ID;
    EXCEPTION WHEN NO_DATA_FOUND THEN Lr_TipoDocumento := null;
  END;
  IF(Lr_TipoDocumento.CODIGO_TIPO_DOCUMENTO is null)THEN
    Lv_MsjResultado:='No se encontro tipo de documento finaciero - '||Lr_InfoDocumentoCab.TIPO_DOCUMENTO_ID;
    RAISE  LE_ERROR;
  END IF;
  
  CASE 
    WHEN Lr_TipoDocumento.CODIGO_TIPO_DOCUMENTO IN ('DEV') THEN
      Lv_MsjResultado:='El tipo de documento asociado es una devolucion, no se puede generar cambio de estado de documento';
      RAISE  LE_ERROR;
    WHEN Lr_TipoDocumento.CODIGO_TIPO_DOCUMENTO IN ('FACP','FAC') THEN
      IF(Lr_InfoDocumentoCab.ESTADO_IMPRESION_FACT='Cerrado') THEN
        update DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB set ESTADO_IMPRESION_FACT='Activo' where ID_DOCUMENTO=Pn_IdDocumento;
        INSERT INTO DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL 
                  (ID_DOCUMENTO_HISTORIAL, DOCUMENTO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION,MOTIVO_ID)
                  VALUES
                  (DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.nextval, Pn_IdDocumento, SYSDATE, 'pmasivo','Activo', '[Proceso anulacion pago]',null); 
      END IF;
    WHEN Lr_TipoDocumento.CODIGO_TIPO_DOCUMENTO IN ('NDI','ND') THEN
      BEGIN
        SELECT ID_CARACTERISTICA into Ln_IdCaracteristica FROM DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA='PROCESO_DIFERIDO';
        EXCEPTION WHEN NO_DATA_FOUND THEN Ln_IdCaracteristica := null;
      END;
      IF(Ln_IdCaracteristica is not null) THEN
        BEGIN
          SELECT ID_DOCUMENTO_CARACTERISTICA into Ln_IdInfoDoc FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA 
          where DOCUMENTO_ID=Pn_IdDocumento and CARACTERISTICA_ID=Ln_IdCaracteristica and ESTADO='Activo';
          EXCEPTION WHEN NO_DATA_FOUND THEN Ln_IdInfoDoc := null;
        END;
        IF(Ln_IdInfoDoc is not null) THEN
          update DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB set ESTADO_IMPRESION_FACT='Activo' where ID_DOCUMENTO=Pn_IdDocumento;
          INSERT INTO DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL 
                  (ID_DOCUMENTO_HISTORIAL, DOCUMENTO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION,MOTIVO_ID)
                  VALUES
                  (DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.nextval, Pn_IdDocumento, SYSDATE, 'pmasivo','Activo', '[Proceso anulacion pago]',null); 
        ELSE
          update DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB set ESTADO_IMPRESION_FACT='Anulado' where ID_DOCUMENTO=Pn_IdDocumento;
          INSERT INTO DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL 
                  (ID_DOCUMENTO_HISTORIAL, DOCUMENTO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION,MOTIVO_ID)
                  VALUES
                  (DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.nextval, Pn_IdDocumento, SYSDATE, 'pmasivo','Anulado', '[Proceso anulacion pago]',null); 
        END IF;
      END IF;
    ELSE DBMS_OUTPUT.PUT_LINE('Tipo de documento omitido por proceso: '||Lr_TipoDocumento.CODIGO_TIPO_DOCUMENTO);
  END CASE;
  
  
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';
  EXCEPTION
      WHEN LE_ERROR THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Pv_MsjSalida := 'Ocurrio un error al ejecutar el Proceso cambio estado documento.' ||
                          ' - ' ||Lv_MsjResultado; 
       DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_CAMBIO_ESTADO_DOCUMENTO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso cambio estado documento.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_CAMBIO_ESTADO_DOCUMENTO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_CAMBIO_ESTADO_DOCUMENTO;

  PROCEDURE P_ENVIAR_CORREO_PAGOS(Pn_IdMasivoPagoCab NUMBER,Pn_CodSalida OUT NUMBER, Pv_MsjSalida OUT VARCHAR2) AS
  CURSOR C_MasPagoDet (Cn_IdProcesCab NUMBER)
      IS
      WITH TMP_PAGOS AS (
      SELECT ipmd.ESTADO,ipmd.OBSERVACION,ipc.ID_PAGO, ipc.PUNTO_ID, ipc.NUMERO_PAGO, ipc.TIPO_DOCUMENTO_ID, ipc.USR_CREACION, 
      TO_CHAR(ipc.FE_CREACION, 'DD-MON-YYYY HH24:MI') as FE_CREACION,
      TO_CHAR(ipmd.FE_ULT_MOD, 'DD-MON-YYYY HH24:MI') as FE_ULT_MOD,
      ipc.ESTADO_PAGO, ipc.PAGO_LINEA_ID, ipd.VALOR_PAGO AS VALOR_TOTAL, ipd.FORMA_PAGO_ID, ipd.BANCO_TIPO_CUENTA_ID,
      atdf.NOMBRE_TIPO_DOCUMENTO
      FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET ipmd JOIN DB_FINANCIERO.INFO_PAGO_CAB ipc ON ipc.ID_PAGO=ipmd.PAGO_ID
      JOIN DB_FINANCIERO.INFO_PAGO_DET ipd ON ipd.PAGO_ID = ipc.ID_PAGO
      JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO atdf ON atdf.ID_TIPO_DOCUMENTO = ipc.TIPO_DOCUMENTO_ID
      WHERE ipmd.PROCESO_MASIVO_CAB_ID=Cn_IdProcesCab)
        SELECT tip.*,
          (SELECT acpn.NOMBRE_CANAL_PAGO_LINEA FROM DB_FINANCIERO.INFO_PAGO_LINEA  ipn
          LEFT JOIN DB_FINANCIERO.ADMI_CANAL_PAGO_LINEA acpn ON acpn.ID_CANAL_PAGO_LINEA = ipn.CANAL_PAGO_LINEA_ID
          WHERE ipn.ID_PAGO_LINEA = tip.PAGO_LINEA_ID) AS NOMBRE_CANAL_PAGO_LINEA,
          (SELECT ab.DESCRIPCION_BANCO FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA abtc
          LEFT JOIN DB_GENERAL.ADMI_BANCO ab ON ab.ID_BANCO=abtc.BANCO_ID
          WHERE abtc.ID_BANCO_TIPO_CUENTA=tip.BANCO_TIPO_CUENTA_ID) AS DESCRIPCION_BANCO,
          (SELECT afp.DESCRIPCION_FORMA_PAGO FROM DB_GENERAL.ADMI_FORMA_PAGO afp 
        WHERE afp.ID_FORMA_PAGO=tip.FORMA_PAGO_ID) AS DESCRIPCION_FORMA_PAGO,
          ipa.LOGIN,ip.IDENTIFICACION_CLIENTE,COALESCE(ip.RAZON_SOCIAL,CONCAT(CONCAT(ip.NOMBRES,' '),ip.APELLIDOS)) as NOMBRE_COMPLETO
        FROM TMP_PAGOS tip
          LEFT JOIN DB_COMERCIAL.INFO_PUNTO ipa on ipa.ID_PUNTO=tip.PUNTO_ID 
          LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper on iper.ID_PERSONA_ROL=ipa.PERSONA_EMPRESA_ROL_ID 
          LEFT JOIN DB_COMERCIAL.INFO_PERSONA ip on ip.ID_PERSONA=iper.PERSONA_ID;
  CURSOR C_DocPagoDet (Cn_IdProcesCab NUMBER)
  IS
    select ipc.NUMERO_PAGO,atdf.NOMBRE_TIPO_DOCUMENTO,idfc.ID_DOCUMENTO,idfc.NUMERO_FACTURA_SRI,idfc.ESTADO_IMPRESION_FACT,TO_CHAR(idfc.FE_CREACION, 'DD-MON-YYYY HH24:MI') as FE_CREACION, ipa.LOGIN
    from DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET ipmd
    join DB_FINANCIERO.INFO_PAGO_CAB ipc on ipc.ID_PAGO=ipmd.PAGO_ID
    join DB_FINANCIERO.INFO_PAGO_DET ipd on ipd.PAGO_ID=ipc.ID_PAGO
    left join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET idfd on idfd.PAGO_DET_ID=ipd.ID_PAGO_DET
    left join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB idfc on idfc.ID_DOCUMENTO=idfd.DOCUMENTO_ID
    left join DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO atdf on atdf.ID_TIPO_DOCUMENTO=idfc.TIPO_DOCUMENTO_ID
    left join DB_COMERCIAL.INFO_PUNTO ipa on ipa.ID_PUNTO=ipc.PUNTO_ID
    where ipmd.ESTADO='Finalizado' and idfc.ID_DOCUMENTO is not null and ipmd.PROCESO_MASIVO_CAB_ID=Cn_IdProcesCab
    union
    select ipc.NUMERO_PAGO,atdf.NOMBRE_TIPO_DOCUMENTO,idfc.ID_DOCUMENTO,idfc.NUMERO_FACTURA_SRI,idfc.ESTADO_IMPRESION_FACT,TO_CHAR(idfc.FE_CREACION, 'DD-MON-YYYY HH24:MI') as FE_CREACION, ipa.LOGIN
    from DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET ipmd
    join DB_FINANCIERO.INFO_PAGO_CAB ipc on ipc.ID_PAGO=ipmd.PAGO_ID
    join DB_FINANCIERO.INFO_PAGO_DET ipd on ipd.PAGO_ID=ipc.ID_PAGO
    left join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB idfc on idfc.id_documento=ipd.REFERENCIA_ID
    left join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET idfd on idfd.PAGO_DET_ID=ipd.ID_PAGO_DET
    left join DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO atdf on atdf.ID_TIPO_DOCUMENTO=idfc.TIPO_DOCUMENTO_ID
    left join DB_COMERCIAL.INFO_PUNTO ipa on ipa.ID_PUNTO=ipc.PUNTO_ID
    where ipmd.ESTADO='Finalizado' and idfc.ID_DOCUMENTO is not null and ipmd.PROCESO_MASIVO_CAB_ID=Cn_IdProcesCab;
  -- Costo del query: 4
  CURSOR C_GetParametro(Cv_NombreParamCab VARCHAR2)
  IS
    SELECT APD.ID_PARAMETRO_DET, APD.VALOR1, APD.VALOR2, APD.VALOR3, APD.VALOR4,APD.VALOR5
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC,
      DB_GENERAL.ADMI_PARAMETRO_DET APD
    WHERE APC.ID_PARAMETRO   = APD.PARAMETRO_ID
    AND APC.ESTADO           = 'Activo'
    AND APD.ESTADO           = 'Activo'
    AND APC.NOMBRE_PARAMETRO = 'PARAM_ANULACION_PAGOS'
    AND APD.VALOR1           = Cv_NombreParamCab;
  CURSOR C_Plantilla (Cv_Codigo VARCHAR2)
      IS
    SELECT PLANTILLA
    FROM DB_COMUNICACION.ADMI_PLANTILLA 
    WHERE ESTADO <> 'Eliminado' and CODIGO=Cv_Codigo;
  Lr_Parametro C_GetParametro%ROWTYPE;
  Lr_Plantilla C_Plantilla%ROWTYPE;
  Lr_ErrorParametro C_GetParametro%ROWTYPE;
  Lr_ErrorPlantilla C_Plantilla%ROWTYPE;
  
  Lfile_Archivo utl_file.file_type;
  Lfile_ArchivoDoc utl_file.file_type;
  Lv_Delimitador			VARCHAR2(1)    := ';';
  Lv_Directorio			VARCHAR2(50)   :='DIR_REPGERENCIA';
  Lv_NombreArchivo		VARCHAR2(50);
  Lv_NombreArchivoDoc	VARCHAR2(50);
  Lv_Gzip					VARCHAR2(100);
  Lv_NombreArchivoZip		VARCHAR2(50);
  Lv_CabeceraArchivo		VARCHAR2(3000);
  Lv_CabeceraArchivoDoc		VARCHAR2(3000);
  Lv_RowArchivo			VARCHAR2(5000);
  Lv_RowArchivoDoc			VARCHAR2(5000);
  Lv_MsjResultado VARCHAR2(5000);
  Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  Ln_ErrorMail Number;
  LV_ERROR VARCHAR2(5000);
  LE_ERROR EXCEPTION; 
  BEGIN
  Ln_ErrorMail:=0;
    IF Pn_IdMasivoPagoCab IS NOT NULL THEN
      --Se obtiene los parámetros para enviar el correo de anulacion de pagos
      OPEN C_GetParametro('ANULACION-OK');
      FETCH C_GetParametro INTO Lr_Parametro;
      CLOSE C_GetParametro;
      --Se obtiene el alias y la plantilla donde se enviará los datos de anulacion 
      OPEN C_Plantilla('NOTI_AP_OK');
      FETCH C_Plantilla INTO Lr_Plantilla;
      CLOSE C_Plantilla;
      
      IF Lr_Plantilla.PLANTILLA IS NULL AND
          Lr_Parametro.VALOR2 IS NULL AND
          Lr_Parametro.VALOR3 IS NULL AND
          Lr_Parametro.VALOR4 IS NULL THEN
        Lv_MsjResultado:='Error no se encontro configuracion para notificacion de proceso de anulacion - '||Pn_IdMasivoPagoCab;
        RAISE LE_ERROR;
      END IF;

      --Se obtiene los parámetros para enviar el correo de anulacion de pagos por error
      OPEN C_GetParametro('ANULACION-DOC');
      FETCH C_GetParametro INTO Lr_ErrorParametro;
      CLOSE C_GetParametro;
      --Se obtiene el alias y la plantilla donde se enviará los datos de anulacion  por error
      OPEN C_Plantilla('NOTI_AP_DOC');
      FETCH C_Plantilla INTO Lr_ErrorPlantilla;
      CLOSE C_Plantilla;
      
      IF Lr_ErrorPlantilla.PLANTILLA IS NULL AND
          Lr_ErrorParametro.VALOR2 IS NULL AND
          Lr_ErrorParametro.VALOR3 IS NULL AND
          Lr_ErrorParametro.VALOR4 IS NULL THEN
        Lv_MsjResultado:='Error no se encontro configuracion para notificacion de proceso de anulacion - '||Pn_IdMasivoPagoCab;
        RAISE LE_ERROR;
      END IF;

      Lv_NombreArchivo:='ReporteAnulacionPago-'||to_char(Sysdate,'DDMMYYYYHHMMSS')||'.csv';
      Lv_NombreArchivoDoc:='ReporteAnulacionPagoDoc-'||to_char(Sysdate,'DDMMYYYYHHMMSS')||'.csv'; 
      Lfile_Archivo:= UTL_FILE.fopen(Lv_Directorio,Lv_NombreArchivo,'w',3000);--Opening a file
      Lfile_ArchivoDoc:= UTL_FILE.fopen(Lv_Directorio,Lv_NombreArchivoDoc,'w',3000);--Opening a file
      Lv_CabeceraArchivo:='Tipo Doc.'||Lv_Delimitador  
               ||'Num.Pago'||Lv_Delimitador 
               ||'Estado'||Lv_Delimitador 
			   ||'Login'||Lv_Delimitador 
			   ||'Valor'||Lv_Delimitador 
			   ||'Fec.Creacion'||Lv_Delimitador 
			   ||'Fec.Anulacion'||Lv_Delimitador 
			   ||'CI'||Lv_Delimitador 
			   ||'Nom.Cliente'||Lv_Delimitador 
			   ||'Tipo Pago'||Lv_Delimitador 
         ||'Banco'||Lv_Delimitador 
			   ||'Observacion'||Lv_Delimitador;
         
      Lv_CabeceraArchivoDoc:='Num.Pago'||Lv_Delimitador 
          ||'Login'||Lv_Delimitador
          ||'Tipo Doc.'||Lv_Delimitador 
			   ||'Num.Doc.Sri'||Lv_Delimitador 
			   ||'Estado'||Lv_Delimitador 
			   ||'Fec.Creacion'||Lv_Delimitador;

      utl_file.put_line(Lfile_Archivo,Lv_CabeceraArchivo);
      utl_file.put_line(Lfile_ArchivoDoc,Lv_CabeceraArchivoDoc);

      FOR pagoMasDet IN C_MasPagoDet(Pn_IdMasivoPagoCab)
      LOOP
        IF pagoMasDet.ESTADO = 'Finalizado' THEN
          Lv_RowArchivo:=NVL(pagoMasDet.NOMBRE_TIPO_DOCUMENTO,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.NUMERO_PAGO,'') ||Lv_Delimitador ||
          'Anulado' ||Lv_Delimitador ||
          NVL(pagoMasDet.LOGIN,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.VALOR_TOTAL,0) ||Lv_Delimitador ||
          NVL(pagoMasDet.FE_CREACION,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.FE_ULT_MOD,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.IDENTIFICACION_CLIENTE,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.NOMBRE_COMPLETO,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.DESCRIPCION_FORMA_PAGO,'') ||Lv_Delimitador||
          NVL(pagoMasDet.DESCRIPCION_BANCO,'') ||Lv_Delimitador;
           
          utl_file.put_line(Lfile_Archivo,Lv_RowArchivo || NVL(pagoMasDet.OBSERVACION,'') ||Lv_Delimitador);

        ELSIF pagoMasDet.ESTADO = 'Error' THEN
          Lv_RowArchivo:=NVL(pagoMasDet.NOMBRE_TIPO_DOCUMENTO,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.NUMERO_PAGO,'') ||Lv_Delimitador ||
          'Error' ||Lv_Delimitador ||
          NVL(pagoMasDet.LOGIN,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.VALOR_TOTAL,0) ||Lv_Delimitador ||
          NVL(pagoMasDet.FE_CREACION,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.FE_ULT_MOD,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.IDENTIFICACION_CLIENTE,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.NOMBRE_COMPLETO,'') ||Lv_Delimitador ||
          NVL(pagoMasDet.DESCRIPCION_FORMA_PAGO,'') ||Lv_Delimitador||
          NVL(pagoMasDet.DESCRIPCION_BANCO,'') ||Lv_Delimitador;
           
          utl_file.put_line(Lfile_Archivo,Lv_RowArchivo || NVL(pagoMasDet.OBSERVACION,'') ||Lv_Delimitador);
          Ln_ErrorMail:=1;
        END IF;
      END LOOP;
      
      
      
      FOR pagoDocDet IN C_DocPagoDet(Pn_IdMasivoPagoCab)
      LOOP
          Lv_RowArchivoDoc:=NVL(pagoDocDet.NUMERO_PAGO,'') ||Lv_Delimitador ||
          NVL(pagoDocDet.LOGIN,'') ||Lv_Delimitador ||
          NVL(pagoDocDet.NOMBRE_TIPO_DOCUMENTO,'') ||Lv_Delimitador ||
          NVL(pagoDocDet.NUMERO_FACTURA_SRI,'') ||Lv_Delimitador ||
          NVL(pagoDocDet.ESTADO_IMPRESION_FACT,'') ||Lv_Delimitador ||
          NVL(pagoDocDet.FE_CREACION,'') ||Lv_Delimitador;
          
          utl_file.put_line(Lfile_ArchivoDoc,Lv_RowArchivoDoc);
      END LOOP;
      
      
      
      Lv_Gzip:='gzip /backup/repgerencia/'||Lv_NombreArchivo;
      Lv_NombreArchivoZip:= Lv_NombreArchivo||'.gz';
      UTL_FILE.fclose(Lfile_Archivo); 
      dbms_output.put_line( NAF47_TNET.JAVARUNCOMMAND (Lv_Gzip) ) ;  
      DBMS_OUTPUT.PUT_LINE(Lv_NombreArchivoZip) ;
      BEGIN 
        DB_GENERAL.GNRLPCK_UTIL.send_email_attach(Lr_Parametro.VALOR2, 
                            Lr_Parametro.VALOR4,
                            Lr_Parametro.VALOR3, 
                            Lr_Plantilla.PLANTILLA, 
                            Lv_Directorio, 
                            Lv_NombreArchivoZip); 
        EXCEPTION
          WHEN OTHERS THEN
            Lv_MsjResultado:='Error al realizar envio de anulacion de proceso cab - '||Pn_IdMasivoPagoCab|| ' - ' || SQLERRM;
            RAISE LE_ERROR;
      END;
      UTL_FILE.FREMOVE (Lv_Directorio,Lv_NombreArchivoZip);    
      Lv_Gzip:='gzip /backup/repgerencia/'||Lv_NombreArchivoDoc;
      Lv_NombreArchivoZip:= Lv_NombreArchivoDoc||'.gz';
      UTL_FILE.fclose(Lfile_ArchivoDoc); 
      dbms_output.put_line( NAF47_TNET.JAVARUNCOMMAND (Lv_Gzip) ) ; 
      DBMS_OUTPUT.PUT_LINE(Lv_NombreArchivoZip) ;
      BEGIN 
        DB_GENERAL.GNRLPCK_UTIL.send_email_attach(Lr_ErrorParametro.VALOR2, 
                            Lr_ErrorParametro.VALOR4,
                            Lr_ErrorParametro.VALOR3, 
                            Lr_ErrorPlantilla.PLANTILLA, 
                            Lv_Directorio, 
                            Lv_NombreArchivoZip); 
        EXCEPTION
          WHEN OTHERS THEN
            Lv_MsjResultado:='Error al realizar envio de anulacion por error de proceso cab - '||Pn_IdMasivoPagoCab|| ' - ' || SQLERRM;
            RAISE LE_ERROR;
      END;
      UTL_FILE.FREMOVE (Lv_Directorio,Lv_NombreArchivoZip);

    ELSE
      Lv_MsjResultado:='Error no se encontro proceso cab - '||Pn_IdMasivoPagoCab;
      RAISE LE_ERROR;
    END IF;
    Pn_CodSalida:=0;
    Pv_MsjSalida:='OK';                                                  
  EXCEPTION
      WHEN LE_ERROR THEN
        Pn_CodSalida:=998;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso envio correo anulacion.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_ENVIAR_CORREO_PAGOS', 
                                             Lv_MsjResultado || ' - ' ||LV_ERROR || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso envio correo anulacion.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_ENVIAR_CORREO_PAGOS', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
  END P_ENVIAR_CORREO_PAGOS;
  
  PROCEDURE P_REVERSA_CONT_PAL (Pv_IdPagoDet IN VARCHAR2,Pv_NoCia IN VARCHAR2,Pv_UsrCreacion   IN VARCHAR2,Pn_CodSalida OUT NUMBER,Pv_MsjSalida OUT VARCHAR2) AS
        CURSOR C_MigraDocAsociado (
            Cv_NoCia         NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO.NO_CIA%TYPE,
            Cv_UsrCreacion   NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO.USR_CREACION%TYPE,
            Cv_DocOrigen     NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO.DOCUMENTO_ORIGEN_ID%TYPE
        ) IS
        SELECT
            MDA.ROWID   PK_DOC_ASOC,
            MDA.*
        FROM
            NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO MDA
        WHERE
            MDA.NO_CIA = Cv_NoCia
            AND MDA.USR_CREACION = Cv_UsrCreacion
            AND MDA.DOCUMENTO_ORIGEN_ID = Cv_DocOrigen;
        --

        CURSOR C_GetMigraArckMM (
            Cv_IdMigracion   NAF47_TNET.MIGRA_ARCKMM.ID_MIGRACION%TYPE,
            Cv_NoCia         NAF47_TNET.MIGRA_ARCKMM.NO_CIA%TYPE,
            Cv_UsrCreacion   NAF47_TNET.MIGRA_ARCKMM.USUARIO_CREACION%TYPE,
            Cv_CodDiario     NAF47_TNET.MIGRA_ARCKMM.COD_DIARIO%TYPE
        ) IS
        SELECT
            ARCK.ROWID   PK_ARCKMM,
            ARCK.*
        FROM
            NAF47_TNET.MIGRA_ARCKMM ARCK
        WHERE
            ARCK.ID_MIGRACION = Cv_IdMigracion
            AND ARCK.NO_CIA = Cv_NoCia
            AND ARCK.USUARIO_CREACION = Cv_UsrCreacion
            AND ARCK.COD_DIARIO = Cv_CodDiario;
        --

        Lv_MsjResultado                VARCHAR2(2000) := '';
        Lv_Code               VARCHAR2(5) := '';
        Lv_TipoMov            VARCHAR2(5) := '';
        Ln_Secuencia          NUMBER := 0;
        LE_ERROR EXCEPTION;
        Lc_GetMigraArckMM     C_GetMigraArckMM%ROWTYPE;
        Lc_MigraDocAsociado   C_MigraDocAsociado%ROWTYPE;
        Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
    --
    BEGIN
        FOR I_IdPagoDet IN (
            SELECT
                TRIM(REGEXP_SUBSTR(Pv_IdPagoDet, '[^,]+', 1, LEVEL)) LevelPagoDet
            FROM
                DUAL
            CONNECT BY
                LEVEL <= REGEXP_COUNT(Pv_IdPagoDet, ',') + 1
        ) LOOP
         --       DBMS_OUTPUT.PUT_LINE('Itera PagoDet: ' || I_IdPagoDet.LevelPagoDet);
        --
                IF C_MigraDocAsociado%ISOPEN THEN
                    CLOSE C_MigraDocAsociado;
                END IF;
        --
                OPEN C_MigraDocAsociado(Pv_NoCia, Pv_UsrCreacion, I_IdPagoDet.LevelPagoDet);
                FETCH C_MigraDocAsociado INTO Lc_MigraDocAsociado;
        --
                CLOSE C_MigraDocAsociado;
        --
        --
                IF Pv_UsrCreacion <> Lc_MigraDocAsociado.USR_CREACION THEN
                    Lv_MsjResultado := 'No se puede regularizar la contabilidad de PagoDet: ' || I_IdPagoDet.LevelPagoDet||' - usuario de reverso '||Pv_UsrCreacion||'incorrecto y registro no coinciden '||Lc_MigraDocAsociado.USR_CREACION;
                    RAISE LE_ERROR;
                END IF;
            --
            
                 IF 'M' <> Lc_MigraDocAsociado.ESTADO THEN
                    Lv_MsjResultado := 'No se puede regularizar la contabilidad de PagoDet: ' || I_IdPagoDet.LevelPagoDet||' - estado de documenton asociado es diferente de M - '||Lc_MigraDocAsociado.ESTADO;
                    RAISE LE_ERROR;
                END IF;
                
                IF  'CK' <> Lc_MigraDocAsociado.TIPO_MIGRACION THEN
                    Lv_MsjResultado := 'No se puede regularizar la contabilidad de PagoDet: ' || I_IdPagoDet.LevelPagoDet||' - tipo de migracion de documenton asociado es diferente de CK - '||Lc_MigraDocAsociado.TIPO_MIGRACION ;
                    RAISE LE_ERROR;
                END IF;
                
                IF '' <> Lc_MigraDocAsociado.MIGRACION_ID THEN
                    Lv_MsjResultado := 'No se puede regularizar la contabilidad de PagoDet: ' || I_IdPagoDet.LevelPagoDet||' - documenton asociado no posee id de migracion';
                    RAISE LE_ERROR;
                END IF;

                IF C_GetMigraArckMM%ISOPEN THEN
                    CLOSE C_GetMigraArckMM;
                END IF;
            --
                OPEN C_GetMigraArckMM(Lc_MigraDocAsociado.MIGRACION_ID, Pv_NoCia, Pv_UsrCreacion, Lc_MigraDocAsociado.TIPO_DOC_MIGRACION);
            --
                FETCH C_GetMigraArckMM INTO Lc_GetMigraArckMM;
            --
                IF C_GetMigraArckMM%NOTFOUND THEN
                    CLOSE C_GetMigraArckMM;
                    Lv_MsjResultado := 'No existe ArckMM para ID_MIGRACION: '
                              || Lc_MigraDocAsociado.MIGRACION_ID
                              || ' de PagoDet: '
                              || I_IdPagoDet.LevelPagoDet;
                    RAISE LE_ERROR;
                END IF;
            --

                CLOSE C_GetMigraArckMM;
            --
                IF Lc_GetMigraArckMM.PROCESADO NOT IN (
                    'N',
                    'S'
                ) THEN
                    Lv_MsjResultado := 'El estado de ArckMM es '
                              || Lc_GetMigraArckMM.PROCESADO
                              || ' de PagoDet: '
                              || I_IdPagoDet.LevelPagoDet;
                    RAISE LE_ERROR;
                END IF;

                IF 'N' = Lc_GetMigraArckMM.PROCESADO THEN
            --
                    /*DBMS_OUTPUT.PUT_LINE('MIGRACION_ID: '|| Lc_MigraDocAsociado.MIGRACION_ID|| ' Pv_NoCia: '|| Pv_NoCia|| ' Pv_UsrCreacion: '|| Pv_UsrCreacion|| 
                                         ' TIPO_DOC_MIGRACION: '|| Lc_MigraDocAsociado.TIPO_DOC_MIGRACION|| ' a [X] y MIGRA_DOCUMENTO_ASOCIADO a [E]');*/

                    UPDATE NAF47_TNET.MIGRA_ARCKMM ARCKU
                    SET
                        ARCKU.PROCESADO = 'X'
                    WHERE
                        ARCKU.ROWID = Lc_GetMigraArckMM.PK_ARCKMM;
                    --

                    UPDATE NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO MDAU
                    SET
                        MDAU.ESTADO = 'E'
                    WHERE
                        MDAU.ROWID = Lc_MigraDocAsociado.PK_DOC_ASOC;
        --

                END IF;

                IF 'S' = Lc_GetMigraArckMM.PROCESADO THEN
                    Ln_Secuencia := 0;
                    Ln_Secuencia := NAF47_TNET.TRANSA_ID.MIGRA_CK(Pv_NoCia);
                    IF 0 = Ln_Secuencia THEN
                        Lv_MsjResultado := 'No se genero la secuencia MIGRA_CK de PagoDet: ' || I_IdPagoDet.LevelPagoDet;
                        RAISE LE_ERROR;
                    END IF;
                --

                    --DBMS_OUTPUT.PUT_LINE('Inserta DocAsociado: '|| Ln_Secuencia|| ', ARCKMM de '|| I_IdPagoDet.LevelPagoDet);
                    INSERT INTO NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO (
                        DOCUMENTO_ORIGEN_ID,
                        TIPO_DOC_MIGRACION,
                        MIGRACION_ID,
                        TIPO_MIGRACION,
                        NO_CIA,
                        FORMA_PAGO_ID,
                        TIPO_DOCUMENTO_ID,
                        ESTADO,
                        USR_CREACION,
                        FE_CREACION,
                        USR_ULT_MOD,
                        FE_ULT_MOD
                    ) VALUES (
                        I_IdPagoDet.LevelPagoDet,
                        Lc_MigraDocAsociado.TIPO_DOC_MIGRACION,
                        Ln_Secuencia,
                        Lc_MigraDocAsociado.TIPO_MIGRACION,
                        Lc_MigraDocAsociado.NO_CIA,
                        Lc_MigraDocAsociado.FORMA_PAGO_ID,
                        Lc_MigraDocAsociado.TIPO_DOCUMENTO_ID,
                        Lc_MigraDocAsociado.ESTADO,
                        Lc_MigraDocAsociado.USR_CREACION,
                        SYSDATE,
                        Lc_MigraDocAsociado.USR_ULT_MOD,
                        SYSDATE
                    );
                --

                    INSERT INTO NAF47_TNET.MIGRA_ARCKMM (
                        NO_CIA,
                        NO_CTA,
                        PROCEDENCIA,
                        TIPO_DOC,
                        NO_DOCU,
                        FECHA,
                        BENEFICIARIO,
                        COMENTARIO,
                        MONTO,
                        DESCUENTO_PP,
                        ESTADO,
                        CONCILIADO,
                        MES,
                        ANO,
                        FECHA_ANULADO,
                        IND_BORRADO,
                        IND_OTROMOV,
                        MONEDA_CTA,
                        TIPO_CAMBIO,
                        TIPO_AJUSTE,
                        IND_DIST,
                        T_CAMB_C_V,
                        IND_OTROS_MESES,
                        MES_CONCILIADO,
                        ANO_CONCILIADO,
                        NO_FISICO,
                        SERIE_FISICO,
                        IND_CON,
                        NUMERO_CTRL,
                        ORIGEN,
                        USUARIO_CREACION,
                        USUARIO_ANULA,
                        USUARIO_PROCESA,
                        FECHA_PROCESA,
                        FECHA_DOC,
                        IND_DIVISION,
                        COD_DIVISION,
                        PROCESADO,
                        FECHA_CREACION,
                        ID_FORMA_PAGO,
                        ID_OFICINA_FACTURACION,
                        ID_MIGRACION,
                        COD_DIARIO
                    ) VALUES (
                        Lc_GetMigraArckMM.NO_CIA,
                        Lc_GetMigraArckMM.NO_CTA,
                        Lc_GetMigraArckMM.PROCEDENCIA,
                        'ND',
                        Lc_GetMigraArckMM.NO_DOCU,
                        Lc_GetMigraArckMM.FECHA,
                        Lc_GetMigraArckMM.BENEFICIARIO,
                        Lc_GetMigraArckMM.COMENTARIO,
                        Lc_GetMigraArckMM.MONTO,
                        Lc_GetMigraArckMM.DESCUENTO_PP,
                        Lc_GetMigraArckMM.ESTADO,
                        Lc_GetMigraArckMM.CONCILIADO,
                        Lc_GetMigraArckMM.MES,
                        Lc_GetMigraArckMM.ANO,
                        SYSDATE,
                        Lc_GetMigraArckMM.IND_BORRADO,
                        Lc_GetMigraArckMM.IND_OTROMOV,
                        Lc_GetMigraArckMM.MONEDA_CTA,
                        Lc_GetMigraArckMM.TIPO_CAMBIO,
                        Lc_GetMigraArckMM.TIPO_AJUSTE,
                        Lc_GetMigraArckMM.IND_DIST,
                        Lc_GetMigraArckMM.T_CAMB_C_V,
                        Lc_GetMigraArckMM.IND_OTROS_MESES,
                        Lc_GetMigraArckMM.MES_CONCILIADO,
                        Lc_GetMigraArckMM.ANO_CONCILIADO,
                        Lc_GetMigraArckMM.NO_FISICO,
                        Lc_GetMigraArckMM.SERIE_FISICO,
                        Lc_GetMigraArckMM.IND_CON,
                        Lc_GetMigraArckMM.NUMERO_CTRL,
                        Lc_GetMigraArckMM.ORIGEN,
                        Lc_GetMigraArckMM.USUARIO_CREACION,
                        Lc_GetMigraArckMM.USUARIO_ANULA,
                        Lc_GetMigraArckMM.USUARIO_PROCESA,
                        Lc_GetMigraArckMM.FECHA_PROCESA,
                        Lc_GetMigraArckMM.FECHA_DOC,
                        Lc_GetMigraArckMM.IND_DIVISION,
                        Lc_GetMigraArckMM.COD_DIVISION,
                        'N',
                        SYSDATE,
                        Lc_GetMigraArckMM.ID_FORMA_PAGO,
                        Lc_GetMigraArckMM.ID_OFICINA_FACTURACION,
                        Ln_Secuencia,
                        Lc_GetMigraArckMM.COD_DIARIO
                    );
                --

                    FOR I_ArckML IN (
                        SELECT
                            ARCKML.*
                        FROM
                            NAF47_TNET.MIGRA_ARCKML ARCKML
                        WHERE
                            ARCKML.MIGRACION_ID = Lc_MigraDocAsociado.MIGRACION_ID
                            AND ARCKML.COD_DIARIO = Lc_MigraDocAsociado.TIPO_DOC_MIGRACION
                            AND ARCKML.NO_CIA = Pv_NoCia
                    ) LOOP
                        Lv_TipoMov := 'D';
                        IF 'D' = I_ArckML.TIPO_MOV THEN
                            Lv_TipoMov := 'C';
                        END IF;
                        INSERT INTO NAF47_TNET.MIGRA_ARCKML (
                            NO_CIA,
                            PROCEDENCIA,
                            TIPO_DOC,
                            NO_DOCU,
                            COD_CONT,
                            CENTRO_COSTO,
                            TIPO_MOV,
                            MONTO,
                            MONTO_DOL,
                            TIPO_CAMBIO,
                            MONEDA,
                            NO_ASIENTO,
                            MODIFICABLE,
                            CODIGO_TERCERO,
                            IND_CON,
                            ANO,
                            MES,
                            MONTO_DC,
                            GLOSA,
                            EXCEDE_PRESUPUESTO,
                            MIGRACION_ID,
                            LINEA,
                            COD_DIARIO
                        ) VALUES (
                            I_ArckML.NO_CIA,
                            I_ArckML.PROCEDENCIA,
                            'ND',
                            I_ArckML.NO_DOCU,
                            I_ArckML.COD_CONT,
                            I_ArckML.CENTRO_COSTO,
                            Lv_TipoMov,
                            I_ArckML.MONTO,
                            I_ArckML.MONTO_DOL,
                            I_ArckML.TIPO_CAMBIO,
                            I_ArckML.MONEDA,
                            I_ArckML.NO_ASIENTO,
                            I_ArckML.MODIFICABLE,
                            I_ArckML.CODIGO_TERCERO,
                            I_ArckML.IND_CON,
                            I_ArckML.ANO,
                            I_ArckML.MES,
                            I_ArckML.MONTO_DC,
                            I_ArckML.GLOSA,
                            I_ArckML.EXCEDE_PRESUPUESTO,
                            Ln_Secuencia,
                            I_ArckML.LINEA,
                            I_ArckML.COD_DIARIO
                        );

                    END LOOP;

                END IF;
        END LOOP;

        Pn_CodSalida := 0;
		Pv_MsjSalida:='OK';
	EXCEPTION
      WHEN LE_ERROR THEN
        ROLLBACK;
        Pn_CodSalida:=998;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso reversa de contabilidad.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_REVERSA_CONT_PAL', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso reversa de contabilidad.' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_REVERSA_CONT_PAL', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
    END P_REVERSA_CONT_PAL;
    
  PROCEDURE P_CORTAR_SERVICIO(Pv_IdPago IN VARCHAR2,Pn_CodSalida OUT NUMBER,Pv_MsjSalida OUT VARCHAR2) AS
    CURSOR C_Punto (Cv_IdPago VARCHAR2)
      IS
    SELECT  ipc.punto_id
            FROM DB_FINANCIERO.info_pago_cab ipc
            WHERE ipc.id_pago= Cv_IdPago
            AND ipc.punto_id IS NOT NULL
            GROUP BY ipc.punto_id;
    CURSOR C_Servicio (Cv_PuntoId VARCHAR2)
      IS
    SELECT DB_COMERCIAL.GET_ID_SERVICIO_PREF(Cv_PuntoId) ID_SERVICIO FROM DUAL;
    CURSOR C_Historial(Cn_IdServicio VARCHAR2)
      IS
      WITH LAST_2_STATES AS ( 
      SELECT 
        ISH.* 
       FROM 
        DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH 
       WHERE 
        ISH.SERVICIO_ID = Cn_IdServicio ORDER BY ISH.ID_SERVICIO_HISTORIAL DESC 
      ) 
      SELECT TBL_SERV_HIST.FE_CREACION, TBL_SERV_HIST.ID_SERVICIO, TBL_SERV_HIST.ESTADO FROM ( 
        SELECT TO_CHAR(L2S.FE_CREACION, 'DD-MM-YYYY HH24:MI:SS') FE_CREACION, 
       L2S.SERVICIO_ID ID_SERVICIO, 
       L2S.ESTADO ESTADO, 
       L2S.FE_CREACION AS FECHA 
        FROM LAST_2_STATES L2S 
        WHERE ROWNUM < 2
      UNION 
        SELECT TO_CHAR(L2S.FE_CREACION, 'DD-MM-YYYY HH24:MI:SS') FE_CREACION, 
       L2S.SERVICIO_ID ID_SERVICIO, 
       L2S.ESTADO ESTADO, 
       L2S.FE_CREACION AS FECHA 
        FROM LAST_2_STATES L2S WHERE 
       FE_CREACION < (SELECT TRUNC(MAX(FE_CREACION)) FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH 
       WHERE ISH.SERVICIO_ID = Cn_IdServicio) 
      AND ROWNUM < 2 ) TBL_SERV_HIST ORDER BY TBL_SERV_HIST.FECHA DESC;
    CURSOR C_Parametro(Cv_PrefijoEmpresa VARCHAR2,Cv_PuntoNombre VARCHAR2) 
      IS
      SELECT APD.ID_PARAMETRO_DET, APD.VALOR1, APD.VALOR2, APD.VALOR3, APD.VALOR4,APD.VALOR5
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC,
        DB_GENERAL.ADMI_PARAMETRO_DET APD
      WHERE APC.ID_PARAMETRO   = APD.PARAMETRO_ID
      AND APC.ESTADO           = 'Activo'
      AND APD.ESTADO           = 'Activo'
      AND APC.NOMBRE_PARAMETRO = 'EMPRESA_EQUIVALENTE'
      AND APD.VALOR1='MD' AND APD.VALOR2='FO';
    Lr_ParametroFO C_Parametro%ROWTYPE;
    Lr_ParametroCO C_Parametro%ROWTYPE;
    Lv_MsjResultado                VARCHAR2(2000) := '';
    Ln_EmpresaId NUMBER(38,0);
    Lv_EmpresaPrefi VARCHAR2(5);
    LE_ERROR EXCEPTION;
    Ln_Error NUMBER;
    Lv_InCorte VARCHAR2(50);
    Lv_Activo VARCHAR2(50);
    Ln_IndiceHistorial NUMBER;
    TYPE Lt_PuntosCorte IS TABLE OF NUMBER;
    Lr_PuntoCorte Lt_PuntosCorte:=Lt_PuntosCorte();
    TYPE Lt_PuntosFO IS TABLE OF NUMBER;
    Lr_PuntoFO Lt_PuntosFO:=Lt_PuntosFO();
    TYPE Lt_PuntosCO IS TABLE OF NUMBER;
    Lr_PuntoCO Lt_PuntosCO:=Lt_PuntosCO();
    TYPE Lt_Milla IS TABLE OF NUMBER INDEX BY VARCHAR2(64);
    Lr_Milla Lt_Milla;
    Lv_ResultMilla VARCHAR2(5);
    Lv_IpCreacion VARCHAR2(16) := (NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
    Lv_EstadoMasivo VARCHAR2(16);
    Ln_IdProcesoMasivoCab NUMBER(38,0);
    Lv_UsuarioPago VARCHAR2(20);
    Ln_IdPagoLinea NUMBER(38,0);
    i PLS_INTEGER;
    BEGIN
	Pv_MsjSalida:='OK';
	Pn_CodSalida := 0;
      BEGIN
        SELECT ieg.PREFIJO,ipc.EMPRESA_ID,ipc.USR_CREACION,ipc.PAGO_LINEA_ID into Lv_EmpresaPrefi,Ln_EmpresaId,Lv_UsuarioPago,Ln_IdPagoLinea FROM DB_FINANCIERO.INFO_PAGO_CAB ipc,DB_FINANCIERO.INFO_EMPRESA_GRUPO ieg 
        WHERE ipc.ID_PAGO=Pv_IdPago and ipc.EMPRESA_ID=ieg.COD_EMPRESA;
        EXCEPTION WHEN NO_DATA_FOUND THEN 
          Lv_MsjResultado := 'No se encontro pago Id'||Pv_IdPago;
          Pn_CodSalida:=998;
      END;
      Ln_Error:=1;
      FOR punto IN C_Punto(Pv_IdPago)
      LOOP
      
        Ln_Error:=2;
        --DBMS_OUTPUT.PUT_LINE('P'||punto.punto_id);
        FOR servicioRef IN C_Servicio(punto.punto_id)
        LOOP
          --DBMS_OUTPUT.PUT_LINE('S'||servicioRef.ID_SERVICIO);
          Ln_IndiceHistorial:=0;
          IF servicioRef.ID_SERVICIO is not null THEN
            Ln_Error:=0;
            FOR historial IN C_Historial(servicioRef.ID_SERVICIO)
            LOOP
              IF Ln_IndiceHistorial=0 and historial.ESTADO='Activo' THEN
                Lv_InCorte:='true';
              END IF;
              IF Ln_IndiceHistorial=1 and historial.ESTADO='In-Corte' THEN
                Lv_Activo:='true';
              END IF;
              Ln_IndiceHistorial:=Ln_IndiceHistorial+1;
            END LOOP;
            IF Lv_InCorte='true' and Lv_Activo='true' THEN
              Lr_PuntoCorte.extend;
              Lr_PuntoCorte(Lr_PuntoCorte.COUNT):=punto.punto_id;
            END IF;
            Lv_InCorte:='false';
            Lv_Activo:='false';
          END IF;
        END LOOP;
         
        IF Ln_Error=0 and Lr_PuntoCorte.COUNT >0 THEN
          DBMS_OUTPUT.PUT_LINE(Pv_IdPago);
          Lr_Milla('FO'):=0;
          Lr_Milla('CR'):=0;
          i := Lr_PuntoCorte.FIRST;
          WHILE (i IS NOT NULL)
          LOOP
            Lv_ResultMilla:=F_GET_MEDIO_POR_PUNTO(Lr_PuntoCorte(i));
            IF Lv_ResultMilla='FO' THEN
              Lr_Milla('FO'):=Lr_Milla('FO')+1;
              Lr_PuntoFO.extend;
              Lr_PuntoFO(Lr_PuntoFO.COUNT):=Lr_PuntoCorte(i);
            END IF;
            IF Lv_ResultMilla='CO' or Lv_ResultMilla='RAD' THEN
              Lr_Milla('CR'):=Lr_Milla('CR')+1;
              Lr_PuntoCO.extend;
              Lr_PuntoCO(Lr_PuntoCO.COUNT):=Lr_PuntoCorte(i);
            END IF;
            i := Lr_PuntoCorte.NEXT(i);
          END LOOP;
          
          IF Lv_EmpresaPrefi='TTCO' THEN
            Lv_EstadoMasivo:='Finalizada';
          ELSE
            Lv_EstadoMasivo:='Pendiente';
          END IF;
          
          
                    
          IF Lr_PuntoFO.COUNT >0 THEN
            select DB_INFRAESTRUCTURA.SEQ_INFO_PROCESO_MASIVO_CAB.NEXTVAL into Ln_IdProcesoMasivoCab from dual;
            INSERT INTO DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB
                  ( ID_PROCESO_MASIVO_CAB,TIPO_PROCESO,CANTIDAD_PUNTOS,EMPRESA_ID,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,PAGO_LINEA_ID)
                  VALUES
                  (Ln_IdProcesoMasivoCab,'CortarCliente',
                      Lr_Milla('FO'),Ln_EmpresaId,Lv_EstadoMasivo,
                    'pmasivo',SYSDATE,Lv_IpCreacion,Ln_IdPagoLinea );
            i := Lr_PuntoFO.FIRST;
            WHILE (i IS NOT NULL)
            LOOP
              Insert into db_infraestructura.info_proceso_masivo_det (ID_PROCESO_MASIVO_DET,PROCESO_MASIVO_CAB_ID,PUNTO_ID,ESTADO,FE_CREACION,USR_CREACION,OBSERVACION) 
              values (DB_INFRAESTRUCTURA.SEQ_INFO_PROCESO_MASIVO_DET.NEXTVAL,Ln_IdProcesoMasivoCab,Lr_PuntoFO(i),'Pendiente',SYSDATE,Lv_UsuarioPago,'');
              i := Lr_PuntoFO.NEXT(i);
            END LOOP;
          END IF;
          IF Lr_PuntoCO.COUNT >0 THEN
          
            select DB_INFRAESTRUCTURA.SEQ_INFO_PROCESO_MASIVO_CAB.NEXTVAL into Ln_IdProcesoMasivoCab from dual;
            
            INSERT INTO DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB
                  ( ID_PROCESO_MASIVO_CAB,TIPO_PROCESO,CANTIDAD_PUNTOS,EMPRESA_ID,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,PAGO_LINEA_ID )
                  VALUES
                  (Ln_IdProcesoMasivoCab,'CortarCliente',
                      Lr_Milla('CR'),Ln_EmpresaId,Lv_EstadoMasivo,
                      'pmasivo',SYSDATE,Lv_IpCreacion,Ln_IdPagoLinea);
            i := Lr_PuntoFO.FIRST;
            WHILE (i IS NOT NULL)
            LOOP
              Insert into db_infraestructura.info_proceso_masivo_det (ID_PROCESO_MASIVO_DET,PROCESO_MASIVO_CAB_ID,PUNTO_ID,ESTADO,FE_CREACION,USR_CREACION,OBSERVACION) 
              values (DB_INFRAESTRUCTURA.SEQ_INFO_PROCESO_MASIVO_DET.NEXTVAL,Ln_IdProcesoMasivoCab,Lr_PuntoCO(i),'Pendiente',SYSDATE,Lv_UsuarioPago,'');
              i := Lr_PuntoFO.NEXT(i);
            END LOOP;
          END IF;
           
        END IF;
      END LOOP;
      IF Ln_Error=1 THEN
        Lv_MsjResultado := 'No se encontro puntos';
		Pn_CodSalida:=998;
      END IF;
      
    
		
    	EXCEPTION
      WHEN LE_ERROR THEN
        ROLLBACK;
        Pn_CodSalida:=998;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso corte servicio' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado; 
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_CORTAR_SERVICIO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM,
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
      WHEN OTHERS THEN
        ROLLBACK;
        Pn_CodSalida:=999;
        Lv_MsjResultado:= 'Ocurrio un error al ejecutar el Proceso corte servicio' ||
                          ' - ' || Lv_MsjResultado;
        Pv_MsjSalida := Lv_MsjResultado;
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+', 
                                             'FNCK_ANULAR_PAGO.P_CORTAR_SERVICIO', 
                                             Lv_MsjResultado || ' - ' || SQLCODE || ' -ERROR- ' || SQLERRM, 
                                             'telcos_store',
                                             SYSDATE,
                                             NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), Lv_IpCreacion));
    END P_CORTAR_SERVICIO;
     
  FUNCTION F_GET_MEDIO_POR_PUNTO(Pn_IdPunto IN NUMBER)  RETURN VARCHAR2 IS
  CURSOR C_GetDescPlan( Cn_IdPunto NUMBER)
  IS
    SELECT TM.CODIGO_TIPO_MEDIO
    FROM DB_COMERCIAL.INFO_SERVICIO ISR,
      DB_COMERCIAL.INFO_PLAN_CAB IPC,
      DB_COMERCIAL.INFO_PLAN_DET IPD,
      DB_COMERCIAL.ADMI_PRODUCTO AP,
      DB_COMERCIAL.INFO_SERVICIO_TECNICO ST,
      DB_COMERCIAL.ADMI_TIPO_MEDIO TM
    WHERE ISR.PLAN_ID     = IPC.ID_PLAN
    AND IPC.ID_PLAN       = IPD.PLAN_ID
    AND IPD.PRODUCTO_ID   = AP.ID_PRODUCTO
    AND AP.NOMBRE_TECNICO = 'INTERNET'
    AND ISR.PUNTO_ID      = Cn_IdPunto
    AND ISR.ID_SERVICIO   = ST.SERVICIO_ID
    AND ST.ULTIMA_MILLA_ID = TM.ID_TIPO_MEDIO;

  CURSOR C_GetDescProducto(  Cn_IdPunto NUMBER)
  IS
    SELECT
    TM.CODIGO_TIPO_MEDIO
    FROM DB_COMERCIAL.INFO_SERVICIO ISR,
      DB_COMERCIAL.INFO_SERVICIO_TECNICO SRT,
      DB_COMERCIAL.ADMI_TIPO_MEDIO TM,
      DB_COMERCIAL.ADMI_PRODUCTO AP
    WHERE ISR.PRODUCTO_ID = AP.ID_PRODUCTO
    AND AP.NOMBRE_TECNICO = 'INTERNET'
    AND AP.ESTADO         = 'Activo'     
    AND ISR.PUNTO_ID      = Cn_IdPunto
    AND ISR.ID_SERVICIO   = SRT.SERVICIO_ID
    AND SRT.ULTIMA_MILLA_ID = TM.ID_TIPO_MEDIO;
  --
  Lv_Descripcion VARCHAR2(1000) := NULL;
BEGIN
 DBMS_OUTPUT.PUT_LINE('P');
  --
  IF C_GetDescPlan%ISOPEN THEN
    --
    CLOSE C_GetDescPlan;
    --
  END IF;
  --
  OPEN C_GetDescPlan( Pn_IdPunto);
  --
  FETCH C_GetDescPlan INTO Lv_Descripcion;
  --
  IF C_GetDescPlan%NOTFOUND THEN
    --
    Lv_Descripcion := NULL;
    --
    IF C_GetDescProducto%ISOPEN THEN
      --
      CLOSE C_GetDescProducto;
      --
    END IF;
    --
    OPEN C_GetDescProducto( Pn_IdPunto);
    --
    FETCH C_GetDescProducto INTO Lv_Descripcion;
    --
    CLOSE C_GetDescProducto;
    --
  END IF;
  --
  CLOSE C_GetDescPlan;
  --
  RETURN Lv_Descripcion;
  --


END F_GET_MEDIO_POR_PUNTO;
  
END FNCK_ANULAR_PAGO;

/