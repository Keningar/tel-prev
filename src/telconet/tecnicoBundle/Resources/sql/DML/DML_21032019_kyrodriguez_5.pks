--=======================================================================
--   Script de regularización para actualizar los racks existentes en 
--   producción que deben ser de marca RINORACK tarea.
--=======================================================================

UPDATE DB_INFRAESTRUCTURA.INFO_ELEMENTO 
SET MODELO_ELEMENTO_ID= (select id_modelo_elemento
      from DB_INFRAESTRUCTURA.admi_modelo_ELEMENTO
      join DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
      on id_marca_elemento=marca_elemento_id
      where NOMBRE_MARCA_ELEMENTO='RINORACK' and tipo_elemento_id=227)
WHERE id_Elemento in (
                        select 
                        ELE.ID_ELEMENTO
                        from DB_INFRAESTRUCTURA.INFO_ELEMENTO ELE
                        JOIN DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EEU
                        ON EEU.ELEMENTO_ID=ELE.ID_ELEMENTO
                        JOIN DB_INFRAESTRUCTURA.INFO_UBICACION UBI
                        ON UBI.ID_UBICACION        =EEU.UBICACION_ID
                        WHERE ELE.estado='Activo'
                        and UBI.PARROQUIA_ID=935
                        and ELE.NOMBRE_ELEMENTO in (
                                                      'F05 RK07',
                                                      'F05 RK08',
                                                      'F05 RK09',
                                                      'F09 RK11',
                                                      'F09 RK12',
                                                      'F09 RK13',
                                                      'F09 RK14',
                                                      'F09 RK16',
                                                      'F09 RK17',
                                                      'F09 RK18',
                                                      'F09 RK19',
                                                      'F09 RK20'
                                                    )
                      );

commit;
/