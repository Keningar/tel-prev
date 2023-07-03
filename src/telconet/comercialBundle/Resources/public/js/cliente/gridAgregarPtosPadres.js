Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 20;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    Ext.override(Ext.data.proxy.Ajax, {timeout: 90000});
    strPrefjoEmpresa = document.getElementById("prefjoEmpresa").value;

    var es_padre_store = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
            {"valor": "S", "signo": "Si"},
            {"valor": "N", "signo": "No"}
        ]
    });


    var es_padre_login_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'top',
        store: es_padre_store,
        id: 'idespadre',
        name: 'idespadre',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Es Padre',
        width: 70,
        mode: 'local',
        allowBlank: true
    });

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
            //anchor : '65%',
            //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
            //anchor : '65%',
            //layout: 'anchor'
    });


    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_cliente_lista_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });
    var estado_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {
                    //alert(Ext.getCmp('idestado').getValue());
                    estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    estado_id = '';
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }
    });

    TFLogin = new Ext.form.TextField({
        labelAlign: 'top',
        id: 'loginPto',
        fieldLabel: 'Login',
        xtype: 'textfield'
    });
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'idPto', type: 'int'},
            {name: 'cliente', type: 'string'},
            {name: 'login', type: 'string'},
            {name: 'descripcionPunto', type: 'string'},
            {name: 'Direccion', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'esPadre', type: 'string'},
            {name: 'datosEnvio', type: 'string'},
            {name: 'nombreEnvio', type: 'string'},
            {name: 'direccionEnvio', type: 'string'},
            {name: 'telefonoEnvio', type: 'string'},
            {name: 'emailEnvio', type: 'string'},
            {name: 'ciudadEnvio', type: 'string'},
            {name: 'parroquiaEnvio', type: 'string'},
            {name: 'sectorEnvio', type: 'string'},
            {name: 'id_ciudadEnvio', type: 'string'},
            {name: 'id_parroquiaEnvio', type: 'string'},
            {name: 'id_sectorEnvio', type: 'string'},
            {name: 'linkVer', type: 'string'},
            //se agrega parametro que almacena valor de bandera de facturacion electronica  		
            {name: 'esElectronica', type: 'string'},
            {name: 'strGastoAdministrativo', type: 'string'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_gridPtos,
            reader: {
                type: 'json',
                root: 'ptos',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: '', nombre: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.idespadre = Ext.getCmp('idespadre').getValue();
                store.getProxy().extraParams.nombre = Ext.getCmp('loginPto').getValue();
            },
            load: function(store) {
                store.each(function(record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    store.load({params: {start: 0, limit: 20}});

    var indexPadreBuscado = "";
    //var loginPadreBuscado="";    

    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            select: function(selectionModel, record, index, eOpts) {
                //console.log('selected:'+index);
                if (record.data.esPadre == 'Si') {
                    //loginPadreBuscado = record.data.login;
                    //indexPadreBuscado = index;
                    store_servicios_verifica.load({params: {start: 0, limit: 1, nombrePadre: record.data.login, criterioLoginPadre: 'igual'}});
                    /*if(validaPadreEnGrid(record.data.login)){
                     //sm.deselect(index);
                     Ext.Msg.alert('Alerta','Este Punto tiene servicios asignados, por favor para poder quitarlo debe asignar a otro punto de facturacion aquellos servicios.');
                     }*/
                }


            },
            deselect: function(selectionModel, record, index, eOpts) {
                var posicion = arrayEncontrados.indexOf(record.data.login);
                //console.log('posicion:'+posicion);
                if (posicion >= 0)
                    arrayEncontrados.splice(posicion, 1);
                // console.log(arrayEncontrados);

            }
        }
    });





    function asignarVarios() {
        var param = '';
        if (sm.getSelection().length > 0)
        {
            var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.idPto;

                if (sm.getSelection()[i].data.esPadre == 'Si')
                {
                    estado = estado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (estado == 0)
            {
                Ext.Msg.confirm('Alerta', 'Se asignaran como punto de facturacion los registros seleccionados. Desea continuar?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: url_agregar_ajax,
                            method: 'post',
                            params: {param: param},
                            success: function(response) {
                                var text = response.responseText;
                                store.load();
                                
                                if( strPrefjoEmpresa == "TN" )
                                {
                                    Ext.Msg.alert('Alerta', 'Los puntos de facturación se agregaron con éxito, a continuación debe proceder '+ 
                                    'con el ingreso de Datos de Envío para poder asignar servicios hijos de los padres agregados.', function(btn) {
                                        if (btn == 'ok') {
                                            //window.location=url_asignar_padre_a_hijos;
                                            winAgregarDatosEnvioPadreFacturacion( param );
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta', 'Los puntos de facturacion se agregaron con exito, a continuacion Debe proceder '+
                                    'a realizar la asignacion de servicios hijos de los padres agregados.', function(btn) {
                                    });
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
                alert('Por lo menos uno de los registros ya fue asignado como padre');
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }


    arrayEncontrados = new Array();

    function quitarVarios() {
        var param = '';
        var encontro = -1;
        //console.log(arrayEncontrados);
        if (sm.getSelection().length > 0)
        {
            //var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.idPto;


                encontro = arrayEncontrados.indexOf(sm.getSelection()[i].data.login);
                if (encontro >= 0)
                    break;


                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (encontro < 0)
            {
                Ext.Msg.confirm('Alerta', 'los puntos seleccionados dejaran de ser puntos de facturacion. Desea continuar?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: url_quitar_ajax,
                            method: 'post',
                            params: {param: param},
                            success: function(response) {
                                var text = response.responseText;
                                store.load();
                                Ext.Msg.alert('Alerta', 'Los puntos de facturacion se quitaron con exito, a continuacion Debe proceder a realizar la asignacion de servicios hijos de los padres agregados.', function(btn) {
                                    if (btn == 'ok') {
                                        //window.location=url_asignar_padre_a_hijos;
                                    }
                                });
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
                mensaje = 'Entre los seleccionados existen logins que tienen servicios asignados, por favor para poder eliminarlo(s) debe asignar a otro punto de facturacion aquellos servicios.';
                Ext.MessageBox.show({
                    icon: Ext.Msg.ERROR,
                    width: 500,
                    height: 300,
                    title: 'Mensaje del Sistema',
                    msg: mensaje,
                    buttonText: {yes: "Ok"}
                });
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }


    /*
     * @author Ronald Saenz 
     * @version 1.0 22-05-2014
     * @since 1.0
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 06-07-2017
     * Se agrega validación para empresa con prefijo TNP (Panamá)
     */
    var listView = Ext.create('Ext.grid.Panel', {
        width: 980,
        height: 275,
        collapsible: false,
        title: 'Listado de Puntos',
        selModel: sm,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'}, {
                        iconCls: 'icon_add',
                        text: 'Agregar',
                        disabled: false,
                        itemId: 'asignarpadre',
                        scope: this,
                        handler: function() {
                            asignarVarios();
                        }
                    }, {
                        iconCls: 'icon_add',
                        text: 'Eliminar',
                        disabled: false,
                        itemId: 'quitarpadre',
                        scope: this,
                        handler: function() {
                            quitarVarios();
                        }
                    }]}],
        renderTo: Ext.get('lista_ptos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
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
        },
        viewConfig: {
            getRowClass: function(record, index) {
                var c = record.get('esPadre');
                //console.log(c);
                if (c == 'Si') {
                    return 'greenTextGrid';
                } else {
                    return 'blackTextGrid';
                }
            }
        },
        columns: [
            new Ext.grid.RowNumberer(),
            {
                text: 'Login',
                width: 140,
                dataIndex: 'login'
            }, {
                text: 'Es Padre',
                dataIndex: 'esPadre',
                align: 'right',
                width: 60
            }, 
            //se agrega campo en grid con valor de bandera de facturacion electronica  		
            {
                text: 'Es Electronica',
                dataIndex: 'esElectronica',
                align: 'right',
                hidden: true,
                width: 80
            },
            {
                text: 'Gasto Adm.',
                dataIndex: 'strGastoAdministrativo',
                align: 'right',
                width: 70
            }, {
                text: 'Datos Envío',
                dataIndex: 'datosEnvio',
                align: 'right',
                width: 70
            }, {
                text: 'Nombre Envío',
                dataIndex: 'nombreEnvio',
                align: 'right',
                width: 100
            }, {
                text: 'Dirección Envío',
                dataIndex: 'direccionEnvio',
                align: 'left',
                width: 200
            }, {
                text: 'Telef. Envío',
                dataIndex: 'telefonoEnvio',
                align: 'right',
                width: 75
            }, {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                flex: 50
            },
            {
                /* CAMBIO RONALD SAENZ 22MAYO... BOTONES ACTION COLUM */
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 130,
                sortable: false,
                items:
                    [
                        {
                            getClass: function(v, meta, rec) {
                                var classA = "button-grid-show";

                                //var permiso = $("#ROLE_78-50");
                                //var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                //if(!boolPermiso){ classA = "icon-invisible"; }

                                if (classA == "icon-invisible")
                                    this.items[0].tooltip = '';
                                else
                                    this.items[0].tooltip = 'Ver Datos del Punto';

                                return classA;
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var classA = "button-grid-show";

                                //var permiso = $("#ROLE_78-50");
                                //var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                //if(!boolPermiso){ classA = "icon-invisible"; }

                                if (classA != "icon-invisible")
                                    window.location = rec.data.linkVer;
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var classA = "button-grid-datosEnvio_pto";

                                //var permiso = $("#ROLE_8-624");
                                //var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							


                                var permiso = '{{ is_granted("#ROLE_8-624") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                                if (!boolPermiso) {
                                    classA = "icon-invisible";
                                }
                                if (rec.data.esPadre != "Si") {
                                    classA = "icon-invisible";
                                }

                                if (classA == "icon-invisible")
                                    this.items[1].tooltip = '';
                                else
                                    this.items[1].tooltip = 'Asignar Datos de Envio';

                                return classA;
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var classA = "button-grid-datosEnvio_pto";
                                var editarDatosEnvio = false;

                                //var permiso = $("#ROLE_8-624");
                                //var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	

                                var permiso = '{{ is_granted("#ROLE_8-624") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                                if (!boolPermiso) {
                                    classA = "icon-invisible";
                                }
                                if (rec.data.esPadre != "Si") {
                                    classA = "icon-invisible";
                                }
                                else {
                                    if (rec.data.datosEnvio == 'Si') {
                                        editarDatosEnvio = true;
                                    }
                                }

                                if (classA != "icon-invisible")
                                {
                                   if( strPrefjoEmpresa == "TN" || strPrefjoEmpresa == "TNP" )
                                   {
                                       winDatosEnvioActualizar(grid.getStore().getAt(rowIndex).data, editarDatosEnvio);
                                   }
                                   else
                                   {
                                       winDatosEnvio(grid.getStore().getAt(rowIndex).data, editarDatosEnvio);
                                   }
                                }
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        //se agrega boton de activar/inactivar facturacion electronica
                        {
                            getClass: function(v, meta, rec) {
                                var classA = "button-grid-datosEnvio";
                                if (!boolActivaDesactivaFactElect) {
                                    classA = "icon-invisible";
                                }
                                else
                                {
                                    if (rec.data.esPadre != "Si") {
                                        classA = "icon-invisible";
                                    }
                                    else
                                    {
                                        if (rec.data.esElectronica != "Si")
                                        {
                                            classA = "button-grid-agregarFacturacionElectronica";
                                            this.items[2].tooltip = 'Activar Facturacion Electronica';
                                        }
                                        else
                                        {
                                            classA = "button-grid-quitarFacturacionElectronica";
                                            this.items[2].tooltip = 'Inactivar Facturacion Electronica';
                                        }
                                    }
                                }
                                if (classA == "icon-invisible")
                                {
                                    this.items[2].tooltip = '';
                                }


                                return classA;
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                if (rec.data.esElectronica != "Si")
                                {
                                    Ext.Msg.confirm('Alerta', 'Se activara la facturación de manera Electronica. Desea continuar?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                url: url_actInaFacturacion,
                                                params: {idPunto: rec.data.idPto, valor: 'S'},
                                                success: function(response) {
                                                    var text = response.responseText;
                                                    Ext.Msg.alert('Mensaje', text);
                                                    store.load();
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
                                    Ext.Msg.confirm('Alerta', 'Se inactivara la facturación de manera Electronica. Desea continuar?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                url: url_actInaFacturacion,
                                                params: {idPunto: rec.data.idPto, valor: 'N'},
                                                success: function(response) {
                                                    var text = response.responseText;
                                                    Ext.Msg.alert('Mensaje', text);
                                                    store.load();
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        },
                        {//Gasto ADministrativo
                            getClass: function(v, meta, rec) {
                                if (rec.data.esPadre !== "Si") {
                                    classA = "icon-invisible";
                                }else{
                                    var objPermiso = $("#ROLE_8-1977");
                                    var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
                                    if (boolPermiso) {
                                        if(rec.data.strGastoAdministrativo === 'Si'){
                                            var classA = "button-grid-gastoAdministrativoNot";
                                            this.items[3].tooltip = 'Inactivar Gasto Adminitrativo';
                                        }else{
                                            var classA = "button-grid-gastoAdministrativo";
                                            this.items[3].tooltip = 'Activar Gasto Adminitrativo';
                                        }
                                    }
                                }
                                return classA;
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var objPermiso = $("#ROLE_8-1977");
                                var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
                                if (boolPermiso) {
                                    var strAccion = '';
                                    if(rec.data.strGastoAdministrativo === 'Si'){
                                        strAccion = 'inactivara';
                                        strValorGstAdmi = 'N';
                                    }else{
                                        strAccion = 'activara';
                                        strValorGstAdmi = 'S';
                                    }
                                    Ext.Msg.confirm('Alerta', 'Se ' + strAccion + ' el cobro de Gasto Administrativo. Desea continuar?', function (btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                url: url_ActGastAdmi,
                                                params: {idPunto: rec.data.idPto, strGastoAdministrativo: strValorGstAdmi},
                                                success: function (response) {
                                                    var strTextResponse = response.responseText;
                                                    Ext.Msg.alert('Mensaje', strTextResponse);
                                                    store.load();
                                                },
                                                failure: function (result)
                                                {
                                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                }
                                            });
                                        }
                                    });
                                }
                            }//gastoAdministrativo
                        }

                    ]
            }
        ]
    });


    function renderAcciones(value, p, record) {
        var iconos = '';
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver Datos del Punto" class="button-grid-show"></a></b>';
        if (record.data.esPadre == 'Si') {
            if (record.data.datosEnvio == 'Si')
                iconos = iconos + '<b><a href="#" onClick="winVerDatosEnvio(' + record.data.idPto + ',\'' + record.data.ciudadEnvio + '\',\'' + record.data.parroquiaEnvio + '\',\'' + record.data.sectorEnvio + '\',\'' + record.data.nombreEnvio + '\',\'' + record.data.direccionEnvio + '\',\'' + record.data.telefonoEnvio + '\',\'' + record.data.emailEnvio + '\')" title="Ver Datos de Envio" class="button-grid-datosEnvio"></a></b>';
            else
                iconos = iconos + '<b><a href="#" onClick="winDatosEnvio(' + record.data.idPto + ')" title="Asignar Datos de Envio" class="button-grid-datosEnvio"></a></b>';
        }
        return Ext.String.format(
            iconos,
            value
            );
    }


    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 2, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: true,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 6,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:2px'
        },
        collapsible: true,
        collapsed: true,
        width: 980,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                //xtype: 'button',
                iconCls: "icon_search",
                handler: Buscar
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: Limpiar
            }

        ],
        items: [
            TFLogin,
            {html: "&nbsp;", border: false, width: 20},
            es_padre_login_cmb,
            {html: "&nbsp;", border: false, width: 20},
        ],
        renderTo: 'filtro_puntos'
    });

    function Buscar() {

        /*if (!Ext.getCmp('loginPto').getValue())
         {
         Ext.Msg.show({
         title:'Error en Busqueda',
         msg: 'Por Favor para realizar la busqueda debe ingresar los criterios.',
         buttons: Ext.Msg.OK,
         animEl: 'elId',
         icon: Ext.MessageBox.ERROR
         });		 
         
         }
         else
         {*/
        store.load({params: {start: 0, limit: 20}});
        //}


    }

    function Limpiar() {

        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('idespadre').setValue('');
        Ext.getCmp('loginPto').setValue('');
    }







// The data store containing the list of states
    var criterio_store = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
            {"valor": "igual", "signo": "igual"},
            {"valor": "diferente", "signo": "diferente"},
            {"valor": "nocontiene", "signo": "no contiene"},
            {"valor": "contiene", "signo": "contiene"}
            //...
        ]
    });


    var criterio_serv_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: criterio_store,
        labelAlign: 'top',
        id: 'idcriterio',
        name: 'idcriterio',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Criterio',
        width: 80,
        mode: 'local',
        allowBlank: true
    });

    TFLoginServ = new Ext.form.TextField({
        id: 'loginServ',
        fieldLabel: 'Login',
        labelAlign: 'top',
        //width: 80,
        xtype: 'textfield'
    });


    TFLoginPadreServ = new Ext.form.TextField({
        id: 'loginPadreServ',
        labelAlign: 'top',
        fieldLabel: 'Login Pto Fact',
        //width: 80,
        xtype: 'textfield'
    });



    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelPadres', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idpadre', type: 'string'},
            {name: 'login', type: 'string'}
        ]
    });
    var padres_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelPadres",
        proxy: {
            type: 'ajax',
            url: url_padres,
            reader: {
                type: 'json',
                root: 'padres'
            }
        }
    });
    var padres_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: padres_store,
        labelAlign: 'left',
        id: 'idpadre',
        name: 'idpadre',
        valueField: 'idpadre',
        displayField: 'login',
        fieldLabel: 'Pto. Facturacion',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {
                    //alert(Ext.getCmp('idestado').getValue());
                    //estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    //estado_id='';
                    padres_store.removeAll();
                    padres_store.load();
                }
            }
        }
    });


    Ext.define('serviciosModel', {
        extend: 'Ext.data.Model',
        idProperty: 'idServicio',
        fields: [
            {name: 'idServicio', type: 'string'},
            {name: 'idPunto', type: 'string'},
            {name: 'descripcionPunto', type: 'string'},
            {name: 'loginPunto', type: 'string'},
            {name: 'idProducto', type: 'string'},
            {name: 'descripcionProducto', type: 'string'},
            {name: 'esVenta', type: 'string'},
            {name: 'cantidad', type: 'string'},
            {name: 'precioVenta', type: 'float'},
            {name: 'estado', type: 'string'},
            {name: 'fechaCreacion', type: 'string'},
            {name: 'padre', type: 'string'},
            {name: 'loginPadre', type: 'string'}
        ]
    });

    var store_servicios_verifica = Ext.create('Ext.data.JsonStore', {
        model: 'serviciosModel',
        pageSize: 9999999,
        proxy: {
            type: 'ajax',
            url: url_servicios,
            reader: {
                type: 'json',
                root: 'servicios'
            }
        },
        listeners: {
            load: function(store) {
                store.each(function(record) {
                    //console.log('loginBuscado:'+loginPadreBuscado);
                    //console.log('login:'+record.data.loginPadre);
                    //console.log("loginBuscadoEnStore:"+loginPadreBuscado);
                    if (record.data.loginPadre) {
                        siencontro = false;
                        indexSelected = '';
                        //console.log(sm.getSelection().length);
                        for (var i = 0; i < sm.getSelection().length; ++i)
                        {
                            //console.log(record.data.loginPadre +"="+ sm.getSelection()[i].data.login+"("+i+")");
                            if (record.data.loginPadre == sm.getSelection()[i].data.login) {
                                //console.log(sm.getSelection()[i].data.login);
                                if (arrayEncontrados.indexOf(sm.getSelection()[i].data.login) < 0)
                                    arrayEncontrados.push(sm.getSelection()[i].data.login);
                                //Ext.Msg.alert('Alerta','Este Punto tiene servicios asignados, por favor para poder eliminarlo debe asignar a otro punto de facturacion aquellos servicios.');
                                //sm.deselect(i); 
                                break;
                                //siencontro=true;
                                //indexSelected=i;                                              
                            }
                        }
                        //console.log(arrayEncontrados);
                        /*if(siencontro){  
                         console.log('entro');
                         Ext.Msg.alert('Alerta','Este Punto tiene servicios asignados, por favor para poder eliminarlo debe asignar a otro punto de facturacion aquellos servicios.');
                         sm.deselect(indexSelected);
                         }*/
                    }
                });
            }
        }
    });

    var store_servicios = Ext.create('Ext.data.JsonStore', {
        model: 'serviciosModel',
        pageSize: 30,
        proxy: {
            type: 'ajax',
            url: url_servicios,
            reader: {
                type: 'json',
                root: 'servicios'
            }
        },
        listeners: {
            beforeload: function(store) {
                store_servicios.getProxy().extraParams.nombre = Ext.getCmp('loginServ').getValue();
                store_servicios.getProxy().extraParams.nombrePadre = Ext.getCmp('loginPadreServ').getValue();
                store_servicios.getProxy().extraParams.criterioLoginPadre = Ext.getCmp('idcriterio').getValue();
            },
            load: function(store) {
                store.each(function(record) {

                });
            }
        }
    });

    store_servicios.load({params: {start: 0, limit: 30}});



    var smServicios = new Ext.selection.CheckboxModel({
        listeners: {
            select: function(selectionModel, record, index, eOpts) {
                //console.log('selected:'+index);
                /*if(record.data.padre){
                 smServicios.deselect(index);
                 Ext.Msg.alert('Alerta','Este Punto ya fue asignado a un Padre');
                 }*/


            }
        }
    });





    function asignarVariosServicios() {
        var param = '';
        if (smServicios.getSelection().length > 0)
        {
            //var estado = 0;
            for (var i = 0; i < smServicios.getSelection().length; ++i)
            {
                param = param + smServicios.getSelection()[i].data.idServicio;

                /*if(smServicios.getSelection()[i].data.padre)
                 {
                 estado = estado + 1;
                 }*/
                if (i < (smServicios.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (padres_cmb.getValue())
            {   
                var contExisteErrorDatosEnvio = 1;
                
                if( strPrefjoEmpresa == "TN" )
                {   
                    Ext.Ajax.request({
                            url: url_verifica_datos_envio_ajax,
                            method: 'post',
                            async:false,
                            params: { idPuntoPadre: padres_cmb.getValue()},
                            success: function(response) {
                                var strRespuesta = response.responseText;
                                if( 'OK' != strRespuesta)
                                {
                                    contExisteErrorDatosEnvio = contExisteErrorDatosEnvio + 1;
                                    Ext.Msg.alert('Error', strRespuesta);
                                }
                            },
                            failure: function(result)
                            {
                                contExisteErrorDatosEnvio = contExisteErrorDatosEnvio + 1;
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                    });
                }
                
                if( contExisteErrorDatosEnvio == 1 )
                {
                    Ext.Msg.confirm('Alerta', 'El punto de facturacion seleccionado se asignara a los servicios seleccionados. Desea continuar?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    url: url_asignar_ajax,
                                    method: 'post',
                                    params: {param: param, padre: padres_cmb.getValue()},
                                    success: function(response) {
                                        var text = response.responseText;
                                        store_servicios.load();
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }
                        }); 
                }
            }
            else
            {
                Ext.Msg.alert('Error', 'Falta escoger el punto padre.');
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }




    var listViewServicios = Ext.create('Ext.grid.Panel', {
        width: 980,
        height: 400,
        collapsible: false,
        title: 'Listado de Servicios',
        selModel: smServicios,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [padres_cmb,
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_add',
                        text: 'Asignar',
                        disabled: false,
                        itemId: 'asignarpadre',
                        scope: this,
                        handler: function() {
                            asignarVariosServicios();
                        }
                    }]
            }],
        renderTo: Ext.get('lista_servicios'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store_servicios,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store_servicios,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        viewConfig: {
            getRowClass: function(record, index) {
                var c = record.get('padre');
                //console.log(c);
                if (c) {
                    return 'greenTextGrid';
                } else {
                    return 'blackTextGrid';
                }
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Punto',
                width: 150,
                dataIndex: 'loginPunto'
            }, {
                text: 'Servicio',
                dataIndex: 'descripcionProducto',
                align: 'right',
                width: 200
            }, {
                text: 'Es Venta',
                dataIndex: 'esVenta',
                align: 'right',
                width: 50
            }, {
                text: 'Precio Venta',
                dataIndex: 'precioVenta',
                align: 'right',
                width: 80
            }, {
                text: 'Padre',
                dataIndex: 'loginPadre',
                align: 'right',
                width: 150
            }, {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                flex: 100
            }, {
                text: 'Acciones',
                width: 130,
                renderer: renderAccionesServicios
            }

        ]
    });


    function renderAccionesServicios(value, p, record) {
        var iconos = '';
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        return Ext.String.format(
            iconos,
            value
            );
    }



    var filterPanelServ = Ext.create('Ext.panel.Panel', {
        bodyPadding: 1, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: true,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:1px'
        },
        collapsible: true,
        collapsed: true,
        width: 980,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                //xtype: 'button',
                iconCls: "icon_search",
                handler: BuscarEnServicios
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: LimpiarServicios
            }

        ],
        items: [
            //{html:"&nbsp;",border:false,width:20},                                        
            TFLoginServ,
            {html: "&nbsp;", border: false, width: 200},
            criterio_serv_cmb,
            TFLoginPadreServ,
        ],
        renderTo: 'filtro_servicios'
    });


    function validaPadreEnGrid(loginPto) {
        console.log("Valida Padre");

        store_servicios_verifica.load({params: {start: 0, limit: 1, nombrePadre: loginPto, criterioLoginPadre: 'igual'}});

        var respuesta = false;
        console.log("count:" + store_servicios_verifica.getCount());
        for (var i = 0; i < store_servicios_verifica.getCount(); i++) {

            console.log("i:" + i);
            if (loginPto == store_servicios_verifica.getAt(i).data.loginPadre) {
                console.log(store_servicios_verifica.getAt(i).data.factura);
                respuesta = true;
            }
        }
        return respuesta;
    }


    function BuscarEnServicios() {

        /*if ((!Ext.getCmp('loginServ').getValue())&&(!Ext.getCmp('loginPadreServ').getValue()))
         {
         Ext.Msg.show({
         title:'Error en Busqueda',
         msg: 'Por Favor para realizar la busqueda debe ingresar criterios.',
         buttons: Ext.Msg.OK,
         animEl: 'elId',
         icon: Ext.MessageBox.ERROR
         });		 
         
         }
         else
         {*/
        store_servicios.load({params: {start: 0, limit: 30}});
        //}


    }

    function LimpiarServicios() {

        Ext.getCmp('idcriterio').setValue('');
        Ext.getCmp('loginPadreServ').setValue('');
        Ext.getCmp('loginServ').setValue('');
    }


});
