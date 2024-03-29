<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('rainbird');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes RainBird}}</legend>
        <?php
        if (count($eqLogics) == 0) {
            echo '<br/><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement Rainbird n\'est paramétré, cliquer sur "Ajouter" pour commencer}}</div>';
        } else {
            // Champ de recherche
            echo '<div class="input-group" style="margin:5px;">';
            echo    '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>';
            echo    '<div class="input-group-btn">';
            echo        '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
            echo        '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
            echo    '</div>';
            echo '</div>';
            // Liste des équipements du plugin
            echo '<div class="eqLogicThumbnailContainer">';
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo    '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo    '<br>';
                echo    '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
	</div>
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Équipement}}</span></a></li>
			<li role="presentation"><a href="#configurationzones" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration des Zones}}</span></a></li>
            <?php
                try {
                    $pluginCalendar = plugin::byId('calendar');
                    if (is_object($pluginCalendar)) {
                        ?>
                            <li role="presentation"><a href="#configureSchedule" aria-controls="home" role="tab" data-toggle="tab"><i class="far fa-clock"></i><span class="hidden-xs"> {{Programmation des Zones}}</span></a></li>
                        <?php
                    }
                } catch (Exception $e) {
                }
            ?>
            <li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i><span class="hidden-xs"> {{Commandes}}</span></a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-7">
							<legend><i class="fas fa-wrench"></i> {{Général}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-7">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
                                            $options = '';
                                            foreach ((jeeObject::buildTree(null, false)) as $object) {
                                                $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                            }
                                            echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Catégorie}}</label>
								<div class="col-sm-9">
									<?php
                                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                            echo '<label class="checkbox-inline">';
                                            echo    '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                            echo '</label>';
                                        }
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Options}}</label>
								<div class="col-sm-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>
							<br>
							<legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>
							<div class="form-group">
								<label for="iprainbird" class="col-sm-3 control-label">{{Ip du RainBird}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" id="iprainbird" data-l1key="configuration" data-l2key="iprainbird"/>
								</div>
							</div>
                            <div class="form-group">
                                <label for="mdprainbird" class="col-sm-3 control-label">{{Mot de passe}}
                                    <sup><i class="fas fa-question-circle tooltips" title="{{C'est le mot de passe utilisé pour avoir accès à la configuration du RainBird}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <input type="password" class="eqLogicAttr form-control" id="mdprainbird" data-l1key="configuration" data-l2key="mdprainbird"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nbzone" class="col-sm-3 control-label">{{Nombre de Zone}}
                                    <sup><i class="fas fa-question-circle tooltips" title="{{Récupérer par une fonction de l'API}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <input id="nbzone" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nbzone" readonly>
                                </div>
                            </div>
						</div>
						<div class="col-lg-5">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <?php
                                foreach ($eqLogics as $eqLogic){
                                    echo '<div class="form-group">';
                                    echo    '<label class="col-lg-2" for="model_rainbird">{{Model}}</label>';
                                    echo    '<input type="text" class="col-lg-3 eqLogicAttr form-control" id="model_rainbird" value="'.$eqLogic->getConfiguration('model').'" data-l1key="configuration" data-l2key="model" readonly>';
                                    echo '</div>';
                                    echo '<div class="form-group">';
                                    echo    '<label class="col-lg-2" for="version_rainbird">{{Version}}</label>';
                                    echo    '<input type="text" class="col-lg-3 eqLogicAttr form-control" id="version_rainbird" value="'.$eqLogic->getConfiguration('version').'" data-l1key="configuration" data-l2key="version" readonly>';
                                    echo '</div>';
                                    echo '<div class="form-group">';
                                    echo    '<label class="col-lg-2" for="serial_rainbird" data-help="Test">{{No Serial}}';
                                    echo        '<sup><i class="fas fa-question-circle tooltips" title="{{Si vous avez un 0 correspond à un ESP-RZXe}}"></i></sup>';
								    echo    '</label>';
                                    echo    '<input type="text" class="col-lg-3 eqLogicAttr form-control" id="serial_rainbird" value="'.$eqLogic->getConfiguration('serial').'" data-l1key="configuration" data-l2key="serial" readonly>';
                                    echo '</div>';
                                }
                            ?>
						</div>
					</fieldset>
				</form>
				<hr>
			</div>
            <div role="tabpanel" class="tab-pane" id="configurationzones">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="row">
                            <div class="col-lg-6">
                                <legend><i class="fas fa-hourglass-half"></i> {{Durée des zones}}</legend>
                                    <?php
                                        if ($eqLogics){
                                            for ($i = 1; $i <= $eqLogics[0]->getConfiguration('nbzone'); $i++){
                                                echo '<div class="form-group">';
                                                echo    '<label for="duree'.$i.'" class="col-sm-3 control-label">{{Zone ' . $i . '}}</label>';
                                                echo    '<div class="col-sm-7">';
                                                echo        '<input type="number" class="eqLogicAttr form-control"  id="duree'.$i.'" data-l1key="configuration" data-l2key="duree'.$i.'">';
                                                echo    '</div>';
                                                echo '</div>';
                                            }
                                        }
                                    ?>
                                <div class="form-group">
                                    <label for="dureetestzone" class="col-sm-3 control-label">{{Tout les zones}}</label>
                                    <div class="col-sm-7">
                                        <input type="number" class="eqLogicAttr form-control"  id="dureetestzone" data-l1key="configuration" data-l2key="dureetestzone">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <legend><i class="fas fa-ellipsis-v"></i> {{Nom des zones}}</legend>
                                <?php
                                    if ($eqLogics) {
                                        for ($i = 1; $i <= $eqLogics[0]->getConfiguration('nbzone'); $i++) {
                                            echo '<div class="form-group">';
                                            echo    '<label for="nomzone' . $i . '" class="col-sm-3 control-label">{{Nom de la Zone ' . $i . '}}</label>';
                                            echo    '<div class="col-sm-7">';
                                            echo        '<input type="text" class="eqLogicAttr form-control"  id="nomzone' . $i . '" data-l1key="configuration" data-l2key="nomzone' . $i . '">';
                                            echo    '</div>';
                                            echo '</div>';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="tab-pane" id="configureSchedule">
                <form class="form-horizontal">
                    <fieldset style="margin-top: 10px">
                        <div id="div_schedule"></div>
                    </fieldset>
                </form>
            </div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
                                <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
                                <th style="min-width:200px;width:350px;">{{Nom}}</th>
                                <th>{{Type}}</th>
                                <th style="min-width:260px;">{{Options}}</th>
                                <th>{{Etat}}</th>
                                <th style="min-width:80px;width:200px;">{{Actions}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'rainbird', 'js', 'rainbird');?>
<?php include_file('core', 'plugin.template', 'js');?>
