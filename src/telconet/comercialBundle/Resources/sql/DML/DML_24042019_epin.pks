/**
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0
 * @since 20-05-2019
 * Se crean las sentencias DML para  updates necesarios para el funcionamiento de nuevo tm-comercial
 */


--Se actualiza los tipos de documentos que serán visibles desde el tm-comercial
UPDATE DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL
   SET MOSTRAR_APP = 'S'
WHERE DESCRIPCION_TIPO_DOCUMENTO IN ('CEDULA', 'CONTRATO', 'OTROS', 'FORMA DE PAGO', 'DOCUMENTO FINANCIERO', 'FOTO', 'CEDULA REVERSO');

UPDATE DB_COMERCIAL.ADMI_FORMA_CONTACTO
   SET MOSTRAR_APP = 'S'
WHERE DESCRIPCION_FORMA_CONTACTO IN ('Facebook', 'Twitter', 'Telefono Fijo', 'Telefono Movil Claro', 'Telefono Movil Movistar', 'Telefono Movil CNT', 'Correo Electronico', 'WhatsApp');

COMMIT;

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'DOCUMENTOS_OBLIGATORIO', 'Documentos obligatorio para contratos digital', 'COMERCIAL', NULL, 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                        WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO'),
       'CEDULA', 'NAT', '18', 1, NULL, 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                        WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO'),
       'CEDULA REVERSO', 'NAT', '18', 138, NULL, 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                        WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO'),
       'FOTO', 'NAT', '18', 134, NULL, 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, null, null, null, null, null);

COMMIT;