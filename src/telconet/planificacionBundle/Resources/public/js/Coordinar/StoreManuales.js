/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 2.0 19-02-2021 - Se aumenta el tipo de cable ethernet para los tipos de planificacion
 * 
 */

function getTiposPlanificacion(){
    if (strPermitVerSolInsp == "S")
    {
        return new Ext.data.Store({
            fields: ['id','descripcion'],
            data: [{'id':'SOLICITUD PLANIFICACION', 'descripcion':'SOLICITUD PLANIFICACION'},
                {'id':'SOLICITUD INSPECCION', 'descripcion':'SOLICITUD INSPECCION'},
                {'id':'SOLICITUD RETIRO EQUIPO', 'descripcion': 'SOLICITUD RETIRO EQUIPO'},
                {'id':'SOLICITUD CAMBIO EQUIPO', 'descripcion':'SOLICITUD CAMBIO EQUIPO'},
                {'id':'SOLICITUD MIGRACION', 'descripcion':'SOLICITUD MIGRACION'},
                {'id':'SOLICITUD AGREGAR EQUIPO', 'descripcion':'SOLICITUD AGREGAR EQUIPO'},
                {'id':'SOLICITUD AGREGAR EQUIPO MASIVO', 'descripcion':'SOLICITUD AGREGAR EQUIPO MASIVO'},
                {'id':'SOLICITUD REUBICACION', 'descripcion':'SOLICITUD REUBICACION'},
                {'id':'SOLICITUD CAMBIO EQUIPO POR SOPORTE', 'descripcion':'SOLICITUD CAMBIO EQUIPO POR SOPORTE'},
                {'id':'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO', 'descripcion':'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO'},
                {'id':'SOLICITUD DE INSTALACION CABLEADO ETHERNET', 'descripcion':'SOLICITUD DE INSTALACION CABLEADO ETHERNET'}
                ]
        });
    }
    else
    {
        return new Ext.data.Store({
            fields: ['id','descripcion'],
            data: [{'id':'SOLICITUD PLANIFICACION', 'descripcion':'SOLICITUD PLANIFICACION'},
                {'id':'SOLICITUD RETIRO EQUIPO', 'descripcion': 'SOLICITUD RETIRO EQUIPO'},
                {'id':'SOLICITUD CAMBIO EQUIPO', 'descripcion':'SOLICITUD CAMBIO EQUIPO'},
                {'id':'SOLICITUD MIGRACION', 'descripcion':'SOLICITUD MIGRACION'},
                {'id':'SOLICITUD AGREGAR EQUIPO', 'descripcion':'SOLICITUD AGREGAR EQUIPO'},
                {'id':'SOLICITUD AGREGAR EQUIPO MASIVO', 'descripcion':'SOLICITUD AGREGAR EQUIPO MASIVO'},
                {'id':'SOLICITUD REUBICACION', 'descripcion':'SOLICITUD REUBICACION'},
                {'id':'SOLICITUD CAMBIO EQUIPO POR SOPORTE', 'descripcion':'SOLICITUD CAMBIO EQUIPO POR SOPORTE'},
                {'id':'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO', 'descripcion':'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO'},
                {'id':'SOLICITUD DE INSTALACION CABLEADO ETHERNET', 'descripcion':'SOLICITUD DE INSTALACION CABLEADO ETHERNET'}
                ]
        });
    }
}
    

function getEstadosPlanificacion(){
    return new Ext.data.Store({
        fields: ['id','descripcion'],
        data: [{'id':'Todos', 'descripcion':'Todos'},
               {'id':'PrePlanificada', 'descripcion': 'PrePlanificada'},
               {'id':'Planificada', 'descripcion':'Planificada'},
               {'id':'Replanificada', 'descripcion':'Replanificada'},
               {'id':'Rechazada', 'descripcion':'Rechazada'},
               {'id':'Detenido', 'descripcion':'Detenido'},
               {'id':'AsignadoTarea', 'descripcion':'AsignadoTarea'},
               {'id':'Asignada', 'descripcion':'Asignada'}
              ]
    })
}
