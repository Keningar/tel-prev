--------------------------------------------------------
--  DDL for Package DB_INFRAESTRUCTURA.INKG_SINC_ARCGIS
--------------------------------------------------------
  CREATE OR REPLACE PACKAGE DB_INFRAESTRUCTURA.INKG_SINC_ARCGIS AS 

  /**
   * Documentación para P_MIGRA_DESDE_GIS
   * Procedimiento que realiza la extracción de datos de cajas creadas, modificadas y eliminadas en ArcGis
   * y las sincroniza con Telcos. Se ejecuta mediante JOB.
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
  */ 
  PROCEDURE P_MIGRA_DESDE_GIS;


  /**
   * Documentación para P_REACTIVA_ELEMENTO
   * Procedimiento que reactiva elementos eliminados por error
   * 
   * @param Pn_Tipo_Elemento            IN NUMBER     Tipo de elemento a reactivar
   * @param Pv_Nombre_Elemento          IN VARCHAR2   Nombre del elemento a buscar como eliminado
   * @param Pn_IDTELCOS                 IN OUT NUMBER Id de Telcos para el elemento
   * @param Pn_EXISTE                   IN OUT NUMBER Trae cantidad de elementos existentes con el mismo nombre
   * @param Pv_Mensaje                  OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */
  PROCEDURE P_REACTIVA_ELEMENTO(
  Pn_Tipo_Elemento    IN NUMBER,
  Pv_Nombre_Elemento  IN VARCHAR2,
  Pn_IDTELCOS         IN OUT NUMBER,
  Pn_EXISTE           IN OUT NUMBER,
  Pv_Mensaje          IN OUT VARCHAR2);


  /**
   * Documentación para P_VALIDAR_DETALLE_CAJA
   * Procedimiento que valida los detalles de cajas para INFO_DETALLE_ELEMENTO
   * 
   * @param Pn_Nivel_Splitter           IN NUMBER     Nivel de splitter en la caja
   * @param Pv_Ubicado_en               IN VARCHAR2   Lugar físico donde se encuentra la caja poste, etc
   * @param Pv_Tipo_Lugar               IN VARCHAR2   Caracteristicas para indicar el lugar del Elemento, aereo, soterrado
   * @param Pv_Mensaje                  OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */
  PROCEDURE P_VALIDAR_DETALLE_CAJA(
  Pn_Nivel_Splitter IN NUMBER,
  Pv_Ubicado_en     IN VARCHAR2,
  Pv_Tipo_Lugar     IN VARCHAR2,
  Pv_Mensaje        IN OUT VARCHAR2);


  /**
   * Documentación para P_VALIDAR_MODELO_ELEMENTO
   * Procedimiento que valida que el modelo de elemento se encuentre registrado en telcos.
   * 
   * @param Pv_Modelo_Elemento          IN VARCHAR2   Modelo de elemento a migrar.
   * @param Pn_Id_Modelo_Elemento       IN NUMBER     ID del modelo de elemento para registro.
   * @param Pv_Mensaje                  OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */
  PROCEDURE P_VALIDAR_MODELO_ELEMENTO(
  Pv_Modelo_Elemento    IN VARCHAR2,
  Pn_Id_Modelo_Elemento OUT NUMBER,
  Pv_MENSAJE            IN OUT VARCHAR2);


  /**
   * Documentación para P_VALIDA_UBICACION
   * Procedimiento que valida que la ubicación geográfica política sea correcta.
   * 
   * @param Pv_Provincia          IN VARCHAR2   Provincia donde se ubica el elemento.
   * @param Pv_Canton             IN VARCHAR2   Cantón donde se ubica el elemento.
   * @param Pv_Parroquia          IN VARCHAR2   Parroquia donde se ubica el elemento.
   * @param Pn_Id_Parroquia       IN NUMBER     ID de la parroquia donde se ubica el elemento para registro.
   * @param Pv_Mensaje            OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */
  PROCEDURE P_VALIDAR_UBICACION(
  Pv_Provincia    IN VARCHAR2,
  Pv_Canton       IN VARCHAR2,
  Pv_Parroquia    IN VARCHAR2, 
  Pn_Id_Parroquia OUT NUMBER,
  Pv_MENSAJE      IN OUT VARCHAR2);


  /**
   * Documentación para P_VALIDAR_EDIFICIO
   * Procedimiento que valida que el edificio o ciudadela ingresada sea valida.
   * 
   * @param Pv_Nombre_Edificacion IN VARCHAR2   Nombre del edificio o ciudadela.
   * @param Pn_Edificacion        IN NUMBER     Id del elemento edificio
   * @param Pv_Mensaje            OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */
  PROCEDURE P_VALIDAR_EDIFICIO(
  Pv_Nombre_Edificacion IN VARCHAR2,
  Pn_Edificacion        OUT NUMBER,
  Pv_Mensaje            IN OUT VARCHAR2);


  /**
   * Documentación para GET_CODIGO_TIPO_ELEMENTO
   * Función que obtiene el ID del tipo de elemento para registro
   * 
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   * 
   * @param Pv_Tipo_Elemento IN VARCHAR Recibe nombre del modelo
   * @return NUMBER retorna ID de tipo de elemento
  */
  FUNCTION GET_CODIGO_TIPO_ELEMENTO(
    Pv_Tipo_Elemento  IN VARCHAR2)
  RETURN NUMBER;


  /**
   * Documentación para P_INSERTAR_UBICACION_CAJA
   * Procedimiento que inserta la ubicación geográfica del elemento.
   * 
   * @param Pn_Id_Elemento              IN NUMBER       Id del elemento
   * @param Pv_Provincia                IN VARCHAR2     Provincia donde se ubica el elemento
   * @param Pv_Canton                   IN VARCHAR2     Cantón donde se ubica el elemento
   * @param Pv_Parroquia                IN VARCHAR2     Parroquia donde se ubica el elemento
   * @param Pn_Latitud                  IN NUMBER       Latitud geográfica del elemento
   * @param Pn_Longitud                 IN NUMBER       Longitud geográfica del elemento
   * @param Pn_Altura                   IN NUMBER       Altura del elemento sobre el nivel del mar
   * @param Pv_Direccion                IN VARCHAR2     Caracteristicas para indicar el lugar del Elemento, aereo, soterrado
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */  
  PROCEDURE P_INSERTAR_UBICACION(
  Pn_ID_ELEMENTO  IN NUMBER,
  Pn_PARROQUIA    IN NUMBER, 
  Pn_LATITUD      IN NUMBER,
  Pn_LONGITUD     IN NUMBER,
  Pn_ALTURA       IN NUMBER,
  Pv_DIRECCION    IN VARCHAR2,
  Pv_USR_ARCGIS   IN VARCHAR2,
  Pv_TELCONET     IN VARCHAR2,
  Pv_MEGADATOS    IN VARCHAR2,  
  Pv_MENSAJE      IN OUT VARCHAR2);


  /**
   * Documentación para P_INSERTAR_DETALLE_ELEMENTO
   * Procedimiento que inserta las características específicas del objeto caja, sea nueva o actualización.
   * 
   * @param Pn_Id_Elemento              IN NUMBER Id del elemento 
   * @param Pn_DETALLE_ELEMENTO_ID      IN NUMBER Id del detalle_elemento desde ArcGis
   * @param Pn_TIPO_ELEMENTO            IN NUMBER Código del tipo elemento según Telcos,
   * @param Pv_USR_ARCGIS               IN VARCHAR2 Usuario que realizó los cambios en ArcGis
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */ 
  PROCEDURE P_INSERTAR_DETALLE_ELEMENTO(  
  Pn_ID_ELEMENTO         IN NUMBER,
  Pn_DETALLE_ELEMENTO_ID IN NUMBER,
  Pn_TIPO_ELEMENTO       IN NUMBER,
  Pv_USR_ARCGIS          IN VARCHAR2,
  Pv_MENSAJE             IN OUT VARCHAR2);

  /**
   * Documentación para P_INSERT_ELEMENTO
   * Procedimiento que inserta un elemento traído desde ArcGis.
   * 
   * @param Pr_InfoElemento             IN DB_INFRAESTRUCTURA.INFO_ELEMENTO%ROWTYPE Recibe un registro para P_INSERT_ELEMENTO
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */ 
  PROCEDURE P_INSERT_ELEMENTO(
  Pr_InfoElemento IN DB_INFRAESTRUCTURA.INFO_ELEMENTO%ROWTYPE,
  Pv_Mensaje IN OUT VARCHAR2);


  /**
   * Documentación para P_INSERT_HISTORIAL
   * Procedimiento que inserta el historial de un elemento traído desde ArcGis.
   * 
   * @param Pr_InfoHistorialElemento    IN DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO%ROWTYPE Recibe un registro para P_INSERT_HISTORIAL
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */ 
  PROCEDURE P_INSERT_HISTORIAL(
  Pr_InfoHistorialElemento IN DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO%ROWTYPE,
  Pv_Mensaje IN OUT VARCHAR2);


  /**
   * Documentación para P_INSERT_INFO_EMPRESA_ELEMENTO
   * Procedimiento que inserta el punto de referencia de un elemento traído desde ArcGis, se realizan validaciones por tipo 
   * de elemento.
   * 
   * @param Pr_InfoEmpresaElemento      IN DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO%ROWTYPE Recibe un registro para P_INSERT_INFO_EMPRESA_ELEMENTO
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 18/03/2020
   */ 
  PROCEDURE P_INSERT_INFO_EMPRESA_ELEMENTO(
  Pr_InfoEmpresaElemento  IN DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO%ROWTYPE,
  Pv_Telconet             IN  VARCHAR2,
  Pv_Megadatos            IN  VARCHAR2,
  Pv_Mensaje              IN OUT VARCHAR2); 


  /**
   * Documentación para P_INSERTAR_EDIFICIO
   * Procedimiento que inserta un elemento traído de ArcGis como contenido de un edificio.
   * 
   * @param Pn_Edificacion              IN NUMBER       Id del elemento edificio  ID_A
   * @param Pn_IDTELCOS                 IN NUMBER       Id del elemento en Telcos ID_B
   * @param Pv_Usuario                  IN VARCHAR2     Usuario que realiza la acción
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 11/12/2019
   */ 
  PROCEDURE P_INSERTAR_EDIFICIO(
  Pn_Edificacion  IN NUMBER,
  Pn_IDTELCOS     IN NUMBER,
  Pv_Usuario      IN VARCHAR2,
  Pv_Mensaje      IN OUT VARCHAR2);


    /**
   * Documentación para P_SET_IDTELCOS_EN_BDARCGIS
   * Procedimiento que setea el ID ArcGis en MIGRATELCOS_ELEMENTO para futuros reportes.
   * 
   * @param Pn_IDTELCOS                 IN NUMBER     Id que Telcos será seteado
   * @param Pn_ID_MIGRATELCOS_ELEMENTO             IN NUMBER     Id del registro que se actualizará
   * @param Pv_Mensaje                  IN OUT VARCHAR2  Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 19/12/2019
   */
  PROCEDURE P_SET_IDTELCOS_EN_BDARCGIS(
  Pn_ID_Telcos    IN NUMBER,
  Pn_ID_MIGRATELCOS_ELEMENTO IN NUMBER,
  Pv_Mensaje      IN OUT VARCHAR2); 

    /**
   * Documentación para P_SET_ESTADO_EN_BDARCGIS
   * Procedimiento que setea-actualiza el estado en MIGRATELCOS_ELEMENTO para futuros reportes.
   * 
   * @param Pn_ID_MIGRATELCOS_ELEMENTO             IN NUMBER       Id del registro que se actualizará
   * @param Pn_IDTELCOS                 IN NUMBER       Id que Telcos será seteado
   * @param Pv_Estado                   IN VARCHAR2     Estado que será seteado
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 19/12/2019
   */  
  PROCEDURE P_SET_ESTADO_EN_BDARCGIS(
  Pn_ID_MIGRATELCOS_ELEMENTO IN NUMBER,
  Pn_ID_Telcos    IN NUMBER,
  Pv_Estado       IN VARCHAR2,
  Pv_Mensaje      IN OUT VARCHAR2);


    /**
   * Documentación para P_ACTUALIZA_ESTADO_ELEMENTO
   * Procedimiento que actualiza el estado del elemento en INFO_ELEMENTO.
   * 
   * @param Pn_ID_MIGRATELCOS_ELEMENTO             IN NUMBER       Id del registro que se actualizará
   * @param Pv_Estado                   IN VARCHAR2     Estado que será seteado
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 19/12/2019
   */
  PROCEDURE P_ACTUALIZA_ESTADO_ELEMENTO(
  Pn_ID_Telcos    IN NUMBER,
  Pv_Estado       IN VARCHAR2,
  Pv_Mensaje      IN OUT VARCHAR2);


    /**
   * Documentación para P_ACTUALIZA_ELEMENTO_CAJA
   * Procedimiento que actualiza el elemento en INFO_ELEMENTO, solo se envían datos necesarios.
   *
   * @param Pn_IDTELCOS                 IN NUMBER       Id que Telcos será seteado
   * @param Pv_Descripcion              IN VARCHAR2     Descripción de elemento a insertar
   * @param Pn_Modelo                   IN NUMBER       Id del modelo a actualizar
   * @param Pv_Comentarios              IN VARCHAR2     Comentarios a actualizar
   * @param Pv_Mensaje                  IN OUT VARCHAR2 Mensaje de la ejecución del procedimiento
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 19/12/2019
   */
  PROCEDURE P_ACTUALIZA_ELEMENTO_CAJA(
  Pn_ID_Telcos    IN NUMBER,
  Lv_Descripcion  IN VARCHAR2,
  Ln_Modelo       IN NUMBER,
  Pv_Comentarios  IN VARCHAR2,
  Pv_Mensaje      IN OUT VARCHAR2);

END INKG_SINC_ARCGIS;

/

--------------------------------------------------------
--  DDL for Package Body DB_INFRAESTRUCTURA.INKG_SINC_ARCGIS
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY DB_INFRAESTRUCTURA.INKG_SINC_ARCGIS AS
  
  PROCEDURE P_MIGRA_DESDE_GIS 
  AS 
    CURSOR C_ARCGIS IS
    SELECT ID_MIGRATELCOS_ELEMENTO, ID_ELEMENTO_ARGIS, UBICACION_ID, DESCRIPCION_ELEMENTO, DETALLE_ELEMENTO_ID, NOMBRE_ELEMENTO,  TIPO_ELEMENTO,
    MODELO_ELEMENTO, LAST_EDITED_USER, LAST_EDITED_DATE, COMENTARIOS, ESTADO_SINCRONIZACION, CAPA
    FROM SDE.MIGRATELCOS_ELEMENTO@dblink_arc 
    WHERE (ESTADO_SINCRONIZACION = 'PENDIENTE' OR ESTADO_SINCRONIZACION = 'ELIMINAR')
    ORDER BY ID_MIGRATELCOS_ELEMENTO ASC; 
    -- 
    Ln_Idtelcos               DB_INFRAESTRUCTURA.INFO_ELEMENTO.ID_ELEMENTO%TYPE;
    Ln_existe                 NUMBER (4); 
    Ln_existe_cursor          NUMBER (4); 
    Lv_Estado_Telcos          VARCHAR2(15); 
    Lv_Mensaje                VARCHAR2(250); 
    Lv_Estado_ArcGis          VARCHAR2(250); 
    Ln_Tipo_Elemento          NUMBER (6);
    Ln_Modelo_Elemento        DB_INFRAESTRUCTURA.INFO_ELEMENTO.MODELO_ELEMENTO_ID%TYPE;
    Ln_Id_Parroquia           NUMBER (10);
    Ln_Edificacion            NUMBER (10);
    Lv_Usr_ArcGis             VARCHAR2(250);
    Lv_Provincia              DB_GENERAL.ADMI_PROVINCIA.NOMBRE_PROVINCIA%TYPE;
    Lv_Canton                 DB_GENERAL.ADMI_CANTON.NOMBRE_CANTON%TYPE;
    Lv_Parroquia              DB_GENERAL.ADMI_PARROQUIA.NOMBRE_PARROQUIA%TYPE;
    Lv_Direccion              DB_INFRAESTRUCTURA.INFO_UBICACION.DIRECCION_UBICACION%TYPE;
    Ln_Latitud                DB_INFRAESTRUCTURA.INFO_UBICACION.LATITUD_UBICACION%TYPE;
    Ln_Longitud               DB_INFRAESTRUCTURA.INFO_UBICACION.LONGITUD_UBICACION%TYPE;
    Lv_Telconet               VARCHAR2(50);
    Lv_Megadatos             VARCHAR2(50);
    Ln_Altura                 DB_INFRAESTRUCTURA.INFO_UBICACION.ALTURA_SNM%TYPE;
    Lr_InfoElemento           DB_INFRAESTRUCTURA.INFO_ELEMENTO%ROWTYPE;
    Lr_InfoHistorialElemento  DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO%ROWTYPE;
    Lr_InfoEmpresaElemento    DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO%ROWTYPE;
    Lr_Atributos              SDE.MIGRATELCOS_DETALLE_ELEMENTO@dblink_arc%ROWTYPE;
    --
    BEGIN 
      --
      IF C_ARCGIS%ISOPEN THEN
        CLOSE C_ARCGIS;
      END IF;
      -- 
      SELECT COUNT(*) INTO Ln_existe_cursor
      FROM SDE.MIGRATELCOS_ELEMENTO@dblink_arc
      WHERE (ESTADO_SINCRONIZACION = 'PENDIENTE' OR ESTADO_SINCRONIZACION = 'ELIMINAR');
      --
      IF Ln_existe_cursor>0 THEN
        FOR REG IN C_ARCGIS LOOP 
          Lv_MENSAJE        :=NULL;
          Lv_Estado_Telcos  :='Activo';
          Ln_existe         :=0;
          Lv_Usr_ArcGis     :=REG.LAST_EDITED_USER;
          Ln_Tipo_Elemento  :=INKG_SINC_ARCGIS.GET_CODIGO_TIPO_ELEMENTO(REG.TIPO_ELEMENTO);

          --SI EL REGISTRO ES PARA CREAR O ACTUALIZAR TENDRÁ ESTADO PENDIENTE
          IF REG.ESTADO_SINCRONIZACION='PENDIENTE' THEN
          /*
            Si existen elementos que consten como eliminados con ese nombre se reactivan para considerarlos como actualización
            esto se hace por que ArcGis permite usar ctrl+z y puede reactivarse una caja eliminada antes por error.
          */
          INKG_SINC_ARCGIS.P_REACTIVA_ELEMENTO(Ln_Tipo_Elemento, REG.NOMBRE_ELEMENTO, Ln_IDTELCOS, Ln_existe, Lv_MENSAJE);

            --Traer los atributos específicos, sirve para todos los elementos
            SELECT * INTO Lr_Atributos
            FROM SDE.MIGRATELCOS_DETALLE_ELEMENTO@dblink_arc
            WHERE ID_DETALLE_ELEMENTO=REG.DETALLE_ELEMENTO_ID;

            --Traer los datos de ubicación, sirve para todos los elementos
            SELECT PROVINCIA, CANTON, PARROQUIA, DIRECCION, LATITUD, LONGITUD, ALTURA, TELCONET, MEGADATOS
            INTO Lv_Provincia, Lv_canton, Lv_Parroquia, Lv_Direccion, Ln_Latitud, Ln_Longitud, Ln_Altura, Lv_Telconet, Lv_Megadatos
            FROM SDE.MIGRATELCOS_UBICACION@dblink_arc 
            WHERE ID_UBICACION=REG.UBICACION_ID;

            --VALIDACIONES GENERALES A TODOS LOS ELEMENTOS
            INKG_SINC_ARCGIS.P_VALIDAR_UBICACION(Lv_Provincia, Lv_canton, Lv_Parroquia, Ln_Id_parroquia, Lv_Mensaje);
            INKG_SINC_ARCGIS.P_VALIDAR_MODELO_ELEMENTO(REG.MODELO_ELEMENTO, Ln_Modelo_elemento, Lv_Mensaje);

            --VALIDACIONES ESPECÍFICAS POR TIPO DE ELEMENTO EN MIGRATELCOS_ELEMENTO
            IF Ln_Tipo_Elemento = 61 THEN
              INKG_SINC_ARCGIS.P_VALIDAR_EDIFICIO(Lr_Atributos.Edificacion, Ln_Edificacion, Lv_Mensaje);
              INKG_SINC_ARCGIS.P_VALIDAR_DETALLE_CAJA(Lr_Atributos.Nivel, Lr_Atributos.Ubicado_en, Lr_Atributos.Tipo_Lugar, Lv_Mensaje);
            ELSE
              Lv_Mensaje:= 'Error, el tipo elemento ' ||REG.TIPO_ELEMENTO||' no esta permitido para ser sincronizado en Telcos';
            END IF;
            --
            IF Ln_existe =0 THEN --PROCESO CREAR
              IF Lv_Mensaje IS NULL AND Ln_Modelo_elemento > 0 THEN
                Lv_Estado_Telcos    :='Activo';
                Ln_IDTELCOS         :=DB_INFRAESTRUCTURA.SEQ_INFO_ELEMENTO.NEXTVAL;
                -- 
                Lr_InfoElemento                      := NULL;
                Lr_InfoElemento.ID_ELEMENTO          := Ln_IDTELCOS;
                Lr_InfoElemento.MODELO_ELEMENTO_ID   := Ln_Modelo_elemento;
                Lr_InfoElemento.NOMBRE_ELEMENTO      := REG.NOMBRE_ELEMENTO;
                Lr_InfoElemento.DESCRIPCION_ELEMENTO := REG.Descripcion_Elemento;
                Lr_InfoElemento.USR_RESPONSABLE      := Lv_Usr_ArcGis;
                Lr_InfoElemento.USR_CREACION         := Lv_Usr_ArcGis;
                Lr_InfoElemento.OBSERVACION          := REG.COMENTARIOS;
                Lr_InfoElemento.FE_CREACION          := SYSDATE;
                Lr_InfoElemento.IP_CREACION          := '127.0.0.1';
                Lr_InfoElemento.ESTADO               := Lv_Estado_Telcos;
                INKG_SINC_ARCGIS.P_INSERT_ELEMENTO(Lr_InfoElemento, Lv_MENSAJE);
                --
                INKG_SINC_ARCGIS.P_INSERTAR_DETALLE_ELEMENTO(Ln_IDTELCOS, REG.DETALLE_ELEMENTO_ID, Ln_Tipo_Elemento, Lv_Usr_ArcGis, Lv_MENSAJE);
                --Si existe una edificacion o ciuadela como contenedor de elemento se vincula
                INKG_SINC_ARCGIS.P_INSERTAR_EDIFICIO(Ln_Edificacion,Ln_IDTELCOS,Lv_Usr_ArcGis, Lv_MENSAJE);
                INKG_SINC_ARCGIS.P_INSERTAR_UBICACION(Ln_IDTELCOS, Ln_Id_parroquia, Ln_Latitud, Ln_Longitud, Ln_Altura, Lv_Direccion, Lv_Usr_ArcGis, Lv_Telconet, Lv_Megadatos, Lv_MENSAJE);
                --
              ELSE
                Lv_MENSAJE:=Lv_MENSAJE || 'Error: Datos incorrectos verifique la información del objeto '|| REG.ID_ELEMENTO_ARGIS ||' en ArcGis.';
              END IF;
              Lv_Estado_ArcGis:='SINCRONIZADO -C';
            --
            ELSE --PROCESO ACTUALIZAR
              IF Lv_Mensaje IS NULL AND Ln_Modelo_elemento > 0 THEN
                Lv_Estado_Telcos:='Activo';
                --
                IF Ln_Tipo_Elemento = 61 THEN
                  INKG_SINC_ARCGIS.P_ACTUALIZA_ELEMENTO_CAJA(Ln_IDTELCOS, REG.Descripcion_Elemento, Ln_Modelo_elemento, REG.COMENTARIOS, Lv_MENSAJE);
                  INKG_SINC_ARCGIS.P_INSERTAR_DETALLE_ELEMENTO(Ln_IDTELCOS, REG.DETALLE_ELEMENTO_ID, Ln_Tipo_Elemento, Lv_Usr_ArcGis, Lv_MENSAJE); 
                ELSE
                  Lv_Mensaje:= 'Error, el tipo elemento ' ||REG.TIPO_ELEMENTO||' no esta permitido para ser sincronizado en Telcos';
                END IF;
                --Si existe una edificacion o ciuadela como contenedor de elemento se vincula
                INKG_SINC_ARCGIS.P_INSERTAR_EDIFICIO(Ln_Edificacion,Ln_IDTELCOS,Lv_Usr_ArcGis, Lv_MENSAJE);
                --
                INKG_SINC_ARCGIS.P_INSERTAR_UBICACION(Ln_IDTELCOS, Ln_Id_parroquia, Ln_Latitud, Ln_Longitud, Ln_Altura, Lv_Direccion, Lv_Usr_ArcGis, Lv_Telconet, Lv_Megadatos, Lv_MENSAJE);
              END IF;
              Lv_Estado_ArcGis:='SINCRONIZADO -A';
            END IF;
            --Se registra el ID_telcos en la tabla de migración para identificación
            INKG_SINC_ARCGIS.P_SET_IDTELCOS_EN_BDARCGIS(Ln_IDTELCOS, REG.ID_MIGRATELCOS_ELEMENTO, Lv_MENSAJE);
            --
          ELSE --PROCESO ELIMINAR 
            SELECT NVL(MAX(INFO_ELEMENTO.ID_ELEMENTO),0), COUNT(INFO_ELEMENTO.NOMBRE_ELEMENTO) INTO Ln_IDTELCOS, Ln_existe
            FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO, DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
            WHERE  INFO_ELEMENTO.MODELO_ELEMENTO_ID=ADMI_MODELO_ELEMENTO.ID_MODELO_ELEMENTO
            AND INFO_ELEMENTO.ESTADO                  ='Activo'
            AND ADMI_MODELO_ELEMENTO.TIPO_ELEMENTO_ID =Ln_Tipo_Elemento
            AND INFO_ELEMENTO.NOMBRE_ELEMENTO         =REG.NOMBRE_ELEMENTO;

            IF Ln_existe=0  or Ln_existe IS NULL THEN
              Lv_Estado_Telcos:='Eliminado';
              Lv_MENSAJE:='Error: El elemento eliminado en ArcGis, no existe o ya ha sido eliminado en Telcos.';
            ELSE
              INKG_SINC_ARCGIS.P_ACTUALIZA_ESTADO_ELEMENTO(Ln_IDTELCOS, 'Eliminado', Lv_MENSAJE);
              Lv_Estado_Telcos:='Eliminado';
              Lv_Estado_ArcGis:='SINCRONIZADO -E';
              INKG_SINC_ARCGIS.P_SET_IDTELCOS_EN_BDARCGIS(Ln_IDTELCOS, REG.ID_MIGRATELCOS_ELEMENTO, Lv_MENSAJE);
            END IF;
          END IF;

          IF Lv_MENSAJE is not null THEN
            Lv_Estado_ArcGis:='ERRORES';
            INKG_SINC_ARCGIS.P_SET_ESTADO_EN_BDARCGIS(REG.ID_MIGRATELCOS_ELEMENTO, 0, Lv_Estado_ArcGis, Lv_MENSAJE);
            COMMIT;
          ELSE
            --Preparamos e insertamos información de historial
            Lr_InfoHistorialElemento.ID_HISTORIAL     :=DB_INFRAESTRUCTURA.SEQ_INFO_HISTORIAL_ELEMENTO.NEXTVAL;
            Lr_InfoHistorialElemento.ELEMENTO_ID      :=Ln_IDTELCOS;
            Lr_InfoHistorialElemento.ESTADO_ELEMENTO  :=Lv_Estado_Telcos;
            IF Lv_Estado_ArcGis = 'SINCRONIZADO -A' THEN
              Lr_InfoHistorialElemento.ESTADO_ELEMENTO  :='Modificado';
            END IF; 
            Lr_InfoHistorialElemento.CAPACIDAD        :=null; 
            Lr_InfoHistorialElemento.OBSERVACION      :='Se sincroniza '||REG.TIPO_ELEMENTO|| ' de ID: '|| Ln_IDTELCOS || ' por medio de ArcGis con ID de Migración: ' || REG.ID_MIGRATELCOS_ELEMENTO;
            Lr_InfoHistorialElemento.USR_CREACION     :=Lv_Usr_ArcGis;
            Lr_InfoHistorialElemento.FE_CREACION      :=SYSDATE;
            Lr_InfoHistorialElemento.IP_CREACION      :='127.0.0.1';
            INKG_SINC_ARCGIS.P_INSERT_HISTORIAL(Lr_InfoHistorialElemento, Lv_MENSAJE);
            --
            --Preparamos los datos para P_INSERT_INFO_EMPRESA_ELEMENTO
            Lr_InfoEmpresaElemento.ELEMENTO_ID          :=Ln_IDTELCOS;
            Lr_InfoEmpresaElemento.OBSERVACION          :=NULL;
            Lr_InfoEmpresaElemento.ESTADO               :='Activo'; 
            Lr_InfoEmpresaElemento.USR_CREACION         :=Lv_Usr_ArcGis;
            Lr_InfoEmpresaElemento.FE_CREACION          :=SYSDATE; 
            Lr_InfoEmpresaElemento.IP_CREACION          :='127.0.0.1';
            INKG_SINC_ARCGIS.P_INSERT_INFO_EMPRESA_ELEMENTO(Lr_InfoEmpresaElemento, Lv_Telconet, Lv_Megadatos, Lv_MENSAJE);
            --
            INKG_SINC_ARCGIS.P_SET_ESTADO_EN_BDARCGIS(REG.ID_MIGRATELCOS_ELEMENTO, Ln_IDTELCOS, Lv_Estado_ArcGis, Lv_MENSAJE);
            --
            COMMIT;
          END IF; 
        END LOOP;
      END IF;
      --
      IF C_ARCGIS%ISOPEN THEN
        CLOSE C_ARCGIS;
      END IF;
      -- 
      ROLLBACK;
      EXCEPTION
      WHEN OTHERS THEN
      DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR('ArcGis - Telcos', 
                                           'INKG_SINC_ARCGIS.P_MIGRA_DESDE_GIS', 
                                           Lv_MENSAJE, 
                                           'DB_INFRAESTRUCTURA', 
                                           sysdate, 
                                           '127.0.0.1');
  END P_MIGRA_DESDE_GIS;


  PROCEDURE P_VALIDAR_DETALLE_CAJA(
  Pn_NIVEL_SPLITTER IN NUMBER,
  Pv_UBICADO_EN     IN VARCHAR2,
  Pv_TIPO_LUGAR     IN VARCHAR2,
  Pv_MENSAJE        IN OUT VARCHAR2)
  AS
  BEGIN
    IF Pn_NIVEL_SPLITTER IS NULL OR Pv_UBICADO_EN IS NULL OR Pv_TIPO_LUGAR IS NULL THEN
      Pv_MENSAJE:=Pv_MENSAJE || 'Faltan detalles de elemento';
    END IF;
  END P_VALIDAR_DETALLE_CAJA;


  PROCEDURE P_INSERTAR_UBICACION(
  Pn_ID_ELEMENTO  IN NUMBER,
  Pn_PARROQUIA    IN NUMBER,
  Pn_LATITUD      IN NUMBER,
  Pn_LONGITUD     IN NUMBER,
  Pn_ALTURA       IN NUMBER,
  Pv_DIRECCION    IN VARCHAR2,
  Pv_USR_ARCGIS   IN VARCHAR2,
  Pv_TELCONET     IN VARCHAR2,
  Pv_MEGADATOS    IN VARCHAR2,
  Pv_MENSAJE      IN OUT VARCHAR2)
  AS 
  Ln_UBICACION    NUMBER (12);
  Ln_existe       NUMBER (5);
  BEGIN

          Ln_UBICACION:=DB_INFRAESTRUCTURA.SEQ_INFO_UBICACION.NEXTVAL;
          INSERT INTO DB_INFRAESTRUCTURA.INFO_UBICACION(ID_UBICACION,PARROQUIA_ID,DIRECCION_UBICACION,LONGITUD_UBICACION,LATITUD_UBICACION,ALTURA_SNM,USR_CREACION,FE_CREACION,IP_CREACION) 
          values (Ln_UBICACION, Pn_PARROQUIA,Pv_DIRECCION,Pn_LONGITUD, Pn_LATITUD, Pn_ALTURA, Pv_Usr_ArcGis, SYSDATE ,'127.0.0.1');
          --
          SELECT COUNT(ELEMENTO_ID) INTO Ln_existe 
          FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA 
          WHERE ELEMENTO_ID =Pn_ID_ELEMENTO
          AND EMPRESA_COD   =10;
          IF Ln_existe >0 THEN
            UPDATE INFO_EMPRESA_ELEMENTO_UBICA 
            SET UBICACION_ID=Ln_UBICACION 
            WHERE ELEMENTO_ID=Pn_ID_ELEMENTO;
          ELSE
            IF UPPER(Pv_TELCONET) ='SI' THEN
              Insert into INFO_EMPRESA_ELEMENTO_UBICA (ID_EMPRESA_ELEMENTO_UBICACION,EMPRESA_COD,ELEMENTO_ID,UBICACION_ID,USR_CREACION,FE_CREACION,IP_CREACION) 
              values (DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO_UBI.NEXTVAL,'10',Pn_ID_ELEMENTO,Ln_UBICACION,Pv_Usr_ArcGis,SYSDATE,'127.0.0.1');
            END IF;
          END IF;

          SELECT COUNT(ELEMENTO_ID) INTO Ln_existe 
          FROM DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA 
          WHERE ELEMENTO_ID =Pn_ID_ELEMENTO
          AND EMPRESA_COD   =18;
          IF Ln_existe >0 THEN
            UPDATE INFO_EMPRESA_ELEMENTO_UBICA 
            SET UBICACION_ID=Ln_UBICACION 
            WHERE ELEMENTO_ID=Pn_ID_ELEMENTO;
          ELSE
            IF UPPER(Pv_MEGADATOS) ='SI' THEN
            Insert into INFO_EMPRESA_ELEMENTO_UBICA (ID_EMPRESA_ELEMENTO_UBICACION,EMPRESA_COD,ELEMENTO_ID,UBICACION_ID,USR_CREACION,FE_CREACION,IP_CREACION) 
            values (DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO_UBI.NEXTVAL,'18',Pn_ID_ELEMENTO,Ln_UBICACION,Pv_Usr_ArcGis,SYSDATE,'127.0.0.1');
            END IF;
          END IF;          
        EXCEPTION
    WHEN OTHERS THEN
    Pv_MENSAJE := Pv_MENSAJE || ' Error al insertar ubicación';
END P_INSERTAR_UBICACION;


  FUNCTION GET_CODIGO_TIPO_ELEMENTO(
  Pv_Tipo_Elemento        IN VARCHAR2)
  RETURN NUMBER
  IS
  Ln_Codigo_tipo_elemento NUMBER (12);
  Lv_Tipo_Elemento        VARCHAR2 (100);
  BEGIN
    IF Pv_Tipo_Elemento IN ('CAJA','PEDESTAL') THEN 
      Lv_Tipo_Elemento:='CAJA DISPERSION';
      else
      Lv_Tipo_Elemento:=Pv_Tipo_Elemento;
    END IF;
    SELECT MAX(ID_TIPO_ELEMENTO) INTO Ln_Codigo_tipo_elemento
    FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO
    WHERE UPPER(NOMBRE_TIPO_ELEMENTO)=UPPER(Lv_Tipo_Elemento);
    --
    RETURN Ln_Codigo_tipo_elemento;
  END GET_CODIGO_TIPO_ELEMENTO;


  PROCEDURE P_VALIDAR_MODELO_ELEMENTO(
  Pv_Modelo_Elemento    IN VARCHAR2,
  Pn_Id_Modelo_Elemento OUT NUMBER,
  Pv_MENSAJE            IN OUT VARCHAR2)
  IS
  BEGIN
    SELECT NVL(ID_MODELO_ELEMENTO,0) INTO Pn_Id_Modelo_Elemento 
    FROM DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO 
    WHERE NOMBRE_MODELO_ELEMENTO=Pv_Modelo_Elemento;
    --
    EXCEPTION
    WHEN OTHERS THEN
      Pn_Id_Modelo_Elemento:=0;
      Pv_MENSAJE:= Pv_MENSAJE || ' Modelo de elemento incorrecto verificar opción ingresada. ';
  END P_VALIDAR_MODELO_ELEMENTO; 


  PROCEDURE P_VALIDAR_EDIFICIO(
  Pv_Nombre_Edificacion IN VARCHAR2,
  Pn_Edificacion        OUT NUMBER,
  Pv_Mensaje            IN OUT VARCHAR2)
  IS
  BEGIN
    IF Pv_Nombre_Edificacion IS NULL OR Pv_Nombre_Edificacion = ' ' OR Pv_Nombre_Edificacion = 'NINGUNA'THEN
      Pn_Edificacion:=0;
    ELSE
      SELECT NVL(E.ID_ELEMENTO,0) INTO Pn_Edificacion
      FROM DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO M, DB_INFRAESTRUCTURA.INFO_ELEMENTO E
      WHERE M.ID_MODELO_ELEMENTO=E.MODELO_ELEMENTO_ID
      AND M.TIPO_ELEMENTO_ID IN (SELECT T.ID_TIPO_ELEMENTO
                                  FROM DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO T
                                  WHERE T.NOMBRE_TIPO_ELEMENTO='EDIFICACION'
                                  AND   T.ESTADO='Activo')
      AND E.ESTADO          ='Activo'
      AND E.NOMBRE_ELEMENTO =Pv_Nombre_Edificacion;
    END IF;
    --
    EXCEPTION
    WHEN OTHERS THEN
      Pn_Edificacion:=0;
      Pv_Mensaje    := Pv_MENSAJE || ' Error: Edificio no se encuentra registrado, verificar la opción ingresada. ';
  END P_VALIDAR_EDIFICIO; 


  PROCEDURE P_VALIDAR_UBICACION(
  Pv_Provincia    IN VARCHAR2,
  Pv_Canton       IN VARCHAR2,
  Pv_Parroquia    IN VARCHAR2,
  Pn_Id_Parroquia OUT NUMBER,
  Pv_MENSAJE      IN OUT VARCHAR2)
  IS
  Ln_Provincia    NUMBER (6);
  Ln_Canton       NUMBER (6);
  BEGIN
      SELECT NVL(MAX(ID_PROVINCIA),0) INTO Ln_Provincia 
      FROM DB_GENERAL.ADMI_PROVINCIA
      WHERE ESTADO='Activo'
      AND NOMBRE_PROVINCIA=UPPER(Pv_Provincia);
      IF Ln_PROVINCIA > 0 THEN
        SELECT NVL(MAX(ID_CANTON),0) INTO Ln_Canton 
        FROM DB_GENERAL.ADMI_CANTON
        WHERE PROVINCIA_ID=Ln_PROVINCIA
        AND ESTADO='Activo'
        AND NOMBRE_CANTON=UPPER(Pv_Canton);
        IF Ln_CANTON >0 THEN
          SELECT NVL(MAX(ID_PARROQUIA),0) INTO Pn_Id_Parroquia
          FROM DB_GENERAL.ADMI_PARROQUIA
          WHERE CANTON_ID=Ln_CANTON
          AND ESTADO='Activo'
          AND NOMBRE_PARROQUIA=UPPER(Pv_Parroquia);
        END IF;
      END IF;
      IF Pn_Id_Parroquia = 0 THEN
        Pv_MENSAJE:= Pv_MENSAJE || ' Error, ubicación geográfica/política incorrecta.';
      END IF;
  END P_VALIDAR_UBICACION;


  PROCEDURE P_REACTIVA_ELEMENTO(
  Pn_Tipo_Elemento    IN NUMBER,
  Pv_Nombre_Elemento  IN VARCHAR2,
  Pn_IDTELCOS         IN OUT NUMBER,
  Pn_EXISTE           IN OUT NUMBER,
  Pv_Mensaje          IN OUT VARCHAR2)
  AS
  Ln_ID_Elemento      NUMBER (10);
  Ln_Modelo_Elemento  NUMBER (6);
  Ln_existe           NUMBER (5);
  BEGIN

    SELECT NVL(MAX(INFO_ELEMENTO.ID_ELEMENTO),0), COUNT(INFO_ELEMENTO.NOMBRE_ELEMENTO) INTO Ln_ID_Elemento, Ln_existe
    FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO, DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
    WHERE  INFO_ELEMENTO.MODELO_ELEMENTO_ID=ADMI_MODELO_ELEMENTO.ID_MODELO_ELEMENTO
    AND INFO_ELEMENTO.ESTADO                  ='Eliminado'
    AND ADMI_MODELO_ELEMENTO.TIPO_ELEMENTO_ID =Pn_Tipo_Elemento
    AND INFO_ELEMENTO.NOMBRE_ELEMENTO         =Pv_Nombre_Elemento;
    IF Ln_existe > 0 THEN
      SELECT MODELO_ELEMENTO_ID INTO Ln_Modelo_elemento 
      FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO
      WHERE ID_ELEMENTO=Ln_ID_Elemento;
      --
      UPDATE INFO_ELEMENTO SET 
        ESTADO  ='Activo'
      WHERE ID_ELEMENTO = Ln_ID_Elemento
      AND MODELO_ELEMENTO_ID = Ln_Modelo_elemento;
      --
      Pn_IDTELCOS :=Ln_ID_Elemento;
      Pn_EXISTE   :=Ln_existe;
    ELSE
      SELECT NVL(MAX(INFO_ELEMENTO.ID_ELEMENTO),0), COUNT(INFO_ELEMENTO.ID_ELEMENTO) INTO Pn_IDTELCOS, Pn_EXISTE
      FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO, DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
      WHERE  INFO_ELEMENTO.MODELO_ELEMENTO_ID=ADMI_MODELO_ELEMENTO.ID_MODELO_ELEMENTO
      AND INFO_ELEMENTO.ESTADO='Activo'
      AND ADMI_MODELO_ELEMENTO.TIPO_ELEMENTO_ID=Pn_Tipo_Elemento
      AND INFO_ELEMENTO.NOMBRE_ELEMENTO=Pv_Nombre_Elemento;
    END IF;
    --
    EXCEPTION
    WHEN OTHERS THEN
    Pv_MENSAJE := Pv_MENSAJE || 'No se puede reactivar elemento'; 
  END P_REACTIVA_ELEMENTO;


  PROCEDURE P_INSERT_ELEMENTO
  (
    Pr_InfoElemento IN DB_INFRAESTRUCTURA.INFO_ELEMENTO%ROWTYPE,
    Pv_Mensaje      IN OUT VARCHAR2
  )
  IS
  BEGIN
    INSERT INTO DB_INFRAESTRUCTURA.INFO_ELEMENTO VALUES Pr_InfoElemento;
  EXCEPTION
  WHEN OTHERS THEN
    Pv_Mensaje := Pv_MENSAJE || ' No se puede insertar elemento';
  END P_INSERT_ELEMENTO;


  PROCEDURE P_INSERT_HISTORIAL 
  (
    Pr_InfoHistorialElemento  IN DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO%ROWTYPE,
    Pv_Mensaje                IN OUT VARCHAR2 
  )
  IS
  BEGIN
    INSERT INTO DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO VALUES Pr_InfoHistorialElemento;
  EXCEPTION
  WHEN OTHERS THEN
    Pv_Mensaje := Pv_MENSAJE || ' No se puede insertar historial de elemento';
  END P_INSERT_HISTORIAL;


  PROCEDURE P_INSERT_INFO_EMPRESA_ELEMENTO 
  (
    Pr_InfoEmpresaElemento  IN DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO%ROWTYPE,
    Pv_Telconet             IN  VARCHAR2,
    Pv_Megadatos            IN  VARCHAR2,
    Pv_Mensaje              IN OUT VARCHAR2 
  )
  IS
    Ln_Existe NUMBER(6);
    Lr_InfoEmpresaElemento DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO%ROWTYPE;
  BEGIN
    Lr_InfoEmpresaElemento:=Pr_InfoEmpresaElemento;
    IF UPPER(Pv_Telconet) = 'SI' THEN
        SELECT COUNT(ELEMENTO_ID) INTO Ln_Existe
        FROM INFO_EMPRESA_ELEMENTO
        WHERE ELEMENTO_ID   = Lr_InfoEmpresaElemento.ELEMENTO_ID
        AND EMPRESA_COD     = '10';
        IF Ln_Existe = 0 THEN
            Lr_InfoEmpresaElemento.ID_EMPRESA_ELEMENTO  :=DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO.NEXTVAL;
            Lr_InfoEmpresaElemento.EMPRESA_COD          :='10';
            INSERT INTO DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO VALUES Lr_InfoEmpresaElemento;
        END IF;
    END IF;

    IF UPPER(Pv_Megadatos) = 'SI' THEN
        SELECT COUNT(ELEMENTO_ID) INTO Ln_Existe
        FROM INFO_EMPRESA_ELEMENTO
        WHERE ELEMENTO_ID   = Lr_InfoEmpresaElemento.ELEMENTO_ID
        AND EMPRESA_COD     = '18';
        IF Ln_Existe = 0 THEN
            Lr_InfoEmpresaElemento.ID_EMPRESA_ELEMENTO  :=DB_INFRAESTRUCTURA.SEQ_INFO_EMPRESA_ELEMENTO.NEXTVAL;
            Lr_InfoEmpresaElemento.EMPRESA_COD          :='18';
            INSERT INTO DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO VALUES Lr_InfoEmpresaElemento;
        END IF;
    END IF;


  EXCEPTION
  WHEN OTHERS THEN
    Pv_Mensaje := Pv_MENSAJE || ' No se puede insertar info_empresa_elemento';

  END P_INSERT_INFO_EMPRESA_ELEMENTO; 


  PROCEDURE P_INSERTAR_EDIFICIO 
  (
    Pn_Edificacion  IN NUMBER,
    Pn_IDTELCOS     IN NUMBER,
    Pv_Usuario      IN VARCHAR2,
    Pv_Mensaje      IN OUT VARCHAR2 
  )
  IS 
  Lr_Info_RelacionElemento DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO%ROWTYPE;
  Ln_existe         NUMBER(5);
  BEGIN
    SELECT COUNT(ID_RELACION_ELEMENTO) INTO Ln_existe 
    FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO
    WHERE ELEMENTO_ID_B=Pn_IDTELCOS
    AND ESTADO='Activo';
    IF Ln_existe > 0 AND Pn_Edificacion = 0 THEN
      UPDATE DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO
      SET ESTADO = 'Eliminado'
      WHERE ELEMENTO_ID_B=Pn_IDTELCOS
      AND ESTADO='Activo';
    ELSIF Pn_Edificacion > 0 THEN 
      UPDATE DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO
      SET ESTADO = 'Eliminado'
      WHERE ELEMENTO_ID_B=Pn_IDTELCOS
      AND ESTADO='Activo';
      Lr_Info_RelacionElemento.ID_RELACION_ELEMENTO  :=DB_INFRAESTRUCTURA.SEQ_INFO_RELACION_ELEMENTO.NEXTVAL;
      Lr_Info_RelacionElemento.ELEMENTO_ID_A         :=Pn_Edificacion;
      Lr_Info_RelacionElemento.ELEMENTO_ID_B         :=Pn_IDTELCOS;
      Lr_Info_RelacionElemento.TIPO_RELACION         :='CONTIENE';
      Lr_Info_RelacionElemento.OBSERVACION           :='Nodo Cliente contiene Caja';
      Lr_Info_RelacionElemento.ESTADO                :='Activo';
      Lr_Info_RelacionElemento.USR_CREACION          :=Pv_Usuario;
      Lr_Info_RelacionElemento.FE_CREACION           :=SYSDATE;
      Lr_Info_RelacionElemento.IP_CREACION           :='127.0.0.1';
      INSERT INTO DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO VALUES Lr_Info_RelacionElemento;
    END IF;
  EXCEPTION
  WHEN OTHERS THEN
    Pv_Mensaje := Pv_MENSAJE || 'No se puede insertar edificio';
  END P_INSERTAR_EDIFICIO; 


  PROCEDURE P_SET_IDTELCOS_EN_BDARCGIS
  (
  Pn_ID_Telcos    IN NUMBER,
  Pn_ID_MIGRATELCOS_ELEMENTO IN NUMBER,
  Pv_Mensaje      IN OUT VARCHAR2
  )
  IS
  BEGIN
    UPDATE SDE.MIGRATELCOS_ELEMENTO@dblink_arc SET 
      ELEMENTO_ID=Pn_ID_Telcos 
    WHERE ID_MIGRATELCOS_ELEMENTO=Pn_ID_MIGRATELCOS_ELEMENTO;
    EXCEPTION
    WHEN OTHERS THEN
      Pv_Mensaje :=Pv_MENSAJE || 'No se puede insertar el id de ArcGis en la tabla de migración';
  END P_SET_IDTELCOS_EN_BDARCGIS;


  PROCEDURE P_SET_ESTADO_EN_BDARCGIS
  (
  Pn_ID_MIGRATELCOS_ELEMENTO IN NUMBER,
  Pn_ID_Telcos    IN NUMBER,
  Pv_Estado       IN VARCHAR2,  
  Pv_Mensaje      IN OUT VARCHAR2
  )
  IS
  BEGIN
    IF Pv_MENSAJE IS NULL THEN
      UPDATE SDE.MIGRATELCOS_ELEMENTO@dblink_arc SET
      ESTADO_SINCRONIZACION  =Pv_Estado, 
      ELEMENTO_ID =Pn_ID_Telcos 
      WHERE ID_MIGRATELCOS_ELEMENTO=Pn_ID_MIGRATELCOS_ELEMENTO;
    ELSE
      UPDATE SDE.MIGRATELCOS_ELEMENTO@dblink_arc SET
      ESTADO_SINCRONIZACION  =Pv_Estado || ' ' || Pv_Mensaje
      --ELEMENTO_ID =Pn_ID_Telcos 
      WHERE ID_MIGRATELCOS_ELEMENTO=Pn_ID_MIGRATELCOS_ELEMENTO;
    END IF;
    EXCEPTION
    WHEN OTHERS THEN
      Pv_Mensaje := Pv_MENSAJE || 'No se puede actualizar el estado en la tabla de migración';
  END P_SET_ESTADO_EN_BDARCGIS;


  PROCEDURE P_ACTUALIZA_ESTADO_ELEMENTO
  (
  Pn_ID_Telcos  IN NUMBER,
  Pv_Estado     IN VARCHAR2,
  Pv_Mensaje    IN OUT VARCHAR2
  )
  IS
  BEGIN
    UPDATE INFO_ELEMENTO SET 
      ESTADO=Pv_Estado 
    where ID_ELEMENTO=Pn_ID_Telcos;
    EXCEPTION 
    WHEN OTHERS THEN
      Pv_Mensaje := Pv_MENSAJE || 'No se puede actualizar el estado del elemento';
  END P_ACTUALIZA_ESTADO_ELEMENTO;


  PROCEDURE P_ACTUALIZA_ELEMENTO_CAJA
  (
  Pn_ID_Telcos      IN NUMBER, 
  Lv_Descripcion    IN VARCHAR2,
  Ln_Modelo         IN NUMBER,
  Pv_Comentarios    IN VARCHAR2,
  Pv_Mensaje        IN OUT VARCHAR2
  )
  IS
  BEGIN
    UPDATE DB_INFRAESTRUCTURA.INFO_ELEMENTO SET 
      DESCRIPCION_ELEMENTO  =Lv_Descripcion,
      MODELO_ELEMENTO_ID    =Ln_Modelo,
      OBSERVACION           =Pv_Comentarios
    WHERE ID_ELEMENTO=Pn_ID_Telcos;
  EXCEPTION
  WHEN OTHERS THEN
    Pv_Mensaje := Pv_MENSAJE || 'No se puede actualizar el elemento';
  END P_ACTUALIZA_ELEMENTO_CAJA;


  PROCEDURE P_INSERTAR_DETALLE_ELEMENTO(
    Pn_ID_ELEMENTO          IN NUMBER,
    Pn_DETALLE_ELEMENTO_ID  IN NUMBER,
    Pn_Tipo_Elemento        IN NUMBER,  
    Pv_USR_ARCGIS           IN VARCHAR2,
    Pv_MENSAJE              IN OUT VARCHAR2)
    AS 
    Ln_existe         NUMBER(5); 
    Lr_Info_Detalle_Elemento DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO%ROWTYPE;
    Lr_Detalle_Elemento_Arcgis SDE.MIGRATELCOS_DETALLE_ELEMENTO@dblink_arc%ROWTYPE;
    BEGIN

    SELECT * INTO Lr_Detalle_Elemento_Arcgis
    FROM SDE.MIGRATELCOS_DETALLE_ELEMENTO@dblink_arc 
    WHERE ID_DETALLE_ELEMENTO=Pn_DETALLE_ELEMENTO_ID;

      --Elemento Caja
      IF Pn_Tipo_Elemento =61 
        AND Lr_Detalle_Elemento_Arcgis.Nivel IS NOT NULL 
        AND Lr_Detalle_Elemento_Arcgis.UBICADO_EN IS NOT NULL 
        AND Lr_Detalle_Elemento_Arcgis.TIPO_LUGAR IS NOT NULL THEN
        SELECT COUNT(ID_DETALLE_ELEMENTO) INTO Ln_existe 
        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
        AND ESTADO='Activo'
        AND DETALLE_NOMBRE='NIVEL'; 
        IF Ln_existe >0 THEN
          UPDATE INFO_DETALLE_ELEMENTO
          SET DETALLE_VALOR=Lr_Detalle_Elemento_Arcgis.Nivel,
          USR_CREACION=Pv_Usr_ArcGis
          WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
          AND DETALLE_NOMBRE='NIVEL';
        ELSE
          Lr_Info_Detalle_Elemento.ID_DETALLE_ELEMENTO    :=DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
          Lr_Info_Detalle_Elemento.ELEMENTO_ID            :=Pn_ID_ELEMENTO;
          Lr_Info_Detalle_Elemento.DETALLE_NOMBRE         :='NIVEL';
          Lr_Info_Detalle_Elemento.DETALLE_VALOR          :=Lr_Detalle_Elemento_Arcgis.Nivel;
          Lr_Info_Detalle_Elemento.DETALLE_DESCRIPCION    :='Caracteristica para indicar el nivel';
          Lr_Info_Detalle_Elemento.USR_CREACION           :=Pv_Usr_ArcGis;
          Lr_Info_Detalle_Elemento.FE_CREACION            :=SYSDATE;
          Lr_Info_Detalle_Elemento.IP_CREACION            :='127.0.0.1'; 
          Lr_Info_Detalle_Elemento.REF_DETALLE_ELEMENTO_ID:=null;
          Lr_Info_Detalle_Elemento.ESTADO                 :='Activo';
          INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO VALUES Lr_Info_Detalle_Elemento;
          --
        END IF;

        SELECT COUNT(ID_DETALLE_ELEMENTO) INTO Ln_existe 
        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
        AND ESTADO='Activo'
        AND DETALLE_NOMBRE='TIPO LUGAR';
        IF Ln_existe >0 THEN
          UPDATE INFO_DETALLE_ELEMENTO
          SET DETALLE_VALOR=Lr_Detalle_Elemento_Arcgis.TIPO_LUGAR,
          USR_CREACION=Pv_Usr_ArcGis
          WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
          AND DETALLE_NOMBRE='TIPO LUGAR';
          --
        ELSE
          Lr_Info_Detalle_Elemento.ID_DETALLE_ELEMENTO    :=DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
          Lr_Info_Detalle_Elemento.ELEMENTO_ID            :=Pn_ID_ELEMENTO;
          Lr_Info_Detalle_Elemento.DETALLE_NOMBRE         :='TIPO LUGAR';
          Lr_Info_Detalle_Elemento.DETALLE_VALOR          :=Lr_Detalle_Elemento_Arcgis.TIPO_LUGAR;
          Lr_Info_Detalle_Elemento.DETALLE_DESCRIPCION    :='Caracteristicas para indicar el lugar del Elemento';
          Lr_Info_Detalle_Elemento.USR_CREACION           :=Pv_Usr_ArcGis;
          Lr_Info_Detalle_Elemento.FE_CREACION            :=SYSDATE;
          Lr_Info_Detalle_Elemento.IP_CREACION            :='127.0.0.1';
          Lr_Info_Detalle_Elemento.REF_DETALLE_ELEMENTO_ID:=null;
          Lr_Info_Detalle_Elemento.ESTADO                 :='Activo';
          INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO VALUES Lr_Info_Detalle_Elemento;
          --
         END IF;

        SELECT COUNT(ID_DETALLE_ELEMENTO) INTO Ln_existe 
        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
        AND ESTADO='Activo'
        AND DETALLE_NOMBRE='UBICADO EN';
        IF Ln_existe >0 THEN
          UPDATE INFO_DETALLE_ELEMENTO
          SET DETALLE_VALOR=Lr_Detalle_Elemento_Arcgis.UBICADO_EN,
          USR_CREACION=Pv_Usr_ArcGis
          WHERE ELEMENTO_ID=Pn_ID_ELEMENTO
          AND DETALLE_NOMBRE='UBICADO EN';
        ELSE
          Lr_Info_Detalle_Elemento.ID_DETALLE_ELEMENTO    :=DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
          Lr_Info_Detalle_Elemento.ELEMENTO_ID            :=Pn_ID_ELEMENTO;
          Lr_Info_Detalle_Elemento.DETALLE_NOMBRE         :='UBICADO EN';
          Lr_Info_Detalle_Elemento.DETALLE_VALOR          :=Lr_Detalle_Elemento_Arcgis.UBICADO_EN;
          Lr_Info_Detalle_Elemento.DETALLE_DESCRIPCION    :='Caracteristicas para indicar donde se ubica el Elemento';
          Lr_Info_Detalle_Elemento.USR_CREACION           :=Pv_Usr_ArcGis;
          Lr_Info_Detalle_Elemento.FE_CREACION            :=SYSDATE;
          Lr_Info_Detalle_Elemento.IP_CREACION            :='127.0.0.1';
          Lr_Info_Detalle_Elemento.REF_DETALLE_ELEMENTO_ID:=null;
          Lr_Info_Detalle_Elemento.ESTADO                 :='Activo';
          INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO VALUES Lr_Info_Detalle_Elemento;
          --
        END IF;
      ELSE
        Pv_MENSAJE:=Pv_MENSAJE || 'Error, faltan detalles de elemento';
      END IF;
      EXCEPTION
      WHEN OTHERS THEN
        ROLLBACK;
        Pv_MENSAJE := Pv_MENSAJE || ' No se puede insertar detalles del elemento';
  END P_INSERTAR_DETALLE_ELEMENTO;

END INKG_SINC_ARCGIS;

/
