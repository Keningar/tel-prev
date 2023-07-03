

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE nombre_parametro = 'SISTEMA OPERATIVO'
AND descripcion = 'LICENCIAS PARA SISTEMAS OPERATIVOS'
AND modulo = 'COMERCIAL'
AND estado = 'Activo'
AND usr_creacion = 'jialava';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE nombre_parametro = 'APLICACIONES'
AND descripcion = 'LICENCIAS PARA APLICACIONES'
AND modulo = 'COMERCIAL'
AND estado = 'Activo'
AND usr_creacion = 'jialava';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE nombre_parametro = 'BASE DE DATOS'
AND descripcion = 'LICENCIAS PARA BASE DE DATOS'
AND modulo = 'COMERCIAL'
AND estado = 'Activo'
AND usr_creacion = 'jialava';

---------

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Licencia SQL Server Standard SPLA 2008,2012,2014,2016'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '4'
AND usr_creacion = 'jialava'
AND estado = 'Activo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '4'
AND usr_creacion = 'jialava'
AND estado = 'Activo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Lic. Sql Server WebEdit Core Licencia SQL Server Web Edition SPLA'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '4'
AND usr_creacion = 'jialava'
AND estado = 'Activo';


--DB de 2

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Adicional Licencia SQL Server Standard SPLA 2008,2012,2014,2016'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '2'
AND usr_creacion = 'jialava'
AND estado = 'Activo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Adicional Licencia SQL Server Enterprise SPLA 2008,2012,2014,2016'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '2'
AND usr_creacion = 'jialava'
AND estado = 'Activo';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'BASE DE DATOS')
AND descripcion = 'Adicional Licencia SQL Server Web Edition SPLA 2008,2012,2014,2016'
AND valor1 = '1'
AND valor2 = '4'
AND valor3 = '4'
AND valor4 = '2'
AND usr_creacion = 'jialava'
AND estado = 'Activo';



-- WINDOWS

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Licencia Windows Server STD 2008/2012/2016 Core Fisico o Virtual'
AND usr_creacion = 'jialava'
AND estado = 'Activo';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripci= 'Lic. Windows Svr DC - Core Licencia Windows Server DataCenter por Core 9EA-0039'
AND usr_creacion = 'jialava'
AND estado = 'Activo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Licencia Windows Server STD 2008/2012/2016 Core Fisico o Virtual'
AND usr_creacion = 'jialava'
AND estado = 'Activo';
   
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Lic. Windows Svr DC - Proc Licencia Windows Server DataCenter por Procesador P71-01031'
AND usr_creacion = 'jialava'
AND estado = 'Activo';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Lic. Windows Svr DC - Proc Licencia Windows Server DataCenter por Procesador P71-01031'
AND usr_creacion = 'jialava'
AND estado = 'Activo';






--
--RED HAT

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Suscripcion Red Hat Large Instance ( Más de 4 Cores fisicos o Virtuales) (COD: MCT2568)'
AND valor1 = 5
AND usr_creacion = 'jialava'
AND estado = 'Activo';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET  
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'SISTEMA OPERATIVO')
AND descripcion = 'Suscripcion Red Hat Small Instance ( hasta 4 Cores fisicos o Virtuales) (COD: MCT2567)'
AND valor2 = 4
AND usr_creacion = 'jialava'
AND estado = 'Activo';



--

COMMIT;


/