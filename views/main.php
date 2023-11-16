<?php
/**
 * @var bool $isBitrix24Connected
 */
?>
<div class="container-fluid">
    <h1><?= _("Integration with Bitrix24") ?></h1>
    <?php if ($isBitrix24Connected): ?>
        <div class="alert alert-info"><?= _("Connection to Bitrix24 established") ?></div>
    <?php else: ?>
        <div class="alert alert-danger"><?= _("No connection to Bitrix24") ?></div>
    <?php endif; ?>
    <div class="display full-border">
        <div class="row">
            <div class="col-sm-12">
                <div class="fpbx-container">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="?display=bitrix24">
                                <?= _("Menu item") ?>
                            </a>
                        </li>
                        <li class="change-tab">
                            <a href="?display=bitrix24&view=settings&mode=edit">
                                <?= _("Settings") ?>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content display">
                        <div id="toolbar-in">
                            <a href=""
                               class="btn btn-default"><i
                                        class="fa fa-plus"></i> <?= _("Add button") ?>
                            </a>
                        </div>
                        <!--- <table id="bitrix24-table-routes"
                               data-url="ajax.php?module=bitrix24"
                               data-cookie="true"
                               data-toolbar="#toolbar-in"
                               data-cookie-id-table="bitrix24-table-routes"
                               data-maintain-selected="true"
                               data-toggle="table"
                               data-pagination="false"
                               data-search="false"
                               class="table table-striped">
                            <thead>
                            <tr>
                                <th data-field="RouteEnabled_printable">Активна</th>
                                <th data-field="RouteName">Номер входящей линии</th>
                                <th data-field="RouteResponsible_printable">Ответственный</th>
                                <th data-field="RouteResponsibleTransfer_printable">Перевести на
                                    ответственного
                                </th>
                                <th data-field="RouteShowClientName_printable">Отображать имя клиента</th>
                                <th data-field="RouteInLine">Направление</th>
                                <th data-field="id"
                                    data-formatter="linkFormatter">Действия
                                </th>
                            </tr>
                            </thead>
                        </table>
                        <script type="text/javascript">
                            function linkFormatter(value, row, index) {
                                let html = '<a href="?display=bitrix24&view=linerules&mode=edit&item=' + value + '"><i class="fa fa-edit"></i></a>';
                                html += '&nbsp;<a href="?display=bitrix24&action=del&mode=linerules&item=' + value + '" class="delAction"><i class="fa fa-trash"></i></a>';
                                return html;
                            }
                        </script>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>