<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = \Illuminate\Support\Facades\File::files(public_path('image/LogoClient'));
        
        // Ensure storage directory exists
        if (!\Illuminate\Support\Facades\File::exists(storage_path('app/public/client_partners'))) {
            \Illuminate\Support\Facades\File::makeDirectory(storage_path('app/public/client_partners'), 0755, true);
        }

        foreach ($files as $file) {
            $filename = $file->getFilename();
            // Copy file to storage so Filament can manage it
            \Illuminate\Support\Facades\File::copy($file->getPathname(), storage_path('app/public/client_partners/' . $filename));
            
            \App\Models\ClientPartner::create([
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'image' => 'client_partners/' . $filename,
                'link' => '#' // Default dummy link
            ]);
        }
    }
}
