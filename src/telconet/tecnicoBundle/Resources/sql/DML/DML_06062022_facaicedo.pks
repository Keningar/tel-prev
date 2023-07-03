
-- INGRESO DEL DETALLE DE PARAMETROS PARA LA NOTIFICACION PASSWORD CAMARA
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'NOTIFICACION_PASSWORD_CAMARA',
    'tecnicoBundle:InfoServicio:mailerPasswordCamSafeCity.html.twig',
    'Notificación Datos Configuración Cámara SafeCity',
    'notificaciones_telcos@telconet.ec',
    'El presente correo es para mostrar los datos de configuración de la cámara safecity:',
    'lescandon@telconet.ec',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

COMMIT;
/
