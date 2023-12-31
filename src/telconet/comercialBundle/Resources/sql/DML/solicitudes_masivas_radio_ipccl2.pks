-----------------------------------------------
--           ESQUEMA DB_COMERCIAL
-----------------------------------------------

--INSERTS CARACTERISTICAS

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO) 
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'Estado Radio','T','Activo',sysdate,'rsalgado','COMERCIAL');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA 
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO) 
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'Estado IPCCL2','T','Activo',sysdate,'rsalgado','COMERCIAL');



DECLARE
  CURSOR MODELO
  IS
    SELECT AME.NOMBRE_MODELO_ELEMENTO,
      AME.CAPACIDAD_ENTRADA,
      AME.UNIDAD_MEDIDA_ENTRADA,
      AME.CAPACIDAD_SALIDA,
      AME.UNIDAD_MEDIDA_SALIDA,
      AMCD.MODELO_CPE,
      AMCD.MAX_BW_PERMITIDO_SUBIDA,
      AMCD.MAX_BW_PERMITIDO_BAJADA
    FROM ADMI_MODELO_ELEMENTO AME
    JOIN DBSIT.admi_modelo_cpe_dat AMCD
    ON AME.NOMBRE_MODELO_ELEMENTO = AMCD.MODELO_CPE;
BEGIN
  FOR ITEM IN MODELO
  LOOP
    UPDATE DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME
    SET AME.CAPACIDAD_ENTRADA        = ITEM.MAX_BW_PERMITIDO_SUBIDA,
      AME.UNIDAD_MEDIDA_ENTRADA      = 'KBPS',
      AME.CAPACIDAD_SALIDA           = ITEM.MAX_BW_PERMITIDO_BAJADA,
      AME.UNIDAD_MEDIDA_SALIDA       = 'KBPS'
    WHERE AME.NOMBRE_MODELO_ELEMENTO = ITEM.MODELO_CPE;
  END LOOP;
END;