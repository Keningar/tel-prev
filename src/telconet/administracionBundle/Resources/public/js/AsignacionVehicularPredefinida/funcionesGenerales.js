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


function validarLetrasYNumeros(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z0-9]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}



function cambioModeloTransporte()
{
    var strModeloSeleccionado = $("#modeloElementoId option:selected").text();
        strModeloSeleccionado = strModeloSeleccionado.trim();
        strModeloSeleccionado = strModeloSeleccionado.toLowerCase();
    var intPos                = strModeloSeleccionado.indexOf("moto"); 
    
    if( intPos === 0 )
    {
        $("#gps" ).val('');
        $("#gps" ).prop( "disabled", true );
        
        $("#letraPlaca").val('');
        $("#letraPlaca").attr( "maxlength", "2" );
        $("#letraPlaca").attr( "title", "2 letras mínimo" );
        $("#numeroPlaca").val('');
        $("#numeroPlaca").attr( "pattern", "[0-9]{3}[a-zA-Z]{1}" );
        $("#numeroPlaca").attr( "title", "3 números mínimo y una letra al final" );
        $("#numeroPlaca").removeAttr( "onkeyPress" );
    }
    else
    {
        $("#gps" ).prop( "disabled", false );
        
        $("#letraPlaca").val('');
        $("#letraPlaca").attr( "maxlength", "3" );
        $("#letraPlaca").attr( "title", "3 letras mínimo" );
        $("#numeroPlaca").val('');
        $("#numeroPlaca").attr( "pattern", ".{3,4}" );
        $("#numeroPlaca").attr( "title", "3 números mínimo" );
        $("#numeroPlaca").attr( "onkeyPress", "return validarSoloNumeros(event);" );
    }
}

function verificarPlacaExistente(strAccion)
{
    var strLetrasPlaca          = $("#letraPlaca" ).val();
    var strNumerosPlaca         = $("#numeroPlaca" ).val();
    var strPlaca                = strLetrasPlaca+'-'+strNumerosPlaca;
    var intTmpIdMedioTransporte = 0;
    
    if( strAccion == 'editar' )
    {
        intTmpIdMedioTransporte = intIdMedioTransporte;
    }
    
    Ext.MessageBox.wait("Guardando datos...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarPlaca,
        method: 'post',
        params: 
        { 
            idMedioTransporte: intTmpIdMedioTransporte,
            placa: strPlaca,
            tipoTransporte: strTipoTransporte,
            accion: strAccion
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
    
    return false;
}


function initial()
{
    var strModeloSeleccionado = $("#modeloElementoId option:selected").text();
        strModeloSeleccionado = strModeloSeleccionado.trim();
        strModeloSeleccionado = strModeloSeleccionado.toLowerCase();
    var intPos                = strModeloSeleccionado.indexOf("moto"); 
    
    if( intPos === 0 )
    {
        $("#gps" ).val('');
        $("#gps" ).prop( "disabled", true );
        
        $( "#letraPlaca" ).attr( "maxlength", "2" );
        $( "#letraPlaca" ).attr( "title", "2 letras mínimo" );
        $( "#numeroPlaca" ).attr( "pattern", "[0-9]{3}[a-zA-Z]{1}" );
        $( "#numeroPlaca" ).attr( "title", "3 números mínimo y una letra al final" );
        $( "#numeroPlaca" ).removeAttr( "onkeyPress" );
    }
    else
    {
        $("#gps" ).prop( "disabled", false );
        
        $( "#letraPlaca" ).attr( "maxlength", "3" );
        $( "#letraPlaca" ).attr( "title", "3 letras mínimo" );
        $( "#numeroPlaca" ).attr( "pattern", ".{3,4}" );
        $( "#numeroPlaca" ).attr( "title", "3 números mínimo" );
        $( "#numeroPlaca" ).attr( "onkeyPress", "return validarSoloNumeros(event);" );
    }
}

initial();
