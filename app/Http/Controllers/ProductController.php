<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name (regular column)
        if ($request->has('search_name') && !empty($request->search_name)) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        // Search by credit_card_number (deterministic encryption)
        if ($request->has('search_cc') && !empty($request->search_cc)) {
            $query->where('credit_card_number', $request->search_cc);
        }

        // Search by secret_notes (randomized encryption)
        // This won't work but we'll include it to demonstrate
        if ($request->has('search_notes') && !empty($request->search_notes)) {
            $query->where('secret_notes', $request->search_notes);
        }

        $products = $query->paginate(10);

        return view('products.index', compact('products'))
            ->with('i', ($request->input('page', 1) - 1) * 10)
            ->with('search_name', $request->search_name)
            ->with('search_cc', $request->search_cc)
            ->with('search_notes', $request->search_notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'credit_card_number' => 'nullable|string|max:255',
            'secret_notes' => 'nullable|string|max:255',
        ]);

        DB::statement(
            "INSERT INTO products (name, description, price, quantity, active, credit_card_number, secret_notes, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $request->name,
                $request->description,
                $request->price,
                $request->quantity,
                $request->boolean('active') ? 1 : 0,
                $request->credit_card_number,
                $request->secret_notes,
                now(),
                now()
            ]
        );

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'credit_card_number' => 'nullable|string|max:255',
            'secret_notes' => 'nullable|string|max:255',
        ]);

        DB::statement(
            "UPDATE products SET name = ?, description = ?, price = ?,
                  quantity = ?, active = ?, credit_card_number = ?, secret_notes = ?,
                  updated_at = ? WHERE id = ?",
            [
                $request->name,
                $request->description,
                $request->price,
                $request->quantity,
                $request->boolean('active') ? 1 : 0,
                $request->credit_card_number,
                $request->secret_notes,
                now(),
                $product->id
            ]
        );

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
