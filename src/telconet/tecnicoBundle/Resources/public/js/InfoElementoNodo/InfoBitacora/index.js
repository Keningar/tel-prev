var store = undefined;

Ext.onReady(function() {

    Ext.tip.QuickTipManager.init();

    store = new Ext.data.Store({
        pageSize: 20,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : urlBitacoraAccesoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'data'
            },
            extraParams: {
                nodo:            '',
                tecnicoAsignado: '',
                fechaApertura:   '',
                canton:          '',
                estado:          '',
                departamento:    '',
                elementoRelacionado: '',
                fechaCierre:         '',
                tarea:          ''
			}
        },
        fields: [
            {name:'id', mapping:'id'},
            {name:'elementoNodoNombre', mapping:'elementoNodoNombre'},
            {name:'departamentoNombre', mapping:'departamentoNombre'},
            {name:'tecnicoAsignado', mapping:'tecnicoAsignado'},
            {name:'telefono', mapping:'telefono'},
            {name:'motivo', mapping:'motivo'},
            {name:'observacion', mapping:'observacion'},
            {name:'tareaId', mapping:'tareaId'},
            {name:'estado', mapping:'estado'},
            {name:'feCreacion', mapping:'feCreacion'},
            {name:'feUltMod', mapping:'feUltMod'},
            {name:'codigos', mapping:'codigos'},
            {name:'elemento', mapping:'elemento'},
            {name:'canton', mapping:'canton'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'usrUltMod', mapping:'usrUltMod'}
        ],
        autoLoad: true
    });

    var formItemFechaApertura = new Ext.form.DateField
    ({
        id: 'txtFechaApertura',
        fieldLabel: 'Fecha de Apertura',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: '40%',
        editable: false
    });

    var formItemFechaCierre = new Ext.form.DateField
    ({
        id: 'txtFechaCierre',
        fieldLabel: 'Fecha de Cierre',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: '40%',
        editable: false
    });

    // Filter Panel
    Ext.create('Ext.panel.Panel', {
        width: '100%',
        bodyPadding: 7,
        border:false,        
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible : true,
        collapsed: false,
        title: 'Criterios de búsqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){ buscar(); }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ limpiar(); }
            }
        ],
        items: [
            { width: '20%',border:false },
            {
                xtype: 'textfield',
                id: 'txtNombreNodo',
                fieldLabel: 'Nombre Nodo',
                value: '',
                width: '40%',
                listeners: {
                    specialkey: function (field, event) {
                        if (event.getKey() == event.ENTER && field.inputEl.dom.value) {
                            buscar();
                        }
                    }
                }
            },
            { width: '10%', border:false },
            {
                xtype: 'textfield',
                id: 'txtLoginTecnico',
                fieldLabel: 'Login del técnico',
                value: '',
                width: '40%'
            },
            {
                xtype: 'textfield',
                id: 'txtDepartamento',
                fieldLabel: 'Departamento',
                value: '',
                width: '40%'
            },
            //-------------------------------------
            { width: '20%',border: false},
            {
                xtype: 'textfield',
                id: 'txElementoRelacionado',
                fieldLabel: 'Elemento Relacionado',
                value: '',
                width: '40%'
            },
            { width: '10%', border:false },
            formItemFechaApertura,
            {
                xtype: 'textfield',
                id: 'txtCiudad',
                fieldLabel: 'Ciudad',
                value: '',
                width: '40%'
            },
            { width: '20%',border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Estado Bitácora',
                id: 'sltEstado',
                value:'',
                store: [
                    ['', 'Todos'],
                    ['Abierta', 'Abierta'],
                    ['Cerrada', 'Cerrada']
                ],
                width: '40%'
            },
            { width: '20%',border: false},
            formItemFechaCierre,
            {
                xtype: 'textfield',
                id: 'txtTarea',
                fieldLabel: 'Número de Tarea',
                value: '',
                width: '40%'
            },
        ],
        renderTo: 'filtro'
    });

    // List Items
    Ext.create('Ext.grid.Panel', {
        width: '100%',
        height: 520,
        store: store,
        loadMask: true,
        frame: false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : 'Copiar texto?',
                    msg    : 'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>'+value+'</b>',
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        viewConfig: {enableTextSelection: true},
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Nodo',
                dataIndex: 'elementoNodoNombre',
                width: 130,
                sortable: true
            },
            {
                header: 'Elemento Relacionado',
                dataIndex: 'elemento',
                width: 200,
                sortable: true
            },
            {
                header: 'Login del Técnico',
                dataIndex: 'tecnicoAsignado',
                width: 130,
                sortable: true
            },
            {
                header: 'Teléfono',
                dataIndex: 'telefono',
                width: 110,
                sortable: true
            },
            {
                header: 'Departamento',
                dataIndex: 'departamentoNombre',
                width: 120,
                sortable: true
            },
            {
                header: 'Ciudad',
                dataIndex: 'canton',
                width: 120,
                sortable: true
            },
            {
                header: 'Número de Tarea',
                dataIndex: 'tareaId',
                width: 100,
                sortable: true
            },
            {
                header: 'Nombre Tarea',
                dataIndex: 'motivo',
                width: 220,
                sortable: true
            },
            {
                header: 'Llave acsys',
                dataIndex: 'codigos',
                width: 160,
                sortable: true
            },
            {
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 280,
                sortable: true
            },
            {
                header: 'Estado Bitácora',
                dataIndex: 'estado',
                width: 110,
                sortable: true
            },
            {
                header: 'Fecha Apertura',
                dataIndex: 'feCreacion',
                width: 120,
                sortable: true
            },
            {
                header: 'Fecha Cierre',
                dataIndex: 'feUltMod',
                width: 120,
                sortable: true
            },
            {
                header: 'Usuario Apertura',
                dataIndex: 'usrCreacion',
                width: 120,
                sortable: true
            },
            {
                header: 'Usuario Cierre',
                dataIndex: 'usrUltMod',
                width: 120,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 90,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-show';
                        },
                        tooltip: 'Ver Bitácora',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            Ext.MessageBox.wait("Cargando, por favor espere...");
                            var rec = store.getAt(rowIndex);
                            window.location = "bitacora/" + rec.get('id') + "/showBitacora";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if (rec.get('estado') === "Abierta") {
                                return 'button-grid-PassWordTg';
                            }
                            return '';
                        },
                        tooltip: 'Cerrar Bitácora',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            Ext.MessageBox.wait("Cargando, por favor espere...");
                            var rec = store.getAt(rowIndex);
                            window.location = "bitacora/" + rec.get('id') + "/editBitacora";
                        }
                    }
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

});


// Functions
function buscar() {
    store.currentPage = 1;
    store.getProxy().extraParams.nodo =     Ext.getCmp('txtNombreNodo').value;
    store.getProxy().extraParams.tecnicoAsignado = Ext.getCmp('txtLoginTecnico').value;
    store.getProxy().extraParams.fechaApertura =  Ext.util.Format.date(Ext.getCmp('txtFechaApertura').getValue(), 'Y-m-d');
    store.getProxy().extraParams.canton = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.estado =         Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.departamento =   Ext.getCmp('txtDepartamento').value;
    store.getProxy().extraParams.elementoRelacionado = Ext.getCmp('txElementoRelacionado').value;
    store.getProxy().extraParams.fechaCierre =   Ext.util.Format.date(Ext.getCmp('txtFechaCierre').getValue(), 'Y-m-d');
    store.getProxy().extraParams.tarea =         Ext.getCmp('txtTarea').value;
    store.load();
}

function limpiar() {
    store.currentPage = 1;
    Ext.getCmp('txtNombreNodo').value = "";
    Ext.getCmp('txtNombreNodo').setRawValue("");

    Ext.getCmp('txtLoginTecnico').value = "";
    Ext.getCmp('txtLoginTecnico').setRawValue("");

    Ext.getCmp('sltEstado').value = "";
    Ext.getCmp('sltEstado').setRawValue("Todos");

    Ext.getCmp('txtFechaApertura').value = "";
    Ext.getCmp('txtFechaApertura').setRawValue("");

    Ext.getCmp('txtDepartamento').value = "";
    Ext.getCmp('txtDepartamento').setRawValue("");

    Ext.getCmp('txtCiudad').value = "";
    Ext.getCmp('txtCiudad').setRawValue("");

    Ext.getCmp('txElementoRelacionado').value = "";
    Ext.getCmp('txElementoRelacionado').setRawValue("");

    Ext.getCmp('txtFechaCierre').value = "";
    Ext.getCmp('txtFechaCierre').setRawValue("");

    Ext.getCmp('txtTarea').value = "";
    Ext.getCmp('txtTarea').setRawValue("");

    store.getProxy().extraParams.nodo =    '';
    store.getProxy().extraParams.tecnicoAsignado = '';
    store.getProxy().extraParams.fechaApertura =  '';
    store.getProxy().extraParams.canton = '';
    store.getProxy().extraParams.estado =     '';
    store.getProxy().extraParams.departamento =  '';
    store.getProxy().extraParams.elementoRelacionado = '';
    store.getProxy().extraParams.fechaCierre =   '';
    store.getProxy().extraParams.tarea =         '';
    store.load();
}
