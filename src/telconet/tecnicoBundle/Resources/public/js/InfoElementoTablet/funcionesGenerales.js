function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 
    
    document.getElementById(idTexto).value = strMayusculas;
}

function validarCaracteresEspeciales(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z0-9-]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


function validarSoloLetras(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


function validarSoloNumeros(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[0-9]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


function verificarImeiExistente(strAccion)
{
    var strImei        = $("#nombreElemento" ).val();
    var strSerieLogica = $("#serieLogica" ).val();
    var intTmpIdTablet = 0;
    
    if( strAccion == 'editar' )
    {
        intTmpIdTablet = intIdTablet;
    }
    
    Ext.MessageBox.wait("Guardando datos...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarImei,
        method: 'post',
        params: 
        { 
            idTablet            : intTmpIdTablet,
            imei                : strImei,
            serieLogica           : strSerieLogica,
            accion              : strAccion
        },
        success: function(response)
        {
            var text = response.responseText;

            if(text === "OK")
            {
                var store = Ext.getCmp('cmbResponsable').getStore();
                var index = store.find('intIdPerResponsableCmb', Ext.getCmp('cmbResponsable').getValue());
                if(index != -1)//el registro ha sido encontrado
                {
                    document.getElementById('intIdPerResponsable').value    = Ext.getCmp('cmbResponsable').getValue();
                    document.getElementById("divResponsable").remove();
                    document.getElementById("form_new_proceso").submit();
                }
                else
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', 'Por favor seleccione el responsable'); 
                }
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