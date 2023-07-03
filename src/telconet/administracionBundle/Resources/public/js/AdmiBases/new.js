function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos..."); 
    
    var boolContinuar = true;
    var objExpRegular = /^[0-9]+([.][0-9]{1,2})?$/;
    var strVendedores = document.getElementById('AdmiParametroDet_valor5').value;
    var strBase       = document.getElementById('AdmiParametroDet_valor3').value;
    var strMes        = document.getElementById('AdmiParametroDet_valor1').value;
    var strAnio       = document.getElementById('AdmiParametroDet_valor2').value;
        
    if( strVendedores == '' || strVendedores == null || strVendedores == 'Seleccione vendedor...' )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un vendedor");
        
        return false;
    }
    else if( strMes == '' || strMes == null || strMes == 'Seleccione mes...' )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un mes");
        
        return false;
    }
    else if( strAnio == '' || strAnio == null || strAnio == 'Seleccione año...' )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un año");
        
        return false;
    }
    else if( strBase == '' || strBase == null)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar la base");
        
        return false;
    }
    else if( !objExpRegular.test(strBase) )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Por favor ingresar solo numeros enteros o decimales. Ejemplo: 1119122.61");
        return false;
    }

    if( boolContinuar )
    {
        Ext.Ajax.request
        ({
            url: strUrlVerificaBase,
            method: 'post',
            params: 
            { 
                strVendedores: strVendedores,
                strMes: strMes,
                strAnio: strAnio                
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