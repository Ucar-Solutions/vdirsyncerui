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
class Version2Date20220123145823 extends SimpleMigrationStep {

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

        $vdirsyncerUiConfigPairs = $schema->createTable('vdirsyncerui_config_pairs');
        $vdirsyncerUiConfigPairs->addColumn('id', 'integer', [
            'autoincrement' => true,
            'notnull'       => true,
        ]);
        $vdirsyncerUiConfigPairs->addColumn('config_id', 'integer', ['notnull' => true]);
        $vdirsyncerUiConfigPairs->addColumn('reference_config_id', 'integer', ['notnull' => true]);
        $vdirsyncerUiConfigPairs->setPrimaryKey(['id'], 'vdsui_config_pairs');
        $vdirsyncerUiConfigPairs->addForeignKeyConstraint('*PREFIX*vdirsyncerui_config', ['config_id'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'], 'pcpcmidx');
        $vdirsyncerUiConfigPairs->addForeignKeyConstraint('*PREFIX*vdirsyncerui_config', ['reference_config_id'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'], 'pcpcmidx2');
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
