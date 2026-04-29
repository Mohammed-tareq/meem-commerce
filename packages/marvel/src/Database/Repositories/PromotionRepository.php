<?php

namespace Marvel\Database\Repositories;

use Exception;
use Illuminate\Http\Request;
use Marvel\Database\Models\Promotion;
use Marvel\Exceptions\MarvelBadRequestException;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class PromotionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'type',
        'code' => 'like',
        'is_active',
    ];

    protected $dataArray = [
        'name',
        'type',
        'value',
        'code',
        'min_order_amount',
        'start_at',
        'end_at',
        'is_active',
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
            return $this->create($data);
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
            return $promotion;
        } catch (Exception $e) {
            throw new MarvelBadRequestException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }
}