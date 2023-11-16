<?php
/**
 * @var \Base\Config $config
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
                        <li class="change-tab">
                            <a href="?display=bitrix24">
                                <?= _("Menu item") ?>
                            </a>
                        </li>
                        <li class="active">
                            <a href="?display=bitrix24&view=settings&mode=edit">
                                <?= _("Settings") ?>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content display">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="fpbx-container">
                                    <form class="popover-form fpbx-submit" name="configform"
                                          action="?display=bitrix24&view=settings&mode=edit" method="post">
                                        <input type="hidden" name="display" value="bitrix24">
                                        <input type="hidden" name="view" value="settings">
                                        <input type="hidden" name="edit" value="Y">
                                        <input type="hidden" name="mode" value="<?= $_REQUEST["mode"] ?>">
                                        <div class="element-container">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-sm-3">
                                                                <label class="control-label"
                                                                       for="IncomingHookAddress"><?= _("Incoming hook address") ?></label>
                                                                <i class="fa fa-question-circle fpbx-help-icon"
                                                                   data-for="IncomingHookAddress"></i>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        required="required"
                                                                        id="IncomingHookAddress"
                                                                        name="form[IncomingHookAddress]"
                                                                        value="<?= $config->getValue("IncomingHookAddress") ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                        <span id="IncomingHookAddress-help"
                                                              class="help-block fpbx-help-block">
                                                            <?= _("The incoming hook is responsible for sending requests to Bitrix24.") ?>
                                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="element-container">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-sm-3">
                                                                <label class="control-label"
                                                                       for="AuthCodeOutgoingHook"><?= _("Authorization code for outgoing hook") ?></label>
                                                                <i class="fa fa-question-circle fpbx-help-icon"
                                                                   data-for="AuthCodeOutgoingHook"></i>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        required="required"
                                                                        id="AuthCodeOutgoingHook"
                                                                        name="form[AuthCodeOutgoingHook]"
                                                                        value="<?= $config->getValue("AuthCodeOutgoingHook") ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                        <span id="AuthCodeOutgoingHook-help"
                                                              class="help-block fpbx-help-block">
                                                            <?= _("The authorization code is needed to respond to events that occur inside Bitrix24.") ?>
                                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>