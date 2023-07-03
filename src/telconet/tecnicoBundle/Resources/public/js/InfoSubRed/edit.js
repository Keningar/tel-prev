Ext.onReady(function(){ 
   
    //deshabilito el modelo
    //document.getElementById('telconet_schemabundle_infosubredtype_estado').disabled = true;  
    var storePe = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            async: false,
            type: 'ajax',
            url: url_getEncontradosPe,
            timeout: 400000,
            extraParams: {
                strNombreElemento: 'pe',
                strEstadoElemento: 'Activo',
                $intPe: 'id_elemento'
                
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
            [
                {name: 'id_elemento', mapping: 'id_elemento'},
                {name: 'nombre_elemento', mapping: 'nombre_elemento'}
            ]
    });
    var campoPe = document.getElementById('pe_set').value;
    comboPe = new Ext.form.ComboBox({
        id: 'comboPe',
        name: 'comboPe',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        store: storePe,
        displayField: 'nombre_elemento',
        valueField: 'id_elemento',
        editable: true,
        renderTo: 'comboPe',
        value: campoPe
    });
    
    var storeUsoSubred = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosUsos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                descripcion:''
                
            }
        },
        fields:
            [
                {name: 'descripcion', mapping: 'descripcion'},
                {name: 'uso', mapping: 'uso'}
            ]
    });
    var campoUso = document.getElementById('uso_set').value;
    combo_usos = new Ext.form.ComboBox({
        id: 'comboUsos',
        name: 'comboUsos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        store: storeUsoSubred,
        displayField: 'descripcion',
        valueField: 'uso',
        renderTo: 'comboUsos',
        editable: false,
        value: campoUso
    });
    
   
    
    var storeTipoSubred = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosTipos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                descripcion_tipo: ''
                
            }
        },
        fields:
            [
                {name: 'descripcion_tipo', mapping: 'descripcion_tipo'},
                {name: 'tipo', mapping: 'tipo'}
            ],
        
    });
    var campoTipo = document.getElementById('tipo_set').value;
   

    
    combo_tipos = new Ext.form.ComboBox({
        id: 'comboTipos',
        name: 'comboTipos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        store: storeTipoSubred,
        displayField: 'descripcion_tipo',
        valueField: 'tipo',
        renderTo: 'comboTipos',
        editable: false,
        value: campoTipo
        
    });
    

    var storeEstadoSubred = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosEstados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                descripcion_estado: ''
                
            }
        },
        fields:
            [
                {name: 'descripcion_estado', mapping: 'descripcion_estado'},
                {name: 'estado', mapping: 'estado'}
            ]
    });
    var campoEstado = document.getElementById('estado_set').value;
    combo_estados = new Ext.form.ComboBox({
        id: 'comboEstados',
        name: 'comboEstados',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        store: storeEstadoSubred,
        displayField: 'descripcion_estado',
        valueField: 'estado',
        renderTo: 'comboEstados',
        editable: false,
        value: campoEstado
       
    });
    
    
    

   

});


function confirmarForm(strInt) 
{
    

    
        
         Ext.Msg.confirm("Mensaje", "¿Desea guardar los cambios?", function (btn) {
                            
                            if ('yes' == btn)
                            {
                               
                                 //campos capturados
                                var form = document.forms[0];
                                var strComboPe       = form[6].value;
                                var strSubred        = document.getElementById("telconet_schemabundle_infosubredtype_subred").value;
                                var strUso           = form[9].value;
                                var strTipo        = form[8].value;
                                var strEstado             = form[10].value;

                                //campos seteados
                                var setComboPe = document.getElementById('pe_set').value;
                                var setUso =document.getElementById('uso_set').value;
                                var setTipo =document.getElementById('tipo_set').value;
                                var setSubred =document.getElementById('subred_completa').value;
                                var setEstado =document.getElementById('estado_set').value;
                                var btn_guardar =document.getElementById('enviar');

                                
                                Ext.Msg.show({
                                    title: 'Mensaje',
                                    msg: '<b>Datos anteriores:</b><br>Pe:'+setComboPe+'<br>Subred:'+setSubred+'<br>Uso:'+setUso+'<br>Tipo:'+setTipo+'<br>Estado:'+setEstado+
                                    '<br><br><b>Datos nuevos:</b><br>Pe:'+strComboPe+'<br>Subred:'+strSubred+'<br>Uso:'+strUso+'<br>Tipo:'+strTipo+'<br>Estado:'+strEstado,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.INFO,
                                    fn: function(btn, text) {
                                        if (btn === 'ok') {
                                            Ext.get(document.body).mask("Espere por favor...");

                                            Ext.Ajax.request({
                                                url: url_updateAjax,
                                                timeout: 3000000,
                                                method: 'post',
                                                params: { strInt:strInt,
                                                         comboPe:strComboPe,
                                                         subred:strSubred ,
                                                         comboUsos:strUso,
                                                         comboTipos:strTipo,
                                                         comboEstados:strEstado
                                                        },
                                                success: function(response){
                                                    Ext.get(document.body).unmask();
                                                    var objData = Ext.JSON.decode(response.responseText);
                                                    var strStatus = objData.status;
                                                    var strMensaje = objData.mensaje;  
                                                    
                                                    if(strStatus == "OK"){
                                                       

                                                            Ext.Msg.alert('Mensaje','Se editaron los datos correctamente!', function(btn){
                                                            if(btn=='ok'){;
                                                                window.location.href = url_redirectGrid;
                                                            }
                                                        });
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Error ',strMensaje);
                                                        btn_guardar.setAttribute('disabled','true');

                                                        
                                                    }
                                                    
                                                    
                                                },
                                                failure: function(response)
                                                {
                                                    Ext.get(document.body).unmask();
                                                    var objData = Ext.JSON.decode(response.responseText);
                                                    
                                                    var strMensaje = objData.mensaje;  
                                                    Ext.Msg.alert('Error ',strMensaje);
                                                    
                                                }
                                        });


                                        }
                                    }
                                });
                                return true;
                                

                            
                            }
                                
                               
                                
                            });

        
   
}


function CambioEstado(){
    var selectEstado = document.getElementById('comboEstados').value;
    document.getElementById('estadoo').value = selectEstado;
}

function validacionesEditForm()
{
    var campo_subred = document.getElementById("subred_completa").value;
    const element=document.getElementById("telconet_schemabundle_infosubredtype_subred");
    
    if(document.getElementById("telconet_schemabundle_infosubredtype_subred").value==""){
        Ext.Msg.show({
            title: 'Error de edición',
            msg: 'Campo Subred no debe quedar vacío ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    if (element.value != "") 
    {
        // Patron para validar la ip

        const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])?(?:\/[1-9][0-9])$/gm);

        if (element.value.search(patronIp)!=0) {

        // Ip no es correcta
        Ext.Msg.show({
            title: 'Error de edición',
            msg: 'La Subred no es correcta ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        document.getElementById("telconet_schemabundle_infosubredtype_subred").value="";
        document.getElementById("telconet_schemabundle_infosubredtype_subred").value = campo_subred;
        
        return false;

        } 
    }
 
    return true;
        
   
}

