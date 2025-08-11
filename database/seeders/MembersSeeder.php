<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileObject;

class MembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the CSV file. Please make sure to place your CSV file in the
        // `database/seeders` directory and name it `Members.csv`.
        $file_path = database_path('seeders/Members.csv');

        // Check if the file exists
        if (!file_exists($file_path)) {
            Log::error("CSV file not found at: {$file_path}");
            return;
        }

        // Open the CSV file for reading
        $file = new SplFileObject($file_path, 'r');
        $file->setFlags(SplFileObject::READ_CSV);

        // Temporarily disable foreign key checks to allow truncation of the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear the existing `employee_details` table data to avoid duplicates
        DB::table('employee_details')->truncate();

        // Prepare an array to hold the data for bulk insertion
        $dataToInsert = [];

        // Flag to skip the header row
        $isFirstRow = true;

        foreach ($file as $row) {
            // Skip the first row, which is the header
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Skip empty rows
            if (!is_array($row) || empty($row[0])) {
                continue;
            }

            // Map the CSV columns to your database columns
            // The CSV headers are: employee_code, name, email, mobile, extension, monthly_cost, hourly_cost
            $dataToInsert[] = [
                'employee_code' => $row[0],
                'name'          => $row[1],
                'email'         => $row[2],
                'mobile'        => $row[3],
                'extension'     => $row[4],
                'monthly_cost'  => $row[5],
                'hourly_cost'   => $row[6],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // Insert the data into the 'employee_details' table in one go for better performance
        if (!empty($dataToInsert)) {
            DB::table('employee_details')->insert($dataToInsert);
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Members seeded successfully!');
    }
}
