function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos..."); 
    
    var boolContinuar = true;
    var objExpRegular = /^[0-9]+([.][0-9]{1,2})?$/;
    var strMRC        = document.getElementById('AdmiParametroDet_valor3').value;
    var strNRC        = document.getElementById('AdmiParametroDet_valor4').value;
        
    if( (strMRC == '' || strMRC == null) || (strNRC == '' || strNRC == null))
    {    
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar los valores");
        return false;
    } 
    else if( !objExpRegular.test(strMRC) || !objExpRegular.test(strNRC))
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Por favor ingresar solo numeros enteros o decimales. Ejemplo: 1119122.61");
        return false;
    }
    if( boolContinuar )
    {
        document.getElementById("form_edit_proceso").submit();
    }
    else
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Error', text); 
    }
}

function calculaBase()
{
    var strBaseID       = document.getElementById('AdmiParametroDet_valor6').value;
    var strBaseBS       = document.getElementById('AdmiParametroDet_valor7').value;
    
    
    if(strBaseID == '' || strBaseID == null){
        strBaseID = 0;
    }
    if(strBaseBS == '' || strBaseBS == null){
        strBaseBS = 0;
    }
    var strBaseTotal    = (parseInt(strBaseID) + parseInt(strBaseBS));
    $('#AdmiParametroDet_valor3').attr('value', strBaseTotal);
    $('#AdmiParametroDet_valor3').prop('value', strBaseTotal);
}