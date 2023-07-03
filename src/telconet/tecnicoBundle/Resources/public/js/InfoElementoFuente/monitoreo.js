var store = null;
var grid  = null;
var win   = null;

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connEsperaAccion = new Ext.data.Connection
({
    listeners:
    {
        'beforerequest': 
        {
            fn: function (con, opt)
            {						
                Ext.MessageBox.show
                ({
                   msg: 'Cargando la información, por favor espere!!',
                   progressText: 'Saving...',
                   width:300,
                   wait:true,
                   waitConfig: {interval:200}
                });
            },
            scope: this
        },
        'requestcomplete':
        {
            fn: function (con, res, opt)
            {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': 
        {
            fn: function (con, res, opt)
            {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

function limpiarBusqueda()
{
    document.getElementById('dispositivo').value = '';
    
    buscarFuentes();
}


function buscarFuentes()
{
    connEsperaAccion.request
    ({
        url: strUrlBuscarDispositivos,
        method: 'post',
        timeout: 90000000,
        params:
        { 
            dispositivo : document.getElementById('dispositivo').value
        },
        success: function(result)
        {
            document.getElementById('monitoreoFuentes').innerHTML = result.responseText;
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function exportarExcel()
{                
    document.forms[0].submit();		
}

setInterval(function(){ "buscarFuentes();" }, 120000);