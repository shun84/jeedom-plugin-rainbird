
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


/*
* Permet la réorganisation des commandes dans l'équipement
*/
$("#table_cmd").sortable({
    axis: "y",
    cursor: "move",
    items: ".cmd",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});
/*
* Fonction permettant l'affichage des commandes dans l'équipement
*/
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
     _cmd = {configuration: {}};
   }
   if (!isset(_cmd.configuration)) {
     _cmd.configuration = {};
   }
   let tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td class="hidden-xs">'
    tr +=   '<span class="cmdAttr" data-l1key="id"></span>'
    tr += '</td>'
    tr += '<td>'
    tr +=   '<div class="input-group">'
    tr +=       '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
    tr +=       '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
    tr +=       '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
    tr +=   '</div>'
    tr +=   '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}">'
    tr +=       '<option value="">{{Aucune}}</option>'
    tr +=   '</select>'
    tr += '</td>'
    tr += '<td>'
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
    tr += '</td>'
    tr += '<td>'
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> '
    tr += '<div style="margin-top:7px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '</div>'
    tr += '</td>'
    tr += '<td>'
    tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'
    tr += '</td>'
    tr += '<td>'
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> '
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>'
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>'
    tr += '</tr>'
    $('#table_cmd tbody').append(tr)
    tr = $('#table_cmd tbody tr').last()
    jeedom.eqLogic.buildSelectCmd({
        id:  $('.eqLogicAttr[data-l1key=id]').value(),
        filter: {type: 'info'},
        error: function (error) {
            jeedomUtils.showAlert({
                message: error.message,
                level: 'danger'
            })
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result)
            tr.setValues(_cmd, '.cmdAttr')
            jeedom.cmd.changeType(tr, init(_cmd.subType))
        }
    })
 }

$('#bt_resetObjectSearch').on('click', function() {
    $('#in_searchEqlogic').val('').keyup()
})

function printEqLogic(_eqLogic) {
    printScheduling(_eqLogic);
}

function printScheduling(_eqLogic){
    $.ajax({
        type: 'POST',
        url: 'plugins/rainbird/core/ajax/rainbird.ajax.php',
        data: {
            action: 'getLinkCalendar',
            id: _eqLogic.id
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            let divschedule = $('#div_schedule');
            if (data.state !== 'ok') {
                $.fn.showAlert({message: data.result, level: 'danger'});
                return;
            }
            divschedule.empty();
            if(data.result.length === 0){
                divschedule.append('<div class="col-xs-10 col-xs-offset-1 alert alert-warning">{{Aucune programmation trouvée. Cliquer sur le bouton ci-après pour programmer le rainbird à l\'aide du}} <a class="btn btn-sm" href="index.php?v=d&m=calendar&p=calendar">{{plugin Agenda}}</a></div>');
            }else{
                let html = '<legend><i class="fas fa-external-link-alt"></i> {{Programmations du plugin Agenda liées au rainbird}} :</legend><hr>';
                for (let i in data.result) {
                    let cmdparam = data.result[i].cmd_param;
                    let color = init(cmdparam.color, '#2980b9');
                    if(cmdparam.transparent === 1){
                        color = 'transparent';
                    }
                    html += '<span class="label cursor" style="font-size:1.2em!important;margin-left:20px;background-color : ' + color + ';color : ' + init(cmdparam.text_color, 'black') + '">';
                    html += '<a href="index.php?v=d&m=calendar&p=calendar&id='+data.result[i].eqLogic_id+'&event_id='+data.result[i].id+'" style="color : ' + init(cmdparam.text_color, 'black') + '">'

                    if (cmdparam.eventName !== '') {
                        html += cmdparam.icon + ' ' + cmdparam.eventName;
                    } else {
                        html += cmdparam.icon + ' ' + cmdparam.name;
                    }
                    html += '</a></span><hr>';
                }
                $('#div_schedule').empty().append(html);
            }
        }
    });
}