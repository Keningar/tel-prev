SET SERVEROUTPUT ON
--Asociación de característica para producto IP FIJA para planes PYME sin IP
--El producto ya debe tener asociada la características IP WAN
DECLARE
  Lv_EstadoActivo   VARCHAR2(6) := 'Activo';
BEGIN
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO= 'IP FIJA'
    AND NOMBRE_TECNICO        = 'IP'
    AND ESTADO                = Lv_EstadoActivo
    ),
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'IP WAN'
    AND ESTADO                       = Lv_EstadoActivo
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
    Lv_EstadoActivo,
    'NO'
  );
  
  UPDATE DB_COMERCIAL.ADMI_PRODUCTO
  SET FUNCION_PRECIO='if ( [ESTATICO]==1 ) { PRECIO=10.00; } else if ( [ESTATICO]==0 ) { PRECIO=0.00; }'
  WHERE ID_PRODUCTO = 66;

  SYS.DBMS_OUTPUT.PUT_LINE('Creación correcta de información');

  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/