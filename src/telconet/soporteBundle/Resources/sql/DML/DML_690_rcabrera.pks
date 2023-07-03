declare

-- Se actualiza el tipo asignado para las asignaciones que tienen este campo en null.

CURSOR cu_tareas is 
SELECT 

i0_.id_detalle_asignacion,
i1_.id_Detalle,
i1_.FE_CREACION,
i2_.estado


FROM db_soporte.INFO_DETALLE_ASIGNACION i0_,
  db_soporte.INFO_DETALLE i1_,
  db_soporte.INFO_DETALLE_HISTORIAL i2_
WHERE i0_.DETALLE_ID         = i1_.ID_DETALLE
AND i1_.ID_DETALLE           = i2_.DETALLE_ID
AND i2_.ID_DETALLE_HISTORIAL =
  (SELECT MAX(i3_.ID_DETALLE_HISTORIAL) AS dctrn__2
  FROM db_soporte.INFO_DETALLE_HISTORIAL i3_
  WHERE i3_.DETALLE_ID = i0_.DETALLE_ID
  )
AND i0_.ID_DETALLE_ASIGNACION =
  (SELECT MAX(i4_.ID_DETALLE_ASIGNACION) AS dctrn__3
  FROM db_soporte.INFO_DETALLE_ASIGNACION i4_
  WHERE i4_.DETALLE_ID = i0_.DETALLE_ID
  )
  AND i0_.tipo_asignado is null
AND i2_.ESTADO NOT IN ('Cancelada','Rechazada','Finalizada','Anulada');

ln_contador number:= 0;

begin

for i in cu_tareas loop
ln_contador := ln_contador + 1;

update db_soporte.INFO_DETALLE_ASIGNACION set tipo_asignado = 'EMPLEADO' where ID_DETALLE_ASIGNACION = i.id_detalle_asignacion;

end loop;

dbms_output.put_line('Registros Actualizados: '||ln_contador);

exception
when others then
dbms_output.put_line('Error: '||sqlerrm);
rollback;

end;