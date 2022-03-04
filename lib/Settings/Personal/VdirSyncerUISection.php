<?php
declare(strict_types=1);
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

namespace OCA\VdirSyncerUI\Settings\Personal;

use OCA\VdirSyncerUI\IApplication;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class VdirSyncerUISection implements IIconSection {

    private IL10N         $l;
    private IURLGenerator $urlGenerator;

    public function __construct(
        IL10N         $l,
        IURLGenerator $urlGenerator
    ) {
        $this->l            = $l;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * returns the ID of the section. It is supposed to be a lower case string
     *
     * @returns string
     */
    public function getID(): string {
        return IApplication::PERSONAL_SECTION_ID;
    }

    /**
     * returns the translated name as it should be displayed, e.g. 'LDAP / AD
     * integration'. Use the L10N service to translate it.
     *
     * @return string
     */
    public function getName(): string {
        return $this->l->t('vdirsyncer UI');
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the settings navigation. The sections are arranged in ascending order of
     * the priority values. It is required to return a value between 0 and 99.
     */
    public function getPriority(): int {
        return 80;
    }

    /**
     * @return string The relative path to a an icon describing the section
     */
    public function getIcon(): string {
        return $this->urlGenerator->imagePath(IApplication::APP_ID, 'sync-solid.svg');
    }

}
