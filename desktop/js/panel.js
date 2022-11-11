$(".in_datepicker").datepicker();

$('#bt_validChangeDate').on('click', function () {
    jeedom.history.chart = [];
    displayRainbird(object_id);
});

displayRainbird(object_id);

function displayRainbird(object_id) {
    $.ajax({
        type: 'POST',
        url: 'plugins/rainbird/core/ajax/rainbird.ajax.php',
        data: {
            action: 'getRainbird',
            object_id: object_id,
            version: 'dashboard'
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state !== 'ok') {
                $.fn.showAlert({message: data.result, level: 'danger'});
            }
            let icon = '';
            if (isset(data.result.object.display) && isset(data.result.object.display.icon)) {
                icon = data.result.object.display.icon;
            }
            document.getElementById('rainbirdname').innerHTML = icon + ' ' + data.result.object.name;

            for (let i in data.result.eqLogics) {
                graphesRainbird(data.result.eqLogics[i].eqLogic.id, data.result.eqLogics[i].eqLogic.configuration.nbzone);
            }
        }
    });
}

function graphesRainbird(_eqLogic_id, _nbzones) {
    jeedom.eqLogic.getCmd({
        id: _eqLogic_id,
        error: function (error) {
            $.fn.showAlert({message: error.message, level: 'danger'});
        },
        success: function (cmds) {
            for (let nbzone = 1; nbzone <= _nbzones; nbzone++){
                jeedom.history.chart['rainbirdzone' + nbzone + _eqLogic_id] = null;
                for (let i in cmds) {
                    if (cmds[i].logicalId === 'getzonelancer' + nbzone) {
                        jeedom.history.drawChart({
                            cmd_id: cmds[i].id,
                            dateStart: $('#in_startDate').value(),
                            dateEnd: $('#in_endDate').value(),
                            el: 'rainbirdzone' + nbzone + _eqLogic_id
                        });
                        document.getElementById('rainbirdzone'+ nbzone).innerHTML = '<div class="chartContainer" id="rainbirdzone' + nbzone + _eqLogic_id + '"></div>';
                    }
                }
            }
        }
    });
}