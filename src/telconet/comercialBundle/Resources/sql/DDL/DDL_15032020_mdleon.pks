/**
 * @author David Leon <mdleon@telconet.ec>
 * @version 1.0  15-03-2020
 * Trriger que llenara la tabla de Seguimiento de los servicios.
 *
 */
create or replace TRIGGER DB_COMERCIAL.T1_INFO_SERVICIO_SEGUIMIENTO
  AFTER INSERT OR UPDATE ON DB_COMERCIAL.INFO_SERVICIO_HISTORIAL REFERENCING OLD AS OLD NEW AS NEW FOR EACH ROW 
  
  DECLARE 
  
  --CONSULTAMOS EL PRODUCTO
  CURSOR C_ProductosPermitidos(Cv_ServicioId INFO_SERVICIO_HISTORIAL.SERVICIO_ID%type)IS
  SELECT APR.DESCRIPCION_PRODUCTO ,APR.ID_PRODUCTO
  FROM INFO_SERVICIO ISER,ADMI_PRODUCTO APR,ADMI_PARAMETRO_CAB APC, ADMI_PARAMETRO_DET APD
  WHERE 
    APC.NOMBRE_PARAMETRO='SEGUIMIENTO_PRODUCTOS' AND
    APC.ID_PARAMETRO=APD.PARAMETRO_ID AND
    ISER.ID_SERVICIO=Cv_ServicioId AND
    ISER.PRODUCTO_ID=APR.ID_PRODUCTO AND
    APR.ESTADO_INICIAL IS NOT NULL AND
    APR.EMPRESA_COD=10 AND
    APD.VALOR1=APR.DESCRIPCION_PRODUCTO ;
    
    
    --OBTENEMOS EL ULTIMO ESTADO DEL SEGUIMIENTO
	CURSOR C_DatoSeguimiento(Cv_servicioId INFO_SERVICIO_HISTORIAL.SERVICIO_ID%type) IS
    SELECT ESTADO,FE_CREACION
    FROM INFO_SEGUIMIENTO_SERVICIO
    WHERE SERVICIO_ID = Cv_servicioId
      AND ID_SEGUIMIENTO_SERVICIO = (SELECT MAX(ID_SEGUIMIENTO_SERVICIO) FROM INFO_SEGUIMIENTO_SERVICIO
    WHERE SERVICIO_ID = Cv_servicioId AND FE_MODIFICACION IS NULL);
	
  --CALCULAMOS LOS MINUTOS ENTRE ESTADOS
  CURSOR C_Fechas(cv_fechaIni INFO_SERVICIO_HISTORIAL.FE_CREACION%type, 
                  cv_fechaFin INFO_SERVICIO_HISTORIAL.FE_CREACION%type)IS
    SELECT 
     CAST((CAST( cv_fechaFin AS DATE) -
     CAST( cv_fechaIni       AS DATE))*24*60 AS INTEGER) AS TIEMPOMINUTOS
    FROM DUAL;          
  
  --CONSULTAMOS EL TIMPO ESTIMADO DE ATENCION POR ESTADO
  CURSOR C_Tiempo_Estados(Cv_estado INFO_SERVICIO_HISTORIAL.ESTADO%type,
                          Cv_productoId INFO_SERVICIO.PRODUCTO_ID%type)IS
  SELECT VALOR3 
  FROM ADMI_PARAMETRO_DET 
  WHERE PARAMETRO_ID=(SELECT ID_PARAMETRO 
                      FROM ADMI_PARAMETRO_CAB 
                      WHERE NOMBRE_PARAMETRO='TIEMPO_ATENCION_ESTADOS')
  AND  VALOR1 = (SELECT DESCRIPCION_PRODUCTO 
                 FROM ADMI_PRODUCTO 
                 WHERE ID_PRODUCTO=Cv_productoId)
  AND  VALOR2  = Cv_estado;  
  
  --OBTENEMOS EL DEPARTAMENTO QUE ATENDIO
  CURSOR C_Departamento(Cv_usuario INFO_SERVICIO.USR_CREACION%type)IS
  SELECT ad.NOMBRE_DEPARTAMENTO 
  FROM INFO_PERSONA ip, INFO_PERSONA_EMPRESA_ROL iper, INFO_EMPRESA_ROL ier, DB_GENERAL.ADMI_DEPARTAMENTO ad
  WHERE ip.login=Cv_usuario and ip.id_persona=iper.persona_id and iper.estado='Activo' and 
        iper.empresa_rol_id=ier.id_empresa_rol and ier.empresa_cod=10 and ad.id_departamento=iper.departamento_id;
  
  Lr_InfoSeguimiento    INFO_SEGUIMIENTO_SERVICIO%ROWTYPE:=NULL;
  Lv_fechaAnterior	    INFO_SERVICIO_HISTORIAL.FE_CREACION%type := NULL;
  Lv_productoDescrip       Varchar2(200)   := NULL;
  Lv_tiempoTarea 	  INT;
  lv_productoId     INT;
  Lv_tiempoEstimado VARCHAR2(4000)  := NULL;
  Lv_estadoAnterior Varchar2(200)   := NULL;
  Lv_Departamento   VARCHAR2(4000)  := NULL;
  Lv_CodigoError  	VARCHAR2(4000)  := NULL;
  Lv_MensajeError 	VARCHAR2(4000)  := NULL;
  e               	EXCEPTION;
  Lv_Mensaje      	VARCHAR2(500);  
  err_msg VARCHAR2(4000)  := NULL;
  BEGIN
    OPEN C_ProductosPermitidos(:NEW.SERVICIO_ID);
    FETCH C_ProductosPermitidos INTO Lv_productoDescrip,lv_productoId;
    CLOSE C_ProductosPermitidos;

    IF (Lv_productoDescrip IS NOT NULL) THEN 
        OPEN C_Tiempo_Estados(:NEW.ESTADO,lv_productoId);
        FETCH C_Tiempo_Estados INTO Lv_tiempoEstimado;
        CLOSE C_Tiempo_Estados;
        Lr_InfoSeguimiento.TIEMPO_ESTIMADO  :=  Lv_tiempoEstimado;
        Lr_InfoSeguimiento.SERVICIO_ID      := :NEW.SERVICIO_ID;
        Lr_InfoSeguimiento.ESTADO           := :NEW.ESTADO; 
        Lr_InfoSeguimiento.USR_CREACION     := :NEW.USR_CREACION;
        Lr_InfoSeguimiento.FE_CREACION      := :NEW.FE_CREACION;
        Lr_InfoSeguimiento.IP_CREACION      := :NEW.IP_CREACION;
        Lr_InfoSeguimiento.OBSERVACION      := :NEW.OBSERVACION;
--      
       
        OPEN C_Departamento(:NEW.USR_CREACION);
        FETCH C_Departamento INTO Lv_Departamento;
        CLOSE C_Departamento;
--       

        IF(Lv_Departamento IS NULL) THEN
          Lr_InfoSeguimiento.DEPARTAMENTO := 'root';
        ELSE
          Lr_InfoSeguimiento.DEPARTAMENTO := Lv_Departamento;
        END IF;

        OPEN C_DatoSeguimiento(:NEW.SERVICIO_ID);
        FETCH C_DatoSeguimiento INTO Lv_estadoAnterior,Lv_fechaAnterior;
        CLOSE C_DatoSeguimiento;

        --INSERTAMOS EL SEGUIMIENTO
        COMEK_MODELO.COMP_INSERTA_SEGUIMIENTO_SERV(Lr_InfoSeguimiento,Lv_estadoAnterior,Lv_fechaAnterior, Lv_CodigoError, Lv_MensajeError);
          IF Lv_CodigoError IS NOT NULL OR Lv_MensajeError IS NOT NULL THEN
             Lv_Mensaje      := Lv_CodigoError||' '||Lv_MensajeError;
             RAISE e;
          END IF;
	END IF;

  EXCEPTION
  WHEN e THEN
    UTL_MAIL.SEND (sender     => 'notificaciones@telconet.ec', 
                  recipients => 'telcos@telconet.ec;', 
                   subject    => 'Error generado en el trigger T1_INFO_SERVICIO_SEGUIMIENTO', 
                   MESSAGE    => '<p>Ocurrio el siguiente error: ' || SQLERRM || ' - ' || SQLCODE ||Lv_Mensaje||' </p>',
                   mime_type => 'text/html; charset=UTF-8' );
  WHEN OTHERS THEN
    UTL_MAIL.SEND (sender     => 'notificaciones@telconet.ec', 
                   recipients => 'telcos@telconet.ec;', 
                   subject    => 'Error generado en el trigger T1_INFO_SERVICIO_SEGUIMIENTO', 
                   MESSAGE    => '<p>Ocurrio el siguiente error: '  || SQLERRM || ' - ' || SQLCODE ||Lv_Mensaje||' </p>', 
                   mime_type  => 'text/html; charset=UTF-8' );
  END;