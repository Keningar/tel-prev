--AGREGAMOS CODIGOS DE PLANTILLAS TEMPORALES.------

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ENC-VIS-TN-PLT';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ENC-INST-TN-PLT';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ACT-ENT-TN-INSP';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ACT-ENT-TN-VISP';

COMMIT;