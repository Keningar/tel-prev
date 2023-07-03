SET SERVEROUTPUT ON
DECLARE
    --
    type                typeArray IS VARRAY(32) OF VARCHAR2(10);
    Ln_Total            NUMBER;
    Ln_Contador         NUMBER  := 9;
    Lr_VelValores       typeArray;
    Ln_Capacidad        NUMBER;
    --
BEGIN
    --
    Lr_VelValores := typeArray('45','50','55','60','65','70','75','80','85','90','95','100','105','110','115',
                    '120','125','130','135','140','145','150','155','160','165','170','175','180','185','190','195','200');
    --
    Ln_Total := Lr_VelValores.count;
    FOR i in 1 .. Ln_Total LOOP
        dbms_output.put_line('INSERT: ' || Lr_VelValores(i));
        --
        Ln_Capacidad := TO_NUMBER(Lr_VelValores(i))*1024;
        --
        INSERT INTO db_general.admi_parametro_det (
            id_parametro_det,
            parametro_id,
            descripcion,
            valor1,
            valor2,
            valor3,
            valor7,
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
                    nombre_parametro = 'PROD_VELOCIDAD_GPON'
            ),
            'PROD_VELOCIDAD_GPON',
            Lr_VelValores(i),
            'MB',
            Ln_Capacidad,
            Ln_Contador,
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
        Ln_Contador := Ln_Contador + 1;
    END LOOP;

    COMMIT;
    DBMS_OUTPUT.put_line('OK: Se guardaron los cambios.');

    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;
