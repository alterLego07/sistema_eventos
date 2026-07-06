<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Elegante',
                'slug' => 'elegante',
                'description' => 'Un diseño sofisticado con tonos dorados y oscuros, ideal para bodas y eventos formales.',
                'preview_image' => null,
                'configuration' => [
                    'colors' => [
                        'primary' => '#C9A96E',
                        'secondary' => '#1a1a2e',
                        'accent' => '#D4AF37',
                        'background' => '#0f0f23',
                        'text' => '#f5f5f5',
                    ],
                    'fonts' => [
                        'heading' => 'Playfair Display',
                        'body' => 'Cormorant Garamond',
                    ],
                    'sections' => ['hero', 'message', 'details', 'countdown', 'rsvp', 'location', 'footer'],
                    'animations' => [
                        'entrance' => 'fadeInUp',
                        'scroll' => true,
                    ],
                    'style' => 'elegant',
                ],
                'active' => true,
            ],
            [
                'name' => 'Minimalista',
                'slug' => 'minimalista',
                'description' => 'Diseño limpio y moderno con espacios amplios, perfecto para cualquier tipo de evento.',
                'preview_image' => null,
                'configuration' => [
                    'colors' => [
                        'primary' => '#2d2d2d',
                        'secondary' => '#ffffff',
                        'accent' => '#6c63ff',
                        'background' => '#fafafa',
                        'text' => '#333333',
                    ],
                    'fonts' => [
                        'heading' => 'Inter',
                        'body' => 'Inter',
                    ],
                    'sections' => ['hero', 'message', 'details', 'rsvp', 'location', 'footer'],
                    'animations' => [
                        'entrance' => 'fadeIn',
                        'scroll' => false,
                    ],
                    'style' => 'minimal',
                ],
                'active' => true,
            ],
            [
                'name' => 'Moderna',
                'slug' => 'moderna',
                'description' => 'Plantilla vibrante con gradientes y efectos dinámicos para eventos contemporáneos.',
                'preview_image' => null,
                'configuration' => [
                    'colors' => [
                        'primary' => '#667eea',
                        'secondary' => '#764ba2',
                        'accent' => '#f093fb',
                        'background' => '#0c0c1d',
                        'text' => '#ffffff',
                    ],
                    'fonts' => [
                        'heading' => 'Outfit',
                        'body' => 'DM Sans',
                    ],
                    'sections' => ['hero', 'message', 'details', 'countdown', 'rsvp', 'song', 'location', 'footer'],
                    'animations' => [
                        'entrance' => 'slideUp',
                        'scroll' => true,
                    ],
                    'style' => 'modern',
                ],
                'active' => true,
            ],
            [
                'name' => 'Floral',
                'slug' => 'floral',
                'description' => 'Diseño romántico con tonos suaves y detalles florales, ideal para bodas y quinceañeras.',
                'preview_image' => null,
                'configuration' => [
                    'colors' => [
                        'primary' => '#d4a0a0',
                        'secondary' => '#f5e6e0',
                        'accent' => '#8fbc8f',
                        'background' => '#fdf8f5',
                        'text' => '#5c4033',
                    ],
                    'fonts' => [
                        'heading' => 'Great Vibes',
                        'body' => 'Lora',
                    ],
                    'sections' => ['hero', 'message', 'details', 'countdown', 'rsvp', 'dietary', 'location', 'footer'],
                    'animations' => [
                        'entrance' => 'fadeInUp',
                        'scroll' => true,
                    ],
                    'style' => 'floral',
                ],
                'active' => true,
            ],
            [
                'name' => 'Corporativa',
                'slug' => 'corporativa',
                'description' => 'Plantilla profesional y seria para eventos empresariales, conferencias y reuniones.',
                'preview_image' => null,
                'configuration' => [
                    'colors' => [
                        'primary' => '#1e3a5f',
                        'secondary' => '#f0f4f8',
                        'accent' => '#4a90d9',
                        'background' => '#ffffff',
                        'text' => '#2d3748',
                    ],
                    'fonts' => [
                        'heading' => 'Roboto',
                        'body' => 'Open Sans',
                    ],
                    'sections' => ['hero', 'details', 'rsvp', 'location', 'footer'],
                    'animations' => [
                        'entrance' => 'fadeIn',
                        'scroll' => false,
                    ],
                    'style' => 'corporate',
                ],
                'active' => true,
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['slug' => $template['slug']],
                $template,
            );
        }
    }
}
