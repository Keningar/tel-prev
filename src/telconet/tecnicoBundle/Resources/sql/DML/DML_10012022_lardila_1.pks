/**
 * DEBE EJECUTARSE EN DB_GENERAL.
 * Parametrizaciones de Rango de visualizacion de saldo para MEGA DATOS
 * DESCRIPCION = RANGO DE VISUALIZACION DE SALDOS
 * VALOR1 = 5(Puntos a visualizar)
 
 * @author Luis Ardila Macias <lardila@telconet.ec>
 * @version 1.0 10-01-2022 - Versión Inicial.
 */

INSERT INTO DB_GENERAL.admi_parametro_cab VALUES(SEQ_ADMI_PARAMETRO_CAB.nextval,'RANGO_VISUALIZACION_SALDO','RANGO DE VISUALIZACION DE SALDOS',null,null,'Activo','lardila','04/01/22','0.0.0.0',null,null,null);
INSERT INTO DB_GENERAL.admi_parametro_det VALUES(SEQ_ADMI_PARAMETRO_DET.nextval,(select cab.id_parametro from DB_GENERAL.admi_parametro_cab cab where cab.nombre_parametro = 'RANGO_VISUALIZACION_SALDO'),'MEGADATOS','5',NULL,NULL,NULL,'Activo','lardila','04/01/22','0.0.0.0',null,null,null,null,null,null,null,null);

COMMIT;
/