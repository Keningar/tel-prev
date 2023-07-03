
create or replace procedure DB_COMERCIAL.P_NOTIFICA_FIN_SEGURIDAD IS

  Le_Exception          EXCEPTION;
  Lv_Error              VARCHAR2(4000);
  Lv_MensajeError       VARCHAR2(4000);
  lv_correos            VARCHAR2(1000);
  Lb_TieneDatos         BOOLEAN;
  Lb_Correo             BOOLEAN;
  Lv_NumerosDias        VARCHAR2(5);
  Lv_Correogerente      VARCHAR2(50);
  Lcl_Plantilla         CLOB;
  Lv_Remitente          VARCHAR2(50)  := 'notificaciones_telcos@telconet.ec';
  Lv_SufijoCorreoVendedor VARCHAR2(20) := '@telconet.ec';
  Lv_AsuntoVendedor     VARCHAR2(300) := 'Notificación Automática por Fin de Licencia de Seguridad de usuario ';
  
CURSOR c_finalizacion_seguridad(Cv_NumeroDias VARCHAR2)IS
SELECT datos.* FROM (
  select ap.DESCRIPCION_PRODUCTO Producto,ip.login,ispc.valor Fecha,
  iser.usr_creacion, iser.usr_vendedor from 
  db_comercial.info_punto ip,
  db_comercial.admi_producto ap,
  db_comercial.info_servicio iser, 
  db_comercial.admi_caracteristica ac, 
  db_comercial.admi_producto_caracteristica apc,
  db_comercial.info_servicio_prod_caract ispc
  where ap.DESCRIPCION_PRODUCTO='SECURITY SECURE SDWAN' and ap.estado='Activo'
  and iser.PRODUCTO_ID=ap.ID_PRODUCTO and iser.estado='Activo'
  and ac.DESCRIPCION_CARACTERISTICA='FECHA_EXPIRACION_SEGURIDAD' and ac.estado='Activo'
  and apc.producto_id=ap.id_producto and apc.caracteristica_id=ac.id_caracteristica
  and ispc.servicio_id=iser.id_servicio and ispc.producto_caracterisitica_id=apc.id_producto_caracterisitica
  and iser.punto_id=ip.id_punto
  and ispc.fe_creacion+Cv_NumeroDias>sysdate)datos where datos.fecha  is not null;

Cursor c_dias_finalizacion IS
  Select apd.valor1 From Db_General.Admi_Parametro_Cab apc,
  Db_General.Admi_Parametro_Det apd
  where apc.nombre_parametro='DIAS_FIN_SEGURIDAD' and apc.estado='Activo' and
  apc.id_parametro=apd.parametro_id;

Cursor c_correo_finalizacion IS
  Select apd.valor1 From Db_General.Admi_Parametro_Cab apc,
  Db_General.Admi_Parametro_Det apd
  where apc.nombre_parametro='CORREO_GERENTE_SEGURIDAD' and apc.estado='Activo' and
  apc.id_parametro=apd.parametro_id;


CURSOR C_GetPlantilla(Cv_CodigoPlantilla DB_COMUNICACION.ADMI_PLANTILLA.CODIGO%TYPE)
    IS
      SELECT AP.PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA AP 
      WHERE AP.CODIGO = Cv_CodigoPlantilla
      AND AP.ESTADO <> 'Eliminado';

  Lc_ServiciosSeguridad  c_finalizacion_seguridad%ROWTYPE;
  BEGIN

  IF c_dias_finalizacion%ISOPEN THEN
      CLOSE c_dias_finalizacion;
  END IF;
  OPEN c_dias_finalizacion;
    FETCH c_dias_finalizacion INTO Lv_NumerosDias;
  CLOSE c_dias_finalizacion;

  IF c_correo_finalizacion%ISOPEN THEN
      CLOSE c_correo_finalizacion;
  END IF;

  OPEN c_correo_finalizacion;
    FETCH c_correo_finalizacion INTO Lv_Correogerente;
    Lb_Correo := c_correo_finalizacion%FOUND;
  CLOSE c_correo_finalizacion;

  IF c_finalizacion_seguridad%ISOPEN THEN
      CLOSE c_finalizacion_seguridad;
  END IF;

    FOR Lc_ServicioSeguridad IN c_finalizacion_seguridad(Lv_NumerosDias)
    LOOP
        OPEN C_GetPlantilla('SEGURIDAD_SDWAN');
        FETCH C_GetPlantilla INTO Lcl_Plantilla;
        CLOSE C_GetPlantilla;
         
        If (Lcl_Plantilla is not null) Then
        
        Lcl_Plantilla                   := REPLACE(Lcl_Plantilla,'{{producto}}', Lc_ServicioSeguridad.producto);
        Lcl_Plantilla                   := REPLACE(Lcl_Plantilla,'{{login}}', Lc_ServicioSeguridad.LOGIN);
        Lcl_Plantilla                   := REPLACE(Lcl_Plantilla,'{{fecha}}', 
                                                           Lc_ServicioSeguridad.Fecha);
        
        If Lb_Correo Then
          lv_correos:= Lv_Correogerente ||';'|| Lc_ServicioSeguridad.usr_vendedor || Lv_SufijoCorreoVendedor ||';'
          || Lc_ServicioSeguridad.usr_creacion|| Lv_SufijoCorreoVendedor;
        else
          lv_correos:= Lc_ServicioSeguridad.usr_vendedor || Lv_SufijoCorreoVendedor ||';'
          || Lc_ServicioSeguridad.usr_creacion|| Lv_SufijoCorreoVendedor;
        End If;
        --Envío de correo al vendedor
        DB_COMUNICACION.CUKG_TRANSACTIONS.P_SEND_MAIL(  Lv_Remitente, lv_correos, 
                                                        Lv_AsuntoVendedor ||' ' || Lc_ServicioSeguridad.LOGIN, 
                                                        SUBSTR(Lcl_Plantilla, 1, 32767), 'text/html; charset=UTF-8', 
                                                        Lv_MensajeError);
                                                
        IF Lv_MensajeError IS NOT NULL THEN 
          DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+', 'P_NOTIFICA_FIN_SEGURIDAD', 
                                                Lv_MensajeError, NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_COMERCIAL'), 
                                                SYSDATE, NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
          Lv_MensajeError := ''; 
        END IF;                                                   
        END IF;

      END LOOP;
  EXCEPTION

    WHEN Le_Exception THEN

      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('P_NOTIFICA_FIN_SEGURIDAD',
                                           'P_NOTIFICA_FIN_SEGURIDAD',
                                            Lv_Error,
                                           'telcos_sdwan',
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),'127.0.0.1'));

    WHEN OTHERS THEN

      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('P_NOTIFICA_FIN_SEGURIDAD',
                                           'P_NOTIFICA_FIN_SEGURIDAD',
                                            SQLCODE||' - ERROR_STACK:'||
                                              DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: '||
                                              DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                           'telcos_sdwan',
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),'127.0.0.1'));
END DB_COMERCIAL.P_NOTIFICA_FIN_SEGURIDAD;

/
