{{--
  Opening of an add/edit form on a staff record tab.
  Needs: $id (form id), $bag (error bag), $store (create URL), $title.
  The form remembers where it was posting to, so a validation error on an
  edit re-opens the edit (not a new record).
--}}
@php $err = $errors->getBag($bag); @endphp
<form id="{{ $id }}" method="POST" action="{{ old('_form_action') && $err->any() ? old('_form_action') : $store }}"
      data-store="{{ $store }}" data-title-add="Add {{ $title }}" data-title-edit="Edit {{ $title }}"
      class="record-form bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 {{ $err->any() ? '' : 'hidden' }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $err->any() ? old('_method', 'POST') : 'POST' }}">
    <input type="hidden" name="_form_action" value="{{ $err->any() ? old('_form_action', $store) : $store }}">
    <p class="text-sm font-bold text-gray-700 mb-3 record-form-title">{{ $err->any() && old('_method') === 'PUT' ? 'Edit ' : 'Add ' }}{{ $title }}</p>
    @if($err->any())
        <div class="mb-3 text-xs text-red-600">{{ $err->first() }}</div>
    @endif
