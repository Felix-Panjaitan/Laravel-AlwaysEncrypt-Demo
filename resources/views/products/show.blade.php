@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Product Details</h2>
        <div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary ms-2">Edit</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">ID:</div>
                <div class="col-md-9">{{ $product->id }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Name:</div>
                <div class="col-md-9">{{ $product->name }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Description:</div>
                <div class="col-md-9">
                    {!! nl2br(e($product->description)) ?: '<span class="text-muted">No description</span>' !!}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Price:</div>
                <div class="col-md-9">${{ number_format($product->price, 2) }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Quantity:</div>
                <div class="col-md-9">{{ $product->quantity }}</div>
            </div>

            <!-- Encrypted Fields -->
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Credit Card (Encrypted):</div>
                <div class="col-md-9">
                    @if($product->credit_card_number)
                        <span class="badge bg-success">{{ $product->credit_card_number }}</span>
                        <small class="text-muted d-block">Deterministic encryption (searchable)</small>
                    @else
                        <span class="text-muted">Not provided</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Secret Notes (Encrypted):</div>
                <div class="col-md-9">
                    @if($product->secret_notes)
                        <span class="badge bg-info">{{ $product->secret_notes }}</span>
                        <small class="text-muted d-block">Randomized encryption (more secure)</small>
                    @else
                        <span class="text-muted">Not provided</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Status:</div>
                <div class="col-md-9">
                    <span class="badge bg-{{ $product->active ? 'success' : 'danger' }}">
                        {{ $product->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Created At:</div>
                <div class="col-md-9">{{ $product->created_at->format('F d, Y h:i A') }}</div>
            </div>

            <div class="row">
                <div class="col-md-3 fw-bold">Last Updated:</div>
                <div class="col-md-9">{{ $product->updated_at->format('F d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</button>
        </form>
    </div>
@endsection
