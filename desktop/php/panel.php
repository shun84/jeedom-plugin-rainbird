<?php

if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$date = [
    'start' => init('startDate', date('Y-m-d', strtotime('-1 month ' . date('Y-m-d')))),
    'end' => init('endDate', date('Y-m-d', strtotime('+1 days ' . date('Y-m-d')))),
];

if (init('object_id') == '') {
    $object = jeeObject::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
} else {
    $object = jeeObject::byId(init('object_id'));
}
if (!is_object($object)) {
    $object = jeeObject::rootObject();
}
$allObject = jeeObject::buildTree();
if (count($object->getEqLogic(true, false, 'rainbird')) == 0) {
    foreach ($allObject as $object_sel) {
        if (count($object_sel->getEqLogic(true, false, 'rainbird')) > 0) {
            $object = $object_sel;
            break;
        }
    }
}
if (is_object($object)) {
    $_GET['object_id'] = $object->getId();
}
sendVarToJs('object_id', init('object_id'));

if (!is_object($object)) {
    throw new Exception('{{Aucun objet racine trouvé. Pour en créer un, allez dans Générale -> Objet.<br/> Si vous ne savez pas quoi faire ou que c\'est la premiere fois que vous utilisez Jeedom n\'hésitez pas a consulter cette <a href="https://doc.jeedom.com/fr_FR/premiers-pas/" target="_blank">page</a>}}');
}
?>
<div class="row row-overflow">
    <div class="col-md-2 reportModeHidden">
        <div class="bs-sidebar">
            <ul id="ul_object" class="nav nav-list bs-sidenav">
                <li class="nav-header">{{Liste objets}}</li>
                <li class="filter" style="margin-bottom: 5px;">
                    <label for="rechercher_oklyn" style="display: none">Recherche</label>
                    <input id="rechercher_oklyn" class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/>
                </li>
                <?php
                    foreach ($allObject as $object_li) {
                        if ($object_li->getIsVisible() != 1 || count($object_li->getEqLogic(true, false, 'rainbird', null, true)) == 0) {
                            continue;
                        }
                        $margin = 5 * $object_li->parentNumber();
                        if ($object_li->getId() == init('object_id')) {
                            echo '<li class="cursor li_object active" ><a data-object_id="' . $object_li->getId() . '" href="index.php?v=d&p=panel&m=rainbird&object_id=' . $object_li->getId() . '" style="padding: 2px 0;"><span style="position:relative;left:' . $margin . 'px;">' . $object_li->getHumanName(true,true) . '</span></a></li>';
                        } else {
                            echo '<li class="cursor li_object" ><a data-object_id="' . $object_li->getId() . '" href="index.php?v=d&p=panel&m=rainbird&object_id=' . $object_li->getId() . '" style="padding: 2px 0;"><span style="position:relative;left:' . $margin . 'px;">' . $object_li->getHumanName(true,true) . '</span></a></li>';
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
    <div id="div_object">
        <div style="height: 35px;">
            <span id="rainbirdname"></span>
            <span>
                <label for="in_startDate">
                    {{du}} <input class="form-control input-sm in_datepicker" id='in_startDate' style="display : inline-block; width: 150px;" value='<?php echo $date['start'] ?>'/>
                </label>
                <label for="in_endDate">
                    {{au}} <input class="form-control input-sm in_datepicker" id='in_endDate' style="display : inline-block; width: 150px;" value='<?php echo $date['end'] ?>'/>
                </label>
                <a class="btn btn-success btn-sm tooltips" id='bt_validChangeDate' title="{{Attention une trop grande plage de date peut mettre très longtemps a etre calculer ou même ne pas s'afficher}}">{{Ok}}</a>
            </span>
        </div>
    </div>
</div>
<?php
    $rainbird = $object->getEqLogic(true, false, 'rainbird');
    $nbzone = $rainbird[0]->getConfiguration('nbzone');
    for ($i = 2; $i <= $nbzone; $i+= 2){
        echo '<div class="row" style="margin-top: 28px">';
        echo    '<div class="col-md-2"></div>';
        for ($j = $i-1; $j <= $i; $j++){
            $id = 'rainbirdzone'.$j;
            echo '<div class="col-md-5">';
            echo    '<div id="'.$id.'"></div>';
            echo '</div>';
        }
        echo '</div>';
    }

    include_file('desktop', 'panel', 'js', 'rainbird');