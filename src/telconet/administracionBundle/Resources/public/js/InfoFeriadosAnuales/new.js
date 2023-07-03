/* global Ext*/

var objDescripcion = '';
Ext.onReady(function() {

    var storeFeriados = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetFeriados,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams: {
                        anio: '2018'
                    }                        
                },
            fields:
                [
                    {name: 'idFeriadosAnuales' , mapping: 'idFeriadosAnuales'},
                    {name: 'feriadosId'        , mapping: 'feriadosId'},
                    {name: 'tipo'              , mapping: 'tipo'},
                    {name: 'feDesde'           , mapping: 'feDesde'},
                    {name: 'feHasta'           , mapping: 'feHasta'},
                    {name: 'cantonId'          , mapping: 'cantonId'},
                    {name: 'nombreCanton'      , mapping: 'nombreCanton'},
                    {name: 'comentario'        , mapping: 'comentario'},
                    {name: 'action1'           , mapping: 'action1'},
                    {name: 'action2'           , mapping: 'action2'},
                    {name: 'action3'           , mapping: 'action3'}
                ],
            autoLoad: true
        });

    storeAnio = getAnios();
    cmbAnio = Ext.create('Ext.data.comboGenericoList', {store: storeAnio,
        fieldLabel: "Año:",
        id: 'cmbAnio',
        name: 'cmbAnio',
        width: 150,
        labelWidth: 50});
    
    
    
    
    this.cmbAnio.on('select',function(cmb){	
        storeFeriados.proxy.extraParams = {anio: cmb.getValue()};
        storeFeriados.load({params: {}});    
    },this); 
    
    var gridFeriados = Ext.create('Ext.grid.Panel',
        {
            width: 930,
            height: 300,
            store: storeFeriados,
            viewConfig:
                {
                    enableTextSelection: true,
                    trackOver: true,
                    stripeRows: true,
                    loadMask: true
                },
            columns:
                [
                    {
                        header: 'idFeriadosAnuales',
                        dataIndex: 'Id',
                        width: 250,
                        sortable: true,
                        hidden:true
                    },
                    {
                        header: 'FeriadosId',
                        dataIndex: 'Id Feriado',
                        width: 250,
                        sortable: true,
                        hidden:true
                    },                                    
                    {
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 150,
                        sortable: true
                    },
                    {
                        header: 'cantonId',
                        dataIndex: 'Id canton',
                        width: 150,
                        hidden: true
                    },                                        
                    {
                        header: 'Cantón',
                        dataIndex: 'nombreCanton',
                        width: 150,
                        sortable: true
                    },                    
                    {
                        header: 'Desde',
                        dataIndex: 'feDesde',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Hasta',
                        dataIndex: 'feHasta',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Comentario',
                        dataIndex: 'comentario',
                        width: 400,
                        align: 'center',
                        sortable: true
                    }
                ],
            title: 'Feriados del Año',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeFeriados,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'Div_New_Feriados'
        });    
    
    formFeriados = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 940,
        title: 'Agregar Feriados',
        renderTo: 'Div_New_Feriados',
        frame: true,
        //layout:'vbox',
        layoutConfig: {
            type: 'table',
            columns: 3,
            pack: 'center',
            align: 'middle',
            tableAttrs: {
                style: {
                    width: '90%',
                    height: '90%'
                }
            },
            tdAttrs: {
                align: 'left',
                valign: 'middle'
            }
        },
        buttonAlign: 'center',
        buttons: [
            {
                text: 'Guardar',
                name: 'btnGuardar',
                id: 'idBtnGuardar',
                disabled: false,
                handler: function() {
                    var form = formFeriados.getForm();
                    var objDescripcion = form.findField('objTxtDescripcion');
                    var objTipo        = form.findField('tipoFeriado');
                    var objMes         = form.findField('mes');
                    var objDia         = form.findField('dia');
                    var objCanton      = form.findField('cmbCanton');
                    var objComentario  = form.findField('txtComentario');

                    if (objDescripcion.getValue().length == 0)
                    {
                        Ext.Msg.alert("Error - Creación de Feriados", "Debe digitar una Descripción del Feriado");
                        objDescripcion.focus();
                        return false;
                    }
                    if (form.isValid())
                    {                    

                        var data = form.getValues();
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url: strUrlSaveFeriado,
                            method: 'POST',
                            params: {
                                data,
                                strDescripcion : objDescripcion.getValue(),
                                strTipo        : objTipo.getValue(),
                                strMes         : objMes.getValue(),
                                strDia         : objDia.getValue(),
                                intCanton      : objCanton.getValue(),
                                strComentario  : objComentario.getValue(),
                                intIdFeriados  : 0
                            },

                            success: function(response) {
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                if (json.strStatus == 'OK')
                                {
                                    Ext.Msg.alert('Creacion de Feriados ', json.strMessageStatus);
                                    window.location.href = strUrlIndex;
                                } else
                                {
                                    Ext.Msg.alert('Error - Creacion de Feriados ', json.strMessageStatus);
                                }
                            },
                            failure: function(result) {
                                Ext.get(document.body).unmask();
                                Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
                            }
                        });
                     }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    storeFeriado.removeAll();
                }
            }]
    });
    

    var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none",
                align: 'center'
            },
            width: 1500,
            items: [
                    cmbAnio
            ]
        });
    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 1500,
            items: [                
                gridFeriados
            ]
        });



    formFeriados.add(container);
    formFeriados.add(container2);
});


