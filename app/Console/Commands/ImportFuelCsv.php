<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

/**
 * Import refuel rows from a CarExpenses (Android app) TSV/CSV export
 * into the Car spending-list as Fuel entries. Idempotent — re-running
 * the command skips rows that already exist (matched by date + odometer
 * + litres).
 */
class ImportFuelCsv extends Command
{
    protected $signature = 'kharcha:import-fuel-csv {file : Path to the CarExpenses TSV/CSV export}';

    protected $description = 'Import refuel records from a CarExpenses export into Entries (Car list, Fuel category)';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $carList = SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first();
        if (! $carList) {
            $this->error('No Car (vehicle) spending list exists — seed one first.');

            return self::FAILURE;
        }

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

        $lines = preg_split("/\r\n|\r|\n/", (string) file_get_contents($path));

        $inRecords = false;
        $headerIndex = null;
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($lines as $line) {
            $trim = trim($line);

            if (str_starts_with($trim, '### records info')) {
                $inRecords = true;
                $headerIndex = null;

                continue;
            }
            if (str_starts_with($trim, '###')) {
                $inRecords = false;

                continue;
            }
            if (! $inRecords || $trim === '') {
                continue;
            }

            $cols = array_map('trim', explode("\t", $line));

            if ($headerIndex === null) {
                $headerIndex = array_flip($cols);

                continue;
            }

            try {
                $col = fn (string $name) => $cols[$headerIndex[$name] ?? -1] ?? '';

                // Only refuel records — CarExpenses uses mark=10 for fuel.
                if ((int) $col('mark') !== 10) {
                    continue;
                }

                $dateStr = $col('date');
                if (strlen($dateStr) !== 8) {
                    continue;
                }
                $date = Carbon::createFromFormat('Ymd', $dateStr)->setTime(12, 0);

                $odometer = (int) $col('mileage');
                $liters = (float) $col('volume');
                $rate = (float) $col('volumecost');
                $cost = (float) $col('cost');
                $fuelType = $col('type') ?: 'E92';
                $note = trim($col('note'));
                $isFull = $note === '' ? true : str_contains(strtolower($note), 'top');

                if ($liters <= 0 || $cost <= 0) {
                    continue;
                }

                $exists = Entry::query()
                    ->where('spending_list_id', $carList->id)
                    ->whereDate('purchased_at', $date->toDateString())
                    ->where('odometer', $odometer)
                    ->where('fuel_liters', $liters)
                    ->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                Entry::create([
                    'spending_list_id' => $carList->id,
                    'category_id' => $fuelCategoryId,
                    'item_name' => "Fuel · {$fuelType}",
                    'amount' => $cost,
                    'quantity' => 1,
                    'purchased_at' => $date,
                    'source' => Entry::SOURCE_MANUAL,
                    'notes' => trim('Imported from CarExpenses CSV. '.$note),
                    'fuel_liters' => $liters,
                    'fuel_rate' => $rate,
                    'odometer' => $odometer,
                    'fuel_type' => $fuelType,
                    'is_full_tank' => $isFull,
                ]);

                $imported++;
            } catch (Throwable $e) {
                $errors++;
                $this->warn("Row skipped: {$e->getMessage()}");
            }
        }

        $this->info("Imported: {$imported}   Already present: {$skipped}".($errors ? "   Errors: {$errors}" : ''));

        return self::SUCCESS;
    }
}
