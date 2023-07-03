/**
 * Documentación INSERT DE CARACTERÍSTICAS DE PROMOCIONES EDITADAS
 * INSERT de parámetros en la estructura  B_COMERCIAL.ADMI_CARACTERISTICA.
 *
 * Se insertan parámetros para verificar si la nueva promoción proviene de una editada.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 21-10-2020
 */

--CREACIÓN DE LA CARACTERÍSTICA *ORIGEN PROMOCIÓN EDITADA*
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    FE_CREACION,
    USR_CREACION,
    TIPO,
    ESTADO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
   'ORIGEN_PROMOCION_EDITADA',
   'T',
    SYSDATE,
   'kyager',
   'COMERCIAL',
   'Activo'
);

COMMIT;

/