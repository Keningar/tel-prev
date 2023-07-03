
Ext.require([
    '*'
]);

Ext.onReady(function() {        

    var tabs = new Ext.TabPanel({
        height: 500,
        renderTo: 'nodos-tabs-consulta',
        activeTab: 0,
        plain: true,
        autoRender: true,
        autoShow: true,
        items: [
            {contentEl: 'tab1', title: 'Datos Generales'},
            {contentEl: 'tab2', title: 'Datos Local', listeners: {
                    activate: function(tab) {
                        gridInfoEspacio.view.refresh();
                    }                    
                }},
            {contentEl: 'tab3', title: 'Datos Contactos', listeners: {
                    activate: function(tab) {
                        gridContacto.view.refresh();                        
                    }

                }}
        ]
    });
    
    var storeTipoEspacio = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: url_infoEspacioNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idNodo: idNodo
            }
        },
        fields:
            [
                {name: 'nombreTipoEspacio', mapping: 'nombreTipoEspacio'},
                {name: 'largo', mapping: 'largo'},
                {name: 'ancho', mapping: 'ancho'},
                {name: 'alto',  mapping: 'alto'},
                {name: 'valor', mapping: 'valor'}
            ],
         autoLoad: true
    });
    
    var gridInfoEspacio = Ext.create('Ext.grid.Panel', {
        id: 'gridInfoEspacio',
        store: storeTipoEspacio,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'nombreTipoEspacio',
                header: 'Tipo Espacio',
                dataIndex: 'nombreTipoEspacio',
                width: 200,
                sortable: true
            },
            {
                id: 'largo',
                header: 'Largo',
                dataIndex: 'largo',
                width: 100 ,
                align:'right' 
            }, {
                id: 'ancho',
                header: 'Ancho',
                dataIndex: 'ancho',
                width: 100,
                align:'right'
            },
            {
                id: 'alto',
                header: 'Alto',
                dataIndex: 'alto',
                width: 100,
                align:'right'
            }, {
                id: 'valorEspacio',
                header: 'Valor ($)',
                dataIndex: 'valor',
                width: 100,
                align:'right'               
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTipoEspacio,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        viewConfig: {
            stripeRows: true
        },
        width: 620,
        height: 200,
        title: 'Informacion de Espacio',
        renderTo: Ext.get('infoEspacio')
    }); 
    
    //Contactos de nodo
    var storeContactoNodo = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: url_infoContactoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idNodo: idNodo
            }
        },
        fields:
            [
                {name: 'descripcionRol', mapping: 'descripcionRol'},
                {name: 'tipoIdentificacion', mapping: 'tipoIdentificacion'},
                {name: 'identificacionCliente', mapping: 'identificacionCliente'},
                {name: 'nombres',  mapping: 'nombres'},
                {name: 'apellidos',  mapping: 'apellidos'},
                {name: 'idPersona',  mapping: 'idPersona'},                
                {name: 'razonSocial',  mapping: 'razonSocial'},
                {name: 'tipoTributario',  mapping: 'tipoTributario'}
                
            ],
         autoLoad: true
    });
    
    var gridContacto = Ext.create('Ext.grid.Panel', {
        id: 'gridContacto',
        store: storeContactoNodo,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idPersona',
                header: 'idPersona',
                dataIndex: 'idPersona',
                hidden: true,
                hideable: false
            },
            {
                id: 'descripcionRol',
                header: 'Tipo Contacto',
                dataIndex: 'descripcionRol',
                width: 100,
                sortable: true
            },
            {
                id: 'tipoIdentificacion',
                header: 'Tipo Ident.',
                dataIndex: 'tipoIdentificacion',
                width: 50 ,    
                sortable: true
            }, {
                id: 'identificacionCliente',
                header: 'Identificacion',
                dataIndex: 'identificacionCliente',
                width: 80                
            },
            {
                id: 'nombres',
                header: 'Nombres',
                dataIndex: 'nombres',
                width: 170                
            }, 
            {
                id: 'apellidos',
                header: 'Apellidos',
                dataIndex: 'apellidos',
                width: 170        
            },
            {
                id: 'razonSocial',
                header: 'Razon Social',
                dataIndex: 'razonSocial',
                width: 200                               
            } ,
            {
                id: 'tipoTributario',
                header: 'Tipo Tributario',
                dataIndex: 'tipoTributario',
                width: 80                               
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 60,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-show';
                        },
                        tooltip: 'Ver Forma de Contacto',
                        handler: function(grid, rowIndex, colIndex) 
                        {                            
                            obtenerFormasContacto(grid.getStore().getAt(rowIndex).data.idPersona);                            
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeContactoNodo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        viewConfig: {
            stripeRows: true
        },
        width: 1000,
        height: 250,
        title: 'Informacion de Contacto',
        renderTo: Ext.get('contactoNodo')
    });             
    
    
    //ToolTips para FLUJO de Solicitud de Nodo
    
    var items = "<tr><td colspan='2' style='padding:6px;'><h1>Flujo de Solicitud de Nuevo Nodo</h1></td></tr>";           
    
    if (estadoSolicitud == 'Pendiente')
    {
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud no ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",'',items,mensaje,false));
        mensaje = "La Solicitud no ha sido aprobada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",'',items,mensaje,false));
        mensaje = "No ha sido generado contrato.";
        showTitle("#flujo-contrato",setImage("#img_contrato",'',items,mensaje,false));
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));
    }
    
    if (estadoSolicitud == 'AutorizadaTecnico')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",url_img_tecAprobado,items,mensaje,true));
        $("#next-autorizado-tecnico").attr("src", url_next);        
        mensaje = "La Solicitud no ha sido aprobada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",'',items,mensaje,false));
        mensaje = "No ha sido generado contrato.";
        showTitle("#flujo-contrato",setImage("#img_contrato",'',items,mensaje,false));
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));

    }
    
    if (estadoSolicitud == 'AutorizadaLegal')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",url_img_tecAprobado,items,mensaje,true));
        $("#next-autorizado-tecnico").attr("src", url_next);        
        mensaje = "La Solicitud ha sido aprobada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",url_img_legalAprobado,items,mensaje,true));
        $("#next-autorizado-legal").attr("src", url_next);       
        mensaje = "No ha sido generado contrato.";
        showTitle("#flujo-contrato",setImage("#img_contrato",'',items,mensaje,false));
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));

    }
    
    if (estadoSolicitud == 'RechazadaTecnico')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido rechazada tecnicamente";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,false));
        mensaje = "La Solicitud no ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",'',items,mensaje,false));               
        mensaje = "La Solicitud no ha sido aprobada legalmente.";        
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",'',items,mensaje,false));                
        mensaje = "No ha sido generado contrato.";
        showTitle("#flujo-contrato",setImage("#img_contrato",'',items,mensaje,false));
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));
    }
    
    if (estadoSolicitud == 'RechazadaLegal')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",url_img_tecAprobado,items,mensaje,true)); 
        $("#next-autorizado-tecnico").attr("src", url_next);      
        mensaje = "La Solicitud ha sido rechazada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",'',items,mensaje,false));              
        mensaje = "No ha sido generado contrato.";
        showTitle("#flujo-contrato",setImage("#img_contrato",'',items,mensaje,false));
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));
    }
    
    if (estadoSolicitud == 'FirmadoContrato')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",url_img_tecAprobado,items,mensaje,true));
        $("#next-autorizado-tecnico").attr("src", url_next);        
        mensaje = "La Solicitud ha sido aprobada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",url_img_legalAprobado,items,mensaje,true));
        $("#next-autorizado-legal").attr("src", url_next);       
        mensaje = "Contrato ha sido generado y firmado";
        showTitle("#flujo-contrato",setImage("#img_contrato",url_img_contrato,items,mensaje,true));
        $("#next-contrato").attr("src", url_next);     
        mensaje = "Nodo no ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",'',items,mensaje,false));
    }
    
    if (estadoSolicitud == 'Finalizada')
    {        
        mensaje = "La Solicitud # " + idSolicitud + " ha sido ingresada.";
        showTitle("#flujo-nodo-ingresado",setImage("#img_nodo_ingresado",url_img_nodoIngresado,items,mensaje,true));
        mensaje = "La Solicitud ha sido aprobada tecnicamente.";
        showTitle("#flujo-autorizado-tecnico",setImage("#img_autorizado_tecnico",url_img_tecAprobado,items,mensaje,true));
        $("#next-autorizado-tecnico").attr("src", url_next);        
        mensaje = "La Solicitud ha sido aprobada legalmente.";
        showTitle("#flujo-autorizado-legal",setImage("#img_autorizado_legal",url_img_legalAprobado,items,mensaje,true));
        $("#next-autorizado-legal").attr("src", url_next);       
        mensaje = "Contrato ha sido generado y firmado";
        showTitle("#flujo-contrato",setImage("#img_contrato",url_img_contrato,items,mensaje,true));
        $("#next-contrato").attr("src", url_next);     
        mensaje = "Nodo ha sido habilitado.";
        showTitle("#flujo-habilitado",setImage("#img_habilitado",url_img_tecAprobado,items,mensaje,true));
    }

    


});

function showTitle(el,items)
{
    $(el).attr("title",
        "<div><table>" + items + "</table></div>");
    $(el).tooltip({
        track: true,
        delay: 0,
        showURL: false,
        showBody: " - ",
        extraClass: "pretty",
        fixPNG: true,
        left: -6
    });
}

function setImage(el,url,items,msg,boolAction)
{
    if(boolAction)
    $(el).attr("src", url);
    
    items = items + "<tr>\n\
                        <td style='padding:6px;'><img width='18' height='18' src='"+(boolAction?url_check:url_delete)+"'></td>\n\
                        <td  style='padding:6px;'>"+msg+"</td>\n\
                    </tr>";
    return items;
}

function obtenerFormasContacto(idPersona)
{
    winFormaContacto      = "";         
    
    var storeFormaContacto = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: url_formaContactoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idPersona: idPersona
            }
        },
        fields:
            [
                {name: 'descripcionFormaContacto', mapping: 'descripcionFormaContacto'},
                {name: 'valor', mapping: 'valor'},
                {name: 'estado', mapping: 'estado'}               
            ]        
    });  
    
    storeFormaContacto.load();
    
    var gridFormaContacto = Ext.create('Ext.grid.Panel', {
        id: 'gridFormaContacto',
        store: storeFormaContacto,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'descripcionFormaContacto',
                header: 'Tipo',
                dataIndex: 'descripcionFormaContacto',
                width: 150,
                sortable: true
            },
            {
                id: 'valorFormaContacto',
                header: 'Valor',
                dataIndex: 'valor',
                width: 200,
                sortable: true
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 80 ,    
                sortable: true
            }
        ],       
        viewConfig: {
            stripeRows: true
        },
        width: 450,
        height: 200
    }); 

    var formPanelFormaContacto = Ext.create('Ext.form.Panel', {
        width: 500,
        height: 300,
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [gridFormaContacto],
        buttons: [
            {
                text: 'Cerrar',
                handler: function() {
                    winFormaContacto.close();
                }
            }
        ]
    });

    winFormaContacto = Ext.widget('window', {
        title: 'Formas De Contacto',
        width: 500,
        height: 300,
        layout: 'fit',
        resizable: false,
        modal: true,
        closabled: false,
        items: [formPanelFormaContacto]
    });
    
    
    winFormaContacto.show();    
}