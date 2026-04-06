<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\AddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    use ApiResponse;

    /**
     * List user's addresses.
     * 
     * GET /api/v1/addresses
     */
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return $this->success(AddressResource::collection($addresses));
    }

    /**
     * Create a new address.
     * 
     * POST /api/v1/addresses
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:50'],
            'street_address' => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // If setting as default, unset other defaults
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        // If this is first address, make it default
        $isFirstAddress = $user->addresses()->count() === 0;

        $address = $user->addresses()->create([
            'label' => $request->label,
            'street_address' => $request->street_address,
            'apartment' => $request->apartment,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'Nigeria',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'delivery_instructions' => $request->delivery_instructions,
            'is_default' => $request->is_default ?? $isFirstAddress,
        ]);

        return $this->created(
            new AddressResource($address),
            'Address added successfully'
        );
    }

    /**
     * Get a specific address.
     * 
     * GET /api/v1/addresses/{id}
     */
    public function show(Request $request, $id)
    {
        $address = $request->user()
            ->addresses()
            ->findOrFail($id);

        return $this->success(new AddressResource($address));
    }

    /**
     * Update an address.
     * 
     * PUT /api/v1/addresses/{id}
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label' => ['sometimes', 'string', 'max:50'],
            'street_address' => ['sometimes', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $address = $user->addresses()->findOrFail($id);

        // If setting as default, unset other defaults
        if ($request->is_default) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($request->only([
            'label',
            'street_address',
            'apartment',
            'city',
            'state',
            'postal_code',
            'country',
            'latitude',
            'longitude',
            'delivery_instructions',
            'is_default',
        ]));

        return $this->success(
            new AddressResource($address->fresh()),
            'Address updated successfully'
        );
    }

    /**
     * Delete an address.
     * 
     * DELETE /api/v1/addresses/{id}
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $address = $user->addresses()->findOrFail($id);
        
        $wasDefault = $address->is_default;
        
        $address->delete();

        // If deleted address was default, make the most recent one default
        if ($wasDefault) {
            $user->addresses()
                ->orderByDesc('created_at')
                ->first()
                ?->update(['is_default' => true]);
        }

        return $this->success(null, 'Address deleted successfully');
    }

    /**
     * Set an address as default.
     * 
     * POST /api/v1/addresses/{id}/default
     */
    public function setDefault(Request $request, $id)
    {
        $user = $request->user();
        
        // Unset all defaults
        $user->addresses()->update(['is_default' => false]);
        
        // Set new default
        $address = $user->addresses()->findOrFail($id);
        $address->update(['is_default' => true]);

        return $this->success(
            new AddressResource($address->fresh()),
            'Default address updated'
        );
    }
}
