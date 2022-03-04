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

namespace OCA\VdirSyncerUI\Db\Pair;

use OCA\VdirSyncerUI\Db\Config\VdirSyncerUIConfig;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class VdirSyncerUIConfigPairRepository extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'vdirsyncerui_config_pairs', VdirSyncerUIConfigPair::class);
    }

    public function addPair(VdirSyncerUIConfig $config): Entity {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('config_id', $qb->createNamedParameter($config->getId()))
            );

        foreach ($this->findEntities($qb) as $entity) {
            $config->addPair($entity);
        }
        return $config;
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

    public function isPaired(int $configId, int $referenceId): bool {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('config_id', $qb->createNamedParameter($configId))
            )
            ->andWhere(
                $qb->expr()->eq('reference_config_id', $qb->createNamedParameter($referenceId))
            );

        $entities = $this->findEntities($qb);
        return count($entities) > 0;
    }

}
