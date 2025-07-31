<x-staff-layout>

<div class="flex flex-col w-[95%] mx-4 border border-0 mx-auto">
    <section class="flex flex-col border-b border-gray-200 py-2 mt-2 md:flex-row md:justify-between">
            <div>
                    <div class="text-2xl font-semibold ">
                        Dashboard                
                    </div>
                    <div>
                        @if (Auth::check())
                            @php
                                $surname = ucfirst(strtolower(Auth::user()->surname))
                            @endphp

                            Welcome, {{ $surname }} {{ Auth::user()->firstname}} 

                            @if (Auth::user()->middlename != null)
                                {{ Auth::user()->middlename }}
                            @endif
                        @endif
                    </div>
            </div>
            <div class='text-sm mt-2 md:text-md flex flex-col md:items-center border-0 justify-center'>
                    <div class='flex flex-row border-0 space-x-4'>
                        <div>
                            <a class='hover:underline' href="#">My Profile</a>
                        </div> 
                        <div>
                            <a class='hover:underline' href="#">Change Password</a>
                        </div>
                    </div>
            </div>
    </section>

    <!-- @include('partials._dashboard_submenu1') -->


    <section class='border-0 mx-auto w-full py-16'>
        <div class='flex flex-col md:flex-row mx-auto w-[80%] border-0 space-y-4 md:space-y-0 md:space-x-4'>

                <a href="#" class='flex flex-col w-full md:w-1/4 p-8 border items-center justify-center 
                            bg-gradient-to-b from-purple-600 to-purple-500 text-white text-xl rounded-md 
                            font-semibold hover:bg-gradient-to-b hover:from-purple-400 hover:to-purple-600'>
                    Staff Data
                </a>

                 <a href="#" class='flex flex-col w-full md:w-1/4 p-8 border items-center justify-center 
                            bg-gradient-to-b from-orange-600 to-orange-500 text-white text-xl rounded-md 
                            font-semibold hover:bg-gradient-to-b hover:from-orange-400 hover:to-orange-600'>
                    APER Form
                 </a>


                 <a href="#" class='flex flex-col w-full md:w-1/4 p-8 border items-center justify-center 
                             bg-gradient-to-b from-blue-600 to-blue-500 text-white text-xl rounded-md 
                             font-semibold hover:bg-gradient-to-b hover:from-blue-400 hover:to-blue-600'>
                    Promotion
                 </a>

                 <a href="#" class='flex flex-col w-full md:w-1/4 p-8 border items-center justify-center 
                             bg-gradient-to-b from-pink-600 to-pink-500 text-white text-xl rounded-md 
                             font-semibold hover:bg-gradient-to-b hover:from-pink-400 hover:to-pink-600'>
                    Leave
                </a>



        </div>

    </section>


    

    
</div>

</x-staff-layout>