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

namespace OCA\VdirSyncerUI\Db\Config;

use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPairRepository;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;

class VdirSyncerUIConfigRepository extends QBMapper {

    private VdirSyncerUIConfigPairRepository $vdirsyncerUiConfigPairRepository;

    public function __construct(
        IDBConnection                    $db,
        VdirSyncerUIConfigPairRepository $vdirsyncerUiConfigPairRepository
    ) {
        parent::__construct($db, 'vdirsyncerui_config', VdirSyncerUIConfig::class);
        $this->vdirsyncerUiConfigPairRepository = $vdirsyncerUiConfigPairRepository;
    }

    public function find(string $userId): array {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
            );

        $configs = $this->findEntities($qb);
        $new     = [];

        foreach ($configs as $key => $config) {
            $config    = $this->vdirsyncerUiConfigPairRepository->addPair($config);
            $new[$key] = $config;
        }
        return $new;
    }


    public function findById(int $id): Entity {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('id', $qb->createNamedParameter($id))
            );

        return $this->findEntity($qb);
    }

    public function isDuplicatedForUser(
        string $storage,
        string $collection,
        string $username,
        string $userId
    ): bool {
        $qb = $this->db->getQueryBuilder();
        $qb
            ->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('storage', $qb->createNamedParameter($storage))
            )
            ->andWhere(
                $qb->expr()->eq('collection', $qb->createNamedParameter($collection))
            )
            ->andWhere(
                $qb->expr()->eq('username', $qb->createNamedParameter($username))
            )
            ->andWhere(
                $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
            );

        $entities = $this->findEntities($qb);
        return count($entities) > 0;
    }

    /**
     * @return VdirSyncerUIConfig[]
     * @throws Exception
     */
    public function findAll(): array {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName());

        $configs = $this->findEntities($qb);
        $new     = [];

        /**
         * @var VdirSyncerUIConfig $config
         */
        foreach ($configs as $key => $config) {
            $config                = $this->vdirsyncerUiConfigPairRepository->addPair($config);
            $new[$config->getId()] = $config;
        }
        return $new;
    }

}
