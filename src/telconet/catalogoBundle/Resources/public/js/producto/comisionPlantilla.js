/**
 * Documentación para el método 'comisionPlantilla'.
 *
 * Envia mediante post el id del Producto
 * permite el ingreso de la Plantilla de Comisiones 
 * 
 * @param integer    intIdProducto        Obtiene el IdProducto al cual le asignara Plantilla de Comisiones.
 * @param string     strIdsProductos      Obtiene los Ids de productos a los cuales les asignara masivamente la plantilla de comisiones
 * 
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 15-03-2017 
 */

function comisionPlantilla(intIdProducto,strIdsProductos)
{  
    var connComisionPlantilla = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Grabando los datos, Por favor espere!!',
                        progressText: 'Guardando...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            }
        }
    }); 
    
    Ext.define('ComisionPlantillaModel', {
        extend: 'Ext.data.Model',
        fields: [           
            {name: 'idComisionDet', type: 'int'},
            {name: 'idParametroDet',  type: 'int'},
            {name: 'parametroDet',  type: 'string'},
            {name: 'requerido',  type: 'string'},
            {name: 'comisionVenta', type: 'string'}
        ]
    });
    // create the Data Store
    storeComision = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'ComisionPlantillaModel',
        proxy: {
            type: 'ajax',
            url: url_gridComisionPlantilla,
            reader: {
                type: 'json',
                root: 'listado',
                totalProperty: 'total'
            },
            extraParams:{intIdProducto:''},
            simpleSortMode: true
        },
        listeners: {
                    beforeload: function(storeComision)
                    {
                     storeComision.getProxy().extraParams.intIdProducto    = intIdProducto;
                    }
                }
    });

   
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });
    
    grid = Ext.create('Ext.grid.Panel', {
        store: storeComision,
        columns: [new Ext.grid.RowNumberer(),
        {
            text:'Grupo Rol',    
            header: 'Grupo Rol',
            dataIndex: 'parametroDet',
            width: 150,
        }, {
            text: 'Comisión Venta (%)',
            header: 'Comisión Venta (%)',
            dataIndex: 'comisionVenta',
            width: 150,
            align: 'right',
            editor:
                  {
                      width: '80%',
                      xtype: 'textfield',                      
                  }          
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_comision_plantilla_grid'),
        width: 350,
        height: 150,
        title: '',
        plugins: [cellEditing]
    });
    
    storeComision.load();
           
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 90,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 1
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 250
                            },
                        items:
                            [
                                grid
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Grabar',
                        formBind: true,
                        handler: function()
                        {   
                            if( validaComisionPlantilla('#admiproductoextratype_strComisionPlantilla') )
                            {
                                connComisionPlantilla.request
                                ({
                                    url: url_guardaComisionPlantillaAjax,
                                    method: 'post',
                                    waitMsg: 'Esperando Respuesta',
                                    timeout: 400000,
                                    params:
                                        {
                                            strComisionPlantilla: $('#admiproductoextratype_strComisionPlantilla').val(),
                                            intIdProducto: intIdProducto,
                                            strIdsProductos: strIdsProductos
                                        },
                                    success: function(response)
                                    {
                                        var respuesta = response.responseText;

                                        if (respuesta == "Error")
                                        {
                                            Ext.Msg.alert('Error ', 'Se presentaron problemas al guardar la información,' +
                                                ' favor notificar a Sistemas');
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('MENSAJE ', 'Se guardó la información correctamente.');
                                            store.load({params: {start: 0, limit: 10}});    
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                                $('#admiproductoextratype_strComisionPlantilla').val('');
                                winComisionPlantilla.destroy();
                                
                            } 
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winComisionPlantilla.destroy();
                        }
                    }
                ]
        });

    var winComisionPlantilla = Ext.create('Ext.window.Window',
        {
            title: 'Ingreso de Plantilla de Comisionistas',
            modal: true,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function validaComisionPlantilla(campo)
{
    var array_data        = new Array();
    var variable          = '';
    var valoresInvalidos  = false;
    var valoresRequeridos = false;
    var esRequerido       = false;
    var sumaTotal         = 0;
    
    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        esRequerido = false;
        variable    = grid.getStore().getAt(i).data;
        for (var key in variable)
        {
            var valor = variable[key];

            if (key == 'requerido' && valor == 'SI')
            {                
                esRequerido = true;
            }
           
            //Valido que el valor en comision por venta sea una valor Valido
            if (key == 'comisionVenta' && (valor != '' && !/^\d+(\.\d+)?$/.test(valor)))
            {
                valoresInvalidos = true;
            }
            else
            {
                //Valido que si el campo es requerido sea obligatorio el ingreso.
                if (esRequerido == true && (key == 'comisionVenta' && (valor == '' || valor == 0)))
                {
                    valoresRequeridos = true;
                }
                else
                {
                    array_data.push(valor);                    
                }
            }
            //Obtengo la sumatoria del valor de las comisiones en venta de todos los GRUPO_ROLES_PERSONAL
             if (key == 'comisionVenta' && /^\d+(\.\d+)?$/.test(valor))
             {
                 sumaTotal = sumaTotal + Number(valor);
             }
        }
    }
    $(campo).val(array_data);
    
    if (valoresInvalidos == true)
    {
        Ext.Msg.alert('Error ','Hay valores en comisión en venta invalidos, por favor corregir.');
        $(campo).val('');
        return false;
    }
    else
    {
        if (valoresRequeridos == true)
        {
            Ext.Msg.alert('Error ','Hay valores en comisión en venta requeridos, por favor corregir.');
            $(campo).val('');
            return false;
        }
        else
        {
            if(sumaTotal>ftlValorMaxComision)
            {
                Ext.Msg.alert('Error ','La sumatoria de los valores de comisión en venta exceden el valor máximo permitido de  [' + 
                              ftlValorMaxComision + '], por favor corregir.');
                $(campo).val('');
                return false; 
            }
        }
    }
    return true;
}
