<?php

namespace Marvel\Database\Repositories;

use Exception;
use Illuminate\Http\Request;
use Marvel\Database\Models\Promotion;
use Marvel\Exceptions\MarvelBadRequestException;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Marvel\Traits\MediaManager;

class PromotionRepository extends BaseRepository
{
    use MediaManager;
    protected $fieldSearchable = [
        'name' => 'like',
        'type',
        'code' => 'like',
        'status',
    ];

    protected $dataArray = [
        'name',
        'type',
        'type_amount',
        'value',
        'code',
        'max_discount_amount',
        'required_quantity_type',
        'start_at',
        'end_at',
        'status',
    ];

    public function getDataArray(): array
    {
        return $this->dataArray;
    }

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }

    public function model()
    {
        return Promotion::class;
    }

    public function storePromotion(Request $request)
    {
        try {
            $data = $request->only($this->dataArray);
            $promotion = $this->create($data);

            if ($request->hasFile('image')) {
                if (!$this->uploadSingleImage($request, 'image', $promotion, 'promotions', 'promotions')) {
                    throw new MarvelBadRequestException('Image upload failed');
                }
            }

            return $promotion;
        } catch (Exception $e) {
            throw new MarvelBadRequestException(COULD_NOT_CREATE_THE_RESOURCE);
        }
    }

    public function updatePromotion($id, Request $request)
    {
        try {
            $promotion = $this->find($id);
            if (!$promotion) {
                throw new MarvelBadRequestException(COULD_NOT_UPDATE_THE_RESOURCE);
            }

            $data = $request->only($this->dataArray);
            $promotion->update($data);

            if ($request->hasFile('image')) {
                if (!$this->updateSingleImage($request, 'image', $promotion, 'promotions', 'promotions')) {
                    throw new MarvelBadRequestException('Image upload failed');
                }
            }

            return $promotion;
        } catch (Exception $e) {
            throw new MarvelBadRequestException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }
}
