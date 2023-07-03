/**
 * Documentación DELETE admi_prod_carac_comportamiento
 *
 * Rollback de los parámetros de comportamiento de las caracteristicas de los productos de MD
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 26-05-2021
 */

delete FROM db_comercial.admi_prod_carac_comportamiento
where usr_creacion = 'migracionMD';
COMMIT;

/
