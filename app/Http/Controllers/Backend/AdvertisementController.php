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
        $homepage_section_banner_one = json_decode($homepage_section_banner_one?->value);

        $homepage_section_banner_two = Advertisement::where('key', 'homepage_section_banner_two')->first();
        $homepage_section_banner_two = json_decode($homepage_section_banner_two?->value);
        return view('admin.advertisement.index', compact('homepage_section_banner_one', 'homepage_section_banner_two'));
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

    public function homepageBannerSectionTwo(Request $request)
    {

        $request->validate([
            'banner_one_image' => ['image'],
            'banner_one_url' => ['required', 'url'],
            'banner_two_image' => ['image'],
            'banner_two_url' => ['required', 'url'],
        ]);

        $homepage_section_banner_two = Advertisement::where('key', 'homepage_section_banner_two')->first();
        $homepage_section_banner_two = json_decode($homepage_section_banner_two->value);

        // handle the image upload
        $imagePathOne = $this->updateImage($request, 'banner_one_image', 'uploads', $homepage_section_banner_two->banner_one->banner_image);

        $imagePathTwo = $this->updateImage($request, 'banner_two_image', 'uploads', $homepage_section_banner_two->banner_two->banner_image);

        $value = [
            'banner_one' => [
                'banner_url' => $request->banner_one_url,
                'status' => $request->banner_one_status == 'on' ? 1 : 0,
            ],
            'banner_two' => [
                'banner_url' => $request->banner_two_url,
                'status' => $request->banner_two_status == 'on' ? 1 : 0,
            ]
        ];

        if (!empty($imagePathOne)) {
            $value['banner_one']['banner_image'] = $imagePathOne;
        } else {
            $value['banner_one']['banner_image'] = $request->banner_one_old_image;
        }
        if (!empty($imagePathTwo)) {
            $value['banner_two']['banner_image'] = $imagePathTwo;
        } else {
            $value['banner_two']['banner_image'] = $request->banner_two_old_image;
        }

        Advertisement::updateOrCreate(
            ['key' => 'homepage_section_banner_two'],
            ['value' => json_encode($value)]
        );

        toastr('Updated successfully', 'success', 'Success');

        return redirect()->back();
    }
}
