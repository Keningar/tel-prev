/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getTiposFeriados(){
    return new Ext.data.Store({
        fields: ['id','descripcion'],
        data: [{'id':'NACIONAL', 'descripcion': 'NACIONAL'},
               {'id':'LOCAL'   , 'descripcion': 'LOCAL'}
              ]
    });
}
    
function getMes(){
    return new Ext.data.Store({
        fields: ['id','descripcion'],
        data: [{'id':'1'  , 'descripcion': 'ENERO'     , dias: 31},
               {'id':'2'  , 'descripcion': 'FEBRERO'   , dias: 28},
               {'id':'3'  , 'descripcion': 'MARZO'     , dias: 31},
               {'id':'4'  , 'descripcion': 'ABRIL'     , dias: 30},
               {'id':'5'  , 'descripcion': 'MAYO'      , dias: 31},
               {'id':'6'  , 'descripcion': 'JUNIO'     , dias: 30},
               {'id':'7'  , 'descripcion': 'JULIO'     , dias: 31},
               {'id':'8'  , 'descripcion': 'AGOSTO'    , dias: 31},
               {'id':'9'  , 'descripcion': 'SEPTIEMBRE', dias: 30},
               {'id':'10' , 'descripcion': 'OCTUBRE'   , dias: 31},
               {'id':'11' , 'descripcion': 'NOVIEMBRE' , dias: 30},
               {'id':'12' , 'descripcion': 'DICIEMBRE' , dias: 31}
              ]
    });
}

function getDia(){
    return new Ext.data.Store({
        fields: ['id','descripcion'],
        data: [{'id':'1'  , 'descripcion': '1'},
               {'id':'2'  , 'descripcion': '2'},
               {'id':'3'  , 'descripcion': '3'},
               {'id':'4'  , 'descripcion': '4'},
               {'id':'5'  , 'descripcion': '5'},
               {'id':'6'  , 'descripcion': '6'},
               {'id':'7'  , 'descripcion': '7'},
               {'id':'8'  , 'descripcion': '8'},
               {'id':'9'  , 'descripcion': '9'},
               {'id':'10' , 'descripcion': '10'},
               {'id':'11' , 'descripcion': '11'},
               {'id':'12' , 'descripcion': '12'},
               {'id':'13' , 'descripcion': '13'},
               {'id':'14' , 'descripcion': '14'},
               {'id':'15' , 'descripcion': '15'},
               {'id':'16' , 'descripcion': '16'},
               {'id':'17' , 'descripcion': '17'},
               {'id':'18' , 'descripcion': '18'},
               {'id':'19' , 'descripcion': '19'},
               {'id':'20' , 'descripcion': '20'},
               {'id':'21' , 'descripcion': '21'},
               {'id':'22' , 'descripcion': '22'},
               {'id':'23' , 'descripcion': '23'},
               {'id':'24' , 'descripcion': '24'},
               {'id':'25' , 'descripcion': '25'},
               {'id':'26' , 'descripcion': '26'},
               {'id':'27' , 'descripcion': '27'},
               {'id':'28' , 'descripcion': '28'},
               {'id':'29' , 'descripcion': '29'},
               {'id':'30' , 'descripcion': '30'},
               {'id':'31' , 'descripcion': '31'}               
              ]
    });
}
