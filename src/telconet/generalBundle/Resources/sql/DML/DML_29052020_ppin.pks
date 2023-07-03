/*
 * ➜ Ingresa una cabecera CARACTERISTICAS_SERVICIOS_SIMULTANEOS.
 *
 * ➜ Ingresa el detalle sobre el nuevo parámetro.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 29-05-2020 - Versión Inicial.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval, 'CARACTERISTICAS_SERVICIOS_SIMULTANEOS',
        'CARACTERISTICAS DE SERVICIOS PARA INSTALACION SIMULTANEA',
        'TECNICO', 'INSTALACION_SIMULTANEA', 'Activo', 'ppin', SYSDATE,
        '127.0.0.1', null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval, (SELECT ID_PARAMETRO
                                                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                    WHERE NOMBRE_PARAMETRO = 'CARACTERISTICAS_SERVICIOS_SIMULTANEOS'),
        'CARACTERISTICAS_SERVICIOS_SIMULTANEOS',
        '[{"PRODUCTO_ID":1117,"DESCRIPCION_PRODUCTO":"Networking LAN","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea Networking LAN","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":null},{"PRODUCTO_ID":1289,"DESCRIPCION_PRODUCTO":"TELCOHOME MG1","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":false,"HELPER":"Instalación simultánea TELCOHOME MG1","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":11945,"DESCRIPCION_CARACTERISTICA":"TIPO ELEMENTO","LABEL":"TIPO ELEMENTO"},{"ID_PRODUCTO_CARACTERISITICA":11941,"DESCRIPCION_CARACTERISTICA":"MARCA ELEMENTO","LABEL":"MARCA ELEMENTO"}]},{"PRODUCTO_ID":1281,"DESCRIPCION_PRODUCTO":"SAFE CAM","ID_CARACTERISTICA":1493,"DESCRIPCION_CARACTERISTICA":"INSTALACION_SIMULTANEA","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Instalación simultánea SAFE CAM","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":11945,"DESCRIPCION_CARACTERISTICA":"IP WAN","LABEL":"IP WAN"},{"ID_PRODUCTO_CARACTERISITICA":11941,"DESCRIPCION_CARACTERISTICA":"FIRMWARE","LABEL":"FIRMWARE"}]}]',
        '{"NO_MOSTRAR_DATA":[1298,1464]}', null, null, 'Activo', 'ppin',
        SYSDATE, '127.0.0.1', null, null, null, null, '10', null, null,
        null);


COMMIT;

/