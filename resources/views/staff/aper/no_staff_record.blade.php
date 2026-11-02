<x-staff-layout title="APER Form — Staff record not found">
    <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center mb-4">
            <i class="ti ti-user-question text-2xl text-amber-500"></i>
        </div>
        <h2 class="text-sm font-bold text-gray-700 mb-1">Your staff record could not be found</h2>
        <p class="text-xs text-gray-500 max-w-md">
            Your login ({{ auth()->user()->fileno ?? '' }}) is not linked to a staff record yet, so the APER
            forms cannot be opened. Please contact the Personnel Records &amp; Statistics Unit (Registry)
            to have your staff and employment details captured.
        </p>
    </div>
</x-staff-layout>
