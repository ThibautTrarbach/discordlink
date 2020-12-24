<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('discordlink');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
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
            <div class="cursor eqLogicAction logoSecondary" style="position: absolute; left: 0px; top: 0px;">
                <a href="index.php?v=d&m=discordlink&p=discordlinkemote"><img style="margin-top:-32px;" src="plugins/discordlink/plugin_info/discordlink_icon.png" width="75" height="75">
                    <br>
                    <span>Emojis Settings</span></a>
            </div>
            <div class="cursor eqLogicAction logoSecondary" style="position: absolute; left: 0px; top: 0px;">
                <a href="index.php?v=d&m=discordlink&p=discordlinkuser"><img style="margin-top:-32px;" src="plugins/discordlink/plugin_info/discordlink_icon.png" width="75" height="75">
                    <br>
                    <span>Covid User</span></a>
            </div>
        </div>
        <legend><i class="fas fa-table"></i> {{Mes Channels}}</legend>
        <!-- Champ de recherche -->
        <div class="input-group" style="margin:5px;">
            <input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="input-group-btn">
                <a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
            </div>
        </div>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                </a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span>
                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
                </a>
            </span>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Equipement}}</span></a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i><span class="hidden-xs"> {{Commandes}}</span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-7">
                            <legend><i class="fas fa-wrench"></i> {{Général}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom du channel}}</label>
                                <div class="col-sm-7">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du channel}}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                <div class="col-sm-7">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
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
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Options}}</label>
                                <div class="col-sm-9">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" title="Activer l'équipement" checked />{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" title="Rendre l'équipement visible" checked />{{Visible}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" title="Activer les intéractions avec Jeedom" data-l2key="interactionjeedom" />{{Interactions avec Jeedom}}</label>
                                </div>
                            </div>
                            </br>
                            <legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Channels}}</label>
                                <div class="col-sm-7">
                                    <select class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="channelid">
                                        <?php
                                        $channels = config::byKey('channels', 'discordlink', 'null');
                                        $deamon = discordlink::deamon_info();
                                        $i = 0;
                                        if ($deamon['state'] == 'ok') {
                                            $channels = discordlink::getchannel();
                                            foreach ($channels as $channel) {
                                                echo '<option value="' . $channel['id'] . '">(' . $channel['guildName'] . ') ' . $channel['name'] . '</option>';
                                                $i++;
                                            }
                                        }

                                        if ($i == 0) {
                                            echo '<option value="null">Pas de channel disponible</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            </br>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <label class="checkbox-inline"><input id="deamoncheck" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="deamoncheck" />{{Vérification Démons}}</label>
                                    <label class="checkbox-inline"><input id="depcheck" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="depcheck" />{{Vérification Dépendances}}</label>
                                    <label class="checkbox-inline"><input id="connectcheck" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="connectcheck" />{{Annonce des connexions}}</label>
                                    <?php
                                    if (discordlink::testplugin('openzwave')) {
                                        echo '<label class="checkbox-inline"><input id="zwavecheck" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="zwavecheck"/>{{Vérification Z-wave}}</label>';
                                    } else {
                                        echo '<div style="visibility: hidden; display: none;"><label class="checkbox-inline"><input id="zwavecheck" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="zwavecheck"/>{{Vérification Z-wave}}</label></div>';
                                    }
                                    ?>
                                    </br>
                                    <label class="checkbox-inline"><input id="clearchannel" type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="clearchannel" />{{Clear automatique des channels chaque jour}}</label>
                                </div>
                            </div>
                            </br>
                            <div class="form-group deamon">
                                <label class="col-sm-3 control-label">{{Auto-actualisation démon}}
                                    <sup><i class="fas fa-question-circle" title="{{Fréquence de rafraîchissement de la vérification des Démons}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="autorefreshDeamon" placeholder="{{Auto-actualisation Deamon (cron)}}" />
                                        <span class="input-group-btn">
                                            <a class="btn btn-default cursor jeeHelper roundedRight" id="bt_cronGeneratordeamon" data-helper="cron" title="Assistant cron">
                                                <i class="fas fa-question-circle"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group dependance">
                                <label class="col-sm-3 control-label">{{Auto-actualisation dépendances}}
                                    <sup><i class="fas fa-question-circle" title="{{Fréquence de rafraîchissement de la vérification des dépendances}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="autorefreshDependances" placeholder="{{Auto-actualisation Dépendances (cron)}}" />
                                        <span class="input-group-btn">
                                            <a class="btn btn-default cursor jeeHelper roundedRight" id="bt_cronGeneratorDependance" data-helper="cron" title="Assistant cron">
                                                <i class="fas fa-question-circle"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group zwave">
                                <label class="col-sm-3 control-label">{{Auto-actualisation Z-Wave}}
                                    <sup><i class="fas fa-question-circle" title="{{Fréquence de rafraîchissement de la vérification Z-Wave}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="autorefreshZWave" placeholder="{{Auto-actualisation Z-Wave (cron)}}" />
                                        <span class="input-group-btn">
                                            <a class="btn btn-default cursor jeeHelper roundedRight" id="bt_cronGeneratorzwave" data-helper="cron" title="Assistant cron">
                                                <i class="fas fa-question-circle"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <label class="col-sm-3 control-label">{{Node Id Zwave à exclure}}

                                </label>
                                <div class="col-sm-7">
                                    <input type="text" class="eqLogicAttr form-control roundedLeft roundedRight" data-l1key="configuration" data-l2key="zwaveIdExclude" placeholder="{{exemple : 21 17 58}}" />
                                </div>
                                <label class="col-sm-3 control-label">{{Temps maximal entre la dernière réponse (Z-Wave)}}
                                    <sup><i class="fas fa-question-circle" title="{{En seconde}}"></i></sup>
                                </label>
                                <div class="col-sm-7">
                                    <input type="number" class="eqLogicAttr form-control roundedLeft roundedRight" data-l1key="configuration" data-l2key="TempMax" placeholder="{{default : 43200}}" />
                                </div>
                            </div>
                        </div>
                        <!-- Partie droite de l'onglet "Équipement" -->
                        <!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
                        <div class="col-lg-5">
                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <div class="text-center">
                                    <img name="icon_visu" src="<?= $plugin->getPathImgIcon(); ?>" style="max-width:160px;" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <!-- <a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a> -->
                <br /><br />
                <div role="tabpanel" class="tab-pane active" id="commandtab">
                    <table id="table_cmd" class="table table-bordered table-condensed ui-sortable">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th style="width: 200px;">{{Nom}}</th>
                                <th style="width: 150px;">{{Type}}</th>
                                <th style="width: 300px;">{{Commande & Variable}}</th>
                                <th style="width: 40px;">{{Min}}</th>
                                <th style="width: 40px;">{{Max}}</th>
                                <th style="width: 150px;">{{Paramètres}}</th>
                                <th style="width: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <?php include_file('desktop', 'discordlink', 'js', 'discordlink'); ?>
            <?php include_file('core', 'plugin.template', 'js'); ?>
            <script type="text/javascript">
                setTimeout(() => {
                    setupcase();
                }, 500);
            </script>