--Este DDL debe ejecutarse en BD_ArcGis esquema SDE
--Creando las tablas que contienen los datos a migrar desde ArcGis a Telcos
CREATE TABLE "SDE"."MIGRATELCOS_ELEMENTO" 
(
"ID_MIGRATELCOS_ELEMENTO" 		NUMBER(38,0), 
"ID_ELEMENTO_ARGIS" 		NUMBER(38,0), 
"ELEMENTO_ID" 		NUMBER(38,0), 
"UBICACION_ID" 		NUMBER(38,0), 
"DETALLE_ELEMENTO_ID" 		NUMBER(38,0), 
"NOMBRE_ELEMENTO" 	NVARCHAR2(200),
"DESCRIPCION_ELEMENTO" 	NVARCHAR2(200),
"TIPO_ELEMENTO" 	NVARCHAR2(200), 
"MODELO_ELEMENTO" 		NVARCHAR2(200), 
"LAST_EDITED_USER" 	NVARCHAR2(255), 
"LAST_EDITED_DATE" 	DATE, 
"COMENTARIOS" 		NVARCHAR2(255), 
"ESTADO_SINCRONIZACION" 		NVARCHAR2(255), 
"CAPA" 			NVARCHAR2(255),
CONSTRAINT "MIGRATELCOS_ELEMENTO_PK" PRIMARY KEY ("ID_MIGRATELCOS_ELEMENTO")
);

COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."ID_MIGRATELCOS_ELEMENTO" IS 'ID del número de registro';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."ID_ELEMENTO_ARGIS" IS 'ID de objeto según capa ArcGis';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."ELEMENTO_ID" IS 'ID de Telcos afectado en caso de sincronización exitosa';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."UBICACION_ID" IS 'ID del registro de ubicacion';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."DETALLE_ELEMENTO_ID" IS 'ID del registro de atributos';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."NOMBRE_ELEMENTO" IS 'Nombre del elemento, es el identificador entre ArcGis y Telcos';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."DESCRIPCION_ELEMENTO" IS 'Descripciones varias del elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."TIPO_ELEMENTO" IS 'Tipo de elemento a migrar';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."MODELO_ELEMENTO" IS 'Modelo específico del elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."LAST_EDITED_USER" IS 'Usuario que realiza la modificación';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."LAST_EDITED_DATE" IS 'Fecha en la que se realiza la modificación';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."COMENTARIOS" IS 'Comentarios sobre el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."ESTADO_SINCRONIZACION" IS 'Estado de la sincronización';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_ELEMENTO"."CAPA" IS 'Capa ArcGis o tipo de ruta a la que pertenece el elemento';
COMMENT ON TABLE "SDE"."MIGRATELCOS_ELEMENTO"  IS 'Tabla que sirve para almacenar los registros a sincronizar desde ArcGis hacia Telcos.';


CREATE TABLE "SDE"."MIGRATELCOS_DETALLE_ELEMENTO" 
(
"ID_DETALLE_ELEMENTO" 		NUMBER(38,0), 
"UBICADO_EN" 		NVARCHAR2(200), 
"NIVEL" 	NUMBER(6,0), 
"EDIFICACION" 		NVARCHAR2(200), 
"TIPO_LUGAR" 		NVARCHAR2(200),
"COSTO" NUMBER(10,2)
);

COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."ID_DETALLE_ELEMENTO" IS 'ID del número de registro';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."UBICADO_EN" IS 'Lugar físico donde se entuentra el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."NIVEL" IS 'Nivel del elemento en la red';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."EDIFICACION" IS 'Edificación donde se ubica el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."TIPO_LUGAR" IS 'Caracteristicas para indicar el lugar del Elemento, aereo, soterrado';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"."COSTO" IS 'Valor económico de un elemento';
COMMENT ON TABLE "SDE"."MIGRATELCOS_DETALLE_ELEMENTO"  IS 'Tabla que sirve para almacenar los detalles de registros a sincronizar desde ArcGis hacia Telcos.';


CREATE TABLE "SDE"."MIGRATELCOS_UBICACION" 
(
"ID_UBICACION" 	NUMBER(38,0), 
"PROVINCIA" 	NVARCHAR2(200), 
"CANTON" 	NVARCHAR2(200), 
"PARROQUIA" 	NVARCHAR2(200), 
"DIRECCION" 	NVARCHAR2(200),
"EMPRESA" NVARCHAR2(50),
"LATITUD" 	NUMBER(38,12), 
"GLAT" 		NUMBER(38,12), 
"MLAT" 		NUMBER(38,12), 
"SLAT" 		NUMBER(38,12), 
"LONGITUD" 	NUMBER(38,12), 
"GLON" 		NUMBER(38,12), 
"MLON" 		NUMBER(38,12), 
"SLON" 		NUMBER(38,12), 
"ALTURA" 	NUMBER(6,0), 
"SHAPE" 	"SDE"."ST_GEOMETRY", 
"MEGADATOS" NVARCHAR2(50), 
"TELCONET" NVARCHAR2(50)
);

COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."ID_UBICACION" IS 'ID del número de registro';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."PROVINCIA" IS 'Provincia en la que se ubica el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."CANTON" IS 'Cantón en el que se ubica el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."PARROQUIA" IS 'Parroquia en la que se ubica el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."DIRECCION" IS 'Dirección específica en la que se ubica el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."EMPRESA" IS 'Indica en que empresa fue creado el elemento';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."LATITUD" IS 'Latitud grados decimales';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."GLAT" IS 'Latitud, grados formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."MLAT" IS 'Latitud, minutos formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."SLAT" IS 'Latitud, segundos formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."LONGITUD" IS 'Longitud grados decimales';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."GLON" IS 'Longitud, grados formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."MLON" IS 'Longitud, minutos formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."SLON" IS 'Longitud, segundos formato GMS';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."ALTURA" IS 'Altura del elemento sobre el nivel del mar';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."SHAPE" IS 'Datos de ubicación en formato original ArcGis';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."MEGADATOS" IS 'Se utiliza para habilitar el elemento en la empresa Megadatos';
COMMENT ON COLUMN "SDE"."MIGRATELCOS_UBICACION"."TELCONET" IS 'Se utiliza para habilitar el elemento en la empresa Telconet';
COMMENT ON TABLE "SDE"."MIGRATELCOS_UBICACION"  IS 'Tabla que sirve para almacenar la ubicacion geográfica de los registros a sincronizar desde ArcGis hacia Telcos.';


--Creando la secuencia del ID_MIGRATELCOS_ELEMENTO
CREATE SEQUENCE  "SDE"."SEQ_MIGRATELCOS_ELEMENTO"  
MINVALUE 1 
MAXVALUE 999999999999999 
INCREMENT BY 1 
START WITH 1 
CACHE 20 NOORDER  NOCYCLE ;

--Creando la secuencia del ID_Migracion_Ubicacion
CREATE SEQUENCE  "SDE"."SEQ_MIGRATELCOS_UBICACION"  
MINVALUE 1 
MAXVALUE 999999999999999 
INCREMENT BY 1 
START WITH 1 
CACHE 20 NOORDER  NOCYCLE ;

--Creando la secuencia del ID_MIGRATELCOS_DETALLE_ELEMENTO
CREATE SEQUENCE  "SDE"."SEQ_MIGRATELCOS_DETALLE_E"  
MINVALUE 1 
MAXVALUE 999999999999999 
INCREMENT BY 1 
START WITH 1 
CACHE 20 NOORDER  NOCYCLE ;




--Creando trigger para sincronizar cajas eliminadas
CREATE OR REPLACE TRIGGER SDE.AFTER_INSERT_D475 AFTER INSERT ON D475 
REFERENCING OLD AS OLD NEW AS NEW FOR EACH ROW
DECLARE
Ln_ID               NUMBER (10);
Lv_CAPA             VARCHAR2(50);
Lv_NOMBRE_ELEMENTO  VARCHAR2(250);
Ln_IDARCGIS         NUMBER (10);
Ln_ESTADO           NUMBER (10);
Lr_ElementoMigrar   SDE.MIGRATELCOS_ELEMENTO%ROWTYPE;
BEGIN
  Ln_IDARCGIS:=:NEW.SDE_DELETES_ROW_ID;
  SELECT MAX(SDE_STATE_ID) INTO Ln_ESTADO
  FROM SDE.A475 
  WHERE OBJECTID=Ln_IDARCGIS;
  --
  IF :NEW.DELETED_AT > Ln_ESTADO THEN
    --
    Ln_ID:=SDE.SEQ_MIGRATELCOS_ELEMENTO.NEXTVAL;
    --
    SELECT NOMBRE_ELEMENTO, TIPO_RUTA INTO Lv_NOMBRE_ELEMENTO, Lv_CAPA
    FROM SDE.A475
    WHERE OBJECTID    =Ln_IDARCGIS
    AND   SDE_STATE_ID=Ln_ESTADO;
    --
    IF Lv_NOMBRE_ELEMENTO IS NOT NULL THEN
      Lr_ElementoMigrar.ID_MIGRATELCOS_ELEMENTO :=Ln_ID;
      Lr_ElementoMigrar.ID_ELEMENTO_ARGIS       :=Ln_IDARCGIS;
      Lr_ElementoMigrar.NOMBRE_ELEMENTO         :=Lv_NOMBRE_ELEMENTO;
      Lr_ElementoMigrar.TIPO_ELEMENTO           :='CAJA DISPERSION';
      Lr_ElementoMigrar.LAST_EDITED_USER        :=USER;
      Lr_ElementoMigrar.ESTADO_SINCRONIZACION   :='ELIMINAR';
      Lr_ElementoMigrar.LAST_EDITED_DATE        :=SYSDATE;
      Lr_ElementoMigrar.CAPA                    :=Lv_CAPA;
      INSERT INTO SDE.MIGRATELCOS_ELEMENTO VALUES Lr_ElementoMigrar;
      --
      UPDATE SDE.MIGRATELCOS_ELEMENTO
      SET ESTADO_SINCRONIZACION ='REEMPLAZADO CON ID_MIGRATELCOS_ELEMENTO: '||Ln_ID 
      WHERE ID_ELEMENTO_ARGIS   =Ln_IDARCGIS 
      AND CAPA                  =Lv_CAPA 
      AND ID_MIGRATELCOS_ELEMENTO <>Ln_ID 
      AND ESTADO_SINCRONIZACION      IN ('PENDIENTE','ELIMINAR');
    END IF;
  END IF;
END;
/
ALTER TRIGGER SDE.AFTER_INSERT_D475 ENABLE;




--Creando trigger para sincronizar cajas creadas y actualizadas
CREATE OR REPLACE TRIGGER SDE.AFTER_INSERT_UPDATE_A475 
AFTER INSERT OR UPDATE ON A475
REFERENCING OLD AS OLD NEW AS NEW FOR EACH ROW
-- 
DECLARE
Ln_LATITUD              NUMBER (38,12);
Ln_LONGITUD             NUMBER (38,12);
Ln_GLAT                 NUMBER (38,12); 
Ln_MLAT                 NUMBER (38,12);
Ln_SLAT                 NUMBER (38,12);
Ln_GLON                 NUMBER (38,12);
Ln_MLON                 NUMBER (38,12);
Ln_SLON                 NUMBER (38,12); 
Ln_ID                   NUMBER (10);
Ln_ID_UBICACION         NUMBER (10);
Ln_ID_DETALLE_ELEMENTO  NUMBER (10); 
Ln_NIVEL                NUMBER (10); 
Lv_TIPO_ELEMENTO        VARCHAR2(50);
Lr_ElementoMigrar SDE.MIGRATELCOS_ELEMENTO%ROWTYPE;
Lr_UbicacionMigrar SDE.MIGRATELCOS_UBICACION%ROWTYPE;
Lr_AtributosMigrar SDE.MIGRATELCOS_DETALLE_ELEMENTO%ROWTYPE;
BEGIN 
  Lv_TIPO_ELEMENTO:=:new.TIPO_ELEMENTO;   
  --Validar campos que son obligatorios en Telcos para que sea posible la sincronización desde ArcGis.
  IF  (UPPER(Lv_TIPO_ELEMENTO)  = 'CAJA' 
      OR UPPER(Lv_TIPO_ELEMENTO)= 'PEDESTAL')
      AND :new.NOMBRE_ELEMENTO  IS NOT NULL
      AND :new.PROVINCIA        IS NOT NULL 
      AND :new.CANTON           IS NOT NULL 
      AND :new.PARROQUIA        IS NOT NULL
      AND :new.ALTURA           IS NOT NULL
      AND :new.MODELO           IS NOT NULL
      AND :new.DIRECCION        IS NOT NULL
      AND :new.NIVEL            IS NOT NULL
      AND :new.TIPO_LUGAR       IS NOT NULL
      AND :new.UBICADO_EN       IS NOT NULL
      AND :new.TIPO_RUTA        IS NOT NULL
      AND (:new.MEGADATOS IS NOT NULL OR :new.TELCONET IS NOT NULL)
      THEN
      Ln_ID                 :=SDE.SEQ_MIGRATELCOS_ELEMENTO.NEXTVAL; 
      Ln_ID_UBICACION       :=SDE.SEQ_MIGRATELCOS_UBICACION.NEXTVAL;
      Ln_ID_DETALLE_ELEMENTO:=SDE.SEQ_MIGRATELCOS_DETALLE_E.NEXTVAL;
      -- 
      IF    :new.NIVEL='NIVEL 1' THEN Ln_NIVEL:=1;
      ELSIF :new.NIVEL='NIVEL 2' THEN Ln_NIVEL:=2;
      ELSE  Ln_NIVEL:=2;
      END IF;
      --
      Lr_ElementoMigrar.ID_MIGRATELCOS_ELEMENTO :=Ln_ID;
      Lr_ElementoMigrar.ID_ELEMENTO_ARGIS       :=:new.OBJECTID;
      Lr_ElementoMigrar.ELEMENTO_ID             :=NULL;
      Lr_ElementoMigrar.UBICACION_ID            :=Ln_ID_UBICACION;
      Lr_ElementoMigrar.DETALLE_ELEMENTO_ID     :=Ln_ID_DETALLE_ELEMENTO;
      Lr_ElementoMigrar.NOMBRE_ELEMENTO         :=:new.NOMBRE_ELEMENTO;
      Lr_ElementoMigrar.DESCRIPCION_ELEMENTO    :=:new.DESCRIPCION_ELEMENTO; 
      Lr_ElementoMigrar.TIPO_ELEMENTO           :='CAJA DISPERSION';
      Lr_ElementoMigrar.MODELO_ELEMENTO         :=:new.MODELO;
      Lr_ElementoMigrar.LAST_EDITED_USER        :=:new.LAST_EDITED_USER;
      Lr_ElementoMigrar.LAST_EDITED_DATE        :=:new.LAST_EDITED_DATE;
      Lr_ElementoMigrar.COMENTARIOS             :=:new.COMENTARIO;
      Lr_ElementoMigrar.ESTADO_SINCRONIZACION   :='PENDIENTE';
      Lr_ElementoMigrar.CAPA                    :=:new.TIPO_RUTA;
      INSERT INTO SDE.MIGRATELCOS_ELEMENTO VALUES Lr_ElementoMigrar;

      Lr_UbicacionMigrar.ID_UBICACION        :=Ln_ID_UBICACION;
      Lr_UbicacionMigrar.PROVINCIA           :=:new.PROVINCIA;
      Lr_UbicacionMigrar.CANTON              :=:new.CANTON;
      Lr_UbicacionMigrar.PARROQUIA           :=:new.PARROQUIA;
      Lr_UbicacionMigrar.DIRECCION           :=:new.DIRECCION;
      Lr_UbicacionMigrar.ALTURA              :=:new.ALTURA;
      Lr_UbicacionMigrar.SHAPE               :=:new.SHAPE;
      Lr_UbicacionMigrar.MEGADATOS           :=:new.MEGADATOS;
      Lr_UbicacionMigrar.TELCONET            :=:new.TELCONET;
      INSERT INTO SDE.MIGRATELCOS_UBICACION VALUES Lr_UbicacionMigrar;

      Lr_AtributosMigrar.ID_DETALLE_ELEMENTO  :=Ln_ID_DETALLE_ELEMENTO;
      Lr_AtributosMigrar.UBICADO_EN           :=:new.UBICADO_EN;
      Lr_AtributosMigrar.NIVEL                :=Ln_NIVEL;
      Lr_AtributosMigrar.EDIFICACION          :=:new.EDIFICACION;
      Lr_AtributosMigrar.TIPO_LUGAR           :=:new.TIPO_LUGAR;
      INSERT INTO SDE.MIGRATELCOS_DETALLE_ELEMENTO VALUES Lr_AtributosMigrar;

      --
      SELECT sde.st_geometry.st_minY(SHAPE), sde.st_geometry.st_minX(SHAPE) INTO Ln_LATITUD, Ln_LONGITUD 
      FROM SDE.MIGRATELCOS_UBICACION
      WHERE ID_UBICACION=Ln_ID_UBICACION;
      --
      SDE.ARPK_SINC_ARCGIS.P_UTMAGRADOS(Ln_LATITUD, Ln_LONGITUD, Ln_GLAT, Ln_MLAT, Ln_SLAT, Ln_GLON, Ln_MLON, Ln_SLON);
      -- 
      UPDATE SDE.MIGRATELCOS_UBICACION
        SET LATITUD =Ln_LATITUD,
          GLAT      =Ln_GLAT,
          MLAT      =Ln_MLAT,
          SLAT      =Ln_SLAT,
          LONGITUD  =Ln_LONGITUD,
          GLON      =Ln_GLON,
          MLON      =Ln_MLON,
          SLON      =Ln_SLON
      WHERE ID_UBICACION=Ln_ID_UBICACION;
      --
      UPDATE SDE.MIGRATELCOS_ELEMENTO
      SET ESTADO_SINCRONIZACION     ='REEMPLAZADO CON ID_MIGRATELCOS_ELEMENTO: '||Ln_ID 
      WHERE ID_ELEMENTO_ARGIS       =:new.OBJECTID 
        AND CAPA                    =:new.TIPO_RUTA 
        AND ID_MIGRATELCOS_ELEMENTO < Ln_ID 
        AND ESTADO_SINCRONIZACION   IN ('PENDIENTE','ELIMINAR');
      --
  END IF;
END;
/
ALTER TRIGGER SDE.AFTER_INSERT_UPDATE_A475 ENABLE;