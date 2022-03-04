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

namespace OCA\VdirSyncerUI\AppInfo;

use doganoo\DI\HTTP\URL\IURLService;
use doganoo\DIP\HTTP\URL\URLService;
use Exception;
use OCA\VdirSyncerUI\IApplication;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use function mkdir;

class Application extends App implements IBootstrap {

    public static string $DATA_DIR   = '';
    public static string $STATUS_DIR = '';

    /**
     * @param array $urlParams
     */
    public function __construct(array $urlParams = []) {
        parent::__construct(IApplication::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerService(
            URLService::class,
            function () {
                return new URLService();
            }
        );
        $context->registerServiceAlias(IURLService::class, URLService::class);
        $this->loadAppDependencies();
        $this->createAppDataDir();
    }

    private function loadAppDependencies(): void {
        require_once __DIR__ . '/../../vendor/autoload.php';
    }

    private function createAppDataDir(): void {
        $dataDir = __DIR__ . '/../../data';

        if (false === is_dir($dataDir)) {
            mkdir($dataDir);
        }
        $dataDir = realpath($dataDir);
        if (false === $dataDir) {
            throw new Exception();
        }

        $statusDir = $dataDir . '/status';

        if (false === is_dir($statusDir)) {
            mkdir($statusDir);
        }

        $statusDir = realpath($statusDir);
        if (false === $statusDir) {
            throw new Exception();
        }

        Application::$DATA_DIR   = $dataDir;
        Application::$STATUS_DIR = $statusDir;
    }

    public function boot(IBootContext $context): void {

    }

}