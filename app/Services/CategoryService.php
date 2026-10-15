<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\User;

class CategoryService
{
    public static function getTesdaBaseCategories(): array
    {
        return [
            'Plumbing Repair' => [
                'desc' => 'Pipe fitting, drainage, leakage, seals, water system installation and repair.',
                'icon' => 'fa-faucet-drip',
                'keywords' => ['plumb', 'pipe', 'drainage', 'tubo', 'water']
            ],
            'Electrical Installation' => [
                'desc' => 'Wiring, circuit breaker troubleshooting, outlets, lighting fixtures, panel boards.',
                'icon' => 'fa-bolt',
                'keywords' => ['electr', 'kuryente', 'wiring', 'breaker', 'light']
            ],
            'Carpentry & Roofing' => [
                'desc' => 'Furniture, ceilings, doors, cabinetry, roofing repair, framing, wooden structures.',
                'icon' => 'fa-hammer',
                'keywords' => ['carpen', 'wood', 'roof', 'karpintero', 'kisame', 'bubong']
            ],
            'Welding & Fabrication' => [
                'desc' => 'Steel gates, window grills, structural metalworks, SMAW arc welding.',
                'icon' => 'fa-fire',
                'keywords' => ['weld', 'bakal', 'grill', 'metal', 'steel', 'smaw']
            ],
            'Masonry & Construction' => [
                'desc' => 'Concrete hollow blocks, tiling, plastering, cement, wall repair, masonry works.',
                'icon' => 'fa-trowel-bricks',
                'keywords' => ['mason', 'tile', 'semento', 'concrete', 'plaster']
            ],
            'Appliance & Refrigeration Repair' => [
                'desc' => 'Air conditioning cleaning, refrigerator maintenance, washing machine repair.',
                'icon' => 'fa-tv',
                'keywords' => ['appliance', 'aircon', 'ref', 'fridge', 'washing']
            ],
            'IT & Computer Systems Servicing' => [
                'desc' => 'Hardware repair, networking, software diagnostics, IT technician services, CSS NC II.',
                'icon' => 'fa-laptop-code',
                'keywords' => ['it', 'computer', 'technician', 'laptop', 'hardware', 'software', 'network']
            ],
            'Automotive & Small Engine Servicing' => [
                'desc' => 'Motorcycle tuning, automobile engine maintenance, brake & electrical repairs.',
                'icon' => 'fa-wrench',
                'keywords' => ['auto', 'motor', 'mechanic', 'mekaniko', 'car', 'engine']
            ],
            'Housekeeping & Domestic Services' => [
                'desc' => 'Home deep cleaning, sanitation, laundry, housekeeping assistance, domestic work.',
                'icon' => 'fa-broom',
                'keywords' => ['housekeep', 'clean', 'domestic', 'linis', 'laundry']
            ],
            'Driving & Transport Services' => [
                'desc' => 'Light vehicle driving, passenger transport, truck logistics, heavy equipment.',
                'icon' => 'fa-truck-fast',
                'keywords' => ['driv', 'transport', 'deliver']
            ],
            'Bread & Pastry Production / Culinary' => [
                'desc' => 'Baking, pastry prep, catering assistance, food handling, culinary services.',
                'icon' => 'fa-bread-slice',
                'keywords' => ['bread', 'pastry', 'baking', 'cook', 'culinary', 'food']
            ],
            'Electronics & Mechatronics' => [
                'desc' => 'Electronic device repair, solar power wiring, PCB troubleshooting, mechatronics.',
                'icon' => 'fa-microchip',
                'keywords' => ['electronic', 'mechatronic', 'circuit', 'solar']
            ],
            'Painting & Surface Finishing' => [
                'desc' => 'Exterior & interior house painting, varnishing, waterproof coating, wall finishing.',
                'icon' => 'fa-paint-roller',
                'keywords' => ['paint', 'pintor', 'varnish']
            ],
        ];
    }

    public static function getCategoriesMap(): array
    {
        $tesdaBaseCategories = self::getTesdaBaseCategories();

        $skilledWorkers = User::where(function ($q) {
            $q->where('role', 'skilled worker')
              ->orWhere('role', 'worker');
        })->get();

        $jobPosts = JobPost::all();

        $categoriesMap = [];

        // Seed with TESDA base categories
        foreach ($tesdaBaseCategories as $catName => $info) {
            $categoriesMap[$catName] = [
                'title' => $catName,
                'desc' => $info['desc'],
                'icon' => $info['icon'],
                'keywords' => $info['keywords'],
                'worker_count' => 0,
                'active_workers' => [],
                'job_count' => 0,
                'is_tesda_standard' => true,
            ];
        }

        // Map skilled workers into categories, and dynamically create new categories for novel skills
        foreach ($skilledWorkers as $worker) {
            $rawSkills = $worker->skills ?? 'General Handyman';
            $skillParts = array_map('trim', explode(',', $rawSkills));

            foreach ($skillParts as $skill) {
                if (empty($skill)) continue;

                $matchedCategory = null;
                $skillLower = strtolower($skill);

                // Check against existing categories keywords
                foreach ($categoriesMap as $catKey => $catData) {
                    foreach ($catData['keywords'] as $kw) {
                        if (str_contains($skillLower, $kw)) {
                            $matchedCategory = $catKey;
                            break 2;
                        }
                    }
                }

                // If not matched, dynamically create category based on the worker's entered skill
                if (!$matchedCategory) {
                    $catTitle = ucwords($skill);
                    if (!isset($categoriesMap[$catTitle])) {
                        $categoriesMap[$catTitle] = [
                            'title' => $catTitle,
                            'desc' => "Community trade category actively offered by registered skilled workers in Magalang.",
                            'icon' => 'fa-toolbox',
                            'keywords' => [strtolower($skill)],
                            'worker_count' => 0,
                            'active_workers' => [],
                            'job_count' => 0,
                            'is_tesda_standard' => false,
                        ];
                    }
                    $matchedCategory = $catTitle;
                }

                // Increment worker count & add worker reference
                $categoriesMap[$matchedCategory]['worker_count']++;
                $workerDisplayName = ($worker->full_name ?: $worker->name) . ' (' . ($worker->barangay ?? 'Magalang') . ')';
                if (!in_array($workerDisplayName, $categoriesMap[$matchedCategory]['active_workers'])) {
                    $categoriesMap[$matchedCategory]['active_workers'][] = $workerDisplayName;
                }
            }
        }

        // Also check JobPost categories
        foreach ($jobPosts as $job) {
            $cat = trim($job->category ?? '');
            if (empty($cat)) continue;

            if (isset($categoriesMap[$cat])) {
                $categoriesMap[$cat]['job_count']++;
            } else {
                // Check if case-insensitive match
                $found = false;
                foreach ($categoriesMap as $k => $v) {
                    if (strcasecmp($k, $cat) === 0) {
                        $categoriesMap[$k]['job_count']++;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $catTitle = ucwords($cat);
                    $categoriesMap[$catTitle] = [
                        'title' => $catTitle,
                        'desc' => "Registered job requirement category posted by clients in Magalang.",
                        'icon' => 'fa-briefcase',
                        'keywords' => [strtolower($cat)],
                        'worker_count' => 0,
                        'active_workers' => [],
                        'job_count' => 1,
                        'is_tesda_standard' => false,
                    ];
                }
            }
        }

        // Sort: Categories with active workers first, then alphabetical
        uasort($categoriesMap, function ($a, $b) {
            if ($a['worker_count'] === $b['worker_count']) {
                return strcmp($a['title'], $b['title']);
            }
            return $b['worker_count'] <=> $a['worker_count'];
        });

        return $categoriesMap;
    }

    public static function getCategoryNames(): array
    {
        $map = self::getCategoriesMap();
        return array_values(array_map(function ($c) {
            return $c['title'];
        }, $map));
    }
}
