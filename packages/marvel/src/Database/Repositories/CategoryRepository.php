<?php


namespace Marvel\Database\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Category;
use Marvel\Http\Requests\CategoryCreateRequest;
use Marvel\Traits\MediaManager;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;



class CategoryRepository extends BaseRepository
{
    use MediaManager;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'        => 'like',
        'parent',
        //        'language',
        //        'type.slug',
    ];

    protected $dataArray = [
        'name',
        'slug',
        'details',
        'parent',
        //'type_id',
        // 'icon',
        // 'image',
        // 'banner_image',
        //  'language',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }


    /**
     * Configure the Model
     **/
    public function model()
    {
        return Category::class;
    }

    public function saveCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only($this->dataArray);
            $data['slug'] = $this->makeSlug($request);
            $category = $this->create($data);


            if ($request->has('images')) {
                $this->uploadImages($request, 'images', $category, 'categories', 'categories');
            }
            DB::commit();
            return $category->load('parent');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateCategory($request, $category)
    {
        try {
            DB::beginTransaction();
            $data = $request->only($this->dataArray);
            if (!empty($request->slug) &&  $request->slug != $category['slug']) {
                $data['slug'] = $this->makeSlug($request);
            }
            $category->update($data);
            if ($request->has('images')) {
                $this->updateImages($request, $category, 'categories', 'categories');
            }
            DB::commit();
            return $this->findOrFail($category->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
