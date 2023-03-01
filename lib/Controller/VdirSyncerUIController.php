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

use doganoo\DI\HTTP\IStatus;
use doganoo\DI\HTTP\URL\IURLService;
use Exception;
use OCA\VdirSyncerUI\AppInfo\Application;
use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfig;
use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfigRepository;
use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPair;
use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPairRepository;
use OCA\VdirSyncerUI\Exception\CouldNotFindEntityException;
use OCA\VdirSyncerUI\Exception\CouldNotHandleFileException;
use OCA\VdirSyncerUI\Service\SyncerDataService;
use OCA\VdirSyncerUI\Service\SyncerService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class VdirSyncerUIController extends Controller {

    private VdirSyncerUIConfigRepository     $vdirsyncerUiConfigRepository;
    private VdirSyncerUIConfigPairRepository $vdirsyncerUiConfigPairRepository;
    private string                           $userId;
    private SyncerService                    $syncerService;
    private IURLService                      $urlService;
    private SyncerDataService                $syncerDataService;
    private LoggerInterface                  $logger;

    public function __construct(
        string                           $appName,
        IRequest                         $request,
        VdirSyncerUIConfigRepository     $vdirsyncerUiConfigPair,
        VdirSyncerUIConfigPairRepository $vdirsyncerUiConfigPairRepository,
        SyncerService                    $syncerService,
        IURLService                      $urlService,
        SyncerDataService                $syncerDataService,
        LoggerInterface                  $logger,
                                         $userId
    ) {
        parent::__construct($appName, $request);

        $this->vdirsyncerUiConfigRepository     = $vdirsyncerUiConfigPair;
        $this->vdirsyncerUiConfigPairRepository = $vdirsyncerUiConfigPairRepository;
        $this->syncerService                    = $syncerService;
        $this->urlService                       = $urlService;
        $this->userId                           = $userId;
        $this->syncerDataService                = $syncerDataService;
        $this->logger                           = $logger;
    }

    /**
     * adds a new resource to the database in order to make them configurable
     *
     * @param string $vdirsyncerUIStorage
     * @param string $vdirsyncerUICollection
     * @param string $vdirsyncerUIUsername
     * @param string $vdirsyncerUIPassword
     * @param string $vdirsyncerUIFingerprint
     * @return JSONResponse
     *
     * @NoAdminRequired
     * @UseSession
     * @BruteForceProtection(action=sudo)
     * @throws \OCP\DB\Exception
     */
    public function add(
        string $vdirsyncerUIStorage,
        string $vdirsyncerUICollection,
        string $vdirsyncerUIUsername,
        string $vdirsyncerUIPassword,
        string $vdirsyncerUIFingerprint
    ): Response {

        if (false === $this->urlService->isUrl($vdirsyncerUIStorage)) {
            $this->logger->warning('invalid storage ' . $vdirsyncerUIStorage);
            return new JSONResponse(
                [
                    'message' => 'Invalid storage'
                ],
                IStatus::BAD_REQUEST
            );
        }

        if (false === $this->syncerService->isValidCollection($vdirsyncerUICollection)) {
            $this->logger->warning('invalid collection ' . $vdirsyncerUICollection);
            return new JSONResponse(
                [
                    'message' => 'Invalid collection name'
                ],
                IStatus::BAD_REQUEST
            );
        }

        if (true === $this->vdirsyncerUiConfigRepository->isDuplicatedForUser(
                $vdirsyncerUIStorage,
                $vdirsyncerUICollection,
                $vdirsyncerUIUsername,
                $this->userId
            )
        ) {
            $this->logger->warning('collection already exists');
            return new JSONResponse(
                [
                    'message' => 'collection already exists'
                ],
                IStatus::BAD_REQUEST
            );
        }

        $vDirSyncerUIConfig = new VdirSyncerUIConfig();
        $vDirSyncerUIConfig->setStorage($vdirsyncerUIStorage);
        $vDirSyncerUIConfig->setCollection($vdirsyncerUICollection);
        $vDirSyncerUIConfig->setUsername($vdirsyncerUIUsername);
        $vDirSyncerUIConfig->setPassword($vdirsyncerUIPassword);
        $vDirSyncerUIConfig->setUserId($this->userId);
        $vDirSyncerUIConfig->setFingerprint(
            "" === trim($vdirsyncerUIFingerprint)
                ? null
                : trim($vdirsyncerUIFingerprint)
        );

        $vDirSyncerUIConfig = $this->vdirsyncerUiConfigRepository->insert($vDirSyncerUIConfig);
        return new JSONResponse(
            [
                'status' => 'success',
                'data'   => $vDirSyncerUIConfig
            ]
        );
    }

    /**
     * @param string $configId
     * @param string $referenceConfigId
     * @return Response
     * @NoAdminRequired
     * @throws \OCP\DB\Exception
     */
    public function pair(string $configId, string $referenceConfigId): Response {
        $vDirSyncerUIConfig = new VdirSyncerUIConfigPair();
        $vDirSyncerUIConfig->setConfigId((int) $configId);
        $vDirSyncerUIConfig->setReferenceConfigId((int) $referenceConfigId);

        $configPaired    = $this->vdirsyncerUiConfigPairRepository->isPaired(
            $vDirSyncerUIConfig->getConfigId(),
            $vDirSyncerUIConfig->getReferenceConfigId()
        );
        $referencePaired = $this->vdirsyncerUiConfigPairRepository->isPaired(
            $vDirSyncerUIConfig->getReferenceConfigId(),
            $vDirSyncerUIConfig->getConfigId()
        );

        if (true === $configPaired || true === $referencePaired) {
            $this->logger->warning('resources already paired');
            return new JSONResponse(
                [
                    'message' => 'Resources already paired'
                ],
                IStatus::BAD_REQUEST
            );
        }

        try {

            $this->vdirsyncerUiConfigPairRepository->insert($vDirSyncerUIConfig);
            $result = $this->syncerService->fillTemplate($vDirSyncerUIConfig);

            $pairName = $result['pairname'];

            $this->syncerDataService->deleteFile($pairName);
            $this->syncerDataService->writeData($pairName, $result['content']);

            return new JSONResponse(
                [
                    'data' => $vDirSyncerUIConfig
                ]
            );
        } catch (CouldNotFindEntityException|CouldNotHandleFileException $exception) {
            $this->vdirsyncerUiConfigPairRepository->delete($vDirSyncerUIConfig);
            $this->logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
        }

        return new JSONResponse(
            [
                'message' => 'Could not insert data'
            ],
            IStatus::BAD_REQUEST
        );
    }

    /**
     * @param string $id
     * @return Response
     * @NoAdminRequired
     */
    public function delete(string $id): Response {

        try {
            $entity = $this->vdirsyncerUiConfigRepository->findById((int) $id);
            $entity = $this->vdirsyncerUiConfigRepository->delete($entity);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
            return new JSONResponse(
                [
                    'message' => 'No Data found'
                ],
                IStatus::BAD_REQUEST
            );
        }

        return new JSONResponse(
            [
                'status' => 'success',
                'data'   => $entity,
            ]
        );
    }

    /**
     * @param string $id
     * @return Response
     * @NoAdminRequired
     */
    public function deletePair(string $id): Response {
        // TODO validate
        try {
            $entity = $this->vdirsyncerUiConfigPairRepository->findById((int) $id);
            $entity = $this->vdirsyncerUiConfigPairRepository->delete($entity);

            $pairName = $this->syncerService->generatePairName(
                $this->vdirsyncerUiConfigRepository->findById($entity->getConfigId()),
                $this->vdirsyncerUiConfigRepository->findById($entity->getReferenceConfigId())
            );
            $fileName = Application::$DATA_DIR . '/' . $pairName;

            if (true === is_file($fileName)) {
                unlink($fileName);
            }

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
            return new JSONResponse(
                [
                    'message' => 'No Data found'
                ],
                IStatus::BAD_REQUEST
            );
        }

        return new JSONResponse(
            [
                'status' => 'success',
                'data'   => $entity,
            ]
        );
    }

}
