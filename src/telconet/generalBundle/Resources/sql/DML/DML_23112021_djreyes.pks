INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS'
    ),
    'GolTv Play','GTV1',null,null,'SI','Activo','djreyes',sysdate,'127.0.0.1','NO',null,null,null,
    'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

COMMIT;
/
