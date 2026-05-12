<?php

namespace App\Services\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Marvel\Database\Models\Category;

class CategoryService
{
    public function paginate(Request $request)
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Category::query()->active()->withCount('products');

        if ($term !== '') {
            $query->where(function (Builder $builder) use ($term) {
                $this->applyTranslatableLike($builder, 'name', $term, app()->getLocale());
                $builder->orWhere(function (Builder $sub) use ($term) {
                    $this->applyTranslatableLike($sub, 'details', $term, app()->getLocale());
                });
            });
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getById($id)
    {
        return Category::query()->active()->with('products')->withCount('products')->where('id', $id)->firstOrFail();
    }
    private function applyTranslatableLike(Builder $query, string $field, string $term, string $locale): void
    {
        $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
    }

    private function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', 15);
        if ($limit <= 0) {
            return 15;
        }

        return min($limit, 100);
    }
}