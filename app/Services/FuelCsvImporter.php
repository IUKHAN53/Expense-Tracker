<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use Carbon\Carbon;
use RuntimeException;
use Throwable;

/**
 * Parses a CarExpenses TSV/CSV export and writes its refuel rows as
 * Fuel entries on the Car spending list. Idempotent — rows that already
 * exist (same date + odometer + litres) are skipped.
 */
class FuelCsvImporter
{
    /** @return array{imported:int, skipped:int, errors:int} */
    public function importFile(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("File not found: {$path}");
        }

        return $this->importContent((string) file_get_contents($path));
    }

    /** @return array{imported:int, skipped:int, errors:int} */
    public function importContent(string $content): array
    {
        $carList = SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first();
        if (! $carList) {
            throw new RuntimeException('No Car (vehicle) spending list exists — seed one first.');
        }

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

        $lines = preg_split("/\r\n|\r|\n/", $content);
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

                // CarExpenses uses mark=10 for refuels.
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
            } catch (Throwable) {
                $errors++;
            }
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }
}
