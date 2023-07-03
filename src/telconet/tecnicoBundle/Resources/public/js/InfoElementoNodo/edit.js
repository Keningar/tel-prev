Ext.onReady(function(){ 
    
    Ext.tip.QuickTipManager.init();
    
    if(esFactibleTorre==='SI')
    {
        $(".altMax").show();
    }
    else
    {
        $(".altMax").hide();
    }
        
    //Recargar Informacion en formulario
    //Se agregó campo medidor eléctrico. Proyecto Nodo Fase 1.
    agregarValue("cmb_clase_medidor", claseMedidor);
    agregarValue("cmb_tipo_medidor", tipoMedidor);
    agregarValue("cmb_medidor_electrico", medidorElectrico);
    agregarValue("telconet_schemabundle_admimotivotype_nombreMotivo", motivo);
    agregarValue("cmb_clase_nodo", claseNodo);
    agregarValue("cmb_es_edificio", esFactibleTorre);
        
    arrayTiposNodo = Array();
    if(tipoNodo.indexOf('|') !== -1)
    {
        arrayTiposNodo = tipoNodo.split("|");  
    }
    else
    {
        arrayTiposNodo[0] = tipoNodo;  
    }
                  
        
    $('input[type=checkbox]').each(function() {

        if ($.inArray($(this).val(), arrayTiposNodo) !== -1)
        {
            $(":checkbox[value='" + $(this).val() + "']").attr('checked', true);
        }
        else
        {
            $(":checkbox[value='" + $(this).val() + "']").attr('checked', false);
        }
    });
   
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando Informacion del Nodo...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    conn.request
        (
            {
                url: url_cargarDatosNodo,
                method: 'post',
                params: {idNodo: idNodo},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);

                    if (json.total > 0) 
                    {
                        presentarRegiones("",
                            "telconet_schemabundle_infoelementonodotype_regionId",                                             
                            json.encontrados[0]['idRegion']);
                            
                        presentarProvincias(json.encontrados[0]['idRegion'],
                            "telconet_schemabundle_infoelementonodotype_provinciaId",                                             
                            json.encontrados[0]['idProvincia']);
                            
                        presentarParroquias(json.encontrados[0]['idCanton'],
                            "telconet_schemabundle_infoelementonodotype_parroquiaId",
                            json.encontrados[0]['idParroquia']);

                        presentarCantones(json.encontrados[0]['idProvincia'],
                            "telconet_schemabundle_infoelementonodotype_cantonId",                                             
                            json.encontrados[0]['idCanton']);                                                         
                    }
                    else {
                        Ext.Msg.alert('Error ', 'No se pudieron cargar los Datos');
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);

                }
            }
        );
    
    var tabs = new Ext.TabPanel({
        height: 700,
        renderTo: 'nodos-tabs-editar',
        activeTab: 0,
        plain: true,
        autoRender: true,
        autoShow: true,
        items: [
            {contentEl: 'tab1', title: 'Datos Generales'},
            {contentEl: 'tab2', title: 'Datos Local', listeners: {
                    activate: function(tab) {
                        gridInformacionEspacio.view.refresh();
                    }                    
                }
            },
            {contentEl: 'tab3', title: 'Datos Contactos', listeners: {
                    activate: function(tab) {
                        gridContacto.view.refresh();                        
                    }

                }}
        ]
    });
    
    //Contactos de nodo
    storeContactoNodo = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: url_infoContactoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idNodo: idNodo
            }
        },
        fields:
            [
                {name: 'idRol', mapping: 'idRol'},
                {name: 'descripcionRol', mapping: 'descripcionRol'},
                {name: 'tipoIdentificacion', mapping: 'tipoIdentificacion'},
                {name: 'identificacionCliente', mapping: 'identificacionCliente'},
                {name: 'nombres',  mapping: 'nombres'},
                {name: 'apellidos',  mapping: 'apellidos'},
                {name: 'idPersona',  mapping: 'idPersona'},                
                {name: 'razonSocial',  mapping: 'razonSocial'},
                {name: 'tipoTributario',  mapping: 'tipoTributario'}
                
            ],
         autoLoad: true
    });
    
    var gridContacto = Ext.create('Ext.grid.Panel', {
        id: 'gridContacto',
        store: storeContactoNodo,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idPersona',
                header: 'idPersona',
                dataIndex: 'idPersona',
                hidden: true,
                hideable: false
            },
            {
                id: 'descripcionRol',
                header: 'Tipo Contacto',
                dataIndex: 'descripcionRol',
                width: 100,
                sortable: true
            },
            {
                id: 'tipoIdentificacion',
                header: 'Tipo Identificación',
                dataIndex: 'tipoIdentificacion',
                width: 100 ,    
                sortable: true
            }, {
                id: 'identificacionCliente',
                header: 'Identificación',
                dataIndex: 'identificacionCliente',
                width: 100                
            },
            {
                id: 'nombres',
                header: 'Nombres',
                dataIndex: 'nombres',
                width: 200                
            }, 
            {
                id: 'apellidos',
                header: 'Apellidos',
                dataIndex: 'apellidos',
                width: 200        
            }, 
            {
                id: 'razonSocial',
                header: 'Razón Social',
                dataIndex: 'razonSocial',
                width: 200                               
            } ,            
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 70,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            this.items[0].tooltip = 'Editar Formas de Contacto';
                            return 'button-grid-show';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                            
                            obtenerFormasContacto(grid.getStore().getAt(rowIndex).data.idPersona);                            
                        }
                    } ,
                    {
                        getClass: function(v, meta, rec) 
                        {
                            this.items[1].tooltip = 'Editar Contacto del Nodo';
                            return 'button-grid-edit';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                            
                            editarContactoNodo(grid.getStore().getAt(rowIndex).data);                            
                        }
                    }                                    
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeContactoNodo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        viewConfig: {
            stripeRows: true
        },
        width: 1000,
        height: 250,
        title: 'Informacion de Contacto',
        renderTo: Ext.get('contactoNodo')
    }); 
        
   //Informacion de Espacio
   var storeTipoUbicacion = new Ext.data.Store({              
            proxy: {
                type: 'ajax',
                url : url_admitipoespacio,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    estado: 'Activo'				
                }
            },
            fields:
                      [
                        {name:'idTipoEspacio', mapping:'idTipoEspacio'},
                        {name:'nombreTipoEspacio', mapping:'nombreTipoEspacio'}
                      ]
        });     
           
    Ext.define('UbicacionModelo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', mapping:'id'},
            {name:'tipoEspacioId', mapping:'idTipoEspacio'},
            {name:'tipoEspacioNombre', mapping:'nombreTipoEspacio'},
            {name:'largo', mapping:'largo'},
            {name:'ancho', mapping:'ancho'},
            {name:'alto', mapping:'alto'},      
            {name:'valor', mapping:'valor'}
        ]
    });     
    
    storeInformacionEspacio = Ext.create('Ext.data.Store', 
    {        
         proxy: {
            type: 'ajax',
            url: url_infoEspacioNodo,
            model : 'UbicacionModelo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idNodo: idNodo
            }
        },
        autoLoad: true
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function(){                
                gridInformacionEspacio.getView().refresh();
            }
        }
    });
    
    var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) 
            {                 
                gridInformacionEspacio.down('#removeButton').setDisabled(selections.length == 0);                
            }
        }
    });
    
    gridInformacionEspacio = Ext.create('Ext.grid.Panel', {
        id: 'gridInformacionEspacio',
        store: storeInformacionEspacio,
        columns: [
            {
                id: 'id',
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipoEspacioId',
                header: 'tipoEspacioId',
                dataIndex: 'tipoEspacioId',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipoEspacioNombre',
                header: 'Tipo Espacio',
                dataIndex: 'tipoEspacioNombre',
                width: 200,
                sortable: true,
                renderer: function(value, metadata, record, rowIndex, colIndex, store) 
                {                                                                               
                    record.data.tipoEspacioId = record.data.tipoEspacioNombre;
                    
                    for (var i = 0; i < storeTipoUbicacion.data.items.length; i++)
                    {
                        if (storeTipoUbicacion.data.items[i].data.idTipoEspacio === record.data.tipoEspacioId)
                        {                            
                            record.data.tipoEspacioNombre = storeTipoUbicacion.data.items[i].data.nombreTipoEspacio;                         
                            break;
                        }
                    }

                    return record.data.tipoEspacioNombre;
                },
                editor: {
                    id: 'searchTipoEspacio_cmp',
                    xtype: 'combobox',
                    typeAhead: true,
                    displayField: 'nombreTipoEspacio',
                    valueField: 'idTipoEspacio',
                    triggerAction: 'all',
                    editable:false,
                    selectOnFocus: true,
                    loadingText: 'Buscando ...',
                    hideTrigger: false,
                    store: storeTipoUbicacion,
                    lazyRender: true,
                    listClass: 'x-combo-list-small',
                    listeners: {
                        select: function(combo) {                                                                                    
                            var r = Ext.create('UbicacionModelo', {
                                id:0,
                                tipoEspacioId: combo.rawValue,
                                tipoEspacioNombre: combo.rawValue,
                                largo: '',
                                ancho: '',
                                alto: '',
                                valor:''
                            });
                            
                            if(existeRecordRelacion(r, gridInformacionEspacio))                        
                            {     
                                //Determinar no repetidos
                                Ext.Msg.alert("Advertencia","Ya ingreso informacio de "+r.get('tipoEspacioNombre'));
                                eliminarSeleccionEspacio(gridInformacionEspacio);
                            }
                        }
                    }
                }
            },
            {
                id: 'largo',
                header: 'Largo',
                dataIndex: 'largo',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            }, {
                id: 'ancho',
                header: 'Ancho',
                dataIndex: 'ancho',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            },
            {
                id: 'alto',
                header: 'Alto',
                dataIndex: 'alto',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            }, {
                id: 'valor',
                header: 'Valor ($)',
                dataIndex: 'valor',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
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
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccionEspacio(gridInformacionEspacio);
                        }
                    }, '-', {
                        text: 'Agregar',
                        tooltip: 'Agrega un item a la lista',
                        iconCls: 'add',
                        handler: function() {

                            var r = Ext.create('UbicacionModelo', {
                                tipoEspacioId: '',
                                nombreEspacioId: '',
                                largo: '0',
                                ancho: '0',
                                alto: '0',
                                valor: '0',
                                id:'0'
                            });

                            storeInformacionEspacio.insert(0, r);
                            cellEditing.startEditByPosition({row: 0, column: 1});
                        }
                    }]
            }],
        width: 850,
        height: 200,
        title: 'Agregue Informacion de Espacio',
        renderTo: Ext.get('informacionEspacioEditar'),
        plugins: [cellEditing]
    });
    
    var eliminarSeleccionEspacio = async function(datosSelect){
        for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
        {
            var intIdEspacio = datosSelect.getSelectionModel().getSelection()[i].data.id;

            datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
        }

        Ext.Ajax.request({
            url: url_deleteEspacioFisico,
            method: 'post',
            params: {
                idEspacio: intIdEspacio
            },
            success: function(response) {
                
                var json = Ext.JSON.decode(response.responseText);
                console.log("Success: " + json.mensaje);
            },
       
            failure: function(response) {
                Ext.Msg.alert("Error ", response.statusText );
            }
        });
    }
    
});

function obtenerFormasContacto(idPersona)
{
    winFormaContacto      = "";         
      
    /*****************************************************************************
     * 
     *                             FORMAS DE CONTACTO
     *  
     *****************************************************************************/
    
     Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });
    
    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [           
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
        
    store = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        autoLoad:true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',                
                totalProperty: 'total'
            },
            extraParams: {personaid: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.personaid = idPersona;
            }
        }
    });
    
    var storeFormasContacto = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    
    gridContactoNodo = Ext.create('Ext.grid.Panel', {
        id:'gridContactoNodo',
        store: store,
        columns: [{
                text: 'Forma Contacto',
                header: 'Forma Contacto',
                dataIndex: 'formaContacto',
                width: 150,
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    triggerAction: 'all',
                    selectOnTab: true,
                    id: 'idFormaContacto',
                    name: 'formaContacto',
                    valueField: 'descripcion',
                    displayField: 'descripcion',
                    store: storeFormasContacto,
                    width: 150,
                    lazyRender: true,
                    listClass: 'x-combo-list-small'
                })
            }, {
                text: 'Valor',
                dataIndex: 'valor',
                width: 250,
                align: 'right',
                editor: {
                    width: '80%',
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            {
                xtype: 'actioncolumn',
                width: 45,
                sortable: false,
                items: [{
                        iconCls: "button-grid-delete",
                        tooltip: 'Borrar Forma Contacto',
                        handler: function(grid, rowIndex, colIndex) {                            
                                store.removeAt(rowIndex);
                        }
                    }]
            }
        ],
        selModel: {
            selType: 'cellmodel'
        },        
        width: 450,
        height: 250,
        title: '',        
        tbar: [{
                text: 'Agregar',
                handler: function() {                    
                    var r = Ext.create('PersonaFormasContactoModel', {
                        idPersonaFormaContacto: '',
                        formaContacto: '',
                        valor: ''
                    });
                    store.insert(0, r);
                    cellEditing.startEditByPosition({row: 0, column: 0});

                }
            }],
        plugins: [cellEditing]
    }); 

    var formPanelFormaContacto = Ext.create('Ext.form.Panel', {
        width: 500,
        height: 300,
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [gridContactoNodo],
        buttons: [
            {
                text: 'Actualizar',
                handler: function()
                {
                    if(validaFormasContacto())
                    {
                        var infoContacto = obtenerInformacionGridInformacionContacto();

                        if (infoContacto)
                        {
                            var conn = new Ext.data.Connection({
                                listeners: {
                                    'beforerequest': {
                                        fn: function(con, opt) {
                                            Ext.get(winFormaContacto.getId()).mask('Actualizando información de Contactos...');
                                        },
                                        scope: this
                                    },
                                    'requestcomplete': {
                                        fn: function(con, res, opt) {
                                            Ext.get(winFormaContacto.getId()).unmask();
                                        },
                                        scope: this
                                    },
                                    'requestexception': {
                                        fn: function(con, res, opt) {
                                            Ext.get(winFormaContacto.getId()).unmask();
                                        },
                                        scope: this
                                    }
                                }
                            });

                            conn.request
                                (
                                    {
                                        url: url_actualizarFormaContacto,
                                        method: 'post',
                                        params: {idPersona: idPersona, formasContacto: infoContacto},
                                        success: function(response)
                                        {
                                            var json = Ext.JSON.decode(response.responseText);

                                            Ext.Msg.alert('Mensaje', json.mensaje, function(btn)
                                            {
                                                if (btn === 'ok')
                                                {
                                                    winFormaContacto.destroy();
                                                }
                                            });
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    }
                                );
                        }
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function() {
                    winFormaContacto.close();
                }
            }
        ]
    });

    winFormaContacto = Ext.widget('window', {
        title: 'Editar Formas de Contacto',
        width: 500,
        height: 300,
        layout: 'fit',
        resizable: false,
        modal: true,
        closabled: false,
        items: [formPanelFormaContacto]
    });
    
    
    winFormaContacto.show();    
}

function editarContactoNodo(data)
{        
    var storeRolesContacto = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getRolesContactoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idRol', mapping: 'idRol'},
                {name: 'nombreRol', mapping: 'nombreRol'}
            ]        
    });    
    
    var formPanelRenovacionContactoNodo = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 380,
                        items:
                            [    
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Contacto:',
                                    id: 'cmbTipoContacto',
                                    name: 'cmbTipoContacto',
                                    store: storeRolesContacto,
                                    displayField: 'nombreRol',
                                    valueField: 'idRol',                                    
                                    emptyText: '',
                                    editable:false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,                                    
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {                                                
                                                combo.setValue(data.idRol);                                                
                                            }                                            
                                        }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Identificación:',
                                    id: 'cmbTipoIdentificacion',
                                    name: 'cmbTipoIdentificacion',                                    
                                    store: [
                                        ['CED', 'CED'],
                                        ['RUC', 'RUC'],
                                        ['PAS', 'PAS']                                        
                                    ],
                                    emptyText: '',
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.tipoIdentificacion);
                                            },
                                            select:function(combo)
                                            {                                                
                                                switch(combo.getValue())
                                                {
                                                    case 'CED':
                                                        $('#identificacionClienteEditar-inputEl').attr('maxlength','10');
                                                        break;
                                                    case 'RUC':
                                                        $('#identificacionClienteEditar-inputEl').attr('maxlength','13');                    
                                                        break;
                                                    case 'PAS':
                                                        $('#identificacionClienteEditar-inputEl').attr('maxlength','20');
                                                        break;
                                                } 
                                            }
                                        }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Tributario:',
                                    id: 'cmbTipoTributario',
                                    name: 'cmbTipoTributario',                                    
                                    store: [
                                        ['Natural', 'NAT'],
                                        ['Jurídico', 'JUR']                                    
                                    ],
                                    emptyText: '',
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.tipoTributario);
                                            }
                                        }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Identificación:',
                                    id: 'identificacionClienteEditar',
                                    name: 'identificacionClienteEditar',
                                    value: data.identificacionCliente,
                                    minValue: data.identificacionCliente,                                    
                                    labelStyle: 'font-weight:bold',                                    
                                    width: 300
                                },     
                                {
                                    xtype: 'displayfield',                                                                        
                                    value: '<div onclick="validarIdentificacion('+data.identificacionCliente+')" \n\
                                                    style="cursor:pointer;position:relative;left:15%;color:blue" \n\
                                                 align="center">\n\
                                            <img src="/public/images/search.png"/>\n\
                                            <b>Verificar Identificacion existe</b><div/>',                                    
                                    width: 300                                                                        
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombres:',
                                    id: 'nombresEditar',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    value: data.nombres,
                                    disabled:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Apellidos:',
                                    id: 'apellidosEditar',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    value: data.apellidos,
                                    disabled:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Razón Social:',
                                    id: 'razonSocialEditar',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    value: data.razonSocial,                                    
                                    disabled:true                                    
                                }                            
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Editar Contacto',
                        handler: function()
                        {                                                       
                            tipoIdentificacion   = Ext.getCmp('cmbTipoIdentificacion').value;
                            tipoTributario       = Ext.getCmp('cmbTipoTributario').value;
                            identificacion       = Ext.getCmp('identificacionClienteEditar').value;
                            nombres              = Ext.getCmp('nombresEditar').value;
                            apellidos            = Ext.getCmp('apellidosEditar').value;
                            razonSocial          = Ext.getCmp('razonSocialEditar').value;
                            tipoContacto         = Ext.getCmp('cmbTipoContacto').value;
                            cambioTipoContacto   = 'N';
                            intNodoId            = idNodo;
                            
                            
                            if(tipoContacto !== data.idRol)
                            {
                                cambioTipoContacto   = 'S';
                            }
                            
                            if (identificacion === "")
                            {
                                Ext.Msg.alert('Advertencia', 'Debe ingresar la identificación del contacto');
                            }
                            else if (tipoTributario === "")
                            {
                                Ext.Msg.alert('Advertencia', 'Debe ingresar tipo tributario');
                            } 
                            else if (tipoTributario === "Natural" && (nombres === "" || apellidos === ""))
                            {
                                Ext.Msg.alert('Advertencia', 'Debe ingresar los nombres y apellidos del contacto');
                            }                           
                            else if (tipoTributario === "Jurídico" && (razonSocial === ""||razonSocial === null) )
                            {
                                Ext.Msg.alert('Advertencia', 'Debe ingresar la Razón Social del contacto');
                            }                            
                            else
                            {                                
                                var conn = new Ext.data.Connection({
                                    listeners: {
                                        'beforerequest': {
                                            fn: function(con, opt) {
                                                Ext.get(formPanelRenovacionContactoNodo.getId()).mask("Actualizando Información de Contacto...");
                                            },
                                            scope: this
                                        },
                                        'requestcomplete': {
                                            fn: function(con, res, opt) {
                                                Ext.get(formPanelRenovacionContactoNodo.getId()).unmask();
                                            },
                                            scope: this
                                        },
                                        'requestexception': {
                                            fn: function(con, res, opt) {
                                                Ext.get(formPanelRenovacionContactoNodo.getId()).unmask();
                                            },
                                            scope: this
                                        }
                                    }
                                });                              
                                conn.request
                                    ({
                                        url: url_actualizarContactoNodo,
                                        method: 'post',
                                        timeout: 300000,
                                        params:
                                            {                                      
                                                idPersona           : data.idPersona,
                                                tipoIdentificacion  : tipoIdentificacion,
                                                tipoTributario      : tipoTributario,
                                                identificacion      : identificacion,
                                                nombres             : nombres,
                                                apellidos           : apellidos,
                                                razonSocial         : razonSocial,
                                                tipoContacto        : tipoContacto,
                                                cambioTipoContacto  : cambioTipoContacto,
                                                tipoContactoAnterior: data.idRol,
                                                intNodoId           : intNodoId
                                            },
                                        success: function(response)
                                        {
                                            Ext.get(formPanelRenovacionContactoNodo.getId()).unmask();
                                            var json = Ext.JSON.decode(response.responseText);
                                            Ext.Msg.alert('Mensaje', json.mensaje, function(btn)
                                            {
                                                if (btn === 'ok')
                                                {              
                                                    storeContactoNodo.load();
                                                    winRenov.destroy();
                                                }
                                            });
                                        },
                                        failure: function(result)
                                        {
                                            Ext.get(formPanelRenovacionContactoNodo.getId()).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            Ext.get(document.body).unmask();
                            winRenov.destroy();
                        }
                    }
                ]
        });

    var winRenov = Ext.create('Ext.window.Window',
        {
            title: 'Editar Información Contacto Nodo',
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formPanelRenovacionContactoNodo]
        }).show();
}

function validarTipoTributario(tipo)
{
    if(tipo === 'NAT')return true;
    else return false;
}

function validarIdentificacion(idPersona)
{
    var identificacion     = Ext.getCmp('identificacionClienteEditar').value;
    var tipoIdentificacion = Ext.getCmp('cmbTipoIdentificacion').value;
    var identificacionEsCorrecta = false;    
    var rol = 'Contacto Nodo';
    
    Ext.getCmp('cmbTipoTributario').setDisabled(true);
    Ext.getCmp('nombresEditar').setDisabled(true);
    Ext.getCmp('apellidosEditar').setDisabled(true);
    Ext.getCmp('razonSocialEditar').setDisabled(true);
    
    Ext.ComponentQuery.query('#cmbTipoTributario')[0].setValue("");
    Ext.ComponentQuery.query('#nombresEditar')[0].setValue(""); 
    Ext.ComponentQuery.query('#apellidosEditar')[0].setValue(""); 
    Ext.ComponentQuery.query('#razonSocialEditar')[0].setValue(""); 
        
    if(tipoIdentificacion === null)
    {
        Ext.Msg.alert('Advertencia', 'Debe escoger el tipo de identificacion');
        return;
    }
    else if(identificacion==="")
    {
        Ext.Msg.alert('Advertencia', 'Debe escribir la identificacion del contacto');
        return;
    }    
    //Se verifica formato
    if (/^[\w]+$/.test(identificacion) && (tipoIdentificacion === 'PAS')) 
    {
        identificacionEsCorrecta = true;
    }
    if (/^\d+$/.test(identificacion) && (tipoIdentificacion === 'RUC' || tipoIdentificacion === 'CED'))
    {
        identificacionEsCorrecta = true;
    }
    
    if(identificacionEsCorrecta)
    {
        var conn = requestMask('Verificando Identificacion');        
        conn.request({
            method: 'POST',
            url: url_validar_identificacion_tipo,
            params:
                {
                    identificacion: identificacion,   
                    tipo          : tipoIdentificacion
                },
            success: function(response)
            {
                response = response.responseText;
                if(response !== "")
                {
                    Ext.Msg.alert('Error', response);
                    return;
                }
                else
                {
                    conn.request({
                        method: 'POST',
                        url: url_valida_identificacion,
                        params:
                            {
                                identificacion: identificacion                    
                            },
                        success: function(response)
                        {
                            response = response.responseText;
                            if(response !== "no")
                            {
                                var data = Ext.JSON.decode(response)[0];   
                                                                
                                arrayRoles = data.roles.split("|");
                                
                                tieneRol = false;
                                
                                for (var i = 0; i < arrayRoles.length; i++) 
                                {                                                                            
                                    if (rol === arrayRoles[i]) 
                                    {
                                        tieneRol = true;
                                        break;
                                    }
                                }
                                
                                Ext.ComponentQuery.query('#cmbTipoTributario')[0].setValue(data.tipoTributario);
                                Ext.ComponentQuery.query('#nombresEditar')[0].setValue(data.nombres);
                                Ext.ComponentQuery.query('#apellidosEditar')[0].setValue(data.apellidos);
                                Ext.ComponentQuery.query('#razonSocialEditar')[0].setValue(data.razonSocial !== null ?
                                    data.razonSocial : data.nombres + " " + data.apellidos); 
                            }  
                            else
                            {
                                Ext.Msg.alert('Info', 'Identificacion Correcta, se debe escalar para el ingreso del nuevo contacto');  
                                if(tipoIdentificacion === 'RUC')
                                {
                                    Ext.getCmp('razonSocial').setDisabled(true);
                                }
                                //Se carga la informacion del cliente
                                Ext.getCmp('cmbTipoTributario').setDisabled(true);
                                Ext.getCmp('nombresEditar').setDisabled(true);
                                Ext.getCmp('apellidosEditar').setDisabled(true);
                                Ext.getCmp('razonSocialEditar').setDisabled(true);
                            }                                                        
                        }
                    });
                }                                
            }
        });        
    }
    else
    {
        Ext.Msg.alert('Advertencia', 'Identificacion es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales');
        return;
    }
}

function requestMask(msg)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.getBody().mask(msg);
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            }
        }
    });
    
    return conn;
}

function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    console.log(key);
    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [45, 46];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];
    }
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46];
    }

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}