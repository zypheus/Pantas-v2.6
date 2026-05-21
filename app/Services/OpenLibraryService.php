<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    public function lookupByIsbn(string $isbn): ?array
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);

        $response = Http::timeout(15)
            ->get("https://openlibrary.org/isbn/{$isbn}.json");

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        // Map as much as possible to your DB fields
        return [
            'isbn'              => $isbn,
            'main_author'       => $this->resolveAuthors($data['authors'] ?? []),
            'title_statement'   => $data['title'] ?? null,
            'pub_place'         => $data['publish_places'][0] ?? null,
            'publisher'         => $data['publishers'][0] ?? null,
            'pub_year'          => $data['publish_date'] ?? null,
            'pages'             => $data['number_of_pages'] ?? null,
            'cover_image'       => $this->getCoverUrl($data),
            'edition'           => $data['edition_name'] ?? null,
            'general_note'      => isset($data['notes'])
                                    ? (is_array($data['notes']) ? implode('; ', $data['notes']) : $data['notes'])
                                    : null,
            'subject_topic'     => isset($data['subjects']) ? implode('; ', $data['subjects']) : null,
            'year'              => $data['publish_date'] ?? null,
            'title_author'      => $data['by_statement'] ?? null,
            // optional: fixed_value / empty
            'volume'            => null,
            'series_title'      => isset($data['series']) ? implode('; ', $data['series']) : null,
            // and other DB fields can stay NULL
        ];
    }

    protected function resolveAuthors(array $authors): string
    {
        $names = [];

        foreach ($authors as $author) {
            if (!isset($author['key'])) continue;

            $response = Http::timeout(10)
                ->get("https://openlibrary.org{$author['key']}.json");

            if ($response->successful()) {
                $name = $response->json()['name'] ?? null;
                if ($name) $names[] = $name;
            }
        }

        return implode('; ', $names);
    }

    protected function getCoverUrl(array $data): ?string
    {
        if (!empty($data['covers']) && is_array($data['covers'])) {
            $coverId = $data['covers'][0];
            return "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
        }

        return null;
    }
}
