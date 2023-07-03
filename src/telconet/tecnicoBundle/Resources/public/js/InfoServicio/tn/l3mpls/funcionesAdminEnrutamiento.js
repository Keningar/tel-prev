function getPrefixGrid()
{    
    var tipoSeleccion = Ext.getCmp('rgRouteMap').getValue().rbrm;
    
    if(tipoSeleccion === 'nuevos' || (storeSubredes.data !== null && storeSubredes.data.length===0))
    {
        Ext.define('infoSubredes', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idPrefix', mapping: 'idPrefix'},
                {name: 'prefixIp', mapping: 'prefixIp'},
                {name: 'prefixMask', mapping: 'prefixMask'},
                {name: 'tipo', mapping: 'tipo'},
                {name: 'valor', mapping: 'valor'},
                {name: 'seq', mapping: 'seq'}
            ]
        });

        storeSubredes = Ext.create('Ext.data.Store',
            {
                autoDestroy: true,
                autoLoad: false,
                model: 'infoSubredes'
            });
    }
    
    //Store para obtener la informacion de prefix ( routemap ) por servicio
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function() {
                gridSubredes.getView().refresh();
            }
        }
    });

    var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridSubredes.down('#removeButton').setDisabled(selections.length === 0);
            }
        }
    });   
    
    var arrayPrefixMask = prefixMask.split("|");
    var arrayPrefixType = prefixType.split("|");

    //Crear store de prefijos
    var modelFields = [];

    for (var i = arrayPrefixMask[1] ; i >= arrayPrefixMask[0] ; i--)
    {
        modelFields.push('/' + i);
    }

    var modelFieldsTipo = [];

    for (var i = arrayPrefixType[1] ; i >= arrayPrefixType[0] ; i--)
    {
        modelFieldsTipo.push(i);
    }

    var gridSubredes = Ext.create('Ext.grid.Panel', {
        id: 'gridSubredes',
        store: storeSubredes,
        columns: [
            {
                id: 'idPrefix',
                dataIndex: 'idPrefix',
                hidden:true
            },
            {
                id: 'prefixIp',
                text: 'Prefix IP',
                dataIndex: 'prefixIp',
                width: 120,
                align: 'center',
                renderer: function(value, metadata, record, rowIndex, colIndex, store)
                {
                    if (record.data.idPrefix == '')
                    {
                        var valor  = record.data.prefixIp;
                        var arrayInfoRuta = valor.split("/");
                        if(arrayInfoRuta.length > 1)
                        {
                            record.data.prefixIp   = arrayInfoRuta[0];
                            record.data.prefixMask = "/"+arrayInfoRuta[1];
                            gridSubredes.getStore().getAt(rowIndex).data.prefixIp=  record.data.prefixIp;
                            gridSubredes.getStore().getAt(rowIndex).data.prefixMask=  record.data.prefixMask;
                        }
                    }
                    return record.data.prefixIp;
                },
                editor:
                    new Ext.form.field.ComboBox({
                        id:'cmbPrefixIp',
                        typeAhead: true,
                        triggerAction: 'all',
                        selectOnTab: true,
                        editable: false,
                        displayField:'subred',
                        valueField:'subred',
                        name: 'prefixIp',
                        store: storeRutasBgp,
                        listClass: 'x-combo-list-small',
                        emptyText: 'Escoja la ruta',
                        lazyRender: true                        
                    })
            },
            {
                id: 'prefixMask',
                text: 'Prefix Mask',
                dataIndex: 'prefixMask',
                align: 'center',
                width: 80
            },
            {
                id: 'tipo',
                text: 'Tipo',
                dataIndex: 'tipo',
                align: 'center',
                width: 70,
                editor:
                    new Ext.form.field.ComboBox({
                        id:'cmbTipo',
                        typeAhead: true,
                        triggerAction: 'all',
                        selectOnTab: true,
                        editable: false,
                        store: [
                            ['le', 'le'],
                            ['ne', 'ne'],
                            ['eq', 'eq']
                        ],
                        listClass: 'x-combo-list-small',
                        emptyText: 'Escoja el tipo'
                    })
            },
            {
                id: 'valor',
                text: 'Valor',
                dataIndex: 'valor',
                align: 'center',
                width: 60,
                editor:
                    new Ext.form.field.ComboBox({
                        typeAhead: true,
                        triggerAction: 'all',
                        selectOnTab: true,
                        editable: false,
                        store: modelFieldsTipo,
                        listClass: 'x-combo-list-small',
                        emptyText: 'Escoja el Valor',
                        listeners:
                        {
                            change: function(combo)
                            {
                                var valor  = combo.getValue();
                                var tipo   = Ext.getCmp('cmbTipo').value;
                                
                                if(Ext.isEmpty(tipo))
                                {
                                    Ext.Msg.alert("Advertencia", "Por favor ingrese los valores de Prefix Mask o Tipo faltantes");
                                    combo.setValue("");
                                    combo.setRawValue("");
                                    return false;
                                }
                                else
                                {
                                    if(tipo !== "eq")
                                    {
                                        var mask = prefix.replace("/", "");
                                        
                                        if(valor <= mask)
                                        {
                                            Ext.Msg.alert("Advertencia", "El Valor debe ser mayor a la máscara escogida");
                                            combo.setValue("");
                                            combo.setRawValue("");
                                        }
                                    }
                                }
                            }
                        }
                    })
            },
            {
                id: 'seq',
                text: 'Secuencia',
                dataIndex: 'seq',
                width: 80,
                align: 'center',
                editor:
                    {
                        xtype: 'numberfield',
                        hideTrigger: true,
                        allowBlank: false,
                        width: 100,
                        emptyText: 'Ingrese Secuencia'
                    }
            }
        ],
        selModel: selEspacioModelo,
        viewConfig: {
            stripeRows: true
        },
        tbar: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el item seleccionado',
                        iconCls: 'icon_delete',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridSubredes);
                        }
                    }, '-', {
                        text: 'Agregar',
                        tooltip: 'Agrega un item a la lista',
                        iconCls: 'icon_add',
                        handler: function() {

                            var grid = gridSubredes;

                            if (grid.getStore().getCount() !== 0)
                            {
                                for (var i = 0; i < grid.getStore().getCount(); i++)
                                {
                                    var prefixIp   = grid.getStore().getAt(i).data.prefixIp;
                                    var prefixMask = grid.getStore().getAt(i).data.prefixMask;
                                    var seq        = grid.getStore().getAt(i).data.seq;

                                    if (!Ext.isEmpty(prefixIp))
                                    {
                                        if (!Utils.validateIp(prefixIp))
                                        {
                                            Ext.Msg.alert("Advertencia", "El Formato de la Ip <b>" + prefixIp + "</b> no es válido");
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        Ext.Msg.alert("Advertencia", "El valor del <b>Prefix IP</b> no debe ser vacío");
                                        return false;
                                    }

                                    if (Ext.isEmpty(prefixMask))
                                    {
                                        Ext.Msg.alert("Advertencia", "El valor de la <b>Prefix Mask</b> no debe ser vacío");
                                        return false;
                                    }

                                    if (Ext.isEmpty(seq))
                                    {
                                        Ext.Msg.alert("Advertencia", "El valor de la <b>Secuencia</b> no debe ser vacío");
                                        return false;
                                    }
                                    else
                                    {
                                        if (seq < 10)
                                        {
                                            Ext.Msg.alert("Advertencia", "El valor de la <b>Secuencia</b> no debe ser menor a 10");
                                            return false;
                                        }
                                    }
                                }
                            }                           
                            var r = Ext.create('infoSubredes', {
                                idPrefix  : '',
                                prefixIp  : '',
                                prefixMask: '',
                                tipo      : '',
                                valor     : '',
                                seq       : ''
                            });

                            storeSubredes.insert(0, r);
                            cellEditing.startEditByPosition({row: 0, column: 0});
                        }
                    }]
            }],
        width: 450,
        height: 200,
        title: 'Subredes',
        plugins: [cellEditing],
        listeners: {
            beforeedit: function(editor, context) 
            {
                if(context.record.data.idPrefix !== '')
                {
                    return false;
                }
            }
        }
    });

    return gridSubredes;
}

function getInfoGrid(grid)
{
    var info = '';
    
    if (grid.getStore().getCount() !== 0)
    {
        var array_data = new Array();
        var array      = new Object();
        array['data']  = new Array();

        for (var i = 0; i < grid.getStore().getCount(); i++)
        {
            var idPrefix   = grid.getStore().getAt(i).data.idPrefix;
            var prefixIp   = grid.getStore().getAt(i).data.prefixIp;
            var prefixMask = grid.getStore().getAt(i).data.prefixMask;
            var tipo       = grid.getStore().getAt(i).data.tipo;
            var valor      = grid.getStore().getAt(i).data.valor;
            var seq        = grid.getStore().getAt(i).data.seq;

            var datos = 
                {
                    idPrefix   : idPrefix,
                    prefixIp   : prefixIp,
                    prefixMask : prefixMask,
                    tipo       : tipo,
                    valor      : valor,
                    seq        : seq
                };
            
            array_data.push(Ext.JSON.encode(datos));
        }
        
        array['data'] = array_data;

        info = Ext.JSON.encode(array);
    }
    
    return info;
}

function getButtons(tipo)
{
    var botones = '';

    switch (tipo)
    {
        case '+':
            botones = '<img height="15" width="15" src="'+urlAddImage+'">';
            break;
        case '-':
            botones = '<img height="15" width="15" src="'+urlDeleteImage+'">';
            break;
    }

    return botones;
}

function isArray(value)
{
    return Object.prototype.toString.call(value) === "[object Array]";
}
