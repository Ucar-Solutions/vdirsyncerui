/* global OC */

/**
 * @copyright Copyright (c) 2022 Dogan Ucar <info@ucar-solutions.de>
 *
 * @license   GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

window.addEventListener(
    'DOMContentLoaded',
    onContentLoad
);

function onContentLoad() {
    handleVdirSyncerUIAdd();
    handleVdirSyncerUIPairButton();
    handleVdirSyncerUIDelete();
    handleVdirSyncerUIPairRemove();
}

function handleVdirSyncerUIAdd() {
    const vdirsyncerUiButton = $("#vdirsyncerui-add-button");

    vdirsyncerUiButton.click(
        () => {

            const vdirsyncerUiAddStorage = $("#vdirsyncerui-add-storage");
            const vdirsyncerUiAddCollection = $("#vdirsyncerui-add-collection");
            const vdirsyncerUiAddUsername = $("#vdirsyncerui-add-username");
            const vdirsyncerUiAddPassword = $("#vdirsyncerui-add-password");
            const vdirsyncerUiAddFingerprint = $("#vdirsyncerui-add-ssc-fp");

            vdirsyncerUiButton.attr('disabled', 'disabled');
            vdirsyncerUiButton.after("<span class='password-loading icon icon-loading-small-dark password-state'></span>");

            if (false === isValidHttpUrl(vdirsyncerUiAddStorage.val())) {
                OC.dialogs.alert(
                    t('vdirsyncerui', 'The given storage is not a valid url', {}),
                    t('vdirsyncerui', 'Invalid storage', {}),
                    () => {
                        $(".password-loading").remove();
                        vdirsyncerUiButton.removeAttr('disabled');
                    },
                    true
                );
                return false;
            }

            if ("" === vdirsyncerUiAddCollection.val()) {
                OC.dialogs.alert(
                    t('vdirsyncerui', 'Collection name can not be empty', {}),
                    t('vdirsyncerui', 'Invalid collection name', {}),
                    () => {
                        $(".password-loading").remove();
                        vdirsyncerUiButton.removeAttr('disabled');
                    },
                    true
                );
                return false;
            }

            if ("" === vdirsyncerUiAddUsername.val()) {
                OC.dialogs.alert(
                    t('vdirsyncerui', 'Username can not be empty', {}),
                    t('vdirsyncerui', 'Invalid Username', {}),
                    () => {
                        $(".password-loading").remove();
                        vdirsyncerUiButton.removeAttr('disabled');
                    },
                    true
                );
                return false;
            }

            if ("" === vdirsyncerUiAddPassword.val()) {
                OC.dialogs.alert(
                    t('vdirsyncerui', 'Password can not be empty', {}),
                    t('vdirsyncerui', 'Invalid Password', {}),
                    () => {
                        $(".password-loading").remove();
                        vdirsyncerUiButton.removeAttr('disabled');
                    },
                    true
                );
                return false;
            }

            $.post(
                OC.generateUrl('/apps/vdirsyncerui/personal/add'),
                {
                    vdirsyncerUIStorage: vdirsyncerUiAddStorage.val(),
                    vdirsyncerUICollection: vdirsyncerUiAddCollection.val(),
                    vdirsyncerUIUsername: vdirsyncerUiAddUsername.val(),
                    vdirsyncerUIPassword: vdirsyncerUiAddPassword.val(),
                    vdirsyncerUIFingerprint: vdirsyncerUiAddFingerprint.val()
                }
            )
                .then(
                    (data) => {
                        data.status = 'success';
                        const messageId = '#vdirsyncerui-add-msg';
                        vdirsyncerUiButton.removeAttr('disabled');
                        addTableRow(data.data);
                        addPairOption(data.data);
                        vdirsyncerUiAddStorage.val('');
                        vdirsyncerUiAddCollection.val('');
                        vdirsyncerUiAddUsername.val('');
                        vdirsyncerUiAddPassword.val('');
                        vdirsyncerUiAddFingerprint.val('');
                        $(".password-loading").remove();
                        OC.msg.finishedSaving(messageId, data);
                    }
                )
                .catch(
                    (response) => {
                        genericErrorHandler(response, vdirsyncerUiButton);
                    }
                )
            ;
            return true;

        });
}

function handleVdirSyncerUIPairButton() {
    const vdirsyncerUiPairButton = $("#vdirsyncerui-pair-button");

    vdirsyncerUiPairButton.click(
        () => {
            vdirsyncerUiPairButton.attr('disabled', 'disabled');

            const configId = $("#vdirsyncerui-pair-config-id");
            const referenceId = $("#vdirsyncerui-pair-reference-config-id");

            $.post(OC.generateUrl('/apps/vdirsyncerui/personal/add/pair'),
                {
                    configId: configId.val(),
                    referenceConfigId: referenceId.val()
                }
            )
                .then(
                    (data) => {
                        data.status = 'success';
                        const messageId = '#vdirsyncerui-pair-msg';
                        vdirsyncerUiPairButton.removeAttr('disabled');
                        configId.removeAttr("selected");
                        referenceId.removeAttr("selected");
                        $(".password-loading").remove();
                        addPair(data.data);
                        OC.msg.finishedSaving(messageId, data);
                    }
                )
                .catch(
                    (response) => {
                        genericErrorHandler(response, vdirsyncerUiPairButton);
                    }
                );

            return true;

        });
}

function genericErrorHandler(response, button) {
    console.log(response);
    $(".password-loading").remove();
    button.removeAttr('disabled');

    let message = 'Internal Server Error';

    if (response.status === 400) {
        message = response.responseJSON.message;
    }

    OC.dialogs.alert(
        t('vdirsyncerui', message, {}),
        t('vdirsyncerui', 'Error', {}),
        () => {
        },
        true
    );
}


function handleVdirSyncerUIDelete() {
    const vdirsyncerUiAddDelete = $(".vdirsyncerui-add-delete");

    vdirsyncerUiAddDelete.each2(
        (i, v) => {
            const el = $(v);
            const id = el.data("id");

            el.off("click").click(
                () => {

                    OC.dialogs.confirm(
                        t('vdirsyncerui', 'The resource and all corresponding pairs are going to delete. This process is not revertable.', {}),
                        t('vdirsyncerui', 'Remove Resource'),
                        (result) => {
                            if (false === result) {
                                return;
                            }
                            if (typeof id === 'undefined') {
                                // TODO inform
                                return;
                            }


                            vdirsyncerUiAddDelete.attr('disabled', 'disabled');
                            $.post(OC.generateUrl('/apps/vdirsyncerui/personal/delete'),
                                {
                                    id: id
                                },
                            )
                                .then((data) => {
                                    data.status = 'success';
                                    const messageId = '#vdirsyncerui-add-msg';
                                    vdirsyncerUiAddDelete.removeAttr('disabled');
                                    $(".password-loading").remove();

                                    OC.msg.finishedSaving(messageId, data);
                                    el.parent().parent().remove();
                                    removePairOption(data.data);

                                })
                                .catch(
                                    (response) => {
                                        genericErrorHandler(response, vdirsyncerUiAddDelete);
                                    }
                                );

                        },
                        true
                    )

                }
            )
        }
    );

}

function handleVdirSyncerUIPairRemove() {
    const vdirsynceruiPairRemovePair = $(".vdirsyncerui-pair-remove-pair");

    vdirsynceruiPairRemovePair.each2(
        (i, v) => {
            const el = $(v);
            const id = el.data("id");

            el.off("click").click(
                () => {

                    OC.dialogs.confirm(
                        t('vdirsyncerui', 'The pair is going to delete. This process is not revertable.', {}),
                        t('vdirsyncerui', 'Remove Pair'),
                        (result) => {
                            if (false === result) {
                                return;
                            }
                            if (typeof id === 'undefined') {
                                // TODO inform
                                return;
                            }


                            vdirsynceruiPairRemovePair.attr('disabled', 'disabled');
                            $.post(OC.generateUrl('/apps/vdirsyncerui/personal/pair/delete'),
                                {
                                    id: id
                                }
                            ).then((data) => {
                                data.status = 'success';
                                const messageId = '#vdirsyncerui-add-msg';
                                $(".password-loading").remove();

                                OC.msg.finishedSaving(messageId, data);
                                el
                                    .parent()
                                    .parent()
                                    .remove();


                            })
                                .catch(
                                    (response) => {
                                        genericErrorHandler(response, vdirsynceruiPairRemovePair);
                                    }
                                );


                        },
                        true
                    )

                }
            )
        }
    )

}

/**
 *
 * @param {object} VdirSyncerUIConfig
 */
function addTableRow(VdirSyncerUIConfig) {
    const pairObject = $("#vdirsyncerui-pair-pairs");
    const pairs = pairObject.data('pairs');

    pairs[VdirSyncerUIConfig.id] = VdirSyncerUIConfig.storage;
    const fingerprint = (VdirSyncerUIConfig.fingerprint === null ? '' : VdirSyncerUIConfig.fingerprint);

    pairObject.data('pairs', pairs);

    $("#vdirsyncerui-add-configs-table tr:last")
        .after(
            '<tr><td class="vdirsyncerui-add-configs-table-data"><div class="icon-user"></div></td><td class="vdirsyncerui-add-configs-table-data"><span>' + VdirSyncerUIConfig.id + '</span></td><td class="vdirsyncerui-add-configs-table-data fingerprint" title="' + VdirSyncerUIConfig.storage + '"><span>' + VdirSyncerUIConfig.storage + '</span></td><td class="vdirsyncerui-add-configs-table-data"><span>' + VdirSyncerUIConfig.collection + '</span></td><td class="vdirsyncerui-add-configs-table-data"><span>' + VdirSyncerUIConfig.username + '</span></td><td class="vdirsyncerui-add-configs-table-data fingerprint" title="' + fingerprint + '"><span>' + fingerprint + '</span></td><td class="vdirsyncerui-add-configs-table-data"><div class="icon-delete clickable vdirsyncerui-add-delete" data-id="' + VdirSyncerUIConfig.id + '"></div></td></tr>'
        );

    handleVdirSyncerUIDelete();
}

/**
 *
 * @param {object} vdirSyncerUIConfig
 */
function addPairOption(vdirSyncerUIConfig) {
    const url = new URL(vdirSyncerUIConfig.storage);
    console.log(vdirSyncerUIConfig.collection);

    $('#vdirsyncerui-pair-config-id')
        .append($('<option>', {
            value: vdirSyncerUIConfig.id,
            text: '(' + vdirSyncerUIConfig.id + ') ' + url.host + ' [' + vdirSyncerUIConfig.collection + ']'
        }));
    $('#vdirsyncerui-pair-reference-config-id')
        .append($('<option>', {
            value: vdirSyncerUIConfig.id,
            text: '(' + vdirSyncerUIConfig.id + ') ' + url.host + ' [' + vdirSyncerUIConfig.collection + ']'
        }));
}

function removePairOption(vdirSyncerUI) {
    $("#vdirsyncerui-pair-config-id option[value='" + vdirSyncerUI.id + "']").each(function () {
        $(this).remove();
    });

    $("#vdirsyncerui-pair-reference-config-id option[value='" + vdirSyncerUI.id + "']").each(function () {
        $(this).remove();
    });

    $(".actions__item").each(
        (i, v) => {

            const element = $(v);
            if (
                parseInt(element.data("config-id")) === parseInt(vdirSyncerUI.id)
                || parseInt(element.data("reference-id")) === parseInt(vdirSyncerUI.id)
            ) {
                element.remove();
            }
        }
    );

}

/**
 *
 * @param {object} pair
 */
function addPair(pair) {
    const pairs = $("#vdirsyncerui-pair-pairs").data('pairs');
    const configId = parseInt(pair.configId);
    const configName = pairs[configId];

    const referenceId = parseInt(pair.reference_config_id);
    const referenceName = pairs[referenceId];

    $("#vdirsyncerui-pair-actions")
        .append(
            '<div class="actions__item colored more" data-config-id="' + configId + '" data-reference-id="' + referenceId + '"><div class="actions__item__description">' + configName + ' (' + configId + ')</div><div class="vdirsyncerui-divider"></div><div class="actions__item__description">' + referenceName + ' (' + referenceId + ')</div><div class="actions__item__description"><div class="icon-delete clickable vdirsyncerui-pair-remove-pair" data-id="' + pair.id + '"></div></div></div>'
        )
    ;
    handleVdirSyncerUIPairRemove();
}
