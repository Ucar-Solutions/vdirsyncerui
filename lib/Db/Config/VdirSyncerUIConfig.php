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

use Exception;
use JsonSerializable;
use OCA\VdirSyncerUI\Db\Pair\VdirSyncerUIConfigPair;
use OCP\AppFramework\Db\Entity;

class VdirSyncerUIConfig extends Entity implements JsonSerializable {

    protected string  $storage     = '';
    protected ?string  $collection  = '';
    protected string  $username    = '';
    protected string  $password    = '';
    protected string  $userId      = '';
    protected ?string $fingerprint = null;
    private array     $pairs       = [];

    public function __construct() {
        $this->addType('id', 'integer');
    }

    public function addPair(VdirSyncerUIConfigPair $pair): void {
        $this->pairs[] = $pair;
    }

    public function getUniqueId(): string {
        if ('' === $this->storage || '' === $this->collection) {
            throw new Exception('url not given');
        }
        return md5($this->storage . $this->collection);
    }

    /**
     * @return VdirSyncerUIConfigPair[]
     */
    public function getPairs(): array {
        return $this->pairs;
    }

    public function jsonSerialize(): array {
        return [
            'id'          => $this->id,
            'storage'     => $this->storage,
            'collection'  => $this->collection,
            'username'    => $this->username,
            'password'    => $this->password,
            'fingerprint' => $this->fingerprint,
            'userId'      => $this->userId
        ];
    }

}
