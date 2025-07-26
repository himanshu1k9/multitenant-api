<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->companies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'industry' => 'required|string',
        ]);

        $company = Auth::user()->companies()->create($validated);

        return response()->json($company, 201);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'industry' => 'required|string',
        ]);

        $company->update($validated);

        return response()->json($company);
    }

    public function destroy($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    public function switch($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);

        Auth::user()->update([
            'active_company_id' => $company->id
        ]);

        return response()->json(['message' => 'Switched to company', 'company' => $company]);
    }
}
