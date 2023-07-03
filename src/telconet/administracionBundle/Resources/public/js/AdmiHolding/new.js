function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos..."); 
    
    var boolContinuar = true;
    var objExpRegular = /^[0-9]+([.][0-9]{1,2})?$/;

    var strIdentificacion       = document.getElementById('admiholdingtype_valor2').value;
    var strNombre               = document.getElementById('admiholdingtype_valor1').value;
    var strLogin                = document.getElementById('admiholdingtype_valor3').value;    
    
    if( strLogin == '' || strLogin == null)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un vendedor");
        
        return false;
    } 
    if( strNombre == '' || strNombre == null)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar el Nombre de la Razón Social");
        
        return false;
    }     
    else if( !objExpRegular.test(strIdentificacion) || !objExpRegular.test(strIdentificacion))
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Por favor ingresar solo números.");
        return false;
    }
    if( boolContinuar )
    {
        document.getElementById("form_new_proceso").submit();
    }
}