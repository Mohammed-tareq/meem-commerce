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
        $pestCategory = $request->query('pest_category', false);
        $parent = $request->query('parent', false);
        $query = Category::query()->active()->withCount('products');

        if ($term !== '') {
            $query->where(function (Builder $builder) use ($term) {
                $this->applyTranslatableLike($builder, 'name', $term, app()->getLocale());
                $builder->orWhere(function (Builder $sub) use ($term) {
                    $this->applyTranslatableLike($sub, 'details', $term, app()->getLocale());
                });
            });
        }
        if ($parent) {
            $query->whereNull('parent_id');
        }
        if ($pestCategory) {
            $query->orderByDesc('products_count');
        } else {
            $query->orderByDesc('id');
        }


        return $query->paginate($limit);
    }

    public function getBySlug($slug)
    {
        return Category::query()
            ->active()
            ->with(['products', 'children' => function ($query) {
                $query->active()->withCount('products');
            }])
            ->withCount('products')
            ->where('slug', $slug)
            ->firstOrFail();
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
