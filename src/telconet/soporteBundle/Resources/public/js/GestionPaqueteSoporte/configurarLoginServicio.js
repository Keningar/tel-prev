
//definicion de check
Ext.define('Ext.ux.CheckColumnPatch', {
    override: 'Ext.ux.CheckColumn',

    /**
     * @cfg {Boolean} [columnHeaderCheckbox=false]
     * True to enable check/uncheck all rows
     */
    columnHeaderCheckbox: false,

    constructor: function (config) {
        var me = this;
        me.callParent(arguments);

        me.addEvents('beforecheckallchange', 'checkallchange');

        if (me.columnHeaderCheckbox) {
            me.on('headerclick', function () {
                this.updateAllRecords();
            }, me);

            me.on('render', function (comp) {
                var grid = comp.up('grid');
                this.mon(grid, 'reconfigure', function () {
                    if (this.isVisible()) {
                        this.bindStore();
                    }
                }, this);

                if (this.isVisible()) {
                    this.bindStore();
                }

                this.on('show', function () {
                    this.bindStore();
                });
                this.on('hide', function () {
                    this.unbindStore();
                });
            }, me);
        }
    },

    onStoreDateUpdate: function () {
        var allChecked,
            image;

        if (!this.updatingAll) {
            allChecked = this.getStoreIsAllChecked();
            if (allChecked !== this.allChecked) {
                this.allChecked = allChecked;
                image = this.getHeaderCheckboxImage(allChecked);
                this.setText(image);
            }
        }
    },

    getStoreIsAllChecked: function () {
        var me = this,
            allChecked = false;
        me.store.each(function (record) {
            if (record.get(this.dataIndex)) {
                allChecked = true;
                return true;
            }else{
                allChecked = false;
                return false;
            }
        }, me);
        return allChecked;
    },

    bindStore: function () {
        var me = this,
            grid = me.up('grid'),
            store = grid.getStore();

        me.store = store;

        me.mon(store, 'datachanged', function () {
            this.onStoreDateUpdate();
        }, me);
        me.mon(store, 'update', function () {
            this.onStoreDateUpdate();
        }, me);

        me.onStoreDateUpdate();
    },

    unbindStore: function () {
        var me = this,
            store = me.store;

        me.mun(store, 'datachanged');
        me.mun(store, 'update');
    },

    updateAllRecords: function () {
        var me = this,
            allChecked = !me.allChecked;

        if (me.fireEvent('beforecheckallchange', me, allChecked) !== false) {
            this.updatingAll = true;
            me.store.suspendEvents();
            me.store.each(function (record) {
                record.set(this.dataIndex, allChecked);
            }, me);
            me.store.resumeEvents();
            me.up('grid').getView().refresh();
            this.updatingAll = false;
            this.onStoreDateUpdate();
            me.fireEvent('checkallchange', me, allChecked);
        }
    },

    getHeaderCheckboxImage: function (allChecked) {
        var cls = [],
            cssPrefix = Ext.baseCSSPrefix;

        if (this.columnHeaderCheckbox) {
            allChecked = this.getStoreIsAllChecked();
            //Extjs 4.2.x css
            cls.push(cssPrefix + 'grid-checkcolumn');
            //Extjs 4.1.x css
            cls.push(cssPrefix + 'grid-checkheader');

            if (allChecked) {
                //Extjs 4.2.x css
                cls.push(cssPrefix + 'grid-checkcolumn-checked');
                //Extjs 4.1.x css
                cls.push(cssPrefix + 'grid-checkheader-checked');
            }
        }
        return '<div style="margin:auto" class="' + cls.join(' ') + '">&#160;</div>'
    }
});
//

Ext.onReady(function () {
    
    Ext.tip.QuickTipManager.init();
    Ext.require([
        'Ext.ux.CheckColumn'
    ]);
    smL = Ext.create('Ext.selection.CheckboxModel',
        {
            checkOnly: true,
            showHeaderCheckbox: true,
            mode: 'SINGLE',
            listeners:
            {
                select: function (model, record, index) {
                    objRecord = record;
                    gridServicios.store.proxy.extraParams.idPunto = (record.data.idPunto).toString();
                    gridServicios.store.proxy.extraParams.login = record.data.loginP;
                    gridServicios.store.proxy.extraParams.login2 = record.data.login;
                    gridServicios.store.proxy.extraParams.Descripcion_Producto = record.data.Descripcion_Producto;
                    gridServicios.store.reload();
                    
                }
                // ,
                // deselect: function (model, record, index) {
                //     gridServicios.store.proxy.extraParams.idPunto = '';
                //     gridServicios.store.proxy.extraParams.login = '';
                //     gridServicios.store.proxy.extraParams.login2 = '';
                //     gridServicios.store.reload();
                // }

            }
        });

    //Creación del store para los logines del cliente
    var storeLogines = Ext.data.Store(
        {
            autoLoad: true,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: url_getPuntosCliente,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'listado',
                },
                extraParams:
                {
                    idCliente: strIdCliente,
                    nombre: strNombre,
                    cliente: strCliente,
                    esPadre: strEsPadre,
                    rol: strRol,
                    razonSocial: strRazonSocial
                }
            },
            fields: [
                { name: 'loginP', type: 'string'},
                { name: 'idPunto', type: 'int' },
                { name: 'codigo', type: 'int' },
                { name: 'login', type: 'string' },
                { name: 'direccion', type: 'string' },
                //{ name: 'Descripcion_Producto', type: 'string' }
            ]
        }
    );

    //Creación del grid que contiene los logines del cliente - primer que grid que aparece lleno
    gridLogines = Ext.create('Ext.grid.Panel',
        {
            id: 'gridLogines',
            width: 1000,
            height: 200,
            renderTo: 'gridLogines',
            tittle: 'Logines',
            store: storeLogines,
            viewConfig:
            {
                enableTextSelection: true,
                loadingText: '<b>Cargando información, por favor espere...',
                emptyText: '<center><br/><b/>*** No se encontró información ***',
                loadMask: true
            },
            selModel: smL,
            columns: [
                {
                    text: 'Codigo',
                    width: 50,
                    dataIndex: 'codigo',
                    hidden: true
                },
                {
                    text: 'Seleccionar',
                    width: 150,
                    dataIndex: 'seleccionar'
                },
                {
                    text: 'Login',
                    width: 350,
                    dataIndex: 'login'
                },
                {
                    text: 'Direccion',
                    width: 450,
                    dataIndex: 'direccion'
                },
                // {
                //     text: 'Producto',
                //     width: 350,
                //     dataIndex: 'Descripcion_Producto'
                // }
            ]
        });

    //Creación del store para los servicios según el punto - segundo grid que depende del primero
    var storeServicios = Ext.data.Store(
        {
            // autoLoad: true,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: url_getServiciosByPunto,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'listado',
                },
                extraParams:
                {
                    idPunto: '',
                    login: '',
                    login2: '',
                    estado: 'Activo'
                }
            },
            fields: [
                { name: 'id_servicio_soporte', type: 'int' },
                { name: 'id_punto_soporte', type: 'int'},
                { name: 'loginauxods', type: 'string' },
                { name: 'Descripcion_Producto', type: 'string' },
                { name: 'estado_servicio', type: 'string' },
                { name: 'permiteactivar', type: 'boolean' },
                { name: 'isSelected', type: 'boolean'}
            ]
        }
    );

    //Creación del grid que contiene los servicios según el punto
    gridServicios = Ext.create('Ext.grid.Panel',
        {
            id: 'gridServicios',
            width: 1000,
            height: 200,
            renderTo: 'gridServicios',
            tittle: 'Servicios',
            store: storeServicios,
            loadMask: true,
            viewConfig:
            {
                enableTextSelection: true,
                loadingText: '<b>Cargando información, por favor espere...',
                emptyText: '<center><br/><b/>*** No se encontró servicios configurados para el paquete de horas de soporte. ***'
            },
            columns: [
                {
                    xtype: 'checkcolumn',
                    columnHeaderCheckbox: true,
                    store: storeServicios,
                    sortable: false,
                    hideable: false,
                    menuDisabled: true,
                    dataIndex: 'isSelected',
                    width: 28,
                    listeners: {
                   
                    },
                    editor: {
                        xtype: 'checkbox',
                        cls: 'x-grid-checkheader-editor'
                    }
                },
                {
                    text: 'Codigo',
                    width: 50,
                    dataIndex: 'codigo',
                    hidden: true
                },
                {
                    text: 'Seleccionar',
                    width: 150  
                },
                {
                    text: 'Login Auxiliar/Orden Servicio',
                    width: 200,
                    dataIndex: 'loginauxods'
                },
                {
                    text: 'Producto',
                    width: 321,
                    dataIndex: 'Descripcion_Producto'
                },
                {
                    text: 'Estado Servicio',
                    width: 100,
                    dataIndex: 'estado_servicio'
                },
                {
                    xtype: 'checkcolumn',
                    name: 'permiteactivar',
                    header: 'Activa paquete de horas de soporte',
                    mode: 'MULTI',
                    width: 198,
                    dataIndex: 'permiteactivar',
                    stopSelection: false,
                    checkOnly: true,
                    listeners: {
                        
                    },
                    editor: {
                        xtype: 'checkbox',
                        cls: 'x-grid-checkheader-editor'
                    }
                }

            ]
        });

    //Creación del store para los servicios asociados
    var storeServiciosA = Ext.data.Store(
        {
            autoLoad: true,
            total: 'total',
            fields: [
                { name: 'servicio_soporte_id', type: 'int' },
                { name: 'punto_soporte_id', type: 'int'},
                { name: 'login', type: 'string' },
                { name: 'loginaux', type: 'string' },
                { name: 'permiteactivar', type: 'boolean'} ,
                { name: 'descripcion_producto', type: 'string'} 
            ],
            proxy:
            {
                type: 'memory',
                enablePaging: true,
                data: []
            }
        }
    );

    //Creación del grid que contiene los servicios asociados.
    gridServiciosA = Ext.create('Ext.grid.Panel',
        {
            id: 'gridServiciosA',
            width: 1000,
            height: 200,
            renderTo: 'gridServiciosA',
            store: storeServiciosA,
            viewConfig:
            {
                enableTextSelection: true,
                loadingText: '<b>Cargando información, por favor espere...',
                emptyText: '<center><br/><b/>*** No se encontró información ***',
                loadMask: true
            },
            tittle: 'Servicios',
            columns: [
                {
                    text: 'Codigo',
                    width: 50,
                    dataIndex: 'codigo',
                    hidden: true
                },
                {
                    text: 'Login',
                    width: 300,
                    dataIndex: 'login'
                },
                {
                    text: 'Login Auxiliar',
                    width: 348,
                    dataIndex: 'loginaux'
                },
                {
                    text: 'Producto',
                    width: 350,
                    dataIndex: 'descripcion_producto'
                }
            ]
        });

    

    Ext.create('Ext.Button', {
        text: 'Agregar',
        padding: 5,
        renderTo: 'button-agregar',
        handler: function () {
            
            
            let login = gridLogines.getSelectionModel().getSelection()[0].data.login;


            (gridServicios.store.data.items).forEach(element => {
                if (element.data.isSelected == true && element.modified.isSelected == false){

//                    record = {punto_soporte_id: element.data.id_punto_soporte, servicio_soporte_id: element.data.id_servicio_soporte, login: login, loginaux: element.data.loginauxods, permiteactivar: element.data.permiteactivar}
record = {punto_soporte_id: element.data.id_punto_soporte, servicio_soporte_id: element.data.id_servicio_soporte, login: login, loginaux: element.data.loginauxods, permiteactivar: element.data.permiteactivar, descripcion_producto: element.data.Descripcion_Producto}
                    let existente = false;

                    (gridServiciosA.store.data.items).forEach(element => {
                        if (element.data.loginaux == record.loginaux && record.login == element.data.login)
                        {
                            existente = true;
                        }

                    })

                    if (!existente) {

                        gridServiciosA.store.proxy.data.unshift(record); 
                    }else{
                        Ext.Msg.alert('Alerta','El login '+record.loginaux+' ya está agregado.');
                    }

                    gridServiciosA.store.reload();

                }

            });

            
        }
    });


    function verificarRepetidos(login, loginaux, record)
    {
        
                if ((loginaux == record.loginaux) && (login == record.login)){
                    console.log('falso');
                    return false;
                    
                }
                else 
                {
                    return true;
                }
            
    } 

    Ext.create('Ext.Button', {
        text: 'Guardar',
        padding: 5,
        renderTo: 'button-guardar',
        handler: function () {
            this.setDisabled(true);
            Ext.Ajax.request({
                url: url_putServiciosPaqueteSoporte,
                method: 'post',
                params:{
                    idServicio: strIdServicio,
                    servicios: JSON.stringify(gridServiciosA.store.proxy.data)
                     
                },
                success: function(response){
                    Ext.Msg.alert('Éxito', 'Los servicios y logines fueron asociados correctamente');
                    gridServiciosA.store.proxy.data = [];
                    window.location.replace('/comercial/punto/'+ strIdPunto +'/Cliente/show')
                    gridServiciosA.store.reload();
                },
                failure: function() {		
                    Ext.Msg.alert('Alerta ','Error al realizar la acción');
                }
            });

        }
    });

    Ext.create('Ext.Button', {
        text: 'Limpiar',
        padding: 5,
        renderTo: 'button-cancelar',
        handler: function () {
            gridServiciosA.store.proxy.data = [];
            gridServiciosA.store.reload();
        }
    });

    Ext.create('Ext.Button', {
        text: 'Regresar',
        padding: 5,
        renderTo: 'button-regresar',
        handler: function () {
            window.location.replace('/comercial/punto/'+ strIdPunto +'/Cliente/show');
        }
    });

});