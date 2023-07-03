/*
 *
 * Se crea un nuevo motivo para la que el coordinador
 * pueda recuperar un empleado prestado con o sin cuadrilla
 *	 
 * @author Daniel Guzman <ddguzman@telconet.ec>
 * @version 1.0 18-01-2023
 *
*/
INSERT INTO DB_GENERAL.ADMI_MOTIVO
(
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        889,
        'Se recupera empleado prestado',
        'Activo',
        'ddguzman',
        SYSDATE,
        'ddguzman',
        SYSDATE
);

COMMIT;
/