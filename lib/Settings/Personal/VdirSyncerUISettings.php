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

use Exception;
use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfigRepository;
use OCA\VdirSyncerUI\IApplication;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\Util;

class VdirSyncerUISettings implements ISettings {

    private VdirSyncerUIConfigRepository $vdirsyncerUiConfigRepository;
    private string                       $userId;

    public function __construct(
        VdirSyncerUIConfigRepository $vdirsyncerUiConfigRepository,
                                     $userId
    ) {
        $this->vdirsyncerUiConfigRepository = $vdirsyncerUiConfigRepository;
        $this->userId                       = $userId;

        Util::addScript(IApplication::APP_ID, 'helper');
        Util::addScript(IApplication::APP_ID, 'vdirsyncerui');
        Util::addStyle(IApplication::APP_ID, 'vdirsyncerui');
    }

    /**
     * @return TemplateResponse
     */
    public function getForm(): Response {
        $vdirsyncerUiConfigs = [];
        try {
            $vdirsyncerUiConfigs = $this->vdirsyncerUiConfigRepository->find(
                $this->userId
            );

        } catch (Exception $exception) {
            // TODO log
        }

        return new TemplateResponse(
            IApplication::APP_ID,
            'settings/personal/index',
            [
                'vdirsyncerUiConfigs' => $vdirsyncerUiConfigs
            ]
        );
    }

    /**
     * @return string the section ID, e.g. 'sharing'
     */
    public function getSection(): string {
        return IApplication::PERSONAL_SECTION_ID;
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     */
    public function getPriority(): int {
        return 50;
    }

}
