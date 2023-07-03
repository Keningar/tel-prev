Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    //Perfil de carga masiva:  carga_masiva_series
    var boolCargaMasiva = true;
    var permiso = $("#ROLE_247-5737");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    if (!boolPermiso)
    {
        boolCargaMasiva = true;
    }
    else
    {
        if(strBanderaCargaMasiva == "S")
        {
            boolCargaMasiva = true;
        }
        else
        {
            boolCargaMasiva = false;
        }
    }

    //store para los modelos
    storeModelos = new Ext.data.Store
    ({ 
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosCpe',
            extraParams: 
            {
                idMarca: '',
                tipoElemento: 'CPE'
            },
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            { name:'nombreModeloElemento'   , mapping:'nombreModeloElemento'    },
            { name:'idModeloElemento'       , mapping:'idModeloElemento'        }
        ]
    });


    storeTipoElementos = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_TiposElementos,
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
            { name:'NombreTipoElemento' , mapping:'NombreTipoElemento'  },
            { name:'idTipoElemento'       , mapping:'idTipoElemento' }
        ]
    });

    storeModelosElementos = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ModelosElementos,
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
            { name:'idModeloElemento' , mapping:'idModeloElemento'  },
            { name:'nombreModeloElemento' , mapping:'nombreModeloElemento' }
        ]
    });

    storeOficina = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getOficinas,
            timeout: 9600000,
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
            { name:'idOficina' , mapping:'idOficina'  },
            { name:'nombreOficina' , mapping:'nombreOficina' }
        ]
    });

    storeValor = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_valor,
            timeout: 9600000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                criterio: ''
            }
        },
        fields:
        [
            { name:'idValor' , mapping:'idValor'  },
            { name:'nombreValor' , mapping:'nombreValor' }
        ]
    });


    storeResponsable = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_responsable,
            timeout: 9600000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                criterio: ''
            }
        },
        fields:
        [
            { name:'idResponsable' , mapping:'idResponsable'  },
            { name:'nombreResponsable' , mapping:'nombreResponsable' }
        ]
    });

    storeGridElementos = new Ext.data.Store({
        total: 'total',
        pageSize: 20,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_gridElementos,
            timeout: 9600000,
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
            { name:'estadoActivo' , mapping:'estadoActivo'  },
            { name:'estadoTelcos' , mapping:'estadoTelcos'  },
            { name:'estadoNaf' , mapping:'estadoNaf' },
            { name:'descripcion' , mapping:'descripcion' },
            { name:'tipo' , mapping:'tipo' },
            { name:'marca' , mapping:'marca' },
            { name:'modelo' , mapping:'modelo' },
            { name:'serie' , mapping:'serie' },
            { name:'responsable' , mapping:'responsable' },
            { name:'feCreacion' , mapping:'feCreacion' },
            { name:'feCreacionNaf' , mapping:'feCreacionNaf' },
            { name:'observacion' , mapping:'observacion' },
            { name:'feCreacionTelcos' , mapping:'feCreacionTelcos' },
            { name:'ubicacion' , mapping:'ubicacion' },
            { name:'cliente' , mapping:'cliente' },
            { name:'action1' , mapping:'action1' },
            { name:'nombreNodo' , mapping:'nombreNodo' },
        ]
    });

    sm = Ext.create('Ext.selection.CheckboxModel', 
    {
        checkOnly: true
    })

    //se crea el grid para los datos
    grid = Ext.create('Ext.grid.Panel',
    {
        width: '98%',
        height: 500,
        store: storeGridElementos,
        loadMask: true,
        selModel: sm,
        frame: false,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        listeners : {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data     = record.data,
                    value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
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
                        iconCls: 'icon_cambiarEstado',
                        text: 'Actualizar Estado',
                        itemId: 'ejecutaAjax',
                        scope: this,
                        hidden: boolCargaMasiva,
                        handler: function() {
                             actualizarEnTransito();
                        }
                    },
                    {
                        iconCls: 'icon_exportar',
                        text: 'Carga Masiva',
                        scope: this,
                        hidden: boolCargaMasiva,
                        handler: function()
                        {
                            cargaMasiva();
                        }
                    },
                    {
                        iconCls: 'icon_descargar',
                        text: 'Exportar',
                        scope: this,
                        handler: function()
                        {
                            exportarReporteria();
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                header: 'Serie',
                dataIndex: 'serie',
                width: 150,
                sortable: true
            },
            {
                header: 'Descripcion',
                dataIndex: 'descripcion',
                width: 150,
                sortable: true
            },
            {
                header: 'Tipo',
                dataIndex: 'tipo',
                width: 100,
                sortable: true
            },
            {
                header: 'Marca',
                dataIndex: 'marca',
                width: 100,
                sortable: true
            },
            {
                header: 'Modelo',
                dataIndex: 'modelo',
                width: 100,
                sortable: true
            },
            {
                header: 'Estado Elemento',
                dataIndex: 'estadoActivo',
                width: 100,
                sortable: true
            },
            {
                id: 'estadoTelcos',
                header: 'Estado Telcos',
                dataIndex: 'estadoTelcos',
                width: 100,
                sortable: true
            },
            {
              header: 'Estado Naf',
                dataIndex: 'estadoNaf',
                width: 120,
                sortable: true
            },
            {
                header: 'Fecha Trazabilidad',
                dataIndex: 'feCreacion',
                width: 120,
                sortable: true
            },
            {
                header: 'Ubicacion',
                dataIndex: 'ubicacion',
                width: 100,
                sortable: true
            },
            {
                header: 'Nombre Nodo',
                dataIndex: 'nombreNodo',
                width: 120,
                sortable: true
            },
            {
                header: 'Responsable',
                dataIndex: 'responsable',
                width: 220,
                sortable: true
            },
            {
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 150,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items:
                [
                    {
                        getClass: function(v, meta, rec){return 'button-grid-show'},
                        tooltip: 'Ver Trazabilidad',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = storeGridElementos.getAt(rowIndex);
                            presentarTrazabilidad(rec.data.serie,
                                                  rec.data.tipo,
                                                  rec.data.marca,
                                                  rec.data.modelo,
                                                  rec.data.descripcion,
                                                  rec.data.feCreacionNaf,
                                                  rec.data.feCreacionTelcos,
                                                  rec.data.observacion,
                                                  rec.data);
                        }
                    },
                    {
                        getClass: function(v, meta, rec)
                        {
                            if (rec.get('action1') == "icon-invisible")
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-reconfigurarPuerto';
                            }
                        },
                        tooltip: 'Actualizar Estado',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = storeGridElementos.getAt(rowIndex);
                            actualizarEstadoSerie(rec.data.serie,rec.data.estadoActivo);
                        }
                    }
				]
			}
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: storeGridElementos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    function prosesarEstadoMasivo(estado)
    {
        var tramaSeries     = '';
        var numeroElementos = 0;
        var estadoActivo    = "";

        if(estado == "EnTransito")
        {
            estadoActivo = 'EnOficinaMd';
        }
        else if(estado == "Perdido")
        {
            estadoActivo = 'EnTransito';
        }

        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            tramaSeries = tramaSeries + sm.getSelection()[i].data.serie;
            if (sm.getSelection()[i].data.estadoActivo != estadoActivo)
            {
                numeroElementos = numeroElementos + 1;
            }
            if (i < (sm.getSelection().length - 1))
            {
                tramaSeries = tramaSeries + '|';
            }
        }

        if (numeroElementos == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se ejecutaran los registros. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    Ext.get("grid").mask('Cargando...');
                    Ext.Ajax.request({
                        url: url_CambioElementoTransito,
                        method: 'post',
                        params: {tramaSerie: tramaSeries,estadoElemento: estado},
                        success: function(response) {
                            var json = Ext.JSON.decode(response.responseText);
                            if (json.estado == "Ok") {
                                Ext.Msg.alert('Alerta', 'Transaccion Exitosa');
                                Ext.get("grid").unmask();
                               storeGridElementos.load();
                            }
                            else {
                                Ext.Msg.alert('Error ', 'Se produjo un error en la ejecucion.');
                                Ext.get("grid").unmask();
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            });
        }
        else
        {
            Ext.Msg.alert('Error ','Solo se pueden ejecutar registros en estado '+ estadoActivo);
        }
    }

    function presentarVentanaEstadoMasivo()
    {
        btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {
            var strEstadoMasivo = Ext.getCmp('comboEstadoElementoMasivo').value;
            win.destroy();
            prosesarEstadoMasivo(strEstadoMasivo);
        }
        });

        btncancelar = Ext.create('Ext.Button', {
                    text: 'Cerrar',
                    cls: 'x-btn-rigth',
                    handler: function() {
                        win.destroy();
                    }
            });


        formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            layout: 'column',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                msgTarget: 'side'
            },
            items:
            [
                {
                    xtype: 'fieldset',
                    title: '',
                    style: 'border: none;padding:0px',
                    autoHeight: true,
                    width: 300,
                    items:
                    [
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'comboEstadoElementoMasivo',
                            width: 300,
                            name: 'comboEstadoElementoMasivo',
                            store: [
                                ['EnTransito', 'En Transito'],
                                ['Perdido', 'Perdido']
                                ],
                            displayField: 'estado_elemento',
                            valueField: 'estado_elemento'
                        }
                    ]
                }
            ]
        });

        win = Ext.create('Ext.window.Window', {
            title: "Actualizar Estado",
            closable: false,
            modal: true,
            width: 340,
            height: 100,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons:[btnguardar,btncancelar]
        }).show();
    }




    function actualizarEnTransito()
    {
        if (sm.getSelection().length > 0)
        {
            presentarVentanaEstadoMasivo();
        }
        else
        {
            Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista');
        }
    }


    comboCriterioBusqueda = Ext.create('Ext.form.ComboBox', {
        id: 'cmbCriterioBusqueda',
        store: [
                ['login'         , 'Login'],
                ['identificacion', 'Identificacion'],
                ['razonSocial'   , 'Razon Social'],
                ['nodo'          , 'Nodo']
               ],
        displayField: 'nombreCriterioBusqueda',
        valueField  : 'idCriterioBusqueda',
        fieldLabel  : 'Cliente',
        height      :  30,
        width       :  255,
        queryMode   : "remote",
        minChars    :  3,
        emptyText   : '',
        disabled    :  false,
        listeners:
        {
            select: function(combo)
            {
                Ext.getCmp('cmbValor').value = "";
                Ext.getCmp('cmbValor').setRawValue("");
                Ext.getCmp('cmbValor').setDisabled(false);
                storeValor.proxy.extraParams = {criterio: combo.getValue()};
            }
        }
    });

    comboEstado = Ext.create('Ext.form.ComboBox', {
        id: 'cmbEstado',
                    store: [
                        ['EnTransito', 'En Transito'],
                        ['EnOficinaMd', 'En Oficina MD'],
                        ['Activo', 'Activo'],
                        ['Inventariado', 'Inventariado'],
                        ['Reingresado', 'Reingresado'],
                        ['Usado', 'Usado'],
                        ['Dañado', 'Dañado'],
                        ['EnGarantia', 'En Garantia'],
                        ['Vendido', 'Vendido'],
                        ['Cancelado', 'Cancelado'],
                        ['CambioEquipo', 'Cambio de Equipo'],
                        ['Perdido', 'Perdido'],
                        ['PorRepotenciar', 'Por Repotenciar']
                        ],
        displayField: 'nombreEstado',
        valueField: 'idEstado',
        fieldLabel: 'Estado Elemento',
        height: 30,
        width: 270,
        emptyText: '',
        disabled: false
    });

    comboOficina = Ext.create('Ext.form.ComboBox', {
        id: 'cmbOficina',
        store: storeOficina,
        displayField: 'nombreOficina',
        valueField: 'idOficina',
        fieldLabel: 'Oficina',
        height: 30,
        width: 270,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        disabled: false
    });

    comboUbicacion = Ext.create('Ext.form.ComboBox', {
        id: 'cmbUbicacion',
        store: [
            [''          ,'Todos'],
            ['EnBodega'  ,'En Bodega'],
            ['EnVenta'   ,'En Venta'],
            ['EnTransito','En Transito'],
            ['EnTelconet','En Telconet'],
            ['Cliente'   ,'En Cliente'],
            ['EnOficina' ,'En Oficina'],
            ['Nodo'      ,'En Nodo']
        ],
        displayField: 'nombreUbicacion',
        valueField: 'idUbicacion',
        fieldLabel: 'Ubicacion',
        height: 30,
        width: 270,
        emptyText: '',
        disabled: false
    });

    comboTipoElementos = Ext.create('Ext.form.ComboBox', {
        id: 'cmbTipoElemento',
        store: storeTipoElementos,
        displayField: 'NombreTipoElemento',
        valueField: 'idTipoElemento',
        fieldLabel: 'Tipo',
        height: 30,
        width: 255,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        disabled: false,
        listeners:
        {
            select: function(combo)
            {
                Ext.getCmp('cmbModelosElementos').value = "";
                Ext.getCmp('cmbModelosElementos').setRawValue("");
                storeModelosElementos.proxy.extraParams = {tipoElementoId: combo.getValue()};
                storeModelosElementos.load();
            }
        }
    });

    comboModelosElementos = Ext.create('Ext.form.ComboBox', {
        id: 'cmbModelosElementos',
        store: storeModelosElementos,
        displayField: 'nombreModeloElemento',
        valueField: 'idModeloElemento',
        fieldLabel: 'Modelo',
        height: 30,
        width: 270,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        disabled: false
    });


    comboValor = Ext.create('Ext.form.ComboBox', {
        id: 'cmbValor',
        store: storeValor,
        displayField: 'nombreValor',
        valueField: 'idValor',
        fieldLabel: 'Valor',
        height: 30,
        width: 255,
        queryMode: "remote",
        minChars: 6,
        emptyText: '',
        disabled: true
    });

    comboResponsable = Ext.create('Ext.form.ComboBox', {
        id: 'cmbResponsable',
        store: storeResponsable,
        displayField: 'nombreResponsable',
        valueField: 'idResponsable',
        fieldLabel: 'Responsable',
        height: 30,
        width: 270,
        queryMode: "remote",
        minChars: 4,
        emptyText: '',
    });


    //creando el panel para el filtro
    var filterPanel = Ext.create('Ext.panel.Panel',
    {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:
        {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: { background: '#fff' },
        collapsible : true,
        collapsed: false,
        width: '98%',
        title: 'Criterios de busqueda',
            buttons:
            [
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
            items:
            [
                {html: "&nbsp;", border: false, width: 150},
                comboCriterioBusqueda,
                {html: "&nbsp;", border: false, width: 150},
                comboUbicacion,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                comboValor,
                {html: "&nbsp;", border: false, width: 150},
                comboResponsable,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                comboTipoElementos,
                {html: "&nbsp;", border: false, width: 150},
                comboModelosElementos,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtSerie',
                    fieldLabel: 'Serie',
                    value: '',
                    width: '30%'
                },
                {html: "&nbsp;", border: false, width: 150},
                comboEstado,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: '',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 255,
                            id: 'fechaTrazabilidadDesde',
                            name: 'fechaTrazabilidadDesde',
                            fieldLabel: 'Desde:',
                            format: 'd-m-Y',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 255,
                            id: 'fechaTrazabilidadHasta',
                            name: 'fechaTrazabilidadHasta',
                            fieldLabel: 'Hasta:',
                            format: 'd-m-Y',
                            editable: false
                        }
                    ]
                },
                {html: "&nbsp;", border: false, width: 150},
                comboOficina
            ],
        renderTo: 'filtro'
    });

    storeModelos.load
    ({
        callback: function()
        {
        }
    });
    
});


function actualizarEstadoSerie(serie,estadoActivo)
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
			text: 'Aceptar',
			cls: 'x-btn-rigth',
			handler: function()
            {
                var strObservacion = Ext.getCmp('observacionSerie').value;
                var strEstado      = Ext.getCmp('comboEstadoElemento').value;
				win.destroy();
				conn.request({
					method: 'POST',
					params :{
						serie: serie,
						nuevoEstado: strEstado,
						observacion: strObservacion,
						estadoActivo: estadoActivo
					},
					url: urlActualizarEstado,
					success: function(response){
                    var json = Ext.JSON.decode(response.responseText);
                        storeGridElementos.load();
                        Ext.Msg.alert('Alerta ',json.mensaje);

					},
					failure: function(result) {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: result.statusText,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
					}
			});
			}
	});

	btncancelar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    win.destroy();
                }
        });


	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		layout: 'column',
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 150,
			msgTarget: 'side'
		},
		items:
		[
			{
				xtype: 'fieldset',
				title: 'Elemento',
				autoHeight: true,
				width: 475,
				items:
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Serie:',
						id: 'elemento_serie',
						name: 'elemento_serie',
						value: serie
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Estado',
						id: 'comboEstadoElemento',
						width: 435,
						name: 'comboEstadoElemento',
                        store: [
                            ['EnOficinaMd', 'En Oficina MD'],
                            ['Perdido', 'Perdido']
                            ],
						displayField: 'estado_elemento',
						valueField: 'estado_elemento',
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'observacionSerie',
						name: 'observacionSerie',
						rows: 3,
						cols: 40,
					}
				]
			}
		]
	});

	win = Ext.create('Ext.window.Window', {
		title: "Ajustar Estado Elemento",
		closable: false,
		modal: true,
		width: 500,
		height: 230,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btncancelar]
	}).show();



}



function ingresarTrazabilidad(serie)
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
			text: 'Aceptar',
			cls: 'x-btn-rigth',
			handler: function()
            {
                var strObservacion = Ext.getCmp('observacionTrazabilidad').value;
				win.destroy();
				conn.request({
					method: 'POST',
					params :{
						observacion: strObservacion,
						serie: serie
					},
					url: urlIngresarTrazabilidad,
					success: function(response){
                    var json = Ext.JSON.decode(response.responseText);
                        storeGridElementos.load();
                        Ext.Msg.alert('Alerta ',json.mensaje);
					},
					failure: function(result) {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: result.statusText,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
					}
			});
			}
	});

	btncancelar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    win.destroy();
                }
        });


	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		layout: 'column',
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 150,
			msgTarget: 'side'
		},
		items:
		[
			{
				xtype: 'fieldset',
				title: 'Trazabilidad',
				autoHeight: true,
				width: 475,
				items:
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Serie:',
						id: 'elemento_serie',
						name: 'elemento_serie',
						value: serie
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'observacionTrazabilidad',
						name: 'observacionTrazabilidad',
						rows: 3,
						cols: 40,
					}
				]
			}
		]
	});

	win = Ext.create('Ext.window.Window', {
		title: "Ingresar Trazabilidad",
		closable: false,
		modal: true,
		width: 500,
		height: 200,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btncancelar]
	}).show();



}


function presentarTrazabilidad(serie,tipo,marca,modelo,descripcion,fechaCreacionNaf,fechaCreacionTelcos,observacion,data)
{
    //Perfil de ingreso de trazabilidad:  ingresar_trazabilidad
    var boolIngresoTrazabilidad = true;
    var permiso = $("#ROLE_247-5758");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    if (!boolPermiso)
    {
        boolIngresoTrazabilidad = true;
    }
    else
    {
        boolIngresoTrazabilidad = false;
    }

    var bandera = true;
    if(!boolIngresoTrazabilidad)
    {
        if(observacion == "La serie no fue encontrada en el Telcos")
        {
            bandera = false;
        }
    }

    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winSeguimientoTarea.destroy();
            }
    });

    storeGridTrazabilidad = new Ext.data.Store({
        total: 'total',
        pageSize: 10,
        proxy: {
            timeout: 9600000,
            type: 'ajax',
            method: 'post',
            url: url_gridTrazabilidad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                serie: serie
            }
        },
        fields:
        [
            { name:'descripcion' , mapping:'descripcion' },
            { name:'estadoActivo' , mapping:'estadoActivo' },
            { name:'estadoTelcos' , mapping:'estadoTelcos' },
            { name:'feCreacion' , mapping:'feCreacion' },
            { name:'estadoNaf' , mapping:'estadoNaf' },
            { name:'ubicacion' , mapping:'ubicacion' },
            { name:'responsable' , mapping:'responsable' },
            { name:'clienteTelcos' , mapping:'clienteTelcos' },
            { name:'observacion' , mapping:'observacion' }
        ]
    });


    //se crea el grid para los datos
    gridTrazabilidad = Ext.create('Ext.grid.Panel',
    {
        width: '100%',
        height: 250,
        store: storeGridTrazabilidad,
        loadMask: true,
        frame: false,
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
                        iconCls: 'icon_descargar',
                        text: 'Exportar',
                        scope: this,
                        handler: function()
                        {
                            exportarTrazabilidad(serie,tipo,marca,modelo,descripcion,fechaCreacionNaf,fechaCreacionTelcos,data);
                        }
                    },
                    {
                        iconCls: 'icon_ingresarTrazabilidad',
                        text: 'Ingresar Trazabilidad',
                        scope: this,
                        hidden: bandera,
                        handler: function()
                        {
                            ingresarTrazabilidad(serie);
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                header: 'Estado Elemento',
                dataIndex: 'estadoActivo',
                width: 100,
                sortable: true
            },
            {
                header: 'Estado Telcos',
                dataIndex: 'estadoTelcos',
                width: 100,
                sortable: true
            },
            {
              header: 'Estado Naf',
                dataIndex: 'estadoNaf',
                width: 120,
                sortable: true
            },
            {
                header: 'Ubicacion',
                dataIndex: 'ubicacion',
                width: 120,
                sortable: true
            },
            {
                header: 'Responsable',
                dataIndex: 'responsable',
                width: 220,
                sortable: true
            },
            {
                header: 'Cliente Telcos',
                dataIndex: 'clienteTelcos',
                width: 150,
                sortable: true
            },
            {
                header: 'Fe Creacion',
                dataIndex: 'feCreacion',
                width: 100,
                sortable: true
            },
            {
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 200,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeGridTrazabilidad,
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
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        }
    });



	formPanelTrazabilidad = Ext.create('Ext.form.Panel', {
			bodyPadding: 3,
			waitMsgTarget: true,
			height: 700,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				msgTarget: 'side'
			},
			items: [
                {
                    xtype: 'fieldset',
                    defaultType: 'textfield',
                    items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Datos',
                                    defaultType: 'textfield',
                                    layout: {
                                        type: 'table',
                                        columns: 3,
                                        pack: 'center'
                                    },
                                    items: [
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Serie:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'serie',
                                                name: 'serie',
                                                value: serie
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: '',
                                                width:100,
                                                id: 'espacio1',
                                                name: 'espacio1',
                                                value: ''
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Tipo:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'tipo',
                                                name: 'tipo',
                                                value: tipo
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Marca:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'marca',
                                                name: 'marca',
                                                value: marca
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: '',
                                                width:100,
                                                id: 'espacio2',
                                                name: 'espacio2',
                                                value: ''
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Modelo:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'modelo',
                                                name: 'modelo',
                                                value: modelo
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Fecha Creación Naf:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'fechaCreacionNaf',
                                                name: 'fechaCreacionNaf',
                                                value: fechaCreacionNaf
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: '',
                                                width:100,
                                                id: 'espacio3',
                                                name: 'espacio3',
                                                value: ''
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Fecha Creación Telcos:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'fechaCreacionTelcos',
                                                name: 'fechaCreacionTelcos',
                                                value: fechaCreacionTelcos
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Descripcion:',
                                                style: Utils.STYLE_BOLD,
                                                id: 'descripcion',
                                                name: 'descripcion',
                                                value: descripcion
                                            },
                                            {
                                                xtype  : 'displayfield',
                                                width  :  100,
                                                id     : 'espacio4',
                                                name   : 'espacio4',
                                                hidden :  Ext.isEmpty(data.nombreNodo)
                                            },
                                            {
                                                xtype      : 'displayfield',
                                                fieldLabel : 'Nombre Nodo:',
                                                style      :  Utils.STYLE_BOLD,
                                                id         : 'nombreNodo',
                                                name       : 'nombreNodo',
                                                value      :  data.nombreNodo,
                                                hidden     :  Ext.isEmpty(data.nombreNodo)
                                            }
                                           ]
                                },
                                {
                                    xtype: 'fieldset',
                                    defaultType: 'textfield',
                                    style: 'border: none;padding:0px',
                                    bodyStyle: 'padding:0px',
                                    items: [
                                                gridTrazabilidad
                                           ]
                                }
                            ]
                }
            ]
		 });

    storeGridTrazabilidad.proxy.extraParams = {serie: serie};
    storeGridTrazabilidad.load();

	winSeguimientoTarea = Ext.create('Ext.window.Window', {
			title: 'Trazabilidad Elemento',
			modal: true,
			width: 990,
			height: 510,
			resizable: true,
			layout: 'fit',
			items: [formPanelTrazabilidad],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();


}



function cargaMasiva()
{
         var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',

        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [{
            xtype: 'filefield',
            id: 'form-file',
            name: 'archivo',
            emptyText: 'Seleccione una Archivo',
            buttonText: 'Browse',
            buttonConfig: {
                iconCls: 'upload-icon'
            }
        },
        {
            xtype: 'combobox',
            fieldLabel: 'Estado ',
            style: Utils.STYLE_BOLD,
            id: 'estadoActualizar',
            value: '',
            store: [
                ['EnOficinaMd', 'En Oficina MD']
                ],
            width: '30%'
        }],

        buttons: [{
            text: 'Subir',
            handler: function(){
                 var form = this.up('form').getForm();
                 if(form.isValid())
		 {
		      form.submit({
			    url: url_fileUpload,
			    params :{
				  origenCarga : 'auditoriaElementos',
                  estadoActualizar :  Ext.getCmp('estadoActualizar').value
			    },
			    waitMsg: 'Procesando Archivo...',
			    success: function(fp, o)
			    {
				  Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
					if(btn=='ok')
					{
					      win.destroy();
					}
				  });
			    },
			    failure: function(fp, o) {
				  Ext.Msg.alert("Alerta",o.result.respuesta);
			    }
			});
                }
            }
        },{
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivo',
        modal: true,
        width: 500,
        height: 150,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

}




function exportarReporteria()
{
    strCriterio     = Ext.getCmp('cmbCriterioBusqueda').value  ?  Ext.getCmp('cmbCriterioBusqueda').value : "";
    strValor        = Ext.getCmp('cmbValor').getRawValue()  ?  Ext.getCmp('cmbValor').getRawValue() : "";
    strResponsable  = Ext.getCmp('cmbResponsable').getRawValue()  ?  Ext.getCmp('cmbResponsable').getRawValue() : "";
    intTipo         = Ext.getCmp('cmbTipoElemento').value  ?  Ext.getCmp('cmbTipoElemento').value : "";
    strUbicacion    = Ext.getCmp('cmbUbicacion').value  ?  Ext.getCmp('cmbUbicacion').value : "";
    intModelo       = Ext.getCmp('cmbModelosElementos').value  ?  Ext.getCmp('cmbModelosElementos').value : "";
    strNombreTipo   = Ext.getCmp('cmbTipoElemento').getRawValue()  ?  Ext.getCmp('cmbTipoElemento').getRawValue() : "";
    strNombreModelo = Ext.getCmp('cmbModelosElementos').getRawValue()  ?  Ext.getCmp('cmbModelosElementos').getRawValue() : "";
    strSerie        = Ext.getCmp('txtSerie').value  ?  Ext.getCmp('txtSerie').value : "";
    strEstado       = Ext.getCmp('cmbEstado').value  ?  Ext.getCmp('cmbEstado').value : "";
    strFechaDesde   = Ext.getCmp('fechaTrazabilidadDesde').getRawValue()  ?  Ext.getCmp('fechaTrazabilidadDesde').getRawValue() : "";
    strFechaHasta   = Ext.getCmp('fechaTrazabilidadHasta').getRawValue()  ?  Ext.getCmp('fechaTrazabilidadHasta').getRawValue() : "";
    intOficina      = Ext.getCmp('cmbOficina').value  ?  Ext.getCmp('cmbOficina').value : "";
    strOficina      = Ext.getCmp('cmbOficina').getRawValue()  ?  Ext.getCmp('cmbOficina').getRawValue() : "";

    window.location = urlExportarReporte + '?criterio=' + strCriterio
        + '&valor='         + strValor
        + '&responsable='   + strResponsable
        + '&tipo='          + intTipo
        + '&modelo='        + intModelo
        + '&ubicacion='     + strUbicacion
        + '&nombreTipo='    + strNombreTipo
        + '&nombreModelo='  + strNombreModelo
        + '&serie='         + strSerie
        + '&fechaDesde='    + strFechaDesde
        + '&fechaHasta='    + strFechaHasta
        + '&idOficina='     + intOficina
        + '&nombreOficina=' + strOficina
        + '&estado='        + strEstado;
}


function exportarTrazabilidad(serie,tipo,marca,modelo,descripcion,fechaCreacionNaf,fechaCreacionTelcos,data)
{
    var nombreNodo = Ext.isEmpty(data.nombreNodo) ? "" : data.nombreNodo;

    if(tipo == null)
    {
        tipo = "";
    }
    if(marca == null)
    {
        marca = "";
    }
    if(modelo == null)
    {
        modelo = "";
    }
    if(descripcion == null)
    {
        descripcion = "";
    }
    if(fechaCreacionNaf == null)
    {
        fechaCreacionNaf = "";
    }
    if(fechaCreacionTelcos == null)
    {
        fechaCreacionTelcos = "";
    }
    window.location = urlExportarTrazabilidad
        + '?serie='              + serie
        + '&nombreNodo='         + nombreNodo
        + '&tipo='               + tipo
        + '&marca='              + marca
        + '&modelo='             + modelo
        + '&descripcion='        + descripcion
        + '&fechaCreacionNaf='   + fechaCreacionNaf
        + '&fechaCreacionTelcos='+ fechaCreacionTelcos
}



function cargarSeries()
{
    connCargaSeries.request({
        url: urlUpLoadFileSeries,
        method: 'post',
        params:
            {
            },
        success: function(response){
            var text = Ext.decode(response.responseText);
            alert(text.mensaje);

        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });


}

/**
 * Funcion que ejecuta la busqueda
 * por los filtros 
 * */
function buscar()
{
    var boolError = false;
    if(Ext.getCmp('cmbCriterioBusqueda').value == null && Ext.getCmp('cmbValor').value == null && Ext.getCmp('cmbResponsable').value == null &&
        Ext.getCmp('cmbTipoElemento').value == null && Ext.getCmp('cmbModelosElementos').value == null
        && Ext.getCmp('txtSerie').value == "" && Ext.getCmp('cmbEstado').value == null && Ext.getCmp('cmbUbicacion').value == null
        && Ext.getCmp('cmbOficina').value == null)
    {
        Ext.Msg.alert("Alerta","Seleccionar al menos un criterio de busqueda");
    }
    else
    {
        if(( Ext.getCmp('fechaTrazabilidadDesde').value !=null)&&(Ext.getCmp('fechaTrazabilidadHasta').value !=null) )
        {
            if(Ext.getCmp('fechaTrazabilidadDesde').value > Ext.getCmp('fechaTrazabilidadHasta').value)
            {
                boolError = true;

                Ext.Msg.show({
                    title:'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde Trazabilidad debe ser menor a Fecha Hasta Trazabilidad',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId'
                });
            }

            if(Ext.getCmp('fechaTrazabilidadHasta').value < Ext.getCmp('fechaTrazabilidadDesde').value)
            {
                boolError = true;

                Ext.Msg.show({
                    title:'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Hasta debe ser mayor a Fecha Desde',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId'
                });
            }

            //Se agrega validacion para filtrar que solo se pueda consultar maximo entre 90 dias en las fechas de planificacion
            var desdeP = Ext.getCmp('fechaTrazabilidadDesde').value;
            var hastaP = Ext.getCmp('fechaTrazabilidadHasta').value;

            var fechaDesdeP = desdeP.getTime();
            var fechaHastaP = hastaP.getTime();

            var differenceP = Math.abs(fechaDesdeP - fechaHastaP)

            //Convierto de milisegundos a dias
            var diasP = differenceP/86400000;

            if(diasP >90){
                boolError = true;
                Ext.Msg.show({
                   title:'Error en Busqueda',
                   msg: 'Por Favor solo se puede realizar busquedas de hasta 90 dias de diferencia entre la Fecha Inicio y Fin',
                   buttons: Ext.Msg.OK,
                   animEl: 'elId',
                   icon: Ext.MessageBox.ERROR
                });
            }
        }

        if(!boolError)
        {
            storeGridElementos.getProxy().extraParams.criterio      = Ext.getCmp('cmbCriterioBusqueda').value;
            storeGridElementos.getProxy().extraParams.valor         = Ext.getCmp('cmbValor').getRawValue();
            storeGridElementos.getProxy().extraParams.responsable   = Ext.getCmp('cmbResponsable').getRawValue();
            storeGridElementos.getProxy().extraParams.ubicacion     = Ext.getCmp('cmbUbicacion').value;
            storeGridElementos.getProxy().extraParams.tipo          = Ext.getCmp('cmbTipoElemento').value;
            storeGridElementos.getProxy().extraParams.modelo        = Ext.getCmp('cmbModelosElementos').value;
            storeGridElementos.getProxy().extraParams.serie         = Ext.getCmp('txtSerie').value;
            storeGridElementos.getProxy().extraParams.estado        = Ext.getCmp('cmbEstado').value;
            storeGridElementos.getProxy().extraParams.fechaDesde    = Ext.getCmp('fechaTrazabilidadDesde').getRawValue();
            storeGridElementos.getProxy().extraParams.fechaHasta    = Ext.getCmp('fechaTrazabilidadHasta').getRawValue();
            storeGridElementos.getProxy().extraParams.nombreOficina = Ext.getCmp('cmbOficina').getRawValue();
            storeGridElementos.getProxy().extraParams.idOficina     = Ext.getCmp('cmbOficina').value;

            storeGridElementos.load();
        }
    }
}

/**
 * Funcion que limpia los filtros
 * */
function limpiar()
{
    Ext.getCmp('cmbCriterioBusqueda').value=null;
    Ext.getCmp('cmbCriterioBusqueda').setRawValue(null);

    Ext.getCmp('cmbValor').value=null;
    Ext.getCmp('cmbValor').setRawValue(null);

    Ext.getCmp('cmbTipoElemento').value=null;
    Ext.getCmp('cmbTipoElemento').setRawValue(null);

    Ext.getCmp('cmbUbicacion').value=null;
    Ext.getCmp('cmbUbicacion').setRawValue(null);

    Ext.getCmp('cmbModelosElementos').value=null;
    Ext.getCmp('cmbModelosElementos').setRawValue(null);

    Ext.getCmp('fechaTrazabilidadDesde').value=null;
    Ext.getCmp('fechaTrazabilidadDesde').setRawValue(null);

    Ext.getCmp('fechaTrazabilidadHasta').value=null;
    Ext.getCmp('fechaTrazabilidadHasta').setRawValue(null);

    Ext.getCmp('cmbOficina').value=null;
    Ext.getCmp('cmbOficina').setRawValue(null);

    Ext.getCmp('txtSerie').value="";
    Ext.getCmp('txtSerie').setRawValue("");

    Ext.getCmp('cmbResponsable').value=null;
    Ext.getCmp('cmbResponsable').setRawValue(null);

    Ext.getCmp('cmbEstado').value=null;
    Ext.getCmp('cmbEstado').setRawValue(null);

    storeGridElementos.removeAll();
    grid.getStore().removeAll();
}