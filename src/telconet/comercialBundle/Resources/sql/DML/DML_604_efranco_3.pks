DECLARE
  --
  CURSOR C_GetServicios
  IS
    --
    SELECT
      ID_DASHBOARD_SERVICIO,
      SERVICIO_ID
    FROM
      DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
    WHERE
      ESTADO = 'Cancel';
  --
	Lr_GetServicios C_GetServicios%ROWTYPE;
  Lv_Motivo VARCHAR2(300);
  Lv_MotivoPadre VARCHAR2(300);
	--
BEGIN
  --
  IF C_GetServicios%ISOPEN THEN
    CLOSE C_GetServicios;
  END IF;
  --
  OPEN C_GetServicios;
  LOOP
    --
    FETCH C_GetServicios INTO Lr_GetServicios;
    --
    EXIT
    WHEN C_GetServicios%NOTFOUND;
		--
		--
		IF Lr_GetServicios.SERVICIO_ID > 0 THEN
      --
      Lv_Motivo      := DB_COMERCIAL.COMEK_CONSULTAS.F_GET_FECHA_CREACION_HISTORIAL( Lr_GetServicios.SERVICIO_ID,
                                                                                     'Cancel',
                                                                                     'Motivo', 
                                                                                     NULL );
      Lv_MotivoPadre := DB_COMERCIAL.COMEK_CONSULTAS.F_GET_FECHA_CREACION_HISTORIAL( Lr_GetServicios.SERVICIO_ID,
                                                                                     'Cancel',
                                                                                     'MotivoPadre',
                                                                                     NULL );
		  --
			UPDATE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO
			SET MOTIVO_PADRE_CANCELACION = Lv_MotivoPadre, MOTIVO_CANCELACION = Lv_Motivo
			WHERE ID_DASHBOARD_SERVICIO = Lr_GetServicios.ID_DASHBOARD_SERVICIO;
			--
		END IF;
		--
	END LOOP;
  --
	COMMIT;
	--
END;






