
var totalStorage = 0;
var ciudad       = '';
var idServicio   = 0;
var carpeta      = '';
var hyperview_nombre    = '';
var vcenter_nombre       = '';
var cluster_nombre      = '';
var storeStorage        = [];
var boolAvanza          = true;

function showFactibilidadHosting(data)
{
    var continuaFlujo            = data.get('continuaFlujoDC');    
    var boolContieneAlquilerServ = data.get('contieneAlquilerServidor')==='S';
    ciudad                       = data.get('ciudad');    
    idServicio                   = data.get('id_servicio');
    var arrayDsEscogido          = [];
    var arraySoEscogido          = [];
    var arrayDataEscogido        = [];
    var idRecursoSeleccionado    = '';
    var valorAnteriorDisco       = '';
    var valorAnteriorSo          = '';
    var valorAnteriorDs          = '';    
    var clickColumn              = 0;    
    
    if(boolContieneAlquilerServ && continuaFlujo === 'N')
    {        
        Ext.Msg.alert('Mensaje', 'No puede generar Factibilidad de Pool de Recursos ( Storage ) sin existir Factibilidad de Servidores Disponibles');
        return false;
    }
    else
    {
        winIngresoFactibilidad = "";
        formPanelInresoFactibilidad = "";

        if(!winIngresoFactibilidad)
        {
            //******** html campos requeridos...
            var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">\n\
                                               <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;* Campos requeridos</p>';
            CamposRequeridos = Ext.create('Ext.Component', {
                html: iniHtmlCamposRequeridos,
                padding: 1,
                layout: 'anchor',
                hidden:boolContieneAlquilerServ,
                style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
            });

            htmlDivisor = Ext.create('Ext.Component', {
                html: '<div class="secHead"><label style="text-align:left;">\n\
                       <b><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;Recursos Contratados</b></label></div>',
                padding: 1,
                layout: 'anchor'
            });
            
            var contentHtmlIT = Ext.create('Ext.Component', {
                html: '<div id="content-recursos">'+
                            '<table style="width:30%;left:10%;">'+
                              '<tr>'+
                                    '<td align="left"><label><b>Storage:</b></label></td>'+
                                    '<td>&nbsp;</td>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-storage" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-storage-label" class="progress-label"></div>'+
                                        '</div>'+
                                    '</td>'+
                              '</tr>'+
                            '</table>'+
                         '</div>',
                hidden:!boolContieneAlquilerServ,
                style: {marginBottom: '15px', border: '0'}
            });
            
            var storeMaquinaVirtual = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: urlGetInformacionGeneralHosting,
                    params: 
                    { 
                        idServicio      : idServicio,
                        tipoInformacion : 'MAQUINAS-VIRTUALES', 
                        esCombo         : 'S'
                    },
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        idServicio      : idServicio,
                        tipoInformacion : 'MAQUINAS-VIRTUALES',
                        esCombo         : 'S'
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'},
                        {name: 'HYPERVIEW', mapping: 'HYPERVIEW'},
                        {name: 'valorCaracteristica', mapping: 'valorCaracteristica'},
                        {name: 'idRecurso', mapping: 'idRecurso'}
                    ]
            });
            
            var storeHyperView = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                async: false,
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: urlGetDatosFactibilidadHosting,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        tipoDato : 'HYPERVIEW',
                        ciudad   : data.get('ciudad').toUpperCase(),
                        idVcenter:''
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });
            
            var storeVCenter = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                async: false,
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: urlGetDatosFactibilidadHosting,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        tipoDato : 'VCENTER',
                        ciudad   : data.get('ciudad').toUpperCase(),
                        idVcenter:''
                    },
                    params: {
                        tipoDato : 'VCENTER',
                        ciudad   : data.get('ciudad').toUpperCase(),
                        idVcenter:''
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });

            var storeCluster = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: urlGetDatosFactibilidadHosting,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        tipoDato: 'CLUSTER',
                        ciudad  : data.get('ciudad').toUpperCase()
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });                        
            
            var storeStoragePoolCompleto = new Ext.data.Store({
                pageSize: 14,
                total: 'total',
                autoLoad: !boolContieneAlquilerServ,
                proxy: {
                    type: 'ajax',
                    url: urlGetDatosFactibilidadHosting,
                    reader: 
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    actionMethods: 
                    {
                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                    },
                    extraParams: 
                    {
                        tipoDato  : 'DATASTORE_DISCO',
                        ciudad    : '',
                        idVcenter : '',
                        idServicio: idServicio,
                        maquinaVirtual: ''
                    }
                },
                fields:
                    [
                        {name: 'idRecurso',     mapping: 'idRecurso'},
                        {name: 'nombreRecurso', mapping: 'nombreRecurso'},
                        {name: 'valor',         mapping: 'valor'},
                        {name: 'datastore',     mapping: 'datastore'}
                    ]
            });
            
            Ext.define('alquilerServidoresModel', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idElemento',   type: 'integer'},
                    {name: 'idRecurso',    type: 'integer'},
                    {name: 'idServicio',   type: 'integer'},
                    {name: 'modelo',       type: 'string'},
                    {name: 'nombreElemento',type: 'string'}, 
                    {name: 'licenciamiento',type: 'string'},
                    {name: 'storage',   type: 'string'},
                    {name: 'valor',        type: 'string'},
                    {name: 'dataStore',    type: 'string'}
                ]
            });    
            
            //Declara stores para flujo de pool bajo servicio de alquiler de servidores
            
            var storeLicenciamiento = [];
            var storeAlquilerServidores = Ext.create('Ext.data.Store', {
                pageSize: 5,
                autoDestroy: true,
                model: 'alquilerServidoresModel',
                proxy: {
                    type: 'memory'
                }
            });
            
            var rowEditingRecursosMV = Ext.create('Ext.grid.plugin.RowEditing', {
                saveBtnText: '<label style="color:blue;"><i class="fa fa-check-square"></i></label>',
                cancelBtnText: '<i class="fa fa-eraser"></i>',
                clicksToMoveEditor: 1,
                autoCancel: false
            });
            
            var rowEditingRecursos = Ext.create('Ext.grid.plugin.RowEditing', {
                saveBtnText: '<label style="color:blue;"><i class="fa fa-check-square"></i></label>',
                cancelBtnText: '<i class="fa fa-eraser"></i>',
                clicksToMoveEditor: 1,
                autoCancel: false,
                listeners: {      
                    canceledit: function(editor,e)
                    {
                        if(clickColumn === 'storage' )
                        {
                            var valor = '';
                            
                            if(storeStorage.findRecord("idRecurso", e.record.data.storage))                                
                                valor = storeStorage.findRecord("idRecurso", e.record.data.storage).get('valor');
                            if(storeStorage.findRecord("nombreRecurso", e.record.data.storage))                                
                                valor = storeStorage.findRecord("nombreRecurso", e.record.data.storage).get('valor');
                            
                            actualizarValorStorage(parseInt(valor),"+");
                            
                            e.record.set('valor','');
                            e.record.set('storage','');
                            
                            arrayDsEscogido = arrayDsEscogido.filter(function(elem)
                            {
                                return elem !== idRecursoSeleccionado;
                            });
                        }
                        else if(clickColumn === 'licenciamiento')
                        {
                            arraySoEscogido = arraySoEscogido.filter(function(elem)
                            {
                                return elem !== idRecursoSeleccionado;
                            });
                            e.record.set('licenciamiento','');
                        }
                        else if(clickColumn === 'dataStore')
                        {
                            arrayDataEscogido = arrayDataEscogido.filter(function(elem)
                            {
                                return elem !== e.record.data.dataStore;
                            });
                            e.record.set('dataStore','');
                        }
                    },
                    beforeedit: function(editor, e)
                    {                        
                        //Setear el valor anterior previo a la edicion para poder manipular el registro de acuerdo al evento requerido
                        var value   = '';
                        var storage = null;

                        if(clickColumn === 'storage')
                        {
                            value   = e.record.data.storage;
                            storage = storeStorage;
                            valorAnteriorDisco = value;
                        }
                        else if(clickColumn === 'licenciamiento')
                        {
                            value   = e.record.data.licenciamiento;                                
                            storage = storeLicenciamiento;
                            valorAnteriorSo = value;
                        }            
                        else if(clickColumn === 'dataStore')
                        {
                            valorAnteriorDs = e.record.data.dataStore;
                        }

                         //Mostrar siempre el nombre del registro al editar la fila
                        if(clickColumn === 'storage' || clickColumn === 'licenciamiento')
                        {                            
                            if(storage.findRecord("nombreRecurso", value))         
                            {
                                idRecursoSeleccionado = storage.findRecord("nombreRecurso", value).get('idRecurso');
                            }
                            
                            e.record.set(clickColumn,value);
                        }
                    },
                    afteredit: function(editor, e, eOpts) 
                    {
                        var value='';
                        var store=[];
                        
                        switch(clickColumn)
                        {
                            case 'storage':
                                //value = e.record.data.storage;
                                store = storeStorage;
                                break;
                            case 'licenciamiento':
                                value = e.record.data.licenciamiento;
                                store = storeLicenciamiento;
                                break;
                            case 'dataStore':
                                value = e.record.data.dataStore;
                                break;
                        }
                        
                        //Se realiza busqueda en store las columnas que manejen combo  de busqueda
                        if(clickColumn!=='dataStore' && store.findRecord("nombreRecurso", value))
                        {
                            value = store.findRecord("nombreRecurso", value).get('idRecurso');
                        }
                        
                        if(!Ext.isEmpty(value) && clickColumn==='storage')
                        {
                            //Validar si no existe registros repetidos que esten siendo seleccionados para asignacion                        
                                arrayDsEscogido.push(value);

                                e.record.set('valor',store.findRecord("idRecurso", value).get('valor'));
                                //Si existe un valor anterior y es cambiado por uno nuevo, eliminar del array referencia el anterior
                                //y configurar el nuevo
                                if(!Ext.isEmpty(valorAnteriorDisco))
                                {
                                    valorGb = store.findRecord("nombreRecurso", valorAnteriorDisco).get('valor');
                                
                                    actualizarValorStorage(parseInt(valorGb),"+");
                                    
                                    arrayDsEscogido = arrayDsEscogido.filter(function(elem)
                                    {
                                        return elem !== valorAnteriorDisco;
                                    });
                                }
                                else
                                {
                                    var valorGb = store.findRecord("idRecurso", value).get('valor');
                                }
                                
                                actualizarValorStorage(parseInt(valorGb),"-");
                        }
                        
                        if(!Ext.isEmpty(value) && clickColumn==='licenciamiento')
                        {                         
                                arraySoEscogido.push(value);
                                
                                //Si existe un valor anterior y es cambiado por uno nuevo, eliminar del array referencia el anterior
                                //y configurar el nuevo
                                if(!Ext.isEmpty(valorAnteriorSo))
                                {
                                    arraySoEscogido = arraySoEscogido.filter(function(elem)
                                    {
                                        return elem !== valorAnteriorSo;
                                    });
                                }
                        }
                        
                        if(!Ext.isEmpty(value) && clickColumn==='dataStore')
                        {                                              
                                arrayDataEscogido.push(value);
                                
                                if(!Ext.isEmpty(valorAnteriorDs))
                                {                                    
                                    arrayDataEscogido = arrayDataEscogido.filter(function(elem)
                                    {
                                        return elem !== valorAnteriorDs;
                                    });
                                }
                        }
                    }
                }
            });
                        
            //Grid para Factibilidad Alquiler Servidores
            var gridAlquilerServidores = Ext.create('Ext.grid.Panel', {
                width: 995,
                title:'Datos de Factibilidad para Servidores',
                id:'gridAlquilerServidores',
                height: 180,
                plugins:[rowEditingRecursos],
                store: storeAlquilerServidores,
                loadMask: true,
                frame: false,
                listeners: {
                    cellclick: function(grid, td, cellIndex) 
                    {                       
                       if(cellIndex === 5)//SO
                       {
                           clickColumn = 'licenciamiento';
                           Ext.getCmp("cmbStorage").setDisabled(true);
                           Ext.getCmp("txtDatastore").setDisabled(true);
                           Ext.getCmp("cmbLicenciamiento").setDisabled(false);
                           Ext.getCmp("txtCapacidad").setDisabled(true);
                       }
                       else if(cellIndex === 6)//DISCO
                       {
                           clickColumn = 'storage';
                           Ext.getCmp("cmbStorage").setDisabled(false);
                           Ext.getCmp("cmbLicenciamiento").setDisabled(true);
                           Ext.getCmp("txtDatastore").setDisabled(true);
                           Ext.getCmp("txtCapacidad").setDisabled(false);
                       }
                       else if(cellIndex === 7)//CAPACIDAD DISCO
                       {
                           clickColumn = 'valor';
                           Ext.getCmp("txtDatastore").setDisabled(true);
                           Ext.getCmp("cmbLicenciamiento").setDisabled(true);
                           Ext.getCmp("cmbStorage").setDisabled(true);
                           Ext.getCmp("txtCapacidad").setDisabled(false);
                       }
                       else if(cellIndex === 8)//DS
                       {
                           clickColumn = 'dataStore';
                           Ext.getCmp("txtDatastore").setDisabled(false);
                           Ext.getCmp("cmbLicenciamiento").setDisabled(true);
                           Ext.getCmp("cmbStorage").setDisabled(true);
                           Ext.getCmp("txtCapacidad").setDisabled(false);
                       }
                    }
                },
                columns: [
                    {
                        id: 'idServicio',
                        header: 'idServicio',
                        dataIndex: 'idServicio',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'idElemento',
                        header: 'idElemento',
                        dataIndex: 'idElemento',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'idRecurso',
                        header: 'idRecurso',
                        dataIndex: 'idRecurso',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'nombreElemento',
                        header: 'Servidor',
                        dataIndex: 'nombreElemento',
                        width: 80,
                        sortable: true
                    },
                    {
                        id: 'modelo',
                        header: 'Modelo',
                        dataIndex: 'modelo',
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'licenciamiento',
                        header: 'Licenciamiento SO',
                        dataIndex: 'licenciamiento',
                        width: 270,
                        editor: new Ext.form.field.ComboBox({
                            typeAhead: true,
                            id: 'cmbLicenciamiento',
                            name: 'cmbLicenciamiento',
                            valueField: 'idRecurso',
                            displayField: 'nombreRecurso',
                            editable: false
                        }),
                        renderer: function(value)
                            {
                                if(!Ext.isEmpty(value))
                                {
                                    if(storeLicenciamiento.findRecord("idRecurso", value))
                                    return storeLicenciamiento.findRecord("idRecurso", value).get('nombreRecurso');
                                
                                    if(storeLicenciamiento.findRecord("nombreRecurso", value))
                                    return value;
                                }
                                else
                                {
                                    return value;
                                }
                            }
                    },
                    {
                        id: 'storage',
                        header: 'Storage',
                        dataIndex: 'storage',
                        width: 240,
                        editor: new Ext.form.field.ComboBox({
                            typeAhead: true,
                            id: 'cmbStorage',
                            name: 'cmbStorage',
                            valueField: 'idRecurso',
                            displayField: 'nombreRecurso',
                            editable: false
                        }),                        
                        renderer: function(value)
                            {
                                if(!Ext.isEmpty(value))
                                {
                                    if(storeStorage.findRecord("idRecurso", value))
                                    return storeStorage.findRecord("idRecurso", value).get('nombreRecurso');
                                
                                    if(storeStorage.findRecord("nombreRecurso", value))
                                    return value;
                                }
                                else
                                {
                                    return value;
                                }
                            }
                    },
                    {
                        id: 'valor',
                        header: 'Cantidad Disco',
                        dataIndex: 'valor',
                        width: 100,
                        align:'center',
                        editor:new Ext.form.TextField({
                            typeAhead: true,
                            id: 'txtCapacidad',
                            name: 'txtCapacidad'
                        })
                    },
                    {
                        id: 'dataStore',
                        header: 'DataStore',
                        dataIndex: 'dataStore',
                        width: 150,
                        editor:new Ext.form.TextField({
                            typeAhead: true,
                            id: 'txtDatastore',
                            name: 'txtDatastore',
                            emptyText:'Ingrese Datastore'
                        })
                    }
                ]
            });
                        
            var gridFactStoragePoolCompleto = Ext.create('Ext.grid.Panel', {
                width: 472,
                height: 160,
                id:'gridFactStoragePoolCompleto',
                title:'Discos contratados por el Cliente',
                store: storeStoragePoolCompleto,
                plugins: [rowEditingRecursosMV],
                loadMask: true,
                frame: false,
                align:'center',
                columns: [
                    {
                        id: 'idRecurso',
                        header: 'idRecurso',
                        dataIndex: 'idRecurso',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'nombreRecurso',
                        header: '<b>Disco</b>',
                        dataIndex: 'nombreRecurso',
                        width: 270,
                        sortable: true
                    },
                    {
                        id: 'valor',
                        header: '<b>Capacidad</b>',
                        dataIndex: 'valor',
                        width: 70,
                        sortable: true,
                        renderer: function(value, meta, record) 
                        {
                            return value+" GB ";
                        }
                    },                    
                    {
                        id: 'datastore',
                        header: '<b>Datastore</b>',
                        dataIndex: 'datastore',
                        width: 130,
                        align:'center',
                        sortable: true,
                        editor:new Ext.form.TextField({
                            typeAhead: true,
                            id: 'txtDatastore',
                            name: 'txtDatastore',
                        })
                    }
                ]
            });
            
            var formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 10,
                bodyStyle: "background: white; padding: 5px; border: 0px none;",
                frame: true,
                items: [
                    CamposRequeridos,
                    //Resumen del cliente
                    {
                        xtype: 'fieldset',
                        title: '<b>Resumen</b>',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 5,
                            pack: 'center'
                        },
                        items: [
                            { width: '10%', border: false},
                            //Datos del Cliente
                            {
                                xtype: 'fieldset',
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Cliente</b>',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 5px;",
                                layout: 'anchor',
                                defaults: {
                                    width: boolContieneAlquilerServ?'510px':'350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Cliente',
                                        name: 'info_cliente',
                                        id: 'info_cliente',
                                        value: data.get("cliente"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Login',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: data.get("login2"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Ciudad',
                                        name: 'info_ciudad',
                                        id: 'info_ciudad',
                                        value: data.get("ciudad"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                            },
                            { width: '10%', border: false},
                            //Datos del Servicio
                            {
                                xtype: 'fieldset',
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Servicio</b>',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 5px;",
                                defaults: {
                                    width:boolContieneAlquilerServ? '510px;':'400px;'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<b>Servicio</b>',
                                        name: 'info_servicio',
                                        id: 'info_servicio',
                                        value: data.get("producto"),
                                        fieldStyle:'font-weight:bold;color:green;',
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    htmlDivisor,
                                    contentHtmlIT,
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<b>Storage Total</b>',
                                        value: data.get("poolStorage"),
                                        allowBlank: false,
                                        readOnly: true,
                                        fieldStyle:'font-weight:bold;color:green;',
                                        hidden:boolContieneAlquilerServ
                                    }
                                ]
                            },
                            { width: '10%', border: false}
                        ]
                    },
                    //Factibilidad de storage y virtualizadores para pool de recursos completos
                    {
                        xtype: 'fieldset',
                        title: '<b>Factibilidad Pool Recursos Completos ( Máquinas Virtuales )</b>',
                        hidden: boolContieneAlquilerServ,
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 4,
                            pack: 'center'
                        },
                        
                        items: [
                                {
                                   xtype: 'fieldset',
                                   title:  '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Máquinas Virtuales</b>',
                                   style: "font-weight:bold; margin-bottom: 5px;",
                                   hidden: boolContieneAlquilerServ,
                                   width: '500px',
                                   colspan: 4,
                                   layout: {
                                       type: 'table',
                                       columns: 4,
                                       pack: 'center'
                                   },
                                   items: [
                                       {
                                            xtype: 'combobox',
                                            hidden:boolContieneAlquilerServ,
                                            name: 'cmbMaquinaVirtual',
                                            id: 'cmbMaquinaVirtual',
                                            fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* Maquina Virtual</b>',
                                            displayField: 'nombreElemento',
                                            valueField: 'idElemento',
                                            //Lleveme al combo
                                            store: storeMaquinaVirtual,
                                            width: 300,
                                            editable:false,
                                            style: "font-weight:bold; margin-bottom: 5px;",
                                            layout: 'anchor',
                                            listeners: {
                                                select: function(combo, rec, idx, data) 
                                                {
                                                    var totalRecurso          = 0;
                                                    var boolDetalle    = false;
                                                    Ext.getCmp('txt_Disco').setValue('');
                                                    Ext.getCmp('txt_Memoria').setValue('');
                                                    Ext.getCmp('txt_Procesador').setValue('');
                                                    Ext.getCmp('txt_SistemaOperativo').setValue('');
                                                    //Sumamos el total usado por cada recurso
                                                    for (var j= 0; j<rec[0].raw.arrayDetalleDisco.length ; j++)
                                                    {         
                                                            boolDetalle = typeof rec[0].raw.arrayDetalleDisco[j].usado === 'undefined' ? false : true;
                                                            totalRecurso=totalRecurso+rec[0].raw.arrayDetalleDisco[j].usado;
                                                            if (boolDetalle)
                                                            {
                                                                Ext.getCmp('txt_Disco').setValue(totalRecurso + ' GB');

                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('txt_Disco').setValue('Sin información');
                                                                break;
                                                            }
                                                    }
                                                    totalRecurso=0;
                                                    for (var k = 0; k<rec[0].raw.arrayDetalleMemoria.length ; k++)
                                                    {         
                                                            boolDetalle = typeof rec[0].raw.arrayDetalleMemoria[k].usado === 'undefined' ? false : true;
                                                            totalRecurso=totalRecurso+rec[0].raw.arrayDetalleMemoria[k].usado;
                                                            if (boolDetalle)
                                                            {
                                                                Ext.getCmp('txt_Memoria').setValue(totalRecurso + ' GB');

                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('txt_Memoria').setValue('Sin información');
                                                                break;
                                                            }
                                                    }
                                                    totalRecurso=0;
                                                    for (var l = 0; l<rec[0].raw.arrayDetalleProcesador.length ; l++)
                                                    {         
                                                            boolDetalle = typeof rec[0].raw.arrayDetalleProcesador[l].usado === 'undefined' ? false : true;
                                                            totalRecurso=totalRecurso+rec[0].raw.arrayDetalleProcesador[l].usado;
                                                            if (boolDetalle)
                                                            {
                                                                Ext.getCmp('txt_Procesador').setValue(totalRecurso + ' Core');

                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('txt_Procesador').setValue('Sin información');
                                                                break;
                                                            }
                                                    }
                                                    
                                                    for (var h = 0; h<rec[0].raw.arrayDetalleLicencia.length ; h++)
                                                    {         
                                                            var arrayNombreLic          =   rec[0].raw.arrayDetalleLicencia[h].nombreRecurso.split("@", 2);
                                                            if( arrayNombreLic[0] == 'SISTEMA OPERATIVO')
                                                            {
                                                                Ext.getCmp('txt_SistemaOperativo').setValue(arrayNombreLic[1]);
                                                            }
                                                    }

                                                    if (rec[0].raw.HYPERVIEW !==null)
                                                    {
                                                        storeHyperView.removeAll();
                                                        storeHyperView.load(); 
                                                        Ext.getCmp('cmbHyperView').setValue(parseInt(rec[0].raw.HYPERVIEW , 10));
                                                        Ext.getCmp('cmbHyperView').setRawValue(rec[0].raw.NOMBRE_HYPERVIEW);
                                                        Ext.getCmp('cmbVcenter').setDisabled(false);
                                                        storeVCenter.proxy.extraParams = {idVcenter: parseInt(rec[0].raw.HYPERVIEW, 10),
                                                                                  tipoDato : 'VCENTER',
                                                                                  ciudad   : ciudad.toUpperCase()
                                                                                 };
                                                        storeVCenter.load({params: {}});
                                                        Ext.getCmp('cmbVcenter').setRawValue(rec[0].raw.NOMBRE_VCENTER);
                                                        Ext.getCmp('cmbVcenter').setValue(parseInt(rec[0].raw.VCENTER , 10));
                                                        Ext.getCmp('cmbCluster').setDisabled(false); 
                                                        storeCluster.proxy.extraParams = {idVcenter: parseInt(rec[0].raw.VCENTER, 10),
                                                                                  tipoDato : 'CLUSTER',
                                                                                  ciudad   : ciudad.toUpperCase()
                                                                                 };
                                                        storeCluster.load();
                                                        Ext.getCmp('cmbCluster').setRawValue(rec[0].raw.NOMBRE_CLUSTER);
                                                        Ext.getCmp('cmbCluster').setValue(parseInt(rec[0].raw.CLUSTER , 10));

                                                    }else
                                                    {
                                                        Ext.getCmp('cmbHyperView').setValue(null);
                                                        Ext.getCmp('cmbVcenter').setValue(null);
                                                        Ext.getCmp('cmbCluster').setValue(null);
                                                    }
                                                    storeStoragePoolCompleto.proxy.extraParams = {
                                                                                tipoDato  : 'DATASTORE_DISCO',
                                                                                ciudad    : '',
                                                                                idVcenter : '',
                                                                                idServicio: idServicio,
                                                                                maquinaVirtual: parseInt(rec[0].raw.idElemento),
                                                                                 };
                                                    storeStoragePoolCompleto.load();
                                                    formPanelIngresoFactibilidad.refresh;

                                                }
                                            }
                                       },                       
                                       

                                    {
                                            xtype: 'textfield',
                                            fieldLabel: 'Total Disco',
                                            style: "font-weight:bold; margin-left: 10px; margin-right: 0px;",
                                            layout: 'fit',
                                            name: 'txt_Disco',
                                            id: 'txt_Disco',
                                            value: data.get(""),
                                            allowBlank: true,
                                            readOnly: true,
                                            width: 160
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Total Memoria',
                                            style: "font-weight:bold; margin-left: 10px; margin-right: 0px;",
                                            layout: 'fit',
                                            name: 'txt_Memoria',
                                            id: 'txt_Memoria',
                                            value: data.get(""),
                                            allowBlank: true,
                                            readOnly: true,
                                            width: 160
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Total Procesador',
                                            style: "font-weight:bold; margin-left: 10px; margin-right: 0px;",
                                            layout: 'fit',
                                            name: 'txt_Procesador',
                                            id: 'txt_Procesador',
                                            value: data.get(""),
                                            allowBlank: false,
                                            readOnly: true,
                                            width: 160
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Sistema Operativo',
                                            style: "font-weight:bold;",
                                            name: 'txt_SistemaOperativo',
                                            id: 'txt_SistemaOperativo',
                                            colspan: 2,
                                            value: data.get(""),
                                            allowBlank: true,
                                            readOnly: true,
                                            width: 400
                                        },


                                   ]
                            },
                            {
                                xtype: 'fieldset',
                                hidden:boolContieneAlquilerServ,
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Factibilidad Virtualizadores</b>',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 5px;",
                                layout: {
                                    tdAttrs: {style: 'padding: 5px;'},
                                    type: 'table',
                                    //columns: 3,
                                    pack: 'center'
                                },
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        style: "border:0",
                                        items: [
                                            {
                                                xtype: 'combobox',
                                                name: 'cmbHyperView',
                                                id: 'cmbHyperView',
                                                fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* Tecnología</b>',
                                                displayField: 'nombreElemento',
                                                valueField: 'idElemento',
                                                store: storeHyperView,
                                                width: 300,
                                                editable:false,
                                                listeners: {
                                                    select: function(combo) 
                                                    { 
                                                        Ext.getCmp('cmbVcenter').setValue("");
                                                        Ext.getCmp('cmbVcenter').setRawValue("");
                                                        Ext.getCmp('cmbCluster').setValue("");
                                                        Ext.getCmp('cmbCluster').setRawValue("");
                                                        storeVCenter.proxy.extraParams = {idVcenter: combo.getValue(),
                                                                                          tipoDato :'VCENTER',
                                                                                          ciudad   : data.get('ciudad').toUpperCase()
                                                                                         };
                                                        storeVCenter.load({params: {}});
                                                        Ext.getCmp('cmbVcenter').setDisabled(false);         
                                                        Ext.getCmp('cmbCluster').setDisabled(true);
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                name: 'cmbVcenter',
                                                id: 'cmbVcenter',
                                                fieldLabel: '<i class="fa fa-connectdevelop" aria-hidden="true"></i>&nbsp;<b>* Ambiente</b>',
                                                displayField: 'nombreElemento',
                                                valueField: 'idElemento',
                                                store: storeVCenter,
                                                width: 300,
                                                disabled:true,
                                                editable:false,
                                                listeners: {
                                                    select: function(combo) 
                                                    { 
                                                        storeCluster.proxy.extraParams = {idVcenter: combo.getValue(),
                                                                                          tipoDato :'CLUSTER',
                                                                                          ciudad   : data.get('ciudad').toUpperCase()
                                                                                         };
                                                        storeCluster.load({params: {}});
                                                        Ext.getCmp('cmbCluster').setDisabled(false);
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'cmbCluster',
                                                name: 'cmbCluster',
                                                fieldLabel: '<i class="fa fa-stack-exchange" aria-hidden="true"></i>&nbsp;&nbsp;<b>* Cluster</b>',
                                                displayField: 'nombreElemento',
                                                valueField: 'idElemento',
                                                store: storeCluster,
                                                width: 300,
                                                editable:false,
                                                disabled:true
                                            }
                                        ]
                                    }
                                ]
                            },
                                gridFactStoragePoolCompleto,
                                { width: '10%', border: false},
                                { width: '10%', border: false},
                                { width: '10%', border: false},
                                {
                                xtype: 'button',
                                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Agregar',
                                align:'left',
                                pack:'left',
                                width: 100,
                                handler: function() 
                                {
                                    //si es flujo normal de pool de recursos entra a este bloque de accion
                                    if(!boolContieneAlquilerServ)
                                    {
                                        var hyperview       = Ext.getCmp("cmbHyperView").getValue();
                                        var vcenter         = Ext.getCmp("cmbVcenter").getValue();
                                        var cluster         = Ext.getCmp("cmbCluster").getValue();
                                        var maquinaVirtual  = Ext.getCmp("cmbMaquinaVirtual").getValue();
                                        //Obtener resumen de datastore por disco
                                        var gridRecursos = Ext.getCmp('gridFactStoragePoolCompleto');
                                        var cont         = 0; 
                                        var arrayDs   = [];

                                        if(Ext.isEmpty(hyperview))
                                        {
                                            Ext.Msg.alert('Mensaje', 'Por favor ingrese la información del Hypervisor para continuar');
                                            return false;
                                        }

                                        if(Ext.isEmpty(vcenter))
                                        {
                                            Ext.Msg.alert('Mensaje', 'Por favor ingrese la información del VCenter para continuar');
                                            return false;
                                        }

                                        if(Ext.isEmpty(cluster))
                                        {
                                            Ext.Msg.alert('Mensaje', 'Por favor ingrese la información del Cluster para continuar');
                                            return false;
                                        }
                                        
                                        for (var b = 0; b < gridRecursos.getStore().getCount(); b++)
                                        {                                    
                                            if(Ext.isEmpty(gridRecursos.getStore().getAt(b).data.datastore))
                                            {
                                                Ext.Msg.alert('Mensaje', 'Por favor de llenar todos los campos de \n' +
                                                                          'datastore para las capacidades ingresadas');
                                                cont++;
                                                return false;
                                            }
                                        }

                                        if(cont===0)
                                        {
                                            for (var d = 0; d < gridRecursos.getStore().getCount(); d++)
                                            {
                                                var json           = {};
                                                json['idRecurso']  = gridRecursos.getStore().getAt(d).data.idRecurso;
                                                json['datastore']  = gridRecursos.getStore().getAt(d).data.datastore;
                                                json['valor']      = gridRecursos.getStore().getAt(d).data.valor;
                                                arrayDs.push(json);
                                            }
                                        }
                                        
                                        $.ajax
                                        ({
                                            type: "POST",
                                            url: urlGuardarDatosFactibilidadHosting,
                                            data:
                                                {
                                                    'idServicio'        : data.get('id_servicio'),
                                                    'vcenter'           : vcenter,
                                                    'cluster'           : cluster,
                                                    'hyperview'         : hyperview,
                                                    'datastore'         : Ext.JSON.encode(arrayDs),
                                                    'maquinaVirtual'    : maquinaVirtual
                                                },
                                            beforeSend: function()
                                            {
                                                Ext.get(winIngresoFactibilidad.getId()).mask('Guardando datos de Factibilidad...');
                                            },
                                            complete: function()
                                            {
                                                Ext.get(winIngresoFactibilidad.getId()).unmask();
                                            },
                                            success: function(data)
                                            {
                                                Ext.Msg.show({
                                                        title: 'Mensaje',
                                                        msg: data.mensaje,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.INFO
                                                    });
                                                    storeMaquinaVirtual.removeAll();
                                                    storeMaquinaVirtual.load();
                                            }
                                        }); 
                                
                                    }
                                }
                            },
                                { width: '10%', border: false}
                            
                        ]
                    },
                    //Factibilidad de storage para alquiler de servidores
                    {
                        xtype: 'fieldset',
                        hidden: !boolContieneAlquilerServ,
                        title: '<b>Factibilidad Pool Recursos - Alquiler Servidores</b>',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 1,
                            pack: 'center'
                        },
                        items: [   
                            {
                                xtype: 'fieldset',
                                hidden:!boolContieneAlquilerServ,
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Factibilidad ( Storage )</b>',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 5px;",
                                layout: {
                                    tdAttrs: {style: 'padding: 5px;'},
                                    type: 'table',
                                    columns: 3,
                                    pack: 'center'
                                },
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        style: "border:0",
                                            layout: {
                                            tdAttrs: {style: 'padding: 5px;'},
                                            type: 'table',
                                            columns: 1,
                                            pack: 'center'
                                        },
                                        items: 
                                        [
                                            gridAlquilerServidores                                    
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ],                
                buttons: [
                    {
                        text: 'Guardar',
                        handler: function() 
                        {
                            //si es flujo normal de pool de recursos entra a este bloque de accion
                            if(!boolContieneAlquilerServ)
                            {
                                var arrayDs   = [];
                                var cont         = 0; 
                                
                                for (var c = 0; c < Ext.getCmp('cmbMaquinaVirtual').getStore().getCount(); c++)
                                {   
                                    if(Ext.isEmpty(Ext.getCmp('cmbMaquinaVirtual').getStore().getAt(c).data.HYPERVIEW))
                                    {
                                        Ext.Msg.alert('Mensaje', 'Favor asignar factibilidad a todas \n' +
                                                                  'las máquinas virtuales');
                                        cont++;
                                        return false;
                                    }
                                }
                                
                                $.ajax({
                                    type: "POST",
                                    url: urlFinalizarFactibilidadHostingMV,
                                    data:
                                        {
                                            'idServicio': data.get('id_servicio'),
                                            'datastore' : Ext.JSON.encode(arrayDs)
                                        },
                                    beforeSend: function()
                                    {
                                        Ext.get(winIngresoFactibilidad.getId()).mask('Guardando datos de Factibilidad...');
                                    },
                                    complete: function()
                                    {
                                        Ext.get(winIngresoFactibilidad.getId()).unmask();
                                    },
                                    success: function(data)
                                    {
                                        Ext.Msg.show({
                                                title: 'Mensaje',
                                                msg: data.mensaje,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.INFO
                                            });
                                        cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                        store.load();
                                    }
                                });             
                            }
                            else
                            {
                                //Bloque para guardar Factibilidad de Alquiler de Servidores y storage
                                var json = obtenerInformacionGridAlquilerServidores(gridAlquilerServidores);
                                
                                if(json && boolAvanza)
                                {
                                    $.ajax({
                                        type: "POST",
                                        url: urlGuardarFactibilidadAlquilerServ,
                                        data:
                                            {
                                                'idServicio': data.get('id_servicio'),
                                                'data'      : json
                                            },
                                        beforeSend: function()
                                        {
                                            Ext.get(winIngresoFactibilidad.getId()).mask('Guardando datos de Factibilidad...');
                                        },
                                        success: function(data)
                                        {
                                            Ext.get(winIngresoFactibilidad.getId()).unmask();
                                            Ext.Msg.show({
                                                    title: 'Mensaje',
                                                    msg: data.mensaje,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.INFO
                                                });
                                            cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                            store.load();
                                        }
                                    });    
                                }
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function() {
                            cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                        }
                    },
                ]
            });

            winIngresoFactibilidad = Ext.widget('window', {
                title: 'Ingreso de Factibilidad POOL RECURSOS',
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: false,
                items: [formPanelIngresoFactibilidad]
            });
        }

        winIngresoFactibilidad.show();
        
        if(boolContieneAlquilerServ)
        {
            totalStorage = parseInt(data.get("poolStorage").replace("(GB)", "").trim());
            $("#progressbar-storage").progressbar({
                max: totalStorage
            });

            $("#progressbar-storage").progressbar("option", "value", totalStorage);
            $("#progressbar-storage-label").text(totalStorage+" (GB)");
            
            $.ajax({
                    type: "POST",
                    url: urlGetServidoresAlquiler,
                    data:
                        {
                            'idServicio': data.get('id_servicio')
                        },
                    beforeSend: function()
                    {
                        Ext.get(gridAlquilerServidores.getId()).mask('Obteniendo Información de Servidores Contratados...');
                    },
                    complete: function()
                    {
                        Ext.get(gridAlquilerServidores.getId()).unmask();
                    },
                    success: function(data)
                    {
                        //Llenar grid de servidores alquilados
                        $.each(data.arrayServidores, function(i, item) 
                        {
                            var recordParamDet = Ext.create('alquilerServidoresModel', {
                                idElemento     : item.idElemento,
                                idServicio     : item.idServicio,
                                idRecurso      : item.idRecurso,
                                modelo         : item.modelo,
                                nombreElemento : item.nombreElemento,
                                licenciamiento : '',
                                storage        : '',
                                dataStore      : '',
                                valor          : ''
                            });
                            
                            storeAlquilerServidores.insert(i, recordParamDet);
                        });
                        
                        //Llenar storage de disco                       
                        storeStorage = new Ext.data.Store({
                            fields: ['idRecurso','nombreRecurso','valor'],
                            data: data.arrayStorage
                        });
                        
                        Ext.getCmp('cmbStorage').bindStore(storeStorage);
                        
                        //Llenar sotorage de licenciamento
                        storeLicenciamiento = new Ext.data.Store({
                            fields: ['idRecurso','nombreRecurso'],
                            data: data.arrayLicencias
                        });
                        
                        Ext.getCmp('cmbLicenciamiento').bindStore(storeLicenciamiento);
                    }
                });   
        }
    }
}

function validarExistenteEnArray(array,value)
{
    var bool = false;
    $.each(array, function(i, item)
    {
        if(item===value)
        {
            bool = true;            
            return false;
        }
    });
    
    return bool;
}

function actualizarValorStorage(valor,operador)
{
    if(operador==='-')
    {
        totalStorage = totalStorage - parseInt(valor);
    }
    else
    {
        totalStorage = totalStorage + parseInt(valor);
    }    
    //Calcular total de storage disponible
    $("#progressbar-storage").progressbar("option", "value", totalStorage);
    $("#progressbar-storage-label").text(totalStorage+" (GB)");
}

function obtenerInformacionGridAlquilerServidores(grid)
{
    if(grid.getStore().getCount()!==0)
    {    
        var array_data = new Array();
        
        for (var i = 0; i < grid.getStore().getCount(); i++)
        {                        
            if(Ext.isEmpty(grid.getStore().getAt(i).data.storage))
            {
                Ext.Msg.alert("Advertencia","Deben asignarse todos los <b>Storage</b> a los servidores");
                return false;
            }
            else if(Ext.isEmpty(grid.getStore().getAt(i).data.valor))
            {
                Ext.Msg.alert("Advertencia","Deben asignar <b>Capacidad de disco</b> a todos los servidores");
                return false;
            }
            else if(Ext.isEmpty(grid.getStore().getAt(i).data.dataStore))
            {
                Ext.Msg.alert(Advertencia,"Deben asignarse todos los <b>Datastore</b> a los servidores");
                return false;
            }  
            else if(Ext.isEmpty(grid.getStore().getAt(i).data.licenciamiento))
            {
                Ext.Msg.alert("Advertencia","Deben asignarse todos las <b>Licencias</b> a los servidores");
                return false;
            }  
            else
            {
               var json                  = {};
               json['idServicio']        = parseInt(grid.getStore().getAt(i).data.idServicio,10);
               json['idRecurso']         = parseInt(grid.getStore().getAt(i).data.idRecurso,10);
               json['elementoId']        = parseInt(grid.getStore().getAt(i).data.idElemento,10);
               json['storage']           = parseInt(grid.getStore().getAt(i).data.storage,10);
               json['cantidadStorage']   = parseInt(grid.getStore().getAt(i).data.valor,10);
               json['licenciamiento']    = parseInt(grid.getStore().getAt(i).data.licenciamiento,10);
               json['datastore']         = grid.getStore().getAt(i).data.dataStore;
               json['modelo']            = grid.getStore().getAt(i).data.modelo;
               
               array_data.push(json);
            }
        }

        var arrayStorage = storeStorage.data.items;
        $.each(arrayStorage, function(i, item)
        {
            var totalDisk = array_data.reduce((sum, value) => (typeof value.cantidadStorage == "number" 
                            && value.storage == item.data.idRecurso ? sum + value.cantidadStorage : sum), 0);
            if(isNaN(totalDisk))
            {
                Ext.Msg.alert("Advertencia", 'Ingresar solo datos numéricos en la capacidad del disco '+item.data.nombreRecurso);
                boolAvanza = false;
                return false;
            }
            boolAvanza = true;
            if(totalDisk>item.data.valor)
            {
                Ext.Msg.alert("Advertencia", 'La cantidad de disco '+item.data.nombreRecurso+' asignada, supera la \n\
                                contratada. Capacidad contratada '+item.data.valor+'GB');
                boolAvanza = false;
                return false;
            }
        });
        

        return Ext.JSON.encode(array_data);     
    }
}

function esEntero(numero)
{
    if (numero % 1 === 0) {
        return true;
    } else {
        return false;
    }
}
