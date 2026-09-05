<?php

namespace App\Support;

/**
 * Converts the admin panel's plain-text list fields into the JSON structures
 * the models store — one item per line, keeps editing simple.
 */
class ListInput
{
    /**
     * "one per line" → ['one', 'two'].
     */
    public static function lines(?string $value): ?array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $lines ?: null;
    }

    /**
     * "value | label" per line → [['value' => …, 'label' => …]].
     */
    public static function pairs(?string $value, string $firstKey, string $secondKey): ?array
    {
        $pairs = collect(self::lines($value) ?? [])
            ->map(function (string $line) use ($firstKey, $secondKey) {
                $parts = array_map('trim', explode('|', $line, 2));

                if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                    return null;
                }

                return [$firstKey => $parts[0], $secondKey => $parts[1]];
            })
            ->filter()
            ->values()
            ->all();

        return $pairs ?: null;
    }

    /**
     * ['one', 'two'] → "one\ntwo" for rendering back into a textarea.
     */
    public static function toText(?array $items): string
    {
        return implode("\n", $items ?? []);
    }

    /**
     * [['value' => …, 'label' => …]] → "value | label" per line.
     */
    public static function pairsToText(?array $items, string $firstKey, string $secondKey): string
    {
        return collect($items ?? [])
            ->map(fn ($item) => ($item[$firstKey] ?? '').' | '.($item[$secondKey] ?? ''))
            ->implode("\n");
    }
}
