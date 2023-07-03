Ext.onReady(function() { 
    
    Ext.tip.QuickTipManager.init();
        
    var boolEliminaIntervalos = true;
    var permiso = $("#ROLE_409-5817");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    if (!boolPermiso)
    {
        boolEliminaIntervalos = true;
    }
    else
    {
        boolEliminaIntervalos = false;
    }

    storeIntervalos = new Ext.data.Store({ 
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
                estado : 'Todos'
            }
        },
        fields:
            [
                {name:'id_intervalo', mapping:'id_intervalo'},
                {name:'hora_inicio' , mapping:'hora_inicio'},
                {name:'hora_fin'    , mapping:'hora_fin'},
                {name:'estado'      , mapping:'estado'}
            ],
        autoLoad: true
    });

    sm = Ext.create('Ext.selection.CheckboxModel', 
      {
          checkOnly: true
      });

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 500,
        height: 500,
        store: storeIntervalos,
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
                        hidden: boolEliminaIntervalos,
                        handler: function() {
                             eliminaIntervalos();
                        }
                    }
                ]
            }
        ],
        columns:[
                {
                  id: 'hora_inicio',
                  header: 'Hora Inicio',
                  dataIndex: 'hora_inicio',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'hora_fin',
                  header: 'Hora Fin',
                  dataIndex: 'hora_fin',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                } 
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storeIntervalos,
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
        },
        renderTo: 'grid'
    });

    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 500,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text    : 'Buscar',
                    iconCls : "icon_search",
                    handler : function(){ buscar();}
                },
                {
                    text    : 'Limpiar',
                    iconCls : "icon_limpiar",
                    handler : function(){ limpiar();}
                }

                ],                
                items: [
                        { width: '5%',border:false},{
                            xtype      : 'combobox',
                            fieldLabel : 'Estado',
                            id         : 'cmbEstado',
                            value      : 'Todos',
                            store: 
                            [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '200'
                        }
                        ],	
        renderTo: 'filtro'
    }); 
    
});


function eliminaIntervalos()
{
    var tramaIntervalos = "";
    var numeroIntervalos = 0;

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

    if (sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            tramaIntervalos = tramaIntervalos + sm.getSelection()[i].data.id_intervalo;

            if (sm.getSelection()[i].data.estado == "Eliminado")
            {
                numeroIntervalos = numeroIntervalos + 1;
            }

            if (i < (sm.getSelection().length - 1))
            {
                tramaIntervalos = tramaIntervalos + '|';
            }
        }
        
        if(numeroIntervalos == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se inactivarán los intervalos seleccionados. Desea continuar?', function(btn) {
                if (btn == 'yes') {

                    conn.request({
                        url: url_eliminarIntervalos,
                        method: 'post',
                        params: {tramaIntervalos: tramaIntervalos},
                        success: function(response) {
                            var json = Ext.JSON.decode(response.responseText);
                            if (json.estado == "Ok") {
                                Ext.Msg.alert('Alerta', 'Transaccion Exitosa');

                               storeIntervalos.load();
                            }
                            else {
                                Ext.Msg.alert('Error ', 'Se produjo un error en la ejecucion.');

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
            Ext.Msg.alert('Alerta ','La acción solo puede ser ejecutada con registros en estado <b style="color:green;">Activo</b>');
        }
    }
    else
    {
        Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista');
    }
}


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar()
{
    storeIntervalos.getProxy().extraParams.estado = Ext.getCmp('cmbEstado').value;
    storeIntervalos.load();
}
function limpiar()
{
    storeIntervalos.removeAll();
    grid.getStore().removeAll();
}