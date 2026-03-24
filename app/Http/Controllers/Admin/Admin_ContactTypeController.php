<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ContactType;

class Admin_ContactTypeController extends Controller
{
    //
    public function index(Request $request)
    {
            $query = $request->query('q');

            if ($query == null)
            {
                $contact_types = ContactType::orderBy('created_at', 'desc')->paginate(50);
            }
            else
            {
                $contact_types = ContactType::where('contact_type_name', 'like', "%{$query}%")
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(50);
            }

            
            return view('admin.contact_types.index', compact('contact_types'));
    }



    public function create()
    {
            return view('admin.contact_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:contact_types,contact_type_name'
        ]);

        $formFields = [
            'contact_type_name' => $request->name
        ];

        
        try
        {
            $create = ContactType::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Contact Type has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Contact Type'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }


    public function edit(ContactType $contact_type)
    {
        return view('admin.contact_types.edit', compact('contact_type'));
    }


    public function update(Request $request, ContactType $contact_type)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $formFields = [
            'contact_type_name' => $request->name
        ];

        
        try
        {
            $update = $contact_type->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Contact Type has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Contact Type'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);

    }

}
