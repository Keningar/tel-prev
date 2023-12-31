
/**
 * Documentación para la creación de caracteristica de Holding.
 *
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 30-10-2020
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
'HOLDING DE EMPRESAS',
'LISTADO DE RAZONES SOCIALES',
'COMERCIAL',
'Activo',
'mdleon',
sysdate,
'127.0.0.1'
);


INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'HOLDING EMPRESARIAL',
'N',
'Activo',
sysdate,
'mdleon',
'COMERCIAL');


COMMIT;

/