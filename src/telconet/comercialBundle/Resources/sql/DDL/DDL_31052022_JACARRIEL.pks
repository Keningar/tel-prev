CREATE OR REPLACE PACKAGE DB_COMERCIAL.SPKG_CALIDAD_INSTALACION
AS

/**
 * Documentación para TYPE 'SPKG_CALIDAD_INSTALACION'.
 *
 * GUARDAR_CALIDAD_INSTALACION
 * Procedimiento que sirve para leer el JSON devuelto por el ws de MD CalidadInstalacion.
 * @since 1.0
 * @author Jeampier Carriel <jacarriel@telconet.ec>
*/
PROCEDURE GUARDAR_CALIDAD_INSTALACION(Pcl_JsonRespuesta    IN CLOB, 
				                      Pv_idServicio      IN VARCHAR2, 
				                      Pv_usuarioCreacion IN VARCHAR2,
				                      Pv_Status         OUT VARCHAR2,
			                          Pv_Mensaje        OUT VARCHAR2);
/**
 * PUT_INSERT_CARACT
 * Procedimiento que sirve para registrar los datos obtenenidos del WS en la tabla INFO_SERVICIO_PROD_CARACT.
 * @since 1.0
 * @author Jeampier Carriel <jacarriel@telconet.ec>
*/		                         

PROCEDURE PUT_INSERT_CARACT (Pn_IdServicio IN VARCHAR2, 
							 Pn_NombreCaracteristica IN VARCHAR2,
							 Pn_ValorCaracteristica IN VARCHAR2, 
							 Pn_UsuarioCreacion IN VARCHAR2);  
/**
 * REPROCESO_CALIDAD
 * Procedimiento quese ejecuta desde un JOB y sirve para reprocesar los registros con ERROR de calidadInstalacion.
 * @since 1.0
 * @author Jeampier Carriel <jacarriel@telconet.ec>
*/	
PROCEDURE REPROCESO_CALIDAD;

END SPKG_CALIDAD_INSTALACION;

CREATE OR REPLACE PACKAGE BODY DB_COMERCIAL.SPKG_CALIDAD_INSTALACION AS

PROCEDURE GUARDAR_CALIDAD_INSTALACION(Pcl_JsonRespuesta    IN CLOB, 
				                      Pv_idServicio      IN VARCHAR2, 
				                      Pv_usuarioCreacion IN VARCHAR2,
				                      Pv_Status         OUT VARCHAR2,
			                          Pv_Mensaje        OUT VARCHAR2)
IS
    --VARIABLES

    Lv_status               VARCHAR2(60);
    Lcl_Respuesta           CLOB;
    Ln_CountDatos           NUMBER;
    Lv_NombreElemento       VARCHAR2(200);
    Ln_CountListaClientes   NUMBER;
    Lv_Login                VARCHAR2(200);
    Lv_SenalOptica          VARCHAR2(200);
    Lv_Hec 					VARCHAR2(200);
    Lv_Fec 		            VARCHAR2(200);
	Lv_Bip 					VARCHAR2(200);
    
    Ln_CountApOnt           NUMBER;
    Ln_CountNeighbors       NUMBER;
    Ln_CountHosts			NUMBER;
    Lv_NombreDipositivo     VARCHAR2(200);
    Lv_IPv4                 VARCHAR2(200);
    Lv_MacAddress           VARCHAR2(200);
    Lv_RSSI                 VARCHAR2(200);
    Lv_Banda                VARCHAR2(200);

    --neighbors
    Lv_neighbors_SSID       VARCHAR2(200);
    Lv_neighbors_canal      VARCHAR2(200);
    Lv_neighbors_RSSI       VARCHAR2(200);
    Lv_neighbors_banda      VARCHAR2(200);
    --hosts
    Lv_hosts_nombreDipositivo   VARCHAR2(200);
    Lv_hosts_IPv4               VARCHAR2(200);
    Lv_hosts_macAddress         VARCHAR2(200);
    Lv_hosts_RSSI               VARCHAR2(200);
    Lv_hosts_banda              VARCHAR2(200);
    --sppedTest
    Lv_speedTest_upload         VARCHAR2(200);
    Lv_speedTest_download       VARCHAR2(200);
    Lv_speedTest_ping           VARCHAR2(200);
	Lv_primerLlamado  			VARCHAR2(100);
        --
    Lv_MsjResultado   VARCHAR2(5000);
    Le_MyException    EXCEPTION;
    --
BEGIN
    --
    --
	IF INSTR(Pcl_JsonRespuesta, 'status') = 0 OR INSTR(Pcl_JsonRespuesta, 'respuesta') = 0 THEN
        Lv_MsjResultado := 'JSON no válido';
        RAISE Le_MyException;
    END IF;
    --
    APEX_JSON.PARSE(Pcl_JsonRespuesta);
    Lv_status      := APEX_JSON.GET_VARCHAR2(P_PATH => 'status');
    Ln_CountDatos  := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento');
    --
    IF Ln_CountDatos > 0 THEN
        FOR Ln_ContadorDatos IN 1 .. Ln_CountDatos LOOP
            Lv_NombreElemento := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].nombreElemento', p0 => Ln_ContadorDatos);
            DBMS_OUTPUT.PUT_LINE('NOMBRE ELEMENTO : '||Lv_NombreElemento);
            --
            Ln_CountListaClientes := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento[%d].listaClientes', p0 => Ln_ContadorDatos);
            IF Ln_CountListaClientes > 0 THEN
            FOR Ln_ContadorListaClientes IN 1 .. Ln_CountListaClientes LOOP
                    Lv_Login := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].login',
                                                        p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    DBMS_OUTPUT.PUT_LINE('LOGIN: '||Lv_Login);
                    Lv_SenalOptica := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.senalOptica',
                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    Lv_Hec := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.HEC',
                    							p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    Lv_Fec := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.FEC',
                    							p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    Lv_Bip := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.BIP',
                    							p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    						
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'senalOptica',Lv_SenalOptica, Pv_usuarioCreacion);
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'HEC',Lv_Hec, Pv_usuarioCreacion);
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'FEC',Lv_Fec, Pv_usuarioCreacion);
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'BIP',Lv_Bip, Pv_usuarioCreacion);
                   	
                   
                    Lv_speedTest_upload := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.speedTest.upload',
                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    Lv_speedTest_download := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.speedTest.download',
                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    Lv_speedTest_ping := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.speedTest.ping',
                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                                               
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'speedTest-upload',Lv_speedTest_upload, Pv_usuarioCreacion);
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'speedTest-download',Lv_speedTest_download, Pv_usuarioCreacion);
	                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'speedTest-ping',Lv_speedTest_ping, Pv_usuarioCreacion);

                    Ln_CountApOnt := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT',
                                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    IF Ln_CountApOnt > 0 THEN
                        FOR Ln_ContadorApOnt IN 1 .. Ln_CountApOnt LOOP
                            Lv_NombreDipositivo := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT[%d].nombreDipositivo',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorApOnt);
                                                       
                            Lv_IPv4 := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT[%d].IPv4',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorApOnt);
                                                       
                            Lv_MacAddress := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT[%d].macAddress',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorApOnt);
                                                       
                            Lv_RSSI := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT[%d].RSSI',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorApOnt);
                                                       
                            Lv_Banda := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.apONT[%d].banda',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorApOnt);
                            
                            DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'apONT-nombreDipositivo',Lv_NombreDipositivo, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'apONT-IPv4',Lv_IPv4, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'apONT-macAddress',Lv_MacAddress, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'apONT-RSSI',Lv_RSSI, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'apONT-banda',Lv_Banda, Pv_usuarioCreacion);
			               
                        END LOOP;
                    END IF;
                    --neighbors
                    Ln_CountNeighbors := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.neighbors',
                                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    IF Ln_CountNeighbors > 0 THEN
                        FOR Ln_ContadorNeighbors IN 1 .. Ln_CountNeighbors LOOP
                            Lv_neighbors_SSID := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.neighbors[%d].SSID',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorNeighbors);
                                                       
                            Lv_neighbors_canal := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.neighbors[%d].canal',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorNeighbors);
                                                       
                            Lv_neighbors_RSSI := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.neighbors[%d].RSSI',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorNeighbors);
                                                       
                            Lv_neighbors_banda := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.neighbors[%d].banda',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorNeighbors);
                            DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'neighbors-SSID',Lv_neighbors_SSID, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'neighbors-canal',Lv_neighbors_canal, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'neighbors-RSSI',Lv_neighbors_RSSI, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'neighbors-banda',Lv_neighbors_banda, Pv_usuarioCreacion);
                        END LOOP;
                    END IF;

                    --hosts
                    Ln_CountHosts := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts',
                                                                p0 => Ln_ContadorDatos, p1 => Ln_ContadorListaClientes);
                    IF Ln_CountHosts > 0 THEN
                        FOR Ln_ContadorHosts IN 1 .. Ln_CountHosts LOOP
                            Lv_hosts_nombreDipositivo := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts[%d].nombreDipositivo',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorHosts);
                                                       
                            Lv_hosts_IPv4 := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts[%d].IPv4',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorHosts);
                                                       
                            Lv_hosts_macAddress := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts[%d].macAddress',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorHosts);
                                                       
                            Lv_hosts_RSSI := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts[%d].RSSI',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorHosts);

                            Lv_hosts_banda := APEX_JSON.GET_VARCHAR2(P_PATH => 'respuesta.datosElemento[%d].listaClientes[%d].parametros.hosts[%d].banda',
                                                        p0 => Ln_ContadorDatos,
                                                        p1 => Ln_ContadorListaClientes,
                                                        p2 => Ln_ContadorHosts);    
                            DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'hosts-nombreDipositivo',Lv_hosts_nombreDipositivo, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'hosts-IPv4',Lv_hosts_IPv4, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'hosts-macAddress',Lv_hosts_macAddress, Pv_usuarioCreacion);
			                DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'hosts-RSSI',Lv_hosts_RSSI, Pv_usuarioCreacion);
							DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT(Pv_idServicio,'hosts-banda',Lv_hosts_banda, Pv_usuarioCreacion);

                        END LOOP;
                    END IF;
                END LOOP;
            END IF;
        END LOOP;
    END IF;
   
   Pv_Status         := 'OK';
   Pv_Mensaje        := 'Transación exitosa';
   
   EXCEPTION 
	   WHEN Le_MyException THEN
	    Pv_Status         := 'ERROR';
	   	Pv_Mensaje        :=  Lv_MsjResultado;  
	   WHEN OTHERS THEN
	   	Pv_Status   := 'ERROR';
	    Pv_Mensaje := SUBSTR('Ha ocurrido un error inesperado al intentar leer el JSON' || SQLERRM,0,3000);
     
END GUARDAR_CALIDAD_INSTALACION;

PROCEDURE PUT_INSERT_CARACT (Pn_IdServicio IN VARCHAR2, 
							 Pn_NombreCaracteristica IN VARCHAR2,
							 Pn_ValorCaracteristica IN VARCHAR2, 
							 Pn_UsuarioCreacion IN VARCHAR2)
IS
   limit_in                 PLS_INTEGER := 5000;
   Lv_output      			VARCHAR2(100)  := NULL;
   Lv_estado      			VARCHAR2(100)  := 'Finalizada';

BEGIN
	IF Pn_ValorCaracteristica IS NOT NULL THEN
		INSERT
		  INTO DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT VALUES
		    (
		      DB_COMERCIAL.SEQ_INFO_SERVICIO_PROD_CARACT.NEXTVAL,
		      Pn_IdServicio,
		      (SELECT ID_PRODUCTO_CARACTERISITICA
		          FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
		          WHERE PRODUCTO_ID     = 63
		          AND CARACTERISTICA_ID =
		            (SELECT ID_CARACTERISTICA
		            FROM DB_COMERCIAL.ADMI_CARACTERISTICA
		            WHERE DESCRIPCION_CARACTERISTICA = Pn_NombreCaracteristica
		            AND ESTADO                       = 'Activo'
		            )
		      ),
		      Pn_ValorCaracteristica,
		      sysdate,
		      sysdate,
		      Pn_UsuarioCreacion,
		      Pn_UsuarioCreacion,
		      'Activo',
		      NULL
		    );
		   
		INSERT INTO DB_SOPORTE.INFO_TAREA_SEGUIMIENTO(ID_SEGUIMIENTO, DETALLE_ID, OBSERVACION, USR_CREACION, FE_CREACION) 
            VALUES (DB_SOPORTE.SEQ_INFO_TAREA_SEGUIMIENTO.NEXTVAL, 
            (SELECT id.ID_DETALLE FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD ids, 
            	DB_SOPORTE.INFO_DETALLE id
				WHERE ids.SERVICIO_ID = Pn_IdServicio AND 
				      ids.ID_DETALLE_SOLICITUD = id.DETALLE_SOLICITUD_ID AND 
				      ids.ESTADO = Lv_estado),
           			  'Se ingresó la caracteristica ' || Pn_NombreCaracteristica || ': '||Pn_ValorCaracteristica,Pn_UsuarioCreacion,SYSDATE);
		   
		   COMMIT; 
	END IF;

EXCEPTION 
	   WHEN OTHERS THEN
	   DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
	                                        'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.PUT_INSERT_CARACT',
	                                         SUBSTR(' -ERROR- : ' || SQLERRM,0,4000),
	                                         Pn_UsuarioCreacion,
	                                         SYSDATE,
	                                         '127.0.0.1');
     
		
END PUT_INSERT_CARACT;

PROCEDURE REPROCESO_CALIDAD
IS
    Lv_Url             		 VARCHAR2(150) := '';
    Lcl_Headers        		 CLOB;
    Lcl_Request        		 CLOB;
   	Lcl_Cabecera       		 CLOB;
    Lcl_DatosElemento 		 CLOB;
	Lcl_DatosCliente 		 CLOB;
    Lcl_Response       		 CLOB;
    Ln_CodeRequest     	 	 NUMBER;
   	Lv_CalidadStatus         VARCHAR2(50);
   	Lv_CalidadMensaje        VARCHAR2(50);
    Lv_StatusResult    		 VARCHAR2(10);
    Lv_MsgResult       		 VARCHAR2(4000);
    Lv_Aplicacion     		 VARCHAR2(50) := 'application/json';
    Lv_CodEmpresa      		 VARCHAR2(5)  := '18';
    Lv_PrefijoEmpresa  		 VARCHAR2(5)  := 'MD';
   	Lv_Ip              		 VARCHAR2(20) := '127.0.0.1';
    Lv_Estado          		 VARCHAR2(20) := 'Activo';
	Lv_Producto        		 VARCHAR2(20) := 'INTERNET';
	Lv_TipoProceso     		 VARCHAR2(20) := 'reproceso';
	Lv_UsrCreacion     		 VARCHAR2(20) := 'TELCOS';
	Lv_DescripCaract   		 VARCHAR2(20) := 'CalidadInstalacion';
	Lv_EjecutaComando        VARCHAR2(5)  := 'SI';
    Lv_EjecutaConfiguracion  VARCHAR2(5)  := 'NO';
    Lv_RequiereAp	         VARCHAR2(5)  := 'true';
    Lv_Opcion                VARCHAR2(50) := 'obtenerParametrosCalidadInstalacion';
    Lv_NombreParametro		 VARCHAR2(50) := 'RANGOS CALIDAD DE INSTALACION';
    Lv_ParametrosWs			 VARCHAR2(50) := 'PARAMETROS_DE_CALIDAD_INSTALACION_WS';
    Lv_EstadoPendiente		 VARCHAR2(50) := 'Pendiente';
    Lv_nombreTecnico		 VARCHAR2(100);
    Lv_apellidoTecnico		 VARCHAR2(100);
    Lv_cedulaTecnico		 VARCHAR2(50);
    TYPE Lv_IdServicio       IS TABLE OF VARCHAR2(50); 
    V_Lv_IdServicio 		 Lv_IdServicio;
    TYPE Lv_Tecnico          IS TABLE OF VARCHAR2(50); 
    V_Lv_Tecnico			 Lv_Tecnico;
    Le_MyException           EXCEPTION;
    Ln_CountElementos	     NUMBER;
    i 					     PLS_INTEGER  := 0;
    x 					     PLS_INTEGER  := 0;
    y 					     PLS_INTEGER  := 0;
   
   CURSOR C_ObtenerParametrosWs IS
      SELECT VALOR1 FROM DB_GENERAL.ADMI_PARAMETRO_DET
            WHERE ESTADO = Lv_Estado AND PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                WHERE NOMBRE_PARAMETRO = Lv_NombreParametro AND ESTADO = Lv_Estado AND ROWNUM = 1)
            			AND DESCRIPCION = Lv_ParametrosWs AND ROWNUM = 1;
	
   CURSOR C_ServProdError IS  
      SELECT SERVICIO_ID,USR_CREACION FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT 
      WHERE PRODUCTO_CARACTERISITICA_ID =
      	(SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
      		WHERE PRODUCTO_ID = 
      			(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = Lv_Producto AND EMPRESA_COD = Lv_CodEmpresa) 
      				AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
						WHERE DESCRIPCION_CARACTERISTICA = Lv_DescripCaract AND ESTADO = Lv_Estado)) AND ESTADO = Lv_EstadoPendiente;
									
	CURSOR C_DatosElemento(Lv_IdServicio VARCHAR2)
	IS
	  SELECT Lv_IdServicio idServicio,ie.NOMBRE_ELEMENTO nombreElemento,ame.NOMBRE_MODELO_ELEMENTO modeloOlt,ii.IP ipElemento
		FROM 
			 DB_COMERCIAL.INFO_SERVICIO tis,  DB_COMERCIAL.INFO_SERVICIO_TECNICO tst,
		  	 DB_INFRAESTRUCTURA.INFO_ELEMENTO ie, 	 DB_INFRAESTRUCTURA.INFO_IP ii,
		  	 DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO ame
			  	 WHERE 
				  tis.ID_SERVICIO = Lv_IdServicio AND tst.SERVICIO_ID = tis.ID_SERVICIO AND
				  tst.ELEMENTO_ID = ie.ID_ELEMENTO AND ie.MODELO_ELEMENTO_ID = ame.ID_MODELO_ELEMENTO AND
				  ii.ELEMENTO_ID = ie.ID_ELEMENTO AND tis.ESTADO = Lv_Estado;
			 
	CURSOR C_DatosCliente(Lv_IdServicio VARCHAR2)
	IS
	  SELECT tip.login,tie.NOMBRE_INTERFACE_ELEMENTO puerto, ie.SERIE_FISICA serialOnt,per.IDENTIFICACION_CLIENTE identificacion,
	   DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(tis.ID_SERVICIO, 'INDICE CLIENTE') ont_id,
	   DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(tis.ID_SERVICIO, 'SERVICE-PROFILE') service_profile
		FROM 
			 DB_COMERCIAL.INFO_SERVICIO tis, DB_COMERCIAL.INFO_PUNTO tip, 
		  	 DB_COMERCIAL.INFO_SERVICIO_TECNICO tst,DB_INFRAESTRUCTURA.INFO_ELEMENTO ie,
		  	 DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO tie,DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL rol,DB_COMERCIAL.INFO_PERSONA per
			     WHERE 
				  tis.ID_SERVICIO = Lv_IdServicio AND tis.PUNTO_ID = tip.ID_PUNTO AND
			      tst.SERVICIO_ID = tis.ID_SERVICIO AND tst.ELEMENTO_CLIENTE_ID = ie.ID_ELEMENTO AND
				  tst.INTERFACE_ELEMENTO_ID = tie.ID_INTERFACE_ELEMENTO AND tip.PERSONA_EMPRESA_ROL_ID = rol.ID_PERSONA_ROL AND
				  rol.PERSONA_ID = per.ID_PERSONA AND tis.ESTADO = Lv_Estado AND tip.ESTADO = Lv_Estado;
				 
	CURSOR C_DatosTecnico(LoginEntrante VARCHAR2)
	IS
	  Select persona.NOMBRES NOMBRES,persona.APELLIDOS APELLIDOS,persona.IDENTIFICACION_CLIENTE IDENTIFICACION_CLIENTE
           from DB_COMERCIAL.info_persona persona,DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,
                DB_GENERAL.ADMI_DEPARTAMENTO dep,DB_COMERCIAL.INFO_EMPRESA_ROL ier,
                DB_COMERCIAL.INFO_OFICINA_GRUPO oficina,DB_GENERAL.ADMI_CANTON canton
                where persona.ID_PERSONA = iper.PERSONA_ID and iper.EMPRESA_ROL_ID = ier.ID_EMPRESA_ROL
                and iper.DEPARTAMENTO_ID = dep.ID_DEPARTAMENTO and iper.OFICINA_ID = oficina.ID_OFICINA
                and oficina.CANTON_ID = canton.ID_CANTON and ier.EMPRESA_COD = Lv_CodEmpresa
                and persona.login = LoginEntrante and iper.estado = Lv_estado; 
		
			 
	datosElemento_rec 			     C_DatosElemento%ROWTYPE;
	TYPE array_Elemento			     IS VARRAY(4) OF VARCHAR2(50); 
	elementos 						 array_Elemento; 
	TYPE R_DatosElemento IS RECORD (
	            idServicio     	   VARCHAR2(50),
	            nombreElemento     VARCHAR2(100),
	            modeloOlt          VARCHAR2(50),
	            ipElemento VARCHAR2(50)
	        );
	TYPE Ltl_DatosElemento IS TABLE OF R_DatosElemento INDEX BY PLS_INTEGER;
	Lr_DatosElemento  Ltl_DatosElemento;

	TYPE R_DatosCliente IS RECORD (
				login 			VARCHAR2(50),            
				puerto     	    VARCHAR2(50),
				serialOnt 		VARCHAR2(50),
				identificacion  VARCHAR2(50),
	            ont_id          VARCHAR2(50),
	            service_profile VARCHAR2(50));
	           
	TYPE Ltl_DatosCliente IS TABLE OF R_DatosCliente INDEX BY PLS_INTEGER;
	Lr_DatosCliente  Ltl_DatosCliente;
   
BEGIN
	
	IF C_ObtenerParametrosWs%ISOPEN THEN
          CLOSE C_ObtenerParametrosWs;
    END IF;
   
    OPEN C_ObtenerParametrosWs;
    FETCH C_ObtenerParametrosWs INTO Lv_Url;
    CLOSE C_ObtenerParametrosWs;
    --
    IF Lv_Url IS NULL THEN
        Lv_MsgResult := 'No se encontrarón los datos del ws en los parámetros para las promociones de ancho de banda.';
        RAISE Le_MyException;
    END IF;
	
	Lcl_Cabecera   := '{
					    "opcion": "' || Lv_Opcion || '",
					    "empresa": "' || Lv_PrefijoEmpresa || '",
					    "tipoProceso": "' || Lv_TipoProceso || '",
					    "usrCreacion": "' || Lv_UsrCreacion || '",
					    "ipCreacion": "' || Lv_Ip || '",
					    "comandoConfiguracion": "' || Lv_EjecutaConfiguracion || '",
					    "ejecutaComando": "' || Lv_EjecutaComando || '",
					    "datosElemento": [';

	OPEN C_ServProdError;
		LOOP
			FETCH C_ServProdError BULK COLLECT INTO V_Lv_IdServicio,V_Lv_Tecnico LIMIT 10;
			EXIT WHEN V_Lv_IdServicio.count=0;
			i := V_Lv_IdServicio.FIRST;
		    WHILE (i IS NOT NULL) 
	        LOOP
				OPEN C_DatosElemento(V_Lv_IdServicio(i));
					LOOP 
				        FETCH C_DatosElemento BULK COLLECT INTO Lr_DatosElemento LIMIT 10;
					    EXIT WHEN Lr_DatosElemento.count=0;
						x := Lr_DatosElemento.FIRST;
						WHILE (x IS NOT NULL) 
					    LOOP
						    Lcl_DatosElemento:='{
									            "nombreElemento": "' || Lr_DatosElemento(x).nombreElemento || '",
									            "ipElemento": "' || Lr_DatosElemento(x).ipElemento || '",
									            "modeloOlt": "' || Lr_DatosElemento(x).modeloOlt || '",
									            "datosCliente": [';
 
								OPEN C_DatosCliente(V_Lv_IdServicio(i));
								LOOP 
							        FETCH C_DatosCliente BULK COLLECT INTO Lr_DatosCliente LIMIT 10;
								    EXIT WHEN Lr_DatosCliente.count=0;
									y := Lr_DatosCliente.FIRST;
									WHILE (y IS NOT NULL) 
								    LOOP
									    OPEN C_DatosTecnico(V_Lv_Tecnico(i));
								        FETCH C_DatosTecnico INTO Lv_nombreTecnico,Lv_apellidoTecnico,Lv_cedulaTecnico;
								    	CLOSE C_DatosTecnico; 
								    
									    Lcl_DatosCliente:='{
												            "puerto": "' || Lr_DatosCliente(y).puerto || '",
												            "ont_id": "' || Lr_DatosCliente(y).ont_id || '",
												            "empresaCod": "' || Lv_CodEmpresa || '",
												            "login": "' || Lr_DatosCliente(y).login || '",
												            "identificacion": "' || Lr_DatosCliente(y).identificacion || '",
												            "serialOnt": "' || Lr_DatosCliente(y).serialOnt || '",
												            "service_profile": "' || Lr_DatosCliente(y).service_profile || '",
												            "requiereAp": "' || Lv_RequiereAp || '",
												            "cedulaTecnico": "' || Lv_cedulaTecnico  || '",
															"nombreTecnico": "' || Lv_nombreTecnico  || '"
												            }
												          ]
												        }
												      ]
												    }';
									   
									    Lcl_Request:=Lcl_Cabecera || Lcl_DatosElemento || Lcl_DatosCliente;
										
									   	Lcl_Request := REPLACE(Lcl_Request, chr(13)||chr(10), '');
									    Lcl_Request := REPLACE(Lcl_Request, chr(9), '');
									   
									   	Lv_StatusResult := 'ERROR';
									    Lv_MsgResult    := 'Mensaje error';
									    --
									    DB_GENERAL.GNKG_WEB_SERVICE.P_POST(Lv_Url,Lcl_Headers,Lcl_Request,Ln_CodeRequest,Lv_MsgResult,Lcl_Response);
									    
									   IF Ln_CodeRequest = 0 AND INSTR(Lcl_Response, 'status') != 0 AND INSTR(Lcl_Response, 'mensaje') != 0 THEN
									        APEX_JSON.PARSE(Lcl_Response);
									        Lv_StatusResult := APEX_JSON.GET_VARCHAR2(p_path => 'status');
									        Lv_MsgResult    := APEX_JSON.GET_VARCHAR2(p_path => 'mensaje');
									        IF Lv_StatusResult = 'OK' THEN
									            Ln_CountElementos := APEX_JSON.GET_COUNT(P_PATH => 'respuesta.datosElemento');
									            IF Ln_CountElementos > 0 THEN
													--ENVIAR DATOS A PAQUETE PARA PROCESAR EL JSON
									            	DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.GUARDAR_CALIDAD_INSTALACION(Lcl_Response,to_number(V_Lv_IdServicio(i)),Lv_UsrCreacion,Lv_CalidadStatus,Lv_CalidadMensaje);
									            	IF Lv_CalidadStatus != 'OK' THEN
									            		DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                          'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.REPROCESO_CALIDAD',
                                                          SUBSTR(' -ERROR- ' || Lv_StatusResult || ' -Respuesta- ' ||Lv_MsgResult || 'Servicio:' || to_number(V_Lv_IdServicio(i)),0,4000),
                                                          Lv_UsrCreacion,
                                                          SYSDATE,
                                                          Lv_Ip);
									            	ELSE
									            		DELETE FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT WHERE SERVICIO_ID = V_Lv_IdServicio(i) AND ESTADO = Lv_EstadoPendiente 
									            			AND PRODUCTO_CARACTERISITICA_ID = (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
												      			WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = Lv_Producto AND EMPRESA_COD = Lv_CodEmpresa) 
													      				AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
																			WHERE DESCRIPCION_CARACTERISTICA = Lv_DescripCaract AND ESTADO = Lv_estado));
									            	END IF;
									            END IF;
									        ELSE
									            DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                          'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.REPROCESO_CALIDAD',
                                                          SUBSTR(' -ERROR- ' || Lv_CalidadMensaje || 'Servicio:' || to_number(V_Lv_IdServicio(i)),0,4000),
                                                          Lv_UsrCreacion,
                                                          SYSDATE,
                                                          Lv_Ip);
									        END IF;
									    ELSE
									        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                          'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.REPROCESO_CALIDAD',
                                                          SUBSTR(' -ERROR-  Falla en la comunicación con el WS' || Lv_Url,0,4000),
                                                          Lv_UsrCreacion,
                                                          SYSDATE,
                                                          Lv_Ip);
									    END IF; 
								    	y := Lr_DatosCliente.NEXT(y);
									END LOOP;
							    END LOOP; 
							    CLOSE C_DatosCliente;	           
					    	x := Lr_DatosElemento.NEXT(x);
						END LOOP;
				    END LOOP; 
				    CLOSE C_DatosElemento;
	        	i := V_Lv_IdServicio.NEXT(i);
	        END LOOP;
		END LOOP;
	CLOSE C_ServProdError;

	EXCEPTION
        WHEN Le_MyException THEN
            --
            --se reservan los cambios
            ROLLBACK;
           DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                          'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.REPROCESO_CALIDAD',
                                                          SUBSTR(Lv_MsgResult,0,4000),
                                                          Lv_UsrCreacion,
                                                          SYSDATE,
                                                          Lv_Ip);
           
        WHEN OTHERS THEN
            --
            --se reservan los cambios
            ROLLBACK;
           Lv_MsgResult := 'Ocurrió un error al ejecutar el procedimiento de reproceso CalidadInstalacion: ';
           DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('Telcos+',
                                                          'DB_COMERCIAL.SPKG_CALIDAD_INSTALACION.REPROCESO_CALIDAD',
                                                          SUBSTR(Lv_MsgResult || SQLCODE || ' -ERROR- ' || SQLERRM,0,4000),
                                                          Lv_UsrCreacion,
                                                          SYSDATE,
                                                          Lv_Ip);

END REPROCESO_CALIDAD;


END SPKG_CALIDAD_INSTALACION;