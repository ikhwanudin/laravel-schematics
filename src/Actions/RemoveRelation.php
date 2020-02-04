<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use ReflectionException;

class RemoveRelation
{
    /**
     * @param $method
     * @return false|int
     */
    public function execute($method)
    {
        $file = $method['file'];
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $index = $method['line'] - 1;

        $removeLines = $this->removeLeading($lines, $index - 1);
        $removeLines = array_merge($removeLines, $this->removeTrailing($lines, $index));

        foreach ($removeLines as $removeLine) {
            unset($lines[$removeLine]);
        }

        return file_put_contents($file, implode("\n", $lines));
    }

    /**
     * @param $lines
     * @param $index
     * @return array
     */
    private function removeLeading($lines, $index): array
    {
        $line = '';
        $remove = [];

        while(! $this->startOfMethod($line)) {
            $line = $lines[$index];

            $remove[] = $index;

            $index--;
        }

        return $remove;
    }

    /**
     * @param $lines
     * @param $index
     * @return array
     */
    private function removeTrailing($lines, $index): array
    {
        $line = '';
        $remove = [];

        while(! $this->endOfMethod($line)) {
            $line = $lines[$index];

            $remove[] = $index;

            $index++;
        }

        if (trim($lines[$index]) === '') {
            $remove[] = $index;
        }

        return $remove;
    }

    /**
     * @param $line
     * @return bool
     */
    private function endOfMethod($line): bool
    {
        return str_replace(' ', '', $line) === '}';
    }

    /**
     * @param $line
     * @return bool
     */
    private function startOfMethod($line): bool
    {
        $line = str_replace(' ', '', $line);

        return $line === '/**'
            || $line === '//'
            || $line === '}';
    }
}