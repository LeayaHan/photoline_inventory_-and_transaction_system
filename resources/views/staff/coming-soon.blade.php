<x-staff-layout :title="$title">
    <div class="staffui-welcome">
        <h1>{{ $title }}</h1>
        <p>This section is not built yet.</p>
    </div>

    <div class="staffui-empty">
        <span class="staffui-card-icon">⌛</span>
        <h3>Coming soon</h3>
        <p>The {{ strtolower($title) }} module for staff will be available here.</p>
        <a href="{{ route('staff.dashboard') }}" class="staffui-btn">Back to dashboard</a>
    </div>
</x-staff-layout>
