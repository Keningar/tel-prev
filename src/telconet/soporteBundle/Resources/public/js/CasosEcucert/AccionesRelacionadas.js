/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
/**
* 
* generarReporte
* 
* Permite notificar al cliente sino ha sido notificado de la incidencia.
* Los parametros de filtro son:
* 
* @param   casoId                - Id del caso
*           idDetalleIncidencia   - Id del de detalle de la incidencia
*           idPunto               - Id del punto del cliente
*           loginCliente          - Login del cliente
*           personaEmpresaRolId   * Id persona empresa Rol
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 2103-2019
*
*/
function generarReporte()
{
    var connGenerarReporte = new Ext.data.Connection({
        timeout : 90000,
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Generando Reporte..');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 
    var formPanelReporteEcu = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 150,
        width: 400,
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [
            {
                    xtype: 'textfield',
                    id: 'numeroTicketReport',
                    name:'numeroTicketReport',
                    fieldLabel: 'Numero Ticket',
                    value: '',
                    width: 424
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Fecha',
                items: [
                    {
                        xtype: 'datefield',
                        width: 320,
                        id: 'feDesdeReporte',
                        name: 'feDesdeReporte',
                        fieldLabel: 'Desde:',
                        format: 'Y-m-d',
                        editable: false
                    },
                    {
                        xtype: 'datefield',
                        width: 320,
                        id: 'feHastaReporte',
                        name: 'feHastaReporte',
                        fieldLabel: 'Hasta:',
                        format: 'Y-m-d',
                        editable: false
                    }
                ]
            }
        ]
    }); 
    var btnaceptar = Ext.create('Ext.Button', {
            text: 'Aceptar',
            cls: 'x-btn-left',
            handler: function() {
                var f = formPanelReporteEcu.getForm();
                var noTicket      = f.findField('numeroTicketReport').getValue();
                var feDesdeReport = f.findField('feDesdeReporte').getValue();
                var feHastaReport = f.findField('feHastaReporte').getValue();
                if ((feDesdeReport && feHastaReport && 
                     feDesdeReport.length!==0 && feHastaReport.length!==0) ||
                     (noTicket && noTicket.length!==0)
                   )
                {
                    winReporteEcu.destroy();
                    connGenerarReporte.request({
                    url: './generarReporte',
                    method: 'post',
                    params:
                    {
                        fechaInicio: feDesdeReport,
                        fechaFin: feHastaReport,
                        noTicket: noTicket
                    },
                    success: function(response) {
                        var strRespuesta = JSON.parse(response.responseText).estado;
                        Ext.Msg.show({
                            title: 'Mensaje',
                            msg: strRespuesta,
                            buttons: Ext.Msg.OK
                        });
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
                else
                {
                    Ext.Msg.show({
                            title: 'Mensaje',
                            msg: "Ingrese el ticket o la fecha",
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.OK
                        });
                }
            }
    }); 
    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		        winReporteEcu.destroy();													
            }
    });  
    var winReporteEcu = Ext.create('Ext.window.Window', {
			title: 'Reporte Ecucert',
			modal: true,
			width: 510,
			height: 190,
			resizable: true,
			layout: 'fit',
			items: [formPanelReporteEcu],
			buttonAlign: 'center',
			buttons:[btnaceptar,btncancelar]
	}).show();
}

/**
* 
* Agregar Categoría y SubCategoría
* 
* Permite agregar los parámetros de categoria y subCategoria.
* Los parametros de filtro son:
* 
* @param   casoId                - Id del caso
*           idDetalleIncidencia   - Id del de detalle de la incidencia
*           idPunto               - Id del punto del cliente
*           loginCliente          - Login del cliente
*           personaEmpresaRolId   * Id persona empresa Rol
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 18-05-2021
*
*/
function agregarCategoria()
{
    var storeCategoriaSub = new Ext.data.Store
    ({ 
        name: 'storeCategoriaSub',
        id: 'storeCategoriaSub',
        total: 'total',
        proxy: 
        {
            timeout: 9600000,
            type: 'ajax',
            url : url_obtener_categoria,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
            {name: 'idCategoria',            	mapping: 'idCategoria'},
            {name: 'categoriaCat',              mapping: 'categoria'},
            {name: 'subCategoriaCat',           mapping: 'subCategoria'},
            {name: 'tipoEventoCat',             mapping: 'tipoEvento'},
            {name: 'codigoPlantilla',           mapping: 'codigoPlantilla'},
            {name: 'idPlantilla',               mapping: 'idPlantilla'}
        ],
        autoLoad: true
    });   

    var gridCategoria = Ext.create('Ext.grid.Panel', {
		id:'gridCategoria',
		store: storeCategoriaSub,	
        //viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        loadMask: true,
        frame: false,	
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
                        iconCls: 'icon_add',
                        text: 'Registrar Categoría/SubCategoría',
                        scope: this,
                        handler: function() 
                        {
                            var storeTipoEventoRg = new Ext.data.Store({ 
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url : 'buscarTipoEvento',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                },
                                fields:
                                [
                                    {name:'comboTipoEventoRg', mapping:'tipoEvento'}
                                ],
                                autoLoad: false,
                            });

                            var formPanelIngresoCategoria = Ext.create('Ext.form.Panel', {
                                bodyPadding: '15 10 0',
                                height: 170,
                                width: 315,
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '*Categoria',
                                        name: "categoriaRg",
                                        value: "",
                                        width: 300,
                                        allowBlank: false
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'SubCategoria',
                                        name: "subCategoriaRg",
                                        value: "",
                                        width: 300,
                                        allowBlank: false
                                    },
                                    {
                                        xtype: 'combobox',
                                        id: 'tipoEventoRg',
                                        name: 'tipoEventoRg',
                                        fieldLabel: "*Tipo de Evento",
                                        emptyText: 'Seleccione el Tipo de Evento',
                                        store: storeTipoEventoRg,
                                        displayField: 'comboTipoEventoRg',
                                        valueField: 'comboTipoEventoRg',
                                        height:30,
                                        width: 300,
                                        border:0,
                                        marginTop:0,
                                        queryMode: "remote",
                                        editable: false
                                    }
                                ]
                             });
    
                            var btncancelarIngresoCat = Ext.create('Ext.Button', {
                                text: 'Cancelar',
                                cls: 'x-btn-rigth',
                                handler: function() {
                                    WinIngresoModalCate.destroy();													
                                }
                            }); 
    
                            var btnaceptarIngresoCat = Ext.create('Ext.Button', {
                                text: 'Agregar',
                                cls: 'x-btn-left',
                                handler: function() {
                                    var categoria         =  formPanelIngresoCategoria.down('textfield[name=categoriaRg]').getValue();   
                                    var subCategoria      =  formPanelIngresoCategoria.down('textfield[name=subCategoriaRg]').getValue();   
                                    var tipoEvento        =  formPanelIngresoCategoria.down('textfield[name=tipoEventoRg]').getValue();    
                                    
                                    if(categoria != null && categoria!= "" 
                                       && tipoEvento != null && tipoEvento != "" )
                                    {
                                        var ok = Ext.MessageBox.down('#ok');
                                        ok.setText('Si');
                                        var cancel = Ext.MessageBox.down('#cancel');
                                        cancel.setText('No');
                                        Ext.MessageBox.show({
                                            title: "Confirmar",
                                            msg: "¿Desea registrar "+categoria+"?",
                                            buttons: Ext.Msg.OKCANCEL,
                                            icon: Ext.MessageBox.QUESTION,
                                            fn: function(buttonId) 
                                            {
                                                if (buttonId === "ok")
                                                {
                                                    Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere'); 
                                                    $.ajax({
                                                        url : url_guardar_categoria,
                                                        type : 'POST',
                                                        data : {
                                                            categoria:          categoria,
                                                            subcategoria:       subCategoria,
                                                            tipoEvento:         tipoEvento
                                                        },
                                                        dataType:'json',
                                                        success : function(data) { 
                                                            var strRespuesta = data.respuesta;
                                                            var strSuccess = data.success;
                                                            Ext.MessageBox.show({
                                                                title:'Mensaje',
                                                                msg: strRespuesta,
                                                                buttons: Ext.Msg.OK,
                                                                fn: function(buttonId) 
                                                                {
                                                                    if (buttonId === "ok")
                                                                    {
                                                                        if(strSuccess == "true")
                                                                        {
                                                                            WinIngresoModalCate.destroy();	 
                                                                            winVerCategoriaEcu.destroy();
                                                                            agregarCategoria();
                                                                        }
                                                                    }
                                                                }
                                                            });     
                                                        },
                                                        error : function(request,error)
                                                        {
                                                            Ext.Msg.show({
                                                                title: 'Error',
                                                                msg: JSON.stringify(request),
                                                                buttons: Ext.Msg.OK,
                                                                icon: Ext.MessageBox.ERROR
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        });

                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje',"Ingrese la información requerida") ;
                                    }
                                   
                                }
                            });
    
                            var WinIngresoModalCate = Ext.create('Ext.window.Window', {
                                title: 'Ingreso categoria/subcategoria',
                                modal: true,
                                width: 335,
                                height: 180,
                                resizable: true,
                                layout: 'fit',
                                items: [formPanelIngresoCategoria],
                                buttonAlign: 'center',
                                buttons:[btnaceptarIngresoCat,btncancelarIngresoCat]
                            }).show(); 
                            
                        }
                    }
                ]
            }
        ],
		columns: [
            {
                id: 'idCategoria',
                header: 'Id Categoria',
                dataIndex: 'idCategoria',
                width: 0,
                sortable: true,
                hidden: true				 
            },
            {
                id: 'idPlantilla',
                header: 'Id Plantilla',
                dataIndex: 'idPlantilla',
                width: 0,
                sortable: true,
                hidden: true				 
            },
			{
			      id: 'categoriaCat',
			      header: 'Categoria',
			      dataIndex: 'categoriaCat',
			      width:190,
			      sortable: true						 
			},
			{
			      id: 'subCategoriaCat',
			      header: 'Sub Categoria',
			      dataIndex: 'subCategoriaCat',
			      width:190,
			      sortable: true						 
            },
            {
                id: 'tipoEventoCat',
                header: 'Tipo de Evento',
                dataIndex: 'tipoEventoCat',
                width:200,
                sortable: true						 
            },
            {
                id: 'codigoPlantilla',
                header: 'Código de Plantilla',
                dataIndex: 'codigoPlantilla',
                width:200,
                sortable: true						 
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 120,
                sortable: true,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-agregarArchivo"
                            this.items[0].tooltip = 'Crear Plantilla';
                            
                            if (rec.data.idPlantilla != 0)
                            {
                                classButton = "button-grid-edit"
                                this.items[0].tooltip = 'Editar Plantilla';
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec             = storeCategoriaSub.getAt(rowIndex);
                            var idPlantilla     = rec.data.idPlantilla;
                            var codigoPlantilla = rec.data.codigoPlantilla;
                            var titulo          = "Registro Nuevo";
                            var mensaje         = "¿Desea crear una plantilla para "+codigoPlantilla+"?";
                            if(idPlantilla != 0)
                            {
                                titulo   = "Editar Registro";
                                mensaje  = "¿Desea editar la plantilla "+codigoPlantilla+"?";
                            }

                            var ok = Ext.MessageBox.down('#ok');
                            ok.setText('Si');
                            var cancel = Ext.MessageBox.down('#cancel');
                            cancel.setText('No');
                            Ext.MessageBox.show({
                                title: titulo,
                                msg: mensaje,
                                buttons: Ext.Msg.OKCANCEL,
                                icon: Ext.MessageBox.QUESTION,
                                fn: function(buttonId) 
                                {
                                    if (buttonId === "ok")
                                    {
                                        var connRemoverCategoria = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        Ext.get(document.body).mask('Cargando la plantilla..');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });
                                        
                                        connRemoverCategoria.request({
                                            url: url_redireccionar_plantilla,
                                            method: 'post',
                                            params:
                                            {
                                                idPlantilla:     idPlantilla,
                                                codigoPlantilla: codigoPlantilla
                                            },
                                            success: function(response) {
                                                var url = JSON.parse(response.responseText).strUrl;
                                                var strSuccess = JSON.parse(response.responseText).success;
                                                var strRespuesta = JSON.parse(response.responseText).respuesta;
                                                
                                                if(strSuccess == "true")
                                                {
                                                    window.location.href = url;
                                                }
                                                else
                                                {
                                                    Ext.Msg.show({
                                                        title: 'Mensaje',
                                                        msg: strRespuesta,
                                                        buttons: Ext.Msg.OK
                                                    });
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
                                }
                            });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-delete"
                            this.items[1].tooltip = 'Borrar Categoría';

                            if (rec.data.idPlantilla != 0)
                            {
                                this.items[1].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                 = storeCategoriaSub.getAt(rowIndex);
                            var idCategoriaParam    = rec.data.idCategoria;
                            var categoria           = rec.data.categoriaCat;
                            var subCategoria        = rec.data.subCategoriaCat;
                            var nombreCatSub        = categoria;
                            if(typeof subCategoria != "undefined" && subCategoria != null && subCategoria != '')
                            {
                                nombreCatSub = nombreCatSub + '/'+subCategoria;
                            }
                            var ok = Ext.MessageBox.down('#ok');
                            ok.setText('Si');
                            var cancel = Ext.MessageBox.down('#cancel');
                            cancel.setText('No');
                            Ext.MessageBox.show({
                                title:'Eliminar',
                                msg: '¿Desea eliminar la categoría/subcategoría - '+nombreCatSub+'?',
                                buttons: Ext.Msg.OKCANCEL,
                                icon: Ext.MessageBox.QUESTION,
                                fn: function(buttonId) 
                                {
                                    if (buttonId === "ok")
                                    {
                                        var connRemoverCategoria = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        Ext.get(document.body).mask('Removiendo registro..');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });
                                        
                                        connRemoverCategoria.request({
                                            url: url_remover_categoria,
                                            method: 'post',
                                            params:
                                                {
                                                    idCategoriaParam:     idCategoriaParam
                                                },
                                            success: function(response) {
                                                var strRespuesta = JSON.parse(response.responseText).respuesta;
                                                var strSuccess = JSON.parse(response.responseText).success;
                                                Ext.Msg.show({
                                                    title: 'Mensaje',
                                                    msg: strRespuesta,
                                                    buttons: Ext.Msg.OK
                                                });
                                                if(strSuccess == "true")
                                                {
                                                    if(storeCategoriaSub != null)
                                                    {
                                                        storeCategoriaSub.load(); 	
                                                    }
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
                                }
                            });
                        }
                    }
               ]
            }
		],	
        viewConfig: {
            stripeRows: false, 
            getRowClass: function(record) {
                idPlantilla = record.data.idPlantilla;
                if(idPlantilla == 0)
                {
                    return "color1-row";
                }
                else
                {
                    return "color2-row";
                }
            }
        },	
		width: 900,
		height: 410
	});

    var formPanelCatEcu = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 420,
        width: 910,
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'label',
                id: 'lblTexto',
                style: {
                    color: 'red'
                },
                text: '*Los registros con color rojo no tienen plantilla'
            },
            gridCategoria
        ]
    }); 
    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		        winVerCategoriaEcu.destroy();													
            }
    });  
    var winVerCategoriaEcu = Ext.create('Ext.window.Window', {
			title: 'Ver Categoría/SubCategoría',
			modal: true,
            width: 930,
		    height: 490,
			resizable: true,
			layout: 'fit',
			items: [formPanelCatEcu],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();
}

/**
* 
* Subir archivo CSV
* 
* Permite subir archivos dsv de ips reportadas.
* Los parametros de filtro son:
* 
* @param   casoId                - Id del caso
*           idDetalleIncidencia   - Id del de detalle de la incidencia
*           idPunto               - Id del punto del cliente
*           loginCliente          - Login del cliente
*           personaEmpresaRolId   * Id persona empresa Rol
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 18-05-2021
*
*/
function subirArchivoCSV()
{

    var storeTipoEventoCSV = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarTipoEvento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
        [
            {name:'comboTipoEventoCSV', mapping:'tipoEvento'}
        ],
        autoLoad: false,
    });

    var formPanelEcuCSV = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 220,
        width: 325,
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'combobox',
                id: 'tipoEventoCSV',
                name: 'tipoEventoCSV',
                fieldLabel: "*Tipo de Evento",
                emptyText: 'Seleccione el Tipo de Evento',
                store: storeTipoEventoCSV,
                displayField: 'comboTipoEventoCSV',
                valueField: 'comboTipoEventoCSV',
                height:30,
                width: 300,
                border:0,
                marginTop:0,
                queryMode: "remote",
                editable: false,
                listeners: {
                    change: function(combo, tipoEvento) {

                        var storeCategoriaCSV = new Ext.data.Store({ 
                            total: 'total',
                            proxy: {
                                type: 'ajax',
                                url : url_obtener_categoria,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                                extraParams: {
                                    tipoEvento: tipoEvento
                                }
                            },
                            fields:
                            [
                                {name:'comboCategoriaCSV', mapping:'categoria'}
                            ],
                            autoLoad: true,
                        });

                        var comboCategoria = this.up('panel').down('#categoriaCSV');
                        comboCategoria.bindStore(storeCategoriaCSV);

                        Ext.getCmp('categoriaCSV').setDisabled(false);
                        Ext.getCmp('subCategoriaCSV').setDisabled(false);
                    }
                }
            },
            {
                xtype: 'combobox',
                id: 'categoriaCSV',
                name: 'categoriaCSV',
                fieldLabel: "*Categoría",
                emptyText: 'Seleccione la Categoría',
                displayField: 'comboCategoriaCSV',
                valueField: 'comboCategoriaCSV',
                height:30,
                width: 300,
                border:0,
                disabled: true,
                marginTop:0,
                queryMode: "remote",
                editable: false,
                listeners: {
                    change: function(combo, categoria) {
                        Ext.getCmp('archivoCSV').setDisabled(false);
                        var storeSubCategoriaCSV = new Ext.data.Store({ 
                            total: 'total',
                            proxy: {
                                type: 'ajax',
                                url : url_obtener_categoria,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                                extraParams: {
                                    categoria: categoria
                                }
                            },
                            fields:
                            [
                                {name:'comboSubCategoriaCSV', mapping:'subCategoria'}
                            ],
                            autoLoad: true,
                        });

                        var comboSubCategoria = this.up('panel').down('#subCategoriaCSV');
                        comboSubCategoria.bindStore(storeSubCategoriaCSV);
                    }
                }
            },
            {
                xtype: 'combobox',
                id: 'subCategoriaCSV',
                name: 'subCategoriaCSV',
                fieldLabel: "SubCategoría",
                emptyText: 'Seleccione la Sub Categoría',
                displayField: 'comboSubCategoriaCSV',
                valueField: 'comboSubCategoriaCSV',
                disabled: true,
                height:30,
                width: 300,
                border:0,
                marginTop:0,
                queryMode: "remote",
                editable: false
            },
            {
                xtype: 'filefield',
                id: 'archivoCSV',
                emptyText: 'Seleccionar archivo CSV',
                fieldLabel: '*Archivo CSV',
                width: 300,
                disabled: true,
                name: 'archivos[]',
                buttonText: '',
                buttonConfig: {
                    iconCls: 'upload-icon'
                }
            }
        ],
        buttons: [{
                text: 'Subir',
                cls: 'x-btn-left',
                handler: function() {
                    var form = this.up('form').getForm();
                    if(form.isValid()){
                        var categoria         =  formPanelEcuCSV.down('textfield[name=categoriaCSV]').getValue();   
                        var tipoEvento        =  formPanelEcuCSV.down('textfield[name=tipoEventoCSV]').getValue();  
                        var archivoCSV        =  formPanelEcuCSV.down('textfield[id=archivoCSV]').getValue();      
                        
                        if(categoria != null && categoria!= "" 
                            && tipoEvento != null && tipoEvento != "" 
                            && archivoCSV != null && archivoCSV!= "" )
                        {

                            var ok = Ext.MessageBox.down('#ok');
                            ok.setText('Si');
                            var cancel = Ext.MessageBox.down('#cancel');
                            cancel.setText('No');
                            Ext.MessageBox.show({
                                title: "Confirmar",
                                msg: "¿Desea subir el archivo asociado a la categoría "+categoria+"?",
                                buttons: Ext.Msg.OKCANCEL,
                                icon: Ext.MessageBox.QUESTION,
                                fn: function(buttonId) 
                                {
                                    if (buttonId === "ok")
                                    {
                                        form.submit({
                                            url: url_subir_archivoCSV,
                                            waitMsg: 'Procesando Archivo...',
                                            success: function(fp, o)
                                            {
                                                Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                                                    if(btn=='ok')
                                                    {
                                                        winReporteEcu.destroy();
                                                    }
                                                });
                                            },
                                            failure: function(fp, o) {
                                                Ext.Msg.alert("Alerta",o.result.respuesta);
                                            }
                                        });
                                    }
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Mensaje',"Ingrese la información requerida") ;
                        }
                    }
                }
        }]
    },
    {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            winReporteEcu.destroy();													
        }
    }); 
 
    var winReporteEcu = Ext.create('Ext.window.Window', {
			title: 'Subir Archivo CSV',
			modal: true,
			width: 340,
			height: 240,
			resizable: true,
			layout: 'fit',
			items: [formPanelEcuCSV]
	}).show();
}