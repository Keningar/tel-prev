CREATE OR REPLACE PACKAGE DB_COMERCIAL.CMKG_ING_MASIVO_CONTACTOS
AS
/**
  * Documentación para PROCEDURE 'P_ING_MASIVO_CONTACTOS'.
  * Procedure que me permite el ingreso masivo de Contactos asociados al Punto o Login
  * Se realiza inicialmente la validacion de la data , telefonos fijos, movil, correos, Titulo, Tipo_contacto, Login
  * Previamente insertada en la tabla de uso Temporal TEMP_CONTACTOS en base a excel enviado por Ventas(Comercial)
  * Solo si la Data completa de TEMP_CONTACTOS es valida se procede a realizar la migracion de Contactos por Punto.
  * Para realizar la subida de la Data se verifica por Login si existen Contactos por tipo de rol ('Contacto Comercial','Contacto Tecnico', etc)
  * si fuera el caso se procede a Eliminar los contactos existentes por Tipo_Rol e insertar los nuevos contactos existentes en TEMP_CONTACTOS 
  * Se registran las formas de Contactos Ligadas al Contacto, se crean Historiales respectivos.
  *
  * PARAMETROS:
  * @Param Lv_Mensaje OUT VARCHAR2 (Mensaje de ejecucion)
  * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
  * @version 1.0 23-05-2018
  */
PROCEDURE P_ING_MASIVO_CONTACTOS(Lv_Mensaje OUT VARCHAR2) ;
Lv_PrefijoEmpresa     CONSTANT VARCHAR2(2)  :='TN';
Lv_EstadoActivo       CONSTANT VARCHAR2(6)  :='Activo';
Lv_TipoRolCont        CONSTANT VARCHAR2(8)  :='Contacto';
Lv_TipoRolCli         CONSTANT VARCHAR2(7)  :='Cliente';
Lv_FormaCont          CONSTANT VARCHAR2(13) :='Telefono Fijo';
Lv_IpCreacion         CONSTANT VARCHAR2(15) :='127.0.0.1';
Lv_DescOficina        CONSTANT VARCHAR2(20) :='TELCONET - Guayaquil'; 
Lv_FormaContCorreo    CONSTANT VARCHAR2(20) :='Correo Electronico';
Lv_FormaContTelfInter CONSTANT VARCHAR2(30) :='Telefono Internacional';

END CMKG_ING_MASIVO_CONTACTOS;
/
CREATE OR REPLACE PACKAGE BODY DB_COMERCIAL.CMKG_ING_MASIVO_CONTACTOS AS
PROCEDURE P_ING_MASIVO_CONTACTOS(Lv_Mensaje OUT VARCHAR2) IS   
--
-- Costo Query: 13786
CURSOR C_ValidaInfoContactos IS
SELECT TMP.ID_CONTACTO,
  TMP.NOMBRES,
  TMP.APELLIDOS,
  TMP.TITULO,
  TMP.TIPO_CONTACTO,
  TMP.TELEFONO_FIJO,
  TMP.TELEFONO_MOVIL1,
  TMP.OPERADORA_MOVIL1,
  TMP.TELEFONO_MOVIL2,
  TMP.OPERADORA_MOVIL2,
  TMP.CORREO_ELECTRONICO,
  TMP.TELEFONO_INTERNACIONAL,
  TMP.LOGIN,      
  (CASE 
   WHEN TMP.TIPO_CONTACTO IS NOT NULL AND TIPO_CONTACTO.DESCRIPCION_ROL IS NULL
   THEN 'TIPO_CONTACTO Incorrecto: '|| TMP.TIPO_CONTACTO
   ELSE ' '
   END) AS OBS_TIPO_CONTACTO,
  (CASE 
   WHEN TMP.LOGIN IS NOT NULL AND  PUNTO.LOGIN IS NULL
   THEN 'LOGIN Incorrecto: '|| TMP.LOGIN
   ELSE ' '
   END) AS OBS_LOGIN,
  (CASE 
   WHEN TMP.TITULO IS NOT NULL AND TI.DESCRIPCION_TITULO IS NULL
   THEN 'TITULO Incorrecto: '|| TMP.TITULO
   ELSE ' '
   END) AS OBS_TITULO,
  (CASE 
   WHEN (TMP.OPERADORA_MOVIL1 IS NULL AND TMP.TELEFONO_MOVIL1 IS NOT NULL)
   THEN 'OPERADORA_MOVIL1 ES CAMPO OBLIGATORIO CUANDO REGISTRA TELEFONO_MOVIL1'
   WHEN TMP.OPERADORA_MOVIL1 IS NOT NULL AND AFC1.DESCRIPCION_FORMA_CONTACTO IS NULL
   THEN 'OPERADORA_MOVIL1 Incorrecto: '|| TMP.OPERADORA_MOVIL1
   ELSE ' '
   END) AS OBS_OPERADORA_MOVIL1,
   (CASE 
   WHEN (TMP.OPERADORA_MOVIL2 IS NULL AND TMP.TELEFONO_MOVIL2 IS NOT NULL)
   THEN 'OPERADORA_MOVIL2 ES CAMPO OBLIGATORIO CUANDO REGISTRA TELEFONO_MOVIL2'
   WHEN TMP.OPERADORA_MOVIL2 IS NOT NULL AND AFC2.DESCRIPCION_FORMA_CONTACTO IS NULL
   THEN 'OPERADORA_MOVIL2 Incorrecto: '|| TMP.OPERADORA_MOVIL2
   ELSE ' '
   END) AS OBS_OPERADORA_MOVIL2,
  (CASE 
   WHEN NOT REGEXP_LIKE (TRIM(TMP.CORREO_ELECTRONICO),  '^[A-Za-z0-9-]+[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$')
   THEN 'CORREO_ELECTRONICO Incorrecto: '|| TMP.CORREO_ELECTRONICO
   WHEN TMP.CORREO_ELECTRONICO IS NULL
   THEN 'CORREO ES CAMPO OBLIGATORIO'
   ELSE ' '
   END) AS OBS_CORREO_ELECTRONICO,  
   (CASE 
   WHEN NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_FIJO),  '^(0[2-8]{1}[0-9]{7})$')
   THEN 'TELEFONO_FIJO Incorrecto: '|| TMP.TELEFONO_FIJO
   ELSE ' '
   END) AS OBS_TELEFONO_FIJO,
   (CASE 
   WHEN  NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_MOVIL1),  '^(09[0-9]{8})$')
   THEN 'TELEFONO_MOVIL1 Incorrecto: '|| TMP.TELEFONO_MOVIL1
   ELSE ' '
   END) AS OBS_TELEFONO_MOVIL1,
   (CASE 
   WHEN (NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_MOVIL2),  '^(09[0-9]{8})$')) 
   THEN 'TELEFONO_MOVIL2 Incorrecto: '|| TMP.TELEFONO_MOVIL2
   ELSE ' '
   END) AS OBS_TELEFONO_MOVIL2,
   (CASE 
   WHEN  NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_INTERNACIONAL),  '^([0-9]{7,15})$')
   THEN 'TELEFONO_INTERNACIONAL Incorrecto: '|| TMP.TELEFONO_INTERNACIONAL
   ELSE ' '
   END) AS OBS_TELEFONO_INTERNACIONAL
   
FROM DB_COMERCIAL.TEMP_CONTACTOS TMP
LEFT JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC1 ON UPPER(AFC1.DESCRIPCION_FORMA_CONTACTO) = UPPER(TRIM(TMP.OPERADORA_MOVIL1)) 
AND  AFC1.ESTADO = Lv_EstadoActivo
LEFT JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC2 ON UPPER(AFC2.DESCRIPCION_FORMA_CONTACTO) = UPPER(TRIM(TMP.OPERADORA_MOVIL2))
AND  AFC2.ESTADO = Lv_EstadoActivo
LEFT JOIN DB_COMERCIAL.ADMI_TITULO TI ON UPPER(TI.DESCRIPCION_TITULO) = UPPER(TRIM(TMP.TITULO))
AND  TI.ESTADO  = Lv_EstadoActivo
LEFT JOIN (SELECT PTO.LOGIN 
  FROM DB_COMERCIAL.INFO_PUNTO PTO,
  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PEMPROL,
  DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
  DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
  DB_GENERAL.ADMI_ROL ROL,
  DB_GENERAL.ADMI_TIPO_ROL TROL
  WHERE 
  PTO.PERSONA_EMPRESA_ROL_ID     = PEMPROL.ID_PERSONA_ROL
  AND PEMPROL.EMPRESA_ROL_ID     = EMPROL.ID_EMPRESA_ROL
  AND EMPROL.EMPRESA_COD         = EMPGR.COD_EMPRESA
  AND EMPGR.PREFIJO              = Lv_PrefijoEmpresa
  AND EMPROL.ROL_ID              = ROL.ID_ROL
  AND ROL.TIPO_ROL_ID            = TROL.ID_TIPO_ROL
  AND TROL.DESCRIPCION_TIPO_ROL  = Lv_TipoRolCli
  ) PUNTO ON UPPER(PUNTO.LOGIN)  = UPPER(TRIM(TMP.LOGIN))
LEFT JOIN (SELECT ROL.DESCRIPCION_ROL 
  FROM DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
  DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
  DB_GENERAL.ADMI_ROL ROL,
  DB_GENERAL.ADMI_TIPO_ROL TROL
  WHERE  
  EMPROL.EMPRESA_COD             = EMPGR.COD_EMPRESA
  AND EMPGR.PREFIJO              = Lv_PrefijoEmpresa
  AND EMPROL.ROL_ID              = ROL.ID_ROL
  AND ROL.TIPO_ROL_ID            = TROL.ID_TIPO_ROL
  AND TROL.DESCRIPCION_TIPO_ROL  = Lv_TipoRolCont
  AND EMPROL.ESTADO              = Lv_EstadoActivo
  ) TIPO_CONTACTO ON  UPPER(TIPO_CONTACTO.DESCRIPCION_ROL) LIKE '%'||UPPER(TRIM(TMP.TIPO_CONTACTO))||'%'
WHERE
(NOT REGEXP_LIKE (TRIM(TMP.CORREO_ELECTRONICO), '^[A-Za-z0-9-]+[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$') OR TMP.CORREO_ELECTRONICO IS NULL)
OR NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_FIJO),  '^(0[2-8]{1}[0-9]{7})$')
OR NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_MOVIL1),  '^(09[0-9]{8})$')
OR NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_MOVIL2),  '^(09[0-9]{8})$')
OR NOT REGEXP_LIKE (TRIM(TMP.TELEFONO_INTERNACIONAL),  '^([0-9]{7,15})$')
OR (TMP.TIPO_CONTACTO IS NOT NULL AND TIPO_CONTACTO.DESCRIPCION_ROL IS NULL)
OR (TMP.LOGIN IS NOT NULL AND  PUNTO.LOGIN IS NULL)
OR (TMP.TITULO IS NOT NULL AND TI.DESCRIPCION_TITULO IS NULL)
OR (TMP.OPERADORA_MOVIL1 IS NOT NULL AND AFC1.DESCRIPCION_FORMA_CONTACTO IS NULL)
OR (TMP.OPERADORA_MOVIL2 IS NOT NULL AND AFC2.DESCRIPCION_FORMA_CONTACTO IS NULL)
OR (TMP.OPERADORA_MOVIL1 IS NULL AND TMP.TELEFONO_MOVIL1 IS NOT NULL)
OR (TMP.OPERADORA_MOVIL2 IS NULL AND TMP.TELEFONO_MOVIL2 IS NOT NULL);
--
--Costo Query: 17
CURSOR C_ListLoginesPorTipoContacto IS
SELECT PTO.ID_PUNTO,
PTO.LOGIN,
PEMPROL.ID_PERSONA_ROL,
PEMPROL.PERSONA_ID,
TMP.TIPO_CONTACTO  
FROM DB_COMERCIAL.TEMP_CONTACTOS TMP,
  DB_COMERCIAL.INFO_PUNTO PTO,
  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PEMPROL,
  DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
  DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
  DB_GENERAL.ADMI_ROL ROL,
  DB_GENERAL.ADMI_TIPO_ROL TROL
WHERE TMP.LOGIN                = PTO.LOGIN
AND PTO.PERSONA_EMPRESA_ROL_ID = PEMPROL.ID_PERSONA_ROL
AND PEMPROL.EMPRESA_ROL_ID     = EMPROL.ID_EMPRESA_ROL
AND EMPROL.EMPRESA_COD         = EMPGR.COD_EMPRESA
AND EMPGR.PREFIJO              = Lv_PrefijoEmpresa
AND EMPROL.ROL_ID              = ROL.ID_ROL
AND ROL.TIPO_ROL_ID            = TROL.ID_TIPO_ROL
AND TROL.DESCRIPCION_TIPO_ROL  = Lv_TipoRolCli
GROUP BY PTO.ID_PUNTO,PTO.LOGIN,PEMPROL.ID_PERSONA_ROL,
PEMPROL.PERSONA_ID,TMP.TIPO_CONTACTO  
ORDER BY PTO.ID_PUNTO,PTO.LOGIN,PEMPROL.ID_PERSONA_ROL,
PEMPROL.PERSONA_ID,TMP.TIPO_CONTACTO;
--
--Costo Query: 13
CURSOR C_ListMasivoPorPuntoYTipo(Cn_IdPunto DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
       Cv_DescripcionRol DB_GENERAL.ADMI_ROL.DESCRIPCION_ROL%TYPE )
IS    
SELECT PTO.ID_PUNTO,PTO.LOGIN AS LOGIN_PTO,
  PEMPROL.ID_PERSONA_ROL,
  PEMPROL.PERSONA_ID,
  TMP.NOMBRES,
  TMP.APELLIDOS,
  TMP.TITULO,
  TMP.TIPO_CONTACTO,
  TMP.TELEFONO_FIJO,
  TMP.TELEFONO_MOVIL1,
  TMP.OPERADORA_MOVIL1,
  TMP.TELEFONO_MOVIL2,
  TMP.OPERADORA_MOVIL2,
  TMP.CORREO_ELECTRONICO,
  TMP.LOGIN,
  TMP.OBSERVACION,
  TMP.TELEFONO_INTERNACIONAL,
  TMP.ID_CONTACTO
FROM DB_COMERCIAL.TEMP_CONTACTOS TMP,
  DB_COMERCIAL.INFO_PUNTO PTO,
  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PEMPROL,
  DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
  DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
  DB_GENERAL.ADMI_ROL ROL,
  DB_GENERAL.ADMI_TIPO_ROL TROL
WHERE TMP.LOGIN                = PTO.LOGIN
AND PTO.ID_PUNTO               = Cn_IdPunto
AND UPPER(TMP.TIPO_CONTACTO) LIKE  '%'||UPPER(Cv_DescripcionRol)||'%' --'Contacto Comercial','Contacto Tecnico', etc
AND PTO.PERSONA_EMPRESA_ROL_ID = PEMPROL.ID_PERSONA_ROL
AND PEMPROL.EMPRESA_ROL_ID     = EMPROL.ID_EMPRESA_ROL
AND EMPROL.EMPRESA_COD         = EMPGR.COD_EMPRESA
AND EMPGR.PREFIJO              = Lv_PrefijoEmpresa
AND EMPROL.ROL_ID              = ROL.ID_ROL
AND ROL.TIPO_ROL_ID            = TROL.ID_TIPO_ROL
AND TROL.DESCRIPCION_TIPO_ROL  = Lv_TipoRolCli
AND EMPROL.ESTADO              = Lv_EstadoActivo
ORDER BY PTO.ID_PUNTO,PTO.LOGIN, TMP.TIPO_CONTACTO ASC;
--
--Costo Query :17
CURSOR  C_ContactosPorPuntoYRol(Cn_IdPunto DB_COMERCIAL.INFO_PUNTO.ID_PUNTO%TYPE,
        Cv_DescripcionRol DB_GENERAL.ADMI_ROL.DESCRIPCION_ROL%TYPE )
IS    
SELECT PTO.ID_PUNTO,
  PTO.LOGIN,
  PERS.ID_PERSONA,
  PERS.NOMBRES,
  PERS.APELLIDOS,
  PERS.IDENTIFICACION_CLIENTE,
  PERS.RAZON_SOCIAL,
  PEMPROL.ID_PERSONA_ROL,
  PTOC.ID_PUNTO_CONTACTO,
  PEMPROL.ESTADO AS ESTADO_PERROL,
  PTOC.ESTADO    AS ESTADO_PTOC,
  EMPGR.COD_EMPRESA,
  ROL.DESCRIPCION_ROL
FROM DB_COMERCIAL.INFO_PERSONA PERS,
  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PEMPROL,
  DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
  DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
  DB_GENERAL.ADMI_ROL ROL,
  DB_GENERAL.ADMI_TIPO_ROL TROL,
  DB_COMERCIAL.INFO_PUNTO_CONTACTO PTOC,
  DB_COMERCIAL.INFO_PUNTO PTO
WHERE 
UPPER(ROL.DESCRIPCION_ROL) LIKE  '%'||UPPER(Cv_DescripcionRol)||'%' --'Contacto Comercial','Contacto Tecnico', etc
AND PERS.ID_PERSONA        = PEMPROL.PERSONA_ID
AND PEMPROL.EMPRESA_ROL_ID = EMPROL.ID_EMPRESA_ROL
AND EMPROL.EMPRESA_COD     = EMPGR.COD_EMPRESA
AND EMPGR.PREFIJO          = Lv_PrefijoEmpresa
AND EMPROL.ROL_ID          = ROL.ID_ROL
AND EMPROL.ESTADO          = Lv_EstadoActivo
AND ROL.TIPO_ROL_ID        = TROL.ID_TIPO_ROL
AND PTOC.CONTACTO_ID       = PERS.ID_PERSONA
AND PEMPROL.ID_PERSONA_ROL = PTOC.PERSONA_EMPRESA_ROL_ID
AND PTOC.PUNTO_ID          = PTO.ID_PUNTO
AND PTOC.ESTADO            = Lv_EstadoActivo
AND PTO.ID_PUNTO           = Cn_IdPunto
GROUP BY (PTO.ID_PUNTO,PTO.LOGIN,PERS.ID_PERSONA,PERS.NOMBRES,PERS.APELLIDOS,PERS.IDENTIFICACION_CLIENTE,
PERS.RAZON_SOCIAL, PEMPROL.ID_PERSONA_ROL,PTOC.ID_PUNTO_CONTACTO,PEMPROL.ESTADO,PTOC.ESTADO, EMPGR.COD_EMPRESA,ROL.DESCRIPCION_ROL) ;
--
--Costo Query:3 
CURSOR C_ObtieneTitulo (Cv_DescripcionTitulo DB_COMERCIAL.ADMI_TITULO.DESCRIPCION_TITULO%TYPE) 
IS
SELECT * FROM DB_COMERCIAL.ADMI_TITULO
WHERE UPPER(DESCRIPCION_TITULO) = UPPER(Cv_DescripcionTitulo) 
AND ESTADO                      = Lv_EstadoActivo
AND ROWNUM                      = 1
;
Lr_ObtieneTitulo C_ObtieneTitulo%ROWTYPE;--
--Costo Query: 9
--Cursor para verificar si el rol para la empresa especifica existe registrado
CURSOR C_ExisteRolEmpresa(Cv_DescripcionRol DB_GENERAL.ADMI_ROL.DESCRIPCION_ROL%TYPE,
       Cv_CodEmpresa DB_COMERCIAL.INFO_EMPRESA_GRUPO.COD_EMPRESA%TYPE) 
IS       
SELECT EMPROL.ID_EMPRESA_ROL,
EMPGRUP.COD_EMPRESA,
ROL.DESCRIPCION_ROL,
TROL.DESCRIPCION_TIPO_ROL
FROM  DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGRUP,
DB_GENERAL.ADMI_ROL ROL,
DB_GENERAL.ADMI_TIPO_ROL TROL       
WHERE 
EMPROL.EMPRESA_COD             = EMPGRUP.COD_EMPRESA 
AND EMPGRUP.COD_EMPRESA        = Cv_CodEmpresa
AND EMPROL.ROL_ID              = rol.ID_ROL 
AND UPPER(ROL.DESCRIPCION_ROL) LIKE  '%'||UPPER(Cv_DescripcionRol)||'%' --'Contacto Comercial','Contacto Tecnico', etc
AND ROL.TIPO_ROL_ID            = TROL.ID_TIPO_ROL
AND TROL.DESCRIPCION_TIPO_ROL  = Lv_TipoRolCont
AND EMPROL.ESTADO              = Lv_EstadoActivo;
 
Lr_ExisteRolEmpresa C_ExisteRolEmpresa%ROWTYPE;
--
--Costor Query: 3
CURSOR C_FormaContacto(Cv_DescripcionFormaContacto DB_COMERCIAL.ADMI_FORMA_CONTACTO.DESCRIPCION_FORMA_CONTACTO%TYPE)
IS
SELECT * FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO 
WHERE UPPER(DESCRIPCION_FORMA_CONTACTO) = UPPER(Cv_DescripcionFormaContacto)
AND ESTADO = Lv_EstadoActivo;

Lr_FormaContacto C_FormaContacto%ROWTYPE;
--
Ln_IdPersona              DB_COMERCIAL.INFO_PERSONA.ID_PERSONA%TYPE;  
Ln_IdPersonaEmpresaRol    DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL.ID_PERSONA_ROL%TYPE;  
Ln_IdPersonaEmpresaRolHis DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO.ID_PERSONA_EMPRESA_ROL_HISTO%TYPE;
Ln_IdPuntoContacto        DB_COMERCIAL.INFO_PUNTO_CONTACTO.ID_PUNTO_CONTACTO%TYPE;  
Ln_IdPersonaFormaCont     DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO.ID_PERSONA_FORMA_CONTACTO%TYPE;
Ln_IdTitulo               NUMBER;
Ln_IdEmpresaRolCont       NUMBER;
Ln_IdFormaContacto        DB_COMERCIAL.ADMI_FORMA_CONTACTO.ID_FORMA_CONTACTO%TYPE; 
Lv_Observacion            DB_COMERCIAL.TEMP_CONTACTOS.OBSERVACION%TYPE:=''; 
Lb_Error                  BOOLEAN:=TRUE;     
Lb_SubidaMasiva           BOOLEAN:=TRUE;
Lv_UsrCreacion            DB_COMERCIAL.INFO_PERSONA.USR_CREACION%TYPE:='masivoContactos';
Lv_CantidadMigrado        NUMBER:=0;
--
BEGIN

--Valido la informacion del Archivo en la tabla TEMP_CONTACTOS, telefonos, correos, titulo y Rol de Contacto, de existir algun datos erroneo
--no se procede a migrar la información, se debera guardar mensaje de error en el campo observacion.
FOR Lr_ValidaInfoContactos IN C_ValidaInfoContactos LOOP
  Lb_Error:=TRUE; 
  Lv_Observacion:='';
  IF Lr_ValidaInfoContactos.OBS_TIPO_CONTACTO IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TIPO_CONTACTO!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TIPO_CONTACTO ;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_LOGIN IS NOT NULL AND Lr_ValidaInfoContactos.OBS_LOGIN!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_LOGIN;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_TITULO IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TITULO!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TITULO;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL1 IS NOT NULL AND Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL1!=' '  THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL1;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL2 IS NOT NULL AND Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL2!=' '  THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_OPERADORA_MOVIL2;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_CORREO_ELECTRONICO IS NOT NULL AND Lr_ValidaInfoContactos.OBS_CORREO_ELECTRONICO!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_CORREO_ELECTRONICO;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_TELEFONO_FIJO IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TELEFONO_FIJO!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TELEFONO_FIJO;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL1 IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL1!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL1;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL2 IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL2!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TELEFONO_MOVIL2;
     Lb_Error:= FALSE;
  END IF;
  IF Lr_ValidaInfoContactos.OBS_TELEFONO_INTERNACIONAL IS NOT NULL AND Lr_ValidaInfoContactos.OBS_TELEFONO_INTERNACIONAL!=' ' THEN
     Lv_Observacion:=  Lv_Observacion || ' | ';
     Lv_Observacion:=  Lv_Observacion || Lr_ValidaInfoContactos.OBS_TELEFONO_INTERNACIONAL;
     Lb_Error:= FALSE;
  END IF;

  --Si existe error inserto Observacion
  IF Lb_Error = FALSE THEN
     UPDATE DB_COMERCIAL.TEMP_CONTACTOS SET OBSERVACION=Lv_Observacion
     WHERE ID_CONTACTO=Lr_ValidaInfoContactos.ID_CONTACTO;
     IF Lb_SubidaMasiva = TRUE THEN
       Lb_SubidaMasiva:=FALSE;
     END IF;
  END IF;
END LOOP;

IF Lb_SubidaMasiva = TRUE THEN  
  --
  FOR Lr_ListLoginesPorTipoContacto IN C_ListLoginesPorTipoContacto LOOP
    --
    --Elimino los Contactos por Punto y por TIPO_CONTACTO o ROL sea 'Contacto Comercial','Contacto Tecnico', etc en estado Activo
    FOR Lr_ContactosPorPuntoYRol IN C_ContactosPorPuntoYRol
    (Lr_ListLoginesPorTipoContacto.ID_PUNTO, Lr_ListLoginesPorTipoContacto.TIPO_CONTACTO) LOOP
    --
       UPDATE DB_COMERCIAL.INFO_PUNTO_CONTACTO 
       SET ESTADO='Eliminado' 
       WHERE ID_PUNTO_CONTACTO=Lr_ContactosPorPuntoYRol.ID_PUNTO_CONTACTO;
       --
       UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL
       SET ESTADO='Eliminado' 
       WHERE ID_PERSONA_ROL=Lr_ContactosPorPuntoYRol.ID_PERSONA_ROL;
       --
       UPDATE DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO 
       SET ESTADO='Eliminado' 
       WHERE PERSONA_ID=Lr_ContactosPorPuntoYRol.ID_PERSONA;
       --
       --Inserto Historial en el Punto indicando que el Contacto fue Eliminado 
      INSERT INTO DB_COMERCIAL.INFO_PUNTO_HISTORIAL
      (ID_PUNTO_HISTORIAL,
      PUNTO_ID,
      VALOR,
      ACCION,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION) 
      VALUES
      (DB_COMERCIAL.SEQ_INFO_PUNTO_HISTORIAL.NEXTVAL,
      Lr_ListLoginesPorTipoContacto.ID_PUNTO,
      'CONTACTO: '|| Lr_ContactosPorPuntoYRol.NOMBRES || ' ' || Lr_ContactosPorPuntoYRol.APELLIDOS || 
      ' TIPO: ' || Lr_ContactosPorPuntoYRol.DESCRIPCION_ROL ||
      ' (ESTADO PUNTO CONTACTO) ANTERIOR:' || Lr_ContactosPorPuntoYRol.ESTADO_PTOC || ' - ACTUAL:Eliminado',
      'Migracion Contactos',
      Lv_UsrCreacion,
      SYSDATE,
      Lv_IpCreacion);
    --    
    END LOOP;
    --
    --Inserto los Contactos por Punto y por Tipo de Contacto
    FOR Lr_ListMasivoPorPuntoYTipo IN C_ListMasivoPorPuntoYTipo
    (Lr_ListLoginesPorTipoContacto.ID_PUNTO, Lr_ListLoginesPorTipoContacto.TIPO_CONTACTO)
    LOOP
      --
      Ln_IdTitulo:=0;
      Ln_IdEmpresaRolCont :=0;
      --
      --Obtengo Titulo del Contacto    
      OPEN C_ObtieneTitulo(Lr_ListMasivoPorPuntoYTipo.TITULO);
      FETCH C_ObtieneTitulo INTO Lr_ObtieneTitulo;
      IF(C_ObtieneTitulo%FOUND) THEN
        --   
        Ln_IdTitulo := Lr_ObtieneTitulo.ID_TITULO;
        --
      END IF;
      CLOSE C_ObtieneTitulo;
      --
      --Obtengo Rol por Empresa por Tipo de Contacto    
      --
      OPEN C_ExisteRolEmpresa(Lr_ListMasivoPorPuntoYTipo.TIPO_CONTACTO,'10');
      FETCH C_ExisteRolEmpresa INTO Lr_ExisteRolEmpresa;    
      IF(C_ExisteRolEmpresa%FOUND) THEN                  
        --
        Ln_IdEmpresaRolCont := lr_ExisteRolEmpresa.ID_EMPRESA_ROL;
        --   
      END IF;
      CLOSE C_ExisteRolEmpresa;
    
      --SI ES VALIDO EL TITULO Y ENCUENTRO EL ROL, SE INSERTA PERSONA Y ROL DE CONTACTO
      IF (Ln_IdTitulo!=0 AND Ln_IdEmpresaRolCont!=0) THEN
        --
        --Inserto informacion de la persona que es Contacto 
        --
        Ln_IdPersona := DB_COMERCIAL.SEQ_INFO_PERSONA.NEXTVAL;
        --INSERTANDO PERSONA
        INSERT INTO DB_COMERCIAL.INFO_PERSONA
        (ID_PERSONA,TITULO_ID,
         ORIGEN_PROSPECTO,TIPO_IDENTIFICACION,
         IDENTIFICACION_CLIENTE,TIPO_EMPRESA,
         TIPO_TRIBUTARIO,NOMBRES,APELLIDOS,
         RAZON_SOCIAL,REPRESENTANTE_LEGAL,
         NACIONALIDAD,DIRECCION,
         LOGIN,CARGO,
         DIRECCION_TRIBUTARIA,CONTRIBUYENTE_ESPECIAL,
         PAGA_IVA,GENERO,
         ESTADO,FE_CREACION,
         USR_CREACION,IP_CREACION,
         ESTADO_CIVIL,FECHA_NACIMIENTO,
         CALIFICACION_CREDITICIA,
         ORIGEN_INGRESOS,ORIGEN_WEB,
         NUMERO_CONADIS
        )
        VALUES
        (Ln_IdPersona,Ln_IdTitulo,
        'N',NULL,NULL,
         NULL,NULL,
         TRIM(Lr_ListMasivoPorPuntoYTipo.NOMBRES),
         TRIM(Lr_ListMasivoPorPuntoYTipo.APELLIDOS),
         NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
         NULL,Lv_EstadoActivo,SYSDATE,
         Lv_UsrCreacion,
         Lv_IpCreacion,NULL,
         NULL,NULL,NULL,
         'S',NULL
         );
        --
        --Inserto Persona con Rol de Contacto para empresa TN
        Ln_IdPersonaEmpresaRol := DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL.NEXTVAL;
        INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL
        (ID_PERSONA_ROL,
         PERSONA_ID,
         EMPRESA_ROL_ID,
         OFICINA_ID,
         DEPARTAMENTO_ID,
         ESTADO,
         USR_CREACION,
         FE_CREACION,
         IP_CREACION,
         CUADRILLA_ID,
         PERSONA_EMPRESA_ROL_ID,
         PERSONA_EMPRESA_ROL_ID_TTCO)
         VALUES 
         (Ln_IdPersonaEmpresaRol,
          Ln_IdPersona,
          Ln_IdEmpresaRolCont,
          (SELECT ID_OFICINA  
           FROM DB_COMERCIAL.INFO_OFICINA_GRUPO OFI,
           INFO_EMPRESA_GRUPO EMP
           WHERE 
           OFI.EMPRESA_ID         = EMP.COD_EMPRESA
           AND EMP.PREFIJO        = Lv_PrefijoEmpresa
           AND OFI.NOMBRE_OFICINA = LV_DescOficina
           AND OFI.ESTADO         = Lv_EstadoActivo),--por default TELCONET - Guayaquil
           NULL,
          Lv_EstadoActivo,
          Lv_UsrCreacion,SYSDATE,Lv_IpCreacion,
          NULL,NULL,NULL);

        --Inserto Historial para el contacto        
        Ln_IdPersonaEmpresaRolHis := DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL_H.NEXTVAL;
        INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO
        (ID_PERSONA_EMPRESA_ROL_HISTO,
        USR_CREACION,FE_CREACION,
        IP_CREACION,
        ESTADO,
        PERSONA_EMPRESA_ROL_ID)
        VALUES
        (Ln_IdPersonaEmpresaRolHis,
         Lv_UsrCreacion,
         sysdate,
         Lv_IpCreacion,
         Lv_EstadoActivo,
         Ln_IdPersonaEmpresaRol);
        --      
        --Inserto Relacion entre la Persona Rol Contacto y el Punto Cliente 
        Ln_IdPuntoContacto := DB_COMERCIAL.SEQ_INFO_PUNTO_CONTACTO.NEXTVAL;
        INSERT INTO DB_COMERCIAL.INFO_PUNTO_CONTACTO
        (ID_PUNTO_CONTACTO,
         CONTACTO_ID,
         ESTADO,
         FE_CREACION,
         USR_CREACION,
         IP_CREACION,
         PUNTO_ID,
         PERSONA_EMPRESA_ROL_ID)
        VALUES
        (Ln_IdPuntoContacto,
         Ln_IdPersona,
         Lv_EstadoActivo,
         SYSDATE,
         Lv_UsrCreacion,
         Lv_IpCreacion,
         Lr_ListMasivoPorPuntoYTipo.ID_PUNTO,
         Ln_IdPersonaEmpresaRol);
        
        IF Lr_ListMasivoPorPuntoYTipo.TELEFONO_FIJO IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.TELEFONO_FIJO!=' ' THEN
          --
          --Inserto Telefono Fijo : 4
          Ln_IdPersonaFormaCont := DB_COMERCIAL.SEQ_INFO_PERSONA_FORMA_CONT.NEXTVAL;
       
          INSERT INTO DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO
          (ID_PERSONA_FORMA_CONTACTO,
          PERSONA_ID,
          FORMA_CONTACTO_ID,
          VALOR,
          ESTADO,
          FE_CREACION,
          FE_ULT_MOD,
          USR_CREACION,
          USR_ULT_MOD,
          IP_CREACION)
          VALUES
          (Ln_IdPersonaFormaCont,
           Ln_IdPersona,
          (
            SELECT ID_FORMA_CONTACTO 
            FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO
            WHERE DESCRIPCION_FORMA_CONTACTO=Lv_FormaCont
            AND ESTADO=Lv_EstadoActivo
           ),--Telefono Fijo : 4
           TRIM(Lr_ListMasivoPorPuntoYTipo.TELEFONO_FIJO),
           Lv_EstadoActivo,
           sysdate,
           NULL,
           Lv_UsrCreacion,
           NULL,
           Lv_IpCreacion);
        --
        END IF;
        
        IF Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL1 IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL1!=' ' THEN
          --
          --Inserto TELEFONO_MOVIL1 y busco Operadora a la que pertenece
          Ln_IdFormaContacto:= NULL;
          OPEN C_FormaContacto(TRIM(Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL1));
          FETCH C_FormaContacto INTO Lr_FormaContacto;
          IF(C_FormaContacto%FOUND) THEN
            --   
            Ln_IdFormaContacto := Lr_FormaContacto.ID_FORMA_CONTACTO;
           --
          END IF;
          CLOSE C_FormaContacto;
          --
          IF Ln_IdFormaContacto IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.TELEFONO_MOVIL1!=' ' THEN
            --
            Ln_IdPersonaFormaCont := DB_COMERCIAL.SEQ_INFO_PERSONA_FORMA_CONT.NEXTVAL;
       
            INSERT INTO DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO
            (ID_PERSONA_FORMA_CONTACTO,
             PERSONA_ID,
             FORMA_CONTACTO_ID,
             VALOR,
             ESTADO,
             FE_CREACION,
             FE_ULT_MOD,
             USR_CREACION,
             USR_ULT_MOD,
             IP_CREACION)
            VALUES
            (Ln_IdPersonaFormaCont,
             Ln_IdPersona,
             Ln_IdFormaContacto,
             TRIM(Lr_ListMasivoPorPuntoYTipo.TELEFONO_MOVIL1),
             Lv_EstadoActivo,
             sysdate,
             NULL,
             Lv_UsrCreacion,
             NULL,
             Lv_IpCreacion);
            --
          END IF;
        --
        END IF;
        
        IF Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL2 IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL2!=' ' THEN
          --
          --Inserto TELEFONO_MOVIL2 y busco Operadora a la que pertenece
          Ln_IdFormaContacto:= NULL;
          OPEN C_FormaContacto(TRIM(Lr_ListMasivoPorPuntoYTipo.OPERADORA_MOVIL2));
          FETCH C_FormaContacto INTO Lr_FormaContacto;
          IF(C_FormaContacto%FOUND) THEN
            --   
            Ln_IdFormaContacto := Lr_FormaContacto.ID_FORMA_CONTACTO;
            --
          END IF;
          CLOSE C_FormaContacto;
      
          IF Ln_IdFormaContacto IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.TELEFONO_MOVIL2!=' ' THEN
            --
            Ln_IdPersonaFormaCont := DB_COMERCIAL.SEQ_INFO_PERSONA_FORMA_CONT.NEXTVAL;
       
            INSERT INTO DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO
            (ID_PERSONA_FORMA_CONTACTO,
             PERSONA_ID,
             FORMA_CONTACTO_ID,
             VALOR,
             ESTADO,
             FE_CREACION,
             FE_ULT_MOD,
             USR_CREACION,
             USR_ULT_MOD,
             IP_CREACION)
            VALUES
            (Ln_IdPersonaFormaCont,
             Ln_IdPersona,
             Ln_IdFormaContacto,
             TRIM(Lr_ListMasivoPorPuntoYTipo.TELEFONO_MOVIL2),
             Lv_EstadoActivo,
             sysdate,
             NULL,
             Lv_UsrCreacion,
             NULL,
             Lv_IpCreacion);
            --
          END IF;
          --
        END IF; 
        
        IF Lr_ListMasivoPorPuntoYTipo.CORREO_ELECTRONICO IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.CORREO_ELECTRONICO!=' ' THEN 
          --
          --Inserto CORREO_ELECTRONICO : 5
          Ln_IdPersonaFormaCont := DB_COMERCIAL.SEQ_INFO_PERSONA_FORMA_CONT.NEXTVAL;
       
          INSERT INTO DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO
          (ID_PERSONA_FORMA_CONTACTO,
           PERSONA_ID,
           FORMA_CONTACTO_ID,
           VALOR,
           ESTADO,
           FE_CREACION,
           FE_ULT_MOD,
           USR_CREACION,
           USR_ULT_MOD,
           IP_CREACION)
          VALUES
          (Ln_IdPersonaFormaCont,
           Ln_IdPersona,
           (
            SELECT ID_FORMA_CONTACTO 
            FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO
            WHERE DESCRIPCION_FORMA_CONTACTO=Lv_FormaContCorreo
            AND ESTADO=Lv_EstadoActivo
           ),
           TRIM(Lr_ListMasivoPorPuntoYTipo.CORREO_ELECTRONICO),
           Lv_EstadoActivo,
           sysdate,
           NULL,
           Lv_UsrCreacion,
           NULL,
           Lv_IpCreacion); 
          --  
        END IF;

        IF Lr_ListMasivoPorPuntoYTipo.TELEFONO_INTERNACIONAL IS NOT NULL AND Lr_ListMasivoPorPuntoYTipo.TELEFONO_INTERNACIONAL!=' ' THEN
          --
          --Inserto Telefono Internacional : 212
          Ln_IdPersonaFormaCont := DB_COMERCIAL.SEQ_INFO_PERSONA_FORMA_CONT.NEXTVAL;
       
          INSERT INTO DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO
          (ID_PERSONA_FORMA_CONTACTO,
          PERSONA_ID,
          FORMA_CONTACTO_ID,
          VALOR,
          ESTADO,
          FE_CREACION,
          FE_ULT_MOD,
          USR_CREACION,
          USR_ULT_MOD,
          IP_CREACION)
          VALUES
          (Ln_IdPersonaFormaCont,
           Ln_IdPersona,
          (
            SELECT ID_FORMA_CONTACTO 
            FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO
            WHERE DESCRIPCION_FORMA_CONTACTO=Lv_FormaContTelfInter
            AND ESTADO=Lv_EstadoActivo
           ),--Telefono internacional : 212
           TRIM(Lr_ListMasivoPorPuntoYTipo.TELEFONO_INTERNACIONAL),
           Lv_EstadoActivo,
           sysdate,
           NULL,
           Lv_UsrCreacion,
           NULL,
           Lv_IpCreacion);
        --
        END IF;
        --
        --Actualizo Observacion de migracion de contacto con exito.
        UPDATE DB_COMERCIAL.TEMP_CONTACTOS SET OBSERVACION='Se migro Contacto con Exito'
        WHERE ID_CONTACTO=Lr_ListMasivoPorPuntoYTipo.ID_CONTACTO;
        --
        Lv_CantidadMigrado:=Lv_CantidadMigrado+1;
        --
      END IF;     
    END LOOP;   
  END LOOP;
  Lv_Mensaje:= 'Cantidad de Contactos Migrados: '||Lv_CantidadMigrado;  
--
ELSE
  Lv_Mensaje:= 'La informacion de contactos no cumple las Validaciones necesarias';
END IF;
COMMIT;
   
EXCEPTION
 WHEN OTHERS THEN
   ROLLBACK; 
   IF C_ObtieneTitulo%ISOPEN THEN  
     CLOSE C_ObtieneTitulo;
   END IF;
   IF C_ExisteRolEmpresa%ISOPEN THEN  
     CLOSE C_ExisteRolEmpresa;
   END IF;
   IF C_FormaContacto%ISOPEN THEN  
     CLOSE C_FormaContacto;
   END IF; 
   Lv_Mensaje:='Error en Migracion Contactos: ' || SQLCODE || ' - ' || SQLERRM;  

END P_ING_MASIVO_CONTACTOS;

END CMKG_ING_MASIVO_CONTACTOS;
