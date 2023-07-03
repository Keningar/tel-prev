function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos...");	

    var intIdOficina                = Ext.getCmp('cmb_oficina').getValue();
    var strCodigoComprobante        = Ext.getCmp('cmbTipoComprobante').getValue();
    var strNumeroEstablecimientoSri = "000";
    var strPuntoEmision             = "001";
    var strDescripcion              = $('#descripcion').val();
    var strNumeroAutorizacion       = "no-obligatorio";

    if ( strMostrarSecuenciales === "S" )
    {
        strNumeroEstablecimientoSri = $('#numeracionUno').val();
        strPuntoEmision             = $('#numeracionDos').val();
    }

    if ( strMostrarNumeroAutorizacion === "S" )
    {
        strNumeroAutorizacion = $('#numeroAutorizacion').val();
    }

    if( intIdOficina == "" || !intIdOficina )
    {  
        intIdOficina = 0; 
    }

    if( strCodigoComprobante == "" || !strCodigoComprobante )
    {  
        strCodigoComprobante = ""; 
    }

    if( strCodigoComprobante == "" )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un tipo de comprobante");

        return false;
    }
    else if( intIdOficina == 0 )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar una oficina de facturación");

        return false;
    }
    else if( strNumeroEstablecimientoSri == "" )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar una oficina que tenga ingresado un número de establecimiento otorgado por el SRI");

        return false;
    }
    else if( strDescripcion == "" )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe agregar una descripción");

        return false;
    }
    else if( strPuntoEmision == "" || strPuntoEmision == "000" )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe escribir un punto de emisión válido");

        return false;
    }
    else if( strMostrarNumeroAutorizacion === "S" && Ext.isEmpty(strNumeroAutorizacion) )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe escribir un número de autorización");

        return false;
    }
    else if( strPuntoEmision.length < 3 )
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "El punto de emisión debe tener mínimo 3 dígitos");

        return false;
    }
                                        
    validacionFormularioViaAjax(strPuntoEmision, 
                                intIdOficina,
                                strNumeroEstablecimientoSri,
                                strNumeroAutorizacion,
                                strCodigoComprobante);
}


function validacionFormularioViaAjax( strPuntoEmision, 
                                      intIdOficina,
                                      strNumeroEstablecimientoSri,
                                      strNumeroAutorizacion,
                                      strCodigoComprobante )
{
    Ext.Ajax.request
    ({
        url: strUrlVerificarNumeracion,
        method: 'post',
        params: 
        { 
            strPuntoEmision: strPuntoEmision,
            intIdOficina: intIdOficina,
            strNumeroEstablecimientoSri: strNumeroEstablecimientoSri,
            strNumeroAutorizacion: strNumeroAutorizacion,
            strMostrarNumeroAutorizacion: strMostrarNumeroAutorizacion,
            strMostrarSecuenciales: strMostrarSecuenciales
        },
        success: function(response)
        {
            var text = response.responseText;

            if(text === "OK")
            {
                document.getElementById('intIdOficinaSeleccionado').value         = intIdOficina;
                document.getElementById('strCodigoComprobanteSeleccionado').value = strCodigoComprobante;
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
                        msg: 'Verificando número de establecimiento otorgado por SRI!!',
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

Ext.onReady(function()
{
    Ext.define('OficinaList', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id_oficina',     type:'int'},
            {name:'nombre_oficina', type:'string'}
        ]
    });
    
    storeOficina = Ext.create('Ext.data.Store',
    {
        model: 'OficinaList',
        proxy: 
        {
            type: 'ajax',
            url : strUrlGetListadoOficinas,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{nombre_oficina: 'Seleccione una oficina', id_oficina: ''}]);
            }      
        },
        autoLoad: true
    });
    
    combo_oficinas = new Ext.form.ComboBox
    ({
        id: 'cmb_oficina',
        name: 'cmb_oficina',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'local',
        width: 250,
        emptyText: 'Seleccione Oficina',
        store: storeOficina,
        displayField: 'nombre_oficina',
        valueField: 'id_oficina',
        renderTo: 'combo_oficina',
        listConfig: 
        {
            listeners: 
            {
                itemclick: function(list, record) 
                {
                    if ( strMostrarSecuenciales === "S" )
                    {
                        getNumeroEstablecimiento(record.get('id_oficina'));
                    }//if ( strMostrarSecuenciales === "S" )
                }
            }
        }
    });
    
    
    Ext.define('TipoComprobanteList', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'valor2', type:'string'},
            {name:'valor1', type:'string'}
        ]
    });
    
    storeTipoComprobantes = Ext.create('Ext.data.Store',
    {
        model: 'TipoComprobanteList',
        proxy: 
        {
            type: 'ajax',
            url : strUrlGetTipoComprobantes,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{valor1: 'Seleccione', valor2: ''}]);
            }      
        },
        autoLoad: true
    });
    
    comboTipoComprobantes = new Ext.form.ComboBox
    ({
        id: 'cmbTipoComprobante',
        name: 'cmbTipoComprobante',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'local',
        width: 250,
        emptyText: 'Seleccione',
        store: storeTipoComprobantes,
        displayField: 'valor1',
        valueField: 'valor2',
        renderTo: 'tipo_comprobante'
    });
});
        
function verificarSoloNumeros(e)
{
    var k = (document.all) ? e.keyCode : e.which;

    if (k==8 || k==0)
    {
        return true;
    }

    var patron = /[0-9 ]/;
    var n = String.fromCharCode(k);

    return patron.test(n);
}


function getNumeroEstablecimiento(idOficina)
{
    connEsperaAccion.request
    ({
        url: strUrlGetNumeroEstablecimientoPorOficina,
        method: 'post',
        dataType: 'json',
        params:
        { 
            idOficina : idOficina
        },
        success: function(result)
        {
            var jsonResult = JSON.parse(result.responseText);
            
            document.getElementById('numeracionUno').value = '';
            
            if( true == jsonResult.error  )
            {
                Ext.Msg.alert('Atención ', jsonResult.mensaje);
            }
            else
            {
                document.getElementById('numeracionUno').value = jsonResult.mensaje;
            }
        },
        failure: function(result)
        {
            document.getElementById('numeracionUno').value = '';
            
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}