<?php

namespace Marvel\Http\Controllers;

use App\Http\Resources\Pages\SectionResource as PagesSectionResource;
use App\Http\Resources\Section\SectionResource;
use Marvel\Traits\ApiResponse;
use Illuminate\Http\Request;
use Marvel\Http\Requests\StoreSectionRequest;
use Marvel\Http\Requests\UpdateContactSectionRequest;
use Marvel\Http\Requests\UpdateSectionRequest;
use Marvel\Models\Section;

class SectionController extends CoreController
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::orderBy('order')->get();
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, PagesSectionResource::collection($sections));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            $section = Section::create($request->validated());
            $section     = $section->fresh();
            return $this->apiResponse("Section created successfully", 200, true, PagesSectionResource::make($section));
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
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, PagesSectionResource::make($section));
        } catch (\Exception $e) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, string $id)
    {

        try {
            $section = Section::findOrFail($id);
            $section->update($request->validated());
            return $this->apiResponse("Section updated successfully", 200, true, PagesSectionResource::make($section));
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
                'sections'   => 'required|array|distinct',
                'sections.*' => 'required|exists:sections,id',
            ]);

            Section::setNewOrder($request->sections);

            return $this->apiResponse("Sections reordered successfully", 200, true);
        } catch (\Exception $e) {
            return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
        }
    }
    public function toggleStatus(Section $section)
    {
        $section->is_active = !$section->is_active;
        $section->save();
        return $this->apiResponse(UPDATE_DATA_SUCCESSFULLY, 200, true, PagesSectionResource::make($section));
    }
}
