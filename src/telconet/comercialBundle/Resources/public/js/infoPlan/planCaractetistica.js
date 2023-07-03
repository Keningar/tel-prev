
function mostrarModalEditar(intId){
    
    var connAutorizar = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {						
                    Ext.MessageBox.show({
                        msg: 'Enviando correo al departamento de Ade para su posterior aprobación, Por favor espere!!',
                        progressText: 'Enviando...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });				
                },
                scope: this
            }
        }
    });

    var formCorreoContrato = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        width: 350,
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },          
        defaultType: 'textfield',
        items: [
            {    
                xtype: 'textfield',
                fieldLabel: 'Valor: ',
                name: 'valorCaracteristica',
                id: 'valorCaracteristica',
                displayField: 'valorCaracteristica',
                valueField: 'valorCaracteristica',
                queryMode: 'local',
                maxLength:100
            }                                          
        ],
        buttons: [          
            {  
                text: 'Aceptar',
                formBind: true,
                handler: function()
                {
                    var valor = Ext.getCmp('valorCaracteristica').value;
                    if (valor == "")
                    {
                        Ext.Msg.alert("Alerta", "Favor ingrese el correo del cliente!");
                    }
                    else
                    {                  
                        var arreglo = JSON.parse(document.getElementById("valores").value);                      
                            var informacion             = [];
                            for ( var x = 0; x < arreglo.length; x++ )
                            {     console.log("Se imprimio 2"); 
                                if(arreglo[x].caracteristica==intId)
                                    {
                                        arreglo[x].valor=valor;
                                        arreglo[x].editar="S";
                                    }

                                    document.getElementById("table-3").deleteRow(1);   
                                    displayResult(new Array(arreglo[x].caracteristica, 
                                                            arreglo[x].nombreCaracteristica,),
                                                  new Array(arreglo[x].tipo_caracteristica)
                                                            ,arreglo[x].valor,arreglo[x].btnEditar);
                                    informacion.push(arreglo[x]);
                            }
                            document.getElementById("valores").value = JSON.stringify(informacion);               
                            winVisualizarCorreo.destroy();
                            Ext.Msg.alert('MENSAJE ', 'Se Actualizó el Valor.');
                    }
                }
            }]
    });
    var winVisualizarCorreo = Ext.create('Ext.window.Window',
    {
        title: 'Ingrese el valor de la caracteristica',
        modal: true,
        width: 320,
        closable: true,
        layout: 'fit',
        items: [formCorreoContrato]
    }).show();                  
}     

 function displayResult(caracteristica,tipo_caracteristica,valor,editar)
 {
         var table       = document.getElementById("table-3");
         var largo       = table.rows.length;
         var row         = table.insertRow(largo);
         var cell1       = row.insertCell(0);
         var cell2       = row.insertCell(1);
         var cell3       = row.insertCell(2);
         var cell4       = row.insertCell(3);                
         cell1.innerHTML = caracteristica[1];
         cell2.innerHTML = tipo_caracteristica[0];
         cell3.innerHTML = valor;      
         if(editar=="S")     
         {
             cell4.innerHTML = " <button type='button' onclick='mostrarModalEditar("+caracteristica[0]+");'class='button-crud'>Editar</button>";
         } 
         else
         {
          cell4.innerHTML = "<button type='button' onclick='removeRow(this,"+caracteristica[0]+");' class='button-crud'>Eliminar</button>";
         }      
         limpiar_detalle();
      
 }
