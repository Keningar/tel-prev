function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos..."); 
    
    var boolContinuar = true;
    var objExpRegular = /^[0-9]+([.][0-9]{1,2})?$/;
    var strVendedores = document.getElementById('AdmiParametroDet_valor5').value;
    var strMRC        = document.getElementById('AdmiParametroDet_valor3').value;
    var strNRC        = document.getElementById('AdmiParametroDet_valor4').value;
    var strAnio       = document.getElementById('AdmiParametroDet_valor2').value;
    var strMes        = document.getElementById('AdmiParametroDet_valor1').value;
        
    if( strVendedores == '' || strVendedores == null || strVendedores == 'Seleccione vendedor...' )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un vendedor");
        
        return false;
    }
    
    else if( strMRC == '' || strMRC == null)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar la meta MRC");
        
        return false;
    } 
    else if( strNRC == '' || strNRC == null)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar la meta NRC");
        
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
        Ext.Ajax.request
        ({
            url: strUrlVerificaMeta,
            method: 'post',
            params: 
            { 
                strVendedores: strVendedores
            },
            success: function(response)
            {
                var text = response.responseText;
                
                if(text === "OK")
                {
                    document.getElementById("form_new_proceso").submit();
                }
                else
                {                    
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', text); 
                }
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error',result.responseText);
            }
        });
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
    var strBaseTotal    = (parseFloat(strBaseID) + parseFloat(strBaseBS));
    $('#AdmiParametroDet_valor3').attr('value', strBaseTotal);
    $('#AdmiParametroDet_valor3').prop('value', strBaseTotal);
}