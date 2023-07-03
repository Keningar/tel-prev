Ext.QuickTips.init();

var estaEnVentana = false;

Ext.onReady(function () {

    storeSoportes = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: url_getSoportesPaqSoporte,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'soportes',
            },
            extraParams:
            {

                idServicio: strIdServicio,
                id: strPersonaEmpresaRolId,
                login: '',
                login_auxiliar: '',
                tarea_id: '',
                fecha: ''
            }
        },
        fields:
            [
                { name: 'en_base', mapping: 'en_base' },
                { name: 'motivo_soporte', mapping: 'motivo_soporte' },
                { name: 'login_punto', mapping: 'login_punto' },
                { name: 'login_auxiliar', mapping: 'login_auxiliar' },
                { name: 'tarea_id', mapping: 'tarea_id' },
                { name: 'observacion', mapping: 'observacion' },
                { name: 'minutos_soporte', mapping: 'minutos_soporte' },
                { name: 'fecha_inicio', mapping: 'fecha_inicio' },
                { name: 'fecha_fin', mapping: 'fecha_fin' },
                { name: 'cliente_soporte', mapping: 'cliente_soporte' },
                { name: 'solucion', mapping: 'solucion' },
                { name: 'minutos_en_horas', mapping: 'minutos_en_horas'},
                { name: 'tiempo_soporte', mapping: 'tiempo_soporte'},
                { name: 'estado_solicitud', mapping: 'estado_solicitud'},
                { name: 'tipo_tarea', mapping: 'tipo_tarea'},
                { name: 'nombre_tarea', mapping: 'nombre_tarea'}

            ]
    });

    gridSoportes = Ext.create('Ext.grid.Panel', {
        id: 'gridSoportes',
        width: 1200,
        height: 400,
        store: storeSoportes,
        viewConfig:
        {
            enableTextSelection: true,
            loadingText: '<b>Cargando Soportes, Por favor espere...',
            emptyText: '<center><br/><b/>*** No se encontraron soportes realizados ***',
            loadMask: true
        },
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id',
                header: 'ID',
                dataIndex: 'id',
                hideable: false,
                hidden: true,
                width: 30
            },
            {
                id: 'cliente_soporte',
                header: 'Cliente Soporte',
                dataIndex: 'cliente_soporte',
                hidden: true
            },
            {
                id: 'motivo_soporte',
                header: 'Nombre Tarea',
                dataIndex: 'nombre_tarea',
                width: 200
            },
            {
                id: 'login_punto',
                header: 'Login',
                dataIndex: 'login_punto',
                width: 150
            },
            {
                id: 'login_auxiliar',
                header: 'Login auxiliar',
                dataIndex: 'login_auxiliar',
                width: 175
            },
            {
                id: 'tipo_tarea',
                header: 'Numero Caso',
                dataIndex: 'tipo_tarea',
                width: 100
            },
            {
                id: 'tarea_id',
                header: 'Numero Tarea',
                dataIndex: 'tarea_id',
                width: 80
            },
            {
                id: 'observacion',
                header: 'Observación',
                dataIndex: 'observacion',
                width: 200
            },
            {
                id: 'minutos_en_horas',
                header: 'Tiempo soporte',
                dataIndex: 'tiempo_soporte',
                width: 115
            },
            {
                id: 'fecha_inicio',
                header: 'Fecha soporte',
                dataIndex: 'fecha_inicio',
                width: 100
            },
            {
                xtype: 'actioncolumn',
                header: 'Acción',
                width: 70,
                items: [
                    {
                        iconCls: 'button-grid-show',
                        tooltip: 'Ver',
                        handler: function (grid, rowIndex, colIndex) {
                            consultarInfoSoporte(grid, rowIndex);
                        }
                    },
                    {
                        iconCls: 'button-grid-edit',
                        tooltip: 'Editar',
                        handler: function (grid, rowIndex, colIndex) {
                            Ext.MessageBox.show({
                                title: 'Confirmación modificación de soporte',
                                msg: '¿Está seguro de modificar este soporte?',
                                buttons: Ext.MessageBox.OKCANCEL,
                                icon: Ext.MessageBox.WARNING,
                                fn: function(btn){
                                    if(btn == 'ok')
                                        ajustarTiempoSoporte(grid, rowIndex);
                                    
                                }
                            });
                            
                        },
                        getClass: function(v, meta, rec) {
                            if (rec.get('estado_solicitud') == 'Aprobado')
                            {
                                return 'icon-invisible';
                            }
                            return 'button-grid-edit';
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSoportes,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4
        },
        bodyStyle: {
            background: '#fff'
        },
        width: 1000,
        items:
            [
                { width: 60, border: false },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Nombre Tarea',
                    id: 'txtTarea',
                    name: 'txtTarea',
                    displayField: 'nombre_tarea',
                    valueField: 'id_tarea',
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                },
                { width: 60, border: false },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Login auxiliar',
                    id: 'txtLoginAuxiliar',
                    name: 'txtLoginAuxiliar',
                    displayField: 'nombre_loginaux',
                    valueField: 'id_loginaux',
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                },
                { width: 60, border: false },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Login',
                    id: 'txtLogin',
                    name: 'txtLogin',
                    displayField: 'nombre_login',
                    valueField: 'id_login',
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                },
                { width: 60, border: false },
                {
                    xtype: 'datefield',
                    fieldLabel: 'Fecha',
                    id: 'dateFecha',
                    name: 'dateFecha',
                    displayField: 'nombre_fecha',
                    valueField: 'id_fecha',
                    width: 250,
                    labelWidth: '9',
                    emptyText: ''
                },

            ],
        renderTo: 'filtro'

    });


    // function verificaEstadoSolicitud(servicioId)
    // {
    //     Ext.Ajax.request({
    //         url: url_putSolAjusteTiempoSoporte,
    //         method: 'post',
    //         params: { 
    //             idServicio: strIdServicio,
    //             tareaId: grid.store.data.items[rowIndex].raw.tarea_id,
    //             motivoId: 1,
    //             minutosSoporte: opTern,
    //             observacion: (Ext.getCmp('txtObservacion').value!= '')?Ext.getCmp('txtObservacion').value:grid.store.data.items[rowIndex].raw.observacion,
    //             usuarioSolicita: ''
    //         },
    //         success: function (response) {
    //             var text = response.responseText;
    //         },
    //         failure: function (result) {
    //             Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
    //         }
    //     });
    // }

    Ext.create('Ext.Button', {
        text: 'Consultar',
        padding: 5,
        handler: function () {
            gridSoportes.store.proxy.extraParams.tarea_id = Ext.getCmp('txtTarea').value;
            gridSoportes.store.proxy.extraParams.login_auxiliar = Ext.getCmp('txtLoginAuxiliar').value;
            gridSoportes.store.proxy.extraParams.login = Ext.getCmp('txtLogin').value;
            gridSoportes.store.proxy.extraParams.fecha = Ext.getCmp('dateFecha').value;

            gridSoportes.store.reload();
        },
        renderTo: 'button-consultar'
    });

    function consultarInfoSoporte(grid, rowIndex) {
        estaEnVentana = true;

        bttonaceptar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                winConsultaInf.destroy();
            }
        });

        bttondescargar = Ext.create('Ext.Button', {
            text: 'Descargar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                winConsultaInf.destroy();
            }
        });

        var panelConsulta = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 4
            },
            bodyStyle: {
                background: '#fff'
            },
            width: 600,
            items: [
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infCliente',
                    fieldLabel: 'Cliente',
                    value: grid.store.data.items[rowIndex].raw.cliente_soporte,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infFecha',
                    fieldLabel: 'Fecha de soporte',
                    value: grid.store.data.items[rowIndex].raw.fecha_inicio,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infProblema',
                    fieldLabel: 'Nombre Tarea',
                    value: grid.store.data.items[rowIndex].raw.nombre_tarea,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infLogin',
                    fieldLabel: 'Login',
                    value: grid.store.data.items[rowIndex].raw.login_punto,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infTarea',
                    fieldLabel: 'Numero Tarea',
                    value: grid.store.data.items[rowIndex].raw.tarea_id,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                }, 
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infLoginAuxiliar',
                    fieldLabel: 'Login auxiliar (Servicio afectado)',
                    value: grid.store.data.items[rowIndex].raw.login_auxiliar,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infMinutos',
                    fieldLabel: '',
                    //value: grid.store.data.items[rowIndex].raw.minutos_en_horas.split(':')[1],
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                { width: 250, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infHoras',
                    fieldLabel: 'Tiempo soporte',
                    value: grid.store.data.items[rowIndex].raw.tiempo_soporte.split(':')[0],
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infFechaFin',
                    fieldLabel: 'Fecha Fin',
                    value: grid.store.data.items[rowIndex].raw.fecha_fin,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                { width: 250, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infSolucion',
                    fieldLabel: 'Observación',
                    value: grid.store.data.items[rowIndex].raw.observacion,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                { width: 250, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infPersonalSoporte',
                    fieldLabel: 'Tecnico soporte',
                    value: grid.store.data.items[rowIndex].raw.tecnico_soporte,
                    width: '250',
                    style: {
                        fontWeight: 'bold'
                    }
                }
            ]
        });

        formPanel = Ext.create('Ext.form.Panel', {
            width: 600,
            height: 450,
            BodyPadding: 10,
            layout: {
                type: 'table',
                columns: 1,
                align: 'left'
            },
            bodyStyle: {
                background: '#fff'
            },
            items: [
                panelConsulta
            ]
        });

        winConsultaInf = Ext.create('Ext.window.Window', {
            title: 'Detalle de soporte realizado',
            modal: true,
            width: 600,
            height: 450,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons: [bttonaceptar]
        }).show();
    }


    function ajustarTiempoSoporte(grid, rowIndex) {
        estaEnVentana = true;

        bttonguardar = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-right',
            handler: function () {

                let estadoSolicitud = grid.store.data.items[rowIndex].raw.estado_solicitud;

                if (estadoSolicitud == 'Pendiente')
                {
                    Ext.Msg.alert('Advertencia', '¡Ya existe una solicitud pendiente de ajuste de tiempo de soporte para este servicio!')
                }else
                {
                    estaEnVentana = false;

                    let horas = parseInt(Ext.getCmp('txtHorasModificadas').value);
                    let minutos = parseInt(Ext.getCmp('txtMinutosModificados').value);
                    let opTern = ((horas*60)+minutos) != 0 ? ((horas*60)+minutos) : (grid.store.data.items[rowIndex].raw.observacion);
                    
                    let obs = Ext.getCmp('txtObservacion').value;

                    Ext.MessageBox.show({
                        title: 'Confirmación modificación de soporte',
                        msg: '¿Realmente quiere crear una solicitud para modificar el tiempo de soporte?',
                        buttons: Ext.MessageBox.OKCANCEL,
                        icon: Ext.MessageBox.WARNING,
                        fn: function(btn){
                            if(btn == 'ok')
                            {    
                                Ext.Ajax.request({
                                url: url_putSolAjusteTiempoSoporte,
                                method: 'post',
                                params: { 
                                    idServicio: strIdServicio,
                                    tareaId: grid.store.data.items[rowIndex].raw.tarea_id,
                                    motivoId: null,
                                    minutosSoporte: opTern,
                                    observacion: (obs!= '')?obs:grid.store.data.items[rowIndex].raw.observacion,
                                    usuarioSolicita: strUser
                                },
                                success: function (response) {
                                    Ext.Msg.alert('Éxito', '¡La solicitud de ajuste de tiempo de soporte se creó exitosamente!')
                                },
                                failure: function (result) {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                                });
                            }
                        }});



                    winAjstTiempo.destroy();
                }


            }
        });

        bttoncancelar = Ext.create('Ext.Button', {
            text: 'Cancelar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                winAjstTiempo.destroy();
            }
        });

        var panelModificacion = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 4
            },
            bodyStyle: {
                background: '#fff'
            },
            width: 600,
            items: [
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 250, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infLogin',
                    fieldLabel: '<b>Login</b>',
                    value: grid.store.data.items[rowIndex].raw.login_punto,
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infTarea',
                    fieldLabel: '<b>Tarea</b>',
                    value: grid.store.data.items[rowIndex].raw.tarea_id,
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infLoginAuxiliar',
                    fieldLabel: '<b>Login Auxiliar</b>',
                    value: grid.store.data.items[rowIndex].raw.login_auxiliar,
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, border: false },
                { width: 200, border: false },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infHorasRegistradas',
                    fieldLabel: '<b>Horas registradas</b>',
                    value: grid.store.data.items[rowIndex].raw.minutos_en_horas.split(':')[0],
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, border: false },
                {
                    xtype: 'textfield',
                    id: 'txtHorasModificadas',
                    fieldLabel: '<b>Horas modificadas</b>',
                    value: 0,
                    min: grid.store.data.items[rowIndex].raw.minutos_en_horas.split(':')[0],
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'displayfield',
                    id: 'infMinutosRegistrados',
                    fieldLabel: '<b>Minutos registrados</b>',
                    value: grid.store.data.items[rowIndex].raw.minutos_en_horas.split(':')[1],
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, border: false },
                {
                    xtype: 'textfield',
                    id: 'txtMinutosModificados',
                    fieldLabel: '<b>Minutos modificados</b>',
                    value: 0,
                    min: grid.store.data.items[rowIndex].raw.minutos_en_horas.split(':')[1],
                    labelWidth: '7',
                    width: '200'
                },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, height: 15, border: false },
                { width: 200, height: 15, border: false },
                { width: 40, border: false },
                {
                    xtype: 'textareafield',
                    id: 'txtObservacion',
                    fieldLabel: '<b>Observación/ Motivo</b>',
                    labelWidth: '7',
                    width: '200'

                }
            ]
        });

        formPanel = Ext.create('Ext.form.Panel', {
            width: 600,
            height: 380,
            BodyPadding: 10,
            layout: {
                type: 'table',
                columns: 1,
                align: 'left'
            },
            bodyStyle: {
                background: '#fff'
            },
            items: [
                panelModificacion
            ]

        });

        winAjstTiempo = Ext.create('Ext.window.Window', {
            title: 'Tiempo registrado en soporte',
            modal: true,
            width: 600,
            height: 380,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons: [bttonguardar, bttoncancelar]
        }).show();
    }

});