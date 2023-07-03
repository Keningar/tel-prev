/**
* DOCUMENTACIÓN DEL BLOQUE PARA INSERTAR LOS BANCOS DE TN(PREFIJO DE EMPRESA TELCONET) PARA EL PROCESAMIENTO AUTOMÁTICO DE LOS DÉBITOS
*
* BLOQUE QUE REGULARIZA.
*
* @author Hector Lozano <hlozano@telconet.ec>
* @version 1.0
*/
DECLARE
  --
  -- CURSOR QUE OBTIENE LOS ID BANCO_TIPO_CUENTA(ID QUE HACE REFERENCIA AL TIPO DE CUENTA DE UN BANCO: AHORRO, CORRIENTE, ETC.) LIGADOS A TELCONET
  CURSOR C_GetBancoTipoCuenta(Cv_EmpresaCod  DB_FINANCIERO.ADMI_FORMATO_DEBITO.EMPRESA_COD%TYPE,
                              Cv_Estado      DB_FINANCIERO.ADMI_FORMATO_DEBITO.ESTADO%TYPE)
  IS
   
    SELECT
      AFD.BANCO_TIPO_CUENTA_ID
    FROM
      DB_FINANCIERO.ADMI_FORMATO_DEBITO AFD
    WHERE
      AFD.EMPRESA_COD = Cv_EmpresaCod
    AND AFD.ESTADO    = Cv_Estado
    GROUP BY AFD.BANCO_TIPO_CUENTA_ID;
  --
  
  Lv_EmpresaCod    DB_FINANCIERO.ADMI_FORMATO_DEBITO.EMPRESA_COD%TYPE := 10;
  Lv_ESTADO        DB_FINANCIERO.ADMI_FORMATO_DEBITO.ESTADO%TYPE := 'Activo';

  --
BEGIN
  --
  IF C_GetBancoTipoCuenta%ISOPEN THEN
    --
    CLOSE C_GetBancoTipoCuenta;
    --
  END IF;
  --
  --RECORRE LOS ID BANCO_TIPO_CUENTA Y REGISTRA LA CARACTERÍSTICA
  FOR I_GetBancoTipoCuenta IN C_GetBancoTipoCuenta(Lv_EmpresaCod,Lv_ESTADO)
  
  LOOP
    
    Insert 
    INTO DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
      (
        ID_FORMATO_DEBITO_CARACT,
        BANCO_TIPO_CUENTA_ID,
        EMPRESA_COD,
        CARACTERISTICA_ID,
        VALOR,
        PROCESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        IP_ULT_MOD
      )
      values 
      (
        DB_FINANCIERO.SEQ_ADMI_FORMATO_DEBITO_CARACT.NEXTVAL,
        I_GetBancoTipoCuenta.BANCO_TIPO_CUENTA_ID,
        '10',
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='CARGA_AUTOMATICA_DEBITO'), 
        'SI',
        'ENVIO',
        'Activo',
        SYSDATE,
        'hlozano',
        '127.0.0.1',
        null,
        null,
        null
      );
    --
  END LOOP;
  --
  COMMIT;

  --
EXCEPTION
WHEN OTHERS THEN
  --
  ROLLBACK;
  --
  DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                        'SCRIPT_REGULARIZACION_CARACTERISTICAS_ADMI_FORMATO_DEBITO_CARACT_TN',
                                        'No se pudo regularizar la información de las características de bancos TN - ' || SQLCODE || ' -ERROR- '
                                        || SQLERRM,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  --
END;

/
