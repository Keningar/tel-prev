Ext.onReady(function(){ 
    var conn = new Ext.data.Connection();
    conn.request
      (
        {
            url: getDatosTipoElemento,
            method: 'post',
            success: function(response)
            {
                var json = Ext.JSON.decode(response.responseText);

                if(json.total>0){
                    agregarValue("telconet_schemabundle_admitipoelementotype_esDe", json.encontrados[0]['parametroDetId']);                        
                }
                else{
                    alert("sin datos");
                }
            },
            failure: function(result)
            {
              Ext.Msg.alert('Error ','Error: ' + result.statusText);

            }
        }
      );
});

function agregarValue(campo, valor){
    document.getElementById(campo).value = valor;
}