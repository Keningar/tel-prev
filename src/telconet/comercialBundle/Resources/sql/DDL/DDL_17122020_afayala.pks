DECLARE
    last_login_aux         INTEGER      := '0';

BEGIN
    --Traemos un listado con todos los servicios que cumplan con las condiciones.
    FOR o IN (
        SELECT ser.id_servicio,
               ser.punto_id,
               ser.producto_id,
               ser.login_aux,
               ser.ESTADO,
               pt.login
        FROM   db_comercial.info_servicio ser 
               LEFT JOIN db_comercial.info_punto pt ON ser.punto_id = pt.id_punto
        WHERE ser.estado = 'Activo'
          AND pt.estado = 'Activo'
          AND ser.producto_id in (1236,1354,1353,1275,1273,1271,1247,1241,1155)
          AND ser.login_aux IS NULL
        )
        --Hacemos un loop por cada uno de los resultados obtenidos en el query anterior.
        LOOP
            --Hacemos un select para obtener el mayor numero dentro del subquery y lo insertamos en una variable.
            SELECT NVL(MAX(SERV.login_split), 0)
            INTO last_login_aux
            FROM (
                     --En este subquery buscamos traer todos los servicios que tengan login auxiliar en el punto para luego
                     -- obtener el de mayor numero.
                     SELECT SUBSTR(ser2.LOGIN_AUX, INSTR(ser2.LOGIN_AUX, '_' ,-1,1) + 1) as login_split
                     FROM db_comercial.info_servicio ser2
                             LEFT JOIN db_comercial.info_punto pt2 ON pt2.ID_PUNTO = ser2.PUNTO_ID
                     WHERE ser2.estado = 'Activo' 
                       AND pt2.estado = 'Activo'
                       AND ser2.login_aux IS NOT NULL
                       AND ser2.PUNTO_ID = o.PUNTO_ID) SERV;
            --Como ya tenemos el ultimo login auxiliar, procedemos a aumentarle 1.
            last_login_aux := last_login_aux + 1;

            --Hacemos el update correspondiente al servicio con el login conseguido.
            UPDATE db_comercial.info_servicio
            SET login_aux = o.login || '_' || last_login_aux
            WHERE id_servicio = o.id_servicio;

            -- Insertamos el historial.
            INSERT INTO db_comercial.info_servicio_historial
            VALUES (db_comercial.seq_info_servicio_historial.nextval,
                    o.ID_SERVICIO, 'regulaLoginAux', SYSDATE,
                    '127.0.0.1', o.ESTADO,
                    NULL, 'Se regulariza login auxiliar servicios bajo red GPON', NULL);
                    
            COMMIT;

        END LOOP;
END ;

/
