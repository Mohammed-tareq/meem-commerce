<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\MeemProduct;

class MeemProductSeeder extends Seeder
{
    /**
     * Seed the meem_products table from the products.csv file.
     *
     * @return void
     */
    public function run()
    {
        $csvPath = base_path('products.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Could not open CSV file.");
            return;
        }

        // Read header row
        $header = fgetcsv($handle);
        if ($header === false) {
            $this->command->error("Could not read CSV header.");
            fclose($handle);
            return;
        }

        // Map header columns to indices
        $headerMap = array_flip($header);

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $batch[] = [
                'name' => $row[$headerMap['name']] ?? '',
                'category' => $row[$headerMap['category']] ?? null,
                'description' => $row[$headerMap['description']] ?? null,
                'image_url' => $row[$headerMap['image_url']] ?? null,
                'price' => floatval($row[$headerMap['price']] ?? 0),
                'url' => $row[$headerMap['url']] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert in batches of 50
            if (count($batch) >= 50) {
                DB::table('meem_products')->insert($batch);
                $batch = [];
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            DB::table('meem_products')->insert($batch);
        }

        fclose($handle);

        $this->command->info("Imported {$count} products into meem_products table.");
    }
}
