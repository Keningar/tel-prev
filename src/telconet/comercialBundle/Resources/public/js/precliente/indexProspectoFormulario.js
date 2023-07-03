Ext.require([
    '*'
]);

var esObligatorio = true;



Ext.onReady(function()
{

    var dataNumero = [];
    Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });



    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'},
            {name: 'esWhatsapp'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'},
            {name: 'maximo', type: 'int'} 
        ]
    });
     
    // create the Data Store
    storePersonaFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_persona,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: ''
                        },
                    simpleSortMode: true
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = personaid;
                    }
                }
        });

    if (typeof formasDeContacto !== typeof undefined && formasDeContacto != '')
    {
        arrayFormasContacto = formasDeContacto.split(',');
        for (i = 0; i < arrayFormasContacto.length; i += 3)
        {
            var registro =
                {
                    'idPersonaFormaContacto': arrayFormasContacto[i],
                    'formaContacto': arrayFormasContacto[i + 1],
                    'valor': arrayFormasContacto[i + 2]
                };
            var rec = new PersonaFormasContactoModel(registro);
            if (rec.formaContacto !== "")
            {
                storePersonaFormasContacto.add(rec);
            }
        }
    }

    // create the Data Store
    var storeFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.

    gridFormasContacto = Ext.create('Ext.grid.Panel',
        {
            id: 'gridFormasContacto',
            name: 'gridFormasContacto',
            store: storePersonaFormasContacto,
            columns:
                [
                    {
                        text: 'Forma Contacto',
                        header: 'Forma Contacto',
                        dataIndex: 'formaContacto',
                        width: 150,
                        editor: new Ext.form.field.ComboBox(
                            {
                                typeAhead: true,
                                triggerAction: 'all',
                                selectOnTab: true,
                                id: 'id',
                                name: 'formaContacto',
                                valueField: 'descripcion',
                                displayField: 'descripcion',
                                store: storeFormasContacto,
                                lazyRender: true,
                                listClass: 'x-combo-list-small',
                                listeners:{
                                    select:{
                                        fn:function(combo, value) {
                                            //$('input[name="lblCantidad"]').val(combo.valueModels[0].data.cantidad);
                                        }
                                    },
                                    beforeselect: function (combo, record, index, eopts) {
                                        var arrayGrid = Ext.getCmp('gridFormasContacto');
                                        var countContacto = 0;
                                        for (var intCounterStore = 0;
                                            intCounterStore < arrayGrid.getStore().getCount(); intCounterStore++)
                                        {
                                            console.log(arrayGrid.getStore().getAt(intCounterStore).data.formaContacto);    
                                            if (record.get('descripcion') == arrayGrid.getStore().getAt(intCounterStore).data.formaContacto) {
                                                countContacto++;
                                            }

                                        }

                                        if (countContacto == record.get('maximo')){
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: "Llegó al límite de Forma de contacto " + record.get('descripcion') + " Máximo permitido " + record.get('maximo'),
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                            return false;  
                                        }
                                        

                                    }
                                }
                
                            })
                    },
                    {
                        text: 'Valor',
                        //header: 'Valor',
                        dataIndex: 'valor',
                        width: 400,
                        align: 'right',
                        editor:
                            {
                                width: '80%',
                                xtype: 'textfield',
                                allowBlank: false
                            }
                    },      
                    {
                        xtype: 'actioncolumn',
                        width: 45,
                        sortable: false,
                        items:
                            [
                                {
                                    iconCls: "button-grid-delete",
                                    tooltip: 'Borrar Forma Contacto',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storePersonaFormasContacto.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            width: 800,
            height: 300,
            align: 'center',
            title: '',
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            var boolError = false;
                            var indice = 0;
                            for (var i = 0; i < storePersonaFormasContacto.getCount(); i++)
                            {
                                variable = storePersonaFormasContacto.getAt(i).data;
                                boolError = trimAll(variable['formaContacto']) == '';

                                if (boolError)
                                {
                                    break;
                                }
                                else
                                {
                                    boolError = trimAll(variable['valor']) == '';
                                    if (boolError)
                                    {
                                        indice = 1;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                var r = Ext.create('PersonaFormasContactoModel',
                                    {
                                        idPersonaFormaContacto: '',
                                        formaContacto: '',
                                        valor: ''
                                    });
                                storePersonaFormasContacto.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }],
            plugins: [cellEditing]
        });
        dataNumero = [];
        var cmbNumero = new Ext.form.ComboBox({
            id: 'cmbNumero',
            name: 'cmbNumero',
            fieldLabel: 'Teléfono',
            emptyText: '',
            store: dataNumero,
            height: 30,
            width: 325,
            border: 0,
            margin: 0,
            triggerAction: 'all',
        });
 
        var formSeleccionaNumero = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            items: [
                cmbNumero
            ],
            buttons: [{
                    text: 'Grabar',
                    name: 'grabar',
                    handler: function() {                        
                        
                        guardar();
                  }},
                {
                    text: 'Cancel',
                    handler: function() { 
                       this.up('window').close(); 
                    }
                }]
        });


        formPlantillaDet = Ext.create('Ext.form.Panel', {

            style: {
                "margin-left": "auto",
                        "margin-right": "auto"
                   },
            bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
            width: 850,
            title: 'Formulario de Aceptacion de Prospecto',
            renderTo: Ext.get('formas_contacto_prospecto'),
            align: 'middle',
            pack: 'center',
            listeners: {
                afterRender: function(thisForm, options) {
                }
            },
    
            layout:'hbox',
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
                        
                        if (grabarFormasContacto() && validaFormasContacto()){
                                var arrayGrid = Ext.getCmp('gridFormasContacto');
                                cmbNumero.getStore().removeAll(); 
                                for (var intCounterStore = 0;
                                    intCounterStore < arrayGrid.getStore().getCount(); intCounterStore++)
                                {    
                                    if (arrayGrid.getStore().getAt(intCounterStore).data.formaContacto == "Teléfono Movil Claro" ||
                                        arrayGrid.getStore().getAt(intCounterStore).data.formaContacto == "Teléfono Movil Movistar" ||
                                        arrayGrid.getStore().getAt(intCounterStore).data.formaContacto == "Teléfono Movil CNT")
                                        {
                                            let arrayValor=[[arrayGrid.getStore().getAt(intCounterStore).data.valor]]; 
                                            cmbNumero.getStore().insert(0,arrayValor );       
                                    }
                                }                                
                                if (enviaWhatsapp == "SI") {
                                    winSeleccionaNumero = Ext.widget('window', {
                                        title: 'Elija el Número para enviar el mensaje Whatsapp',
                                        closeAction: 'hide',
                                        closable: false,
                                        width: 350,
                                        height: 170,
                                        minHeight: 150,
                                        layout: 'fit',
                                        resizable: true,
                                        modal: true,
                                        items: formSeleccionaNumero
                                    });
    
                                    winSeleccionaNumero.show();    
                                } else {
                                    guardar();
                                }
    

                        }
                        }
                    
                },
                {
                    text: 'Regresar',
                    handler: function() { 
                        //this.up('form').getForm().reset();
                        //storeHorario.removeAll();
                        window.location.href = strUrlIndex;
                    }
                }]
        });
        var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center', },
            width: 800,
            items: [
                {
                    xtype: 'panel',
                    border: false,

                    layout: {
                        type: 'hbox',
                        align: 'center',
                        pack: 'center'
                    }, 
                    //layout: { type: 'hbox', align: 'stretch' },
                    items: [
                        {
                            items: [gridFormasContacto]
                        }]
                }]
        });


    formPlantillaDet.add(container);

        
    function trimAll(texto)
    {
        return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
    /**
     * Permite validar las formas de contactos.
     *
     * @version 1.00
     * 
     * Se llama a validación de formas de contactos centralizada.
     *
     * @author Héctor Ortega <haortega@telconet.ec>
     * @version 1.01, 29/11/2016
     */

}); 
function validaFormasContacto(){
    if (Utils.validaFormasContacto(gridFormasContacto))
    {
        var arrayGrid = Ext.getCmp('gridFormasContacto');
        
        for (var i = 0; i < arrayGrid.getStore().getCount(); i++)
        {
            variable = arrayGrid.getStore().getAt(i).data;
            if (variable.formaContacto.toUpperCase().match(/^TELEFONO.*$/))
            {
                if (variable.formaContacto.toUpperCase().match(/^TELEFONO MOVIL.*$/))
                {
                    return validaTelefonoMovil(variable.valor);
                }
                else
                {
                    return validaTelefono(variable.valor);
                }
            }
        }
        return true; 
        
    }else {
    return false; 
    }

}
 
function validaTelefono(telefono)
{
    var RegExPattern = /^(0[2-8]{1}[0-9]{7})$/;
    if ((telefono.match(RegExPattern)) && (telefono.value != ''))
    {
        return true;
    }
    else
    {
        Ext.Msg.alert("Error", "El Teléfono <b>" + telefono + "</b> está mal formado, por favor corregir.");
        return false;
    }
}

function validaTelefonoMovil(telefono)
{
    var RegExPattern = /^(09[0-9]{8})$/;
    if ((telefono.match(RegExPattern)) && (telefono.value != ''))
    {
        return true;
    }
    else
    {
        Ext.Msg.alert("Error", "El Teléfono <b>" + telefono + "</b> está mal formado, por favor corregir.");
        return false;
    }
}

function grabarFormasContacto(campo)
{

    var array_data = new Array();
    var variable='';
    var valoresVacios=false;
    for(var i=0; i < gridFormasContacto.getStore().getCount(); i++){ 
        variable=gridFormasContacto.getStore().getAt(i).data;
        for(var key in variable) {
            var valor = variable[key];
            if (key=='valor' && valor==''){
                    valoresVacios=true;
            }else{
                    array_data.push(valor);
            }
        } 
    }
    $(campo).val(array_data); 
    if (($(campo).val()=='0,,') || ($(campo).val()=='')) {
        alert('No hay formas de contacto aun ingresadas.');
        $(campo).val('');
        return false;
    }else{
        if(valoresVacios==true){
            alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
            $(campo).val('');
            return false;
        }
    }
    return true;
}

function guardar(){
    Ext.MessageBox.show({
        icon: Ext.Msg.INFO,
        title:'Mensaje',
        msg: '¿Está seguro que desea solicitar la aceptación de la política de mejora de la experiencia?',
        buttons    : Ext.MessageBox.YESNO,
        buttonText: {yes: "Si"},
        fn: function(btn){
            if(btn=='yes'){
                Ext.get(document.body).mask('Guardando datos...');
                var arrayGrid = Ext.getCmp('gridFormasContacto');
                var arrayData = Array(); 
                for (var intCounterStore = 0;
                    intCounterStore < arrayGrid.getStore().getCount(); intCounterStore++)
                {   
                    if (Ext.getCmp('cmbNumero').value == arrayGrid.getStore().getAt(intCounterStore).data.valor){
                        arrayGrid.getStore().getAt(intCounterStore).data.esWhatsapp = true; 
                    }
                    arrayData.push(arrayGrid.getStore().getAt(intCounterStore).data);
                }

                jsonData = Ext.JSON.encode(arrayData);
                Ext.Ajax.request({
                    url: url_guardar_formas_contacto,
                    timeout: 1000000,
                    method: 'POST',
                    params: {
                        data: jsonData
                    },

                    success: function(response) {
                        Ext.get(document.body).unmask();
                        var json = Ext.JSON.decode(response.responseText);
                        console.log(json);
                        if (json.status == 0)
                        {
                            Ext.Msg.alert('Formas de contacto guardadas con exito ', json.message);
                            window.location.href = strUrlIndex;
                        } else
                        {
                            Ext.Msg.alert('Error - Formas de Contacto Prospecto ', json.message);
                        }
                    },
                    failure: function(result) {
                        Ext.get(document.body).unmask();
                        Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
                    }
                });


            } else {
                return false;
            }
        }    
    });  
}