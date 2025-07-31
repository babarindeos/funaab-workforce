<section class="flex flex-col md:flex-row md:space-x-4">
    <a href="{{ route('staff.documents.mydocuments') }}" class="border border-green-600 py-2 px-4 rounded-md mt-1 font-semibold 
                hover:bg-green-600 hover:text-white hover:shadow-md">
            Staff Data Update
    </a>

     @if (Auth::user()->role === 'manager')

        <div class="border border-green-600 py-2 px-4 rounded-md mt-1 font-semibold 
                        hover:bg-green-600 hover:text-white hover:shadow-md">
                Track Documents
        </div>

    @endif

    <a href="{{ route('staff.documents.create') }}" class="border border-green-600 py-2 px-4 rounded-md mt-1 font-semibold 
                     hover:bg-green-600 hover:text-white hover:shadow-md hidden">
        Upload Document
    </a>
</section>
