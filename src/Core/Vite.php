<?php

namespace Themosis\Core;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Vite
{
    /**
     * Generate the HTML tags for the given Vite entry point(s).
     *
     * @param string|array $entrypoints       One entry ("resources/js/app.js") or an
     *                                        array of entries.
     * @param string       $manifestDirectory Same role as Mix's $manifestDirectory:
     *                                        defaults to the active theme's dist
     *                                        folder when WordPress is loaded.
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function __invoke($entrypoints, $manifestDirectory = '')
    {
        static $manifests = [];

        $entrypoints = (array) $entrypoints;

        // Default to the users theme if available, otherwise the public path
        if (! $manifestDirectory && function_exists('wp_get_theme')) {
            $manifestDirectory = '/content/themes/' . wp_get_theme()->stylesheet . '/dist';
        }

        if ($manifestDirectory == '/') {
            $manifestDirectory = '';
        }

        if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        $hotFile = public_path($manifestDirectory . '/hot');

        if (file_exists($hotFile)) {
            $url = rtrim(file_get_contents($hotFile));

            $tags = ['<script type="module" src="' . $url . '/@vite/client"></script>'];

            foreach ($entrypoints as $entrypoint) {
                $assetUrl = $url . '/' . ltrim($entrypoint, '/');

                $tags[] = $this->isCssPath($assetUrl)
                    ? '<link rel="stylesheet" href="' . $assetUrl . '">'
                    : '<script type="module" src="' . $assetUrl . '"></script>';
            }

            return new HtmlString(implode('', $tags));
        }

        $manifestPath = public_path($manifestDirectory . '/manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Vite manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];
        $debug = app('config')->get('app.debug');
        $baseUrl = app('config')->get('app.asset_url') . $manifestDirectory;

        $tags = [];

        foreach ($entrypoints as $entrypoint) {
            $chunk = $this->chunk($manifest, $entrypoint, $debug);

            if (is_null($chunk)) {
                continue;
            }

            foreach ($chunk['imports'] ?? [] as $import) {
                $tags[] = '<link rel="modulepreload" href="' . $baseUrl . '/' . $manifest[$import]['file'] . '">';
            }

            foreach ($chunk['css'] ?? [] as $css) {
                $tags[] = '<link rel="stylesheet" href="' . $baseUrl . '/' . $css . '">';
            }

            $assetUrl = $baseUrl . '/' . $chunk['file'];

            $tags[] = $this->isCssPath($assetUrl)
                ? '<link rel="stylesheet" href="' . $assetUrl . '">'
                : '<script type="module" src="' . $assetUrl . '"></script>';
        }

        return new HtmlString(implode('', $tags));
    }

    /**
     * Resolve a single entrypoint's chunk from the manifest, honoring the
     * same debug/production degrade behavior as Mix.
     *
     * @param array  $manifest
     * @param string $entrypoint
     * @param bool   $debug
     *
     * @throws \Exception
     *
     * @return array|null
     */
    protected function chunk(array $manifest, $entrypoint, $debug)
    {
        if (! isset($manifest[$entrypoint])) {
            $exception = new Exception("Unable to locate file in Vite manifest: {$entrypoint}.");

            if (! $debug) {
                report($exception);

                return null;
            }

            throw $exception;
        }

        return $manifest[$entrypoint];
    }

    /**
     * Determine whether the given path is a CSS file.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isCssPath($path)
    {
        return preg_match('/\.(css|less|sass|scss|styl|stylus|pcss|postcss)(\?[^\.]*)?$/', $path) === 1;
    }
}
