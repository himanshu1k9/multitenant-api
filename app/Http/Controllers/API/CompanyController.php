<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index()
    {
        try {
            $companies = Auth::user()->companies;
            return response()->json($companies, 200);
        } catch (\Throwable $e) {
            Log::error('Company index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch companies', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string',
                'industry' => 'required|string',
            ]);

            $company = Auth::user()->companies()->create($validated);
            return response()->json($company, 201);
        } catch (\Throwable $e) {
            Log::error('Company store error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create company', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Auth::user()->companies()->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string',
                'industry' => 'required|string',
            ]);

            $company->update($validated);
            return response()->json($company, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Company not found for update: ' . $e->getMessage());
            return response()->json(['message' => 'Company not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Company update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update company', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Auth::user()->companies()->findOrFail($id);
            $company->delete();
            return response()->json(['message' => 'Company deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Company not found for deletion: ' . $e->getMessage());
            return response()->json(['message' => 'Company not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Company destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete company', 'error' => $e->getMessage()], 500);
        }
    }

    public function switch($id)
    {
        try {
            $company = Auth::user()->companies()->findOrFail($id);

            Auth::user()->update([
                'active_company_id' => $company->id
            ]);

            return response()->json(['message' => 'Switched to company', 'company' => $company], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Company not found for switch: ' . $e->getMessage());
            return response()->json(['message' => 'Company not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Company switch error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to switch company', 'error' => $e->getMessage()], 500);
        }
    }
}
