--@author wvera Update a la admitarea para saber que motivos tienen que pedir fibra óptica

UPDATE  DB_SOPORTE.ADMI_TAREA AT
  SET AT.REQUIERE_FIBRA ='S'
  WHERE AT.ID_TAREA IN ('2611', '2662');

  COMMIT;