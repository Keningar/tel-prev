/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function validar()
{
    nombre  = Ext.get('telconet_schemabundle_admiclasetipomediotype_nombreClaseTipoMedio').dom.value;
    
    if(nombre==="")
    {
        alert("Falta llenar campo Nombre, favor revisar!");
        return false;
    }
    
    return true;
}