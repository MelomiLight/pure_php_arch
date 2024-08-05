<?php

namespace Helpers;
class GetClassNameFromFile
{
    /**
     * Get the fully qualified class name from a file.
     *
     * @param string $filePath The path to the PHP file.
     * @return string|null The fully qualified class name, or null if not found.
     */
    public function getClassNameFromFile(string $filePath): ?string
    {
        $className = null;
        $namespace = '';

        if (!file_exists($filePath)) {
            return null;
        }

        $contents = file_get_contents($filePath);
        $tokens = token_get_all($contents);
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (is_array($token)) {
                if ($token[0] === T_NAMESPACE) {
                    $namespace = '';
                    $i++;
                    while ($tokens[$i] !== ';' && $tokens[$i] !== '{') {
                        if (is_array($tokens[$i])) {
                            $namespace .= $tokens[$i][1];
                        }
                        $i++;
                    }
                    $namespace = trim($namespace) . '\\';
                }

                if ($token[0] === T_CLASS) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        if ($tokens[$j] === '{') {
                            $className = $tokens[$i + 2][1];
                            break 2;
                        }
                    }
                }
            }
            $i++;
        }

        return $namespace . $className;
    }

}
