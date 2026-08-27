<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixMenuImages extends Command
{
    protected $signature = 'menu:fix-images';
    protected $description = 'Re-upload original seeder images to Supabase and fix broken image paths';

    public function handle()
    {
        $items = MenuItem::all();
        $imagesDir = database_path('seeders/images');

        // Build a lookup: lowercase filename (without extension) => actual filename on disk
        $filesOnDisk = collect(scandir($imagesDir))
            ->filter(fn ($f) => str_ends_with(strtolower($f), '.jpg') || str_ends_with(strtolower($f), '.jpeg') || str_ends_with(strtolower($f), '.png'));

        $fixed = 0;
        $skipped = 0;

        foreach ($items as $item) {
            // Try to find a matching file by comparing item name (case-insensitive, ignoring punctuation)
            $match = $filesOnDisk->first(function ($filename) use ($item) {
                $base = pathinfo($filename, PATHINFO_FILENAME);
                return $this->normalize($base) === $this->normalize($item->name);
            });

            if (! $match) {
                $this->warn("No matching file found for: {$item->name}");
                $skipped++;
                continue;
            }

            $fullPath = $imagesDir.'/'.$match;
            $storedName = uniqid().'_'.$match;

            Storage::disk('supabase')->put($storedName, file_get_contents($fullPath));

            $item->update(['image' => $storedName]);

            $this->info("Fixed: {$item->name} -> {$storedName}");
            $fixed++;
        }

        $this->newLine();
        $this->info("Done. Fixed: {$fixed}, Skipped (no match): {$skipped}");
    }

    private function normalize(string $value): string
    {
        // Lowercase, strip apostrophes/parentheses/extra spaces, so "Bok L'hong" matches "bok lhong" etc.
        $value = strtolower($value);
        $value = preg_replace('/\(.*?\)/', '', $value); // remove "(Sugarcane Juice)" etc.
        $value = preg_replace('/[^a-z0-9]/', '', $value); // strip all non-alphanumeric
        return trim($value);
    }
}