-- Parametros nuevos creados para el proyecto
-- CAB - Insert para nueva parametrizacion cabecera
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
  ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION
)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
 'PRODUCTOS ADICIONALES AUTOMATICOS',
 'LISTA DE PARAMETROS PARA LOS PRODUCTOS AUTOMATICOS QUE FUNCIONARAN CON EL INTERNET DE FORMA SIMULTANEA',
 'COMERCIAL','Activo','djreyes',SYSDATE,'127.0.0.1');

-- DET 1A - Insert para nuevo detalle de lista de los productos permitidos
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
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de productos adicionales automaticos','1263','ECOMMERCE BASIC','SI',null,'Activo',
    'djreyes',sysdate,'127.0.0.1',null,18,null, null,
    'Valor1 es codigo del producto, valor2 es la descripcion, valor3 si es prodcuto Konibit'
);
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
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de productos adicionales automaticos','1262','Netlife Assistance Pro','SI',null,'Activo',
    'djreyes',sysdate,'127.0.0.1',null,18,null, null,
    'Valor1 es codigo del producto, valor2 es la descripcion, valor3 si es prodcuto Konibit'
);
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
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de productos adicionales automaticos','1130','NetlifeAssistance','NO',null,'Activo',
    'djreyes',sysdate,'127.0.0.1',null,18,null, null,
    'Valor1 es codigo del producto, valor2 es la descripcion, valor3 si es prodcuto Konibit'
);

-- DET 2A - Insert para nuevo detalle de estados permitidos para el servicio de internet
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Estados permitidos del servicio internet','Factible','PrePlanificada','Planificada','Replanificada',
    'Activo','djreyes',sysdate,'127.0.0.1','AsignadoTarea',18,'Asignada','EnVerificacion'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Estados permitidos del servicio internet','Detenido','Activo',null,null,
    'Activo','djreyes',sysdate,'127.0.0.1',null,18,null,null
);

-- DET 3A - Insert para nuevo detalle de estados permitidos para productos adicionales
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Estados permitidos para los productos adicionales','Pendiente','Activo',null,null,
    'Activo','djreyes',sysdate,'127.0.0.1',null,18,null,null
);

-- DET 4A - Insert para los reintentos y delay para los konibit adicionales
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
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Reintentos y delay para los productos adicionales',3,40000,null,null,
    'Activo','djreyes',sysdate,'127.0.0.1',null,18,null,null,
    'Valor1 cantidad de reintentos permitidos, valor2 es el tiempo en milisegundos maximo de la transaccion'
);
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
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Reintentos y delay para los productos incluidos',1,40000,null,null,
    'Activo','djreyes',sysdate,'127.0.0.1',null,18,null,null,
    'Valor1 cantidad de reintentos permitidos, valor2 es el tiempo en milisegundos maximo de la transaccion'
);

-- DET 5A - Insert para los correos que deben enviarse al fallar konibit
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Lista de correos a enviar para error en konibit','marketing@netlife.net.ec',
    'informaticos@netlife.net.ec','lbarahona@netlife.net.ec',null,'Activo',
    'djreyes',sysdate,'127.0.0.1',null,18,null, null
);

-- DET 6A - Insert para los productos automaticos que se deben trasladar con el internet
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
(
  ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,VALOR5,EMPRESA_COD,VALOR6,VALOR7
)
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS'
    ),
    'Listado de productos automatico a trasladar no activos',1263,1262,1130,null,
    'Activo','djreyes',sysdate,'127.0.0.1',null,18,null, null
);


-- Nuevos parametros anexos al proyecto
-- DET 1B Insert para mostrar desde movil los productos adicionales que se activaran
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
    'ECOMMERCE BASIC','KO02',null,null,'NO','Activo','djreyes',sysdate,'127.0.0.1','NO',null,null,null,
    'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);
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
    'Netlife Assistance Pro','KO01',null,null,'NO','Activo','djreyes',sysdate,'127.0.0.1','NO',null,null,null,
    'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);
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
    'NetlifeAssistance','ASSI',null,null,'NO','Activo','djreyes',sysdate,'127.0.0.1','NO',null,null,null,
    'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

-- Nuevas caracteristicas para los productos con konibit
-- KON 1C Insert para crear nueva caracteristica del producto konibit
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
(
  ID_CARACTERISTICA, DESCRIPCION_CARACTERISTICA, TIPO_INGRESO,
  ESTADO, FE_CREACION, USR_CREACION, TIPO
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ACTIVO KONIBIT','N','Activo',sysdate,'djreyes','TECNICA'
);

-- KON 2C Insert para crear nuevos productos caracteristica
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, '1262',
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'ACTIVO KONIBIT'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
(
  ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
  FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL
)
VALUES 
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL, '1263',
    (
        SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
        where DESCRIPCION_CARACTERISTICA = 'ACTIVO KONIBIT'
    ),
    sysdate, 'djreyes', 'Activo', 'NO'
);

-- DET 1D - Para insertar nueva plantilla para correo de error en konibit
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO,
  FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificacion de error en konibit','NOT_ERR_KON','TECNICO',
    '<html>
       <head>
          <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
          <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
       </head>
       <body>
          <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
             <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;"><img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/></td>
             </tr>
             <tr>
                <td style="border:1px solid #6699CC;">
                   <table width="100%" cellspacing="0" cellpadding="5">
                      <tr>
                         <td colspan="2">Estimado personal,</td>
                      </tr>
                      <tr>
                        <td colspan="2">El cliente {{cliente}} con el login {{login}} no pudo activar el producto {{producto}} en la plataforma de konibit por el/los error/es:</td>
                      </tr>
                      <tr>
                           <td></td>
                     </tr>
                      <tr>
                         <td colspan="2">
                           <table class = "cssTable"  align="center" >
                               <tr>
                                    <th> Errores </th>
                               </tr>
                               {{ mensaje | raw }}
                            </table>
                         </td>
                      </tr>
                      <tr>
                           <td></td>
                     </tr>
                     <tr>
                       <td>
                          <hr />
                       </td>
                     </tr>
                   </table>
                </td>
             </tr>
             <tr>
                <td></td>
             </tr>
          </table>
       </body>
    </html>',
    'Activo',sysdate,'djreyes',null,null,18
);

COMMIT;
/