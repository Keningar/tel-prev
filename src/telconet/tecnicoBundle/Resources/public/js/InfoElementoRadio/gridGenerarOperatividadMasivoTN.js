Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
Ext.onReady(function()
{
    document.getElementById('div_count_servicios').innerHTML = 0;
    var intIdPunto = null;
    Ext.create('Ext.Button', {
        text:      'Registros Agregados para ' + strNombreProceso,
        renderTo:  Ext.get('btn_generar_masivo'),
        iconCls:   'iconSave',
        width:     '100%',
        cls:       'x-btn-item-medium',
        id:        'btnGenerarMasivo',
        itemId:    'btnGenerarMasivo',
        textAlign: 'center',
        listeners: {
            click: function() {
                gridServiciosSeleccionados();
            }
        }
    });
    storeServicios = Ext.create('Ext.data.Store',{
        model: 'ListModelRazonSocial',
        autoLoad: booleanPerEmp,
        pageSize: 5000,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_servicios_clientes,
            actionMethods: 'POST',
            timeout: 3000000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                tipoProceso:      strTipoProceso,
                intIdPerEmpRol:   intIdPerEmpRol,
                intIdPunto:       intIdPunto,
                arrayIdServicios: Ext.encode(arrayIdServicios)
            }
        },
        fields:
        [
            {name:'idLogin',              mapping:'idLogin'},
            {name:'login',                mapping:'login'},
            {name:'idElemento',           mapping:'idElemento'},
            {name:'nombreElemento',       mapping:'nombreElemento'},
            {name:'estado',               mapping:'estado'},
            {name:'nombreModeloElemento', mapping:'nombreModeloElemento'},
            {name:'nombreMarcaElemento',  mapping:'nombreMarcaElemento'},
            {name:'esOperativo',          mapping:'esOperativo'}
        ]
    });
    gridServicios  = Ext.create('Ext.grid.Panel', {
	title: 'Listado de Radio',
        id: 'gridServicios',
        width: '100%',
        height: 450,
        store: storeServicios,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        renderTo: Ext.get('lista_servicios'),
        dockedItems:[
        {
            id:    'toolbarButtonsServicios',
            xtype: 'toolbar',
            dock:  'top',
            align: 'left',
            items:
            [
                {
                    iconCls:  'icon_add',
                    text:     'Agregar Seleccionados',
                    id:       'btnAddSeleccionados',
                    itemId:   'btnAddSeleccionados',
                    scope:     this,
                    cls:       'button-text-green x-btn-item-medium',
                    handler:   function()
                    {
                        var xRowSelMod = Ext.getCmp('gridServicios').getSelectionModel().getSelection();
                        if( xRowSelMod.length > 0 && xRowSelMod.length <= intMaxServiciosAgregar && 
                            (arrayIdServicios.length + xRowSelMod.length) <= intMaxServiciosAgregar ){
                            xRowSelMod.map(item=>{
                                if( !arrayIdServicios.includes(item.get('idElemento')) ){
                                    arrayIdServicios.push(item.get('idElemento'));
                                    var objectDatos = {
                                        idElemento:           item.get('idElemento'),
                                        esOperativo:          item.get('esOperativo')
                                    };
                                    arrayObjServicios.push(objectDatos);
                                    var objectServicio = {
                                        idLogin:              item.get('idLogin'),
                                        login:                item.get('login'),
                                        idElemento:           item.get('idElemento'),
                                        nombreElemento:       item.get('nombreElemento'),
                                        estado:               item.get('estado'),
                                        nombreModeloElemento: item.get('nombreModeloElemento'),
                                        nombreMarcaElemento:  item.get('nombreMarcaElemento'),
                                        esOperativo:          item.get('esOperativo')
                                    };
                                    arrayDatosServicios.push(objectServicio);
                                }
                            });
                            document.getElementById('div_count_servicios').innerHTML = arrayIdServicios.length;
                            updateServiciosPorRazonSocial();
                        }else if(xRowSelMod.length > intMaxServiciosAgregar){
                            Ext.Msg.show({
                                title:   'Informaci\xf3n',
                                msg:     'Se ha excedido el límite de los ' + intMaxServiciosAgregar + ' servicios seleccionados.',
                                buttons:  Ext.Msg.OK,
                                icon:     Ext.MessageBox.INFO
                            });
                        }else if((arrayIdServicios.length + xRowSelMod.length) > intMaxServiciosAgregar){
                            Ext.Msg.show({
                                title:   'Informaci\xf3n',
                                msg:     'Se ha excedido el límite de los ' + intMaxServiciosAgregar + ' servicios agregados.',
                                buttons:  Ext.Msg.OK,
                                icon:     Ext.MessageBox.INFO
                            });
                        }else{
                            Ext.Msg.show({
                                title:   'Informaci\xf3n',
                                msg:     'No ha seleccionado ningún servicio.',
                                buttons:  Ext.Msg.OK,
                                icon:     Ext.MessageBox.INFO
                            });
                        }
                    }
                }
            ]
        }],
        columns:[
            {
              id: 'idElemento',
              header: 'idElemento',
              dataIndex: 'idElemento',
              hidden: true,
              hideable: false
            },
            {
              header: 'Nombre Radio',
              dataIndex: 'nombreElemento',
              width: '25%',
              sortable: true
            },
            {
              header: 'Marca',
              dataIndex: 'nombreMarcaElemento',
              width: '20%',
              sortable: true
            },
            {
              header: 'Modelo',
              dataIndex: 'nombreModeloElemento',
              width: '20%',
              sortable: true
            },
            {
              header: 'Elemento Opertaivo',
              dataIndex: 'esOperativo',
              width: '15%',
              sortable: true
            },
            {
              header: 'Estado',
              dataIndex: 'estado',
              width: '15%',
              sortable: true
            }
            
        ],
        selModel: {
            checkOnly: false,
            injectCheckbox: 'first',
            mode: 'SIMPLE'
        },
        selType: 'checkboxmodel',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeServicios,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    gridServicios.getStore().sort('login', 'ASC');
    Ext.create('Ext.Button', {
        text:      'Limpiar',
        width:     '100%',
        renderTo:  Ext.get('btn_limpiar'),
        iconCls:   'icon_limpiar',
        cls:       'x-btn-item-medium',
        id:        'btnLimpiar',
        itemId:    'btnLimpiar',
        textAlign: 'center',
        listeners: {
            click: function() {
                intIdPerEmpRol      = null;
                intIdPunto          = null;
                arrayIdServicios    = [];
                arrayObjServicios   = [];
                arrayDatosServicios = [];
                document.getElementById('div_count_servicios').innerHTML = 0;
                document.getElementById('label_tipo_identificacion_cliente').innerHTML = '';
                document.getElementById('label_identificacion_cliente').innerHTML = '';
                Ext.getCmp('cbxRazonSocial').setValue('');
                Ext.getCmp('cbxRazonSocial').setRawValue("");
                Ext.getCmp('cbxRazonSocial').setDisabled(false);
                Ext.getCmp('cbxLoginsRazonSocial').setValue('');
                Ext.getCmp('cbxLoginsRazonSocial').setRawValue("");
                Ext.getCmp('cbxLoginsRazonSocial').setDisabled(true);
                updateServiciosPorRazonSocial();
            }
        }
    });
    storeRazonSocial = Ext.create('Ext.data.Store',{
        model: 'ListModelRazonSocial',
        autoLoad: !booleanPerEmp,
        proxy:
        {
            type: 'ajax',
            url: url_razon_social,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
        [
            {name: 'id',     mapping: 'id',     type: 'string'},
            {name: 'nombre', mapping: 'nombre', type: 'string'},
            {name: 'tipo_identificacion', mapping: 'tipo_identificacion', type: 'string'},
            {name: 'identificacion',      mapping: 'identificacion',      type: 'string'}
        ]
    });
    new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeRazonSocial,
        labelAlign: 'left',
        name: 'cbxRazonSocial',
        id: 'cbxRazonSocial',
        valueField: 'id',
        displayField: 'nombre',
        fieldLabel: '',
        loadingText: 'Buscando ...',
        width: 400,
        emptyText: 'Digite la Razon Social o Identificación para la busqueda',
        renderTo: 'combo_razon_social',
        allowBlank: true,
        disabled: false,
        matchFieldWidth: true,
        queryMode: 'remote',
        listeners:
        {
            select:
            {
                fn: function(combo,  records, index)
                {
                    intIdPerEmpRol = combo.getValue();
                    document.getElementById('label_tipo_identificacion_cliente').innerHTML = records[0].get('tipo_identificacion') + ': ';
                    document.getElementById('label_identificacion_cliente').innerHTML = records[0].get('identificacion');
                    Ext.getCmp('cbxRazonSocial').setDisabled(true);
                    storeLogins.getProxy().extraParams = {};
                    storeLogins.getProxy().extraParams.tipoProceso    = strTipoProceso;
                    storeLogins.getProxy().extraParams.intIdPerEmpRol = intIdPerEmpRol;
                    storeLogins.getProxy().timeout                    = 300000;
                    storeLogins.load();
                    Ext.getCmp('cbxLoginsRazonSocial').setDisabled(false);
                    updateServiciosPorRazonSocial();
                }
            },
            click:
            {
                element: 'el'
            }
        }
    });
    storeLogins = Ext.create('Ext.data.Store',{
        model: 'ListModelLogins',
        autoLoad: booleanPerEmp,
        proxy: {
            type: 'ajax',
            url : url_logins_clientes,
            timeout: 3000000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                tipoProceso:    strTipoProceso,
                intIdPerEmpRol: intIdPerEmpRol
            }
        },
        fields:
        [
            {name: 'id',     mapping: 'id',     type: 'string'},
            {name: 'login',  mapping: 'login',  type: 'string'}
        ]
    });
    new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeLogins,
        labelAlign: 'left',
        name: 'cbxLoginsRazonSocial',
        id: 'cbxLoginsRazonSocial',
        valueField: 'id',
        displayField: 'login',
        fieldLabel: '',
        loadingText: 'Buscando ...',
        width: 400,
        emptyText: 'Digite el Login para la busqueda',
        renderTo: 'combo_logins_razon_social',
        allowBlank: true,
        disabled: true,
        matchFieldWidth: true,
        queryMode: 'remote',
        listeners:
        {
            select:
            {
                fn: function(combo)
                {
                    intIdPunto = combo.getValue();
                    updateServiciosPorRazonSocial();
                }
            },
            click:
            {
                element: 'el'
            }
        }
    });
    
    if(booleanPerEmp)
    {
        Ext.getCmp('cbxRazonSocial').setValue(strRazonSocial);
        Ext.getCmp('cbxRazonSocial').setDisabled(true);
        Ext.getCmp('cbxLoginsRazonSocial').setDisabled(false);
    }

    function updateServiciosPorRazonSocial(){
        storeServicios.getProxy().extraParams = {};
        storeServicios.getProxy().extraParams.tipoProceso      = strTipoProceso;
        storeServicios.getProxy().extraParams.intIdPerEmpRol   = intIdPerEmpRol;
        storeServicios.getProxy().extraParams.intIdPunto       = intIdPunto;
        storeServicios.getProxy().extraParams.arrayIdServicios = Ext.encode(arrayIdServicios);
        storeServicios.getProxy().timeout                      = 300000;
        storeServicios.on('load', function(store, records, successful){
            if( intIdPerEmpRol != null && ( intIdPunto == null || intIdPunto == '' ) && store.getCount() == 0 ){
                Ext.getCmp('cbxLoginsRazonSocial').setDisabled(true);
            }
            else if( intIdPerEmpRol != null ) {
                Ext.getCmp('cbxLoginsRazonSocial').setDisabled(false);
            }
        });
        storeServicios.load();
    }
    function ejecutarOperatividadMasivo(){
        if( arrayIdServicios.length > 0 )
        {
            Ext.Msg.confirm('Alerta','Está seguro de generar la ' + strMinNombreProceso +
                            ' para los radios agregados.',
            function(btn){
                if (btn == 'yes'){
                    win2.destroy();
                    connGrabandoDatos.request({
                        url:    url_generar_masivo_operatividad,
                        method: 'post',
                        params:{
                            tipoProceso:      strTipoProceso,
                            arrayObjServicios: Ext.encode(arrayObjServicios)
                        },
                        success: function(response){
                            var result = Ext.decode(response.responseText);
                            if (result.status == 'OK'){
                                Ext.Msg.show({
                                    title:   'Informaci\xf3n',
                                    msg:     result.mensaje,
                                    buttons: Ext.Msg.OK,
                                    icon:    Ext.MessageBox.INFO
                                });
                                arrayIdServicios    = [];
                                arrayObjServicios   = [];
                                arrayDatosServicios = [];
                                document.getElementById('div_count_servicios').innerHTML = 0;
                                updateServiciosPorRazonSocial();
                            }
                            else{
                                Ext.Msg.show({
                                    title:   'Error',
                                    msg:      result.mensaje,
                                    buttons:  Ext.Msg.OK,
                                    icon:     Ext.MessageBox.ERROR
                                });
                            }
                        },
                        failure: function(response){
                            Ext.Msg.show({
                                title:   'Error',
                                msg:     'Error: ' + response.statusText,
                                buttons:  Ext.Msg.OK,
                                icon:     Ext.MessageBox.ERROR
                            });
                        }
                    });
                }
            });
        }
        else
        {
            Ext.Msg.show({
                title:   'Informaci\xf3n',
                msg:     'No se ha agregado ningún registro.',
                buttons:  Ext.Msg.OK,
                icon:     Ext.MessageBox.INFO
            });
        }
    }
    function gridServiciosSeleccionados(){
        if( arrayIdServicios.length > 0 ){
            dataStoreDatosServicios = new Ext.data.ArrayStore({
                storeId: 'dataStoreDatosServicios',
                idIndex: 0,
                autoDestroy: true,
                autoLoad: true,
                data: arrayDatosServicios,
                fields:[
                    {name:'idLogin',              mapping:'idLogin'},
                    {name:'login',                mapping:'login'},
                    {name:'idElemento',           mapping:'idElemento'},
                    {name:'nombreElemento',       mapping:'nombreElemento'},
                    {name:'estado',               mapping:'estado'},
                    {name:'nombreModeloElemento', mapping:'nombreModeloElemento'},
                    {name:'nombreMarcaElemento',  mapping:'nombreMarcaElemento'},
                    {name:'esOperativo',          mapping:'esOperativo'}
                ]
            });
            gridDatosServiciosSeleccionados = Ext.create('Ext.grid.Panel',{
                id: 'gridDatosServiciosSeleccionados',
                store: dataStoreDatosServicios,
                width: 800,
                height: 300,
                loadMask: true,
                frame: false,
                buttons:[
                    {
                        iconCls: 'icon_cerrar',
                        text:    'Cerrar',
                        cls:     'x-btn-item-medium',
                        handler: function(){
                            win2.destroy();
                        }
                    },
                    {
                        iconCls: 'iconSave',
                        text:    'Generar',
                        cls:     'x-btn-item-medium',
                        handler: function(){
                            ejecutarOperatividadMasivo();
                        }
                    }
                ],
                columns:[
                    {
                        header: 'Nombre Radio',
                        dataIndex: 'nombreElemento',
                        width: '25%',
                        sortable: true
                    },
                    {
                        header: 'Marca',
                        dataIndex: 'nombreMarcaElemento',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Modelo',
                        dataIndex: 'nombreModeloElemento',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Elemento Operativo',
                        dataIndex: 'esOperativo',
                        width: '20%',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: '15%',
                        sortable: true
                    },
                    {
                        xtype:  'actioncolumn',
                        header: 'Acciones',
                        width: '10%',
                        items:[{
                            iconCls: "button-grid-delete",
                            tooltip: 'Quitar',
                            handler: function(grid, rowIndex){
                                var rec = dataStoreDatosServicios.getAt(rowIndex);
                                var strMessage = 'Está seguro de quitar el registro: ' + rec.get('nombreElemento');
                                if( rec.get('nombreElemento') == null ){
                                    strMessage = 'Está seguro de quitar el registro.';
                                }
                                Ext.Msg.confirm('Alerta', strMessage,
                                function(btn){
                                    if (btn === 'yes'){
                                        dataStoreDatosServicios.removeAt(rowIndex);
                                        var indexIdServicios = arrayIdServicios.indexOf(rec.get('idElemento'));
                                        if(indexIdServicios >= 0){
                                            arrayIdServicios.splice(indexIdServicios,1);
                                        }
                                        
                                        var indexObjServicios = null;
                                        arrayObjServicios.forEach(function(object, index){
                                            if( object.idElemento == rec.get('idElemento') ){
                                                indexObjServicios = index;
                                            }
                                        });
                                        if(indexObjServicios >= 0){
                                            arrayObjServicios.splice(indexObjServicios,1);
                                        }
                                        
                                        var indexDatosServicios = null;
                                        arrayDatosServicios.forEach(function(object, index){
                                            if( object.idServicio == rec.get('idElemento') ){
                                                indexDatosServicios = index;
                                            }
                                        });
                                        if(indexDatosServicios >= 0){
                                            arrayDatosServicios.splice(indexDatosServicios,1);
                                        }
                                        document.getElementById('div_count_servicios').innerHTML = arrayIdServicios.length;
                                        updateServiciosPorRazonSocial();
                                    }
                                });
                            }
                        }]
                    }
                ]
            });
            Ext.create('Ext.form.Panel',{
                id: 'formDatosServicios',
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults:{
                    labelAlign: 'left',
                    labelWidth: 200,
                    msgTarget:  'side'
                },
                items:[{
                    xtype: 'fieldset',
                    title: '',
                    defaultType: 'textfield',
                    defaults:{
                        width: 800
                    },
                    layout:{
                        type: 'table',
                        columns: 5,
                        align: 'left'
                    },
                    items:[
                        gridDatosServiciosSeleccionados
                    ]
                }]
            });
            win2 = Ext.create('Ext.window.Window',{
                title: 'Lista de Registros Agregados para ' + strNombreProceso,
                modal: true,
                width: 800,
                closable: true,
                layout: 'fit',
                items: [gridDatosServiciosSeleccionados]
            }).show();
        }
        else{
            Ext.Msg.show({
                title:   'Informaci\xf3n',
                msg:     'No se ha agregado ningún servicio.',
                buttons:  Ext.Msg.OK,
                icon:     Ext.MessageBox.INFO
            });
        }
    }

    var connGrabandoDatos = new Ext.data.Connection(
    {
        listeners:
        {
            'beforerequest':
            {
                fn: function()
                {
                    Ext.MessageBox.show(
                    {
                        msg:          'Generando ' + strMinNombreProceso + ', por favor espere!!',
                        progressText: 'Saving...',
                        width:         300,
                        wait:          true,
                        waitConfig:
                        {
                            interval: 200
                        }
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
});
