function factibilidadRapida($item, ciudad)
{ 
    var strNombreMaquina    =   "";
    var idHyperview         =   0;
    var idVCenter           =   0;
    var idCluster           =   0;
    var nombreHyperview     =   "";
    var nombreVCenter       =   "";
    var nombreCluster       =   "";
    
    $item       = $item.parents('li');

    $item.find("h5").each(function() 
    {       
        var nombre = $(this).text();
        strNombreMaquina =nombre;
        $.each(arrayInformacion, function(key, value) {
            if(value.nombre === nombre)
            {
                jsonInfo = value;
                return false;
            }
        });
    });
    
    $.ajax
    ({
        type: "POST",
        url: urlGetInformacionGeneralHosting,
        data:
        {
            idServicio      : idServicio,
            tipoInformacion : 'MAQUINAS-VIRTUALES',
            esCombo         : 'S',
            async           : false,
        },
        success: function(data)
        {   
            $.each(data, function(key, value) {
                if(value.nombreElemento === strNombreMaquina){
                    idHyperview     =   value.HYPERVIEW;
                    nombreHyperview =   value.NOMBRE_HYPERVIEW;
                    idVCenter       =   value.VCENTER;
                    nombreVCenter   =   value.NOMBRE_VCENTER;
                    idCluster       =   value.CLUSTER;
                    nombreCluster   =   value.NOMBRE_CLUSTER;
                    if(idHyperview !== null)
                    {
                        storeHyperView.removeAll();
                        storeHyperView.load(); 
                        Ext.getCmp('cmbHyperView').setRawValue(nombreHyperview);
                        Ext.getCmp('cmbHyperView').setValue(parseInt(idHyperview , 10));
                        Ext.getCmp('cmbVcenter').setDisabled(false);
                        storeVCenter.proxy.extraParams = {idVcenter: parseInt(idHyperview, 10),
                                                  tipoDato :'VCENTER',
                                                  ciudad   : ciudad
                                                 };
                        storeVCenter.load({params: {}});
                        Ext.getCmp('cmbVcenter').setRawValue(nombreVCenter);
                        Ext.getCmp('cmbVcenter').setValue(parseInt(idVCenter , 10));
                        Ext.getCmp('cmbCluster').setDisabled(false); 
                        storeCluster.proxy.extraParams = {idVcenter: parseInt(idVCenter, 10),
                                                  tipoDato :'CLUSTER',
                                                  ciudad   : ciudad
                                                 };
                        storeCluster.load();
                        Ext.getCmp('cmbCluster').setRawValue(nombreCluster);
                        Ext.getCmp('cmbCluster').setValue(parseInt(idCluster , 10));
                        formPanelFactibilidadRapida.refresh;
                        return false;
                    }
                }
            });
        }
    });
    var rowEditingRecursosMV = Ext.create('Ext.grid.plugin.RowEditing', {
                saveBtnText: '<label style="color:blue;"><i class="fa fa-check-square"></i></label>',
                cancelBtnText: '<i class="fa fa-eraser"></i>',
                clicksToMoveEditor: 1,
                autoCancel: false
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
                        ciudad   : ciudad,
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
                        ciudad   : ciudad,
                        idVcenter:''
                    },
                    params: {
                        tipoDato : 'VCENTER',
                        ciudad   : ciudad,
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
                        ciudad  : ciudad
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
                autoLoad: true,
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
                        maquinaVirtual: jsonInfo.idMaquina
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

    var formPanelFactibilidadRapida = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 10,
                bodyStyle: "background: white; padding: 5px; border: 0px none;",
                height: 350,
                width: 490,
                frame: true,
                items: [
                    //Resumen del cliente
                    {   width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        fieldLabel: '<b> Nombre de Máquina</b>',
                        name: 'txt_MaquinaVirtual',
                        id: 'txt_MaquinaVirtual',
                        value: jsonInfo.nombre,
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'combobox',
                        name: 'cmbHyperView',
                        id: 'cmbHyperView',
                        fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* HyperView</b>',
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
                                                                  ciudad   : ciudad,
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
                        fieldLabel: '<i class="fa fa-connectdevelop" aria-hidden="true"></i>&nbsp;<b>* VCenter</b>',
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
                                                                  ciudad   : ciudad,
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
                    },
                    gridFactStoragePoolCompleto
                    
                ],                
                buttons: [
                    {
                        text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Guardar',
                        handler: function() 
                        {
                            //Valida que todos los campos estén llenos y procede a guardar la factibilidad
                                var hyperview       = Ext.getCmp("cmbHyperView").getValue() == nombreHyperview ? parseInt(idHyperview) : Ext.getCmp("cmbHyperView").getValue();
                                var vcenter         = Ext.getCmp("cmbVcenter").getValue() == nombreVCenter ? parseInt(idVCenter) : Ext.getCmp("cmbVcenter").getValue();
                                var cluster         = Ext.getCmp("cmbCluster").getValue() == nombreCluster ? parseInt(idCluster) : Ext.getCmp("cmbCluster").getValue();
                                var maquinaVirtual  = jsonInfo.idMaquina;
                                var gridRecursos    = Ext.getCmp('gridFactStoragePoolCompleto');
                                var cont            = 0; 
                                var arrayDs         = [];

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
                                
                                //Se envía parámetro  'factibilidadRap':'S' para diferenciar del flujo normal de factibilidad donde se ata a una solicitud de Factibilidad
                                $.ajax
                                ({
                                    type: "POST",
                                    url: urlGuardarDatosFactibilidadHosting,
                                    data:
                                        {
                                            'idServicio': idServicio,
                                            'vcenter'   : vcenter,
                                            'cluster'   : cluster,
                                            'hyperview' : hyperview,
                                            'datastore' : Ext.JSON.encode(arrayDs),
                                            'maquinaVirtual'      : maquinaVirtual,
                                            'factibilidadRap'     :   'S'
                                        },
                                    success: function(data)
                                    {
                                        Ext.Msg.show({
                                                title: 'Mensaje',
                                                msg: data.mensaje,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.INFO
                                            });
                                        
                                        winFactibilidadRapida.close();
                                        winFactibilidadRapida.destroy();
                                    }
                                }); 
                        }
                    },
                    {
                        text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                        handler: function() {
                            winFactibilidadRapida.close();
                            winFactibilidadRapida.destroy();
                        }
                    },
                ]
            });


    winFactibilidadRapida = Ext.widget('window', {
                            title: 'Factibilidad Rápida',
                            layout: 'fit',
                            resizable: true,
                            modal: true,
                            closable: false,
                            items: [formPanelFactibilidadRapida]
                        });
    winFactibilidadRapida.show();
}

/*Función que muestra pantalla con caracterísiticas de producto para hosting cloud IAAS y 
*envía parámetros para crear tarea al asesor comercial
*/
function crearTareaAComercial($item)
{ 
    var jsonInfo               = {};
    var recurso     = '';

    var winIngresoTarea = "";
    $item       = $item.parents('li');
        
        $item.find("h5").each(function() 
        {       
            var nombre = $(this).text();

            $.each(arrayInformacion, function(key, value) {
                if(value.nombre === nombre)
                {
                    jsonInfo = value;
                    return false;
                }
            });
        });
    
    var storeProductos = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: urlGetInformacionSolucion,
                    params: 
                    { 
                        idServicio      : idServicio
                    },
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        idServicio      : idServicio
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });

    var formPanelCreacionTarea = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 10,
                bodyStyle: "background: white; padding: 5px; border: 0px none;",
                height: 200,
                width: 400,
                frame: true,
                items: [
                    //Resumen del cliente
                    {   width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        fieldLabel: '<b> Nombre Máquina</b>',
                        name: 'txt_MaquinaVirtual',
                        id: 'txt_MaquinaVirtual',
                        value: jsonInfo.nombre,
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'combobox',
                        name: 'cmbRecurso',
                        id: 'cmbRecurso',
                        fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* Recurso</b>',
                        displayField: 'nombreElemento',
                        valueField: 'idElemento',
                        //Lleveme al combo
                        store: storeProductos,
                        width: 300,
                        editable:true,
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: 'anchor',
                        listeners: {
                            select: function(combo, rec, idx, data) 
                            {
                                recurso =   rec[0].raw.nombreElemento;
                                Ext.getCmp('txt_Observacion').setValue(Ext.getCmp('txt_Observacion').getValue()  + recurso);
                            }

                        }
                    },
                    {
                        xtype: 'textareafield',
                        fieldLabel: '<b>Observación</b>',
                        name: 'txt_Observacion',
                        id: 'txt_Observacion',
                        value: "",
                        width: 300,
                        heigth: 400,
                        allowBlank: false,
                        readOnly: false
                    },
                    { width: '10%', border: false},
                ],                
                buttons: [
                    {
                        text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Solicitar',
                        handler: function() 
                        {
                            $.ajax({
                            url: urlCrearTareaAComercial,
                                type: "POST",
                                timeout:600000,
                                data: 
                                { 
                                    idServicio      : idServicio,
                                    observacion     : '<b>' + Ext.getCmp('txt_MaquinaVirtual').getValue() +'</b> <br/> '
                                                      +Ext.getCmp('txt_Observacion').getValue()  + ' ' 
                                },
                                beforeSend: function()
                                {
                                    Ext.get(winIngresoTarea.getId()).mask('Generando Tarea...');
                                },
                                complete: function()
                                {
                                    Ext.get(winIngresoTarea.getId()).unmask();
                                },
                                success: function(data)
                                {
                                            Ext.Msg.alert('Mensaje', data.asignacion+"\nNúmero: <b>"+data.numeroTarea+"</b>", function(btn) {
                                                if (btn == 'ok'){
                                                    winIngresoTarea.close();
                                                    winIngresoTarea.destroy();
                                                }
                                            });
                                    
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });
                        }
                    },
                    {
                        text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                        handler: function() {
                            winIngresoTarea.close();
                            winIngresoTarea.destroy();
                        }
                    },
                ]
            });
            
            winIngresoTarea = Ext.widget('window', {
                title: 'Solicitar Recursos a Comercial',
                layout: 'fit',
                resizable: true,
                modal: true,
                closable: false,
                items: [formPanelCreacionTarea]
            });
            winIngresoTarea.show();

}