<?php

namespace Marvel\Http\Controllers;

use App\Models\Section;
use App\Http\Resources\Section\SectionResource;
use Marvel\Traits\ApiResponse;
use Illuminate\Http\Request;

class SectionController extends CoreController
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::orderBy('order')->get();
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, SectionResource::collection($sections));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'      => 'required|string',
            'title'     => 'required|string',
            'order'     => 'sometimes|integer',
            'endpoint'  => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $section = Section::create($request->all());
            return $this->apiResponse("Section created successfully", 200, true, SectionResource::make($section));
        } catch (\Exception $e) {
            return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $section = Section::findOrFail($id);
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, SectionResource::make($section));
        } catch (\Exception $e) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type'      => 'sometimes|string',
            'title'     => 'sometimes|string',
            'order'     => 'sometimes|integer',
            'endpoint'  => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $section = Section::findOrFail($id);
            $section->update($request->all());
            return $this->apiResponse("Section updated successfully", 200, true, SectionResource::make($section));
        } catch (\Exception $e) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $section = Section::findOrFail($id);
            $section->delete();
            return $this->apiResponse("Section deleted successfully", 200, true);
        } catch (\Exception $e) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
    }

    /**
     * Reorder sections.
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'sections'   => 'required|array',
                'sections.*' => 'required|exists:sections,id',
            ]);

            Section::setNewOrder($request->sections);

            return $this->apiResponse("Sections reordered successfully", 200, true);
        } catch (\Exception $e) {
            return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
        }
    }
}
