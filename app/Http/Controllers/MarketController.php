<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class MarketController extends Controller
{
    /**
     * Daftar produk
     */
    private function products()
    {
        return [
            'gold' => [
                'symbol' => 'XAU/USD',
                'name' => 'GOLD',
            ],

            'hangseng' => [
                'symbol' => 'HSI',
                'name' => 'HANG SENG',
            ],

            'nikkei' => [
                'symbol' => 'N225',
                'name' => 'NIKKEI',
            ],
        ];
    }


    /**
     * Ambil API Key Twelve Data
     */
    private function apiKey()
    {
        return config('services.twelvedata.key');
    }


    /**
     * Ambil harga market terbaru
     */
    public function price(Request $request)
    {
        $product = $request->get('product', 'gold');

        $products = $this->products();


        // Cek produk
        if (!isset($products[$product])) {

            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 400);

        }


        // Cek API Key
        $apiKey = $this->apiKey();

        if (empty($apiKey)) {

            return response()->json([
                'success' => false,
                'message' => 'TWELVE_DATA_API_KEY belum terbaca oleh Laravel',
            ], 500);

        }


        $symbol = $products[$product]['symbol'];


        try {

            $response = Http::timeout(10)
                ->acceptJson()
                ->get(
                    'https://api.twelvedata.com/price',
                    [
                        'symbol' => $symbol,
                        'apikey' => $apiKey,
                    ]
                );


        } catch (ConnectionException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Laravel gagal terhubung ke Twelve Data',
                'error' => $e->getMessage(),
            ], 502);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat mengambil harga',
                'error' => $e->getMessage(),
            ], 500);

        }


        /*
        |--------------------------------------------------------------------------
        | Baca response Twelve Data
        |--------------------------------------------------------------------------
        */

        $data = $response->json();


        /*
        |--------------------------------------------------------------------------
        | Jika HTTP error
        |--------------------------------------------------------------------------
        */

        if ($response->failed()) {

            return response()->json([
                'success' => false,
                'message' => 'Twelve Data menolak request harga',
                'http_status' => $response->status(),
                'twelve_data_response' => $data ?? $response->body(),
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | Jika Twelve Data mengirim error meskipun HTTP 200
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['status']) &&
            $data['status'] === 'error'
        ) {

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Twelve Data mengembalikan error',
                'twelve_data_response' => $data,
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan price tersedia
        |--------------------------------------------------------------------------
        */

        if (!isset($data['price'])) {

            return response()->json([
                'success' => false,
                'message' => 'Harga tidak tersedia dari Twelve Data',
                'twelve_data_response' => $data,
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'product' => $product,
            'symbol' => $symbol,
            'name' => $products[$product]['name'],
            'price' => (float) $data['price'],
        ]);
    }


    /**
     * Ambil data candlestick
     */
    public function chart(Request $request)
    {
        $product = $request->get('product', 'gold');

        $products = $this->products();


        // Cek produk
        if (!isset($products[$product])) {

            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 400);

        }


        // Cek API Key
        $apiKey = $this->apiKey();

        if (empty($apiKey)) {

            return response()->json([
                'success' => false,
                'message' => 'TWELVE_DATA_API_KEY belum terbaca oleh Laravel',
            ], 500);

        }


        $symbol = $products[$product]['symbol'];


        try {

            $response = Http::timeout(15)
                ->acceptJson()
                ->get(
                    'https://api.twelvedata.com/time_series',
                    [
                        'symbol' => $symbol,
                        'interval' => '1min',
                        'outputsize' => 30,
                        'apikey' => $apiKey,
                    ]
                );


        } catch (ConnectionException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Laravel gagal terhubung ke Twelve Data',
                'error' => $e->getMessage(),
            ], 502);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat mengambil chart',
                'error' => $e->getMessage(),
            ], 500);

        }


        $data = $response->json();


        /*
        |--------------------------------------------------------------------------
        | HTTP ERROR
        |--------------------------------------------------------------------------
        */

        if ($response->failed()) {

            return response()->json([
                'success' => false,
                'message' => 'Twelve Data menolak request chart',
                'http_status' => $response->status(),
                'twelve_data_response' => $data ?? $response->body(),
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | TWELVE DATA ERROR
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['status']) &&
            $data['status'] === 'error'
        ) {

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Twelve Data mengembalikan error',
                'twelve_data_response' => $data,
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | VALUES TIDAK ADA
        |--------------------------------------------------------------------------
        */

        if (
            !isset($data['values']) ||
            !is_array($data['values'])
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Data candlestick tidak tersedia',
                'twelve_data_response' => $data,
            ], 502);

        }


        /*
        |--------------------------------------------------------------------------
        | Urutkan dari waktu lama → terbaru
        |--------------------------------------------------------------------------
        */

        $values = array_reverse($data['values']);


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'product' => $product,
            'symbol' => $symbol,
            'name' => $products[$product]['name'],
            'values' => $values,
        ]);
    }
}