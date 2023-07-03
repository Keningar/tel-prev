--=======================================================================
-- Ingresar mapeo velocidad para los traffic table de los servicios TN MPLS-GPON
--=======================================================================
SET SERVEROUTPUT ON
DECLARE
    --
    type                typeArray IS VARRAY(40) OF VARCHAR2(10);
    Ln_Total            NUMBER;
    Lr_VelValores       typeArray;
    Lr_TrafficValores   typeArray;
BEGIN
    --
    Lr_VelValores       := typeArray('5','10','15','20','25','30','35','40','45','50','55','60','65','70','75','80',
            '85','90','95','100','105','110','115','120','125','130','135','140','145','150','155','160','165','170',
            '175','180','185','190','195','200');
    --
    Lr_TrafficValores   := typeArray('56','11','16','19','25','31','35','39','47','43','57','62','65','71','76','81',
            '85','91','95','126','105','110','115','121','127','130','135','141','145','151','155','161','165','170',
            '175','181','185','190','195','202');
    --
    INSERT INTO db_general.admi_parametro_cab (
        id_parametro,
        nombre_parametro,
        descripcion,
        modulo,
        estado,
        usr_creacion,
        fe_creacion,
        ip_creacion
    ) VALUES (
        db_general.seq_admi_parametro_cab.nextval,
        'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON',
        'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
    );
    --
    Ln_Total := Lr_VelValores.count;
    FOR i in 1 .. Ln_Total LOOP 
        dbms_output.put_line('INSERT: ' || Lr_VelValores(i));
        INSERT INTO db_general.admi_parametro_det (
            id_parametro_det,
            parametro_id,
            descripcion,
            valor1,
            valor2,
            estado,
            usr_creacion,
            fe_creacion,
            ip_creacion,
            empresa_cod
        ) VALUES (
            db_general.seq_admi_parametro_det.nextval,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON'
            ),
            'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON',
            Lr_VelValores(i),
            Lr_TrafficValores(i),
            'Activo',
            'facaicedo',
            SYSDATE,
            '127.0.0.1',
            (
                SELECT COD_EMPRESA
                FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                WHERE PREFIJO = 'TN'
            )
        );
    END LOOP;

    COMMIT;
    DBMS_OUTPUT.put_line('OK: Se guardaron los cambios.');

    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;
