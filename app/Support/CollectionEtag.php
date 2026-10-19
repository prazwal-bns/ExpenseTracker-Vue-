<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CollectionEtag
{
    /**
     * Return the list, or 304 when the caller's copy is still current.
     *
     * @param  Collection<int, Model>  $models
     * @param  Closure(): JsonResponse  $response
     */
    public static function response(Request $request, Collection $models, Closure $response): JsonResponse
    {
        $etag = self::make($models);

        if (self::matches($request, $etag)) {
            return JsonResponse::fromJsonString('', 304, self::headers($etag));
        }

        return $response()->withHeaders(self::headers($etag));
    }

    /**
     * @param  Collection<int, Model>  $models
     */
    public static function make(Collection $models): string
    {
        $fingerprint = $models
            ->map(fn (Model $model): string => self::fingerprint($model))
            ->implode('|');

        return '"'.hash('sha256', $fingerprint).'"';
    }

    public static function matches(Request $request, string $etag): bool
    {
        return trim((string) $request->headers->get('If-None-Match')) === $etag;
    }

    /**
     * @return array<string, string>
     */
    private static function headers(string $etag): array
    {
        return [
            'ETag' => $etag,
            'Cache-Control' => 'private, no-cache',
        ];
    }

    /**
     * Include related values that appear in the JSON but do not touch this row's timestamp.
     */
    private static function fingerprint(Model $model): string
    {
        $parts = [
            (string) $model->getKey(),
            (string) ($model->updated_at?->getTimestamp() ?? 0),
        ];

        if (isset($model->expenses_count)) {
            $parts[] = (string) $model->expenses_count;
        }

        $category = $model->relationLoaded('category') ? $model->getRelation('category') : null;

        if ($category instanceof Model) {
            $parts[] = (string) $category->getKey();
            $parts[] = (string) ($category->updated_at?->getTimestamp() ?? 0);
        }

        return implode(':', $parts);
    }
}
