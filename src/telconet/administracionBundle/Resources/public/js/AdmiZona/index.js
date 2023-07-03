var storeZonas;

Ext.onReady(function() { 

    Ext.tip.QuickTipManager.init();

    var boolEliminaZonas   = true;
    var boolActualizaZonas = true;
    var boolNuevaZona      = true;

    //Elimina Zona
    var permisoEliminar = $("#ROLE_410-5820");
    var boolPermiso = (typeof permisoEliminar === 'undefined') ? false : (permisoEliminar.val() == 1 ? true : false);

    if (!boolPermiso) {
        boolEliminaZonas = true;
    } else {
        boolEliminaZonas = false;
    }

    //Actualiza Zona
    var permisoActualizar = $("#ROLE_410-5819");
    var boolPermiso = (typeof permisoActualizar === 'undefined') ? false : (permisoActualizar.val() == 1 ? true : false);

    if (!boolPermiso) {
        boolActualizaZonas = true;
    } else {
        boolActualizaZonas = false;
    }

    //Nueva Zona
    var permisoIngresar = $("#ROLE_410-5818");
    var boolPermiso = (typeof permisoIngresar === 'undefined') ? false : (permisoIngresar.val() == 1 ? true : false);

    if (!boolPermiso) {
        boolNuevaZona = true;
    } else {
        boolNuevaZona = false;
    }

    storeZonas = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type    : 'ajax',
            timeout : 9600000,
            url     : 'grid',
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                estado: 'Todos'
            }
        },
        fields:
		[
                    {name:'id_zona'        , mapping:'id_zona'},
                    {name:'nombre_zona'    , mapping:'nombre_zona'},
                    {name:'estado'         , mapping:'estado'},
                    {name:'responsableZona', mapping:'responsableZona'}
		],
        autoLoad: true
    });

    sm = Ext.create('Ext.selection.CheckboxModel',
      {
          checkOnly: true
      });

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 700,
        height: 500,
        store: storeZonas,
        selModel: sm,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        iconCls: 'icon-grid',
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'ejecutaAjax',
                        scope: this,
                        hidden: boolEliminaZonas,
                        handler: function() {
                             eliminaZonas();
                        }
                    },
                    {
                        iconCls: 'icon_ingresarTrazabilidad',
                        text: 'Ingresar',
                        itemId: 'ingresarAjax',
                        scope: this,
                        hidden: boolNuevaZona,
                        handler: function() {
                             ingresarZonas();
                        }
                    }
                ]
            }
        ],
        columns:[
                {
                  id: 'nombre_zona',
                  header: 'Nombre Zona',
                  dataIndex: 'nombre_zona',
                  width: 170,
                  sortable: true
                },
                {
                  id        : 'responsableZona',
                  dataIndex : 'responsableZona',
                  header    : 'Responsable Zona',
                  width     : 270,
                  sortable  : true,
                  renderer  : function (value, metaData, record, rowIndex, colIndex, store) {
                        var responsableZona = record.data.responsableZona;
                        if (responsableZona !== null) {
                            responsableZona = responsableZona.split('@@')[0];
                        }
                        return responsableZona;
                  }
                },
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 115,
                    items:
                    [
                        {
                            getClass: function(v, meta, rec)
                            {
                                if(boolActualizaZonas || rec.get('estado') == "Eliminado") {
                                    return '';
                                } else {
                                    return 'button-grid-edit';
                                }
                            },
                            tooltip: 'Editar Zona',
                            handler: function(grid, rowIndex, colIndex)
                            {
                                var rec = storeZonas.getAt(rowIndex);
                                actualizarZona(rec.data.id_zona,rec.data.nombre_zona,rec.data.estado);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if(rec.get('estado') == "Eliminado") {
                                    return '';
                                } else {
                                    return "btn-acciones btn-asignar-jefe";
                                }
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = storeZonas.getAt(rowIndex);
                                mostrarAsignarTarea(rec.data);
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storeZonas,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners:
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                    if (value !== null) {
                        value = value.split('@@')[0];
                    }
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        renderTo: 'grid'
    });

    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding : 7,
        border      : false,
        buttonAlign : 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible : true,
        collapsed   : true,
        width       : 700,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],
                items: [
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'filtro_nombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '200'
                        },
                        { width: '10%',border:false},
                        {
                            xtype      : 'combobox',
                            fieldLabel : 'Estado',
                            id         : 'cmbEstado',
                            value      : 'Todos',
                            width      : '30',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        }
                        ],
        renderTo: 'filtro'
    });
    
});

function ingresarZonas()
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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

    btnguardar = Ext.create('Ext.Button', {
        text    : 'Aceptar',
        cls     : 'x-btn-rigth',
        handler : function()
        {
            var strNombreZona = Ext.getCmp('nombre_zona2').value;

            if(strNombreZona === "" || strNombreZona === null) {
                Ext.Msg.alert('Alerta', '<b>No se puede registrar valores nulos..!!</b>');
                return;
            }

            conn.request({
                method : 'POST',
                url    : url_ingresarZonas,
                params : {
                    nombre_zona: strNombreZona
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.estado === 'Ok') {
                        storeZonas.load();
                        win.destroy();
                        Ext.Msg.alert('Alerta ',json.mensaje);  
                    } else {
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    }
                },
                failure: function(result) {
                    win.destroy();
                    Ext.Msg.show({
                        title   : 'Error',
                        msg     : result.statusText,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.MessageBox.ERROR
                    });
                }
            });
        }
    });

    btncancelar = Ext.create('Ext.Button', {
        text    : 'Cerrar',
        cls     : 'x-btn-rigth',
        handler : function() {
            win.destroy();
        }
    });

    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding   : 5,
        waitMsgTarget : true,
        layout        : 'column',
        fieldDefaults :
        {
            labelAlign : 'left',
            labelWidth : 150,
            msgTarget  : 'side'
        },
        items:
        [
            {
                xtype      : 'fieldset',
                title      : 'Zona',
                autoHeight : true,
                width      : 430,
                items:
                [
                    {
                        xtype      : 'textfield',
                        id         : 'nombre_zona2',
                        fieldLabel : 'Nombre',
                        value      : "",
                        width      : 400
                    }
                ]
            }
        ]
    });

    win = Ext.create('Ext.window.Window', {
            title       : "Ingresar Zona",
            closable    : false,
            modal       : true,
            width       : 470,
            height      : 150,
            resizable   : false,
            layout      : 'fit',
            items       : [formPanel],
            buttonAlign : 'center',
            buttons     :[btnguardar,btncancelar]
    }).show();
}

function actualizarZona(id_zona,nombre_zona,estado)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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

    btnguardar = Ext.create('Ext.Button', {
        text    : 'Aceptar',
        cls     : 'x-btn-rigth',
        handler : function()
        {
            var strNombreZona = Ext.getCmp('nombre_zona1').value;

            if (strNombreZona === "" || strNombreZona === null) {
                Ext.Msg.alert('Alerta ','<b>No se puede registrar valores nulos..!!</b>');
                return;
            }

            conn.request({
                method : 'POST',
                url    : url_actualizarZonas,
                params : {
                    nombre_zona : strNombreZona,
                    id_zona     : id_zona
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.estado === 'ok') {
                        storeZonas.load();
                        win.destroy();
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    } else {
                        Ext.Msg.alert('Error ',json.mensaje);
                    }
                },
                failure: function(result) {
                    win.destroy();
                    Ext.Msg.show({
                        title   : 'Error',
                        msg     : result.statusText,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.MessageBox.ERROR
                    });
                }
            });
        }
    });

    btncancelar = Ext.create('Ext.Button', {
            text    : 'Cerrar',
            cls     : 'x-btn-rigth',
            handler : function() {
                win.destroy();
            }
    });

    formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding   : 5,
            waitMsgTarget : true,
            layout        : 'column',
            fieldDefaults : {
                labelAlign : 'left',
                labelWidth :  130,
                msgTarget  : 'side'
            },
            items:
            [
                {
                    xtype       : 'fieldset',
                    title       : 'Zona',
                    autoHeight  : true,
                    width       : 430,
                    items:
                    [
                        {
                            xtype      : 'textfield',
                            id         : 'nombre_zona1',
                            fieldLabel : 'Nombre',
                            value      : nombre_zona,
                            width      : 400
                        },
                        {
                            xtype       : 'displayfield',
                            fieldLabel  : 'Estado:',
                            id          : 'estado_zona2',
                            name        : 'estado_zona2',
                            value       : estado
                        }
                    ]
                }
            ]
    });

    win = Ext.create('Ext.window.Window', {
            title: "Actualizar Zona",
            closable: false,
            modal: true,
            width: 460,
            height: 170,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons:[btnguardar,btncancelar]
    }).show();
}

function eliminaZonas()
{
    var tramaZonas = "";
    var numeroZonas = 0;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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

    if (sm.getSelection().length > 0) {

        for (var i = 0; i < sm.getSelection().length; ++i) {

            tramaZonas = tramaZonas + sm.getSelection()[i].data.id_zona;

            if (sm.getSelection()[i].data.estado == "Eliminado") {
                numeroZonas = numeroZonas + 1;
            }

            if (i < (sm.getSelection().length - 1)) {
                tramaZonas = tramaZonas + '|';
            }
        }

        if(numeroZonas == 0) {
            Ext.Msg.confirm('Alerta', 'Se Eliminaran las zonas. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    conn.request({
                        url    : url_eliminarZonas,
                        method : 'post',
                        params : {
                            tramaZonas: tramaZonas
                        },
                        success: function(response) {
                            var json = Ext.JSON.decode(response.responseText);
                            if (json.estado == "ok") {
                               Ext.Msg.alert('Alerta', json.mensaje);
                               storeZonas.load();
                            } else {
                                Ext.Msg.alert('Error ', json.mensaje);
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            });
        } else {
            Ext.Msg.alert('Alerta ','Solo se pueden eliminar los registros en estado <b style="color:green;">Activo</b>');
        }
    } else {
        Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un registro de la lista..!!');
    }
}

function buscar()
{
    storeZonas.getProxy().extraParams.nombreZona = Ext.getCmp('filtro_nombre').value;
    storeZonas.getProxy().extraParams.estado     = Ext.getCmp('cmbEstado').value;
    storeZonas.load();
}

function limpiar()
{
    Ext.getCmp('filtro_nombre').value = "";
    Ext.getCmp('filtro_nombre').setRawValue("");
    storeZonas.removeAll();
    grid.getStore().removeAll();
}

function mostrarAsignarTarea(data)
{
    var responableJson = {};
    responableJson['responsable'] = 'Sin asignación';
    if (data.responsableZona !== null) {
        responableJson['responsable']         = data.responsableZona.split('@@')[0];
        responableJson['idPersona']           = data.responsableZona.split('@@')[1];
        responableJson['idPersonaEmpresaRol'] = data.responsableZona.split('@@')[2];
    }

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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

    storeEmpresas = new Ext.data.Store ({
        total    : 'total',
        pageSize : 200,
        async    : false,
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    : strUrlEmpresaPorSistema,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'
            }
        },
        fields:
        [
            {name: 'opcion', mapping: 'nombre_empresa'},
            {name: 'valor', mapping: 'prefijo'}
        ]
    });

    storeCiudades = new Ext.data.Store ({
        total    : 'total',
        pageSize : 200,
        async    : false,
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    : strUrlCiudadesEmpresa,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                nombre : '',
                estado : 'Activo'		
            }
        },
        fields:
        [
            {name:'id_canton',      mapping:'id_canton'},
            {name:'nombre_canton',  mapping:'nombre_canton'}
        ]
    });   

    storeDepartamentosCiudad = new Ext.data.Store ({
        total    : 'total',
        pageSize :  200,
        async    : false,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : strUrlDepartamentosEmpresaCiudad,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'		
            }
        },
        fields:
        [
            {name:'id_departamento',     mapping:'id_departamento'},
            {name:'nombre_departamento', mapping:'nombre_departamento'}
        ]
    });

    storeAsignaEmpleado = new Ext.data.Store ({
        total    : 'total',
        autoLoad : true,
        async    : false,
        proxy: 
        {
            type : 'ajax',
            url  : strUrlEmpleadosDepartamentCiudad,

            reader: 
            {
                type          : 'json',
                totalProperty : 'result.total',
                root          : 'result.encontrados',
                metaProperty  : 'myMetaData'
            }
        },
        fields:
        [
            {name:'id_empleado',     mapping:'id_empleado'},
            {name:'nombre_empleado', mapping:'nombre_empleado'}
        ]
    }); 

    function presentarCiudades(empresa) {
        storeCiudades.proxy.extraParams = { empresa:empresa };
        storeCiudades.load();
    }

    function presentarDepartamentosPorCiudad(id_canton, empresa) {
        storeDepartamentosCiudad.proxy.extraParams = { 
                                                        id_canton : id_canton,
                                                        empresa   : empresa 
                                                     };
        storeDepartamentosCiudad.load();
    }

    function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento) {
        storeAsignaEmpleado.proxy.extraParams = {
                                                    id_canton         : id_canton,
                                                    empresa           : empresa,
                                                    id_departamento   : id_departamento,
                                                    departamento_caso : valorIdDepartamento
                                                };
        storeAsignaEmpleado.load();
    }

    combo_empleados = new Ext.form.ComboBox ({
        id           : 'comboAsignadoEmpleado',
        name         : 'comboAsignadoEmpleado',
        fieldLabel   : "<b>Empleado:</b>",
        store        :  storeAsignaEmpleado,
        displayField : 'nombre_empleado',
        valueField   : 'id_empleado',
        queryMode    : "remote",
        emptyText    : '',
        disabled     : true
    });

    formPanelAsignarResponsable = Ext.create('Ext.form.Panel', {
        bodyPadding   : 5,
        waitMsgTarget : true,
        fieldDefaults: 
        {
            labelAlign : 'left',
            labelWidth : 150,
            msgTarget  : 'side'
        },
        items: 
        [
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue">Actual</b>',
                defaults : { width: 450 },
                items :
                [
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Responsable:</b>',
                        value      : responableJson['responsable'],
                        id         :'cuadrillaDetalle',
                        readOnly   : true
                    }
                ]
            },
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue">Nuevo</b>',
                defaults : { width: 450 },
                items: 
                [
                    {
                        xtype          : 'combobox',
                        fieldLabel     : '<b>Empresa:</b>',
                        id             : 'comboEmpresa',
                        store          : storeEmpresas,
                        displayField   : 'opcion',
                        valueField     : 'valor',
                        queryMode      : "remote",
                        emptyText      : '',
                        forceSelection : true,
                        listeners: {
                            select: function(combo) {
                                Ext.getCmp('comboCiudad').reset();									
                                Ext.getCmp('comboDepartamento').reset();
                                Ext.getCmp('comboAsignadoEmpleado').reset();

                                Ext.getCmp('comboCiudad').setDisabled(false);								
                                Ext.getCmp('comboDepartamento').setDisabled(true);
                                Ext.getCmp('comboAsignadoEmpleado').setDisabled(true);

                                presentarCiudades(combo.getValue());
                            }
                        }
                    }, 
                    {
                        xtype          : 'combobox',
                        fieldLabel     : '<b>Ciudad:</b>',
                        id             : 'comboCiudad',
                        name           : 'comboCiudad',
                        store          : storeCiudades,
                        displayField   : 'nombre_canton',
                        valueField     : 'id_canton',
                        queryMode      : "remote",
                        emptyText      : '',
                        disabled       : true,
                        forceSelection : true,
                        listeners: {
                            select: function(combo) {
                                Ext.getCmp('comboDepartamento').reset();
                                Ext.getCmp('comboAsignadoEmpleado').reset();
                                Ext.getCmp('comboDepartamento').setDisabled(false);
                                Ext.getCmp('comboAsignadoEmpleado').setDisabled(true);
                                var empresa = Ext.getCmp('comboEmpresa').getValue();
                                presentarDepartamentosPorCiudad(combo.getValue(),empresa);
                            }
                        },
                    }, 
                    {
                        xtype          : 'combobox',
                        fieldLabel     : '<b>Departamento:</b>',
                        id             : 'comboDepartamento',
                        name           : 'comboDepartamento',
                        store          : storeDepartamentosCiudad,
                        displayField   : 'nombre_departamento',
                        valueField     : 'id_departamento',
                        queryMode      : "remote",
                        emptyText      : '',
                        minChars       : 3,
                        disabled       : true,
                        forceSelection : true,
                        listeners: {
                            afterRender: function(combo) {

                                if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                {
                                    storeEmpresas.load(function() {

                                        Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);

                                        storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };

                                        storeCiudades.load(function() {

                                            Ext.getCmp('comboCiudad').setDisabled(false);

                                            if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                            {
                                                Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));

                                                storeDepartamentosCiudad.proxy.extraParams = {
                                                                                                id_canton : strIdCantonUsrSession,
                                                                                                empresa   : strPrefijoEmpresaSession
                                                                                             };

                                                storeDepartamentosCiudad.load(function() {

                                                    Ext.getCmp('comboDepartamento').setDisabled(false);
                                                    combo.setValue(Number(strIdDepartamentoUsrSession));
                                                    Ext.getCmp('comboAsignadoEmpleado').setDisabled(false);
                                                    presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                          strIdCantonUsrSession, 
                                                                                          strPrefijoEmpresaSession);
                                                    elWinAsignarCaso.unmask();
                                                });
                                            } else {
                                                elWinAsignarCaso.unmask();
                                            }
                                        });
                                    });
                                } else {
                                    elWinAsignarCaso.unmask();
                                }
                            },
                            select: function(combo) {
                                Ext.getCmp('comboAsignadoEmpleado').reset();
                                Ext.getCmp('comboAsignadoEmpleado').setDisabled(false);
                                var empresa = Ext.getCmp('comboEmpresa').getValue();
                                var canton  = Ext.getCmp('comboCiudad').getValue();
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa);
                            }
                        }
                    },
                    combo_empleados
                ]
            }
        ],
        buttonAlign : 'center',
        buttons     :
        [
            {
                text     : 'Guardar',
                formBind : true,
                handler  : function() {
                    if(Ext.getCmp('comboAsignadoEmpleado') && Ext.getCmp('comboAsignadoEmpleado').value)
                    {
                        combo_empleados.setVisible(true);
                        var empleadoAsignado = {};
                        var jsonData         = {};

                        empleadoAsignado['responsable']         = combo_empleados.getRawValue();
                        empleadoAsignado['idPersona']           = combo_empleados.getValue().split('@@')[0];
                        empleadoAsignado['idPersonaEmpresaRol'] = combo_empleados.getValue().split('@@')[1];

                        if (empleadoAsignado['idPersonaEmpresaRol'] === responableJson['idPersonaEmpresaRol']){
                           Ext.Msg.alert('Alerta ', 'El empleado seleccionado es el actual responsable');
                           return;
                        }

                        jsonData['actual'] = responableJson;
                        jsonData['nuevo']  = empleadoAsignado;
                        jsonData['idZona'] = data.id_zona;

                        conn.request({
                            url    : url_asignarResponsableAjax,
                            method : 'post',
                            params : {
                                'jsonData' : Ext.JSON.encode(jsonData)
                            },
                            success: function(response) {
                                var json = Ext.JSON.decode(response.responseText);

                                if (json.succes) {
                                   Ext.Msg.alert('Alerta', json.message);
                                   winAsignarCaso.destroy();
                                   storeZonas.load();
                                } else {
                                    Ext.Msg.alert('Error ', json.message);
                                }
                            },
                            failure: function(result) {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                winAsignarCaso.destroy();
                                storeZonas.load();
                            }
                        });                        
                    } else {
                        Ext.Msg.alert('Alerta ', 'Por favor escoja el empleado');
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    winAsignarCaso.destroy();
                }
            }
        ]
    });

    winAsignarCaso = Ext.create('Ext.window.Window', {
        title    : 'Asignar Responsable',
        modal    : true,
        closable : false,
        width    : 530,
        layout   : 'fit',
        items    : [formPanelAsignarResponsable]
    });

    winAsignarCaso.show();
    elWinAsignarCaso = winAsignarCaso.getEl();
    elWinAsignarCaso.mask('Cargando...');
}
