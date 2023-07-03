/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    Ext.define('clase', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'claseInterface', type: 'string'}
        ]
    });
    
    comboClase = new Ext.data.Store({ 
        model: 'clase',
        data : [
            {claseInterface:'Standar' },
            {claseInterface:'Modular' },
        ]
    });
    
    var storeTipoInterface = new Ext.data.Store({  
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url : '../../admi_tipo_interface/getTiposInterfaces',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
                  [
                    {name:'idTipoInterface', mapping:'idTipoInterface'},
                    {name:'nombreTipoInterface', mapping:'nombreTipoInterface'}
                  ]
    });
    
    Ext.define('InterfaceModelo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idInterfaceModelo', mapping:'idInterfaceModelo'},
            {name:'tipoInterfaceId', mapping:'tipoInterfaceId'},
            {name:'tipoInterfaceNombre', mapping:'nombreTipoInterface'},
            {name:'cantidadInterface', mapping:'cantidadInterface'},
            {name:'formatoInterface', mapping:'formatoInterface'},
            {name:'claseInterface', mappgin:'claseInterface'},
            {name:'caracteristicasInterface', mappgin:'caracteristicasInterface'}
        ]
    });
    
    var storeInterfacesModelos = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getInterfacesModelo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idInterfaceModelo', mapping:'idInterfaceModelo'},
                {name:'tipoInterfaceId', mapping:'tipoInterfaceId'},
                {name:'tipoInterfaceNombre', mapping:'nombreTipoInterface'},
                {name:'claseInterface', mapping:'claseInterface'},
                {name:'cantidadInterface', mapping:'cantidadInterface'},
                {name:'formatoInterface', mapping:'formatoInterface'},
                {name:'caracteristicasInterface', mapping:'caracteristicasInterface'}
              ]
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridInterfacesModelos.getView().refresh();
            }
        }
    });
    
    var selInterfaceModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridInterfacesModelos.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de interfaces
    gridInterfacesModelos = Ext.create('Ext.grid.Panel', {
        id:'gridInterfacesModelos',
        store: storeInterfacesModelos,
        columnLines: true,
        columns: [{
            id: 'idInterfaceModelo',
            header: 'idInterfaceModelo',
            dataIndex: 'idInterfaceModelo',
            hidden: true,
            hideable: false
        },{
            id: 'tipoInterfaceId',
            header: 'tipoInterfaceId',
            dataIndex: 'tipoInterfaceId',
            hidden: true,
            hideable: false
        },{
            id: 'tipoInterfaceNombre',
            header: 'Tipo Interface',
            dataIndex: 'tipoInterfaceNombre',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.tipoInterfaceNombre) == "number")
                {
                    console.log(record.data.tipoInterfaceNombre);
                    
                    record.data.tipoInterfaceId = record.data.tipoInterfaceNombre;
                    for (var i = 0;i< storeTipoInterface.data.items.length;i++)
                    {
                        console.log(record.data.tipoInterfaceId);
                        console.log(storeTipoInterface.data.items[i].data.idTipoInterface);
                        if (storeTipoInterface.data.items[i].data.idTipoInterface == record.data.tipoInterfaceId)
                        {
                            record.data.tipoInterfaceNombre = storeTipoInterface.data.items[i].data.nombreTipoInterface;
                            break;
                        }
                    }
                }
                return record.data.tipoInterfaceNombre;
            },
            editor: {
                id:'searchTipoInterface_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreTipoInterface',
                valueField: 'idTipoInterface',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeTipoInterface,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('InterfaceModelo', {
                            idInterfaceModelo: '',
                            tipoInterfaceId: combo.getValue(),
                            tipoInterfaceNombre: combo.lastSelectionText,
                            cantidadInterface: '',
                            formatoInterface: '',
                            claseInterface: '',
                            caracteristicasInterface:''
                        });
                        if(!existeRecordRelacion(r, gridInterfacesModelos))
                        {
                            Ext.get('searchTipoInterface_cmp').dom.value='';
                            if(r.tipoInterfaceId != 'null')
                            {
                                Ext.get('searchTipoInterface_cmp').dom.value=r.get('tipoInterfaceNOmbre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridInterfacesModelos);
                        }
                    }
                }
            }
        },{
            id: 'cantidadInterface',
            header: 'Cantidad',
            dataIndex: 'cantidadInterface',
            width: 150,
            editor: {
                allowBlank: false
            }
        },{
            id: 'formatoInterface',
            header: 'Formato',
            dataIndex: 'formatoInterface',
            width: 150,
            editor: {
                allowBlank: false
            }
        },{
            id: 'claseInterface',
            header: 'Clase',
            dataIndex: 'claseInterface',
            width: 150,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    record.data.claseInterface = record.data.claseInterface;
                    for(var i = 0;i< comboClase.data.items.length;i++){
                        if(comboClase.data.items[i].data.claseInterface == value){
                            record.data.claseInterface = comboClase.data.items[i].data.claseInterface;                            
                            break;
                        }
                    }                    
                    return record.data.claseInterface;
                },
            editor   :  {
                    xtype: 'combobox',
                    id:'comboClase',
                    name: 'comboClase',
                    store: comboClase,
                    displayField: 'claseInterface',
                    valueField: 'claseInterface',
                    queryMode: 'local',
                    lazyRender: true,
                    emptyText: '',
                    forceSelection: true
                }
        },
        {
            id: 'caracteristicasInterface',
            header: 'caracteristicasInterface',
            dataIndex: 'caracteristicasInterface',
            hidden: true,
            hideable: false
        },{
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return 'button-grid-agregarCaracteristica'},
                tooltip: 'Agregar Caracteristica',
                handler: function(grid, rowIndex, colIndex) {
                        //grid de detalles de las interfaces
                        agregarCaracteristica(grid.getStore().getAt(rowIndex).data);
                    }
                }
            ]
        }],
        selModel: selInterfaceModelo,
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
                handler : function(){eliminarSeleccion(gridInterfacesModelos);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('InterfaceModelo', { 
                        idInterfaceModelo: '',
                        idTipoInterface: '',
                            nombreTipoInterface: '',
                            cantidadInterface: '',
                            formatoInterface: '',
                            claseInterface: '',
                            caracteristicasInterface:''
                    });
                    if(!existeRecordRelacion(r, gridInterfacesModelos))
                    {
                        storeInterfacesModelos.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 850,
        height: 200,
        frame: true,
        title: 'Agregar Interfaces',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
    
    //-------------------------------------------------------------------------------
    Ext.define('preferencia', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'esPreferencia', type: 'string'}
        ]
    });
    
    comboEsPreferencia = new Ext.data.Store({ 
        model: 'preferencia',
        data : [
            {esPreferencia:'NO' },
            {esPreferencia:'SI' },
        ]
    });
    
    var storeUsuarios = new Ext.data.Store({  
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url : '../../admi_usuario_acceso/getUsuarios',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idUsuarioAcceso', mapping:'idUsuarioAcceso'},
              {name:'nombreUsuarioAcceso', mapping:'nombreUsuarioAcceso'}
            ]
    });
    
    Ext.define('UsuarioAcceso', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idModeloUsuarioAcceso', mapping:'idModeloUsuarioAcceso'},
            {name:'usuarioAccesoId', mapping:'usuarioAccesoId'},
            {name:'usuarioAccesoNombre', mapping:'usuarioAccesoNombre'},
            {name:'esPreferencia', mapping:'esPreferencia'}
        ]
    });
    
    var storeUsuariosAcceso = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloUsuariosAcceso',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idModeloUsuarioAcceso', mapping:'idModeloUsuarioAcceso'},
                {name:'usuarioAccesoId', mapping:'idUsuarioAcceso'},
                {name:'usuarioAccesoNombre', mapping:'nombreUsuarioAcceso'},
                {name:'esPreferencia', mapping:'esPreferenciaUsuario'}
              ]
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridUsuariosAcceso.getView().refresh();
            }
        }
    });
    
    var selUsuariosAcceso = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridUsuariosAcceso.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de usuarios
    gridUsuariosAcceso = Ext.create('Ext.grid.Panel', {
        id:'gridUsuariosAcceso',
        store: storeUsuariosAcceso,
        columnLines: true,
        columns: [{
            id: 'idModeloUsuarioAcceso',
            header: 'idModeloUsuarioAcceso',
            dataIndex: 'idModeloUsuarioAcceso',
            hidden: true,
            hideable: false
        },{
            id: 'usuarioAccesoId',
            header: 'idUsuarioAcceso',
            dataIndex: 'usuarioAccesoId',
            hidden: true,
            hideable: false
        },{
            id: 'usuarioAccesoNombre',
            header: 'Usuario',
            dataIndex: 'usuarioAccesoNombre',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.usuarioAccesoNombre) == "number")
                {
                    
                    record.data.usuarioAccesoId = record.data.usuarioAccesoNombre;
                    for (var i = 0;i< storeUsuarios.data.items.length;i++)
                    {
                        if (storeUsuarios.data.items[i].data.idUsuarioAcceso == record.data.usuarioAccesoId)
                        {
                            record.data.usuarioAccesoNombre = storeUsuarios.data.items[i].data.nombreUsuarioAcceso;
                            break;
                        }
                    }
                }
                return record.data.usuarioAccesoNombre;
            },
            editor: {
                id:'searchUsuarios_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreUsuarioAcceso',
                valueField: 'idUsuarioAcceso',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeUsuarios,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('UsuarioAcceso', {
                            usuarioAccesoId: combo.getValue(),
                            usuarioAccesoNombre: combo.lastSelectionText,
                            esPreferencia: ''
                        });
                        if(!existeRecordUsuario(r, gridUsuariosAcceso))
                        {
                            Ext.get('searchUsuarios_cmp').dom.value='';
                            if(r.usuarioAccesoId != 'null')
                            {
                                Ext.get('searchUsuarios_cmp').dom.value=r.get('usuarioAccesoNombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridUsuariosAcceso);
                        }
                    }
                }
            }
        },{
            id: 'esPreferencia',
            header: 'Es Preferencia',
            dataIndex: 'esPreferencia',
            width: 100,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    record.data.esPreferencia = record.data.esPreferencia;
                    for(var i = 0;i< comboEsPreferencia.data.items.length;i++){
                        if(comboEsPreferencia.data.items[i].data.esPreferencia == value){
                            record.data.esPreferencia = comboEsPreferencia.data.items[i].data.esPreferencia;                            
                            break;
                        }
                    }                    
                    return record.data.esPreferencia;
                },
            editor   :  {
                    xtype: 'combobox',
                    id:'comboEsPreferencia',
                    name: 'comboEsPreferencia',
                    store: comboEsPreferencia,
                    displayField: 'esPreferencia',
                    valueField: 'esPreferencia',
                    queryMode: 'local',
                    lazyRender: true,
                    emptyText: '',
                    forceSelection: true
                }
        }],
        selModel: selUsuariosAcceso,
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
                handler : function(){eliminarSeleccion(gridUsuariosAcceso);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('UsuarioAcceso', { 
                        usuarioAccesoId: '',
                            usuarioAccesoNombre: '',
                            esPreferencia: ''
                    });
                    if(!existeRecordUsuario(r, gridUsuariosAcceso))
                    {
                        storeUsuariosAcceso.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 425,
        height: 200,
        frame: true,
        title: 'Agregar Usuarios',
        renderTo: 'gridUsuarios',
        plugins: [cellEditing]
    });
    
    //-------------------------------------------------------------------------------
    
    Ext.define('preferenciaProtocolo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'esPreferenciaProtocolo', type: 'string'}
        ]
    });
    
    comboEsPreferenciaProtocolo = new Ext.data.Store({ 
        model: 'preferenciaProtocolo',
        data : [
            {esPreferenciaProtocolo:'NO' },
            {esPreferenciaProtocolo:'SI' },
        ]
    });
    
    var storeProtocolo = new Ext.data.Store({  
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url : '../../admi_protocolo/getProtocolos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idProtocolo', mapping:'idProtocolo'},
              {name:'nombreProtocolo', mapping:'nombreProtocolo'}
            ]
    });
    
    Ext.define('Protocolo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idModeloProtocolo', mapping:'idModeloProtocolo'},
            {name:'protocoloId', mapping:'protocoloId'},
            {name:'protocoloNombre', mapping:'protocoloNombre'},
            {name:'esPreferenciaProtocolo', mapping:'esPreferenciaProtocolo'}
        ]
    });
    
    var storeProtocolos = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloProtocolos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idModeloProtocolo', mapping:'idModeloProtocolo'},
                {name:'protocoloId', mapping:'idProtocolo'},
                {name:'protocoloNombre', mapping:'nombreProtocolo'},
                {name:'esPreferenciaProtocolo', mapping:'esPreferenciaProtocolo'}
              ]
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridProtocolo.getView().refresh();
            }
        }
    });
    
    var selProtocolos = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridProtocolo.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de protocolos
    gridProtocolo = Ext.create('Ext.grid.Panel', {
        id:'gridProtocolo',
        store: storeProtocolos,
        columnLines: true,
        columns: [{
            id: 'idModeloProtocolo',
            header: 'idModeloProtocolo',
            dataIndex: 'idModeloProtocolo',
            hidden: true,
            hideable: false
        },{
            id: 'protocoloId',
            header: 'idProtocolo',
            dataIndex: 'protocoloId',
            hidden: true,
            hideable: false
        },{
            id: 'protocoloNombre',
            header: 'Protocolo',
            dataIndex: 'protocoloNombre',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.protocoloNombre) == "number")
                {
                    
                    record.data.protocoloId = record.data.protocoloNombre;
                    for (var i = 0;i< storeProtocolo.data.items.length;i++)
                    {
                        if (storeProtocolo.data.items[i].data.idProtocolo == record.data.protocoloId)
                        {
                            record.data.protocoloNombre = storeProtocolo.data.items[i].data.nombreProtocolo;
                            break;
                        }
                    }
                }
                return record.data.protocoloNombre;
            },
            editor: {
                id:'searchProtocolos_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreProtocolo',
                valueField: 'idProtocolo',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeProtocolo,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('Protocolo', {
                            protocoloId: combo.getValue(),
                            protocoloNombre: combo.lastSelectionText,
                            esPreferenciaProtocolo: ''
                        });
                        if(!existeRecordProtocolo(r, gridProtocolo))
                        {
                            Ext.get('searchProtocolos_cmp').dom.value='';
                            if(r.protocoloId != 'null')
                            {
                                Ext.get('searchProtocolos_cmp').dom.value=r.get('protocoloNombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridProtocolo);
                        }
                    }
                }
            }
        },{
            id: 'esPreferenciaProtocolo',
            header: 'Es Preferencia',
            dataIndex: 'esPreferenciaProtocolo',
            width: 100,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    record.data.esPreferenciaProtocolo = record.data.esPreferenciaProtocolo;
                    for(var i = 0;i< comboEsPreferenciaProtocolo.data.items.length;i++){
                        if(comboEsPreferenciaProtocolo.data.items[i].data.esPreferenciaProtocolo == value){
                            record.data.esPreferenciaProtocolo = comboEsPreferenciaProtocolo.data.items[i].data.esPreferenciaProtocolo;                            
                            break;
                        }
                    }                    
                    return record.data.esPreferenciaProtocolo;
                },
            editor   :  {
                    xtype: 'combobox',
                    id:'comboEsPreferenciaProtocolo',
                    name: 'comboEsPreferenciaProtocolo',
                    store: comboEsPreferenciaProtocolo,
                    displayField: 'esPreferenciaProtocolo',
                    valueField: 'esPreferenciaProtocolo',
                    queryMode: 'local',
                    lazyRender: true,
                    emptyText: '',
                    forceSelection: true
                }
        }],
        selModel: selProtocolos,
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
                handler : function(){eliminarSeleccion(gridProtocolo);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Protocolo', { 
                        protocoloId: '',
                            protocoloNombre: '',
                            esPreferenciaProtocolo: ''
                    });
                    if(!existeRecordProtocolo(r, gridProtocolo))
                    {
                        storeProtocolos.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 425,
        height: 200,
        frame: true,
        title: 'Agregar Protocolo',
        renderTo: 'gridProtocolos',
        plugins: [cellEditing]
    });
    
    //-------------------------------------------------------------------------------
    
    var storeTecnologia = new Ext.data.Store({  
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url : '../../admi_tecnologia/getTecnologias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idTecnologia', mapping:'idTecnologia'},
              {name:'nombreTecnologia', mapping:'nombreTecnologia'}
            ]
    });
    
    Ext.define('Tecnologia', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idModeloTecnologia', mapping:'idModeloTecnologia'},
            {name:'tecnologiaId', mapping:'tecnologiaId'},
            {name:'tecnologiaNombre', mapping:'tecnologiaNombre'}
        ]
    });
    
    var storeTecnologias = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloTecnologias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idModeloTecnologia', mapping:'idModeloTecnologia'},
                {name:'tecnologiaId', mapping:'idTecnologia'},
                {name:'tecnologiaNombre', mapping:'nombreTecnologia'}
              ]
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridTecnologia.getView().refresh();
            }
        }
    });
    
    var selTecnologias = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridTecnologia.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de tecnologias
    gridTecnologia = Ext.create('Ext.grid.Panel', {
        id:'gridTecnologia',
        store: storeTecnologias,
        columnLines: true,
        columns: [{
            id: 'idModeloTecnologia',
            header: 'idModeloTecnologia',
            dataIndex: 'idModeloTecnologia',
            hidden: true,
            hideable: false
        },{
            id: 'tecnologiaId',
            header: 'idTecnologia',
            dataIndex: 'tecnologiaId',
            hidden: true,
            hideable: false
        },{
            id: 'tecnologiaNombre',
            header: 'Tecnologia',
            dataIndex: 'tecnologiaNombre',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.tecnologiaNombre) == "number")
                {
                    
                    record.data.tecnologiaId = record.data.tecnologiaNombre;
                    for (var i = 0;i< storeTecnologia.data.items.length;i++)
                    {
                        if (storeTecnologia.data.items[i].data.idTecnologia == record.data.tecnologiaId)
                        {
                            record.data.tecnologiaNombre = storeTecnologia.data.items[i].data.nombreTecnologia;
                            break;
                        }
                    }
                }
                return record.data.tecnologiaNombre;
            },
            editor: {
                id:'searchTecnologias_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreTecnologia',
                valueField: 'idTecnologia',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeTecnologia,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('Tecnologia', {
                            tecnologiaId: combo.getValue(),
                            tecnologiaNombre: combo.lastSelectionText,
                            esPreferenciaTecnologia: ''
                        });
                        if(!existeRecordTecnologia(r, gridTecnologia))
                        {
                            Ext.get('searchTecnologias_cmp').dom.value='';
                            if(r.tecnologiaId != 'null')
                            {
                                Ext.get('searchTecnologias_cmp').dom.value=r.get('tecnologiaNombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridTecnologia);
                        }
                    }
                }
            }
        }],
        selModel: selTecnologias,
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
                handler : function(){eliminarSeleccion(gridTecnologia);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Tecnologia', { 
                        tecnologiaId: '',
                            tecnologiaNombre: ''
                    });
                    if(!existeRecordTecnologia(r, gridTecnologia))
                    {
                        storeTecnologias.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 425,
        height: 200,
        frame: true,
        title: 'Agregar Tecnologia',
        renderTo: 'gridTecnologias',
        plugins: [cellEditing]
    });
    
    //-----------------------------------------------------------------------------------------
    var storeDetalleModelo = new Ext.data.Store({  
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : '../../admi_detalle/getDetalles',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                      [
                        {name:'idDetalle', mapping:'idDetalle'},
                        {name:'nombreDetalle', mapping:'nombreDetalle'}
                      ]
        });
        
    
    Ext.define('DetalleModelo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idDetalleModelo', mapping:'idDetalleModelo'},
            {name:'detalleModeloId', mapping:'idDetalle'},
            {name:'detalleModeloNombre', mapping:'nombreDetalle'}
        ]
    });
    
    var storeDetallesModelo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getDetallesModelo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idDetalleModelo', mapping:'idDetalleModelo'},
                {name:'detalleModeloId', mapping:'idDetalle'},
                {name:'detalleModeloNombre', mapping:'nombreDetalle'}
              ]
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridDetallesModelo.getView().refresh();
            }
        }
    });
    
    var selDetalleModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridDetallesModelo.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de detalles de modelo
    gridDetallesModelo = Ext.create('Ext.grid.Panel', {
        id:'gridDetallesModelo',
        store: storeDetallesModelo,
        columnLines: true,
        columns: [{
            id: 'idDetalleModelo',
            header: 'idDetalleModelo',
            dataIndex: 'idDetalleModelo',
            hidden: true,
            hideable: false
        },{
            id: 'detalleModeloId',
            header: 'detalleModeloId',
            dataIndex: 'detalleModeloId',
            hidden: true,
            hideable: false
        },{
            id: 'detalleModeloNombre',
            header: 'Caracteristica',
            dataIndex: 'detalleModeloNombre',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.detalleModeloNombre) == "number")
                {
                    
                    record.data.detalleModeloId = record.data.detalleModeloNombre;
                    for (var i = 0;i< storeDetalleModelo.data.items.length;i++)
                    {
                        if (storeDetalleModelo.data.items[i].data.idDetalle == record.data.detalleModeloId)
                        {
                            record.data.detalleModeloNombre = storeDetalleModelo.data.items[i].data.nombreDetalle;
                            break;
                        }
                    }
                }
                return record.data.detalleModeloNombre;
            },
            editor: {
                id:'searchDetalleModelo_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreDetalle',
                valueField: 'idDetalle',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeDetalleModelo,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('DetalleModelo', {
                            detalleModeloId: combo.getValue(),
                            detalleModeloNombre: combo.lastSelectionText
                        });
                        if(!existeRecordDetalleModelo(r, gridDetallesModelo))
                        {
                            Ext.get('searchDetalleModelo_cmp').dom.value='';
                            if(r.detalleModeloId != 'null')
                            {
                                Ext.get('searchDetalleModelo_cmp').dom.value=r.get('detalleModeloNombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridDetallesModelo);
                        }
                    }
                }
            }
        }],
        selModel: selDetalleModelo,
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
                handler : function(){eliminarSeleccion(gridDetallesModelo);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('DetalleModelo', { 
                            detalleModeloId: '',
                            detalleModeloNombre: ''
                    });
                    if(!existeRecordDetalleModelo(r, gridDetallesModelo))
                    {
                        storeDetallesModelo.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],
    

        width: 425,
        height: 200,
        frame: true,
        title: 'Agregar Caracteristicas Modelo',
        renderTo: 'gridDetallesModelo',
        plugins: [cellEditing]
    });
    
});

function agregarCaracteristica(data){
    var storeDetalle = new Ext.data.Store({  
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : '../../admi_detalle/getDetalles',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                      [
                        {name:'idDetalle', mapping:'idDetalle'},
                        {name:'nombreDetalle', mapping:'nombreDetalle'}
                      ]
        });
        
    
    Ext.define('Detalle', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idDetalleInterface', mapping:'idDetalleInterface'},
            {name:'idDetalle', mapping:'idDetalle'},
            {name:'nombreDetalle', mapping:'nombreDetalle'}
        ]
    });

    if(data.caracteristicasInterface == '' ){
        
        storeDetalles = Ext.create('Ext.data.Store', {
            // destroy the store if the grid is destroyed
            autoDestroy: true,
            autoLoad: false,
            model: 'Detalle',        
            proxy: {
                type: 'ajax',
                // load remote data using HTTP
                url: 'gridDetalles',
                // specify a XmlReader (coincides with the XML format of the returned data)
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    // records will have a 'plant' tag
                    root: 'detalles'
                }
            }
        });
    }
    else{
//        console.log(data.caracteristicasInterface);
        storeDetalles = Ext.create('Ext.data.Store', {
            // destroy the store if the grid is destroyed
            pageSize: 100,
            autoLoad: true,
            model: 'Detalle',
            data: Ext.JSON.decode(data.caracteristicasInterface),
            proxy: {
                type: 'memory',
            
                // specify a XmlReader (coincides with the XML format of the returned data)
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    // records will have a 'plant' tag
                    root: 'detalles'
                }
            }
        });
    }
    
    
        
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridDetalles.getView().refresh();
            }
        }
    });
    
    var selDetalle = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridDetalles.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    gridDetalles = Ext.create('Ext.grid.Panel', {
        id:'gridDetalles',
        store: storeDetalles,
        columnLines: true,
        columns: [{
            id: 'idDetalleInterface',
            header: 'idDetalleInterface',
            dataIndex: 'idDetalleInterface',
            hidden: true,
            hideable: false
        },{
            id: 'idDetalle',
            header: 'idDetalle',
            dataIndex: 'idDetalle',
            hidden: true,
            hideable: false
        },{
            id: 'nombreDetalle',
            header: 'Caracteristica',
            dataIndex: 'nombreDetalle',
            width: 200,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.nombreDetalle) == "number")
                {
                    
                    record.data.idDetalle = record.data.nombreDetalle;
                    for (var i = 0;i< storeDetalle.data.items.length;i++)
                    {
                        if (storeDetalle.data.items[i].data.idDetalle == record.data.idDetalle)
                        {
                            record.data.nombreDetalle = storeDetalle.data.items[i].data.nombreDetalle;
                            break;
                        }
                    }
                }
                return record.data.nombreDetalle;
            },
            editor: {
                id:'searchDetalle_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreDetalle',
                valueField: 'idDetalle',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeDetalle,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('Detalle', {
                            idDetalleInterface: '',
                            idDetalle: combo.getValue(),
                            nombreDetalle: combo.lastSelectionText
                        });
                        if(!existeRecordDetalle(r, gridDetalles))
                        {
                            Ext.get('searchDetalle_cmp').dom.value='';
                            if(r.idDetalle != 'null')
                            {
                                Ext.get('searchDetalle_cmp').dom.value=r.get('nombreDetalle');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridDetalles);
                        }
                    }
                }
            }
        }],
        selModel: selDetalle,
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
                handler : function(){eliminarSeleccion(gridDetalles);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Detalle', { 
                            idDetalleInterface: '',
                            idDetalle: '',
                            nombreDetalle: ''
                    });
                    if(!existeRecordDetalle(r, gridDetalles))
                    {
                        storeDetalles.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],
    
        buttons: [{
            text: 'Guardar Caracteristicas',
            formBind: true,
            handler: function(){
                var datos = "";
//                        var puerto = Ext.getCmp('puerto').value;
//                        var mac = Ext.getCmp('mac').value;
//                        var vlan = Ext.getCmp('vlan').value;
//                        datos = idDispositivo+":"+puerto+":"+mac+":"+vlan;
                if(true){
                    obtenerCaracteristicas(data);
                    win.destroy();
                }
                else{
                    Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                            if(btn=='ok'){
                            }
                    });
                }

            }
        },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }],

        width: 850,
        height: 200,
        frame: true,
        title: 'Agregar Caracteristicas',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
    
    // manually trigger the data store load
    if(data.caracteristicasInterface != '')
    {
        console.log("load");
        gridDetalles.getStore().load();
    }
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar Caracteristica',
        modal: true,
        width: 260,
        closable: false,
        layout: 'fit',
        items: [gridDetalles]
    }).show();
}

function eliminarSeleccion(datosSelect)
{
  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
  {
	datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}

function existeRecordDetalle(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('idDetalle');

    if((canton == myRecord.get('idDetalle') ) || canton == myRecord.get('idDetalle'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function existeRecordRelacion(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('tipoInterfaceId');

    if((canton == myRecord.get('tipoInterfaceId') ) || canton == myRecord.get('tipoInterfaceId'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function existeRecordUsuario(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('usuarioAccesoId');

    if((canton == myRecord.get('usuarioAccesoId') ) || canton == myRecord.get('usuarioAccesoId'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function existeRecordProtocolo(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('protocoloId');

    if((canton == myRecord.get('protocoloId') ) || canton == myRecord.get('protocoloId'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function existeRecordTecnologia(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('tecnologiaId');

    if((canton == myRecord.get('tecnologiaId') ) || canton == myRecord.get('tecnologiaId'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function existeRecordDetalleModelo(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('detalleModeloId');

    if((canton == myRecord.get('detalleModeloId') ) || canton == myRecord.get('detalleModeloId'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function obtenerCaracteristicas(data)
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridDetalles.getStore().getCount();
  array_relaciones['detalles'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridDetalles.getStore().getCount(); i++)
  {
  	array_data.push(gridDetalles.getStore().getAt(i).data);
  }
  array_relaciones['detalles'] = array_data;
  data.caracteristicasInterface = Ext.JSON.encode(array_relaciones);
}

function obtenerInterfaces()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridInterfacesModelos.getStore().getCount();
  array_relaciones['interfacesModelos'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridInterfacesModelos.getStore().getCount(); i++)
  {
  	array_data.push(gridInterfacesModelos.getStore().getAt(i).data);
  }
  array_relaciones['interfacesModelos'] = array_data;
  Ext.get('telconet_schemabundle_admimodeloelementotype_interfacesModelos').dom.value = Ext.JSON.encode(array_relaciones);
}

function obtenerUsuarios()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridUsuariosAcceso.getStore().getCount();
  array_relaciones['usuariosAcceso'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridUsuariosAcceso.getStore().getCount(); i++)
  {
  	array_data.push(gridUsuariosAcceso.getStore().getAt(i).data);
  }
  array_relaciones['usuariosAcceso'] = array_data;
  Ext.get('telconet_schemabundle_admimodeloelementotype_usuariosAcceso').dom.value = Ext.JSON.encode(array_relaciones);
}

function obtenerProtocolos()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridProtocolo.getStore().getCount();
  array_relaciones['protocolos'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridProtocolo.getStore().getCount(); i++)
  {
  	array_data.push(gridProtocolo.getStore().getAt(i).data);
  }
  array_relaciones['protocolos'] = array_data;
  Ext.get('telconet_schemabundle_admimodeloelementotype_protocolos').dom.value = Ext.JSON.encode(array_relaciones);
}

function obtenerTecnologias()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridTecnologia.getStore().getCount();
  array_relaciones['tecnologias'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridTecnologia.getStore().getCount(); i++)
  {
  	array_data.push(gridTecnologia.getStore().getAt(i).data);
  }
  array_relaciones['tecnologias'] = array_data;
  Ext.get('telconet_schemabundle_admimodeloelementotype_tecnologias').dom.value = Ext.JSON.encode(array_relaciones);
}

function obtenerDetallesModelo()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridDetallesModelo.getStore().getCount();
  array_relaciones['detallesModelo'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridDetallesModelo.getStore().getCount(); i++)
  {
  	array_data.push(gridDetallesModelo.getStore().getAt(i).data);
  }
  array_relaciones['detallesModelo'] = array_data;
  Ext.get('telconet_schemabundle_admimodeloelementotype_detallesModelo').dom.value = Ext.JSON.encode(array_relaciones);
}

function validarFormulario()
{
  obtenerInterfaces();
  obtenerUsuarios();
  obtenerProtocolos();
  obtenerDetallesModelo();
  obtenerTecnologias();
  
  return true;
}