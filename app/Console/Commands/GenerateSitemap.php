<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for public pages';

    /**
     * Route prefixes to exclude from the sitemap.
     *
     * @var array<string>
     */
    protected array $excludePrefixes = [
        'admin',
        'api',
        'auth',
        'broadcasting',
        'login',
        'register',
        'logout',
        'horizon',
        'sanctum',
        'up',
        // Utility endpoints. /health answers 503 whenever any check fails, so
        // listing it puts a 5xx URL in the sitemap; /release is JSON; _inertia is
        // the dev-tools route, present whenever the command is run locally.
        'health',
        'release',
        '_inertia',
        '_debugbar',
        'storage',
    ];

    /**
     * Route names to exclude from the sitemap.
     *
     * @var array<string>
     */
    protected array $excludeNames = [
        'login',
        'register',
        'password.*',
        'verification.*',
        'health',
        'release.version',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add static routes
        $this->addStaticRoutes($sitemap);

        // Add dynamic pages (products, posts, etc.)
        $this->addDynamicPages($sitemap);

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap generated at: {$path}");

        return Command::SUCCESS;
    }

    /**
     * Add static routes to the sitemap.
     */
    protected function addStaticRoutes(Sitemap $sitemap): void
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => $this->shouldIncludeRoute($route));

        foreach ($routes as $route) {
            // Skip routes with parameters (handled in addDynamicPages)
            if (str_contains($route->uri(), '{')) {
                continue;
            }

            $url = url($route->uri());

            $sitemap->add(
                Url::create($url)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority($route->uri() === '/' ? 1.0 : 0.8)
            );

            $this->line("  Added: {$url}");
        }
    }

    /**
     * Add dynamic pages to the sitemap.
     *
     * Customize this method to add your dynamic content pages.
     * Examples: products, blog posts, categories, etc.
     */
    protected function addDynamicPages(Sitemap $sitemap): void
    {
        // Example: Add all published products
        // Product::where('published', true)->each(function ($product) use ($sitemap) {
        //     $sitemap->add(
        //         Url::create(route('products.show', $product))
        //             ->setLastModificationDate($product->updated_at)
        //             ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        //             ->setPriority(0.8)
        //     );
        //     $this->line("  Added: " . route('products.show', $product));
        // });

        // Example: Add all blog posts
        // Post::published()->each(function ($post) use ($sitemap) {
        //     $sitemap->add(
        //         Url::create(route('blog.show', $post->slug))
        //             ->setLastModificationDate($post->updated_at)
        //             ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        //             ->setPriority(0.7)
        //     );
        //     $this->line("  Added: " . route('blog.show', $post->slug));
        // });

        // Example: Add category pages
        // Category::all()->each(function ($category) use ($sitemap) {
        //     $sitemap->add(
        //         Url::create(route('categories.show', $category->slug))
        //             ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        //             ->setPriority(0.6)
        //     );
        // });
    }

    /**
     * Determine if a route should be included in the sitemap.
     */
    protected function shouldIncludeRoute(\Illuminate\Routing\Route $route): bool
    {
        // Only include GET routes
        if (! in_array('GET', $route->methods())) {
            return false;
        }

        // Exclude routes that require authentication
        $middleware = $route->middleware();
        if (in_array('auth', $middleware) || in_array('auth:sanctum', $middleware)) {
            return false;
        }

        // Exclude guest-only routes (login, register)
        if (in_array('guest', $middleware)) {
            return false;
        }

        // Exclude routes by prefix
        $uri = $route->uri();
        foreach ($this->excludePrefixes as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                return false;
            }
        }

        // Exclude routes by name pattern
        $name = $route->getName();
        if ($name) {
            foreach ($this->excludeNames as $pattern) {
                if (str_contains($pattern, '*')) {
                    $regex = '/^'.str_replace('*', '.*', $pattern).'$/';
                    if (preg_match($regex, $name)) {
                        return false;
                    }
                } elseif ($name === $pattern) {
                    return false;
                }
            }
        }

        return true;
    }
}
