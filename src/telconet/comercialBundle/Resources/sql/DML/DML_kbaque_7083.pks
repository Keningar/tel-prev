
    /**
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 26-05-2019
    * Característica para determinar si el prospecto es de origen de la plataforma TelcoCRM.
    */
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'PROSPECTO_ORIGEN_TELCOCRM','N','Activo',SYSDATE,'kbaque','COMERCIAL');

COMMIT;
/