SET SERVEROUTPUT ON
DECLARE
    --
    type                typeArray IS VARRAY(40) OF VARCHAR2(25);
    Ln_Total            NUMBER;
    Lr_PlanValores 	typeArray;
    Lr_VelValores       typeArray;
    Lr_CapValores 	typeArray;
    --
BEGIN
    --
    Lr_VelValores   := typeArray('5','10','15','20','25','30','35','40','45','50','55','60','65','70','75','80',
            '85','90','95','100','105','110','115','120','125','130','135','140','145','150','155','160','165','170','175',
            '180','185','190','195','200');
    --
    Lr_PlanValores  := typeArray('TN_INTERNET_5M','TN_INTERNET_10M','TN_INTERNET_15M','TN_INTERNET_20M',
            'TN_INTERNET_25M','TN_INTERNET_30M','TN_INTERNET_35M','TN_INTERNET_40M','TN_INTERNET_45M','TN_INTERNET_50M',
            'TN_INTERNET_55M','TN_INTERNET_60M','TN_INTERNET_65M','TN_INTERNET_70M','TN_INTERNET_75M','TN_INTERNET_80M',
            'TN_INTERNET_85M','TN_INTERNET_90M','TN_INTERNET_95M','TN_INTERNET_100M','TN_INTERNET_105M','TN_INTERNET_110M',
            'TN_INTERNET_115M','TN_INTERNET_120M','TN_INTERNET_125M','TN_INTERNET_130M','TN_INTERNET_135M','TN_INTERNET_140M',
            'TN_INTERNET_145M','TN_INTERNET_150M','TN_INTERNET_155M','TN_INTERNET_160M','TN_INTERNET_165M','TN_INTERNET_170M',
            'TN_INTERNET_175M','TN_INTERNET_180M','TN_INTERNET_185M','TN_INTERNET_190M','TN_INTERNET_195M','TN_INTERNET_200M');
    --
    Lr_CapValores   := typeArray('5000','10000','15000','20000','25000','30000','35000','40000','45000','50000','55000',
            '60000','65000','70000','75000','80000','85000','90000','95000','100000','105000','110000','115000','120000',
            '125000','130000','135000','140000','145000','150000','155000','160000','165000','170000','175000','180000',
            '185000','190000','195000','200000');
    --
    Ln_Total := Lr_VelValores.count;
    FOR i in 1 .. Ln_Total LOOP 
        dbms_output.put_line('INSERT: ' || Lr_VelValores(i));
        --insert INTMPLS
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
        (
            ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            VALOR3,
            VALOR4,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION,
            EMPRESA_COD
        )
        VALUES
        (
            DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            (
                SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                WHERE NOMBRE_PARAMETRO = 'MAPEO_VELOCIDAD_PERFIL'
                AND ESTADO = 'Activo'
            ),
            'Mapeo de perfiles de acuerdo a la velocidad del producto Internet MPLS',
            Lr_VelValores(i),
            Lr_PlanValores(i),
            'INTMPLS',
            Lr_CapValores(i),
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
        --insert INTERNET
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
        (
                ID_PARAMETRO_DET,
                PARAMETRO_ID,
                DESCRIPCION,
                VALOR1,
                VALOR2,
                VALOR3,
                VALOR4,
                ESTADO,
                USR_CREACION,
                FE_CREACION,
                IP_CREACION,
                EMPRESA_COD
        )
        VALUES
        (
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (
                    SELECT ID_PARAMETRO
                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                    WHERE NOMBRE_PARAMETRO = 'MAPEO_VELOCIDAD_PERFIL'
                    AND ESTADO = 'Activo'
                ),
                'Mapeo de perfiles de acuerdo a la velocidad del producto Internet Dedicado',
                Lr_VelValores(i),
                Lr_PlanValores(i),
                'INTERNET',
                Lr_CapValores(i),
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
