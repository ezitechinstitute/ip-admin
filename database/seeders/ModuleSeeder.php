<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use Illuminate\Support\Facades\File;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Define all role-based JSON files and their primary role[cite: 7, 8, 9, 11, 13]
        $roleFiles = [
        'admin'      => 'verticalMenu.json',
        'manager'    => 'managerMenu.json',
        'supervisor' => 'supervisorMenu.json',
        'intern'     => 'internMenu.json'
    ];

    foreach ($roleFiles as $role => $file) {
        $path = base_path("resources/menu/{$file}");
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path));
            foreach ($data->menu as $menu) {
                // Save Parent Menu
                if (isset($menu->slug)) {
                    $slug = is_array($menu->slug) ? $menu->slug[0] : $menu->slug;
                    \App\Models\Module::updateOrCreate(
                        ['slug' => $slug],
                        ['name' => $menu->name ?? 'Header', 'role_access' => $role]
                    );
                }
                // Save Submenu items (This is likely what you are missing!)
                if (isset($menu->submenu)) {
                    foreach ($menu->submenu as $sub) {
                        $subSlug = is_array($sub->slug) ? $sub->slug[0] : $sub->slug;
                        \App\Models\Module::updateOrCreate(
                            ['slug' => $subSlug],
                            ['name' => $sub->name, 'role_access' => $role]
                        );
                    }
                }
            }
        }
    }
    }

    /**
     * Recursive function to handle nested submenus
     */
    private function processMenuItems($items, $role)
    {
        foreach ($items as $item) {
            // Only process items that have both a name and a slug[cite: 7, 8, 9]
            if (isset($item['name']) && isset($item['slug'])) {
                
                // Handle slugs that are arrays (like in the Pages menu)
                $slug = is_array($item['slug']) ? $item['slug'][0] : $item['slug'];

                Module::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $item['name'],
                        'role_access' => $role
                    ]
                );
            }

            // If this item has a submenu, process it recursively[cite: 11, 13]
            if (isset($item['submenu'])) {
                $this->processMenuItems($item['submenu'], $role);
            }
        }
    }
}