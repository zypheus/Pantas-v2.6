<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    /**
     * ISBN lookup via Google Books API (often stronger for Philippine / regional publishers than Open Library).
     *
     * @return array<string, mixed>|null Same keys as OpenLibraryService::lookupByIsbn where possible.
     */
    public function lookupByIsbn(string $isbn): ?array
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);
        if ($isbn === '') {
            return null;
        }

        $url = 'https://www.googleapis.com/books/v1/volumes?q='.rawurlencode('isbn:'.$isbn).'&maxResults=1';
        $key = config('services.google_books.key');
        if (is_string($key) && $key !== '') {
            $url .= '&key='.rawurlencode($key);
        }

        $response = Http::timeout(15)->get($url);

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();
        $item = $payload['items'][0] ?? null;
        if (! is_array($item)) {
            return null;
        }

        $info = $item['volumeInfo'] ?? null;
        if (! is_array($info)) {
            return null;
        }

        $title = (string) ($info['title'] ?? '');
        if (isset($info['subtitle']) && is_string($info['subtitle']) && $info['subtitle'] !== '') {
            $title = $title !== '' ? $title.' : '.$info['subtitle'] : $info['subtitle'];
        }

        $authors = [];
        if (! empty($info['authors']) && is_array($info['authors'])) {
            foreach ($info['authors'] as $a) {
                if (is_string($a) && $a !== '') {
                    $authors[] = $a;
                }
            }
        }

        $published = isset($info['publishedDate']) ? (string) $info['publishedDate'] : '';
        $pubYear = $published !== '' ? (preg_match('/^(\d{4})/', $published, $m) ? $m[1] : $published) : null;

        $coverUrl = $this->bestCoverUrl($info['imageLinks'] ?? null);

        $categories = [];
        if (! empty($info['categories']) && is_array($info['categories'])) {
            foreach ($info['categories'] as $c) {
                if (is_string($c) && $c !== '') {
                    $categories[] = $c;
                }
            }
        }

        $byStatement = null;
        if ($authors !== [] && $title !== '') {
            $byStatement = implode(', ', $authors).' — '.$title;
        }

        return [
            'isbn' => $isbn,
            'main_author' => $authors !== [] ? implode('; ', $authors) : null,
            'title_statement' => $title !== '' ? $title : null,
            'pub_place' => null,
            'publisher' => isset($info['publisher']) ? (string) $info['publisher'] : null,
            'pub_year' => $pubYear,
            'pages' => isset($info['pageCount']) ? (string) $info['pageCount'] : null,
            'cover_image' => $coverUrl,
            'edition' => null,
            'general_note' => isset($info['description']) ? (is_string($info['description']) ? $info['description'] : null) : null,
            'subject_topic' => $categories !== [] ? implode('; ', $categories) : null,
            'year' => $pubYear,
            'title_author' => $byStatement,
            'volume' => null,
            'series_title' => null,
        ];
    }

    /**
     * @param  mixed  $imageLinks
     */
    protected function bestCoverUrl($imageLinks): ?string
    {
        if (! is_array($imageLinks)) {
            return null;
        }

        foreach (['extraLarge', 'large', 'medium', 'small', 'thumbnail', 'smallThumbnail'] as $k) {
            if (! empty($imageLinks[$k]) && is_string($imageLinks[$k])) {
                $u = $imageLinks[$k];

                return str_replace('http://', 'https://', $u);
            }
        }

        return null;
    }
}
