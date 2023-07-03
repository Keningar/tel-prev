DECLARE

  ubicacionDC         NUMBER      := 0;
  idElementoHyperview NUMBER      := 0;

  type array_t IS       varray(200) OF VARCHAR2(50);
  array array_t := array_t('VMWARE');

  type array_t1 IS       varray(200) OF VARCHAR2(50);
  array_vc array_t1 := array_t1('sr1telconet-vc.telconet.cloud1','sr1aen1gyed-vc.telconet.cloud1','sr1aen2gyed-vc.telconet.cloud1','sr1aen3gyed-vc.telconet.cloud1','sr1aen4gyed-vc.telconet.cloud1','sr1aen5gyed-vc.telconet.cloud1','sr1aen6gyed-vc.telconet.cloud1','sr1aen8gyed-vc.telconet.cloud1','sr1aen9gyed-vc.telconet.cloud1');   

BEGIN

  SELECT UB.ID_UBICACION
  INTO ubicacionDC
  FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EU,
    DB_INFRAESTRUCTURA.INFO_UBICACION UB,
    DB_INFRAESTRUCTURA.INFO_ELEMENTO EL
  WHERE EL.NOMBRE_ELEMENTO = 'Data Center ( GYE )'
  AND EL.ESTADO            = 'Activo'
  AND EL.ID_ELEMENTO       = EU.ELEMENTO_ID
  AND EU.UBICACION_ID      = UB.ID_UBICACION
  AND EU.EMPRESA_COD       = 10;

  FOR i IN 1..array.count
  LOOP

    select DB_INFRAESTRUCTURA.SEQ_INFO_ELEMENTO.NEXTVAL INTO idElementoHyperview FROM DUAL;
  
    INSERT
    INTO DB_INFRAESTRUCTURA.INFO_ELEMENTO VALUES
      (
        idElementoHyperview,
        (SELECT ID_MODELO_ELEMENTO
        FROM DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
        WHERE NOMBRE_MODELO_ELEMENTO = 'MODELO HYPERVIEW'
        ) ,
        array(i),
        'HYPERVIEW GYE-DC',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        'arsuarez',
        'arsuarez',
        sysdate,
        '127.0.0.1',
        NULL,
        'Activo',
        NULL
      );
      
    -- se ingresa la ubicacion del elemento
    INSERT
    INTO DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA VALUES
      (
        DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO_UBI.NEXTVAL,
        10,
        idElementoHyperview,
        ubicacionDC,
        'arsuarez',
        sysdate,
        '127.0.0.1'
      );
    INSERT
    INTO DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO VALUES
      (
        DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO.NEXTVAL,
        10,
        idElementoHyperview,
        'HYPERVIEW : '||array(i),
        'Activo',
        'arsuarez',
        sysdate,
        '127.0.0.1'
      );
    --HISTORIAL
    INSERT
    INTO DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO VALUES
      (
        DB_INFRAESTRUCTURA.SEQ_INFO_HISTORIAL_ELEMENTO.NEXTVAL,
        idElementoHyperview,
        'Activo',
        NULL,
        NULL,
        'arsuarez',
        SYSDATE,
        '127.0.0.1'
      );          

      FOR j IN 1..array_vc.count
         LOOP

	   INSERT
            INTO DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO VALUES
              (
                DB_INFRAESTRUCTURA.SEQ_INFO_RELACION_ELEMENTO.NEXTVAL,
                idElementoHyperview,
                (SELECT ID_ELEMENTO
                FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO
                WHERE NOMBRE_ELEMENTO = array_vc(j)
                AND estado            = 'Activo'
                ),
                'CONTIENE',
                NULL,
                NULL,
                NULL,
                'HYPERVIEW CONTIENE VCENTER',
                'Activo',
                'arsuarez',
                sysdate,
                '127.0.0.1'
              );

	  END LOOP;

  END LOOP;
  
  COMMIT;

EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  raise_application_error(-20001,'UN ERROR A OCURRIDO - '||SQLERRM || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE ||' -ERROR- '||SQLERRM);
END;