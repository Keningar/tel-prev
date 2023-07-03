/** 
 * @author José Cruz <jfcruzc@telconet.ec>
 * @version 1.0 
 * @since 19-01-2023
 * Se crea DML de configuraciones cambio plan.
 */

DECLARE 
    Ln_IdParamCab             NUMBER;
BEGIN

Ln_IdParamCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
        (Ln_IdParamCab,
          'USR_CAMBIO_PLAN_TAREA_AUTO',
          'USR_CAMBIO_PLAN_TAREA_AUTO',
          'TECNICO',
          NULL,
          'Activo',
          'jfcruzc',
          SYSDATE,
          '127.0.0.1',
          NULL,
          NULL,
          NULL
        );
  COMMIT;
  


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
            Ln_IdParamCab,
            'USR_CAMBIO_PLAN_TAREA_AUTO',
            'chatbot',
            'ATC',
            NULL,
            NULL,
            'Activo',
            'jfcruzc',
            SYSDATE,
            '127.0.0.1',
            (
                SELECT COD_EMPRESA
                FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                WHERE PREFIJO = 'MD'
            )
    );
  COMMIT;
  

  
  
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
            Ln_IdParamCab,
            'USR_CAMBIO_PLAN_TAREA_AUTO',
            'extranet',
            'ATC',
            NULL,
            NULL,
            'Activo',
            'jfcruzc',
            SYSDATE,
            '127.0.0.1',
            (
                SELECT COD_EMPRESA
                FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                WHERE PREFIJO = 'MD'
            )
    );
  COMMIT;
  


  
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
            Ln_IdParamCab,
            'USR_CAMBIO_PLAN_TAREA_AUTO',
            'appMovil',
            'ATC',
            NULL,
            NULL,
            'Activo',
            'jfcruzc',
            SYSDATE,
            '127.0.0.1',
            (
                SELECT COD_EMPRESA
                FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                WHERE PREFIJO = 'MD'
            )
    );
  COMMIT;

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
            Ln_IdParamCab,
            'VALOR_TOP_DOWN_CAMBIO_PLAN_F_A1',
            '4',
            '0',
            NULL,
            NULL,
            'Activo',
            'jfcruzc',
            SYSDATE,
            '127.0.0.1',
            (
                SELECT COD_EMPRESA
                FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
                WHERE PREFIJO = 'MD'
            )
    );
  COMMIT;

  Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParamCab,'VALOR_TOP_DOWN_CAMBIO_PLAN_F_A1','4','0',null,null,'Activo','jfcruzc',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
  Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParamCab,'USR_CAMBIO_PLAN_TAREA_AUTO','appMovil','ATC',null,null,'Activo','jfcruzc',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
  Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParamCab,'USR_CAMBIO_PLAN_TAREA_AUTO','extranet','ATC',null,null,'Activo','jfcruzc',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
  Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,Ln_IdParamCab,'USR_CAMBIO_PLAN_TAREA_AUTO','chatbot','ATC',null,null,'Activo','jfcruzc',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
  COMMIT;

END;