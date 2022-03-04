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

namespace OCA\VdirSyncerUI\Controller;

use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfigRepository;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\OCSController;
use OCP\DB\Exception;
use OCP\IRequest;

class ExternalVdirSyncerUIController extends OCSController {

    private VdirSyncerUIConfigRepository $vdirsyncerUiConfigRepository;

    public function __construct(
        $appName,
        IRequest $request,
        VdirSyncerUIConfigRepository $vdirsyncerUiConfigPair,
        $corsMethods = 'PUT, POST, GET, DELETE, PATCH',
        $corsAllowedHeaders = 'Authorization, Content-Type, Accept',
        $corsMaxAge = 1728000
    ) {
        parent::__construct($appName, $request, $corsMethods, $corsAllowedHeaders, $corsMaxAge);

        $this->vdirsyncerUiConfigRepository = $vdirsyncerUiConfigPair;
    }

    /**
     * @return Response
     * @throws Exception
     * @NoCSRFRequired
     */
    public function index(): Response {
        return new JSONResponse(
            $this->vdirsyncerUiConfigRepository->findAll()
        );
    }

}
