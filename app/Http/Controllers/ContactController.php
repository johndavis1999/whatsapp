<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Contact;
use App\Models\User;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = auth()->user()->contacts()->paginate();
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:50',
            'email' => [
                'email',
                'required',
                'exists:users',
                Rule::notIn([auth()->user()->email]),
            ],
        ]);

        $user = User::where('email', $request->email)->first();

        $data['contact_id'] = $user->id;
        $data['user_id'] = auth()->id();
        $data['name'] = $request->name;
        
        $contact = Contact::create($data);
        session()->flash('flash.banner', 'El contacto se ha creado correctamente.');
        session()->flash('flash.bannerStyle', 'success');
        return redirect()->route('contacts.edit', $contact);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return view('contacts.create', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        return view('contacts.create', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
