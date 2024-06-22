<?php

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

use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfig;
use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPair;
use OCP\IL10N;

/**
 * @var VdirSyncerUIConfig[]   $vdirsyncerUiConfigs
 * @var VdirSyncerUIConfigPair $mapping
 * @var IL10N                  $l
 */
$names = [];

?>
<div class="app">
    <div class="app-content section">
        <h2 class="inlineblock"><?php

            p($l->t('vdirsyncer UI Configuration')); ?></h2>

        <div class="section">
            <h2><?php p($l->t('Existing Resources')); ?></h2>
            <p class="vdirsyncerui-add-hint hidden-when-empty">
                <?php p($l->t('CalDav-Resources in your account')); ?>
            </p>
            <table id="vdirsyncerui-add-configs-table">
                <thead>
                <tr>
                    <th class="vdirsyncerui-add-configs-table-header"></th>
                    <th class="vdirsyncerui-add-configs-table-header"><?php p($l->t('ID')); ?></th>
                    <th class="vdirsyncerui-add-configs-table-header"><?php p($l->t('Storage')); ?></th>
                    <th class="vdirsyncerui-add-configs-table-header"><?php p($l->t('Collection')); ?></th>
                    <th class="vdirsyncerui-add-configs-table-header"><?php p($l->t('Username')); ?></th>
                    <th class="vdirsyncerui-add-configs-table-header"><?php p($l->t('Fingerprint')); ?></th>
                    <th class="vdirsyncerui-add-configs-table-header"></th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($vdirsyncerUiConfigs as $vdirsyncerUiConfig) {
                    $parsed = parse_url($vdirsyncerUiConfig->getStorage()); // TODO
                    $name   = null === $parsed['host'] ? $parsed['path'] : $parsed['host'];

                    // TODO validate $name
                    $names[$vdirsyncerUiConfig->getId()] = $name;
                    ?>
                    <tr>
                        <td class="vdirsyncerui-add-configs-table-data">
                            <div class="icon-user"></div>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data">
                            <span><?php p($vdirsyncerUiConfig->getId()); ?></span>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data fingerprint"
                            title="<?php p($vdirsyncerUiConfig->getStorage()); ?>">
                            <span><?php p($vdirsyncerUiConfig->getStorage()); ?></span>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data">
                            <span><?php p($vdirsyncerUiConfig->getCollection()); ?></span>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data">
                            <span><?php p($vdirsyncerUiConfig->getUsername()); ?></span>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data fingerprint"
                            title="<?php p($vdirsyncerUiConfig->getFingerprint()); ?>">
                            <span><?php p($vdirsyncerUiConfig->getFingerprint()); ?></span>
                        </td>
                        <td class="vdirsyncerui-add-configs-table-data">
                            <div class="icon-delete clickable vdirsyncerui-add-delete"
                                 data-id="<?= $vdirsyncerUiConfig->getId() ?>"
                            ></div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <div class="hidden" id="vdirsyncerui-pair-pairs"
                 data-pairs="<?php p(json_encode($names)); ?>"></div>
            <div class="vdirsyncerui-setting-box">
                <form id="vdirsyncerui-add-form" method="POST">
                    <label for="vdirsyncerui-add-storage" class="hidden-visually"><?php p($l->t('Storage')); ?>
                        : </label>
                    <input
                            type="url"
                            id="vdirsyncerui-add-storage"
                            name="vdirsyncerui-add-storage"
                            placeholder="<?php p($l->t('Storage')); ?>"
                            value=""
                            autocomplete="off"
                            autocapitalize="none"
                    />
                    <label for="vdirsyncerui-add-collection"
                           class="hidden-visually"><?php p($l->t('Collection')); ?>: </label>
                    <input
                            type="text"
                            id="vdirsyncerui-add-collection"
                            name="vdirsyncerui-add-collection"
                            placeholder="<?php p($l->t('Collection')); ?>"
                            value=""
                            autocomplete="off"
                            autocapitalize="none"
                    />
                    <label for="vdirsyncerui-add-username" class="hidden-visually"><?php p($l->t('Username')); ?>
                        : </label>
                    <input
                            type="text"
                            id="vdirsyncerui-add-username"
                            name="vdirsyncerui-add-username"
                            placeholder="<?php p($l->t('Username')); ?>"
                            value=""
                            autocomplete="current-username"
                            autocapitalize="none"
                    />
                    <label for="vdirsyncerui-add-password" class="hidden-visually"><?php p($l->t('Password')); ?>
                        : </label>
                    <input
                            type="password"
                            id="vdirsyncerui-add-password"
                            name="vdirsyncerui-add-password"
                            placeholder="<?php p($l->t('Password')); ?>"
                            value=""
                            autocomplete="current-password"
                            autocapitalize="none"
                    />

                    <label for="vdirsyncerui-add-ssc-fp"
                           class="hidden-visually"><?php p($l->t('Self Signed Certificate')); ?>
                        : </label>
                    <input
                            type="text"
                            id="vdirsyncerui-add-ssc-fp"
                            name="vdirsyncerui-add-ssc-fp"
                            placeholder="<?php p($l->t('Self Signed SSL Certificate SHA256 Fingerprint')); ?>"
                            value=""
                            autocomplete="current-ssc-fp"
                            autocapitalize="none"
                    />
                    <em>
                        <small>
                            <?= $l->t('Please place the SHA256 Fingerprint colon separated, for instance: 94:FD:7A:CB:50:75:A4:69:82:0A:F8:23:DF:07:FC:69:3E:CD:90:CA. Please consult the <a href="http://vdirsyncer.pimutils.org/en/stable/ssl-tutorial.html">vdirsyncer documentation</a> for further information') ?>
                        </small>
                    </em>
                    <div class="clear"></div>

                    <input id="vdirsyncerui-add-button" type="submit" value="<?php p($l->t('Save')); ?>"/>
                    <span id="vdirsyncerui-add-msg" class="msg success hidden"><?php p($l->t('Saved')); ?></span>
                </form>
            </div>
        </div>
        <div>
            <div class="section"><h2><?php p($l->t('Existing Pairs')); ?></h2>
                <p class="vdirsyncerui-pair-hint"><?php p($l->t('The following CalDav Resources are syncronised bidirectionally')); ?></p>
                <div class="actions" id="vdirsyncerui-pair-actions">
                    <?php
                    foreach ($vdirsyncerUiConfigs as $vdirsyncerUiConfig) {
                        foreach ($vdirsyncerUiConfig->getPairs() as $mapping) {
                            echo '<div class="actions">
					<div class="actions__item colored more" data-config-id="' . $mapping->getConfigId() . '" data-reference-id="' . $mapping->getReferenceConfigId() . '">
						<div class="actions__item__description">
							' . $names[$mapping->getConfigId()] . ' (' . $mapping->getConfigId() . ')
						</div>
						<div class="vdirsyncerui-divider"></div>
						<div class="actions__item__description">
							' . $names[$mapping->getReferenceConfigId()] . ' (' . $mapping->getReferenceConfigId() . ')
						</div>
						<div class="actions__item__description">
							<div class="icon-delete clickable vdirsyncerui-pair-remove-pair" data-id="' . $mapping->getId() . '"></div>
						</div>
					</div>
				</div>';
                        }
                    }

                    ?>
                </div>
            </div>
            <div class="vdirsyncerui-setting-box">
                <form id="vdirsyncerui-pair-form" method="POST">
                    <label for="vdirsyncerui-pair-config-id" class="hidden-visually"><?php p($l->t('Config ID')); ?>
                        : </label>

                    <select
                            id="vdirsyncerui-pair-config-id"
                            name="vdirsyncerui-pair-config-id"
                            autocomplete="vdirsyncerui-pair-config-id"
                            autocapitalize="none"
                    >
                        <option value=""><?php p($l->t('Config ID')); ?></option>
                        <?php
                        foreach ($vdirsyncerUiConfigs as $vdirsyncerUiConfig) {
                            echo '<option value="' . $vdirsyncerUiConfig->getId() . '">(' . $vdirsyncerUiConfig->getId() . ') ' . $names[$vdirsyncerUiConfig->getId()] . '[' . $vdirsyncerUiConfig->getCollection() . ']' . '</option>';
                        }
                        ?>

                    </select>

                    <label for="vdirsyncerui-pair-reference-config-id"
                           class="hidden-visually"><?php p($l->t('Reference Config ID')); ?>: </label>
                    <select
                            id="vdirsyncerui-pair-reference-config-id"
                            name="vdirsyncerui-pair-reference-config-id"
                            autocomplete="vdirsyncerui-pair-reference-config-id"
                            autocapitalize="none"
                    >
                        <option value=""><?php p($l->t('Reference Config ID')); ?></option>
                        <?php
                        foreach ($vdirsyncerUiConfigs as $vdirsyncerUiConfig) {
                            echo '<option value="' . $vdirsyncerUiConfig->getId() . '">(' . $vdirsyncerUiConfig->getId() . ') ' . $names[$vdirsyncerUiConfig->getId()] . '[' . $vdirsyncerUiConfig->getCollection() . ']' . '</option>';
                        }
                        ?>

                    </select>

                    <input id="vdirsyncerui-pair-button" type="submit" value="<?php p($l->t('Save')); ?>"/>
                    <span id="vdirsyncerui-pair-msg" class="msg success hidden"><?php p($l->t('Saved')); ?></span>
                </form>
            </div>
        </div>
        <div class="vdsui--container">
            <div class="vdsui--us--logo">
            </div>
            <div class="vdsui--text">
                <p>
                    vdirsyncer UI is a service by <a class="vdsui--link" href="https://ucar-solutions.de/"
                                                     target="_blank">Ucar Solutions UG
                        (haftungsbeschränkt)</a>
                </p>
            </div>
        </div>
    </div>
</div>
