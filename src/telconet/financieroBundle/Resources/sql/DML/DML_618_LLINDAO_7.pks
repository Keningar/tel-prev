DECLARE
  --
  Gv_CodEmpresa         CONSTANT VARCHAR2(2) := '18';
  Gv_UsrCreacion        CONSTANT VARCHAR2(100) := 'db_financiero';
  Gv_TipoProceso        CONSTANT VARCHAR2(5) := 'MIXTO';
  Gv_PlantillaAjuste    CONSTANT VARCHAR2(20) := 'AJUSTE POR REDONDEO';
  Gv_IpCreacion         CONSTANT VARCHAR2(10) := '127.0.0.1';
  Gv_Estado             CONSTANT VARCHAR2(10) := 'Activo';
  --
  Lr_InfoPlantillaCab DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB%ROWTYPE := NULL;
  Lr_InfoPlantillaDet DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET%ROWTYPE := NULL;
  Lr_AdmiTipoCuenta   DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE%ROWTYPE := NULL;
  --
  Lv_CuentaContable   VARCHAR2(15) := NULL;
  --
  CURSOR C_DOCUMENTOS IS 
    SELECT ATDF.ID_TIPO_DOCUMENTO,
      ATDF.CODIGO_TIPO_DOCUMENTO,
      UPPER(ATDF.NOMBRE_TIPO_DOCUMENTO) AS NOMBRE_TIPO_DOCUMENTO,
      DECODE(ATDF.CODIGO_TIPO_DOCUMENTO, 'NC', 'Nota de Crédito|fe_emision| - |nombre_oficina', 
                                               'Facturación|fe_emision| - |nombre_oficina') AS GLOSA_DETALLE,
      DECODE(ATDF.CODIGO_TIPO_DOCUMENTO, 'NC', 'M_NC1', 'M_F_1') AS COD_DIARIO,
      DECODE(ATDF.CODIGO_TIPO_DOCUMENTO, 'NC', 'Nota de Crédito|fe_emision| - |nombre_oficina', 
                                               'Facturación|fe_emision| - |nombre_oficina') AS GLOSA_CABECERA
      
    FROM DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF
    WHERE ATDF.CODIGO_TIPO_DOCUMENTO IN ('FAC','NC');
  --
  FUNCTION F_EXISTE_PLANTILLA (Pv_CodTipoDoc IN VARCHAR2) RETURN BOOLEAN IS
    CURSOR C_VERIFICA_PLANTILLA IS
      SELECT PC.ID_PLANTILLA_CONTABLE_CAB
      FROM DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF,
           DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PC
      WHERE PC.TIPO_PROCESO = Gv_TipoProceso
      AND PC.DESCRIPCION LIKE '%'||Gv_PlantillaAjuste||'%'
      AND ATDF.CODIGO_TIPO_DOCUMENTO = Pv_CodTipoDoc
      AND PC.EMPRESA_COD = Gv_CodEmpresa
      AND PC.TIPO_DOCUMENTO_ID = ATDF.ID_TIPO_DOCUMENTO;
    --
    Lb_Existe          BOOLEAN := FALSE;
    Ln_IdPlantillaCont NUMBER := NULL;
    --
  BEGIN
    --
    IF C_VERIFICA_PLANTILLA%ISOPEN THEN
      CLOSE C_VERIFICA_PLANTILLA;
    END IF;
    --
    OPEN C_VERIFICA_PLANTILLA;
    FETCH C_VERIFICA_PLANTILLA INTO Ln_IdPlantillaCont;
    Lb_Existe := C_VERIFICA_PLANTILLA%FOUND;
    CLOSE C_VERIFICA_PLANTILLA;
    --
    RETURN Lb_Existe;
    --
  END;
  --
  PROCEDURE P_INSERTA_PLANTILLA_CABECERA (Pr_InfoPlantillaCab IN DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB%ROWTYPE) IS
    
  BEGIN
    INSERT INTO DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB 
         ( ID_PLANTILLA_CONTABLE_CAB, 
           TIPO_DOCUMENTO_ID, 
           DESCRIPCION, 
           EMPRESA_COD, 
           FE_CREACION, 
           USR_CREACION, 
           IP_CREACION, 
           TABLA_CABECERA, 
           TABLA_DETALLE, 
           TIPO_PROCESO, 
           COD_DIARIO, 
           FORMATO_NO_DOCU_ASIENTO,
           ESTADO,
           FORMATO_GLOSA)
    values (Pr_InfoPlantillaCab.id_plantilla_contable_cab, 
            Pr_InfoPlantillaCab.tipo_documento_id, 
            Pr_InfoPlantillaCab.descripcion, 
            Gv_CodEmpresa, 
            sysdate, 
            Gv_UsrCreacion,
            Gv_IpCreacion, 
            Pr_InfoPlantillaCab.tabla_cabecera,
            Pr_InfoPlantillaCab.tabla_detalle,
            Gv_TipoProceso, 
            Pr_InfoPlantillaCab.cod_diario, 
            Pr_InfoPlantillaCab.formato_no_docu_asiento,
            Gv_Estado, 
            Pr_InfoPlantillaCab.formato_glosa);

  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('P_INSERTA_PLANTILLA_DETALLE'||' - '||SQLERRM);
      ROLLBACK;
  END P_INSERTA_PLANTILLA_CABECERA;
  --
  PROCEDURE P_INSERTA_PLANTILLA_DETALLE (Pr_InfoPlantillaDet IN DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET%ROWTYPE) IS
    
  BEGIN
    
    INSERT INTO DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET 
         (PLANTILLA_CONTABLE_CAB_ID,
          ID_PLANTILLA_CONTABLE_DET, 
          TIPO_CUENTA_CONTABLE_ID,
          DESCRIPCION, 
          POSICION,
          FE_CREACION, 
          USR_CREACION, 
          IP_CREACION, 
          ESTADO, 
          TIPO_DETALLE, 
          FORMATO_GLOSA)
    values (Pr_InfoPlantillaDet.plantilla_contable_cab_id,
            Pr_InfoPlantillaDet.id_plantilla_contable_det,
            Pr_InfoPlantillaDet.tipo_cuenta_contable_id,
            Pr_InfoPlantillaDet.descripcion,
            Pr_InfoPlantillaDet.posicion,
            sysdate,
            Gv_UsrCreacion,
            Gv_IpCreacion,
            Gv_Estado,
            Pr_InfoPlantillaDet.tipo_detalle,
            Pr_InfoPlantillaDet.formato_glosa);

  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('P_INSERTA_PLANTILLA_DETALLE'||' - '||SQLERRM);
      ROLLBACK;
  END P_INSERTA_PLANTILLA_DETALLE;
  --
  PROCEDURE P_INSERTA_TIPO_CUENTA (Pr_AdmiTipoCuenta IN DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE%ROWTYPE) IS
    
  BEGIN
    
    INSERT INTO DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE 
         ( id_tipo_cuenta_contable, 
           descripcion, 
           fe_creacion, 
           usr_creacion, 
           ip_creacion, 
           estado)
    VALUES (Pr_AdmiTipoCuenta.id_tipo_cuenta_contable, 
            Pr_AdmiTipoCuenta.descripcion,
            sysdate,
            Gv_UsrCreacion,
            Gv_IpCreacion,
            Gv_Estado);

  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('P_INSERTA_TIPO_CUENTA'||' - '||SQLERRM);
      ROLLBACK;
  END P_INSERTA_TIPO_CUENTA;
  --
BEGIN
  
  FOR Lr_TipoDoc IN C_DOCUMENTOS LOOP
    -- si no existe cabecera AJUSTE PARA FACTURAS se inserta
    IF NOT F_EXISTE_PLANTILLA (Lr_TipoDoc.CODIGO_TIPO_DOCUMENTO) THEN
      --
      Lr_InfoPlantillaCab := NULL;
      --
      Lr_InfoPlantillaCab.id_plantilla_contable_cab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
      Lr_InfoPlantillaCab.tipo_documento_id := Lr_TipoDoc.ID_TIPO_DOCUMENTO; 
      Lr_InfoPlantillaCab.descripcion :=  Lr_TipoDoc.NOMBRE_TIPO_DOCUMENTO ||' '|| Gv_PlantillaAjuste;
      Lr_InfoPlantillaCab.tabla_cabecera := 'MIGRA_ARCGAE';
      Lr_InfoPlantillaCab.tabla_detalle := 'MIGRA_ARCGAL';
      Lr_InfoPlantillaCab.cod_diario := Lr_TipoDoc.COD_DIARIO;
      Lr_InfoPlantillaCab.formato_no_docu_asiento := '1|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision';
      Lr_InfoPlantillaCab.estado :=  'Activo';
      Lr_InfoPlantillaCab.formato_glosa :=  Lr_TipoDoc.GLOSA_CABECERA;
      --
      P_INSERTA_PLANTILLA_CABECERA(Lr_InfoPlantillaCab);
      --
      -- Se genera detalle contable
      FOR Ln_Detalle IN 1..2 LOOP
        --
        Lr_InfoPlantillaDet := NULL;
        Lr_AdmiTipoCuenta := NULL;
        --
        -- se verifica que exista tipo de cuenta contable
        IF Ln_Detalle = 1 THEN
          Lr_AdmiTipoCuenta.descripcion := 'OTROS GASTOS';
          Lr_InfoPlantillaDet.posicion := 'D';
          Lv_CuentaContable := '7210102001';
        ELSIF Ln_Detalle = 2 THEN
          Lr_AdmiTipoCuenta.descripcion := 'OTROS INGRESOS';
          Lr_InfoPlantillaDet.posicion := 'C';
          Lv_CuentaContable := '7110102001';
        END IF;
        --
        SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
                    FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
                    WHERE DESCRIPCION = Lr_AdmiTipoCuenta.descripcion),0)
        INTO Lr_AdmiTipoCuenta.id_tipo_cuenta_contable
        FROM DUAL;
        --
        IF Lr_AdmiTipoCuenta.id_tipo_cuenta_contable = 0 THEN
          --
          Lr_AdmiTipoCuenta.id_tipo_cuenta_contable := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
          --
          P_INSERTA_TIPO_CUENTA (Lr_AdmiTipoCuenta);
          --
        END IF;
        --
        --
        Lr_InfoPlantillaDet.plantilla_contable_cab_id := Lr_InfoPlantillaCab.id_plantilla_contable_cab;
        Lr_InfoPlantillaDet.id_plantilla_contable_det := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL;
        Lr_InfoPlantillaDet.tipo_cuenta_contable_id := Lr_AdmiTipoCuenta.id_tipo_cuenta_contable;
        Lr_InfoPlantillaDet.descripcion := Lr_TipoDoc.NOMBRE_TIPO_DOCUMENTO ||' '|| Gv_PlantillaAjuste||' - '||Lr_AdmiTipoCuenta.descripcion;
        Lr_InfoPlantillaDet.tipo_detalle := 'FIJO';
        Lr_InfoPlantillaDet.formato_glosa := Lr_TipoDoc.GLOSA_DETALLE;
        --
        P_INSERTA_PLANTILLA_DETALLE(Lr_InfoPlantillaDet);
        --
        -- se inserta detalle de cuentas
        INSERT INTO DB_FINANCIERO.ADMI_CUENTA_CONTABLE
             (ID_CUENTA_CONTABLE,
              NO_CIA,
              CUENTA,
              TABLA_REFERENCIAL,
              CAMPO_REFERENCIAL,
              VALOR_CAMPO_REFERENCIAL,
              NOMBRE_OBJETO_NAF,
              TIPO_CUENTA_CONTABLE_ID,
              DESCRIPCION,
              EMPRESA_COD,
              OFICINA_ID,
              FE_CREACION,
              USR_CREACION,
              IP_CREACION,
              ESTADO)    
        SELECT DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL,
          ACC.NO_CIA,
          Lv_CuentaContable,
          ACC.TABLA_REFERENCIAL,
          ACC.CAMPO_REFERENCIAL,
          ACC.OFICINA_ID VALOR_CAMPO_REFERENCIAL,
          ACC.NOMBRE_OBJETO_NAF,
          Lr_AdmiTipoCuenta.id_tipo_cuenta_contable,
          ACC.DESCRIPCION,
          ACC.EMPRESA_COD,
          ACC.OFICINA_ID,
          SYSDATE,
          Gv_UsrCreacion,
          Gv_IpCreacion,
          ACC.ESTADO
        FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
        WHERE ACC.TIPO_CUENTA_CONTABLE_ID = 37
        AND ACC.NO_CIA = Gv_CodEmpresa
        AND NOT EXISTS (SELECT NULL
                        FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC2
                        WHERE ACC2.TIPO_CUENTA_CONTABLE_ID = Lr_AdmiTipoCuenta.id_tipo_cuenta_contable
                        AND ACC2.NO_CIA = ACC.NO_CIA);
        --
      END LOOP;
      --
      --
    END IF;
  END LOOP;
  
  --
  COMMIT;
  --

END;
/
