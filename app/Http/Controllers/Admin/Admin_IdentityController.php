<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;

class Admin_IdentityController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        $search_result = null;

        if ($query != null)
        {
            $search_result = Identity::where('fileno', 'like', "%{$query}%")                                      
                                      ->orWhere('names', 'like', "%{$query}%")
                                      ->get();
                                      
        }       

        return view('admin.identity.index', compact('query', 'search_result'));
    }


    public function show(Identity $identity)
    {   
        return view('admin.identity.show', compact('identity'));
    }

    /**
     * Edit an identity (nominal-roll) record. Only the fields listed in
     * identity_update_fields are editable; each change is logged.
     */
    public function edit(Identity $identity)
    {
        $fields = \App\Models\IdentityUpdateField::orderBy('id')->get()
            ->filter(fn ($f) => \Illuminate\Support\Facades\Schema::hasColumn('identities', $f->field));

        $history = \App\Models\IdentityUpdateHistory::where('identity_id', $identity->id)
            ->latest()->take(20)->get();

        return view('admin.identity.edit', compact('identity', 'fields', 'history'));
    }

    public function update(Request $request, Identity $identity)
    {
        $fields = \App\Models\IdentityUpdateField::orderBy('id')->get()
            ->filter(fn ($f) => \Illuminate\Support\Facades\Schema::hasColumn('identities', $f->field))
            ->keyBy('field');

        $request->validate(
            $fields->mapWithKeys(fn ($f) => [$f->field => ['nullable', 'string', 'max:255']])->all()
            + ['remark' => ['nullable', 'string', 'max:1000']]
        );

        $changed = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $identity, $fields, &$changed) {
            foreach ($fields as $name => $field) {
                if (!$request->has($name)) {
                    continue;
                }
                $old = (string) ($identity->{$name} ?? '');
                $new = trim((string) $request->input($name, ''));
                if ($old === $new) {
                    continue;
                }
                \App\Models\IdentityUpdateHistory::insert([
                    'identity_id'     => $identity->id,
                    'update_field_id' => $field->id,
                    'old'             => mb_substr($old, 0, 255),
                    'new'             => mb_substr($new, 0, 255),
                    'remark'          => $request->input('remark'),
                    'updated_by'      => auth()->id(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $identity->{$name} = $new === '' ? null : $new;
                $changed++;
            }
            $identity->save();
        });

        return redirect()->route('admin.identity.show', $identity->id)
            ->with('success', $changed ? "{$changed} field(s) updated." : 'No changes were made.');
    }
}
