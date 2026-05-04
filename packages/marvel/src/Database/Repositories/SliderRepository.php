<?php


namespace Marvel\Database\Repositories;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Marvel\Traits\MediaManager;
use Illuminate\Http\Request;
use Marvel\Database\Models\Slider;

class SliderRepository extends BaseRepository
{
    use MediaManager;
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Slider::class;
    }
    public function getSliders()
    {
        $limit = request()->limit ?? 15;
        $active = request()->active ?? false;
        $sliders = Slider::when($active, fn($query) => $query->active())->ordered()->paginate($limit);
        return $sliders;
    }

    public function createSlider(Request $request)
    {
        try {

            DB::beginTransaction();
            $slider = $this->create($request->except('image'));
            if ($request->has('image')) {
                if ($request->type === "slider") {
                    if (!$this->uploadSingleImage($request, 'image', $slider, 'slider-image', 'sliders')) {
                        throw new HttpException(422, 'Slider image upload failed, please check the file format or size.');
                    }
                } else {
                    if (!$this->uploadSingleImage($request, 'image', $slider, 'sliders-images-secondary', 'sliders')) {
                        throw new HttpException(422, 'Secondary slider image upload failed, please check the file format or size.');
                    }
                }
            }


            DB::commit();
            return $slider;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function updateSlider(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $slider = $this->findOrFail($id);
            $slider->update($request->except('image'));
            if ($request->has('image')) {
                if ($request->type === "slider") {
                    if (!$this->updateSingleImage($request, 'image', $slider, 'slider-image', 'sliders')) {
                        throw new HttpException(422, 'Slider image upload failed, please check the file format or size.');
                    }
                } else {
                    if (!$this->updateSingleImage($request, 'image', $slider, 'sliders-images-secondary', 'sliders')) {
                        throw new HttpException(422, 'Secondary slider image upload failed, please check the file format or size.');
                    }
                }
            }
            DB::commit();
            return $slider;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpException(400, NOT_FOUND);
        }
    }

    public function changeStatus($id)
    {
        try {
            $slider = $this->findOrFail($id);
            $slider->update(['is_active' => !$slider->is_active]);
            return $slider;
        } catch (\Exception $e) {
            throw new HttpException(400, NOT_FOUND);
        }
    }

    public function reorder(array $sliders)
    {
        try {
            $this->setNewOrder($sliders);

        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
