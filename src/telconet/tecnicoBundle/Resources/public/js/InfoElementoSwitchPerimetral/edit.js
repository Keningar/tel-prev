Ext.onReady(function(){ 
    //deshabilito el modelo
    document.getElementById('telconet_schemabundle_infoelementoswitchperimetraltype_modeloElementoId').disabled = true;  

});

function validacionesEditForm()
{

    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_nombreElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    } 
        
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_descripcionElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_serieFisica").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }  
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_versionOs").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }        
    
    return true;
}