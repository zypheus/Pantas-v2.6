<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use Illuminate\Support\Facades\Response;

class EmployeeController extends Controller
{
    /**
     * Show all faculty (employees with role_id = 2)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $faculty = Employee::with('role')
            ->where('role_id', 2)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->orderBy('lastname', 'asc') // ⭐ Order by lastname
            ->paginate(10);              // ⭐ 10 per page
    
        return view('employees.index', compact('faculty'));
    }
    
        /**
     * Show the edit form for an employee
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update employee record
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
    
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'tin_id_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
            'sss_number' => 'nullable|string|max:255',
            'hdmf_number' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string|max:255',
            'employee_signature' => 'nullable|string',
            'formal_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // Ensure role stays as Faculty (2)
        $validated['role_id'] = 2;
    
        // ✅ Handle profile picture upload (replace old one if new uploaded)
        if ($request->hasFile('formal_picture')) {
            $file = $request->file('formal_picture');
            $filename = time() . '_profile_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(base_path('images/formal_pictures'), $filename);
            $validated['formal_picture'] = 'images/formal_pictures/' . $filename;
        }
    
        // ✅ Handle signature (base64)
        if (!empty($validated['employee_signature']) && str_starts_with($validated['employee_signature'], 'data:')) {
            $data = $validated['employee_signature'];
            [$meta, $contents] = explode(',', $data, 2);
            $ext = 'png';
            if (preg_match('/data:image\/(jpeg|jpg)/i', $meta)) $ext = 'jpg';
            $sigName = time() . '_sig.' . $ext;
    
            // Ensure directory exists
            if (!file_exists(base_path('images/signatures'))) {
                mkdir(base_path('images/signatures'), 0755, true);
            }
    
            file_put_contents(base_path('images/signatures/' . $sigName), base64_decode($contents));
            $validated['employee_signature'] = 'images/signatures/' . $sigName;
        }
    
        // ✅ Update record
        $employee->update($validated);
    
        return redirect()->back()->with('success', 'Employee details updated successfully.');
    }



    /**
     * Delete employee
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }
}
