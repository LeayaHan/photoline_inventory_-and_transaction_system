@php($editing = isset($user))

<div class="form-group">
    <label for="employee_id">Employee ID</label>
    <input type="text" id="employee_id" name="employee_id"
           value="{{ old('employee_id', $user->employee_id ?? '') }}" required>
    @error('employee_id') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Full Name</label>
    <div class="name-grid">
        <div>
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name"
                   value="{{ old('first_name', $user->first_name ?? '') }}" required>
            @error('first_name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name"
                   value="{{ old('middle_name', $user->middle_name ?? '') }}" placeholder="Optional">
            @error('middle_name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name"
                   value="{{ old('last_name', $user->last_name ?? '') }}" required>
            @error('last_name') <div class="error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-group two-grid">
    <div>
        <label for="position">Position</label>
        <input type="text" id="position" name="position"
               value="{{ old('position', $user->position ?? '') }}" placeholder="e.g. Photographer, Cashier" required>
        @error('position') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="date_hired">Date Hired</label>
        <input type="date" id="date_hired" name="date_hired" max="{{ now()->toDateString() }}"
               value="{{ old('date_hired', optional($user->date_hired ?? null)->format('Y-m-d')) }}" required>
        @error('date_hired') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group two-grid">
    <div>
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone"
               value="{{ old('phone', $user->phone ?? '') }}" required>
        @error('phone') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="email">Email (login)</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label for="address">Home Address</label>
    <input type="text" id="address" name="address"
           value="{{ old('address', $user->address ?? '') }}" required>
    @error('address') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group two-grid">
    <div>
        <label for="emergency_contact_name">Emergency Contact Name</label>
        <input type="text" id="emergency_contact_name" name="emergency_contact_name"
               value="{{ old('emergency_contact_name', $user->emergency_contact_name ?? '') }}" required>
        @error('emergency_contact_name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="emergency_contact_phone">Emergency Contact Phone</label>
        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
               value="{{ old('emergency_contact_phone', $user->emergency_contact_phone ?? '') }}" required>
        @error('emergency_contact_phone') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group two-grid">
    <div>
        <label for="password">{{ $editing ? 'New Password' : 'Password' }}</label>
        <input type="password" id="password" name="password"
               @if($editing) placeholder="Leave blank to keep current password" @else required @endif>
        @error('password') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="password_confirmation">{{ $editing ? 'Confirm New Password' : 'Confirm Password' }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               @if($editing) placeholder="Re-enter new password" @else required @endif>
    </div>
</div>
