<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cities = json_decode(File::get(public_path('cities.json')), true);

        $subtotal = (int) str_replace(',', '', Cart::subtotal(0));
        $tax = round($subtotal * 0.03); // 3% tax
        $total = $subtotal + $tax;

        return view('checkout', compact('cities', 'subtotal', 'tax', 'total'));
    }

    public function getShippingCost(Request $request)
    {
        $origin = $request->origin;
        $destination = $request->destination;
        $courier = $request->courier;

        $coasts = json_decode(File::get(public_path('coasts.json')), true);

        foreach ($coasts as $costData) {
            if (
                $costData['origin_id'] == $origin &&
                $costData['destination_id'] == $destination &&
                $costData['courier'] == $courier
            ) {
                return response()->json([
                    'services' => $costData['services']
                ]);
            }
        }

        return response()->json([
            'services' => []
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'            => 'required|email',
            'name'             => 'required|string',
            'address'          => 'required|string',
            'origin'           => 'required|string',
            'destination'      => 'required|string',
            'phone'            => 'required|string',
            'shipping_cost'    => 'required|numeric',
            'shipping_service' => 'required|string',
        ]);

        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $subtotal = (int) str_replace(',', '', Cart::subtotal(0));
        $tax = round($subtotal * 0.03);
        $shipping = (int) $request->shipping_cost;
        $total = $subtotal + $tax + $shipping;

        $params = [
            'transaction_details' => [
                'order_id'     => Str::uuid()->toString(),
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'billing_address' => [
                    'address'     => $request->address,
                    'city'        => $request->destination,
                    'postal_code' => $request->postal_code ?? '',
                    'country_code'=> 'IDN'
                ],
            ],
            'item_details' => [],
        ];

        foreach (Cart::content() as $item) {
            $params['item_details'][] = [
                'id'       => $item->id,
                'price'    => (int) $item->price,
                'quantity' => $item->qty,
                'name'     => Str::limit($item->name, 50)
            ];
        }

        $params['item_details'][] = [
            'id'       => 'ongkir',
            'price'    => $shipping,
            'quantity' => 1,
            'name'     => 'Ongkos Kirim - ' . $request->shipping_service
        ];

        $params['item_details'][] = [
            'id'       => 'tax',
            'price'    => $tax,
            'quantity' => 1,
            'name'     => 'Pajak 3%'
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
