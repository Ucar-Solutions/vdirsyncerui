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

namespace OCA\VdirSyncerUI\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1Date20220114135831 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array   $options
     */
    public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array   $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        $vdirsyncerUiConfig = $schema->createTable('vdirsyncerui_config');
        $vdirsyncerUiConfig->addColumn('id', 'integer', [
            'autoincrement' => true,
            'notnull'       => true,
        ]);
        $vdirsyncerUiConfig->addColumn('username', 'string', [
            'notnull' => true,
            'length'  => 200
        ]);
        $vdirsyncerUiConfig->addColumn('password', 'string', [
            'notnull' => true,
            'length'  => 200
        ]);
        $vdirsyncerUiConfig->addColumn('user_id', 'string', [
            'notnull' => true,
            'length'  => 200,
        ]);
        $vdirsyncerUiConfig->addColumn('url', 'text', [
            'notnull' => true,
            'default' => ''
        ]);

        $vdirsyncerUiConfig->setPrimaryKey(['id']);
        $vdirsyncerUiConfig->addIndex(['user_id'], 'vdsui_user_id_index');
        $vdirsyncerUiConfig->addForeignKeyConstraint('*PREFIX*users', ['user_id'], ['uid'], [], 'vdsui_config_users_idx');

        return $schema;

    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array   $options
     */
    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
    }

}
