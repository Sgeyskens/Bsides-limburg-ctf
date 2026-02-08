<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display products with filters and sorting.
     */
    public function index(Request $request, string $type)
    {
        $validTypes = ['movie', 'game', 'merch'];
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        // Store filter state in session
        $this->persistFiltersToSession($request, $type);

        // Build base query with eager loading
        $query = Product::where('product_type', $type)
            ->with(['properties', 'ratings']);

        // Get filter options before applying filters (for the UI)
        $filterOptions = $this->getFilterOptions($type);

        // Apply filters
        $query = $this->applyFilters($query, $request, $type);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get products
        $products = $query->get();

        // Calculate average ratings for each product
        $products = $products->map(function ($product) {
            $product->avg_rating = $product->ratings->avg('rating') ?? 0;
            $product->rating_count = $product->ratings->count();
            return $product;
        });

        // Get active filters for the view
        $activeFilters = $this->getActiveFilters($request, $type);

        $viewName = match($type) {
            'movie' => 'movies',
            'game' => 'games',
            'merch' => 'merch',
        };

        $variableName = match($type) {
            'movie' => 'movies',
            'game' => 'games',
            'merch' => 'merch',
        };

        return view($viewName, [
            $variableName => $products,
            'filterOptions' => $filterOptions,
            'activeFilters' => $activeFilters,
            'productType' => $type,
        ]);
    }

    /**
     * Get products via AJAX for dynamic filtering.
     */
    public function filter(Request $request, string $type)
    {
        $validTypes = ['movie', 'game', 'merch'];
        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid product type'], 400);
        }

        // Store filter state in session
        $this->persistFiltersToSession($request, $type);

        // Build query
        $query = Product::where('product_type', $type)
            ->with(['properties', 'ratings']);

        // Apply filters
        $query = $this->applyFilters($query, $request, $type);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get products
        $products = $query->get();

        // Calculate average ratings
        $products = $products->map(function ($product) {
            $product->avg_rating = round($product->ratings->avg('rating') ?? 0, 1);
            $product->rating_count = $product->ratings->count();
            return $product;
        });

        return response()->json([
            'products' => $products,
            'count' => $products->count(),
        ]);
    }

    /**
     * Get filter options for a product type.
     */
    private function getFilterOptions(string $type): array
    {
        // Get price range
        $priceRange = Product::where('product_type', $type)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        // Get unique property values for this product type
        $properties = DB::table('product_property')
            ->join('property', 'product_property.property_id', '=', 'property.property_id')
            ->join('products', 'product_property.product_id', '=', 'products.product_id')
            ->where('products.product_type', $type)
            ->select('property.property_name', 'product_property.property_value')
            ->distinct()
            ->get()
            ->groupBy('property_name')
            ->map(function ($items) {
                return $items->pluck('property_value')->unique()->values()->toArray();
            })
            ->toArray();

        // Get rating distribution
        $ratingDistribution = DB::table('product_ratings')
            ->join('products', 'product_ratings.product_id', '=', 'products.product_id')
            ->where('products.product_type', $type)
            ->select('product_ratings.rating', DB::raw('COUNT(*) as count'))
            ->groupBy('product_ratings.rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            'minPrice' => (float) ($priceRange->min_price ?? 0),
            'maxPrice' => (float) ($priceRange->max_price ?? 100),
            'properties' => $properties,
            'ratingDistribution' => $ratingDistribution,
        ];
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request, string $type)
    {
        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Rating filter (minimum rating)
        if ($request->filled('min_rating')) {
            $minRating = (int) $request->input('min_rating');
            // Use a subquery to find products with average rating >= min_rating
            $query->whereIn('products.product_id', function ($subquery) use ($minRating) {
                $subquery->select('product_id')
                    ->from('product_ratings')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // Property filters (Genre, Year, Platform, etc.)
        $propertyFilters = $request->input('properties', []);
        if (is_array($propertyFilters)) {
            foreach ($propertyFilters as $propertyName => $values) {
                if (!empty($values)) {
                    $valuesArray = is_array($values) ? $values : [$values];
                    $query->whereHas('properties', function ($q) use ($propertyName, $valuesArray) {
                        $q->where('property_name', $propertyName)
                            ->whereIn('product_property.property_value', $valuesArray);
                    });
                }
            }
        }

        return $query;
    }

    /**
     * Apply sorting to the query.
     */
    private function applySorting($query, Request $request)
    {
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Validate sort order
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'rating':
                // Sort by average rating using a subquery
                // Use COALESCE to handle NULL ratings (products with no ratings)
                $nullHandling = $sortOrder === 'desc' ? '0' : '999';
                $query->leftJoin('product_ratings', 'products.product_id', '=', 'product_ratings.product_id')
                    ->select('products.*')
                    ->groupBy('products.product_id')
                    ->orderByRaw('COALESCE(AVG(product_ratings.rating), ' . $nullHandling . ') ' . $sortOrder);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        return $query;
    }

    /**
     * Persist filter state to session.
     */
    private function persistFiltersToSession(Request $request, string $type)
    {
        $filterKey = "filters_{$type}";

        $filters = [
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'min_rating' => $request->input('min_rating'),
            'properties' => $request->input('properties', []),
            'sort_by' => $request->input('sort_by', 'name'),
            'sort_order' => $request->input('sort_order', 'asc'),
        ];

        // Only update session if filters are provided in request
        if ($request->hasAny(['min_price', 'max_price', 'min_rating', 'properties', 'sort_by', 'sort_order', 'clear_filters'])) {
            if ($request->has('clear_filters')) {
                session()->forget($filterKey);
            } else {
                session()->put($filterKey, $filters);
            }
        }
    }

    /**
     * Get active filters from request or session.
     */
    private function getActiveFilters(Request $request, string $type): array
    {
        $filterKey = "filters_{$type}";

        // If request has filters, use those; otherwise use session
        if ($request->hasAny(['min_price', 'max_price', 'min_rating', 'properties', 'sort_by', 'sort_order'])) {
            return [
                'min_price' => $request->input('min_price'),
                'max_price' => $request->input('max_price'),
                'min_rating' => $request->input('min_rating'),
                'properties' => $request->input('properties', []),
                'sort_by' => $request->input('sort_by', 'name'),
                'sort_order' => $request->input('sort_order', 'asc'),
            ];
        }

        return session()->get($filterKey, [
            'min_price' => null,
            'max_price' => null,
            'min_rating' => null,
            'properties' => [],
            'sort_by' => 'name',
            'sort_order' => 'asc',
        ]);
    }
}
