<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use App\Models\DhakaArea;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends BaseController
{
    public function getThanas()
    {
        // Assuming Thanas are stored in GlobalOptionValue with option_id 7 as seen in DhakaAreaForm
        $thanas = GlobalOptionValue::where('option_id', 7)->select('id', 'option_value as name')->get();
        return response()->json($thanas);
    }

    public function getAreas($thanaId)
    {
        $areas = DhakaArea::where('thana_id', $thanaId)->select('id', 'name', 'price')->get();
        return response()->json($areas);
    }

    public function setLocation(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:ec_dhaka_area,id',
            'area_name' => 'required|string',
            'thana_name' => 'required|string',
        ]);

        $locationData = [
            'area_id' => $request->input('area_id'),
            'area_name' => $request->input('area_name'),
            'thana_name' => $request->input('thana_name'),
        ];

        Session::put('user_selected_location', $locationData);

        // Also save to cookie for persistence
        cookie()->queue('user_selected_location', json_encode($locationData), 60 * 24 * 30); // 30 days

        return response()->json(['success' => true, 'message' => 'Location saved successfully']);
    }
}
