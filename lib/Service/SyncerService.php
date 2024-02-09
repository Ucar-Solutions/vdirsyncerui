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

namespace OCA\VdirSyncerUI\Service;

use DateTime;
use OCA\VdirSyncerUI\AppInfo\Application;
use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfig;
use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfigRepository;
use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPair;
use OCA\VdirSyncerUI\Exception\CouldNotFindEntityException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class SyncerService {

    public const INTERVAL  = 1;
    public const LOG_LEVEL = "DEBUG";
    public const TEMPLATE  = '[general]
status_path = "${statusDir}"

[pair ${pairName}]
conflict_resolution = "a wins"
a = "${principalName}"
b = "${referenceName}"
collections = [["${collectionNamePrincipal}", "${collectionPrincipal}", "${collectionReference}"]]
metadata = ["color", "displayname"]

[storage ${principalName}]
type = "caldav"
url = "${principalURL}"
username = "${principalUser}"
password = "${principalPassword}"
# verify = ${verifyPrincipal}
# verify_fingerprint = "${verifyFingerprintPrincipal}"

[storage ${referenceName}]
type = "caldav"
url = "${referenceURL}"
username = "${referenceUser}"
password = "${referencePassword}"
# verify = ${verifyReference}
# verify_fingerprint = "${verifyFingerprintReference}"
';

    private VdirSyncerUIConfigRepository $vdirsyncerUiConfigRepository;
    private LoggerInterface              $logger;

    public function __construct(
        VdirSyncerUIConfigRepository $vdirsyncerUiConfigRepository,
        LoggerInterface              $logger
    ) {
        $this->vdirsyncerUiConfigRepository = $vdirsyncerUiConfigRepository;
        $this->logger                       = $logger;
    }

    public function generatePairName(VdirSyncerUIConfig $config, VdirSyncerUIConfig $reference): string {
        return "{$config->getUniqueId()}{$reference->getUniqueId()}";
    }

    public function fillTemplate(VdirSyncerUIConfigPair $pair): array {

        /** @var VdirSyncerUIConfig $config */
        $config = $this->vdirsyncerUiConfigRepository->findById((int) $pair->getConfigId());
        /** @var VdirSyncerUIConfig $reference */
        $reference = $this->vdirsyncerUiConfigRepository->findById((int) $pair->getReferenceConfigId());

        if (false === $config instanceof VdirSyncerUIConfig) {
            throw new CouldNotFindEntityException('config is not given');
        }

        if (false === $reference instanceof VdirSyncerUIConfig) {
            throw new CouldNotFindEntityException('reference is not given');
        }

        $content                 = SyncerService::TEMPLATE;
        $pairName                = $this->generatePairName($config, $reference);
        $statusDir               = Application::$STATUS_DIR;
        $collectionNamePrincipal = "{$config->getCollection()}{$reference->getCollection()}";
        $collectionNameReference = "{$reference->getCollection()}{$config->getCollection()}";
        $principalName           = Uuid::uuid1()->getHex()->toString();
        $referenceName           = Uuid::uuid1()->getHex()->toString();

        $content = str_replace('${pairName}', $pairName, $content);
        $content = str_replace('${statusDir}', $statusDir, $content);
        $content = str_replace('${principalName}', $principalName, $content);
        $content = str_replace('${referenceName}', $referenceName, $content);
        $content = str_replace('${collectionNamePrincipal}', $collectionNamePrincipal, $content);
        $content = str_replace('${collectionNameReference}', $collectionNameReference, $content);
        $content = str_replace('${collectionPrincipal}', $config->getCollection(), $content);
        $content = str_replace('${collectionReference}', $reference->getCollection(), $content);

        $content = str_replace('${principalURL}', $config->getStorage(), $content);
        $content = str_replace('${principalUser}', $config->getUsername(), $content);
        $content = str_replace('${principalPassword}', $config->getPassword(), $content);
        $content = str_replace('${referenceURL}', $reference->getStorage(), $content);
        $content = str_replace('${referenceUser}', $reference->getUsername(), $content);
        $content = str_replace('${referencePassword}', $reference->getPassword(), $content);

        if (null !== $config->getFingerprint()) {
            $content = str_replace('# verify = ${verifyPrincipal}', 'verify = ${verifyPrincipal}', $content);
            $content = str_replace('# verify_fingerprint = "${verifyFingerprintPrincipal}"', 'verify_fingerprint = "${verifyFingerprintPrincipal}"', $content);
            $content = str_replace('${verifyPrincipal}', $config->getFingerprint() === null ? 'true' : 'false', $content);
            $content = str_replace('${verifyFingerprintPrincipal}', $config->getFingerprint(), $content);
        }

        if (null !== $reference->getFingerprint()) {
            $content = str_replace('# verify = ${verifyReference}', 'verify = ${verifyReference}', $content);
            $content = str_replace('# verify_fingerprint = "${verifyFingerprintReference}"', 'verify_fingerprint = "${verifyFingerprintReference}"', $content);
            $content = str_replace('${verifyReference}', $reference->getFingerprint() === null ? 'true' : 'false', $content);
            $content = str_replace('${verifyFingerprintReference}', $reference->getFingerprint(), $content);
        }

        return [
            'content'                 => $content,
            'pairname'                => $pairName,
            'collectionnamePrincipal' => $collectionNamePrincipal,
            'collectionnameReference' => $collectionNameReference
        ];
    }

    public function sync(): void {
        $this->logger->debug('starting syncer job: ' . (new DateTime())->format('Y-m-d H:i:s'));
        $all = $this->vdirsyncerUiConfigRepository->findAll();

        foreach ($all as $vdirsyncerUiConfig) {

            foreach ($vdirsyncerUiConfig->getPairs() as $pair) {

                $result                  = $this->fillTemplate($pair);
                $pairName                = $result['pairname'];
                $collectionNamePrincipal = $result['collectionnamePrincipal'];
                $configPath              = Application::$DATA_DIR . '/' . $pairName;

                $this->logger->debug('configPath: ' . $configPath);
                $level      = SyncerService::LOG_LEVEL;
                $sync       = "vdirsyncer -v $level --config $configPath sync --force-delete $pairName/$collectionNamePrincipal<<-EOF
y
y
EOF";
                $output     = [];
                $resultCode = -1;
                exec($sync, $output, $resultCode);

                $this->logger->debug('result sync: ' . json_encode($output, JSON_PRETTY_PRINT));
                $this->logger->debug('result code sync: ' . $resultCode);

            }
        }

        $this->logger->debug('ending syncer job: ' . (new DateTime())->format('Y-m-d H:i:s'));

    }

    public function discover(): void {
        $this->logger->debug('starting discover job: ' . (new DateTime())->format('Y-m-d H:i:s'));
        $all = $this->vdirsyncerUiConfigRepository->findAll();

        foreach ($all as $vdirsyncerUiConfig) {

            foreach ($vdirsyncerUiConfig->getPairs() as $pair) {

                $result     = $this->fillTemplate($pair);
                $pairName   = $result['pairname'];
                $configPath = Application::$DATA_DIR . '/' . $pairName;

                $this->logger->debug('configPath: ' . $configPath);
                $level      = SyncerService::LOG_LEVEL;
                $discover   = "vdirsyncer -v $level --config $configPath discover $pairName<<-EOF
y
y
EOF";
                $output     = [];
                $resultCode = -1;
                exec($discover, $output, $resultCode);

                $this->logger->debug('result discover: ' . json_encode($output, JSON_PRETTY_PRINT));
                $this->logger->debug('result code discover: ' . $resultCode);

            }
        }

        $this->logger->debug('ending discover job: ' . (new DateTime())->format('Y-m-d H:i:s'));

    }

    public function isValidCollection(string $collection): bool {
        return strlen(trim($collection)) > 0;
    }

}
