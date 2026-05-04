<?php


namespace Marvel\Database\Repositories;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Marvel\Database\Models\Banner;
use Marvel\Traits\MediaManager;
use Illuminate\Http\Request;

use const Dom\NOT_FOUND_ERR;

class BannerRepository extends BaseRepository
{
    use MediaManager;
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Banner::class;
    }
    public function getBanners()
    {
        $limit = request()->limit ?? 15;
        $active = request()->active ?? false;
        $banners = Banner::with('products')->when($active, fn($query) => $query->active())->ordered()->paginate($limit);
        return $banners;
    }

    public function createBanner(Request $request)
    {
        try {

            DB::beginTransaction();
            $banner = $this->create($request->except('image'));
            if ($request->has('image')) {
                if (!$this->uploadSingleImage($request, 'image', $banner, 'banners', 'banners')) {
                    throw new HttpException(422, 'Banner image upload failed, please check the file format or size.');
                }
            }
            DB::commit();
            return $banner;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function updateBanner(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $banner = $this->findOrFail($id);
            $banner->update($request->except('image'));
            if ($request->has('image')) {
                if (!$this->updateSingleImage($request, 'image', $banner, 'banners', 'banners')) {
                    throw new HttpException(422, 'Banner image upload failed, please check the file format or size.');
                }
            }
            DB::commit();
            return $banner;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpException(400, NOT_FOUND);
        }
    }


    public function changeStatus($id)
    {
        try {
            $banner = $this->find($id);
            $banner->update(['is_active' => !$banner->is_active]);
            return $banner;
        } catch (\Exception $e) {
            throw new HttpException(400, NOT_FOUND);
        }
    }

    public function reorder(array $banners)
    {
        try {
            $this->setNewOrder($banners);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}