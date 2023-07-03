/* Funcion que sirve para mostrar la pantalla de cambio de
 * cpe y realizar la llamada ajax para el cambio de
 * cpe para las empresas TTCO y MD
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 */
var strTieneMigracionHw    = "";
var strEquipoCpeHw         = "";
var strEquipoWifiAdicional = "";
var strAgregarWifi         = "";
var intElementoWifi        = "";
var strNombreWifi          = "";

var connCargarLider = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Consultando el líder, Por favor espere!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

function cambioElementoCliente(data){
    var esIsb                = "NO";
    var hiddenResponsable    = true;
    var seleccionResponsable = "C";
    var strNombreLider       = "";

    if(data.descripcionProducto === "INTERNET SMALL BUSINESS" || data.descripcionProducto === "TELCOHOME")
    {
        esIsb = "SI";
    }
    booleanServicioSegVeh = false;
    if(data.descripcionProducto === "SEG_VEHICULO")
    {
        booleanServicioSegVeh = true;
    }

    //Se verfica si el producto es SAFE ENTRY
    boolSafeEntry = data.descripcionProducto === 'SAFE ENTRY';
    if(data.flujo == "TN")
    {
        hiddenResponsable = false;
    }

    booleanTipoRedGpon = false;
    if(typeof data.booleanTipoRedGpon !== 'undefined')
    {
        booleanTipoRedGpon = data.booleanTipoRedGpon;
    }
    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_cuadrillas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'id_cuadrilla', mapping: 'id_cuadrilla'},
                {name: 'nombre_cuadrilla', mapping: 'nombre_cuadrilla'}
            ]
    });

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empleadosPorEmpresa,
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
                {name: 'intIdPersonEmpresaRol', mapping: 'intIdPersonEmpresaRol'},
                {name: 'strNombresEmpleado', mapping: 'strNombresEmpleado'}
            ]
    });

    var storeInterfacesModelo = new Ext.data.Store({
        pageSize: 100,
        proxy: {
            type: 'ajax',
            url : getInterfacesPorModelo,
            reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
            }
        },
        fields:
            [
              {name:'nombreInterface', mapping:'nombreInterface'}
            ]
    });

    var storeSolicitud = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        timeout: 400000,
        proxy: {
            type: 'ajax',
            url : getElementosPorSolicitud,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio,
                esIsb     : esIsb
            }
        },
        fields:
            [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombre', mapping:'nombre'},
              {name:'nombreModelo', mapping:'nombreModelo'},
              {name:'tipoElemento', mapping:'tipoElemento'},
              {name:'serie', mapping:'serie'},
              {name:'mac', mapping:'mac'},
              {name:'ubicacion', mapping:'ubicacion'},
              {name:'ip', mapping:'ip'},
              {name:'descripcion', mapping:'descripcion'},
              {name:'strTieneMigracionHw', mapping:'strTieneMigracionHw'},
              {name:'strEquipoCpeHw', mapping:'strEquipoCpeHw'},
              {name:'strEquipoWifiAdicional', mapping:'strEquipoWifiAdicional'},
              {name:'strAgregarWifi', mapping:'strAgregarWifi'},
              {name:'intElementoWifi', mapping:'intElementoWifi'},
              {name:'strNombreWifi', mapping:'strNombreWifi'},
              {name:'numeroInterfacesConectados', mapping:'numeroInterfacesConectados'}
            ]
    });
    
    Ext.define('ElementosPorSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombre', mapping:'nombre'},
              {name:'nombreModelo', mapping:'nombreModelo'},
              {name:'tipoElemento', mapping:'tipoElemento'},
              {name:'serie', mapping:'serie'},
              {name:'mac', mapping:'mac'},
              {name:'ip', mapping:'ip'},
              {name:'descripcion', mapping:'descripcion'},
              {name:'strTieneMigracionHw', mapping:'strTieneMigracionHw'},
              {name:'strEquipoCpeHw', mapping:'strEquipoCpeHw'},
              {name:'strEquipoWifiAdicional', mapping:'strEquipoWifiAdicional'},
              {name:'strAgregarWifi', mapping:'strAgregarWifi'},
              {name:'intElementoWifi', mapping:'intElementoWifi'},
              {name:'strNombreWifi', mapping:'strNombreWifi'},
              {name:'numeroInterfacesConectados', mapping:'numeroInterfacesConectados'}
        ]
    });
    
    gridElementosPorSolicitud = new Ext.create('Ext.grid.Panel', {
        id:'gridElementosPorSolicitud',
        store: storeSolicitud,
        columnLines: true,
        columns: [
        {
            header: 'Nombre Elemento',
            dataIndex: 'nombre',
            width: 200,
            sortable: true
        },
        {
            header: 'Modelo Elemento',
            dataIndex: 'nombreModelo',
            width: 100,
            sortable: true
        },
        {
            header: 'Tipo Elemento',
            dataIndex: 'tipoElemento',
            width: 100,
            sortable: true
        },
        {
            header: 'Ip',
            dataIndex: 'ip',
            width: 150,
            sortable: true
        },
        {
            header: 'Mac',
            dataIndex: 'mac',
            width: 120,
            sortable: true
        },
        {
            header: 'Serie',
            dataIndex: 'serie',
            width: 120,
            sortable: true
        },
        {
            header   : 'Ubicación',
            dataIndex: 'ubicacion',
            width    :  120,
            sortable :  true
        },
        {
            header: 'idElemento',
            dataIndex: 'idElemento',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            header: 'strTieneMigracionHw',
            dataIndex: 'strTieneMigracionHw',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            header: 'strEquipoCpeHw',
            dataIndex: 'strEquipoCpeHw',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            header: 'strEquipoWifiAdicional',
            dataIndex: 'strEquipoWifiAdicional',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            header: 'strAgregarWifi',
            dataIndex: 'strAgregarWifi',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            header: 'Elemento Wifi',
            dataIndex: 'intElementoWifi',
            width: 120,
        },
        {
            header: 'numeroInterfacesOcupadas',
            dataIndex: 'numeroInterfacesConectados',           
            hidden: true,
            hideable: false
        },
        {
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 85,
            items: [
                //CAMBIAR DE ELEMENTO
                {
                    getClass: function(v, meta, rec) {
                        return 'button-grid-verDslam';
                    },
                    tooltip: 'Cambiar Elemento Cliente',
                    handler: function(grid, rowIndex, colIndex) {

                        var ubicacionDispositivo   = grid.getStore().getAt(rowIndex).data.ubicacion;
                        var idElementoCliente      = grid.getStore().getAt(rowIndex).data.idElemento;
                        var nombreElementoCliente  = grid.getStore().getAt(rowIndex).data.nombre;
                        var ipElementoCliente      = grid.getStore().getAt(rowIndex).data.ip;
                        strTieneMigracionHw        = grid.getStore().getAt(rowIndex).data.strTieneMigracionHw;
                        strEquipoCpeHw             = grid.getStore().getAt(rowIndex).data.strEquipoCpeHw;
                        strEquipoWifiAdicional     = grid.getStore().getAt(rowIndex).data.strEquipoWifiAdicional;
                        strAgregarWifi             = grid.getStore().getAt(rowIndex).data.strAgregarWifi;
                        intElementoWifi            = grid.getStore().getAt(rowIndex).data.intElementoWifi;
                        strNombreWifi              = grid.getStore().getAt(rowIndex).data.strNombreWifi;
                        
                        storeInterfacesModelo.loadData([],false);
                        var storeModelosCpe            = null;
                        var storeModelosTransciever    = null;
                        var storeModelosWifi           = null;
                        var storeModelosNodo           = null;
                        var strTipoElemento            = grid.getStore().getAt(rowIndex).data.tipoElemento;
                        var boolHiddenWanAdicional     = true;
                        var numeroInterfacesConectadas = grid.getStore().getAt(rowIndex).data.numeroInterfacesConectados;
                        
                        //Si el cpe tiene los 2 puertos wan ocupados se solicitara al usuario ingresar las
                        //2 macs nuevas correspondientes a los  puertos wan1 y wan2 del cpe
                        if(numeroInterfacesConectadas === 2)
                        {
                            boolHiddenWanAdicional = false;
                        }
                        
                        if(booleanTipoRedGpon){
                            storeModelosCpe  = new Ext.data.Store({
                                pageSize: 3000,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        strTipoRed: data.strTipoRed,
                                        forma:      'Empieza con',
                                        estado:     "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo = storeModelosCpe;
                        }
                        else if(strTipoElemento.indexOf("TRANSCEIVER") === 0){//Si el tipo empieza con TRANSCEIVER
                            storeModelosTransciever = new Ext.data.Store({  
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'TRANSCEIVER',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo        = storeModelosTransciever;
                        }
                        else if(strTipoElemento.indexOf("CPE") === 0){//Si el tipo empieza con CPE
                            storeModelosCpe  = new Ext.data.Store({
                                pageSize: 1000,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'CPE',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo = storeModelosCpe;
                        }
                        else if(strTipoElemento.indexOf("ROUTER") === 0){//Si el tipo empieza con ROUTER
                            storeModelosCpe  = new Ext.data.Store({
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'ROUTER',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo = storeModelosCpe;
                        }
                        else if(strTipoElemento.indexOf("RADIO") === 0){//Si el tipo empieza con RADIO
                            storeModelosCpe  = new Ext.data.Store({
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'RADIO',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo = storeModelosCpe;
                        }
                        else{
                            storeModelosCpe  = new Ext.data.Store({
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   strTipoElemento,
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                            storeModelosNodo = storeModelosCpe;
                        }

                        if (strTieneMigracionHw == "SI")
                        {
                             storeModelosWifi = new Ext.data.Store({
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'CPE WIFI',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                      {name:'modelo', mapping:'modelo'},
                                      {name:'codigo', mapping:'codigo'}
                                    ]
                            });
                        }

                        var elementoClienteNuevo = {
                            id: 'elementoClienteGroup',
                            xtype: 'fieldset',
                            title: 'Elemento Nuevo',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500,
                                layout: 'fit'
                            },
                            items: [{
                                xtype: 'container',
                                layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                },
                                items: [
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'nombreCpe',
                                            name: 'nombreCpe',
                                            fieldLabel: 'Elemento',
                                            displayField: nombreElementoCliente,
                                            value: nombreElementoCliente,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'ipCpe',
                                            name: 'ipCpe',
                                            fieldLabel: 'Ip',
                                            displayField: ipElementoCliente,
                                            value: ipElementoCliente,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'serieCpe',
                                            name: 'serieCpe',
                                            fieldLabel: 'Serie Elemento',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modeloCpe',
                                            name: 'modeloCpe',
                                            fieldLabel: 'Modelo',
                                            displayField:'modelo',
                                            valueField: 'modelo',
                                            loadingText: 'Buscando...',
                                            store: storeModelosCpe,
                                            width: '35%',
                                            listeners: {                                                
                                                blur: function(combo){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio',
                                                            idServicio: data.idServicio
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];                                                            
                                                            var mensaje = respuesta[1].split(","); 	
                                                            var descripcion = mensaje[0]; 	
                                                            var mac = mensaje[1];

                                                            if(status=="OK")
                                                            {                                                                
                                                                Ext.getCmp('descripcionCpe').setValue = descripcion; 
                                                                Ext.getCmp('descripcionCpe').setRawValue(descripcion); 	
                                                                Ext.getCmp('macCpe').setValue = mac; 	
                                                                Ext.getCmp('macCpe').setRawValue(mac);
                                                                
                                                                if(prefijoEmpresa == 'TN' && esIsb === "NO" && !booleanTipoRedGpon
                                                                   && !booleanServicioSegVeh && !boolSafeEntry)
                                                                {  
                                                                    storeInterfacesModelo.proxy.extraParams = {
                                                                                                               modeloElemento     : combo.getValue(),
                                                                                                               idElementoAnterior : idElementoCliente,
                                                                                                               estado             : 'Activo'
                                                                                                              };
                                                                    storeInterfacesModelo.load({
                                                                        callback:function()
                                                                        {
                                                                            if(storeInterfacesModelo.getCount()===0)
                                                                            {
                                                                                Ext.Msg.alert('Error ',"Modelo '"+ combo.getValue() +"' no posee interfaces tipo Wan. \n\
                                                                                               Necesarias para realizar cambio.");
                                                                            }
                                                                        }
                                                                    }); 
                                                                }
                                                                else
                                                                {
                                                                    Ext.getCmp('cmbPuerto').setVisible(false);
                                                                    if (nombreElementoCliente.includes("RentaSmartWifi"))
                                                                    {
                                                                        Ext.getCmp('macCpe').setReadOnly(true);
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionCpe').setValue = status;
                                                                Ext.getCmp('descripcionCpe').setRawValue(status);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        { width: '10%', border: false},
                                        
                                        //--------------------------------------
                                        
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionCpe',
                                            name: 'descripcionCpe',
                                            fieldLabel: 'Descripcion',
                                            displayField: '',
                                            value: '',
                                            width: '30%'                                        
                                        },
                                        { width: '15%', border: false},
                                        { width: '30%', border: false},
                                        { width: '10%', border: false},
                                        
                                        //---------------------------------------
                                        
                                        { width: '10%', border: false},
                                        {
                                                id: 'interfacesCpeGroup',
                                                xtype: 'fieldset',
                                                title: 'Interfaces Nuevas',
                                                defaultType: 'textfield',
                                                colspan: 3,
                                                defaults: {
                                                    width: 500,
                                                    layout: 'fit'
                                                },
                                                items: [
                                                    {
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 5,
                                                            align: 'stretch'
                                                        },
                                                        items: [
                                                        
                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'combobox',
                                                                queryMode: 'local',
                                                                id: 'cmbPuerto',
                                                                name: 'cmbPuerto',
                                                                fieldLabel: (prefijoEmpresa === 'TN' && esIsb === "NO") ? 'Puerto (PRI)':'Interface',
                                                                displayField: 'nombreInterface',
                                                                valueField: 'nombreInterface',
                                                                width: '30%',
                                                                loadingText: 'Buscando...',
                                                                store: storeInterfacesModelo,
                                                                hidden: booleanTipoRedGpon || booleanServicioSegVeh || boolSafeEntry,
                                                                listeners: {
                                                                    change: function(combo) 
                                                                    {
                                                                        var cmbPri = combo.getValue();
                                                                        var cmbBck = Ext.getCmp('cmbPuertoBck').getValue();

                                                                        if (cmbBck === cmbPri)
                                                                        {
                                                                            Ext.Msg.alert('Alerta', 'La interface ya se encuentra seleccionada, \n\
                                                                                                     por favor elija otro puerto');
                                                                            Ext.getCmp('cmbPuerto').clearValue();                                                                            
                                                                        }
                                                                    }
                                                                }
                                                            },
                                                            {width: '15%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                id: 'macCpe',
                                                                name: 'macCpe',
                                                                fieldLabel: 'Mac',
                                                                displayField: "",
                                                                value: "",
                                                                width: '30%',
                                                            },
                                                            {width: '10%', border: false},
                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'combobox',
                                                                queryMode: 'local',
                                                                id: 'cmbPuertoBck',
                                                                name: 'cmbPuertoBck',
                                                                fieldLabel: 'Puerto (BCK)',
                                                                displayField: 'nombreInterface',
                                                                valueField: 'nombreInterface',
                                                                width: '30%',
                                                                loadingText: 'Buscando...',
                                                                store: storeInterfacesModelo,
                                                                hidden: boolHiddenWanAdicional,
                                                                listeners: {
                                                                    change: function(combo) 
                                                                    {
                                                                        var cmbBck = combo.getValue();
                                                                        var cmbPri = Ext.getCmp('cmbPuerto').getValue();

                                                                        if (cmbBck === cmbPri)
                                                                        {
                                                                            Ext.Msg.alert('Alerta', 'La interface ya se encuentra seleccionada, \n\
                                                                                                     por favor elija otro puerto');
                                                                            Ext.getCmp('cmbPuertoBck').clearValue();                                                                           
                                                                        }
                                                                    }
                                                                }

                                                            },
                                                            {width: '15%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                id: 'macCpeBck',
                                                                name: 'macCpeBck',
                                                                fieldLabel: 'Mac',
                                                                displayField: "",
                                                                value: "",
                                                                width: '30%',
                                                                allowBlank: boolHiddenWanAdicional,
                                                                blankText : 'Debe Ingresar la MAC para enlace Backup',
                                                                hidden: boolHiddenWanAdicional
                                                            },
                                                            {width: '10%', border: false}
                                                    ]
                                                }
                                            ]
                                        },
                                        //---------------------------------------
                                        { width: '10%', border: false},
                                        { width: '15%', border: false},
                                        { width: '30%', border: false},
                                        { width: '10%', border: false},

                                        //--------------------------------------
 
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'hidden',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'hidden',
                                            id: 'idElemento',
                                            name: 'idElemento',
                                            fieldLabel: 'id',
                                            displayField: idElementoCliente,
                                            value: idElementoCliente,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false}
                                        
                                        //---------------------------------------
                                ]
                            }]
                        };

                        var elementoTransceiver  = {
                            id: 'transcieverGroup',
                            xtype: 'fieldset',
                            title: 'Transciever',
                            defaultType: 'textfield',
                            visible: false,
                            hidden: true,
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 4,
                                        align: 'stretch'
                                    },
                                    items: [

                                        { width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'serieNuevoTransciever',
                                            name:           'serieNuevoTransciever',
                                            fieldLabel:     'Serie Transciever',
                                            displayField:   "",
                                            value:          "",
                                            width:          '25%'
                                        },
                                        { width: '20%', border: false},
                                        {
                                            queryMode:      'local',
                                            xtype:          'combobox',
                                            id:             'modeloNuevoTransciever',
                                            name:           'modeloNuevoTransciever',
                                            fieldLabel:     'Modelo Transciever',
                                            displayField:   'modelo',
                                            valueField:     'modelo',
                                            loadingText:    'Buscando...',
                                            store:          storeModelosTransciever,
                                            width: '25%',
                                            listeners: {
                                                blur: function(combo){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: { 
                                                            serieCpe:       Ext.getCmp('serieNuevoTransciever').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado:         'PI',
                                                            bandera:        'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta   = response.responseText.split("|");
                                                            var status      = respuesta[0];
                                                            var mensaje     = respuesta[1].split(",");
                                                            var descripcion = mensaje[0];

                                                            if(status=="OK")
                                                            {
                                                                Ext.getCmp('descripcionNuevoTransciever').setValue = descripcion;
                                                                Ext.getCmp('descripcionNuevoTransciever').setRawValue(descripcion);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionNuevoTransciever').setValue = status;
                                                                Ext.getCmp('descripcionNuevoTransciever').setRawValue(status);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });//Ext.Ajax.request
                                                }//blur
                                            }//listener
                                        },
                                        {   width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'descripcionNuevoTransciever',
                                            name:           'descripcionNuevoTransciever',
                                            fieldLabel:     'Descripcion Transciever',
                                            displayField:   "",
                                            value:          "",
                                            readOnly:       true,
                                            width:          '25%'
                                        }
                                        //---------------------------------------
                                    ]//items container
                                }//items panel
                            ]//items panel
                        };

                        var dipositivoNodo       = {
                            id          : 'dispositivoNodo',
                            xtype       : 'fieldset',
                            title       : 'Dipositivo Nuevo',
                            defaultType : 'textfield',
                            hidden      :  true,
                            items:
                            [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type   : 'table',
                                        align  : 'stretch',
                                        columns:  4
                                    },
                                    items:
                                    [
                                        {width:'20%',border:false},
                                        {
                                            xtype:       'textfield',
                                            id:          'nombreDispositivo',
                                            name:        'nombreDispositivo',
                                            fieldLabel:  'Nombre Dispositivo',
                                            displayField: nombreElementoCliente,
                                            value:        nombreElementoCliente,
                                            labelWidth:   120,
                                            width:       '35%'
                                        },
                                        {width:'20%',border:false},
                                        {width:'20%',border:false},

                                        {width:'20%',border:false},
                                        {
                                            xtype:        'textfield',
                                            id:           'serieNuevoDipositivo',
                                            name:         'serieNuevoDipositivo',
                                            fieldLabel:   'Serie Dipositivo',
                                            displayField: '',
                                            value:        '',
                                            labelWidth:    120,
                                            width:        '35%'
                                        },
                                        {width:'20%',border:false},
                                        {
                                            xtype:        'combobox',
                                            id:           'modeloNuevoDispositivo',
                                            name:         'modeloNuevoDispositivo',
                                            queryMode:    'local',
                                            fieldLabel:   'Modelo Dispositivo',
                                            displayField: 'modelo',
                                            valueField:   'modelo',
                                            loadingText:  'Buscando...',
                                            store:         storeModelosNodo,
                                            labelWidth:    120,
                                            width:        '30%',
                                            listeners:
                                            {
                                                blur: function(combo)
                                                {
                                                    Ext.Ajax.request(
                                                    {
                                                        url   :  buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe:        Ext.getCmp('serieNuevoDipositivo').getValue(),
                                                            modeloElemento:  combo.getValue(),
                                                            estado:         'PI',
                                                            bandera:        'ActivarServicio'
                                                        },
                                                        success: function(response)
                                                        {
                                                            var respuesta   = response.responseText.split("|");
                                                            var status      = respuesta[0];
                                                            var mensaje     = respuesta[1].split(",");
                                                            var descripcion = mensaje[0];
                                                            var mac         = mensaje[1];

                                                            if (status === "OK")
                                                            {
                                                                Ext.getCmp('descripcionNuevoDispositivo').setValue = descripcion;
                                                                Ext.getCmp('descripcionNuevoDispositivo').setRawValue(descripcion);
                                                                Ext.getCmp('macNuevoDispositivo').setValue = mac;
                                                                Ext.getCmp('macNuevoDispositivo').setRawValue(mac);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionNuevoDispositivo').setValue = status;
                                                                Ext.getCmp('descripcionNuevoDispositivo').setRawValue(status);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        },

                                        {width:'20%',border:false},
                                        {
                                            xtype:        'textfield',
                                            id:           'macNuevoDispositivo',
                                            name:         'macNuevoDispositivo',
                                            fieldLabel:   'Mac Dispositivo',
                                            displayField: '',
                                            value:        '',
                                            readOnly:      true,
                                            labelWidth:    120,
                                            width:        '35%'
                                        },
                                        {width:'20%',border:false},
                                        {
                                            xtype:        'textfield',
                                            id:           'descripcionNuevoDispositivo',
                                            name:         'descripcionNuevoDispositivo',
                                            fieldLabel:   'Descripción Dispositivo',
                                            displayField: '',
                                            value:        '',
                                            readOnly:      true,
                                            labelWidth:    120,
                                            width:        '35%'
                                        }
                                    ]
                                }
                            ]
                        };

                        var elementoResponsable  = {
                                    xtype: 'fieldset',
                                    id: 'responsableCambioCpe',
                                    title: 'Seleccionar responsable del retiro de equipo (*)',
                                    defaultType: 'textfield',
                                    visible: true,
                                    hidden: hiddenResponsable,
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 1,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'combobox',
                                                    queryMode: 'remote',
                                                    id: 'cmb_cuadrillas',
                                                    name: 'cmb_cuadrillas',
                                                    fieldLabel: 'Cuadrilla',
                                                    displayField: 'nombre_cuadrilla',
                                                    valueField: 'id_cuadrilla',
                                                    width: 350,
                                                    minChars: 3,
                                                    loadingText: 'Buscando...',
                                                    store: storeCuadrillas,
                                                    listeners: {
                                                        select: function(combo) {

                                                            seteaLiderCuadrilla(combo.getValue());
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Líder:',
                                                    id: 'nombreLider',
                                                    name: 'nombreLider',
                                                    value: ""
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    queryMode: 'remote',
                                                    id: 'cmb_empleados',
                                                    name: 'cmb_empleados',
                                                    fieldLabel: 'Empleado',
                                                    hidden: true,
                                                    displayField: 'strNombresEmpleado',
                                                    valueField: 'intIdPersonEmpresaRol',
                                                    width: 400,
                                                    loadingText: 'Buscando...',
                                                    store: storeEmpleados
                                                }
                                        ]
                                    }
                                ]
                            };

                        var elementoRadioGrupo   = {
                                xtype: 'radiogroup',
                                hidden: hiddenResponsable,
                                fieldLabel: '<b>Responsable</b>',
                                columns: 1,
                                items: [
                                    {
                                        boxLabel: 'Cuadrilla',
                                        id: 'rbResponsableCuadrilla',
                                        name: 'rbResponsable',
                                        checked: true,
                                        inputValue: "cuadrilla",
                                        listeners:
                                        {
                                            change: function (cb, nv, ov)
                                            {
                                                if (nv)
                                                {
                                                    Ext.getCmp('cmb_cuadrillas').setVisible(true);
                                                    Ext.getCmp('nombreLider').setVisible(true);
                                                    Ext.getCmp('cmb_empleados').setVisible(false);
                                                    Ext.getCmp('cmb_empleados').value = "";
                                                    Ext.getCmp('cmb_empleados').setRawValue("");
                                                    seleccionResponsable = "C";
                                                }
                                            }
                                        }
                                    },
                                    {
                                        boxLabel: 'Empleado',
                                        id: 'rbResponsableEmpleado',
                                        name: 'rbResponsable',
                                        checked: false,
                                        inputValue: "empleado",
                                        listeners:
                                        {
                                            change: function (cb, nv, ov)
                                            {
                                                if (nv)
                                                {
                                                    Ext.getCmp('cmb_empleados').setVisible(true);
                                                    Ext.getCmp('cmb_cuadrillas').setVisible(false);
                                                    Ext.getCmp('nombreLider').setVisible(false);
                                                    Ext.getCmp('cmb_cuadrillas').value = "";
                                                    Ext.getCmp('cmb_cuadrillas').setRawValue("");
                                                    Ext.getCmp('nombreLider').value = "";
                                                    Ext.getCmp('nombreLider').setRawValue("");
                                                    seleccionResponsable = "E";
                                                }
                                            }
                                        }
                                    }
                                ]
                            };

                        var elementoWifiNuevo    = {
                            id: 'elementoWifiGroup',
                            xtype: 'fieldset',
                            title: 'Elemento Wifi',            
                            defaultType: 'textfield',
                            defaults: {
                                width: 500,
                                height: 110
                            },
                            items: [{
                                xtype: 'container',
                                layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                },
                                items: [
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'nombreWifi',
                                            name: 'nombreWifi',
                                            fieldLabel: 'Elemento',
                                            displayField: strNombreWifi,
                                            value: strNombreWifi,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '30%', border: false},
                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'serieWifi',
                                            name: 'serieWifi',
                                            fieldLabel: 'Serie Elemento',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modeloWifi',
                                            name: 'modeloWifi',
                                            fieldLabel: 'Modelo',
                                            displayField:'modelo',
                                            valueField: 'modelo',
                                            loadingText: 'Buscando...',
                                            store: storeModelosWifi,
                                            width: '35%',
                                            listeners: {                                                
                                                blur: function(combo){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: Ext.getCmp('serieWifi').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];                                                            
                                                            var mensaje = respuesta[1].split(","); 	
                                                            var descripcion = mensaje[0]; 	
                                                            var mac = mensaje[1];

                                                            if(status=="OK")
                                                            {                                                                
                                                                Ext.getCmp('descripcionWifi').setValue = descripcion; 
                                                                Ext.getCmp('descripcionWifi').setRawValue(descripcion); 	
                                                                Ext.getCmp('macWifi').setValue = mac; 	
                                                                Ext.getCmp('macWifi').setRawValue(mac);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionWifi').setValue = status;
                                                                Ext.getCmp('descripcionWifi').setRawValue(status);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'macWifi',
                                            name: 'macWifi',
                                            fieldLabel: 'Mac',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionWifi',
                                            name: 'descripcionWifi',
                                            fieldLabel: 'Descripcion',
                                            displayField: '',
                                            value: '',
                                            width: '35%'                                        
                                        },
                                        { width: '10%', border: false},
                                        
                                       
                                        { width: '10%', border: false},
                                        { width: '30%', border: false},

                                        { width: '15%', border: false}, 
 
                                        { width: '10%', border: false},
                                       
                                        {
                                            xtype: 'hidden',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        { width: '30%', border: false}
                                ]
                            }]
                        };

                        var formPanelElementoNuevo = Ext.create('Ext.form.Panel', {
                            bodyPadding  : 2,
                            waitMsgTarget: true,
                            fieldDefaults: {
                                labelAlign: 'left',
                                msgTarget : 'side',
                                labelWidth:  85
                            },
                            items: [
                                elementoClienteNuevo,
                                elementoTransceiver,
                                dipositivoNodo,
                                elementoRadioGrupo,
                                elementoResponsable,
                                elementoWifiNuevo
                            ],
                            buttons: [{
                                text: 'Cambiar',
                                formBind: true,
                                handler: function(){
                                    if (ubicacionDispositivo === 'Nodo') {
                                        var serieDispositivo       = Ext.getCmp('serieNuevoDipositivo');
                                        var modeloDispositivo      = Ext.getCmp('modeloNuevoDispositivo');
                                        var descripcionDispositivo = Ext.getCmp('descripcionNuevoDispositivo');
                                        var macNuevoDispositivo    = Ext.getCmp('macNuevoDispositivo');
                                        var idResponsable          = null;

                                        if (Ext.getCmp('rbResponsableCuadrilla').checked)
                                        {
                                            idResponsable  = Ext.getCmp('cmb_cuadrillas').getValue();
                                            strNombreLider = Ext.getCmp('nombreLider').getValue();
                                        }
                                        else
                                        {
                                            idResponsable  = Ext.getCmp('cmb_empleados').getValue();
                                            strNombreLider = null;
                                        }

                                        if (Ext.isEmpty(serieDispositivo.getValue()))
                                        {
                                            Ext.Msg.show({
                                                title     : 'Alerta',
                                                msg       : 'Ingrese el número de serie del dispositivo.',
                                                icon      :  Ext.Msg.WARNING,
                                                buttons   :  Ext.Msg.CANCEL,
                                                buttonText: {cancel: 'Cerrar'}
                                            });
                                            return;
                                        }

                                        if (Ext.isEmpty(modeloDispositivo.getValue()))
                                        {
                                            Ext.Msg.show({
                                                title     : 'Alerta',
                                                msg       : 'Seleccione el modelo del dispositivo.',
                                                icon      :  Ext.Msg.WARNING,
                                                buttons   :  Ext.Msg.CANCEL,
                                                buttonText: {cancel: 'Cerrar'}
                                            });
                                            return;
                                        }

                                        if (descripcionDispositivo.getValue() === ""                   ||
                                            descripcionDispositivo.getValue() === null                 ||
                                            descripcionDispositivo.getValue() === "NO EXISTE ELEMENTO" ||
                                            descripcionDispositivo.getValue() === "NO HAY STOCK"       ||
                                            descripcionDispositivo.getValue() === "NO EXISTE SERIAL"   ||
                                            descripcionDispositivo.getValue() === "CPE NO ESTA EN ESTADO")
                                        {
                                            Ext.Msg.show({
                                                title     : 'Alerta',
                                                msg       : 'Datos del dispositivo incorrectos, favor revisar!',
                                                icon      :  Ext.Msg.WARNING,
                                                buttons   :  Ext.Msg.CANCEL,
                                                buttonText: {cancel: 'Cerrar'}
                                            });
                                            return;
                                        }

                                        if (strNombreLider === "N/A")
                                        {
                                            Ext.Msg.show({
                                                title     : 'Alerta',
                                                msg       : 'Es obligatorio que la cuadrilla tenga un Líder para realizar '+
                                                            'el cambio de dispositivo.',
                                                icon      :  Ext.Msg.WARNING,
                                                buttons   :  Ext.Msg.CANCEL,
                                                buttonText: {cancel: 'Cerrar'}
                                            });
                                            return;
                                        }

                                        if(!hiddenResponsable && (idResponsable === "" || idResponsable === null))
                                        {
                                            Ext.Msg.show({
                                                title     : 'Alerta',
                                                msg       : 'Favor escoger el responsable del retiro de equipo',
                                                icon      :  Ext.Msg.WARNING,
                                                buttons   :  Ext.Msg.CANCEL,
                                                buttonText: {cancel: 'Cerrar'}
                                            });
                                            return;
                                        }

                                        Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando dispositivo del Cliente...');
                                        Ext.Ajax.request({
                                            url     :  cambiarCpeBoton,
                                            method  : 'post',
                                            timeout :  1000000,
                                            params: {
                                                'ubicacionDispositivo': ubicacionDispositivo,
                                                'idServicio'          : data.idServicio,
                                                'idElemento'          : idElementoCliente,
                                                'serieCpe'            : serieDispositivo.getValue(),
                                                'modeloCpe'           : modeloDispositivo.getValue(),
                                                'tipoElementoCpe'     : strTipoElemento,
                                                'macCpe'              : macNuevoDispositivo.getValue(),
                                                'tipoResponsable'     : seleccionResponsable,
                                                'idResponsable'       : idResponsable
                                            },
                                            success: function(response)
                                            {
                                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                if (response.responseText === "OK") {
                                                    Ext.Msg.alert('Mensaje','Se Cambio el dispositivo del Cliente', function(btn) {
                                                        if (btn === "ok") {
                                                            store.load();
                                                            storeSolicitud.load();
                                                            win.destroy();
                                                        }
                                                    });
                                                } else {
                                                    Ext.Msg.show({
                                                        title     : 'Alerta',
                                                        msg       :  response.responseText,
                                                        icon      :  Ext.Msg.WARNING,
                                                        buttons   :  Ext.Msg.CANCEL,
                                                        buttonText: {cancel: 'Cerrar'}
                                                    });
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                Ext.Msg.show({
                                                    title     : 'Error',
                                                    msg       :  result.statusText,
                                                    icon      :  Ext.Msg.ERROR,
                                                    buttons   :  Ext.Msg.CANCEL,
                                                    buttonText: {cancel: 'Cerrar'}
                                                });
                                            }
                                        });
                                    }
                                    else if(grid.getStore().getAt(rowIndex).data.tipoElemento == "TRANSCEIVER"){
                                        var tipoElemento           = grid.getStore().getAt(rowIndex).data.tipoElemento;
                                        var txtSerieElemento       = Ext.getCmp('serieNuevoTransciever');
                                        var cbxModeloElemento      = Ext.getCmp('modeloNuevoTransciever');
                                        var txtDescripcionElemento = Ext.getCmp('descripcionNuevoTransciever');
                                        var idResponsableCambio    = null;

                                        if(Ext.getCmp('rbResponsableCuadrilla').checked == true)
                                        {
                                            idResponsableCambio = Ext.getCmp('cmb_cuadrillas').getValue();
                                            strNombreLider      = Ext.getCmp('nombreLider').getValue();
                                        }
                                        else if(Ext.getCmp('rbResponsableEmpleado').checked == true)
                                        {
                                            idResponsableCambio = Ext.getCmp('cmb_empleados').getValue();
                                        }

                                        // =============================================================
                                        // Validaciones de los datos requeridos
                                        // =============================================================
                                        if (txtSerieElemento.getValue() === "")
                                        {
                                            txtSerieElemento.markInvalid('Ingrese el numero de serie');
                                            return;
                                        }
                                        if (cbxModeloElemento.getValue() === "")
                                        {
                                            Ext.getCmp('modeloCpe').markInvalid('Seleccione el tipo de subred');
                                            return;
                                        }
                                        if (txtDescripcionElemento.getValue() === "")
                                        {
                                            txtDescripcionElemento.markInvalid('Ingrese el numero de serie');
                                            return;
                                        }
                                        if(txtDescripcionElemento.getValue()=="NO HAY STOCK" || 
                                           txtDescripcionElemento.getValue()=="NO EXISTE SERIAL" || 
                                           txtDescripcionElemento.getValue()=="CPE NO ESTA EN ESTADO")
                                        {
                                            Ext.Msg.alert("Validación","Datos del Elemento incorrectos, favor revisar!", function(btn){
                                                        if(btn=='ok'){

                                                        }
                                                });
                                            return;
                                        }

                                        if(strNombreLider === "N/A")
                                        {
                                            Ext.Msg.alert('Validación ','Es obligatorio que la cuadrilla tenga un Líder para realizar la asignación');
                                            return;
                                        }

                                        if(hiddenResponsable == false &&
                                            (idResponsableCambio == "" || idResponsableCambio == null))
                                        {
                                            Ext.Msg.alert('Validación ','Favor escoger el responsable del retiro de equipo');
                                            return;
                                        }
                                        // =============================================================
                                        Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando Elemento del Cliente...');
                                            
                                        Ext.Ajax.request({
                                            url: cambiarCpeBoton,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                serieCpe:               txtSerieElemento.getValue(),
                                                modeloCpe:              cbxModeloElemento.getValue(),
                                                idServicio:             data.idServicio,
                                                idElemento:             idElementoCliente,
                                                descripcionCpe:         txtDescripcionElemento.getValue(),
                                                tipoElementoCpe:        tipoElemento,
                                                strRegistraEquipo:      data.registroEquipo,
                                                idResponsable:          idResponsableCambio,
                                                tipoResponsable:        seleccionResponsable
                                            },
                                            success: function(response){
                                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                if(response.responseText == "OK"){
                                                    Ext.Msg.alert('Mensaje','Se Cambio el Elemento del Cliente', function(btn){
                                                        if(btn=='ok'){
                                                            store.load();
                                                            storeSolicitud.load();
                                                            win.destroy();
                                                        }
                                                    });
                                                }
                                                else if(response.responseText == "NO ID CLIENTE"){
                                                    Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                                }
                                                else if(response.responseText == "MAX ID CLIENTE"){
                                                    Ext.Msg.alert('Mensaje ','Limite de clientes por Puerto esta en el maximo, <br> Favor comunicarse con el departamento de GEPON' );
                                                }
                                                else if(response.responseText=="IP DEL EQUIPO INCORRECTA"){
                                                    Ext.Msg.alert('Mensaje ', response.responseText + ', <BR> NO PODRA CONTINUAR CON EL CAMBIO DEL EQUIPO');
                                                }
                                                else if(response.responseText == "CANTIDAD CERO"){
                                                    Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                                }
                                                else if(response.responseText == "NO EXISTE PRODUCTO"){
                                                    Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                                }
                                                else if(response.responseText == "NO EXISTE CPE"){
                                                    Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                                }
                                                else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                                    Ext.Msg.alert('Mensaje ','CPE con estado incorrecto, favor revisar!' );
                                                }
                                                else if(response.responseText == "NAF"){
                                                    Ext.Msg.alert('Mensaje ',response.responseText);
                                                }
                                                else{
                                                    Ext.Msg.alert('Mensaje ',response.responseText );
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                    else{
                                        var booleanElementosDinamicos = booleanServicioSegVeh || boolSafeEntry;
                                        var ipCpe            = Ext.getCmp('ipCpe').getValue();
                                        var macCpe           = Ext.getCmp('macCpe').getValue();
                                        var serieCpe         = Ext.getCmp('serieCpe').getValue();
                                        var nombreCpe        = Ext.getCmp('nombreCpe').getValue();
                                        var modeloCpe        = Ext.getCmp('modeloCpe').getValue();
                                        var idElemento       = Ext.getCmp('idElemento').getValue();
                                        var tipoElemento     = grid.getStore().getAt(rowIndex).data.tipoElemento;
                                        var descripcionCpe   = Ext.getCmp('descripcionCpe').getValue();
                                        var nombreInterface  = Ext.getCmp('cmbPuerto').getValue();
                                        var nombreInterfaceBck = Ext.getCmp('cmbPuertoBck').getValue();
                                        var macCpeBck          = Ext.getCmp('macCpeBck').getValue();
                                        var nombreWifi       = Ext.getCmp('nombreWifi').getValue();
                                        var macWifi          = Ext.getCmp('macWifi').getValue();
                                        var serieWifi        = Ext.getCmp('serieWifi').getValue();
                                        var modeloWifi       = Ext.getCmp('modeloWifi').getValue();
                                        var descripcionWifi  = Ext.getCmp('descripcionWifi').getValue();

                                        if(Ext.getCmp('rbResponsableCuadrilla').checked == true)
                                        {
                                            var idResponsable = Ext.getCmp('cmb_cuadrillas').getValue();
                                            strNombreLider    = Ext.getCmp('nombreLider').getValue();
                                        }
                                        else if(Ext.getCmp('rbResponsableEmpleado').checked == true)
                                        {
                                            var idResponsable = Ext.getCmp('cmb_empleados').getValue();
                                        }

                                        var validacion=false;
                                        flag = 0;
                                        
                                        
                                        if(serieCpe=="" || (macCpe=="" && !booleanElementosDinamicos)){
                                            validacion=false;
                                        }
                                        else{
                                            validacion=true;
                                        }

                                        if(macCpe=="" && !booleanElementosDinamicos){
                                            flag=5;
                                        }

                                        if(descripcionCpe=="NO HAY STOCK" || descripcionCpe=="NO EXISTE SERIAL" || descripcionCpe=="CPE NO ESTA EN ESTADO"){
                                            validacion=false;
                                            flag=3;
                                        }
                                        
                                        if (strTieneMigracionHw == "SI")
                                        {
                                             if ((strEquipoWifiAdicional == "CAMBIAR EQUIPO" || strAgregarWifi == "SI") && (tipoElemento == "CPE WIFI"))
                                             {
                                                if(serieWifi=="" || macWifi=="")
                                                {
                                                    validacion=false;
                                                }
                                                else{
                                                    validacion=true;
                                                }

                                                if(descripcionWifi == "NO HAY STOCK"     ||
                                                   descripcionWifi == "NO EXISTE SERIAL" || 
                                                   descripcionWifi == "CPE NO ESTA EN ESTADO")
                                                {
                                                    validacion=false;
                                                    flag=3;
                                                }
                                             }
                                        }
                                        
                                        if(prefijoEmpresa == 'TN' && esIsb === "NO" && !booleanTipoRedGpon && !booleanElementosDinamicos)
                                        {
                                            if(Ext.isEmpty(nombreInterface))
                                            {
                                                validacion=false;
                                                flag = 4;
                                            }
                                            if(!boolHiddenWanAdicional)
                                            {
                                                if(Ext.isEmpty(nombreInterfaceBck))
                                                {
                                                    validacion=false;
                                                    flag = 4;
                                                }
                                            }
                                        }   
                                        
                                        var strEsSmartWifi = 'NO';
                                        if (nombreElementoCliente.includes("RentaSmartWifi"))
                                        {
                                            strEsSmartWifi = 'SI';
                                        }
                                        
                                        var strEsApWifi = 'NO';
                                        if (nombreElementoCliente.includes("RentaApWifi"))
                                        {
                                            strEsApWifi = 'SI';
                                        }
                                        
                                        var strEsExtenderDualBand = 'NO';
                                        if (nombreElementoCliente.includes("ExtenderDualBand"))
                                        {
                                            strEsExtenderDualBand = 'SI';
                                        }

                                        if(strNombreLider === "N/A")
                                        {
                                            Ext.Msg.alert('Validación ','Es obligatorio que la cuadrilla tenga un Líder para realizar la asignación');
                                            return;
                                        }

                                        if(hiddenResponsable == false &&
                                            (idResponsable == "" || idResponsable == null))
                                        {
                                            Ext.Msg.alert('Validación ','Favor escoger el responsable del retiro de equipo' );
                                        }
                                        else
                                        {
                                            if(validacion){
                                                Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando Elemento del Cliente...');

                                                Ext.Ajax.request({
                                                    url: cambiarCpeBoton,
                                                    method: 'post',
                                                    timeout: 1000000,
                                                    params: {
                                                        idServicio:             data.idServicio,
                                                        idElemento:             idElemento,
                                                        modeloCpe:              modeloCpe,
                                                        ipCpe:                  ipCpe,
                                                        strRegistraEquipo:      data.registroEquipo,
                                                        idResponsable:          idResponsable,
                                                        tipoResponsable:        seleccionResponsable,
                                                        nombreCpe:              nombreCpe,
                                                        macCpe:                 macCpe,
                                                        serieCpe:               serieCpe,
                                                        descripcionCpe:         descripcionCpe,
                                                        tipoElementoCpe:        tipoElemento,
                                                        nombreInterface:        nombreInterface,
                                                        nombreInterfaceBck:     nombreInterfaceBck,
                                                        macCpeBck:              macCpeBck,
                                                        interfacesConectadas:   numeroInterfacesConectadas,
                                                        intIdElementoWifi:      intElementoWifi,
                                                        strModeloWifi:          modeloWifi,
                                                        strNombreWifi:          nombreWifi,
                                                        strMacWifi:             macWifi,
                                                        strSerieWifi:           serieWifi,
                                                        strDescripcionWifi:     descripcionWifi,
                                                        strTieneMigracionHw :   strTieneMigracionHw,
                                                        strEquipoCpeHw:         strEquipoCpeHw,
                                                        strEquipoWifiAdicional: strEquipoWifiAdicional,
                                                        strAgregarWifi:         strAgregarWifi,
                                                        esPseudoPe:             data.esPseudoPe,
                                                        strEsSmartWifi:         strEsSmartWifi,
                                                        strEsApWifi:            strEsApWifi,
                                                        tipoRed:                data.strTipoRed,
                                                        strEsExtenderDualBand:  strEsExtenderDualBand
                                                    },
                                                    success: function(response){
                                                        Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                        if(response.responseText == "OK"){
                                                            Ext.Msg.alert('Mensaje','Se Cambio el Elemento del Cliente', function(btn){
                                                                if(btn=='ok'){
                                                                    store.load();
                                                                    storeSolicitud.load();
                                                                    win.destroy();
                                                                }
                                                            });
                                                        }
                                                        else if(response.responseText == "NO ID CLIENTE"){
                                                            Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                                        }
                                                        else if(response.responseText == "MAX ID CLIENTE"){
                                                            Ext.Msg.alert('Mensaje ','Limite de clientes por Puerto esta en el maximo, <br> Favor comunicarse con el departamento de GEPON' );
                                                        }
                                                        else if(response.responseText=="IP DEL EQUIPO INCORRECTA"){
                                                            Ext.Msg.alert('Mensaje ', response.responseText + ', <BR> NO PODRA CONTINUAR CON EL CAMBIO DEL EQUIPO');
                                                        }
                                                        else if(response.responseText == "CANTIDAD CERO"){
                                                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                                        }
                                                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                                                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                                        }
                                                        else if(response.responseText == "NO EXISTE CPE"){
                                                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                                        }
                                                        else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                                            Ext.Msg.alert('Mensaje ','CPE no esta en el estado Correcto, favor revisar!' );
                                                        }
                                                        else if(response.responseText == "NAF"){
                                                            Ext.Msg.alert('Mensaje ',response.responseText);
                                                        }
                                                        else{
                                                            Ext.Msg.alert('Mensaje ',response.responseText );
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                    }
                                                });

                                            }
                                            else{
                                                if(flag==3){
                                                    Ext.Msg.alert("Validación","Datos del Elemento incorrectos, favor revisar!", function(btn){
                                                            if(btn=='ok'){
                                                            }
                                                    });
                                                }
                                                else if(flag==4){
                                                    Ext.Msg.alert("Validación","Seleccionar el puerto del elemento", function(btn){
                                                            if(btn=='ok'){
                                                            }
                                                    });
                                                }
                                                else if(flag==5){
                                                    Ext.Msg.alert("Validación","Debe Ingresar la MAC para enlace Principal", function(btn){
                                                            if(btn=='ok'){
                                                            }
                                                    });
                                                }
                                                else{
                                                    Ext.Msg.alert("Validación","Favor Revise los campos", function(btn){
                                                            if(btn=='ok'){
                                                            }
                                                    });
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                handler: function(){
                                    Ext.get(gridServicios.getId()).unmask();
                                    win.destroy();
                                }
                            }]
                        });

                        if (ubicacionDispositivo === 'Nodo')
                        {
                            Ext.getCmp('dispositivoNodo').show();
                            Ext.getCmp('transcieverGroup').hide();
                            Ext.getCmp('elementoClienteGroup').hide();
                            Ext.getCmp('elementoWifiGroup').hide();
                        }
                        else
                        {
                            // VALIDACION DE LOS FIELDSETS DE LOS ELEMENTOS CPE / TRANSCEIVER
                            if(grid.getStore().getAt(rowIndex).data.tipoElemento == "TRANSCEIVER")
                            {
                                Ext.getCmp('transcieverGroup').show();
                                Ext.getCmp('elementoClienteGroup').hide();
                            }

                            if(prefijoEmpresa == 'MD' || esIsb === "SI")
                            {
                                if (strTieneMigracionHw == "SI")
                                {
                                    if (strEquipoCpeHw == "MANTENER EQUIPO" && (strTipoElemento == "CPE WIFI"))
                                    {
                                        Ext.getCmp('elementoClienteGroup').hide();
                                    }
                                    if ((strEquipoWifiAdicional == "CAMBIAR EQUIPO" || strAgregarWifi == "SI") && (strTipoElemento == "CPE WIFI"))
                                    {
                                        Ext.getCmp('elementoWifiGroup').show();
                                        if (strNombreWifi == null || strNombreWifi == '')
                                        {
                                            Ext.getCmp('nombreWifi').hide();
                                        }
                                    }
                                    else
                                    {
                                        Ext.getCmp('elementoWifiGroup').hide();
                                    }
                                }
                                else
                                {
                                    Ext.getCmp('elementoWifiGroup').hide();
                                }
                            }
                            else
                            {
                                Ext.getCmp('elementoWifiGroup').hide();
                            }

                            if (nombreElementoCliente.includes("RentaSmartWifi"))
                            {
                                Ext.getCmp('nombreCpe').setDisabled(true);
                                Ext.getCmp('ipCpe').setDisabled(true);
                            }
                        }

                        var win = Ext.create('Ext.window.Window', {
                            title   : booleanTipoRedGpon ? 'Cambiar Equipo del Cliente ' + data.strTipoRed : 'Cambiar Equipo del Cliente',
                            modal   :  true,
                            width   :  650,
                            closable:  true,
                            layout  : 'fit',
                            items   : [formPanelElementoNuevo]
                        }).show();

                    }
                },
                //SETEAR ESQUEMA SDWAN - Reutlizar el mismo esquipo
                {
                    getClass: function(v, meta, rec)
                    {
                        var permisoTnp     = $("#ROLE_151-6477");
                        var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                        if (!boolPermisoTnp)
                        {
                            return 'button-grid-invisible';
                        }
                        else if(data.flujo           === 'TN'     &&
                                data.tipoEnlace      === "BACKUP" &&
                                rec.get('ubicacion') !== "Nodo"   &&
                                (data.descripcionProducto === "INTERNET"       ||
                                 data.descripcionProducto === "INTMPLS"        ||
                                 data.descripcionProducto === "INTERNET SDWAN" ||
                                 data.descripcionProducto === "L3MPLS"))
                        {
                            return 'button-grid-setearSdWan';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    },
                    tooltip: 'Definir Esquema SDWAN-CAMBIO EQUIPO',
                    handler: function (grid, rowIndex, colIndex)
                    {
                        setearEsquemaSdwanCambioEquipo(data.idServicio,data.productoId);
                    }
                }
            ]
        }
        ],
        viewConfig:{
            stripeRows:true
        },
        frame: false,
        height: 200
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
        
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
            defaults: {
                width: 900
            },
            items: [

                gridElementosPorSolicitud

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Elementos Por Solicitud',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

/**
 * Documentación para el método 'setearEsquemaSdwanCambioEquipo'.
 *
 * Método encargado de definir si una orden de servicio va utilizar el esquema Sdwan, lo que quiere decir que se pueda ingresar la serie de un equipo
 * ya instalado y seleccionar una interfaz.
 *
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 04-04-2019
 */
function setearEsquemaSdwanCambioEquipo(intIdServicio,intProducto)
{
    var connSdwanCambioEquipo = new Ext.data.Connection
        ({
            listeners:
                {
                    'beforerequest':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.show
                                    ({
                                        msg: 'Consultando característica SDWAN-CAMBIO EQUIPO.',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 200}
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

    var strMensaje = '¿Está seguro que desea habilitar el cambio de CPE con un equipo ya instalado?';

    connSdwanCambioEquipo.request({
        url: urlGetServicioProdCaract,
        method: 'post',
        params:
        {
            intIdServicio: intIdServicio,
            intIdProducto: intProducto
        },
        success: function(response)
        {
            var json = Ext.JSON.decode(response.responseText);

            if(json.strCaracteristica == 'N')
            {
                var pregunta = Ext.Msg.confirm('Alerta', strMensaje, function(btn)
                {
                    if(btn === 'yes')
                    {
                        connSdwanCambioEquipo.request(
                        {
                            url: urlSetearSdwan,
                            method: 'POST',
                            timeout: 60000,
                            params:
                                {
                                    idServicio: intIdServicio,
                                    idProducto: intProducto,
                                },
                            success: function(response)
                            {
                                var json = Ext.JSON.decode(response.responseText);
                                if (json.status == 'OK')
                                {
                                    var objAlertaExito = Ext.Msg.alert("Mensaje",json.mensaje);

                                    Ext.defer(function() {
                                        objAlertaExito.toFront();
                                    }, 50);

                                    store.load({params: {start: 0, limit: 10}});
                                }
                                else
                                {
                                    var objAlertaValidacion = Ext.Msg.alert("Alerta",json.mensaje);

                                    Ext.defer(function() {
                                        objAlertaValidacion.toFront();
                                    }, 50);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.MessageBox.hide();
                                Ext.Msg.alert('Error', result.responseText);
                            }
                        });
                    }
                });

                Ext.defer(function() {
                    pregunta.toFront();
                }, 50);
            }
            else if(json.strCaracteristica == 'E')
            {
                var strAlerta = Ext.Msg.alert("Alerta","Para ejecutar esta acción es necesario que el servicio principal se encuentre en otro "
                                                        + " equipo");

                Ext.defer(function() {
                    strAlerta.toFront();
                }, 50);
            }
            else if(json.strCaracteristica == 'T')
            {
                var strAlerta2 = Ext.Msg.alert("Alerta","Se verificaron inconsistencias en el servicio principal o backup,favor notificar a Sistemas "
                                                        + " antes de continuar con la ejecución");

                Ext.defer(function() {
                    strAlerta2.toFront();
                }, 50);
            }
            else if(json.strCaracteristica == 'C')
            {
                var strAlerta3 = Ext.Msg.alert("Alerta","La caracteristica ES_BACKUP no fue encontrada para este servicio, favor notificar a "
                                                        + " Sistemas");

                Ext.defer(function() {
                    strAlerta3.toFront();
                }, 50);
            }
            else
            {
                var strAlerta4 = Ext.Msg.alert("Alerta","El servicio ya tiene habilitada la opción para reutilizar un equipo ya instalado");

                Ext.defer(function() {
                    strAlerta4.toFront();
                }, 50);
            }
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

function seteaLiderCuadrilla(cuadrilla)
{
    connCargarLider.request({
        url: getLiderCuadrilla,
        method: 'post',
        params:
            {
                cuadrillaId: cuadrilla
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);

            Ext.getCmp('nombreLider').setValue(text.nombres);
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

function generarAlCambioEquipoSoporte(data,clienteEm){   

    var serie = consultarInformacion(data.idServicio, 'SERIE_EQUIPO_PTZ');
    var mac = consultarInformacion(data.idServicio, 'MAC');

    var agregarFormPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 2
                        },
                        items: [
                           
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                           
                           
                            {
                                xtype: 'textfield',
                                id: 'serieActual',
                                name: 'serieActual',
                                fieldLabel: 'Serie',
                                value: serie['valor'],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            
                            {
                                xtype: 'textfield',
                                id: 'macActual',
                                name: 'macActual',
                                fieldLabel: 'Mac',
                                value: mac['valor'],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            }
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información Equipo Nuevo',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 2
                        },
                        items: [
                            
                            {
                                xtype: 'textfield',
                                id: 'serieCamara',
                                name: 'serieCamara',
                                fieldLabel: 'Serie Cámara',
                                value: '',
                                width: '50%',
                                enableKeyEvents: true,
                                listeners:
                                {
                                    blur: function(serie){
                                    
                                            Ext.Ajax.request({
                                                url: url_buscarDatosNaf,
                                                dataType: 'text',
                                                method: 'post',
                                                params: {   
                                                    serieCpe:          serie.getValue(),
                                                    idProducto:     data.idProducto,
                                                    idPersonaRol: data.idPersonaEmpresaRol
                                                },
                                                success: function(response){                                                                                
                                                    var respuestajson     = JSON.parse(response.responseText);
                                                    var arrayjson     =  respuestajson["mensaje"].split(",");
                                                    var modelo        =  arrayjson[0];
                                                    var mac           =  arrayjson[3];
                                                    var status        =  respuestajson.status;

                                                    if(status=="OK")
                                                    {
                                                        Ext.getCmp('modeloCamara').value = modelo;
                                                        Ext.getCmp('modeloCamara').setRawValue(modelo);
                                                        Ext.getCmp('macCamara').value = mac;
                                                        Ext.getCmp('macCamara').setRawValue(mac);
                                                        Ext.Msg.alert('Mensaje ', 'Cámara encontrada');
                                                        Ext.getCmp('btnCrearCambioEquipo').setDisabled(false);

                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Mensaje ', respuestajson.mensaje);
                                                        Ext.getCmp('serieCamara').value = '';
                                                        Ext.getCmp('serieCamara').setRawValue('');
                                                        Ext.getCmp('macCamara').value = '';
                                                        Ext.getCmp('macCamara').setRawValue('');
                                                        Ext.getCmp('modeloCamara').value = '';
                                                        Ext.getCmp('modeloCamara').setRawValue('');
                                                        Ext.getCmp('btnCrearCambioEquipo').setDisabled(true);
                                                        
                  
                                                        
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                }
                                            });//Ext.Ajax.request
                                        
                                    },//blur
                                    keyup: function(form, e){
                                        Ext.getCmp('btnCrearCambioEquipo').setDisabled(true);
                                    }
                                }
                                
                            },
                           
                            {
                                xtype: 'textfield',
                                id: 'macCamara',
                                name: 'macCamara',
                                fieldLabel: 'Mac Cámara',
                                value: '',
                                width: '50%'
                            },
                            
                            {
                                xtype: 'textfield',
                                id: 'modeloCamara',
                                name: 'modeloCamara',
                                fieldLabel: 'Modelo Cámara',
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                value: '',
                                width: '50%'
                            }
                        ]
                    }
                ]
            }
        ],
        buttons: [{
            text: 'Crear',
            id: 'btnCrearCambioEquipo',
            disabled: true,
            handler: function() {
                var serieActual = Ext.getCmp('serieActual').value;
                var macActual = Ext.getCmp('macActual').value;
                var macCamara = Ext.getCmp('macCamara').value;
                var serieCamara = Ext.getCmp('serieCamara').value;
                var modeloCamara = Ext.getCmp('modeloCamara').value;
                
                if(serieCamara == '')
                {
                     Ext.Msg.alert("Alerta","Por favor Ingrese la serie");

                }else
                {
                    Ext.get(agregarFormPanel.getId()).mask('Creando...');

                    Ext.Ajax.request({
                        url: CambioLogicoEquipoSoporteBoton,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            intIdServicio        : data.idServicio,
                            intIdPersonaEmpresaRol: data.idPersonaEmpresaRol,
                            serieActual          : serieActual,
                            macActual            : macActual,
                            serieCamara          : serieCamara,
                            macCamara            : macCamara,
                            modeloCamara         : modeloCamara,
                            login                : data.login,
                            idServicioProdCaractMac : mac['idServicioProdCaract'],
                            estadoMac: mac['estado'],
                            descripcionMac: mac['descripcionCaracteristica'],
                            idServicioProdCaractSerie: serie['idServicioProdCaract'],
                            estadoSerie: serie['estado'],
                            descripcionMacSerie: serie['descripcionCaracteristica']

                        },
                        success: function(response) {
                            Ext.get(agregarFormPanel.getId()).unmask();
                            var objData    = Ext.JSON.decode(response.responseText);
                            var strStatus  = objData.strStatus;
                            var strMensaje = objData.strMensaje;
                            if (strStatus == "OK") {
                                win.destroy();
                                Ext.Msg.alert('Mensaje', 'Se generó Cambio Equipo Lógico al Servicio: ' + data.login, function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }
                            else {
                                Ext.Msg.alert('Mensaje ', 'Error:' + strMensaje);
                            }
                        },
                        failure: function(result)
                        {
                            Ext.get(agregarFormPanel.getId()).unmask();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });

                }
                

            }
        }, {
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }]	
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cambio de Equipo por Soporte',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();

}

/**
 * Funcion que sirve para generar una solicitud de cambio de elemento por soporte MD a clientes
 * con planes antiguos que presentan problemas con sus equipos obsoletos
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 07-05-2019
 * @param data
 * @param idAccion
 */
function generarSolCambioEquipoSoporte(data, idAccion)
{
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });
    
   

    var agregarFormPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'producto',
                                fieldLabel: 'Plan',
                                value: data.nombrePlan,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'ultimaMillaServicio',
                                fieldLabel: 'Última Milla',
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información actual del Equipo CPE ONT',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'nombreEquipo',
                                fieldLabel: 'Nombre elemento',
                                value: data.strNombreElementoHw,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'serieWifi',
                                fieldLabel: 'Serie Wifi',
                                value: data.strSerieEquipoHw,
                                readOnly: true,
                                width: 225,
                                
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'modeloWifi',
                                fieldLabel: 'Modelo Wifi',
                                value: data.strModeloEquipoHw,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'macWifi',
                                fieldLabel: 'Mac Wifi',
                                value: data.strMacEquipoHw,
                                readOnly: true,
                                width: 225
                            },
                            {width: 25, border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: [{
                id: 'btnCrearSolicitud',
                text: 'Crear Solicitud',
                formBind: true,
                handler: function() {
                    Ext.get(agregarFormPanel.getId()).mask('Creando...');
                    Ext.Ajax.request({
                        url: crearSolCambioEquipoSoporteBoton,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            intIdServicio        : data.idServicio
                        },
                        success: function(response) {
                            Ext.get(agregarFormPanel.getId()).unmask();
                            var objData    = Ext.JSON.decode(response.responseText);
                            var strStatus  = objData.strStatus;
                            var strMensaje = objData.strMensaje;
                            if (strStatus == "OK") {
                                win.destroy();
                                Ext.Msg.alert('Mensaje', 'Se generó la Solicitud Cambio Equipo por Soporte al Servicio: ' + data.login, function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }
                            else {
                                Ext.Msg.alert('Mensaje ', 'Error:' + strMensaje);
                            }
                        },
                        failure: function(result)
                        {
                            Ext.get(agregarFormPanel.getId()).unmask();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Crear Solicitud de Cambio de Equipo por Soporte',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();
    
}


/**
 * Funcion que sirve para cambiar el elemento wifi mediante solicitud por soporte
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 13-05-2019
 * @param data
 * @param idAccion
 */
function cambiarElementoPorSoporte(data, idAccion)
{
    var agregarFormPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'producto',
                                fieldLabel: 'Plan',
                                value: data.nombrePlan,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'ultimaMillaServicio',
                                fieldLabel: 'Última Milla',
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                    title: 'Información del Equipo CPE ONT',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'serieCpeOnt',
                                name: 'serieCpeOnt',
                                fieldLabel: 'Serie Cpe Ont',
                                displayField: "",
                                value: "",
                                width: 280,
                                listeners: {
                                    blur: function(inputText) {
                                        Ext.getCmp('btnCambiar').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: inputText.getValue(),
                                                modeloElemento: Ext.getCmp('modeloCpeOnt').getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function(response) 
                                            {
                                                Ext.getCmp('btnCambiar').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionCpeOnt').setValue = mensaje;
                                                    Ext.getCmp('descripcionCpeOnt').setRawValue(mensaje);
                                                    var arrayInformacionWifi       = mensaje.split(",");
                                                    Ext.getCmp('macCpeOnt').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macCpeOnt').setRawValue(arrayInformacionWifi[1]);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionCpeOnt').setValue = status;
                                                    Ext.getCmp('descripcionCpeOnt').setRawValue(status);
                                                    Ext.getCmp('macCpeOnt').setValue = "";
                                                    Ext.getCmp('macCpeOnt').setRawValue("");
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.getCmp('btnCambiar').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macCpeOnt').setValue = "";
                                                Ext.getCmp('macCpeOnt').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'modeloCpeOnt',
                                name: 'modeloCpeOnt',
                                fieldLabel: 'Modelo Cpe Ont',
                                displayField: "",
                                readOnly: true,
                                value: data.strModeloCpeOnt,
                                width: 225
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macCpeOnt',
                                name: 'macCpeOnt',
                                fieldLabel: 'Mac Cpe Ont',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionCpeOnt',
                                name: 'descripcionCpeOnt',
                                fieldLabel: 'Descripción Cpe Ont',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: 225
                            },
                            {width: 25, border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: [{
                id: 'btnCambiar',
                text: 'Cambiar',
                formBind: true,
                handler: function() {

                    var strSerieCpeOnt       = Ext.getCmp('serieCpeOnt').getValue();
                    var strModeloCpeOnt      = Ext.getCmp('modeloCpeOnt').getValue();
                    var strMacCpeOnt         = Ext.getCmp('macCpeOnt').getValue();
                    var strDescripcionCpeOnt = Ext.getCmp('descripcionCpeOnt').getValue();
                    var booleanValidacion  = true;
                    intBanderaErroflag     = 0;
                    
                    if(Ext.isEmpty(strMacCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    }
                    else if( strDescripcionCpeOnt == "ELEMENTO ESTADO INCORRECTO" || 
                             strDescripcionCpeOnt == "ELMENTO CON SALDO CERO"    || 
                             strDescripcionCpeOnt == "NO EXISTE ELEMENTO" )
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 3;
                    }
                    else if(Ext.isEmpty(strSerieCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }  
                    else if(Ext.isEmpty(strModeloCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 5;
                    }
                    if (booleanValidacion) 
                    {
                        Ext.get(agregarFormPanel.getId()).mask('Ejecutando...');
                        
                        Ext.Ajax.request({
                                            url: cambiarCpeBoton,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {
                                                idServicio:             data.idServicio,
                                                idElemento:             data.intIdElementoHw,
                                                modeloCpe:              data.strModeloCpeOnt,
                                                nombreCpe:              data.strNombreElementoHw,
                                                macCpe:                 strMacCpeOnt,
                                                serieCpe:               strSerieCpeOnt,
                                                descripcionCpe:         strDescripcionCpeOnt,
                                                tipoElementoCpe:        "CPE ONT",
                                                strEsCambioPorSoporte:  "SI",
                                                strEsCambioEquiSoporteMasivo:data.strEsCambioEquiSoporteMasivo
                            },
                            success: function(response) {
                                Ext.get(agregarFormPanel.getId()).unmask();
                                if(response.responseText == "OK"){
                                    Ext.Msg.alert('Mensaje','Se Cambió el Elemento del Cliente', function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                }
                                else if(response.responseText == "NO ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                }
                                else if(response.responseText == "MAX ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Límite de clientes por Puerto esta en el máximo, <br> Favor comunicarse con el departamento de GEPON' );
                                }
                                else if(response.responseText=="IP DEL EQUIPO INCORRECTA"){
                                    Ext.Msg.alert('Mensaje ', response.responseText + ', <BR> NO PODRA CONTINUAR CON EL CAMBIO DEL EQUIPO');
                                }
                                else if(response.responseText == "CANTIDAD CERO"){
                                    Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                }
                                else if(response.responseText == "NO EXISTE PRODUCTO"){
                                    Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                }
                                else if(response.responseText == "NO EXISTE CPE"){
                                    Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                }
                                else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                    Ext.Msg.alert('Mensaje ','CPE con estado incorrecto, favor revisar!' );
                                }
                                else if(response.responseText == "NAF"){
                                    Ext.Msg.alert('Mensaje ',response.responseText);
                                }
                                else{
                                    Ext.Msg.alert('Mensaje ',response.responseText );
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(agregarFormPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else 
                    {
                        if( intBanderaErroflag == 1 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la observación correspondiente!");
                        }
                        else if( intBanderaErroflag == 2 )
                        {
                            Ext.Msg.alert("Validación","No existe valor de Mac, favor revisar!");
                        }
                        else if( intBanderaErroflag == 3 )
                        {
                            Ext.Msg.alert("Validación","Datos del Wifi incorrectos, favor revisar!");
                        }
                        else if( intBanderaErroflag == 4 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                        }
                        else if( intBanderaErroflag == 5 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese el modelo correspondiente!");
                        }
                        else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
                    }
                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cambiar Equipo Por Soporte',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();
}


/**
 * cambioOntPorSolAgregarEquipo
 * 
 * Función que sirve para realizar un cambio de ont por medio de la solicitud de agregar equipo
* 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 15-04-2021
 * @param data
 */
function cambioEquipoOntPorSolAgregarEquipo(data)
{
    var storeModelosOnt = new Ext.data.Store({
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: strUrlGetModelosEquiposPorTecnologia,
            extraParams: {
                strTipoEquipos: data.strTipoOntNuevoPorSolAgregarEquipo,
                intIdServicio: data.idServicio
            },
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayRegistros'
            }
        },
        fields:
            [
                {name: 'strNombreModelo', mapping: 'strNombreModelo'}
            ]
    });
    
    var agregarEquiposPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'plan',
                                fieldLabel: 'Plan',
                                value: data.nombrePlan,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información del Equipo Ont',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'serieOnt',
                                name: 'serieOnt',
                                fieldLabel: 'Serie',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                queryMode: 'local',
                                xtype: 'combobox',
                                id: 'modeloOnt',
                                name: 'modeloOnt',
                                fieldLabel: 'Modelo',
                                displayField: 'strNombreModelo',
                                valueField: 'strNombreModelo',
                                loadingText: 'Buscando...',
                                store: storeModelosOnt,
                                width: 225,
                                listeners: {
                                    blur: function (combo) {
                                        Ext.getCmp('btnCambioEquipoOnt').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: Ext.getCmp('serieOnt').getValue(),
                                                modeloElemento: combo.getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function (response)
                                            {
                                                Ext.getCmp('btnCambioEquipoOnt').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionOnt').setValue = mensaje;
                                                    Ext.getCmp('descripcionOnt').setRawValue(mensaje);
                                                    var arrayInformacionWifi = mensaje.split(",");
                                                    Ext.getCmp('macOnt').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macOnt').setRawValue(arrayInformacionWifi[1]);
                                                } else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionOnt').setValue = status;
                                                    Ext.getCmp('descripcionOnt').setRawValue(status);
                                                    Ext.getCmp('macOnt').setValue = "";
                                                    Ext.getCmp('macOnt').setRawValue("");
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.getCmp('btnCambioEquipoOnt').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macOnt').setValue = "";
                                                Ext.getCmp('macOnt').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macOnt',
                                name: 'macOnt',
                                fieldLabel: 'Mac',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionOnt',
                                name: 'descripcionOnt',
                                fieldLabel: 'Descripción',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: 225
                            },
                            {width: 25, border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: [{
                id: 'btnCambioEquipoOnt',
                text: 'Cambiar',
                formBind: true,
                handler: function () {
                    var strSerieOnt = Ext.getCmp('serieOnt').getValue();
                    var strModeloOnt = Ext.getCmp('modeloOnt').getValue();
                    var strMacOnt = Ext.getCmp('macOnt').getValue();
                    var strDescripcionOnt = Ext.getCmp('descripcionOnt').getValue();
                    var booleanValidacion = true;
                    intBanderaErroflag = 0;
                    
                    if (Ext.isEmpty(strMacOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 1;
                    }
                    else if (strDescripcionOnt == "ELEMENTO ESTADO INCORRECTO" ||
                        strDescripcionOnt == "ELMENTO CON SALDO CERO" ||
                        strDescripcionOnt == "NO EXISTE ELEMENTO")
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    } 
                    else if (Ext.isEmpty(strSerieOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 3;
                    } 
                    else if (Ext.isEmpty(strModeloOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }
                    
                    if (booleanValidacion)
                    {
                        Ext.get(agregarEquiposPanel.getId()).mask('Cargando...');
                        Ext.Ajax.request({
                            url: strUrlCambioEquipoOntPorSolAgregarEquipo,
                            method: 'post',
                            timeout: 400000,  
                            params: {
                                intIdServicio: data.idServicio,
                                strSerieOnt: strSerieOnt,
                                strModeloOnt: strModeloOnt,
                                strMacOnt: strMacOnt,
                                strTipoOntNuevoPorSolAgregarEquipo: data.strTipoOntNuevoPorSolAgregarEquipo
                            },
                            success: function (response) {
                                Ext.get(agregarEquiposPanel.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje', "Se Cambio el Elemento del Cliente", function (btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                } else {
                                    Ext.Msg.alert('Mensaje ', 'Error:' + strMensaje);
                                }
                            },
                            failure: function (result)
                            {
                                Ext.get(agregarEquiposPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else
                    {
                        if (intBanderaErroflag == 1)
                        {
                            Ext.Msg.alert("Validación", "No existe valor de Mac, favor revisar!");
                        } 
                        else if (intBanderaErroflag == 2)
                        {
                            Ext.Msg.alert("Validación", "Datos incorrectos, favor revisar!");
                        }
                        else if (intBanderaErroflag == 3)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese la serie correspondiente!");
                        }
                        else if (intBanderaErroflag == 4)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese el modelo correspondiente!");
                        }
                        else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
                    }
                }
            }, 
            {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });
    
    storeModelosOnt.load();
    var win = Ext.create('Ext.window.Window', {
        title: "Cambio a Equipo "+ data.strTipoOntNuevoPorSolAgregarEquipo,
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarEquiposPanel]
    }).show();
}
