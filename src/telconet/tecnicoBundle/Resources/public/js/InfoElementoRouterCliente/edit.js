Ext.onReady(function(){ 
    //deshabilito el modelo del router
    document.getElementById('telconet_schemabundle_infoelementorouterclientetype_modeloElementoId').disabled = true;  

});

function validacionesEditForm()
{

    if(document.getElementById("telconet_schemabundle_infoelementorouterclientetype_nombreElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    } 
        
    if(document.getElementById("telconet_schemabundle_infoelementorouterclientetype_descripcionElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementorouterclientetype_serieFisica").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }  
    
    if(document.getElementById("telconet_schemabundle_infoelementorouterclientetype_versionOs").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }        
    
    return true;
}