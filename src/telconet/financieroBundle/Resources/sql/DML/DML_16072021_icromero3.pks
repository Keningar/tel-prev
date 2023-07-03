CREATE OR REPLACE PACKAGE DB_FINANCIERO.FNKG_INGRESO_BANCO_GRUPOS AS 
    
    
     /**
     * Inserta registro en ADMI_GRUPO_ARCHIVO_DEBITO_DET , 
     * @author Ivan Romero <icromero@telconet.ec>
     * @since 16-07-2018
     **/
    PROCEDURE P_INSERT_ADMI_GRUPO_DET (
        Pv_GrupoDebitoId       IN                 NUMBER,
        Pv_BancoTipoCuentaId   IN                 NUMBER,
        Pv_UsrCreacion         IN                 VARCHAR2,
        Pv_Estado              IN                 VARCHAR2,
        Pv_Msn             OUT                VARCHAR2
    );
   /**
     * Inserta registro en ADMI_GRUPO_ARCHIVO_DEBITO_CAB y en ADMI_GRUPO_ARCHIVO_DEBITO_DET  , 
     * @author Ivan Romero <icromero@telconet.ec>
     * @since 16-07-2018
     **/
   PROCEDURE P_INSERT_ADMI_GRUPO_CAB_DET (
        Pv_BancoTipoCuentaId   IN                 NUMBER,
        Pv_UsrCreacion        IN                 VARCHAR2,
        Pv_NombreGrupo        IN                 VARCHAR2,
        Pv_Estado              IN                 VARCHAR2,
        Pv_Empresa              IN                 VARCHAR2,
        Pv_Msn             OUT                VARCHAR2
    ) ;
   
   

END FNKG_INGRESO_BANCO_GRUPOS;
/


CREATE OR REPLACE PACKAGE BODY DB_FINANCIERO.FNKG_INGRESO_BANCO_GRUPOS AS

    PROCEDURE P_INSERT_ADMI_GRUPO_DET (
        Pv_GrupoDebitoId       IN                 NUMBER,
        Pv_BancoTipoCuentaId   IN                 NUMBER,
        Pv_UsrCreacion        IN                 VARCHAR2,
        Pv_Estado              IN                 VARCHAR2,
        Pv_Msn             OUT                VARCHAR2
    ) AS
    BEGIN
        Insert into DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET (
        ID_GRUPO_DEBITO_DET,
        GRUPO_DEBITO_ID,
        BANCO_TIPO_CUENTA_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO) 
        values (
        DB_FINANCIERO.SEQ_ADMI_GRUPO_ARCHIVO_DEB_DET.nextval,
        Pv_GrupoDebitoId,
        Pv_BancoTipoCuentaId,
        sysdate,
        Pv_UsrCreacion,
        Pv_Estado);
        

        Pv_Msn := 'registro insertado correctamente';
    EXCEPTION
        WHEN OTHERS THEN
            Pv_Msn := 'Error: ' || SQLERRM;
        ROLLBACK;
    END P_INSERT_ADMI_GRUPO_DET;
    
    PROCEDURE P_INSERT_ADMI_GRUPO_CAB_DET (
        Pv_BancoTipoCuentaId   IN                 NUMBER,
        Pv_UsrCreacion        IN                 VARCHAR2,
        Pv_NombreGrupo        IN                 VARCHAR2,
        Pv_Estado              IN                 VARCHAR2,
        Pv_Empresa              IN                 VARCHAR2,
        Pv_Msn             OUT                VARCHAR2
    ) AS
    Pn_idGrupoDebito Number;
    BEGIN
    
        Pn_idGrupoDebito := DB_FINANCIERO.SEQ_ADMI_GRUPO_ARCHIVO_DEB_CAB.nextval;
        Insert into DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB  (
        ID_GRUPO_DEBITO,
        NOMBRE_GRUPO,
        BANCO_TIPO_CUENTA_ID,
        EMPRESA_COD,
        FE_CREACION,
        USR_CREACION,
        ESTADO,
        TIPO_GRUPO) 
        values (Pn_idGrupoDebito,
        Pv_NombreGrupo,
        Pv_BancoTipoCuentaId,
        Pv_Empresa,
        sysdate,
        Pv_UsrCreacion,
        Pv_Estado,
        'NORMAL');
    
        Insert into DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET (
        ID_GRUPO_DEBITO_DET,
        GRUPO_DEBITO_ID,
        BANCO_TIPO_CUENTA_ID,
        FE_CREACION,
        USR_CREACION,
        ESTADO) 
        values (
        DB_FINANCIERO.SEQ_ADMI_GRUPO_ARCHIVO_DEB_DET.nextval,
        Pn_idGrupoDebito,
        Pv_BancoTipoCuentaId,
        sysdate,
        Pv_UsrCreacion,
        Pv_Estado);
        

        Pv_Msn := 'registros insertados correctamente';
    EXCEPTION
        WHEN OTHERS THEN
            Pv_Msn := 'Error: ' || SQLERRM;
        ROLLBACK;
    END P_INSERT_ADMI_GRUPO_CAB_DET;

   
   

END FNKG_INGRESO_BANCO_GRUPOS;
/
