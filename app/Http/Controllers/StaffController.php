<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    private function protectManager(Staff $staff): void
    {
        abort_if(
            $staff->email === 'photolinemanager@gmail.com',
            403,
            'This account is protected.'
        );
    }

    public function index()
    {
        $staff = Staff::where(
            'email',
            '!=',
            'photolinemanager@gmail.com'
        )->latest()->paginate(10);

        return view('branch-manager.users.index', compact('staff'));
    }

    public function create()
    {
        return view('branch-manager.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:staff,email'],
            'contact_number' => ['required', 'string', 'max:30'],
            'position' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $nextId = (Staff::max('id') ?? 0) + 1;

        $validated['staff_id'] = 'STF-' . str_pad(
            $nextId,
            6,
            '0',
            STR_PAD_LEFT
        );

        $validated['password'] = Hash::make($validated['password']);

        Staff::create($validated);

        return redirect()
            ->route('branch-manager.users.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function show(Staff $staff)
    {
        $this->protectManager($staff);

        return view('branch-manager.users.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $this->protectManager($staff);

        return view('branch-manager.users.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $this->protectManager($staff);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staff->id),
            ],
            'contact_number' => ['required', 'string', 'max:30'],
            'position' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        return redirect()
            ->route('branch-manager.users.index')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $this->protectManager($staff);

        $staff->delete();

        return redirect()
            ->route('branch-manager.users.index')
            ->with('success', 'Staff account deleted successfully.');
    }
}
