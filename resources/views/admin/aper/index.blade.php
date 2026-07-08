<x-admin-layout title="APER Evaluation">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">APER Form</h1>
            <p class="pg-sub">Annual Performance Evaluation Report — Federal University of Agriculture, Abeokuta</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> Create New APER Form
        </button>
    </div>

    {{-- Filters & Search --}}
    <div class="wf-card mb-4">
        <form method="GET" action="{{ route('admin.aper.index') }}" class="flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                
                <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
            </div>

            {{-- Status Filter --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                @foreach(['all' => 'All', 'opened' => 'Opened', 'closed' => 'Closed'] as $val => $label)
                    <a href="{{ route('admin.aper.index', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                       class="px-3 py-1.5 text-xs font-semibold rounded-md transition
                           {{ request('status', 'all') === $val ? 'bg-white text-funaab-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <button type="submit" class="btn-wf-ghost">
                <i class="ti ti-filter text-sm"></i> Filter
            </button>

            @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('admin.aper.index') }}" class="text-xs text-gray-400 hover:text-red-500 font-semibold">
                    <i class="ti ti-x text-sm"></i> Clear
                </a>
            @endif

        </form>
    </div>

    {{-- APER Forms Table --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-forms text-gray-400"></i> APER Form Records
            </div>
            <span class="text-[10.5px] text-gray-400 font-semibold">
                {{ $aperForms->total() }} record{{ $aperForms->total() !== 1 ? 's' : '' }} found
            </span>
        </div>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aperForms as $form)
                    @php
                        $today  = \Carbon\Carbon::today();
                        $start  = \Carbon\Carbon::parse($form->start_date);
                        $end    = \Carbon\Carbon::parse($form->end_date);
                        $isOpen = $today->between($start, $end);
                    @endphp
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $aperForms->firstItem() + $loop->index }}</td>
                        <td class="col-name">{{ $form->name }}</td>
                        <td>{{ $start->format('d M Y') }}</td>
                        <td>{{ $end->format('d M Y') }}</td>
                        <td>
                            @if($isOpen)
                                <span class="badge badge-active">Opened</span>
                            @else
                                <span class="badge badge-inactive">Closed</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                
                                <button onclick="openEdit(this)"
                                        data-id="{{ $form->uuid }}"
                                        data-name="{{ addslashes($form->name) }}"
                                        data-start="{{ $form->start_date }}"
                                        data-end="{{ $form->end_date }}"
                                        data-description="{{ addslashes($form->description ?? '') }}"
                                        class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-edit text-xs"></i> Edit
                                </button>


                                <form action="{{ route('admin.aper.destroy', $form->uuid) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this APER form? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-wf-danger btn-wf-sm">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <i class="ti ti-forms text-3xl block mb-2"></i>
                            No APER forms found. Create one to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($aperForms->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-[11px] text-gray-400">
                    Showing <span class="font-semibold text-gray-600">{{ $aperForms->firstItem() }}</span>
                    – <span class="font-semibold text-gray-600">{{ $aperForms->lastItem() }}</span>
                    of <span class="font-semibold text-gray-600">{{ $aperForms->total() }}</span> records
                </p>
                {{ $aperForms->appends(request()->query())->links() }}
            </div>
        @endif
    </div>


    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Create New APER Form</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="{{ route('admin.aper.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        FORM NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. APER Form 2025/2026"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            START DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                               required />
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            END DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                               required />
                        @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        DESCRIPTION <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" rows="3"
                              placeholder="Brief description of this APER form cycle…"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none
                                     focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save APER Form
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit APER Form</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        FORM NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required />
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            START DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="edit-start-date"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                               required />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            END DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="edit-end-date"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                               required />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        DESCRIPTION <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" id="edit-description" rows="3"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none
                                     focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update APER Form
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openEdit(btn) {
            document.getElementById('edit-name').value         = btn.dataset.name;
            document.getElementById('edit-start-date').value   = btn.dataset.start;
            document.getElementById('edit-end-date').value     = btn.dataset.end;
            document.getElementById('edit-description').value  = btn.dataset.description;
            document.getElementById('edit-form').action        = `/admin/aper/${btn.dataset.id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        // Re-open create modal on validation error
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-admin-layout>