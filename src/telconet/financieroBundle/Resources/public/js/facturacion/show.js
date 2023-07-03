/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var connEsperaAccion;
Ext.onReady(function(){     

    connEsperaAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Actualizando los datos, Por favor espere!!',
                                    progressText: 'Saving...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }


    });


var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

Ext.define('ListadoDetalleOrden', {
    extend: 'Ext.data.Model',
    fields: [
             {name:'id', type: 'int'},
			 {name:'informacion', type: 'string'},
             {name:'precio', type: 'string'},
             {name:'cantidad', type: 'string'},
             {name:'descuento', type: 'string'},
             {name:'descripcion', type: 'string'},
             {name:'login', type: 'string'},
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'ajax',
        timeout: 9000000,
        url: url_listar_informacion_existente,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:{facturaid:factura_id},
        simpleSortMode: true               
    },
});

store.load();

// create the grid and specify what field you want
// to use for the editor at each header.
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        id: 'grid_detalles_factura',
        columns: [new Ext.grid.RowNumberer(), 
        {
            text: 'Producto/Plan',
            width: 200,
            dataIndex: 'informacion'
        },{
            text: 'Login',
            width: 200,
            dataIndex: 'login'
        }, 
        {
            text: 'Descripcion',
            width: 200,
            dataIndex: 'descripcion',
            editor: 'textfield'    
        },
        {
            text: 'Cantidad',
            dataIndex: 'cantidad',
            align: 'right',
            width: 70			
        },{
            text: 'Descuento',
            dataIndex: 'descuento',
            align: 'right',
            width: 70			
        },{
            text: 'Precio',
            width: 130,
            dataIndex: 'precio'
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'listado_detalle_factura',
        width: 910,
        height: 200,
        title: 'Detalle de factura',
        frame: true,
        plugins: [cellEditing],
        viewConfig: 
        {
            stripeRows: true,
            enableTextSelection: true
        },
        
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        }
       
    });


    grid.on('beforeedit', function(editor, e) {
        if (puede_modificar_descripcion==true)
        {
            //Ext.getCmp('btnModificar').setDisabled(false);
            return true;
        }
            
        else 
        {
            return false;
        }
      });
    
    //Ext.getCmp('btnModificar').setDisabled(true);
});


function enviar_datos()
{
    console.log("Por enviar consulta");
                
                    
                        var arrayGridDetalleFactura = Ext.getCmp('grid_detalles_factura');
                        var arrayDetallesFactura = new Object();
                        arrayDetallesFactura['intTotal'] = arrayGridDetalleFactura.getStore().getCount();
                        arrayDetallesFactura['arrayData'] = new Array();
                        arrayDetallesFactura['idFactura'] = factura_id;
                        for (var i = 0; i < grid.getStore().getCount(); i++)
                        {
                            var strDescripcion = grid.getStore().getAt(i).data.descripcion;
                            var strId = grid.getStore().getAt(i).data.id;
                            arrayDetallesFactura["arrayData"].push({descripcion:strDescripcion, id:strId});
                        }
                        console.log(arrayDetallesFactura["arrayData"]);    
                        arrayJsonDetallesFactura = Ext.JSON.encode(arrayDetallesFactura);
                        
                        connEsperaAccion.request(
                            {
                                url: url_modificar_descripcion,
                                method: 'POST',
                                timeout: 60000,
                                params:
                                {
                                    arrayDetallesFactura: arrayJsonDetallesFactura
                                },
                                success: function(response)
                                {
                                    var text = Ext.decode(response.responseText);
                                    console.log(text);
                                    if (text.boolCodError=="sucess")
                                    {
                                        window.location.reload();
                                        Ext.Msg.show(
                                            {
                                                title: 'Información',
                                                msg: text.strMensaje,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.INFO
                                            });
                                            
                                    }
                                    else
                                    {
                                        Ext.Msg.show(
                                            {
                                                title: 'Error',
                                                msg: text.strMensaje,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.show(
                                        {
                                            title: 'Error',
                                            msg: 'Error: ' + result.statusText,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                }
                            });    
    
}

function validaAutorizadoSri(idDocumento) {

    var parametros = { "intIdDocumento": idDocumento };
    $.ajax({
        data: parametros,
        url: url_validaAutorizadoSri,
        type: 'post',
        beforeSend: function () {
        },
        success: function (response) {

            if (response.strAutorizaSri == "N")
            {
                $('#modalMensajes .modal-body').html('<p>La factura aún no esta autorizada, Favor intente en las próximas 24 horas.</p>');
                $('#modalMensajes').modal('show');

            } else
            {
                window.location.href = url_creaNotaCredito;
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>Error, Favor comuníquese con el departamento de Sistemas.</p>');
            $('#modalMensajes').modal('show');
        }
    });

}
