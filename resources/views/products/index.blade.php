@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Products List</h2>
        <a href="{{ route('products.create') }}" class="btn btn-success">Add New Product</a>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Search Products</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="search_name" name="search_name" value="{{ request('search_name') }}">
                    <small class="text-muted">Regular column (always searchable)</small>
                </div>

                <div class="col-md-4">
                    <label for="search_cc" class="form-label">Credit Card Number</label>
                    <input type="text" class="form-control" id="search_cc" name="search_cc" value="{{ request('search_cc') }}">
                    <small class="text-muted">Deterministic encryption (searchable)</small>
                </div>

                <div class="col-md-4">
                    <label for="search_notes" class="form-label">Secret Notes</label>
                    <input type="text" class="form-control" id="search_notes" name="search_notes" value="{{ request('search_notes') }}">
                    <small class="text-muted">Randomized encryption (not searchable)</small>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Encryption Explanation -->
    <div class="alert alert-info mb-4">
        <h5>About Encrypted Column Searches</h5>
        <p><strong>Deterministic Encryption:</strong> Always encrypts the same plaintext to the same ciphertext, making it searchable but slightly less secure.</p>
        <p><strong>Randomized Encryption:</strong> Adds randomness to encryption, so the same plaintext encrypts differently each time, making it more secure but not searchable.</p>

        <div class="mt-2">
            <strong>Search Capability:</strong>
            <ul>
                <li>Regular columns (Name): Fully searchable with partial matches</li>
                <li>Deterministic columns (Credit Card): Searchable with exact matches only</li>
                <li>Randomized columns (Secret Notes): Not searchable - will never match</li>
            </ul>
        </div>
    </div>

    @if(request('search_name') || request('search_cc') || request('search_notes'))
    <div class="alert alert-secondary mb-4">
        <h5>Search Results</h5>
        <ul>
            @if(request('search_name'))
                <li><strong>Name contains:</strong> {{ request('search_name') }}</li>
            @endif

            @if(request('search_cc'))
                <li>
                    <strong>Credit Card Number equals:</strong> {{ request('search_cc') }}
                    <small class="text-muted">(Deterministic encryption allows exact matches)</small>
                </li>
            @endif

            @if(request('search_notes'))
                <li>
                    <strong>Secret Notes equals:</strong> {{ request('search_notes') }}
                    <small class="text-muted">(Randomized encryption prevents searching - this will never match)</small>
                </li>
            @endif
        </ul>
        <p>Found {{ $products->total() }} matching products</p>
    </div>
    @endif

    <!-- Results Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Credit Card (Deterministic)</th>
                            <th>Secret Notes (Randomized)</th>
                            <th>Status</th>
                            <th width="250">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->credit_card_number ?: 'Not provided' }}</td>
                                <td>{{ $product->secret_notes ?: 'Not provided' }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->active ? 'success' : 'danger' }}">
                                        {{ $product->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No products found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
