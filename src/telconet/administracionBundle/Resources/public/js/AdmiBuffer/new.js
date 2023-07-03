/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
    storeHilo = new Ext.data.Store({
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: getHilos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idHilo',        mapping: 'idHilo'},
                {name: 'numeroColor',   mapping: 'numeroColor'}
            ]
    }); 
        
    //crear modelo para el grid
    Ext.define('bufferHiloModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'hiloId',         mapping:'hiloId'},
            {name:'hiloNumeroColor',mappgin:'hiloNumeroColor'}
        ]
    });
    
    //store buffer hilo
    storeBufferHilo = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        autoLoad: false,
        model: 'bufferHiloModel',
        proxy: {
            type: 'ajax',
            url: 'gridBufferHilo',
            reader: {
                type:           'json',
                totalProperty:  'total',
                root:           'bufferHilo'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridBufferHilo.getView().refresh();
            }
        }
    });
    
    var selBufferHilo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridBufferHilo.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid buffer hilo
    gridBufferHilo = Ext.create('Ext.grid.Panel', {
        id:'gridBufferHilo',
        store: storeBufferHilo,
        columnLines: true,
        columns: 
            [
                {
                    id: 'hiloId',
                    header: 'hiloId',
                    dataIndex: 'hiloId',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'hiloNumero',
                    header: 'Numero - Color Hilo',
                    dataIndex: 'hiloNumeroColor',
                    width: 200,
                    sortable: true,
                    renderer: function (value, metadata, record, rowIndex, colIndex, store){
                        if (typeof (record.data.hiloNumeroColor) == "number")
                        {
                            record.data.hiloId = record.data.hiloNumeroColor;
                            for (var i = 0; i < storeHilo.data.items.length; i++)
                            {
                                if (storeHilo.data.items[i].data.idHilo == record.data.hiloId)
                                {
                                    record.data.hiloNumeroColor = storeHilo.data.items[i].data.numeroColor;
                                    break;
                                }
                            }
                        }
                        return record.data.hiloNumeroColor;
                    },
                    editor: {
                        id:'searchHilo_cmp',
                        xtype: 'combobox',
                        typeAhead: true,
                        displayField:'numeroColor',
                        valueField: 'idHilo',
                        triggerAction: 'all',
                        selectOnFocus: true,
                        loadingText: 'Buscando ...',
                        hideTrigger: false,
                        store: storeHilo,
                        lazyRender: true,
                        listClass: 'x-combo-list-small',
                        listeners: {
                            select: function(combo){
                                var r = Ext.create('bufferHiloModel', {
                                    hiloId:          combo.getValue(),
                                    hiloNumeroColor: combo.lastSelectionText
                                });
                                if(!existeRecordHilo(r, gridBufferHilo))
                                {
                                    Ext.get('searchHilo_cmp').dom.value='';
                                    if(r.get('hiloId') != 'null')
                                    {
                                        Ext.get('searchHilo_cmp').dom.value=r.get('hiloNumeroColor');
                                        this.collapse();
                                    }
                                }
                                else
                                {
                                  alert('Ya existe!');
                                  eliminarSeleccion(gridBufferHilo);
                                }
                            }
                        }//listeners
                    }//editor
                }
            ],
        selModel: selBufferHilo,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridBufferHilo);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('bufferHiloModel', { 
                            hiloId:          '',
                            hiloNumeroColor: ''
                    });
                    if(!existeRecordHilo(r, gridBufferHilo))
                    {
                        storeBufferHilo.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 250,
        height: 200,
        frame: true,
        title: 'Agregar Hilos',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
});

function existeRecordHilo(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();

    for (var i = 0; i < num; i++)
    {
        var hilo = grid.getStore().getAt(i).get('hiloId');
        if (hilo === myRecord.get('hiloId') )
        {
            existe = true;
            break;
        }
    }
    return existe;
}

function eliminarSeleccion(datosSelect)
{
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}

function cargarHilosPorClaseTipoMedio(combo)
{
    storeHilo.proxy.extraParams = {estado: 'Activo', claseTipoMedioId:combo.value};
    storeHilo.load({params: {}});
}

function obtenerJsonHilos()
{
    var array_hilos = new Object();
    array_hilos['total'] = gridBufferHilo.getStore().getCount();
    array_hilos['bufferHilo'] = new Array();
    var array_data = new Array();
    for (var i = 0; i < gridBufferHilo.getStore().getCount(); i++)
    {
        array_data.push(gridBufferHilo.getStore().getAt(i).data);
    }
    array_hilos['bufferHilo'] = array_data;
    Ext.get('bufferHilo').dom.value = Ext.JSON.encode(array_hilos);
}

function validar()
{
    obtenerJsonHilos();

    var hilos = gridBufferHilo.getStore().getCount();
    if (hilos === 0)
    {
        alert("No se han registrado los Hilos, favor Revisar!");
        return false;
    }
    
    var colorBuffer = document.getElementById('telconet_schemabundle_admibuffertype_colorBuffer').value;
    var numeroBuffer = document.getElementById('telconet_schemabundle_admibuffertype_numeroBuffer').value;
    if (colorBuffer === "" || numeroBuffer === "")
    {
        alert("Existen campos vacios, favor revisar!");
        return false;
    }

    return true;
}