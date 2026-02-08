<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display search results page
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $products = collect();

        if (strlen($query) >= 2) {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orderBy('name')
                ->get();
        }

        return view('search.index', compact('products', 'query'));
    }

    /**
     * Autocomplete suggestions (AJAX)
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->select('product_id', 'name', 'product_type', 'price', 'image_url')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return response()->json($products);
    }
}
