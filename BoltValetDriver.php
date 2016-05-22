<?php

class BoltSmsRecursiveFilterIterator extends RecursiveFilterIterator {

    public static $FILTERS = [
        '.',
        '..',
        'node_modules',
        'bower_components'
    ];

    public function accept() {
        return ! in_array(
            $this->current()->getFilename(),
            self::$FILTERS,
            true
        );
    }

}

class BoltValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        if (file_exists($sitePath.'/app/nut')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $recursiveThemeIterator = new RecursiveDirectoryIterator($sitePath.'/theme/');
        $filteredThemeIterator = new BoltSmsRecursiveFilterIterator($recursiveThemeIterator);
        $filteredRecursiveThemeIterator = new RecursiveIteratorIterator($filteredThemeIterator);

        $recursiveFilesIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sitePath.'/files/')
        );

        $staticFiles = new AppendIterator();
        $staticFiles->append($filteredRecursiveThemeIterator);
        $staticFiles->append($recursiveFilesIterator);

        //foreach ($staticFiles as $file) {
            if (file_exists($staticFilePath = $sitePath.$uri)) {
                return $staticFilePath;
            }
        //}

        if (file_exists($staticFilePath = $sitePath.'/files/'.$uri)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        return $sitePath.'/index.php';
    }
}
