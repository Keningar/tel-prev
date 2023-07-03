/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.require(["Ext.ux.grid.plugin.PagingSelectionPersistence"]);

Ext.onReady(function() {

    Ext.tip.QuickTipManager.init();
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields: [
            { name: "idRegistroCalendario", mapping: "idRegistroCalendario" },
            { name: "nombreDia", mapping: "nombreDia" },
            { name: "valor", mapping: "valor" },
            { name: "estado", mapping: "estado" },
            { name: "usrCreacion", mapping: "usrCreacion" },
            { name: "usrUtlMod", mapping: "usrUtlMod" },
            { name: "feUltMod", mapping: "feUltMod" },
            { name: "feCreacion", mapping: "feCreacion" }
        ],
        idProperty: 'idRegistroCalendario'
    });

    store = new Ext.data.Store({
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: 'grid',
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'data'
            },
        },
        /* data: [
            { idRegistroCalendario: 'idRegistroCalendario', nombreDia: 'nombreDia', valor: 'valor', estado: 'estado', usrCreacion: 'usrCreacion' },

        ], */
        autoLoad: true
    });

    Ext.create("Ext.grid.Panel", {
        id: "grid",
        width: 1000,
        height: 390,
        store: store,
        plugins: [{ ptype: "pagingselectpersist" }],
        viewConfig: { enableTextSelection: true },     
        columns: [{
                id: "idRegistroCalendario",
                header: "idRegistroCalendario",
                dataIndex: "idRegistroCalendario",
                hidden: true,
                hideable: false,
            },
            {
                id: "nombreDia",
                header: "Día",
                dataIndex: "nombreDia",
                width: 130,
                
            },
            {
                id: "valor",
                header: "valor",
                dataIndex: "valor",
                width: 130,
                
            },
            {
                id: "usrCreacion",
                header: "Usuario Creación",
                dataIndex: "usrCreacion",
                width: 120,
                
            },
            {
                id: "usrUtlMod",
                header: "Usuario Modificación",
                dataIndex: "usrUtlMod",
                width: 120,
                
            },

            {
                id: "feUltMod",
                header: "Fecha Modificación",
                dataIndex: "feUltMod",
                width: 120,
                
            },           

            {
                id: "feCreacion",
                header: "Fecha Creación",
                dataIndex: "feCreacion",
                width: 120,
                
            },
            {
                id: "estado",
                header: "Estado",
                dataIndex: "estado",
                width: 120,
                
            },


            {
                xtype: "actioncolumn",
                header: "Acciones",
                width: 120,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-delete';
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) {
                            let rec = store.getAt(rowIndex);
                            Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                if(btn=='yes'){
                                   eliminar(rec);
                                }
                            });
                        

                        }
                    }

                ],
            },
        ],
        bbar: Ext.create("Ext.PagingToolbar", {
            store: store,
            displayInfo: true,
            displayMsg: "Mostrando {0} - {1} de {2}",
            emptyMsg: "No hay datos que mostrar.",
        }),
        renderTo: "grid",
    });

    
    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    Ext.create("Ext.panel.Panel", {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: "center",
        layout: {
            type: "table",
            columns: 3,
            align: "center",
        },
        bodyStyle: {
            background: "#fff",
        },

        collapsible: false,
        collapsed: false,
        width: 1000,
        title: "Horarios Ṕlanificación Comercial",
        buttons: [{
                text: "Guardar",
                iconCls: "icon_search",
                handler: function() {
                    guardar();
                },
            },
            {
                text: "Cancelar",
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                },
            },
        ],
        items: [
            { html: "&nbsp;", border: false, width: 110 },
            {
                xtype: "combobox",
                fieldLabel: "Día",
                id: "sltnombreDias",
                value: "",
                store: [
                    ["Lunes", "Lunes"],
                    ["Martes", "Martes"],
                    ["Miércoles", "Miércoles"],
                    ["Jueves", "Jueves"],
                    ["Viernes", "Viernes"],
                    ["Sábado", "Sábado"],
                    ["Domingo", "Domingo"],
                ],
                width: "365",
            },
            {
                xtype: "timefield",
                name: "out",
                id: "sltHoras",
                format    : 'H:i',
                fieldLabel: "Horas",
                minValue  : minHora,
                maxValue  : maxHora,
                increment: parseInt(tiempoMinuto),
                anchor: "100%",
            },
            { html: "&nbsp;", border: false, width: 110 },
            { html: "&nbsp;", border: false, width: 355 },
        ],
        renderTo: "filtro",
    });
});


let requestAJax = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Cargando.....!!',
                    progressText: 'Espere...',
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


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */


function limpiar() {
    Ext.getCmp("txtNombre").value = "";
    Ext.getCmp("txtNombre").setRawValue("");

    Ext.getCmp("txtApellido").value = "";
    Ext.getCmp("txtApellido").setRawValue("");

    Ext.getCmp("txtlogin").value = "";
    Ext.getCmp("txtlogin").setRawValue("");

    Ext.getCmp("sltmes").value = "";
    Ext.getCmp("sltmes").setRawValue("");

    store.getProxy().extraParams.nombre = Ext.getCmp("txtNombre").value;
    store.getProxy().extraParams.apellido = Ext.getCmp("txtApellido").value;
    store.getProxy().extraParams.login = Ext.getCmp("txtlogin").value;
    store.getProxy().extraParams.mes = Ext.getCmp("sltmes").value;
    store.load();
}


function guardar(){

    

let nombreDia=Ext.getCmp("sltnombreDias").value

let valorHora =Ext.getCmp("sltHoras").value;


if(nombreDia==null){
    Ext.Msg.alert('Error ',"Se requiere que se seleccione el día");
    return;
}


if(valorHora==null){
    Ext.Msg.alert('Error ',"Se requiere que se seleccione la hora");
    return;
}



let hours = valorHora.getHours();
let minutes = valorHora.getMinutes();

console.log(valorHora);


hours = hours < 10 ? "0" + hours : hours;
minutes = minutes < 10 ? "0" + minutes : minutes;

let valor=hours+':'+minutes;


Ext.getCmp("sltnombreDias").setValue(null);

Ext.getCmp("sltHoras").setValue(null);

requestAJax.request({
        url:url_saveCalendario,
        method: 'post',                          
        params: {
            valor        : valor,
            nombreDia    : nombreDia            
        },   
        success: function(response){
            console.log('consumi el url_saveCalendario');
            console.log(response);

            if(response.status===200)
            {
                 const jsonResponse = JSON.parse(response.responseText);
                 if(jsonResponse.status!="OK"){

                    Ext.Msg.alert('Error ',jsonResponse.message);
                 }                  
                

            }else{
                Ext.Msg.alert('Error ',"Ocurrió un error al realizar la petición");
            }
            store.load();                             
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });   
}


function eliminar(rec){
    requestAJax.request({
        url:url_inactivarRegistro,
        method: 'post',                          
        params: {
            idRegistroCalendario        : rec.get('idRegistroCalendario')
            
        },   
        success: function(response){
            console.log('consumi el inactivar');
            console.log(response);

         //   showProgramar(rec, 'local', 0);

            if(response.status===200)
            {
                 const jsonResponse = JSON.parse(response.responseText);
                 if(jsonResponse.status!="OK"){

                    Ext.Msg.alert('Error ',jsonResponse.message);
                 }                  
                

            }else{
                Ext.Msg.alert('Error ',"Ocurrió un error al realizar la petición");
            }
            store.load();                             
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });    
}