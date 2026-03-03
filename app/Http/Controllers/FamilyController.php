<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function index($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $families = $employee->families;
        return view('families.index', compact('employee', 'families'));
    }

    public function create($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        return view('families.create', compact('employee'));
    }

    public function store(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'hubungan' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $validated['employee_id'] = $employee->id;
        Family::create($validated);

        return redirect()->route('families.index', $employee)->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function edit($employeeId, Family $family)
    {
        $employee = Employee::findOrFail($employeeId);
        if ($family->employee_id !== $employee->id) abort(404);
        return view('families.edit', compact('employee', 'family'));
    }

    public function update(Request $request, $employeeId, Family $family)
    {
        $employee = Employee::findOrFail($employeeId);
        if ($family->employee_id !== $employee->id) abort(404);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'hubungan' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $family->update($validated);
        return redirect()->route('families.index', $employee)->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy($employeeId, Family $family)
    {
        $employee = Employee::findOrFail($employeeId);
        if ($family->employee_id !== $employee->id) abort(404);
        $family->delete();
        return redirect()->route('families.index', $employee)->with('success', 'Data keluarga berhasil dihapus.');
    }
}
