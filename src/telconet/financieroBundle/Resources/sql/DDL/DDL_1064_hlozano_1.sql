
/**
 * Script que regulariza las características mediante los productos con NOMBRE_TECNICO='NETHOME'.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0
 * @since 19-11-2018
 */
DECLARE

  -- Cursor que obtiene los productos con nombre_tecnico='NETHOME', cod_empresa=18 y estado='Activo'
  CURSOR C_GetProductos(Cv_EmpresaCod      DB_COMERCIAL.ADMI_PRODUCTO.EMPRESA_COD%TYPE,
                        Cv_NombreTecnico   DB_COMERCIAL.ADMI_PRODUCTO.NOMBRE_TECNICO%TYPE,
                        Cv_Estado          DB_COMERCIAL.ADMI_PRODUCTO.ESTADO%TYPE)
  IS
   
    SELECT
      AP.CODIGO_PRODUCTO,
      AP.ESTADO
    FROM
      DB_COMERCIAL.ADMI_PRODUCTO AP
    WHERE
      AP.EMPRESA_COD = Cv_EmpresaCod
    AND AP.NOMBRE_TECNICO = Cv_NombreTecnico
    AND AP.ESTADO = Cv_Estado;
  --
  
  Lv_EmpresaCod    DB_COMERCIAL.ADMI_PRODUCTO.EMPRESA_COD%TYPE := 18;
  Lv_NombreTecnico DB_COMERCIAL.ADMI_PRODUCTO.NOMBRE_TECNICO%TYPE := 'NETHOME';
  Lv_ESTADO        DB_COMERCIAL.ADMI_PRODUCTO.ESTADO%TYPE := 'Activo';

  --
BEGIN
  --
  IF C_GetProductos%ISOPEN THEN
    --
    CLOSE C_GetProductos;
    --
  END IF;
  --
  --Obtiene, itera los productos y almacena las características
  FOR I_GetProducto IN C_GetProductos(Lv_EmpresaCod,Lv_NombreTecnico,Lv_ESTADO)
  
  LOOP
    
    Insert 
    INTO DB_COMERCIAL.ADMI_CARACTERISTICA
      (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        TIPO
      )
      values 
      (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        I_GetProducto.CODIGO_PRODUCTO,
        'N',
        I_GetProducto.ESTADO,
        SYSDATE,
        'telcos_nethome',
        NULL,
        NULL,
        'COMERCIAL'
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
                                        'SCRIPT_REGULARIZACION_CARACTERISTICAS_PRODUCTOS_NETHOME',
                                        'No se pudo regularizar la información de las caracteristicas de productos MD - ' || SQLCODE || ' -ERROR- '
                                        || SQLERRM,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_COMERCIAL'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  --
END;
