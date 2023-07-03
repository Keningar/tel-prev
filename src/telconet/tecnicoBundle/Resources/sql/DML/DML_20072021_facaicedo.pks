--=======================================================================
-- Actualizaciones al cambio de tipo de red GPON_MPLS
--=======================================================================

--actualizar precios de los productos por tipo red
UPDATE db_comercial.ADMI_PRODUCTO SET FUNCION_PRECIO = 'if ("[TIPO_RED]"=="GPON_MPLS") {
    if ([CAPACIDAD1]>=1024 && [CAPACIDAD1]<=5120) { PRECIO = 19.00; }
    else if ([CAPACIDAD1]>5120 && [CAPACIDAD1]<=10240) { PRECIO = 24.99; }
    else if ([CAPACIDAD1]>10240 && [CAPACIDAD1]<=15360) { PRECIO = 33.40; }
    else { PRECIO = 50; }
    Math.round(PRECIO * 100) / 100;
}
else{
    if("[Zona]"=="Zona3"){
            if ([CAPACIDAD2] >= [CAPACIDAD1]) { PRECIO=1000*Math.ceil([CAPACIDAD2]/1024);}
            else{	 PRECIO=1000*Math.ceil([CAPACIDAD2]/1024)*1000;}
    }
    else{
            if ( "[Grupo Negocio]" == "ISP" ) {

                    if ( "[Zona]"=="Zona1"||"[Zona]"=="Zona2") {
                            if ( [CAPACIDAD1]== [CAPACIDAD2] ){
                    if ([CAPACIDAD1]<52224)  { PRECIO=20*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=52224 && [CAPACIDAD1]<102400)  { PRECIO=20*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=102400 && [CAPACIDAD1]<204800)  { PRECIO=15*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=204800 && [CAPACIDAD1]<409600)  { PRECIO=13*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=409600 && [CAPACIDAD1]<1024000)  { PRECIO=12*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=1024000 && [CAPACIDAD1]<1536000)  { PRECIO=11*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=1536000)  { PRECIO=10*([CAPACIDAD1]/1024);  }
                    }

            Math.round(PRECIO * 100) / 100;
            }
            } else if ( "[Grupo Negocio]" == "Corporativo" ) {
                    if ( "[Zona]"=="Zona1"||"[Zona]"=="Zona2") {
                            if ( [CAPACIDAD1]== [CAPACIDAD2] ){
                    if ([CAPACIDAD1]<=1024)  { PRECIO=40;  }
                    else if ([CAPACIDAD1]>1024 && [CAPACIDAD1]<2048)  { PRECIO=40*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=2048 && [CAPACIDAD1]<3072)  { PRECIO=35*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=3072 && [CAPACIDAD1]<4096)  { PRECIO=30*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=4096 && [CAPACIDAD1]<5120)  { PRECIO=28*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=5120)  { PRECIO=25*([CAPACIDAD1]/1024);  }
                    }

            Math.round(PRECIO * 100) / 100;
            }

            } else if ( "[Grupo Negocio]" == "Cybers" ) {

                    if( [CAPACIDAD2] >= [CAPACIDAD1]) {
                            if( [CAPACIDAD2] < 512 ){PRECIO=150;}
                            else if( [CAPACIDAD2] <=768 ) {PRECIO=139.8;}
                            else if( [CAPACIDAD2] <=1024) {PRECIO=120;}
                            else if( [CAPACIDAD2] >=2048) {PRECIO=105;}
                            else if( [CAPACIDAD2] >=2048) {PRECIO=135;}
                    } else {
                            if( [CAPACIDAD1] < 512 ){PRECIO=150;}
                            else if( [CAPACIDAD1] <=768 ) {PRECIO=139.8;}
                            else if( [CAPACIDAD1] <=1024) {PRECIO=120;}
                            else if( [CAPACIDAD1] >=2048) {PRECIO=105;}
                            else if( [CAPACIDAD1] >=2048) {PRECIO=135;}
                    }
            }
    }
}'
WHERE ID_PRODUCTO = ( SELECT
        id_producto
    FROM
        db_comercial.admi_producto
    WHERE
        descripcion_producto = 'Internet Dedicado'
        AND empresa_cod = 10
        AND nombre_tecnico <> 'FINANCIERO'
        AND estado = 'Activo'
);

UPDATE db_comercial.ADMI_PRODUCTO SET FUNCION_PRECIO = 'if ("[TIPO_RED]"=="GPON_MPLS") {
    if ([CAPACIDAD1]>=1024 && [CAPACIDAD1]<=5120) { PRECIO = 19.00; }
    else if ([CAPACIDAD1]>5120 && [CAPACIDAD1]<=10240) { PRECIO = 24.99; }
    else if ([CAPACIDAD1]>10240 && [CAPACIDAD1]<=15360) { PRECIO = 33.40; }
    else { PRECIO = 50; }
}
else{
    if ( "[Zona]"=="Zona1"||"[Zona]"=="Zona2") {
            if ( [CAPACIDAD1]== [CAPACIDAD2] ){
                    if ([CAPACIDAD1]<=1024)  { PRECIO=40;  }
                    else if ([CAPACIDAD1]>1024 && [CAPACIDAD1]<2048)  { PRECIO=40*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=2048 && [CAPACIDAD1]<3072)  { PRECIO=35*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=3072 && [CAPACIDAD1]<4096)  { PRECIO=30*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=4096 && [CAPACIDAD1]<5120)  { PRECIO=28*([CAPACIDAD1]/1024);  }
                    else if ([CAPACIDAD1]>=5120)  { PRECIO=25*([CAPACIDAD1]/1024);  }
            }
    }
    else {
            if ([CAPACIDAD2] >= [CAPACIDAD1]){PRECIO=1000*Math.ceil([CAPACIDAD2]/1024);}
            else{PRECIO=1000*Math.ceil([CAPACIDAD2]/1024)*1000;}
    }
}
Math.round(PRECIO * 100) / 100;'
WHERE ID_PRODUCTO = ( SELECT
        id_producto
    FROM
        db_comercial.admi_producto
    WHERE
        descripcion_producto = 'L3MPLS'
        AND empresa_cod = 10
        AND nombre_tecnico <> 'FINANCIERO'
        AND estado = 'Activo'
);

--actualizar parametro PROD_TIPO_RED - GPON con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR1 = 'GPON_MPLS', PDE.VALOR2 = 'GPON_MPLS', ESTADO = 'Activo' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'PROD_TIPO_RED'
    ) AND DET.DESCRIPCION = 'PROD_TIPO_RED' AND DET.VALOR1 = 'GPON' AND DET.VALOR2 = 'GPON'
);

--actualizar parametro ENLACE_DATOS con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR3 = 'GPON_MPLS' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.VALOR2 = 'ENLACE_DATOS' AND DET.VALOR3 = 'GPON'
);

--actualizar parametro RELACION_PRODUCTO_CARACTERISTICA con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR3 = 'GPON_MPLS' WHERE PDE.ID_PARAMETRO_DET IN (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.DESCRIPCION = 'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO' AND DET.VALOR3 = 'GPON'
    AND DET.VALOR5 = 'RELACION_PRODUCTO_CARACTERISTICA'
);

--actualizar parametro VERIFICAR_GPON con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR2 = 'GPON_MPLS' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.DESCRIPCION = 'VERIFICAR TIPO RED' AND DET.VALOR1 = 'VERIFICAR_GPON' AND DET.VALOR2 = 'GPON'
);

--actualizar parametro VPN TIPO RED - VPN_GPON con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR1 = 'GPON_MPLS' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.DESCRIPCION = 'VPN TIPO RED' AND DET.VALOR1 = 'GPON' AND DET.VALOR2 = 'VPN_GPON'
);

--actualizar parametro CONFIG_PRODUCTO_DATOS_SAFE_CITY - AGREGAR_SERVICIO_ADICIONAL con tipo red GPON_MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR7 = 'GPON_MPLS' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
    ) AND DET.VALOR2 = 'AGREGAR_SERVICIO_ADICIONAL'
);

--actualizar INFO_SERVICIO_PROD_CARACT con tipo red GPON_MPLS
UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT CARACT SET CARACT.VALOR = 'GPON_MPLS'
WHERE CARACT.ID_SERVICIO_PROD_CARACT IN (
  SELECT CAR.ID_SERVICIO_PROD_CARACT FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT CAR
  INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PRC ON PRC.ID_PRODUCTO_CARACTERISITICA = CAR.PRODUCTO_CARACTERISITICA_ID
  INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA            C ON C.ID_CARACTERISTICA             = PRC.CARACTERISTICA_ID
  WHERE C.DESCRIPCION_CARACTERISTICA = 'TIPO_RED'
  AND CAR.VALOR = 'GPON'
);

--actualizar parametro PROD_TIPO_RED - GPON definir red por default MPLS
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR4 = 'DEFAULT' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'PROD_TIPO_RED'
    ) AND DET.DESCRIPCION = 'PROD_TIPO_RED' AND DET.VALOR1 = 'MPLS' AND DET.VALOR2 = 'MPLS'
);

--INGRESAR PROTOCOLO STANDARD PARA ENRUTAMIENTO ESTATICO
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VERIFICAR TIPO ENRUTAMIENTO',
        'VERIFICAR_TIPO_ENRUTAMIENTO',
        'STANDARD',
        'ESTATICO',
        'DEFAULT',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--INGRESAR PROTOCOLO BGP PARA ENRUTAMIENTO DINAMICO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VERIFICAR TIPO ENRUTAMIENTO',
        'VERIFICAR_TIPO_ENRUTAMIENTO',
        'BGP',
        'DINAMICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

COMMIT;
/
