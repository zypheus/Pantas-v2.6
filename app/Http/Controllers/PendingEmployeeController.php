<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingEmployee;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PendingEmployeeController extends Controller
{
    public function create()
    {
        $roles = Role::all();
        return view('pending.register', compact('roles'));
    }

    public function index()
    {
        $pending = PendingEmployee::with('role')->latest()->get();
        return view('pending.index', compact('pending'));
    }

    public function store(Request $request)
    {

        // Validation
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'employee_id' => 'required|string|max:255|unique:pending_employees,employee_id',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'civil_status' => 'nullable|string|max:50',
            'blood_type' => 'nullable|string|max:5',
            'tin_id_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'sss_number' => 'nullable|string|max:255',
            'hdmf_number' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'formal_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'employee_signature' => 'nullable|string',
        ]);
    
        // Set role_id = 2 (Faculty)
        $validated['role_id'] = 2;
    
        // Handle profile picture upload
        if ($request->hasFile('formal_picture')) {
            $file = $request->file('formal_picture');
            $filename = time() . '_profile_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(base_path('images/formal_pictures'), $filename);
            $validated['formal_picture'] = 'images/formal_pictures/' . $filename;
        }
    
        // Handle signature from base64
        if (!empty($validated['employee_signature']) && str_starts_with($validated['employee_signature'], 'data:')) {
            $data = $validated['employee_signature'];
            [$meta, $contents] = explode(',', $data, 2);
            $ext = 'png';
            if (preg_match('/data:image\/(jpeg|jpg)/i', $meta)) $ext = 'jpg';
            $sigName = time() . '_sig.' . $ext;
    
            if (!file_exists(base_path('images/signatures'))) {
                mkdir(base_path('images/signatures'), 0755, true);
            }
    
            file_put_contents(base_path('images/signatures/' . $sigName), base64_decode($contents));
            $validated['employee_signature'] = 'images/signatures/' . $sigName;
        }
    
        // Auto-generate QR code
        $last = PendingEmployee::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($last && !empty($last->qrcode) && str_starts_with($last->qrcode, 'E-')) {
            $lastNum = (int) Str::after($last->qrcode, 'E-');
            $nextNumber = $lastNum + 1;
        }
        $validated['qrcode'] = 'E-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    
        // Save
        PendingEmployee::create($validated);
    
        return redirect()->back()->with('success', 'Employee registration submitted! Await admin approval.');
    }
    
    public function approve($id)
    {
        DB::beginTransaction();
    
        try {
            $pending = PendingEmployee::findOrFail($id);
    
            // Generate next QR code for employee (E-00000001 style)
            $lastEmployee = Employee::orderBy('id', 'desc')->first();
            $lastQr = $lastEmployee ? $lastEmployee->qrcode : null;
            $nextNumber = 1;
    
            if ($lastQr && str_starts_with($lastQr, 'E-')) {
                $lastNum = intval(substr($lastQr, 2));
                $nextNumber = $lastNum + 1;
            }
    
            $newQr = 'E-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    
            // Role ID for Faculty (you said role_id = 2)
            $rid = 2;
    
            // Insert into real employees table
            Employee::create([
                'employee_id'   => $pending->employee_id, // unique ID from pending table
                'formal_picture'=> $pending->formal_picture,
                'department'    => $pending->department,
                'firstname'     => $pending->firstname,
                'lastname'      => $pending->lastname,
                'position'      => $pending->position,
                'birth_date'    => $pending->birth_date ?? null,
                'sex'           => $pending->sex ?? null,
                'tin_id_number' => $pending->tin_id_number ?? null,
                'philhealth_number' => $pending->philhealth_number ?? null,
                'civil_status'  => $pending->civil_status ?? null,
                'blood_type'    => $pending->blood_type ?? null,
                'sss_number'    => $pending->sss_number ?? null,
                'hdmf_number'   => $pending->hdmf_number ?? null,
                'qrcode'        => $newQr,
                'emergency_contact_name' => $pending->emergency_contact_name ?? null,
                'emergency_contact_relationship' => $pending->emergency_contact_relationship ?? null,
                'address'       => $pending->address ?? null,
                'emergency_contact_number' => $pending->emergency_contact_number ?? null,
                'employee_signature' => $pending->employee_signature ?? null,
                'role_id'       => $rid,
            ]);
            // Delete pending record after approval
            $pending->delete();
    
            DB::commit();
            return back()->with('success', 'Employee approved and added to the employees table.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function reject($id)
    {
        $pending = PendingEmployee::findOrFail($id);
        $pending->delete();
    
        return back()->with('success', 'Employee registration rejected.');
    }


}
