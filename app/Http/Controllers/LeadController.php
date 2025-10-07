<?php

namespace App\Http\Controllers;

use App\LeadSource;
use App\LeadStatus;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Lead::with('practice');

        if ($request->has('practice_id')) {
            $query->where('practice_id', $request->practice_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('email')) {
            $query->where('email', $request->email);
        }

        if ($request->has('phone')) {
            $query->where('phone', $request->phone);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($leads);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|exists:practices,id',
            'source' => ['nullable', Rule::enum(LeadSource::class)],
            'lead_type' => 'nullable|string|max:255',
            'data' => 'required|array',
        ]);

        try {
            $source = isset($validated['source'])
                ? LeadSource::from($validated['source'])
                : null;

            $lead = Lead::createFromData(
                $validated['practice_id'],
                $validated['data'],
                $source,
                $validated['lead_type'] ?? null
            );

            return response()->json($lead->load('practice'), 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Lead $lead): JsonResponse
    {
        return response()->json($lead->load('practice'));
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::enum(LeadStatus::class)],
            'contacted_at' => 'nullable|date',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        if (isset($validated['status'])) {
            $validated['status'] = LeadStatus::from($validated['status']);
        }

        $lead->update($validated);

        return response()->json($lead->load('practice'));
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }
}
