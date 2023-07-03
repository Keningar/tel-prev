
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET                                    
WHERE lower(descripcion) = 'el canal del futbol'
  AND valor1 = 'ECDF';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET                                    
WHERE descripcion = 'CORREO ELECTRONICO'
  AND valor1 = 'ECDF';

COMMIT;

/                    