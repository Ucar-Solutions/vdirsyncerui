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

namespace OCA\VdirSyncerUI\Command;

use OCA\VdirSyncerUI\Service\SyncerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncerCommand extends Command {

    private SyncerService $syncerService;

    public function __construct(
        SyncerService $syncerService
    ) {
        parent::__construct(null);

        $this->syncerService = $syncerService;
    }

    protected function configure() {
        $this
            ->setName('vdirsyncerui:sync')
            ->setDescription('sync your calendars using vdirsyncer');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->syncerService->sync();
        return 0;
    }

}