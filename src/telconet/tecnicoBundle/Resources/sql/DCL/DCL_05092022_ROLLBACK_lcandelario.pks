/**
 * Se agrega rollback del grant para tener permisos de lectura 
*  y actualizaciòn de las tablas FIER_TN Y FIBER_MD

 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 05-09-2022 -
 */

REVOKE SELECT,UPDATE ON SDE.FIBER_TN  FROM ARCGIS;

REVOKE SELECT,UPDATE ON SDE.FIBER_MD  FROM ARCGIS;

/