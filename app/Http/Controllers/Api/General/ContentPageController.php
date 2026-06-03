<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentPageResource;
use Marvel\Models\ContentPage ;
use Marvel\Traits\ApiResponse;

class ContentPageController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $pages = ContentPage::with('sections')->paginate(15);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ContentPageResource::collection($pages));
    }

    public function show($slug)
    {
        $content_page = ContentPage::where('slug',"like" ,"%".$slug."%")->with('sections')->firstOrFail();
        return   $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ContentPageResource::make($content_page));
    }


}
