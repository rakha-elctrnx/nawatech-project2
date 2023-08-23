<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AhassController extends Controller
{

    public function index()
    {
        $userDataPath = base_path('resources/json/user-data.json');
        $workshopDataPath = base_path('resources/json/workshop-data.json');

        $userData = json_decode(File::get($userDataPath), true);
        $workshopData = json_decode(File::get($workshopDataPath), true);

        $newData = [
            'status' => 1,
            'message' => 'Data Successfully Retrieved.',
            'data' => [],
        ];

        foreach ($userData['data'] as $item) {
            $ahassData = $this->findAhassData($workshopData['data'], $item['booking']['workshop']['code']);

            $newData['data'][] = [
                'name' => $item['name'],
                'email' => $item['email'],
                'booking_number' => $item['booking']['booking_number'],
                'book_date' => $item['booking']['book_date'],
                'ahass_code' => $ahassData['code'],
                'ahass_name' => $ahassData['name'],
                'ahass_address' => $ahassData['address'],
                'ahass_contact' => $ahassData['phone_number'],
                'ahass_distance' => $ahassData['distance'],
                'motorcycle_ut_code' => $item['booking']['motorcycle']['ut_code'],
                'motorcycle' => $item['booking']['motorcycle']['name'],
            ];
        }

        $dataCollection = collect($newData['data']);
        $dataCollection = $dataCollection->sortBy('ahass_distance');
        $sortedData = $dataCollection->values()->all();
        $newData['data'] = $sortedData;

        return response()->json($newData);
    }

    private function findAhassData($ahassData, $code)
    {
        foreach ($ahassData as $item) {
            if ($item['code'] === $code) {
                return $item;
            }
        }
        return [
            'code' => '',
            'name' => '',
            'address' => '',
            'phone_number' => '',
            'distance' => 0,
        ];
    }
}
