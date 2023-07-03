/**
 * Documentación
 *
 * Restauración de las plantillas originales de los documentos de contrato digital
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 17-02-2020
 */

DECLARE
  registro_contrato_respaldo DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_pagare_respaldo   DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_debito_respaldo   DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_correo_respaldo   DB_COMUNICACION.ADMI_PLANTILLA%ROWTYPE;
BEGIN
    
  ------- CONTRATO ----- 
  SELECT t.* INTO registro_contrato_respaldo FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'contratoMegadatosBACKUP17022020';
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = registro_contrato_respaldo.HTML WHERE t.ID_EMPRESA_PLANTILLA = 1 AND t.COD_PLANTILLA = 'contratoMegadatos';
  ---------------------- 
  
    ------- PAGARE ----- 
  SELECT t.* INTO registro_pagare_respaldo FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'pagareMegadatosBACKUP17022020';
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = registro_pagare_respaldo.HTML WHERE t.ID_EMPRESA_PLANTILLA = 22 AND t.COD_PLANTILLA = 'pagareMegadatos';
  ---------------------- 
  
  ------- DEBITO ----- 
  SELECT t.* INTO registro_debito_respaldo FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'debitoMegadatosBACKUP17022020';
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = registro_debito_respaldo.HTML WHERE t.ID_EMPRESA_PLANTILLA = 21 AND t.COD_PLANTILLA = 'debitoMegadatos';
  -------------------- 
  
  ------- CORREO ----- 
  SELECT t.* INTO registro_correo_respaldo FROM DB_COMUNICACION.ADMI_PLANTILLA t WHERE t.CODIGO = 'CONTDIGITAL_OLD' AND t.NOMBRE_PLANTILLA = 'Notificacion Contrato Digital BACKUP17022020';
  
  UPDATE DB_COMUNICACION.ADMI_PLANTILLA t SET t.PLANTILLA = registro_correo_respaldo.PLANTILLA WHERE t.CODIGO = 'CONTDIGITAL_NEW' AND t.NOMBRE_PLANTILLA = 'Notificacion Contrato Digital' AND t.ESTADO = 'Activo';
  -------------------- 
  
  DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = registro_contrato_respaldo.ID_EMPRESA_PLANTILLA;
  DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = registro_pagare_respaldo.ID_EMPRESA_PLANTILLA;
  DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = registro_debito_respaldo.ID_EMPRESA_PLANTILLA;
  DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA t      WHERE t.ID_PLANTILLA = registro_correo_respaldo.ID_PLANTILLA;
  
  COMMIT;
  
EXCEPTION
  WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.put_line(SUBSTR(SQLERRM, 1, 2000));
END;
/