--SE INSERTAN LOS ID_TAREA QUE REQUIEREN MATERIAL
--
SET serveroutput ON

BEGIN
  FOR SOMEONE IN 
  (
    SELECT COLUMN_VALUE AS ID_TAREA FROM TABLE
    (
      SYS.ODCIVARCHAR2LIST
      (
        1346,
        1347,
        1951,
        1953,
        1956,
        1965,
        2016,
        2020,
        2008,
        2236,
        2237,
        2327,
        2344,
        2346,
        2347,
        2594,
        2595,
        2596,
        2597,
        2598,
        2599,
        2600,
        2603,
        2604,
        2605,
        2606,
        2607,
        2608,
        2609,
        2610,
        2611,
        2612,
        2613,
        2614,
        2615,
        2616,
        2617,
        2622,
        2629,
        2630,
        2633,
        2640,
        2641,
        2642,
        2650,
        2651,
        2652,
        2654,
        2655,
        2656,
        2658,
        2659,
        2660,
        2661,
        2662,
        2663,
        2664,
        2665,
        2666,
        2667,
        3044,
        3218,
        3219,
        3220,
        3224,
        3225,
        3226,
        3227,
        3228,
        3233,
        3234,
        3262,
        3263,
        3357,
        3363,
        3503,
        3598,
        3875,
        3876,
        3877,
        3878,
        3879,
        3880,
        3887,
        3903,
        3904,
        3905,
        3906,
        3907,
        3908,
        3914,
        3921,
        3923,
        3925,
        3926,
        3941,
        3944,
        3947,
        3948,
        3949,
        3950,
        3951,
        4040,
        4110,
        4463,
        4575,
        4577,
        4591,
        4678,
        4968,
        4969,
        4983,
        4992,
        4994,
        5000,
        5001,
        5002,
        5006,
        5010,
        5045,
        5084,
        1026,
        5829,
        5830,
        5880
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
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'REQUIERE_MATERIAL'),
      'S',
      SYSDATE,
      'jnazareno',
      '172.24.4.23',
      null,
      null,
      null,
      'Activo'
    );
    DBMS_OUTPUT.PUT_LINE('INSERT EXITOSO REQUIERE_MATERIAL ' || SOMEONE.ID_TAREA);
  END LOOP;

END;
/
COMMIT;

/
