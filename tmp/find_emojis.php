<?php
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(dirname(__DIR__))
);
$emojis_found = [];
foreach ($files as $file) {
    if ($file->getExtension() === 'php' && strpos($file->getPathname(), 'vendor') === false) {
        $content = file_get_contents($file->getPathname());
        // Match common emojis
        if (preg_match_all('/[\x{1F300}-\x{1F5FF}\x{1F900}-\x{1F9FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $emoji = $match[0];
                $offset = $match[1];
                $line = substr_count(substr($content, 0, $offset), "\n") + 1;
                $line_content = trim(explode("\n", substr($content, 0, $offset + 50))[substr_count(substr($content, 0, $offset), "\n")]);
                echo $file->getFilename() . " line " . $line . ": " . $emoji . " -> " . $line_content . "\n";
            }
        }
    }
}
