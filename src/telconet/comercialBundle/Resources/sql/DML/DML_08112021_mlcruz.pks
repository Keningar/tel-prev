/*
 * Regularización de registros repetidos asociados a un mismo punto en la DB_COMERCIAL.INFO_PUNTO_SALDO
 * Tiempo aproximado en base de desarrollo 117.12 segundos
 */
SET SERVEROUTPUT ON
DECLARE
  CURSOR Lc_GetSaldosPuntosVista
  IS
    SELECT PUNTO_ID,
      SALDO
    FROM DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO
    WHERE PUNTO_ID IN
      (SELECT PUNTO_ID
      FROM DB_COMERCIAL.INFO_PUNTO_SALDO
      WHERE PUNTO_ID IS NOT NULL
      GROUP BY PUNTO_ID
      HAVING COUNT(*) > 1
      );
TYPE Lt_VistaPuntosRepetidos
IS
  TABLE OF Lc_GetSaldosPuntosVista%ROWTYPE INDEX BY PLS_INTEGER;
  Lt_TVistaPuntosRepetidos  Lt_VistaPuntosRepetidos;
  Ln_IndxRegRepetidos       NUMBER;
  Lr_RegPuntoSaldo          Lc_GetSaldosPuntosVista%ROWTYPE;
BEGIN
  SYS.DBMS_OUTPUT.PUT_LINE('INCIO DE REGULARIZACION DE SALDOS EN PUNTOS DUPLICADOS');
  OPEN Lc_GetSaldosPuntosVista;
  LOOP
    FETCH Lc_GetSaldosPuntosVista BULK COLLECT INTO Lt_TVistaPuntosRepetidos LIMIT 100;
    Ln_IndxRegRepetidos := Lt_TVistaPuntosRepetidos.FIRST;
    WHILE (Ln_IndxRegRepetidos IS NOT NULL)
    LOOP
      Lr_RegPuntoSaldo  := Lt_TVistaPuntosRepetidos(Ln_IndxRegRepetidos);
      SYS.DBMS_OUTPUT.PUT_LINE('ID_PUNTO:'|| Lr_RegPuntoSaldo.PUNTO_ID);
      DELETE
      FROM DB_COMERCIAL.INFO_PUNTO_SALDO
      WHERE PUNTO_ID = Lr_RegPuntoSaldo.PUNTO_ID;
      INSERT
      INTO DB_COMERCIAL.INFO_PUNTO_SALDO
        (
          PUNTO_ID,
          SALDO
        )
        VALUES
        (
          Lr_RegPuntoSaldo.PUNTO_ID,
          Lr_RegPuntoSaldo.SALDO
        );
      COMMIT;
      Ln_IndxRegRepetidos := Lt_TVistaPuntosRepetidos.NEXT(Ln_IndxRegRepetidos);
    END LOOP;
    EXIT WHEN Lc_GetSaldosPuntosVista%NOTFOUND;
  END LOOP;
  CLOSE Lc_GetSaldosPuntosVista;
  SYS.DBMS_OUTPUT.PUT_LINE('FIN DE REGULARIZACION DE SALDOS EN PUNTOS DUPLICADOS');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
  || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/