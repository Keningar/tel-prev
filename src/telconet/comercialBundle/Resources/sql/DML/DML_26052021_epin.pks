INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            (SELECT ID_PARAMETRO
             FROM DB_GENERAL.ADMI_PARAMETRO_CAB
             WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL'), 'ESTADO_SERVICIO','Pre-servicio,Factible,PrePlanificada,Planificada,Activo', 
             'Pendiente,PrePlanificada', '5', null, 'Activo', 'epin', SYSDATE, '127.0.0.1', NULL, NULL, NULL, null, NULL, null, null,
             'Estados a considerar para determinar si un servicio se esta intentando duplicar desde tm-comercial;  valor1: Estado de planes de internet, valor2: Estado de productos adicionales, valor3: tiempo en minutos que se consideran para verificar si un producto adicional ya existe');

COMMIT;