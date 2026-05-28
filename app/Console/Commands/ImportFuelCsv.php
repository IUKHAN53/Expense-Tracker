<?php

namespace App\Console\Commands;

use App\Services\FuelCsvImporter;
use Illuminate\Console\Command;
use Throwable;

/**
 * Thin CLI wrapper around FuelCsvImporter — also callable from the admin
 * panel's "Import CSV" header action.
 */
class ImportFuelCsv extends Command
{
    protected $signature = 'kharcha:import-fuel-csv {file : Path to the CarExpenses TSV/CSV export}';

    protected $description = 'Import refuel records from a CarExpenses export into Entries (Car list, Fuel category)';

    public function handle(): int
    {
        try {
            $result = (new FuelCsvImporter)->importFile($this->argument('file'));
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $line = "Imported: {$result['imported']}   Already present: {$result['skipped']}";
        if ($result['errors']) {
            $line .= "   Errors: {$result['errors']}";
        }
        $this->info($line);

        return self::SUCCESS;
    }
}
