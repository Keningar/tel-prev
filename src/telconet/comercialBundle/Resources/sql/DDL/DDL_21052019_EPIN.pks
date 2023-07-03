/**
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0
 * @since 20-05-2018
 * Se crean las sentencias DDL con los cambios en estructuras necesarios para contrato digital
 */

--Se crea campo que sirve para determinar si un registro se puede visualizar desde el TM-COMERCIAL
ALTER TABLE DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL ADD (MOSTRAR_APP VARCHAR2(1));

--Se agrega el comentario del campo correspondiente
COMMENT ON COLUMN DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL.MOSTRAR_APP   IS 'Determina si este registro es visible en TM-COMERCIAL';

--Se crea campo que sirve para determinar si un registro se puede visualizar desde el TM-COMERCIAL
ALTER TABLE DB_COMERCIAL.ADMI_FORMA_CONTACTO ADD (MOSTRAR_APP VARCHAR2(1));

--Se agrega el comentario del campo correspondiente
COMMENT ON COLUMN DB_COMERCIAL.ADMI_FORMA_CONTACTO.MOSTRAR_APP   IS 'Determina si este registro es visible en TM-COMERCIAL';

-- Se crea indice para mayor agilidad en la consulta de certificados
CREATE INDEX "DB_FIRMAELECT"."IDX_CEDULA_ESTADO" ON "DB_FIRMAELECT"."INFO_CERTIFICADO" ("NUM_CEDULA", "ESTADO");
