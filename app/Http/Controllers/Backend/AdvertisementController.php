<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $homepage_section_banner_one = Advertisement::where('key', 'homepage_section_banner_one')->first();
        $homepage_section_banner_one = json_decode($homepage_section_banner_one->value);
        return view('admin.advertisement.index', compact('homepage_section_banner_one'));
    }
    public function homepageBannerSectionOne(Request $request)
    {
        $request->validate([
            'banner_image' => ['image'],
            'banner_url' => ['required', 'url'],
        ]);

        $homepage_section_banner_one = Advertisement::where('key', 'homepage_section_banner_one')->first();
        $homepage_section_banner_one = json_decode($homepage_section_banner_one->value);
        //handle the image upload
        $imagePath = $this->updateImage($request, 'banner_image', 'uploads', $homepage_section_banner_one->banner_one->banner_image);

        $value = [
            'banner_one' => [
                'banner_url' => $request->banner_url,
                'status' => $request->status == 'on' ? 1 : 0,
            ]
        ];

        if (!empty($imagePath)) {
            $value['banner_one']['banner_image'] = $imagePath;
        } else {
            $value['banner_one']['banner_image'] = $request->banner_old_image;
        }

        Advertisement::updateOrCreate(
            ['key' => 'homepage_section_banner_one'],
            ['value' => json_encode($value)]
        );

        toastr('Updated successfully', 'success', 'Success');

        return redirect()->back();
    }
}
// {"banner_one":{"banner_url":"http:\/\/ecommerce.test\/admin\/advertisement#list-profile","status":1,"banner_image":"uploads\/media_67c3fdd501929.png"}}
