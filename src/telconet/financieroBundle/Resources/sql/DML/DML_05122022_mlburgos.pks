/** 
 * @author Leonela Burgos <mlburgos@telconet.ec>
 * @version 1.0 
 * @since 10-11-2022
 * Se crea DML de configuraciones del Proyecto Tarjetas ABU actualizacion de algunos parametros
 */


update DB_GENERAL.ADMI_PARAMETRO_DET  set OBSERVACION ='Posición del lado izquierdo  es LTRIM;  Posición del lado derecho  es RTRIM;' 
    where   VALOR1='LADO_RELLENO_IDENTIFICACION'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );
    update DB_GENERAL.ADMI_PARAMETRO_DET  set OBSERVACION ='Posición del lado izquierdo  es LTRIM;  Posición del lado derecho  es RTRIM;' 
    where   VALOR1='LADO_RELLENO_NUMERO_TARJETA_ANTIGUO' AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );
    update DB_GENERAL.ADMI_PARAMETRO_DET  set OBSERVACION ='Posición del lado izquierdo  es LTRIM;  Posición del lado derecho  es RTRIM;' 
    where   VALOR1='LADO_RELLENO_NUMERO_TARJETA_NUEVO'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );    
          
    update DB_GENERAL.ADMI_PARAMETRO_DET  set OBSERVACION ='El valor2 es el identificador del que esta como relleno para la identificacion' 
    where   VALOR1='RELLENO_IDENTIFICACION'  AND PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );                 
                                                
    update DB_GENERAL.ADMI_PARAMETRO_DET  set OBSERVACION ='El valor2 es el identificador del que esta como relleno para el numero antiguo de tarjeta' 
    where   VALOR1='RELLENO_NUMERO_TARJETA_ANTIGUO'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );                 
                                
                          
    update DB_GENERAL.ADMI_PARAMETRO_DET  set 
    OBSERVACION ='El valor2 es el identificador del que esta como relleno para el numero nuevo de la tarjeta' 
    where   VALOR1='RELLENO_NUMERO_TARJETA_NUEVO'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ); 

    update DB_GENERAL.ADMI_PARAMETRO_DET  set 
    OBSERVACION ='Valor1: Es el link del web services que se debe de modificar dependiendo el ambiente; Valor 2: La opción función que va a realizar la tarea automatica; Valor 3: tipo de proceso; Valor 4: caracteres extraños',
    VALOR1='http://telcos-ws-lb.telconet.ec/rs/comercial/ws/rest/ejecutar'
    where   DESCRIPCION='PARAMETROS_WEBSERVICES_TAREA'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    ); 
    update DB_GENERAL.ADMI_PARAMETRO_DET  set VALOR2 ='telcos_abu' 
    where   VALOR1='USUARIO_ABU'  AND  PARAMETRO_ID  = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
    );   
    
                          
    COMMIT;      
