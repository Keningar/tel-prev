var gridOpciones = null;

Ext.onReady(function()
{
    Ext.define('Opciones', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'idParametroCab', mapping:'idParametroCab'},
            {name:'idParametroDet', mapping:'idParametroDet'},
            {name:'valorParametro', mapping:'valorParametro'}
        ]
    });
    
    var storeOpciones = Ext.create('Ext.data.Store', 
    {
        autoDestroy: true,
        autoLoad: false,
        model: 'Opciones',        
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetOpcionesSeleccionable,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
    {
        clicksToEdit: 2,
        listeners: 
        {
            edit: function()
            {
                gridOpciones.getView().refresh();
            }
        }
    });
    
    var selModelRelaciones = Ext.create('Ext.selection.CheckboxModel',
    {
        listeners:
        {
            selectionchange: function(sm, selections)
            {
                gridOpciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridOpciones = Ext.create('Ext.grid.Panel',
    {
        id:'gridOpciones',
        store: storeOpciones,
        columnLines: true,
        columns: 
        [
            {
                id: 'idParametroCab',
                header: 'idParametroCab',
                dataIndex: 'idParametroCab',
                hidden: true,
                hideable: false
            }, 
            {
                id: 'idParametroDet',
                header: 'idParametroDet',
                dataIndex: 'idParametroDet',
                hidden: true,
                hideable: false
            }, 
            {
                id: 'valorParametro',
                header: 'Opción',
                dataIndex: 'valorParametro',
                width: 264,
                sortable: true,
                editor: 
                {
                    id:'searchAccion_cmp',
                    xtype: 'textfield',
                    typeAhead: true,
                    displayField:'valorParametro',
                    valueField: 'idParametroDet',
                    size: 200
                } 
            }
        ],
        selModel: selModelRelaciones,
        viewConfig:
        {
            stripeRows:true
        },
        dockedItems:
        [{
            xtype: 'toolbar',
            items: 
            [
                {
                    itemId: 'removeButton',
                    text:'Eliminar',
                    tooltip:'Elimina la opción seleccionada',
                    iconCls:'remove',
                    disabled: true,
                    handler : function()
                    {
                        eliminarSeleccion(gridOpciones);
                    }
                }, 
                '-', 
                {
                    itemId: 'addButton',
                    text:'Agregar',
                    tooltip:'Agrega una opción a la lista',
                    iconCls:'add',
                    handler : function()
                    {
                        // Create a model instance
                        var r = Ext.create('Opciones', { idParametroCab: '', idParametroDet: '', valorParametro: ''});
                        
                        if(!existeRecordRelacion(r, gridOpciones))
                        {
                            storeOpciones.insert(0, r);
                            cellEditing.startEditByPosition({row: 0, column: 3});
                        }
                        else
                        {
                            alert('Ya existe un registro vacio.');
                        }
                    }
                }
            ]
        }],
        width: 300,
        height: 200,
        frame: true,
        title: 'Agregar Opciones',
        plugins: [cellEditing]
    });
});


function existeRecordRelacion(myRecord, grid)
{
    var existe  = false;
    var num     = grid.getStore().getCount();

    for(var i=0; i < num ; i++)
    {
        var valorParametro = grid.getStore().getAt(i).get('valorParametro');

        if(valorParametro == myRecord.get('valorParametro') )
        {
            existe = true;
            break;
        }
    }
    
    return existe;
}


function eliminarSeleccion(datosSelect)
{
    var xRowSelMod      = datosSelect.getSelectionModel().getCount();
    var intValorInicial = xRowSelMod - 1;
 
    for(var i = intValorInicial; i >= 0; i--)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}


function cambioTipoIngreso()
{
    var strTipoIngreso = document.getElementById('admicaracteristica_tipoIngreso').value;
    
    if( strTipoIngreso == 'S' )
    {
        document.getElementById('gridSeleccionable').style.display = '';
        
        if( !gridOpciones.rendered )
        {
            gridOpciones.render('gridSeleccionable');
        }
    }
    else
    {
        document.getElementById('gridSeleccionable').style.display = 'none';
    } 
}


function verificarValoresRepetidos()
{
    var intTotalOpciones = gridOpciones.getStore().getCount();
    var intContador      = 0;

    for(var i=0; i < intTotalOpciones ; i++)
    {
        var valorParametroOpcion1 = gridOpciones.getStore().getAt(i).get('valorParametro');
        
        for(var j=i+1; j < intTotalOpciones ; j++)
        {
            var valorParametroOpcion2 = gridOpciones.getStore().getAt(j).get('valorParametro');
            
            if(valorParametroOpcion1 == valorParametroOpcion2 )
            {
                intContador++;
            }
        }
    }
    
    if( intContador > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}


function obtenerOpcionesSeleccionables()
{
    var intTotalOpciones          = 0;
    var boolExistenValoresVacios  = false;
    var arrayOpciones             = new Object();
        arrayOpciones['opciones'] = new Array();

    var arrayData = new Array();

    for(var i=0; i < gridOpciones.getStore().getCount(); i++)
    {
        var strTmpValorParametro = gridOpciones.getStore().getAt(i).data.valorParametro;
        
        if( strTmpValorParametro.trim() != '' && strTmpValorParametro.trim() != null )
        {
            arrayData.push(gridOpciones.getStore().getAt(i).data);
            intTotalOpciones++;
        }
        else
        {
            boolExistenValoresVacios = true;
        }
    }

    if( !boolExistenValoresVacios )
    {
        arrayOpciones['opciones'] = arrayData;
        arrayOpciones['total']    = intTotalOpciones;

        if( arrayOpciones['total'] > 0 )
        {
            Ext.get('opciones').dom.value = Ext.JSON.encode(arrayOpciones);
        }
        else
        {
            Ext.get('opciones').dom.value = '';
        }
    }
    else
    {
        Ext.get('opciones').dom.value = '';
    }
}


function validarFormulario()
{
    Ext.MessageBox.wait("Guardando datos..."); 
    
    var boolContinuar  = true;
    var strDescripcion = document.getElementById('admicaracteristica_descripcionCaracteristica').value;
    var strTipoIngreso = document.getElementById('admicaracteristica_tipoIngreso').value;
    var strModulo      = document.getElementById('admicaracteristica_tipo').value;
    
    if( strDescripcion == '' || strDescripcion == null )
    {
        boolContinuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar una descripción");
        
        return false;
    }
    else if( strTipoIngreso == '' || strTipoIngreso == null || strTipoIngreso == 'Seleccione' )
    {
        boolContinuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un tipo de ingreso de la característica");
        
        return false;
    }
    else if( strModulo == '' || strModulo == null || strModulo == 'Seleccione' )
    {
        boolContinuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un tipo de característica");
        
        return false;
    }
    else if( strTipoIngreso == 'S' )
    {
        if( verificarValoresRepetidos() )
        {
            boolContinuar = false;

            Ext.MessageBox.hide();
            Ext.Msg.alert("Atención", "Existen opciones con el mismo nombre");

            return false;
        }
        else
        {
            obtenerOpcionesSeleccionables();

            if( Ext.get('opciones').dom.value == '' || Ext.get('opciones').dom.value == null)
            {
                boolContinuar = false;

                Ext.MessageBox.hide();
                Ext.Msg.alert("Atención", "Debe llenar todas las opciones relacionadas a la característica");

                return false;
            }
        }
    }
    
    
    if( boolContinuar )
    {
        Ext.Ajax.request
        ({
            url: strUrlVerificaCaracteristica,
            method: 'post',
            params: 
            { 
                strCaracteristica: strDescripcion,
                strTipoIngreso: strTipoIngreso,
                strTipo: strModulo
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