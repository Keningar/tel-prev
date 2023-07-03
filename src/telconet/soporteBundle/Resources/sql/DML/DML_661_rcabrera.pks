declare

cursor cu_tareas is
SELECT 
nvl((select min(id_comunicacion) from DB_SOPORTE.info_comunicacion where detalle_id =  i0_.detalle_id),'1') numero_tarea,

(select NOMBRE_PROCESO from DB_SOPORTE.ADMI_PROCESO where id_proceso = (select proceso_id from DB_SOPORTE.admi_Tarea where id_tarea = (select tarea_id from DB_SOPORTE.info_detalle 
where id_detalle = i1_.ID_DETALLE))) nombre_proceso,

(select nombre_tarea from DB_SOPORTE.admi_Tarea where id_tarea = (select tarea_id from DB_SOPORTE.info_detalle 
where id_detalle = i1_.ID_DETALLE)) tarea,
i0_.asignado_nombre AS CUADRILLA_DEPARTAMENTO,
i0_.ref_asignado_nombre RESPONSABLE,

TO_CHAR (i1_.FE_CREACION, 'yyyy') anio,
TO_CHAR (i1_.FE_CREACION, 'mm') mes,
TO_CHAR (i1_.FE_CREACION, 'dd') dia,


i1_.FE_CREACION,
i2_.ESTADO,
(SELECT NOMBRE_CANTON FROM DB_SOPORTE.ADMI_CANTON WHERE ID_CANTON = ( SELECT CANTON_ID FROM DB_SOPORTE.INFO_OFICINA_GRUPO WHERE ID_OFICINA = 
( SELECT OFICINA_ID FROM DB_SOPORTE.INFO_PERSONA_EMPRESA_ROL WHERE ID_PERSONA_ROL = i0_.PERSONA_EMPRESA_ROL_ID))) CIUDAD,

i1_.id_detalle,
i0_.id_detalle_asignacion,
i2_.id_detalle_historial,
i0_.persona_empresa_rol_id,
(select departamento_id from DB_SOPORTE.info_persona_empresa_rol where id_persona_rol = i0_.persona_empresa_rol_id) departamento_id,
(select oficina_id from DB_SOPORTE.info_persona_empresa_rol where id_persona_rol = i0_.persona_empresa_rol_id) oficina_id

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
AND i2_.ESTADO NOT            IN ('Cancelada','Rechazada','Finalizada','Anulada')
AND i0_.persona_empresa_rol_id is not null
AND i1_.fe_creacion >= TO_DATE ('01/11/2017', 'dd/mm/yyyy')
order by i1_.fe_creacion desc;

ln_contador number := 0;

begin

for i in cu_tareas loop
ln_contador := ln_contador + 1;
insert into db_soporte.info_detalle_tareas values(i.id_detalle,i.numero_tarea,i.persona_empresa_rol_id,i.departamento_id,i.oficina_id,i.estado,i.id_detalle_asignacion,
i.id_detalle_historial,'rcabrera',sysdate);

end loop;
DBMS_OUTPUT.PUT_LINE('REG: '||ln_contador);
exception
when others then
rollback;

end;