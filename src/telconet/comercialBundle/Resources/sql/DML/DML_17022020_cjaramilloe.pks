/**
 * Documentación UPDATE PLANTILLA DE DOCUMENTOS DIGITALES MEGADATOS
 *
 * Actualización del número telefónico de Netlife en las plantillas de los documentos de contrato digital.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 17-02-2020
 */

DECLARE
  registro_contrato DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_pagare   DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_debito   DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  registro_correo   DB_COMUNICACION.ADMI_PLANTILLA%ROWTYPE;
  
  plantilla_editada_contrato CLOB;
  plantilla_editada_pagare   CLOB;
  plantilla_editada_debito   CLOB;
  plantilla_editada_correo   CLOB;
BEGIN
    
  ------- CONTRATO ----- 
  SELECT t.* INTO registro_contrato FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = 1 AND t.COD_PLANTILLA = 'contratoMegadatos' AND t.ESTADO = 'Activo';
  
  INSERT INTO DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA (ID_EMPRESA_PLANTILLA, COD_PLANTILLA, EMPRESA_ID, DESCRIPCION, HTML, ESTADO, PROPIEDADES) 
  VALUES (DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA.NEXTVAL, 'contratoMegadatosBACKUP17022020', registro_contrato.EMPRESA_ID, registro_contrato.DESCRIPCION, registro_contrato.HTML, 'Inactivo', registro_contrato.PROPIEDADES);

  plantilla_editada_contrato := REPLACE(registro_contrato.HTML,'37-31-300','3920000');
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_contrato WHERE t.ID_EMPRESA_PLANTILLA = 1 AND t.COD_PLANTILLA = 'contratoMegadatos' AND t.ESTADO = 'Activo';
  ----------------------
  
  ------- PAGARE ----- 
  SELECT t.* INTO registro_pagare FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = 22 AND t.COD_PLANTILLA = 'pagareMegadatos' AND t.ESTADO = 'Activo';
  
  INSERT INTO DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA (ID_EMPRESA_PLANTILLA, COD_PLANTILLA, EMPRESA_ID, DESCRIPCION, HTML, ESTADO, PROPIEDADES) 
  VALUES (DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA.NEXTVAL, 'pagareMegadatosBACKUP17022020', registro_pagare.EMPRESA_ID, registro_pagare.DESCRIPCION, registro_pagare.HTML, 'Inactivo', registro_pagare.PROPIEDADES);

  plantilla_editada_pagare := REPLACE(registro_pagare.HTML,'37-31-300','3920000');
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_pagare WHERE t.ID_EMPRESA_PLANTILLA = 22 AND t.COD_PLANTILLA = 'pagareMegadatos' AND t.ESTADO = 'Activo';
  --------------------
  
  ------- DEBITO ----- 
  SELECT t.* INTO registro_debito FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.ID_EMPRESA_PLANTILLA = 21 AND t.COD_PLANTILLA = 'debitoMegadatos' AND t.ESTADO = 'Activo';
  
  INSERT INTO DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA (ID_EMPRESA_PLANTILLA, COD_PLANTILLA, EMPRESA_ID, DESCRIPCION, HTML, ESTADO, PROPIEDADES) 
  VALUES (DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA.NEXTVAL, 'debitoMegadatosBACKUP17022020', registro_debito.EMPRESA_ID, registro_debito.DESCRIPCION, registro_debito.HTML, 'Inactivo', registro_debito.PROPIEDADES);

  plantilla_editada_debito := REPLACE(registro_debito.HTML,'37-31-300','3920000');
  
  UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_debito WHERE t.ID_EMPRESA_PLANTILLA = 21 AND t.COD_PLANTILLA = 'debitoMegadatos' AND t.ESTADO = 'Activo';
  --------------------
  
  ------- CORREO ----- 
  SELECT t.* INTO registro_correo FROM DB_COMUNICACION.ADMI_PLANTILLA t WHERE t.CODIGO = 'CONTDIGITAL_NEW' AND t.ESTADO = 'Activo';
  
  INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA,NOMBRE_PLANTILLA,CODIGO,MODULO,PLANTILLA,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,EMPRESA_COD) 
  VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'Notificacion Contrato Digital BACKUP17022020', 'CONTDIGITAL_OLD', registro_correo.MODULO, registro_correo.PLANTILLA, 'Inactivo', SYSDATE, 'cjaramilloe', SYSDATE, 'cjaramilloe', registro_correo.EMPRESA_COD);

  plantilla_editada_correo := REPLACE(registro_correo.PLANTILLA,'37-31-300','3920000');
  
  UPDATE DB_COMUNICACION.ADMI_PLANTILLA t SET t.PLANTILLA = plantilla_editada_correo WHERE t.CODIGO = 'CONTDIGITAL_NEW' AND t.ESTADO = 'Activo';
  --------------------
  
  COMMIT;
  
EXCEPTION
  WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.put_line(SUBSTR(SQLERRM, 1, 2000));
END;
/