/**
 * Se inserta nueva caracteristica para REQUIERE_EQUIPO
 *
 * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
 * @version 1.0 29-06-2021
 */

--
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO,
	DETALLE_CARACTERISTICA
) 
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'REQUIERE_EQUIPOS_NODO',
    'C',
    'Activo',
    SYSDATE,
    'jnazareno',
    NULL,
    NULL,
    'SOPORTE',
	NULL 
);
 

COMMIT;
/

--SE INSERTAN LOS ID_TAREA QUE REQUIEREN EQUIPOS EN NODO
SET serveroutput ON

BEGIN
  FOR SOMEONE IN 
  (
    SELECT COLUMN_VALUE AS ID_TAREA FROM TABLE
    (
      SYS.ODCIVARCHAR2LIST
      (
        2076,
        2294,
        2442,
        2514,
        2517,
        2518,
        2519,
        2534,
        2535,
        2538,
        2543,
        2598,
        2600,
        2601,
        2602,
        2623,
        2632,
        2651,
        2652,
        2655,
        4728,
        4729,
        5235,
        5237,
        5238,
        5239,
        5240,
        5241,
        5242,
        5243,
        5244,
        5254,
        5255,
        5256,
        5257,
        5259,
        5260,
        5261,
        5262,
        5263,
        6318,
        6319,
        6322,
        6334,
        6356,
        6358,
        6362,
        6383,
        6385,
        6386,
        6391,
        6406,
        6407,
        6427,
        6459,
        6460,
        6461,
        6463,
        6464,
        6525,
        6526,
        6574
      )
    )
  )
  LOOP
    INSERT INTO DB_SOPORTE.INFO_TAREA_CARACTERISTICA
    (
      ID_TAREA_CARACTERISTICA,
      TAREA_ID,
      DETALLE_ID,
      CARACTERISTICA_ID,
      VALOR,
      FE_CREACION,
      USR_CREACION,
      IP_CREACION,
      FE_MODIFICACION,
      USR_MODIFICACION,
      IP_MODIFICACION,
      ESTADO
    ) 
    VALUES 
    (
      DB_SOPORTE.SEQ_INFO_TAREA_CARACTERISTICA.NEXTVAL,
      SOMEONE.ID_TAREA,
      null,
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'REQUIERE_EQUIPOS_NODO'),
      'S',
      SYSDATE,
      'jnazareno',
      '127.0.0.1',
      null,
      null,
      null,
      'Activo'
    );
    DBMS_OUTPUT.PUT_LINE('INSERT EXITOSO REQUIERE_EQUIPOS_NODO ' || SOMEONE.ID_TAREA);
  END LOOP;

END;
/
COMMIT;
/