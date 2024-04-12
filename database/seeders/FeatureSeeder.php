<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\Feature;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends BaseSeeder
{
    public function run(): void
    {
        Feature::query()->truncate();

        $features = [
            [
                'name' => 'Have High Rating',
                'icon' => 'flaticon-rating',
                'is_featured' => true,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'name' => 'Quiet Hours',
                'icon' => 'flaticon-clock',
                'is_featured' => true,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'name' => 'Best Locations',
                'icon' => 'flaticon-location-pin',
                'is_featured' => true,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'name' => 'Free Cancellation',
                'icon' => 'flaticon-clock-1',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'name' => 'Payment Options',
                'icon' => 'flaticon-credit-card',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'name' => 'Special Offers',
                'icon' => 'flaticon-discount',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
        ];

        foreach ($features as $feature) {
            Feature::query()->create($feature);
        }

        DB::table('ht_features_translations')->truncate();

        $translations = [
            [
                'name' => 'Đánh giá cao',
            ],
            [
                'name' => 'Giờ giấc yên tĩnh',
            ],
            [
                'name' => 'Vị trí đẹp',
            ],
            [
                'name' => 'Miễn phí hủy đặt chỗ',
            ],
            [
                'name' => 'Nhiều tùy chọn thanh toán',
            ],
            [
                'name' => 'Khuyến mãi đặc biệt',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_features_id'] = $index + 1;

            DB::table('ht_features_translations')->insert($item);
        }
    }
}
