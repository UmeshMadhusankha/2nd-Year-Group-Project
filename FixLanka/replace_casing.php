<?php
$directory = __DIR__;
$replacements = [
    'profilePicture' => 'profile_picture',
    'dateCreated' => 'created_at',
    'dateJoined' => 'joined_at',
    'completedJobsCount' => 'completed_jobs_count'
];

$extensions = ['php', 'js', 'sql'];

function processDirectory($dir, $replacements, $extensions) {
    echo "Processing directory: $dir\n";
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            // skip node_modules, vendor, .git etc.
            if (!in_array($file, ['node_modules', 'vendor', '.git', '.gemini'])) {
                processDirectory($path, $replacements, $extensions);
            }
        } else {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                $content = file_get_contents($path);
                $originalContent = $content;
                $changed = false;
                
                // Do not replace in THIS script itself
                if (basename($path) === 'replace_casing.php') {
                    continue;
                }

                foreach ($replacements as $search => $replace) {
                    if (strpos($content, $search) !== false) {
                        $content = str_replace($search, $replace, $content);
                        $changed = true;
                    }
                }

                if ($changed) {
                    file_put_contents($path, $content);
                    echo "Updated: $path\n";
                }
            }
        }
    }
}

processDirectory($directory, $replacements, $extensions);
echo "Done.\n";
