var arrayStores  = [];

Ext.tip.QuickTipManager.init();

var storeClases = Ext.create('Ext.data.Store',{
    id: 'storeIdTipoRuta',
    async: false,
    proxy: {
        type: 'ajax',
        url: strUrlGetTipoRuta,
        timeout: 600000,
        reader: {
            type: 'json',
            root: 'encontrados'
        }
    },
    fields: [
        {name: 'idTipoElemento', mapping: 'idTipoElemento'},
        {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'}
    ],
    autoLoad: true,
});

var objStoreTipo = Ext.create('Ext.data.Store', {
    id: 'storeIdTipo',
    async: false,
    proxy: {
        type: 'ajax',
        url: strUrlGetModelo,
        timeout: 600000,
        reader: {
            type: 'json',
            root: 'encontrados'
        },
    },
    fields: [
        {name: 'idModeloElemento', mapping: 'idModeloElemento'},
        {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
    ],
    autoLoad: true,
}); 

var storeClaseTipoMedio = Ext.create('Ext.data.Store', {
    id: 'storeIdTipo',
    async: false,
    proxy: {
        type: 'ajax',
        url: url_claseTipoMedio,
        timeout: 600000,
        reader: {
            type: 'json',
            root: 'encontrados'
        },
        extraParams: {
            estado: 'Activo'
        },
    },
    fields: [
        {name: 'idClaseTipoMedio', mapping: 'idClaseTipoMedio'},
        {name: 'nombreClaseTipoMedio', mapping: 'nombreClaseTipoMedio'}
    ],
    autoLoad: true,
}); 

var storeTramos = new Ext.data.Store({
            total: 'total',
            async: false,
            proxy: {
                type: 'ajax',
                url: url_getTramos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idRelacion', mapping: 'idRelacion'},            
                    {name: 'nombreTramo', mapping: 'nombreTramo'},
                    {name: 'idElementoA', mapping: 'idElementoA'},
                    {name: 'idCantonElementoA', mapping: 'idCantonElementoA'},
                    {name: 'nombreElementoA', mapping: 'nombreElementoA'},
                    {name: 'tipoElementoA', mapping: 'tipoElementoA'},
                    {name: 'idElementoB', mapping: 'idElementoB'},
                    {name: 'idCantonElementoB', mapping: 'idCantonElementoB'},
                    {name: 'nombreElementoB', mapping: 'nombreElementoB'},
                    {name: 'tipoElementoB', mapping: 'tipoElementoB'},
                    {name: 'claseTipoMedio', mapping: 'claseTipoMedio'},
                    {name: 'idClaseTipoMedio', mapping: 'idClaseTipoMedio'},
                    {name: 'tipoLugar', mapping: 'tipoLugar'},
                    {name: 'feCreacion', mapping: 'feCreacion'},
                    {name: 'usrCreacion', mapping: 'usrCreacion'},
                    {name: 'level', mapping: 'level'}
                    
                ]
});  

var storeBuscaTipoElemento = new Ext.data.Store({
    total: 'total',
    async: false,
    proxy: {
        type: 'ajax',
        url: urlTipoElementosRuta,
        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
    },
    fields:
        [
            {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'},
            {name: 'idTipoElemento', mapping: 'idTipoElemento'}
        ],
    autoLoad: true,    
});  

var storeBuscaElemento = new Ext.data.Store({
total:     'total',
pageSize : '',
proxy: {
    type: 'ajax',
    url:  urlBuscarElemento,
    timeout: 60000,
    reader: {
        type:          'json',
        totalProperty: 'total',
        root:          'encontrados'
    }
},
fields:
    [
        {name: 'idElemento'     , mapping: 'id'},
        {name: 'nombreElemento' , mapping: 'nombre'},
        {name: 'modeloElemento' , mapping: 'modelo'},
        {name: 'estadoElemento' , mapping: 'estado'},
        {name: 'nuevaRuta'      , mapping: 'nueva'}
    ]
});

var storeBuscaElementoFin = new Ext.data.Store({
total:     'total',
pageSize : '',
async: false,
proxy: {
    type: 'ajax',
    url:  urlBuscarElemento,
    reader: {
        type:          'json',
        totalProperty: 'total',
        root:          'encontrados'
    }
},
fields:
    [
        {name: 'idElemento'     , mapping: 'id'},
        {name: 'nombreElemento' , mapping: 'nombre'},
        {name: 'modeloElemento' , mapping: 'modelo'},
        {name: 'estadoElemento' , mapping: 'estado'}
    ]
});
        
Ext.onReady(function()
{
    
    var verPoste = function(grid, rowIndex, colIndex) {
        var rec = store.getAt(rowIndex);

        var formVerPoste = Ext.create('Ext.form.Panel', {
            id: 'formVerPoste',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            layout: {
                type: 'table',
                columns: 4,
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    style: ' padding: 5px;',
                    align: 'left',
                    valign: 'middle'
                }
            },
            items: []
        });

        var objLblNombreElemento = Utils.objLabel();
        objLblNombreElemento.style = Utils.STYLE_BOLD;
        objLblNombreElemento.text = "Nombre Elemento:";
        var objLblValorNombreElemento = Utils.objLabel();
        objLblValorNombreElemento.text = rec.get('nombreElemento');

        var objLblEstado = Utils.objLabel();
        objLblEstado.style = Utils.STYLE_BOLD;
        objLblEstado.text = "Estado: ";
        var objLblValorEstado = Utils.objLabel();
        objLblValorEstado.text = rec.get('estado');

        var objLblDescElemento = Utils.objLabel();
        objLblDescElemento.style = Utils.STYLE_BOLD;
        objLblDescElemento.text = "Descripción: ";
        var objLblValorDescElemento = Utils.objLabel();
        objLblValorDescElemento.text = rec.get('descripcionElemento');

        var objLblTipo = Utils.objLabel();
        objLblTipo.style = Utils.STYLE_BOLD;
        objLblTipo.text = "Modelo: ";
        var objLblValorTipo = Utils.objLabel();
        objLblValorTipo.text = rec.get('nombreModelo');

        var objLblClase = Utils.objLabel();
        objLblClase.style = Utils.STYLE_BOLD;
        objLblClase.text = "Tipo de Ruta: ";
        var objLblValorClase = Utils.objLabel();
        objLblValorClase.text = rec.get('detalleValor');

        formVerPoste.add(objLblNombreElemento);
        formVerPoste.add(objLblValorNombreElemento);
        formVerPoste.add(objLblEstado);
        formVerPoste.add(objLblValorEstado);
        formVerPoste.add(objLblDescElemento);
        formVerPoste.add(objLblValorDescElemento);
        formVerPoste.add(objLblTipo);
        formVerPoste.add(objLblValorTipo);
        formVerPoste.add(objLblClase);
        formVerPoste.add(objLblValorClase);

        var storeHistorial = new Ext.data.Store({
            total: 'total',
            async: false,
            proxy: {
                type: 'ajax',
                url: url_getHistorialElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'estado_elemento', mapping: 'estado_elemento'},
                    {name: 'fe_creacion', mapping: 'fe_creacion'},
                    {name: 'usr_creacion', mapping: 'usr_creacion'},
                    {name: 'observacion', mapping: 'observacion'}
                ]
        });
        
        storeHistorial.load({params: {
                idElemento: rec.get('idElemento')
            }});
        
        storeTramos.load({params: {
                intElemento: rec.get('idElemento')
            }});        


        var formTramos = Ext.create('Ext.grid.Panel', {
            width: 890,
            height: 280,
            store: storeTramos,
            loadMask: true,
            frame: false,
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            title: 'Tramos de la Ruta',
            viewConfig: {enableTextSelection: true},
            columns: [
                {
                    id: 'nombreTramo',
                    header: 'Nombre',
                    dataIndex: 'nombreTramo',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'nombreElementoA',
                    header: 'Elemento A',
                    dataIndex: 'nombreElementoA',
                    width: 150,
                    sortable: true
                },      
                {
                    id: 'tipoElementoA',
                    header: 'Tipo',
                    dataIndex: 'tipoElementoA',
                    width: 100,
                    sortable: true
                },   
                {
                    id: 'nombreElementoB',
                    header: 'Elemento B',
                    dataIndex: 'nombreElementoB',
                    width: 150,
                    sortable: true
                },   
                {
                    id: 'tipoElementoB',
                    header: 'Tipo',
                    dataIndex: 'tipoElementoB',
                    width: 100,
                    sortable: true
                },                 
                {
                    id: 'claseTipoMedio',
                    header: 'Tipo Fibra',
                    dataIndex: 'claseTipoMedio',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'tipoLugar',
                    header: 'Tipo Lugar',
                    dataIndex: 'tipoLugar',
                    width: 100,
                    sortable: true
                },                
                {
                    id: 'feCreacion',
                    header: 'Fecha',
                    dataIndex: 'feCreacion',
                    width: 120,
                    sortable: true
                },
                {
                    id: 'usrCreacion',
                    header: 'Usuario',
                    dataIndex: 'usrCreacion',
                    width: 60,
                    sortable: true
                }
            ]
        });


        var formVerHistorialPoste = Ext.create('Ext.grid.Panel', {
            width: 890,
            height: 240,
            store: storeHistorial,
            loadMask: true,
            frame: false,
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            title: 'Historial de Elemento',
            viewConfig: {enableTextSelection: true},
            columns: [
                {
                    id: 'estado_elemento',
                    header: 'Estado',
                    dataIndex: 'estado_elemento',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'fe_creacion',
                    header: 'Fecha Creación',
                    dataIndex: 'fe_creacion',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'usr_creacion',
                    header: 'Usuario Creación',
                    dataIndex: 'usr_creacion',
                    width: 150,
                    sortable: true
                },
                {
                    id: 'observacion',
                    header: 'Observación',
                    dataIndex: 'observacion',
                    width: 300,
                    sortable: true
                }
            ]
        });

        btnregresar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                windowVerPoste.destroy();
            }
        });

        var windowVerPoste = Ext.widget('window', {
            title: 'Información de elemento ' + rec.get('nombreElemento'),
            id: 'windowVerPoste',
            height: 690,
            width: 900,
            modal: true,
            resizable: false,
            closeAction: 'destroy',
            items: [formVerPoste,
                    formTramos,
                    formVerHistorialPoste],
            buttonAlign: 'center',
            buttons: [btnregresar]
        });
        windowVerPoste.show();
    };

    var btnVerPoste = Ext.create('Ext.button.Button', {
        text: 'Ver',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            return 'button-grid-show';
        },
        tooltip: 'Ver',
        handler: verPoste
    });

    var editarPoste = function(grid, rowIndex, colIndex) {
        
        var rec = store.getAt(rowIndex);        
        var idRuta = rec.get('idElemento');
        
        //editar tramo
        var btnEditarTramo = Ext.create('Ext.button.Button', {
            text: 'Editar',
            scope: this,
            style: {
                marginTop: '10px'
            },
            getClass: function(v, meta, rec)
            {
                var strEstado = rec.get('estado');
                //valido para que no se pueda editar ni el primer ni ultimo tramo
                var final = storeTramos.totalCount ;
                var mostrar = true;
                if(final == rec.get('level') || 1 == rec.get('level'))
                {
                    mostrar = false;    
                }
                
                if (strEstado !== 'Eliminado' && boolPermisoEditarTramo && mostrar)
                {
                    return 'button-grid-edit';
                }
                return '';
            },
            tooltip: 'Editar Tramo',
            handler: function(grid, rowIndex, colIndex) {
                
                var rec = storeTramos.getAt(rowIndex);
                editarTramo(rec);
            }
        }); 
        
        //eliminar tramo
        var btnEliminarTramo = Ext.create('Ext.button.Button', {
            text: 'Eliminar',
            scope: this,
            style: {
                marginTop: '10px'
            },
            getClass: function(v, meta, rec)
            {
                var strEstado = rec.get('estado');
                //valido para que no se pueda editar ni el primer ni ultimo tramo
                var final = storeTramos.totalCount ;
                var mostrar = true;
                if(final == rec.get('level') || 1 == rec.get('level'))
                {
                    mostrar = false;    
                }
                
                if (strEstado !== 'Eliminado' && boolPermisoEliminar && mostrar)
                {
                    return 'button-grid-delete';
                }
                return '';
            },
            tooltip: 'Eliminar Tramo',
            handler: function(grid, rowIndex, colIndex) {
                var rec = storeTramos.getAt(rowIndex);
                eliminarTramo(rec);
            }
        });
        
        function eliminarTramo(rec)
        {
            Ext.Msg.confirm('Alerta', 'Se Eliminará el tramo seleccionado . Desea continuar?', function(btn) {
            if (btn === 'yes') 
            {
                Ext.get(document.body).mask('Eliminando Tramo...');
                Ext.Ajax.request({
                    url: urlEliminarTramo,
                    method: 'post',
                    params: {
                            idElementoA: rec.get('idElementoA'),
                            idElementoB: rec.get('idElementoB'),
                            idTramo: rec.get('idRelacion'),
                            idRuta: idRuta
                    },
                    success: function(response)
                    {
                        Ext.get(document.body).unmask();
                        var json = Ext.JSON.decode(response.responseText);
                        Ext.Msg.alert('Mensaje', json.strMessageStatus);
                        storeTramos.load({params: {
                        intElemento: idRuta
                        }});     
                    },
                    failure: function(result)
                    {
                        Ext.get(document.body).unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
            });
        }

        function editarTramo(rec)
        {
            var intIdTipoElemento   =  objIdTipoElemento.getValue();
            var intIdModeloElemento =  objIdModeloElemento.getValue();
            
            storeBuscaTipoElemento.loadData([],false);
            storeBuscaTipoElemento.proxy.extraParams = 
            {
                tipoElemento: intIdTipoElemento,
                tipoInfraestructura: intIdModeloElemento,
                estado: 'Activo'
            };
            storeBuscaTipoElemento.load();
            
            var formTramo = Ext.create('Ext.form.Panel', {
                height: 350,
                width: 380,
                bodyPadding: 10,
                items: [
                    {
                        id: 'editTipoFibra',
                        name: 'editTipoFibra',
                        xtype: 'combobox',
                        fieldLabel: 'Tipo Fibra',
                        displayField: 'nombreClaseTipoMedio',
                        valueField: 'idClaseTipoMedio',
                        queryMode: "local",
                        triggerAction: 'all',
                        selectOnFocus: true,
                        allowBlank: false,
                        loadingText: 'Buscando...',
                        hideTrigger: false,
                        store: storeClaseTipoMedio,
                        listClass: 'x-combo-list-small',
                        disabled: true,
                        width: 330,
                    },                       
                    {
                        xtype: 'fieldset',
                        title: 'Elemento Inicio',
                        defaultType: 'textfield',
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: 'anchor',
                        defaults: {
                            width: 370
                        },
                        items: [
                            {
                                id: 'editTipoElementoA',
                                fieldLabel: 'Tipo',
                                xtype: 'combobox',
                                typeAhead: true,
                                displayField: 'nombreTipoElemento',
                                valueField: 'idTipoElemento',
                                queryMode: "local",
                                store: storeBuscaTipoElemento,
                                listClass: 'x-combo-list-small',
                                width: 330,
                                listeners: {
                                    select: function(combo, record, index)
                                    {
                                        Ext.getCmp('editNombreElementoA').setRawValue('');
                                        Ext.getCmp('editNombreElementoA').value = ''; 
                                        storeBuscaElemento.loadData([],false);
                                        storeBuscaElemento.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: combo.rawValue,
                                            estado: 'Activo',
                                            nuevaRuta: 'S',
                                            registroUnico: 'S'
                                        };
                                        storeBuscaElemento.load();
                                    },
                                },
                            },
                            {
                                id: 'editNombreElementoA',
                                name: 'editNombreElementoA',
                                xtype: 'combobox',
                                fieldLabel: 'Elemento',
                                typeAhead: true,
                                displayField: 'nombreElemento',
                                valueField: 'idElemento',
                                queryMode: "remote",
                                triggerAction: 'all',
                                selectOnFocus: true,
                                allowBlank: false,
                                loadingText: 'Buscando...',
                                hideTrigger: false,
                                store: storeBuscaElemento,
                                listClass: 'x-combo-list-small',
                                minChars: 3,
                                width: 330,
                                listeners: {
                                    beforerender: function() {
                                        storeBuscaElemento.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: Ext.getCmp('editTipoElementoA').getRawValue(),
                                            estado: 'Activo',
                                            registroUnico: 'S'
                                        };
                                    },
                                    select: function(combo, record, index)
                                    {
                                        Ext.getCmp('editTipoFibra').setDisabled(false);
                                    }
                                }
                            },
                            
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Elemento Final',
                        defaultType: 'textfield',
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: 'anchor',
                        defaults: {
                            width: 370
                        },
                        items: [
                            {
                                name: 'editTipoElementoB',
                                id: 'editTipoElementoB',
                                fieldLabel: 'Tipo',
                                xtype: 'combobox',
                                typeAhead: true,
                                displayField: 'nombreTipoElemento',
                                valueField: 'idTipoElemento',
                                queryMode: "local",
                                store: storeBuscaTipoElemento,
                                listClass: 'x-combo-list-small',
                                width: 330,
                                listeners: {
                                    select: function(combo, record, index)
                                    {
                                        Ext.getCmp('editNombreElementoB').setRawValue('');
                                        Ext.getCmp('editNombreElementoB').value = '';
                                        storeBuscaElementoFin.loadData([],false);
                                        storeBuscaElementoFin.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: combo.rawValue,
                                            estado: 'Activo',
                                            nuevaRuta: 'S'
                                        };
                                        storeBuscaElementoFin.load();
                                    },
                                },
                            },
                            {
                                name: 'editNombreElementoB',
                                id: 'editNombreElementoB',
                                xtype: 'combobox',
                                typeAhead: true,
                                fieldLabel: 'Elemento',
                                displayField: 'nombreElemento',
                                valueField: 'idElemento',
                                queryMode: "remote",
                                triggerAction: 'all',
                                selectOnFocus: true,
                                loadingText: 'Buscando...',
                                hideTrigger: false,
                                allowBlank: false,
                                store: storeBuscaElementoFin,
                                listClass: 'x-combo-list-small',                                
                                minChars: 3,
                                width: 330,
                                listeners: {
                                    beforerender: function() {
                                        storeBuscaElementoFin.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: Ext.getCmp('editTipoElementoB').getRawValue(),
                                            estado: 'Activo'
                                        };
                                    },
                                    select: function(combo, record, index)
                                    {
                                        Ext.getCmp('editTipoFibra').setDisabled(false);
                                    }
                                }
                            },
                        ]
                    },                    
                ]
            });
            
            //lleno los campos
            Ext.getCmp('editTipoElementoA').setRawValue(rec.get('tipoElementoA'));     
            Ext.getCmp('editNombreElementoA').setRawValue(rec.get('nombreElementoA'));   
            Ext.getCmp('editTipoElementoB').setRawValue(rec.get('tipoElementoB'));   
            Ext.getCmp('editNombreElementoB').setRawValue(rec.get('nombreElementoB'));           
            Ext.getCmp('editTipoFibra').setRawValue(rec.get('claseTipoMedio'));  

            btnregresar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    windowVerTramo.destroy();
                }
            });

            var windowVerTramo = Ext.widget('window', {
                title: 'Edición de tramo ' ,
                id: 'windowVerTramo',
                name: 'windowVerTramo',
                height: 320,
                width: 390,
                modal: true,
                resizable: false,
                closeAction: 'destroy',
                items: [formTramo],
                buttonAlign: 'center',
                buttons: [                
                    {
                        text: 'Guardar',
                        name: 'idBtnGuardarTramo',
                        id: 'idBtnGuardarTramo',
                        disabled: false,
                        handler: function() {
                            var form = formTramo.getForm();
                            if (form.isValid())
                            {
                                Ext.get('windowVerTramo').mask('Editando datos...');
                                Ext.Ajax.request({
                                    url: url_EditTramo,
                                    method: 'POST',
                                    params: {
                                        idElementoA: Ext.getCmp('editNombreElementoA').value,
                                        idElementoB: Ext.getCmp('editNombreElementoB').value,
                                        idTipoFibra: Ext.getCmp('editTipoFibra').value,
                                        idTramo: rec.get('idRelacion'),
                                        idRuta: idRuta
                                    },
                                    success: function(response) {
                                        Ext.get('windowVerTramo').unmask();
                                        var json = Ext.JSON.decode(response.responseText);
                                        Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                        storeTramos.load({params: {
                                            intElemento: idRuta
                                        }});     
                                        windowVerTramo.destroy();
                                    },
                                    failure: function(result) {
                                        Ext.get('windowVerTramo').unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }

                        }
                    },
                btnregresar]
            });
            windowVerTramo.show();
        }
        
        //agregar tramo
        var btnAgregarTramo = Ext.create('Ext.button.Button', {
            text: 'Editar',
            scope: this,
            style: {
                marginTop: '10px'
            },
            getClass: function(v, meta, rec)
            {
                var strEstado = rec.get('estado');
                //valido para que no se pueda editar ni el primer ni ultimo tramo
                var final = storeTramos.totalCount;

                var mostrar = true;
                if(final == rec.get('level'))
                {
                    mostrar = false;    
                }
                
                if (strEstado !== 'Eliminado' && boolPermisoAgregarTramo && mostrar)
                {
                    return 'button-grid-crearSolicitud';
                }
                return '';
            },
            tooltip: 'Agregar Tramo',
            handler: function(grid, rowIndex, colIndex) {
                
                var rec = storeTramos.getAt(rowIndex);
                agregarTramo(rec);
            }
        });
        
        function agregarTramo(rec)
        {
            var intIdTipoElemento   =  objIdTipoElemento.getValue();
            var intIdModeloElemento =  objIdModeloElemento.getValue();

            storeBuscaTipoElemento.loadData([],false);
            storeBuscaTipoElemento.proxy.extraParams = 
            {
                tipoElemento: intIdTipoElemento,
                tipoInfraestructura: intIdModeloElemento,
                estado: 'Activo'
            };
            storeBuscaTipoElemento.load();

            var formTramoAdd = Ext.create('Ext.form.Panel', {
                height: 350,
                width: 380,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Elemento Nuevo',
                        defaultType: 'textfield',
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: 'anchor',
                        defaults: {
                            width: 370
                        },
                        items: [
                            {
                                id: 'editTipoElementoA',
                                fieldLabel: 'Tipo',
                                xtype: 'combobox',
                                typeAhead: true,
                                displayField: 'nombreTipoElemento',
                                valueField: 'idTipoElemento',
                                queryMode: "local",
                                store: storeBuscaTipoElemento,
                                listClass: 'x-combo-list-small',
                                width: 330,
                                listeners: {
                                    select: function(combo, record, index)
                                    {
                                        Ext.getCmp('addElemento').setRawValue('');
                                        Ext.getCmp('addElemento').value = '';
                                        Ext.getCmp('agregar').setDisabled(true);

                                        storeBuscaElemento.loadData([],false);
                                        storeBuscaElemento.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: combo.rawValue,
                                            estado: 'Activo',
                                            nuevaRuta: 'S',
                                            registroUnico: 'S'
                                        };
                                        storeBuscaElemento.load();
                                                                            
                                    },
                                },
                            },
                            {
                                id: 'addElemento',
                                name: 'addElemento',
                                xtype: 'combobox',
                                fieldLabel: 'Elemento',
                                typeAhead: true,
                                displayField: 'nombreElemento',
                                valueField: 'idElemento',
                                queryMode: "remote",
                                triggerAction: 'all',
                                selectOnFocus: true,
                                allowBlank: false,
                                loadingText: 'Buscando...',
                                hideTrigger: false,
                                store: storeBuscaElemento,
                                listClass: 'x-combo-list-small',
                                minChars: 3,
                                width: 330,
                                listeners: {
                                    afterRender: function() {
                                        Ext.getCmp('agregar').setDisabled(true);

                                        storeBuscaElemento.proxy.extraParams = 
                                        {
                                            strIdsCantonElemento: rec.get('idCantonElementoA') + "," + rec.get('idCantonElementoB'),
                                            tipoElemento: Ext.getCmp('editTipoElementoA').getRawValue(),
                                            registroUnico: 'S',
                                            estado: 'Activo'
                                        };
                                    }
                                }
                            },
                            {
                                id: 'addTipoFibra',
                                name: 'addTipoFibra',
                                xtype: 'combobox',
                                fieldLabel: 'Tipo Fibra',
                                displayField: 'nombreClaseTipoMedio',
                                valueField: 'idClaseTipoMedio',
                                queryMode: "local",
                                triggerAction: 'all',
                                selectOnFocus: true,
                                allowBlank: false,
                                loadingText: 'Buscando...',
                                hideTrigger: false,
                                store: storeClaseTipoMedio,
                                listClass: 'x-combo-list-small',
                                width: 330,
                                listeners: {
                                    select: function(combo, record, index)
                                    {
                                        if (Ext.getCmp('addElemento').value !== "")
                                        {
                                            Ext.getCmp('agregar').setDisabled(false);
                                        }
                                    },
                                },
                            },                              
                        ]
                    },                 
                ]
            });
            
           btnregresar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    windowAddTramo.destroy();
                }
            });

            var windowAddTramo = Ext.widget('window', {
                title: 'Agregar tramo ' ,
                id: 'windowAddTramo',
                name: 'windowAddTramo',
                height: 205,
                width: 390,
                modal: true,
                resizable: false,
                closeAction: 'destroy',
                items: [formTramoAdd],
                buttonAlign: 'center',
                buttons: [                
                    {
                        id: 'agregar',
                        name: 'agregar',
                        text: 'Agregar',
                        disabled: true,
                        handler: function() {

                            var form = formTramoAdd.getForm();
                            if (form.isValid())
                            {
                                Ext.get('windowAddTramo').mask('Editando datos...');
                                Ext.Ajax.request({
                                    url: url_addTramo,
                                    method: 'POST',
                                    params: {
                                        idElemento: Ext.getCmp('addElemento').value,
                                        tipoFibra: Ext.getCmp('addTipoFibra').value,
                                        idTramo: rec.get('idRelacion'),
                                        idRuta: idRuta
                                    },
                                    success: function(response) {
                                        Ext.get('windowAddTramo').unmask();
                                        var json = Ext.JSON.decode(response.responseText);
                                        Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                        storeTramos.load({params: {
                                            intElemento: idRuta
                                        }});     
                                        windowAddTramo.destroy();
                                    },
                                    failure: function(result) {
                                        Ext.get(document.body).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }

                        }
                    },
                btnregresar]
            });
            windowAddTramo.show();
        }

        var objComboTipoLugar = function() {

                return Ext.create('Ext.form.ComboBox', {
                    store:        storeClases,
                    queryMode:    'local',
                    displayField: 'nombreTipoElemento',
                    valueField:   'nombreTipoElemento',
                });
        };        

        var objComboTipo = function () {
            return Ext.create('Ext.form.ComboBox', {
                store:        objStoreTipo,
                queryMode:    'local',
                displayField: 'nombreModeloElemento',
                valueField:   'nombreModeloElemento',
            });
        };

        formEditElemento = Ext.create('Ext.form.Panel', {
            id: 'formEditElemento',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            title: 'Información del elemento',
            layout: {
                type: 'table',
                columns: 12,
                pack: 'center',
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    align: 'left',
                    valign: 'middle'
                }
            },
            buttonAlign: 'center',
            buttons: [
                {
                    text: 'Guardar',
                    name: 'btnGuardar',
                    id: 'idBtnGuardar',
                    disabled: false,
                    handler: function() {
                        var form = formEditElemento.getForm();
                        if (form.isValid())
                        {
                            var data = form.getValues();
                            Ext.get(document.body).mask('Editando datos...');
                            Ext.Ajax.request({
                                url: urlEdit,
                                method: 'POST',
                                params: data,
                                success: function(response) {
                                    Ext.get(document.body).unmask();
                                    var json = Ext.JSON.decode(response.responseText);
                                    Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                    store.load();
                                    windowEditarElemento.destroy();
                                },
                                failure: function(result) {
                                    Ext.get(document.body).unmask();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }

                    }
                },
                {
                    text: 'Cancelar',
                    handler: function() {
                        windowEditarElemento.destroy();
                    }
                }]
        });
        
        storeTramos.load({params: {
                intElemento: rec.get('idElemento')
            }});        
        
        var formTramos = Ext.create('Ext.grid.Panel', {
            width: 900,
            height: 280,
            store: storeTramos,
            loadMask: true,
            frame: false,
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            title: 'Tramos de la Ruta',
            viewConfig: {enableTextSelection: true},
            columns: [
                {
                    id: 'nombreTramo',
                    header: 'Nombre',
                    dataIndex: 'nombreTramo',
                    width: 220,
                    sortable: true
                },
                {
                    id: 'nombreElementoA',
                    header: 'Elemento Inicio',
                    dataIndex: 'nombreElementoA',
                    width: 160,
                    sortable: true
                },      
                {
                    id: 'tipoElementoA',
                    header: 'Tipo',
                    dataIndex: 'tipoElementoA',
                    width: 100,
                    sortable: true
                },   
                {
                    id: 'nombreElementoB',
                    header: 'Elemento Fin',
                    dataIndex: 'nombreElementoB',
                    width: 160,
                    sortable: true
                },   
                {
                    id: 'tipoElementoB',
                    header: 'Tipo',
                    dataIndex: 'tipoElementoB',
                    width: 100,
                    sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 100,
                    items: [
                        btnEditarTramo,
                        btnAgregarTramo,
                        btnEliminarTramo
                    ]
                }                
            ]
        });        

        var windowEditarElemento = Ext.widget('window', {
            title: 'Editar Elemento',
            id: 'windowEditarElemento',
            height: 525,
            width: 850,
            modal: true,
            resizable: false,
            closeAction: 'destroy',
            items: [
                    formTramos,
                    formEditElemento],
            buttonAlign: 'center'
        });
        windowEditarElemento.show();
        
        var intWidth = 325;
        
        var objIdElemento = Utils.objText();
        objIdElemento.style = Utils.GREY_BOLD_COLOR;
        objIdElemento.id = 'objIdElemento';
        objIdElemento.name = 'objIdElemento';
        objIdElemento.fieldLabel = "*Nombre";
        objIdElemento.colspan = 6;
        objIdElemento.hidden = true;
        objIdElemento.setValue(rec.get('idElemento'));
        
        var objIdTipoElemento = Utils.objText();
        objIdTipoElemento.style = Utils.GREY_BOLD_COLOR;
        objIdTipoElemento.id = 'objIdTipoElemento';
        objIdTipoElemento.name = 'objIdTipoElemento';
        objIdTipoElemento.fieldLabel = "Id Tipo de Elemento Edit";
        objIdTipoElemento.colspan = 6;
        objIdTipoElemento.hidden = true;
        objIdTipoElemento.setValue(rec.get('idTipoElemento'));
        
        var objIdModeloElemento = Utils.objText();
        objIdModeloElemento.style = Utils.GREY_BOLD_COLOR;
        objIdModeloElemento.id = 'objIdModeloElemento';
        objIdModeloElemento.name = 'objIdModeloElemento';
        objIdModeloElemento.fieldLabel = "Id Modelo Edit";
        objIdModeloElemento.colspan = 6;
        objIdModeloElemento.hidden = true;
        objIdModeloElemento.setValue(rec.get('modeloElementoId'));
        
        var objTxtNombreElemento = Utils.objText();
        objTxtNombreElemento.style = Utils.GREY_BOLD_COLOR;
        objTxtNombreElemento.id = 'objTxtNombreElemento';
        objTxtNombreElemento.name = 'objTxtNombreElemento';
        objTxtNombreElemento.fieldLabel = "*Nombre";
        objTxtNombreElemento.colspan = 6;
        objTxtNombreElemento.width = intWidth;
        objTxtNombreElemento.allowBlank = false;
        objTxtNombreElemento.blankText = 'Ingrese nombre por favor';
        objTxtNombreElemento.setValue(rec.get('nombreElemento'));

        var objCmbDetalle = objComboTipoLugar();
        objCmbDetalle.style = Utils.GREY_BOLD_COLOR;
        objCmbDetalle.id = 'objCmbDetalle';
        objCmbDetalle.name = 'objCmbDetalle';
        objCmbDetalle.fieldLabel = "*Tipo de Ruta";
        objCmbDetalle.readOnly = true;
        objCmbDetalle.colspan = 6;
        objCmbDetalle.width = intWidth;
        objCmbDetalle.allowBlank = false;
        objCmbDetalle.blankText = 'Ingrese propietario por favor';
        objCmbDetalle.setValue(rec.get('propietario'));
        objCmbDetalle.setRawValue(rec.get('detalleValor'));

        var objTarDescripcionElemento = Utils.objTextArea();
        objTarDescripcionElemento.style = Utils.GREY_BOLD_COLOR;
        objTarDescripcionElemento.id = 'objTarDescripcionElemento';
        objTarDescripcionElemento.name = 'objTarDescripcionElemento';
        objTarDescripcionElemento.fieldLabel = "*Descripción";
        objTarDescripcionElemento.colspan = 6;
        objTarDescripcionElemento.width = intWidth;
        objTarDescripcionElemento.allowBlank = false;
        objTarDescripcionElemento.blankText = 'Ingrese descripción por favor';
        objTarDescripcionElemento.setValue(rec.get('descripcionElemento'));

        var objCmbModelo = objComboTipo();
        objCmbModelo.style = Utils.GREY_BOLD_COLOR;
        objCmbModelo.id = 'objCmbModelo';
        objCmbModelo.name = 'objCmbModelo';
        objCmbModelo.fieldLabel = "*Tipo de Infraestructura";
        objCmbModelo.readOnly = true;
        objCmbModelo.colspan = 6;
        objCmbModelo.width = intWidth;
        objCmbModelo.allowBlank = false;
        objCmbModelo.blankText = 'Ingrese tipo por favor';
        objCmbModelo.forceSelection= true;
        objCmbModelo.setValue(rec.get('modeloElementoId'));
        objCmbModelo.setRawValue(rec.get('nombreModelo'));  
         
         
        formEditElemento.add(objTxtNombreElemento);
        formEditElemento.add(objCmbDetalle);
        formEditElemento.add(objTarDescripcionElemento);
        formEditElemento.add(objCmbModelo);
        formEditElemento.add(objIdElemento);  
        formEditElemento.add(objIdTipoElemento);
        formEditElemento.add(objIdModeloElemento);
          
    };

    var btnEditarPoste = Ext.create('Ext.button.Button', {
        text: 'Editar',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            var strEstado = rec.get('estado');
            var strTipoModelo = rec.get('nombreModelo');
            if (strEstado !== 'Eliminado' && boolPermisoEditar 
                && strTipoModelo !== 'NODO-ODF-MANGA-ODF-NODO'
                && strTipoModelo !== 'MANGA-MANGA-ODF-NODO')
            {
                return 'button-grid-edit';
            }
            return '';
        },
        tooltip: 'Editar Elemento',
        handler: editarPoste
    });
    
    var eliminarPoste = function(rec) {
        var intIdElemento = rec.get('idElemento');
        var strNombreElemento = rec.get('nombreElemento');
        var strEstado = rec.get('estado');
        Ext.Msg.confirm('Alerta', 'Se Eliminará : ' + strNombreElemento + ' . Desea continuar?', function(btn) {
            if (btn === 'yes') {
                if (strEstado !== 'Eliminado') {

                    Ext.get(document.body).mask('Eliminando Poste...');
                    Ext.Ajax.request({
                        url: urlDeletePoste,
                        method: 'post',
                        params: {
                            idElemento: intIdElemento
                        },
                        success: function(response)
                        {
                            Ext.get(document.body).unmask();
                            var json = Ext.JSON.decode(response.responseText);
                            Ext.Msg.alert('Mensaje', json.strMessageStatus);
                            store.load();
                        },
                        failure: function(result)
                        {
                            Ext.get(document.body).unmask();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                } else {
                    alert('Error - (' + strNombreElemento + ') Solo se puede eliminar una solicitud en estado: ');
                }
            }
        });
    };

    var btnEliminarPoste = Ext.create('Ext.button.Button', {
        text: 'Eliminar',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            var strEstado = rec.get('estado');
            var strTipoModelo = rec.get('nombreModelo');
            if (strEstado !== 'Eliminado' && boolPermisoEliminar
                && strTipoModelo !== 'NODO-ODF-MANGA-ODF-NODO'
                && strTipoModelo !== 'MANGA-MANGA-ODF-NODO')
            {
                return 'button-grid-delete';
            }
            return '';
        },
        tooltip: 'Eliminar',
        handler: function(grid, rowIndex, colIndex) {
            var rec = store.getAt(rowIndex);
            eliminarPoste(rec);
        }
    });
    
    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getEncontrados,
            timeout: 800000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                sltTipoElemento:'RUTA',
                strEstado : 'Activo'
            },
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'estado', mapping: 'estado'},
                {name: 'nombreModelo', mapping: 'nombreModelo'},
                {name: 'descripcionElemento', mapping: 'descripcionElemento'},
                {name: 'modeloElementoId', mapping: 'idModelo'},
                {name: 'detalleValor', mapping: 'detalleValor'},
                {name: 'idTipoElemento', mapping: 'idTipoElemento'}
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    gridPostes = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true},
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [  
                    {xtype: 'tbfill'},
                    {
                        text: 'Subir Rutas Excel',
                        cls: 'button-docked-items-custom',
                        itemId: 'subir',
                        scope: this,
                        handler: function() {
                            var permiso = $("#ROLE_400-8077");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Alerta ', 'No tiene permisos para realizar esta accion');
                            }
                            else
                            {
                                subir();
                            }
                        }
                    }
                ]}
        ],
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'elemento',
                header: 'Elemento',
                xtype: 'templatecolumn',
                width: 350,
                tpl: '<span class="box-detalle"> {nombreElemento}</span>\n\
                        <span class="bold">Tipo de Ruta:</span><span> {detalleValor}</span></br>\n\\n\ '

            },
            {
                id: 'modelo',
                header: 'Tipo de Infraestructura',
                dataIndex: 'nombreModelo',
                width: 200,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 275,
                items: [
                    btnVerPoste,
                    btnEditarPoste,
                    btnEliminarPoste
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 930,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '5%', border: false},
            {
                xtype: 'textfield',
                id: 'sltNombreElemento',
                name: 'sltNombreElemento',
                fieldLabel: 'Nombre elemento',
                value: '',
                width: '40%'
            },            
            {
                xtype: 'combobox',
                id: 'sltModeloElemento',
                fieldLabel: 'Tipo de Infraestructuras',
                displayField: 'nombreModeloElemento',
                valueField: 'idModeloElemento',
                loadingText: 'Buscando ...',
                store: objStoreTipo,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '40%'
            },
            {width: '10%', border: false},
            {width: '5%', border: false},
            {
                xtype: 'combobox',
                id: 'sltClase',
                name: 'sltClase',
                fieldLabel: 'Tipo de Ruta',
                displayField: 'nombreTipoElemento',
                valueField: 'idTipoElemento',
                loadingText: 'Buscando ...',
                store: storeClases,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '40%',
                listeners: {
                    select: function(combo)
                    {
                        var strTipoElemento = Ext.getCmp('sltClase').getRawValue();
                        
                        Ext.getCmp('sltModeloElemento').value = "";
                        Ext.getCmp('sltModeloElemento').setRawValue("");
                                        
                        objStoreTipo.loadData([],false);
                        objStoreTipo.proxy.extraParams = 
                        {
                            tipoElemento: strTipoElemento, 
                        };
                        objStoreTipo.load();
                    },
                },
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: '',
                store: [
                    ['', 'Todos'],
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado'],
                    ['Eliminado', 'Eliminado']
                ],
                width: '40%'
            },
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });


});

function buscar() {

        store.getProxy().extraParams.sltNombreElemento = Ext.getCmp('sltNombreElemento').value;
        store.getProxy().extraParams.sltClase = Ext.getCmp('sltClase').value;
        store.getProxy().extraParams.sltModeloElemento = Ext.getCmp('sltModeloElemento').value;
        store.getProxy().extraParams.strEstado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.sltTipoElemento = '';
        store.getProxy().extraParams.intElemento = '';
        store.load();

}

function limpiar() {
    Ext.getCmp('sltNombreElemento').value = "";
    Ext.getCmp('sltNombreElemento').setRawValue("");

    Ext.getCmp('sltClase').value = "";
    Ext.getCmp('sltClase').setRawValue("");

    Ext.getCmp('sltModeloElemento').value = "";
    Ext.getCmp('sltModeloElemento').setRawValue("");

    Ext.getCmp('sltEstado').value = "";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.sltNombreElemento = Ext.getCmp('sltNombreElemento').value;
    store.getProxy().extraParams.sltClase = Ext.getCmp('sltClase').value;
    store.getProxy().extraParams.sltModeloElemento = Ext.getCmp('sltModeloElemento').value;
    store.getProxy().extraParams.strEstado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.sltTipoElemento = '';
    store.getProxy().extraParams.intElemento = '';
    store.load();
}

function nuevaRuta()
{
        var rec;
        var record;
        
        objStoreTipo.removeAll();
                
        var storeElementoContenido = new Ext.data.Store({            
                total: 'total',
                async: false,
                proxy:
                    {                    
                        type: 'ajax',
                        url:  '',
                        reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'encontrados'
                        },
                        extraParams:
                        {   
                            idNodo:          '',
                            strTipoElemento: 'POSTE'
                        }
                    },
                fields:
                    [
                        {name:'idElemento',       mapping: 'idElemento'},
                        {name:'idModeloElemento', mapping: 'idModeloElemento'},
                        {name:'nombreElemento',   mapping: 'nombreElemento'},
                        {name:'modeloElemento',   mapping: 'modeloElemento'},
                        {name:'tipoElemento',     mapping: 'tipoElemento'},
                        {name:'estado',           mapping: 'estado'}                   
                    ]
            });

        Ext.define('elementoContenidoModelo', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id',                mapping: 'id'},
                {name:'idElemento',        mapping: 'idElemento'},
                {name:'idModeloElemento',  mapping:'idModeloElemento'},
                {name:'nombreElemento',    mapping: 'nombreElemento'},
                {name:'modeloElemento',    mapping: 'modeloElemento'},
                {name:'nombreTipoElemento',mapping: 'tipoElemento'},
                {name:'tipoFibra',         mapping: 'tipoFibra'},                
                {name:'idTipoFibra',       mapping: 'idTipoFibra'}, 
                {name:'estado',            mapping: 'estado'},
                {name:'nuevo',             mapping: 'nuevo'}
            ]
        });
        
        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                edit: function(){                
                    gridElementoContenido.getView().refresh();
                }
            }
        });
        
        var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) 
                {   
                    gridElementoContenido.down('#btnQuitar').setDisabled(selections.length === 0);
                    
                }
            }
        });
    
        var toolbarElementosContenidos = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items:
                [{xtype: 'tbfill'},
                    {
                        xtype: 'button',
                        id: "btnAgregar",
                        iconCls: "icon_anadir",
                        text: 'Agregar',
                        scope: this,
                        handler: function() {
                            var strIdsCantonElemento = "";
                            if(Ext.isEmpty(objCmbTipoLugar.getValue()))
                            {
                                Ext.Msg.alert('Alerta!', 'Para agregar elemento debe seleccionar un tipo de Ruta.');
                                return;
                            }
                            else
                            {
                                if(Ext.isEmpty(objTxtNombreElemento.getValue()))
                                {
                                    Ext.Msg.alert('Alerta!', 'Para agregar elemento debe ingresar un Nombre');
                                    return;
                                }
                                if(Ext.isEmpty(objCmbTipoElemento.getValue()))
                                {
                                    Ext.Msg.alert('Alerta!', 'Para agregar elemento debe seleccionar un tipo de Infraestructura');
                                    return;
                                }
                                if (Ext.isEmpty(objIdCantonNodoInicio.getValue()) ||  Ext.isEmpty(objCmbElementoIni.getValue()))
                                {
                                    Ext.Msg.alert('Alerta!', 'Para agregar elemento debe seleccionar el Elemento Inicio.');
                                    return;
                                }

                                if (Ext.isEmpty(objIdCantonNodoFin.getValue()) ||  Ext.isEmpty(objCmbElementoFin.getValue()))
                                {
                                    Ext.Msg.alert('Alerta!', 'Para agregar elemento debe seleccionar el Elemento Fin.');
                                    return;
                                }

                                strIdsCantonElemento = objIdCantonNodoInicio.getValue() + "," + objIdCantonNodoFin.getValue();
                                
                            }
                            
                            var strTipoRuta            =  objCmbTipoLugar.getValue();
                            var strTipoInfraestructura =  objCmbTipoElemento.getValue();
                            
                            storeBuscaTipoElemento.loadData([],false);
                            storeBuscaTipoElemento.proxy.extraParams = 
                            {
                                tipoElemento: strTipoRuta,
                                tipoInfraestructura: strTipoInfraestructura,
                                estado: 'Activo'
                            };
                            storeBuscaTipoElemento.load();
                            
                            var formRutaAdd = Ext.create('Ext.form.Panel', {
                                height: 370,
                                width: 380,
                                bodyPadding: 10,
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        title: 'Elemento Nuevo',
                                        defaultType: 'textfield',
                                        style: "font-weight:bold; margin-bottom: 5px;",
                                        layout: 'anchor',
                                        defaults: {
                                            width: 370
                                        },
                                        items: [
                                            {
                                                id: 'cbTipoElemento',
                                                name: 'cbTipoElemento',
                                                fieldLabel: 'Tipo',
                                                xtype: 'combobox',
                                                typeAhead: true,
                                                displayField: 'nombreTipoElemento',
                                                valueField: 'idTipoElemento',
                                                queryMode: "local",
                                                store: storeBuscaTipoElemento,
                                                listClass: 'x-combo-list-small',
                                                width: 330,
                                                listeners: {
                                                    select: function(combo, record, index)
                                                    {
                                                        Ext.getCmp('agregar').setDisabled(true);

                                                        if(combo.rawValue == 'MANGA' || combo.rawValue == 'CDP'
                                                           || combo.rawValue == 'CAJA DISPERSION' || combo.rawValue == 'RESERVA'
                                                           || combo.rawValue == 'ODF')
                                                        {
                                                            Ext.getCmp('addTipoFibra').enable();
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('addTipoFibra').disable();
                                                        }
                                                        
                                                        Ext.getCmp('addElemento').setRawValue('');
                                                        Ext.getCmp('addElemento').value = '';
                                                        
                                                        storeBuscaElemento.removeAll();
                                                        storeBuscaElemento.load({params:{
                                                            strIdsCantonElemento: strIdsCantonElemento,
                                                            tipoElemento: combo.rawValue,
                                                            estado: 'Activo',
                                                            registroUnico: 'S',
                                                            nuevaRuta: 'S'
                                                        }});
                                                    },
                                                },
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'addElemento',
                                                name: 'addElemento',
                                                fieldLabel: 'Elemento',
                                                valueField: 'idElemento',
                                                displayField: 'nombreElemento',
                                                queryMode: "local",
                                                allowBlank: false,
                                                store: storeBuscaElemento,
                                                width: 330,
                                                listeners: {
                                                    afterRender: function() {
                                                        storeBuscaElemento.proxy.extraParams = 
                                                        {
                                                            registroUnico: 'S'
                                                        };
                                                    },
                                                    select: function(combo, rec) {
                                                        Ext.getCmp('agregar').setDisabled(true);
                                                        record = rec[0].data;
                                                    }
                                                }
                                            },
                                            {
                                                id: 'addTipoFibra',
                                                name: 'addTipoFibra',
                                                xtype: 'combobox',
                                                fieldLabel: 'Tipo Fibra',
                                                displayField: 'nombreClaseTipoMedio',
                                                valueField: 'idClaseTipoMedio',
                                                queryMode: "local",
                                                triggerAction: 'all',
                                                selectOnFocus: true,
                                                allowBlank: false,
                                                loadingText: 'Buscando...',
                                                hideTrigger: false,
                                                disabled: true,
                                                store: storeClaseTipoMedio,
                                                listClass: 'x-combo-list-small',
                                                width: 330,
                                                listeners: {
                                                    select: function(combo, record, index)
                                                    {
                                                        if (Ext.getCmp('addElemento').value !== "")
                                                        {
                                                            Ext.getCmp('agregar').setDisabled(false);
                                                        }    
                                                    },
                                                },
                                            },                                            
                                        ]
                                    },
                                ]
                            });

                            btnregresar = Ext.create('Ext.Button', {
                                text: 'Cerrar',
                                cls: 'x-btn-rigth',
                                handler: function() {
                                    windowAddGrid.destroy();
                                }
                            });

                            var windowAddGrid = Ext.widget('window', {
                                title: 'Agregar Elemento ',
                                id: 'windowAddGrid',
                                height: 210,
                                width: 390,
                                modal: true,
                                resizable: false,
                                closeAction: 'destroy',
                                items: [formRutaAdd],
                                buttonAlign: 'center',
                                buttons: [
                                    {
                                        text: 'Agregar',
                                        id: 'agregar',
                                        name: 'agregar',
                                        disabled: true,
                                        handler: function() {
                                            var form = formRutaAdd.getForm();
                                            
                                            if (form.isValid())
                                            {
                                                if (record === null || typeof record === 'undefined') {
                                                    Ext.Msg.alert("Advertencia","Por favor vuelva a seleccionar el elemento.");
                                                    return;
                                                }

                                                var index = storeElementoContenido.findBy(function (dataStore) {
                                                    return dataStore.data.idElemento == record.idElemento;
                                                });

                                                if (index >= 0) {
                                                    Ext.Msg.alert("Advertencia","Ya ingreso información de " + record.nombreElemento);
                                                    return;
                                                }
                                                
                                                var intIdElementoIni = objCmbElementoIni.getValue();
                                                var intIdElementoFin = objCmbElementoFin.getValue();
                                                
                                                var r = Ext.create('elementoContenidoModelo', {
                                                    idElemento: record.idElemento,
                                                    idModeloElemento: '0',
                                                    nombreElemento: record.nombreElemento,
                                                    modeloElemento: record.modeloElemento,
                                                    idTipoElemento: '',
                                                    nombreTipoElemento: Ext.getCmp('cbTipoElemento').getRawValue(),
                                                    tipoFibra: Ext.getCmp('addTipoFibra').getRawValue(),
                                                    idTipoFibra: Ext.getCmp('addTipoFibra').getValue(),                                                    
                                                    estado: '',
                                                    id: '0',
                                                    nuevo: '1'
                                                });
                                                
                                                if (intIdElementoIni === Ext.getCmp('addElemento').getValue())
                                                {
                                                    Ext.Msg.alert("Advertencia","Ya se encuentra como elemento Inicio");
                                                    return;
                                                }
                                                
                                                if(intIdElementoFin === Ext.getCmp('addElemento').getValue())
                                                {
                                                    Ext.Msg.alert("Advertencia","Ya se encuentra como elemento Fin");
                                                    return;
                                                }
                                                
                                                var intCountGridElementos = gridElementoContenido.getStore().getCount();

                                                if (intCountGridElementos === 0)
                                                {
                                                    storeElementoContenido.insert(0, r);
                                                }

                                                if (intCountGridElementos >= 1)
                                                {
                                                    if ('' === gridElementoContenido.getStore().getAt(0).data.modeloElemento.trim())
                                                    {
                                                        Ext.Msg.alert('Alerta!', 'Debe ingresar la información del elemento.');
                                                    }
                                                    else
                                                    {
                                                        storeElementoContenido.insert(intCountGridElementos, r);
                                                    }
                                                }
                                                record = null;
                                                windowAddGrid.destroy();
                                            }

                                        }
                                    },
                                    btnregresar]
                            });
                            windowAddGrid.show();
                        }  
                    },
                    {
                        xtype:    'button',
                        id:       "btnQuitar",
                        iconCls:  "icon_remover",
                        text:     'Quitar',
                        scope:    this,
                        disabled: true,
                        handler: function (){
                            var intCountGridElementos = gridElementoContenido.getStore().getCount();
                            if (intCountGridElementos !== 0)
                                {
                                    if (selEspacioModelo.getSelection().length > 0)
                                        {
                                            var arraySeleccionados = new Array();
                                            var arraySeleccionados1 = new Array();
                                            var arraySeleccionados2 = new Array();
                                            var strJsonElementosEliminarB = '';

                                            var intX = 0;
                                            var intY = 0;
                                            for (var i = 0; i < selEspacioModelo.getSelection().length; i++) 
                                            {  
                                                if(selEspacioModelo.getSelection()[i].data.nuevo !== '1')
                                                {
                                                   arraySeleccionados[intX] = selEspacioModelo.getSelection()[i].data.idElemento;
                                                   arraySeleccionados1[intX] = selEspacioModelo.getSelection()[i];
                                                   intX++;
                                                }
                                                else
                                                {
                                                    arraySeleccionados2[intY] = selEspacioModelo.getSelection()[i];
                                                    intY++;
                                                }
                                            }
                                            if (arraySeleccionados2!== null || arraySeleccionados2.lenght >0)
                                            {
                                                gridElementoContenido.getStore().remove(arraySeleccionados2);
                                            }
                                            
                                            if (arraySeleccionados!== null && arraySeleccionados.length >0 )
                                            {
                                                strJsonElementosEliminarB = Ext.JSON.encode(arraySeleccionados);
                                                Ext.get(document.body).mask('Guardando datos...');
                                                Ext.Ajax.request({
                                                    url :    urlEliminaRelacionElementoSave,
                                                    method : 'POST',
                                                    params :
                                                    {
                                                        idElementoA:  '',   
                                                        strElemntosB: strJsonElementosEliminarB
                                                    },
                                                    success:function(response)
                                                    {
                                                        Ext.get(document.body).unmask();
                                                        var json = Ext.JSON.decode(response.responseText);
                                                        Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                                        gridElementoContenido.getStore().remove(arraySeleccionados1);
                                                    },
                                                    failure:function(result)
                                                    {
                                                        Ext.get(document.body).unmask();
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });

                                            }
                                        }
                                }
                        }
                    }
                ]
        });
        
    
        var objComboTipoLugar = function() {

                return Ext.create('Ext.form.ComboBox', {
                    store:        storeClases,
                    queryMode:    'local',
                    displayField: 'nombreTipoElemento',
                    valueField:   'idTipoElemento',
                    listeners: {
                        select: function(records) {
                            
                            objTxtNombreElemento.setValue('');
                            objTarDescripcionElemento.setValue('');
                            objCmbElementoIni.setValue('');
                            objCmbElementoFin.setValue('');
                            objCmbClaseMedio.setValue('');
                            objCmbCanton.setValue('');
                            objCmbTipoElemento.setValue('');
                            objCmbTipoElemento.setRawValue('');

                            var strClase =  objCmbTipoLugar.getRawValue();
                            
                            objStoreTipo.loadData([],false);
                            objStoreTipo.proxy.extraParams = 
                            {
                                tipoElemento: strClase, 
                            };
                            objStoreTipo.load();
                            
                            objCmbElementoIni.enable();
                            objCmbElementoIni.show();
                            objCmbElementoFin.enable();
                            objCmbElementoFin.show();
                            objCmbCanton.disable();
                            objCmbCanton.hide();
                            
                            gridElementoContenido.getStore().removeAll();
                        }
                    }                    
                });
        };          
        

        var objComboTipo = function () {

            return Ext.create('Ext.form.ComboBox', {
                store:        objStoreTipo,
                queryMode:    'local',
                displayField: 'nombreModeloElemento',
                valueField:   'idModeloElemento',
                listeners: {
                        select: function(records) {
                            
                            var strTipoRuta            =  objCmbTipoLugar.getValue();
                            var strTipoInfraestructura =  objCmbTipoElemento.getValue();
                            
                            objCmbElementoIni.setValue('');
                            objCmbElementoIni.setRawValue('');
                            objCmbElementoFin.setValue('');
                            objCmbElementoFin.setRawValue('');
                                                                                    
                            storeElementoIni.loadData([],false);
                            storeElementoIni.proxy.extraParams = 
                            {
                                tipoElemento: strTipoRuta,
                                tipoInfraestructura: strTipoInfraestructura,
                                estado: 'Activo',
                                elementoTipo: 'Inicio'
                            };
                            storeElementoIni.load();
                            
                            storeElementoFin.loadData([],false);
                            storeElementoFin.proxy.extraParams = 
                            {
                                tipoElemento: strTipoRuta,
                                tipoInfraestructura: strTipoInfraestructura,
                                estado: 'Activo',
                                elementoTipo: 'Fin'
                            };
                            storeElementoFin.load();
                        }
                    }
            });
        };
        
        var objComboClaseMedio = function () {

            return Ext.create('Ext.form.ComboBox', {
                store:        storeClaseTipoMedio,
                queryMode:    'local',
                displayField: 'nombreClaseTipoMedio',
                valueField:   'idClaseTipoMedio',
            });
        };

        var storeCantones = Ext.create('Ext.data.Store', {
            id: 'storeIdCantones',
            proxy: {
                type: 'ajax',
                url: strUrlGetCantones,
                timeout: 600000,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    strFiltrarPorRegion: 'SI'
                }
            },
            fields: [
                {name:'nombre_canton', mapping:'nombre_canton'},
                {name:'id_canton', mapping:'id_canton'}
            ]
        }); 

        var objComboCanton = function () {
            return Ext.create('Ext.form.ComboBox', {
                store:        storeCantones,
                queryMode:    'remote',
                displayField: 'nombre_canton',
                valueField:   'id_canton',
                minChars: 3
            });
        };

        var storeElementoIni = new Ext.data.Store({
            id: 'storeElementoIni',
            pageSize: 100,
            proxy: {
                type: 'ajax',
                timeout: 400000,
                url: urlBuscarElemento,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
            },
            fields:
                [
                    {name: 'id_elemento', mapping: 'id'},
                    {name: 'nombre_elemento', mapping: 'nombre'},
                    {name: 'id_canton', mapping: 'idCanton'}
                ]
        });       

        var objComboElementoIni = function() {

            return Ext.create('Ext.form.ComboBox', {
                store: storeElementoIni,
                queryMode: 'remote',
                displayField: 'nombre_elemento',
                valueField: 'id_elemento',
                minChars: 3,
                listeners: {
                    select: function(combo, record, index) {
                        objIdCantonNodoInicio.setValue(record[0].data.id_canton);
                                         
                    }
                }
            });
        };

        var storeElementoFin = new Ext.data.Store({
            id: 'storeElementoFin',
            pageSize: 100,
            proxy: {
                type: 'ajax',
                timeout: 400000,
                url: urlBuscarElemento,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
            },
            fields:
                [
                    {name: 'id_elemento',       mapping: 'id'},
                    {name: 'nombre_elemento',   mapping: 'nombre'},
                    {name: 'id_canton',         mapping: 'idCanton'}
                ]
        });       

        var objComboElementoFin = function() {
            return Ext.create('Ext.form.ComboBox', {
                store: storeElementoFin,
                queryMode: 'remote',
                displayField: 'nombre_elemento',
                valueField: 'id_elemento',
                minChars: 3,
                listeners: {
                    select: function(combo, record, index) {
                        objIdCantonNodoFin.setValue(record[0].data.id_canton);
                    }
                }
            });
        };   
        
        
        //ingresar informacion de la ruta
        
        var formVerPoste = Ext.create('Ext.form.Panel', {
            id: 'formVerPoste',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            layout: {
                type: 'table',
                columns: 12,
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    style: ' padding: 5px;',
                    align: 'left',
                    valign: 'middle'
                }
            },
            items: []
        });
        
        var intWidth                    = 325;

        var objIdCantonNodoInicio           = Utils.objText();
        objIdCantonNodoInicio.style         = Utils.GREY_BOLD_COLOR;
        objIdCantonNodoInicio.id            = 'objIdCantonNodoInicio';
        objIdCantonNodoInicio.name          = 'objIdCantonNodoInicio';
        objIdCantonNodoInicio.fieldLabel    = "IdCantonNodoInicio";
        objIdCantonNodoInicio.hidden        = true;
        
        var objIdCantonNodoFin              = Utils.objText();
        objIdCantonNodoFin.style            = Utils.GREY_BOLD_COLOR;
        objIdCantonNodoFin.id               = 'objIdCantonNodoFin';
        objIdCantonNodoFin.name             = 'objIdCantonNodoFin';
        objIdCantonNodoFin.fieldLabel       = "IdCantonNodoFin";
        objIdCantonNodoFin.hidden           = true;

        var objTxtNombreElemento        = Utils.objText();
        objTxtNombreElemento.style      = Utils.GREY_BOLD_COLOR;
        objTxtNombreElemento.id         = 'objTxtNombreElemento';
        objTxtNombreElemento.name       = 'objTxtNombreElemento';
        objTxtNombreElemento.fieldLabel = "*Nombre"; 
        objTxtNombreElemento.colspan    = 6;
        objTxtNombreElemento.width      = intWidth;
        objTxtNombreElemento.allowBlank = false;
        objTxtNombreElemento.blankText  = 'Ingrese nombre por favor';
        
        var objTarDescripcionElemento        = Utils.objTextArea();
        objTarDescripcionElemento.style      = Utils.GREY_BOLD_COLOR;
        objTarDescripcionElemento.id         = 'objTarDescripcionElemento';
        objTarDescripcionElemento.name       = 'objTarDescripcionElemento';
        objTarDescripcionElemento.fieldLabel = "Descripción"; 
        objTarDescripcionElemento.colspan    = 6;
        objTarDescripcionElemento.width      = intWidth;
        objTarDescripcionElemento.blankText  = 'Ingrese descripción por favor';        

        var objCmbTipoLugar             = objComboTipoLugar();
        objCmbTipoLugar.style           = Utils.GREY_BOLD_COLOR;
        objCmbTipoLugar.id              = 'objCmbTipoLugar';
        objCmbTipoLugar.name            = 'objCmbTipoLugar';
        objCmbTipoLugar.fieldLabel      = "*Tipo de Ruta";        
        objCmbTipoLugar.colspan         = 6;
        objCmbTipoLugar.width           = intWidth; 
        objCmbTipoLugar.allowBlank      = false;
        objCmbTipoLugar.blankText       = 'Ingrese tipo lugar por favor';

        var objCmbTipoElemento        = objComboTipo();
        objCmbTipoElemento.style      = Utils.GREY_BOLD_COLOR;
        objCmbTipoElemento.id         = 'objCmbTipoElemento';
        objCmbTipoElemento.name       = 'objCmbTipoElemento';
        objCmbTipoElemento.fieldLabel = "*Tipo de Infraestructura"; 
        objCmbTipoElemento.colspan    = 6;
        objCmbTipoElemento.width      = intWidth; 
        objCmbTipoElemento.allowBlank = false;
        objCmbTipoElemento.blankText  = 'Ingrese tipo por favor';    
        
        var objCmbElementoIni        = objComboElementoIni();
        objCmbElementoIni.style      = Utils.GREY_BOLD_COLOR;
        objCmbElementoIni.id         = 'objCmbElementoIni';
        objCmbElementoIni.name       = 'objCmbElementoIni';
        objCmbElementoIni.fieldLabel = "Elemento Inicio"; 
        objCmbElementoIni.colspan    = 6;
        objCmbElementoIni.width      = intWidth;         
        objCmbElementoIni.blankText  = 'Ingrese tipo por favor'; 
        objCmbElementoIni.queryMode  = 'remote'; 
        objCmbElementoIni.lazyRender = true; 
        objCmbElementoIni.loadingText= 'Buscando...'; 
        objCmbElementoIni.minChars   = 3;        
        objCmbElementoIni.allowBlank = false;
        
        var objCmbElementoFin        = objComboElementoFin();
        objCmbElementoFin.style      = Utils.GREY_BOLD_COLOR;
        objCmbElementoFin.id         = 'objCmbElementoFin';
        objCmbElementoFin.name       = 'objCmbElementoFin';
        objCmbElementoFin.fieldLabel = "Elemento Fin";         
        objCmbElementoFin.colspan    = 6;
        objCmbElementoFin.width      = intWidth; 
        objCmbElementoFin.blankText  = 'Ingrese tipo por favor'; 
        objCmbElementoFin.queryMode  = 'remote'; 
        objCmbElementoFin.lazyRender = true; 
        objCmbElementoFin.loadingText= 'Buscando...'; 
        objCmbElementoFin.minChars   = 3; 
        objCmbElementoFin.allowBlank = false;
        
        var objCmbClaseMedio        = objComboClaseMedio();
        objCmbClaseMedio.style      = Utils.GREY_BOLD_COLOR;
        objCmbClaseMedio.id         = 'objCmbClaseMedio';
        objCmbClaseMedio.name       = 'objCmbClaseMedio';
        objCmbClaseMedio.fieldLabel = "Tipo Fibra"; 
        objCmbClaseMedio.colspan    = 6;
        objCmbClaseMedio.width      = intWidth; 
        objCmbClaseMedio.allowBlank = false;
        objCmbClaseMedio.blankText  = 'Ingrese tipo por favor';
        
        var objCmbCanton            = objComboCanton();
        objCmbCanton.style          = Utils.GREY_BOLD_COLOR;
        objCmbCanton.id             = 'objCmbCanton';
        objCmbCanton.name           = 'objCmbCanton';
        objCmbCanton.fieldLabel     = "Cantón"; 
        objCmbCanton.colspan        = 6;
        objCmbCanton.width          = intWidth;
        objCmbCanton.blankText      = 'Ingrese cantón por favor';
        objCmbCanton.queryMode      = 'remote'; 
        objCmbCanton.lazyRender     = true;
        objCmbCanton.loadingText    = 'Buscando...'; 
        objCmbCanton.minChars       = 3;      
        objCmbCanton.allowBlank     = false;
        objCmbCanton.disable();
        objCmbCanton.hide();
        
        var objComponentVacio      =  Ext.create('Ext.Component', {
                                                        html: '',
                                                        width: intWidth,
                                                        colspan: 6,
                                                        style: { color: '#000000' }
                                                    });
        
        formVerPoste.add(objIdCantonNodoInicio);
        formVerPoste.add(objIdCantonNodoFin);
        formVerPoste.add(objCmbTipoLugar);
        formVerPoste.add(objComponentVacio);
        formVerPoste.add(objTxtNombreElemento);
        formVerPoste.add(objCmbClaseMedio);
        formVerPoste.add(objTarDescripcionElemento);        
        formVerPoste.add(objCmbTipoElemento);
        formVerPoste.add(objCmbElementoIni);
        formVerPoste.add(objCmbElementoFin);
        formVerPoste.add(objCmbCanton);
        
     
        gridElementoContenido = Ext.create('Ext.grid.Panel',
        {
            width:     '100%',
            height:     300,
            store:       storeElementoContenido,
            loadMask:    true,
            frame:       false,   
            dockedItems: [toolbarElementosContenidos],
            plugins:     [cellEditing],
            selModel:    selEspacioModelo,       
            viewConfig: {
                stripeRows: true
            },
            columns:
                [   
                    {
                        header:    'nuevo',
                        id:        'nuevo',
                        dataIndex: 'nuevo',
                        hidden:    true,
                        hideable:  false                        
                    },
                    {
                        header:    'idElementoA',
                        id:        'id',
                        dataIndex: 'id',
                        hidden:    true,
                        hideable:  false,
                        value:     '',
                        
                    },
                    {
                        header:    'idTipoElemento',
                        id:        'idTipoElementoGrid',
                        dataIndex: 'idTipoElemento',
                        hidden:    true,
                        hideable:  false
                    },
                    {
                        header:    'Tipo',
                        id    :    'nombreTipoElementoGrid',
                        dataIndex: 'nombreTipoElemento',
                        width:     '15%',
                        sortable:  true
                    },
                    {
                        header:    'idElemento',
                        id:        'idElementoGrid',
                        dataIndex: 'idElemento',
                        hidden:    true
                    },
                    {
                        header:    'Nombre Elemento',
                        id    :    'nombreElementoGrid',
                        dataIndex: 'nombreElemento',
                        width:     '45%',
                        sortable:  true
                    },
                    {
                        header:    'Modelo',
                        id    :    'modeloElementoGrid',
                        dataIndex: 'modeloElemento',
                        width:     '20%',
                        sortable:  true
                    },
                    {
                        header:    'Tipo Fibra',
                        id    :    'tipoFibra',
                        dataIndex: 'tipoFibra',
                        width:     '15%',
                        sortable:  true
                    },
                    {
                        header:    'Tipo Fibra',
                        id    :    'idTipoFibra',
                        dataIndex: 'idTipoFibra',
                        sortable:  true,
                        hidden:    true
                    }                    
                ]
        });
    
        function existeRecordElemento(myRecord, grid)
            {    
                var existe = false;        

                var num = grid.getStore().getCount();    
     
                for (var i = 0; i < num; i++)
                {
                    var idElemento = grid.getStore().getAt(i).data.idElemento;   
              
                    if (idElemento === myRecord.data.idElemento)//myRecord.raw.idElemento
                    {
                        existe = true;
                        break;
                    }
                }
                return existe;
            }

        function eliminarSeleccionTipoElemento(datosSelect)
        {
        for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
        {
            datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
        }
    }
    
        formRelacionElemento = Ext.create('Ext.form.Panel', {
            bodyPadding:   5,
            name: 'formNuevaRuta',
            id: 'formNuevaRuta',
            waitMsgTarget: true,
            layout:        'column',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 150,
                msgTarget: 'side'
            },
            items:
                [
                    {
                        xtype:      'fieldset',
                        autoHeight: true,
                        width:      720,
                        items:
                            [formVerPoste, gridElementoContenido]
                    }
                ],
            buttonAlign: 'center',
            buttons: 
                [
                    {
                        text:     'Guardar',
                        name:     'btnGuardar',
                        align:    'center',
                        buttonAlign: 'center',
                        id:       'idBtnGuardar',
                        disabled:  false,
                        handler:   function () 
                        {
                        var strInfo ;
                        var form = formVerPoste.getForm();
                        if (form.isValid())
                        {
                            var data = form.getValues();

                            var intCountGridElementos = gridElementoContenido.getStore().getCount();
                            var strJsonElementosB = '';
                            var arrElementosB = [];

                            if (intCountGridElementos !== 0)
                            {
                                if ('' === gridElementoContenido.getStore().getAt(0).data.modeloElemento.trim())
                                {
                                    Ext.Msg.alert('Alerta!', 'Registro en blanco, debe ingresar la información del elemento.');
                                    return;
                                }
                                else
                                {
                                    for (var i = 0; i < intCountGridElementos; i++)
                                    {
                                        var intIdElementoB = gridElementoContenido.getStore().getAt(i).data.idElemento;

                                        if (intIdElementoB !== null && intIdElementoB > 0)
                                        {
                                            //concateno la informacion
                                            strInfo  = gridElementoContenido.getStore().getAt(i).data.idElemento+"|"+
                                                       gridElementoContenido.getStore().getAt(i).data.idTipoFibra;

                                            arrElementosB[i] = strInfo;
                                        }
                                    }
                                    if (arrElementosB === null || arrElementosB.length === 0)
                                    {
                                        Ext.Msg.alert('Alerta ', 'Error: Debe ingresar la información del elemento.');
                                        return;
                                    }
                                        
                                    strJsonElementosB = Ext.JSON.encode(arrElementosB);
                                    Ext.get('formNuevaRuta').mask('Guardando datos...');
                                    Ext.Ajax.request({
                                        url: urlSaveRuta,
                                        method: 'POST',
                                        params:
                                            {
                                               objCmbElementoFin: data.objCmbElementoFin,
                                               objCmbElementoIni:     data.objCmbElementoIni,
                                               objCmbTipoElemento: data.objCmbTipoElemento,
                                               objCmbTipoLugar: data.objCmbTipoLugar,
                                               objTarDescripcionElemento: data.objTarDescripcionElemento,
                                               objTxtNombreElemento: data.objTxtNombreElemento,                                           
                                               objCmbClaseMedio: data.objCmbClaseMedio,
                                               strElemntosB: strJsonElementosB
                                            },
                                        success: function(response)
                                        {
                                            Ext.get('formNuevaRuta').unmask('Guardando datos...');
                                            var json = Ext.JSON.decode(response.responseText);
                                            Ext.Msg.alert('Mensaje', json.strMessageStatus);                                           
                                            
                                            store.getProxy().extraParams.intElemento = json.intElemento;
                                            store.load();
                                            winElementoContenido.destroy();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.get('formNuevaRuta').unmask('Guardando datos...');
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                }
                            }
                            else
                            {
                                    Ext.Msg.alert('Alerta!', 'Ingrese elementos en la ruta.');
                                    return;                                
                            }
                        }
                    }
                    },
                    {
                        text:        'Cancelar',
                        buttonAlign: 'center',
                        listeners: {
                            click: function() 
                            {
                                objStoreTipo.removeAll();
                                winElementoContenido.destroy();
                            }
                        }   
                    }   
                ]
        });        
    
        winElementoContenido = Ext.create('Ext.window.Window', {
            title:     'Nueva Ruta',
            modal:     true,
            width:     745,        
            resizable: false,
            layout:    'fit',
            items:     [formRelacionElemento]
        }).show();
}

function subir()
{
    var formPanel =  Ext.widget('form', {    
        width: 400,
        bodyPadding: 10,
        items: [{
            xtype: 'filefield',
            name: 'archivo',
            id: 'archivo',
            fieldLabel: 'Archivo a cargar(*):',
            labelWidth: 120,
            anchor: '100%',
            buttonText: 'Seleccionar Archivo...'
        },
        {
            xtype: 'textareafield',
            name: 'txt_Observacion',
            id: 'txt_Observacion',
            value: 'Ingresar de forma obligatoria los campos del detalle del archivo para un nuevo elemento.\n'+
'Considerar que longitud y latitud deben ser tipo de dato decimal con punto (.)',
            width: '100%',
            allowBlank: false,
            readOnly: true
        }
        ],
        buttons: [{
            text: 'Guardar',
            handler: function () {
                var form = this.up('form').getForm();
                //Se valida mini formulario para ingreso de rutas masivas
                var archivoRutaComp = Ext.getCmp('archivo').value;
                                                                                                                                                                                
                if(!archivoRutaComp)
                {
                    Ext.Msg.alert('Advertencia', 'Debe seleccionar el archivo a subir');
                }                                        
                else
                {   
                    var archivoFinal = archivoRutaComp.toLowerCase();
                    var ext = getFileExt(archivoFinal);
                    if (ext == "csv") 
                    {
                        form.submit({
                        url: strUrlcargarDocumento,
                        params: {
                                strObservacion: 'Se subirá archivo para ingreso de Rutas',
                                strNombreDocumento: 'Archivo csv subido por Rutas',
                                strEsIngresoRutas: 'SI',
                                tipo: 'TECNICO',
                                strMensaje: 'Documento que se sube para realizar el ingreso de rutas',
                                data: JSON.stringify({app:"TelcosWeb", modulo:"Tecnico", submodulo:"SubidaRutas"})
                            },

                        waitMsg: 'Subiendo Archivo...',
                        success: function(fp, o)
                        {
                            Ext.Msg.alert('Mensaje ', 'Archivo subido exitosamente');
                            win.destroy();
                        },
                        failure: function(fp, o) {
                            Ext.Msg.alert('Alerta ', o.result.respuesta);
                        }
                        });
                    }else 
                    {
                        Ext.Msg.alert('Advertencia', 'Solo se aceptan archivos con extensión .csv');
                        Ext.getCmp('archivo').value="";
                        Ext.getCmp('archivo').setRawValue("");
                    }
                    
                } 
            }
            },
            {
                text: 'Salir',
                handler: function()
                {
                    win.destroy();
                }
            }]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Ingreso Masivo de Rutas',
            modal: true,
            width: 600,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

// Obtener extensión
function getFileExt(sPTF, bDot) 
{
    if (!bDot)
    {
        bDot = false;
    }
    return sPTF.substr(sPTF.lastIndexOf('.') + ((!bDot) ? 1 : 0));
}