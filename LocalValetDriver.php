<?php use Valet\Drivers\BasicValetDriver;

class LocalValetDriver extends BasicValetDriver {
    public function serves($sitePath, $siteName, $uri): bool { return is_dir($sitePath . '/public/wordpress'); }

    public function isStaticFile($sitePath, $siteName, $uri): bool|string {
        $staticFilePath = $sitePath . '/public' . $uri;
        if ($this->isActualFile($staticFilePath)) {
            return $staticFilePath;
        }

        return false;
    }

    public function frontControllerPath($sitePath, $siteName, $uri): string {
        return parent::frontControllerPath($sitePath . '/public', $siteName, $this->forceTrailingSlash($uri));
    }

    private function forceTrailingSlash($uri) {
        if (str_ends_with($uri, '/wordpress/wp-admin')) {
            header('Location: ' . $uri . '/');
            exit;
        }

        return $uri;
    }
}
