<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    
    public function indexView()
    {
        $packages = Package::with('permissions')->get();
        $permissions = Permission::all();
        return view('packages.index', compact('packages', 'permissions'));
    }

    
    public function index()
    {
        $packages = Package::with('permissions')->get();
        return response()->json($packages);
    }

    private function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:packages,name',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:packages,name',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $package = Package::create($data);

        if (!empty($data['permissions'])) {
            $package->permissions()->attach($data['permissions']);
        }

        return response()->json([
            'message' => 'Package created successfully',
            'package' => $package->load('permissions')
        ], 201);
    }
    
    public function show($id)
    {
        $package = Package::with('permissions')->find($id);

        return $package
            ? response()->json($package)
            : response()->json(['message' => 'Package not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:packages,name,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $package->update($data);

        if (isset($data['permissions'])) {
            $package->permissions()->sync($data['permissions']);
        }

        return response()->json([
            'message' => 'Package updated successfully',
            'package' => $package->load('permissions')
        ]);
    }

    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $package->delete();

        return response()->json(['message' => 'Package deleted successfully']);
    }
}
