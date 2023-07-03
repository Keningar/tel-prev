/**
 * El usuaurio que hace la conexión del db_link tiene que tener permisos de lectura 
*  y actualizaciòn de las tablas FIER_TN Y FIBER_MD
*
* @author Liseth Candelario <lcandelario@telconet.ec>
* @version 1.0 05-09-2022
*/

GRANT SELECT,UPDATE ON SDE.FIBER_TN TO ARCGIS;

GRANT SELECT,UPDATE ON SDE.FIBER_MD TO ARCGIS;

/