<x-identity-layout>


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
                            <a class='hover:underline' href="{{ route('identity.dashboard.my_profile') }}">My Profile</a>
                        </div> 
                        <div>
                            <a class='hover:underline' href="{{ route('identity.auth.change_password') }}">Change Password</a>
                        </div>
                    </div>
            </div>
    </section>

    <!-- @include('partials._dashboard_submenu1') -->


    <section class='border-0 mx-auto w-full py-4'>
        <div class='flex flex-col w-full md:flex-row mx-auto md:w-1/2 border-0 space-y-4 md:space-y-0 md:space-x-4'>

            

                    <!-- Search //-->
                    
                            <form action="{{ route('identity.dashboard.index') }}" method="GET" class="flex flex-row items-center w-full border-0">
                            
                                            <div class="flex flex-col justify-center items-center border-0 w-full">
                                            
                                                    <input type="text" name="q" class="w-full md:w-2/2 border border-1 border-gray-400 bg-gray-50
                                                            p-4 rounded-l-md 
                                                            focus:outline-none
                                                            focus:border-blue-500 
                                                            focus:ring
                                                            focus:ring-blue-100" placeholder="Search..."                
                                                            
                                                            style="font-family:'Lato';font-size:20px;font-weight:500;"                                                                  
                                                    
                                                    />  
                                            </div>
                                            <div class='border-0'>
                                                    
                                                    <button type='submit' class="hover:bg-blue-500 hover:border-blue-500 bg-blue-400 text-white rounded-r-md 
                                                            px-4 py-4 text-md border-blue-400 border-0" ><i class="fa-solid fa-magnifying-glass"></i></button>
                                                    
                                            </div>
                                            
                                    
                            </form>
                   
                    <!-- end of Search //-->

        </div>
    </section>




    @if ($query != null )
        @if ($search_result->count())


                <div class="py-1 w-[95%] border-0 mx-auto ">
                                     <div class="flex flex-col md:flex-row border-b">
                                        <div class='text-xl py-3'>Search Result ({{ $search_result->total() }}) </div>
                                     </div>
                                     <div class='py-2'>
                                            {{ $search_result->links() }}
                                        </div>
                                    <div class="flex flex-col overflow-x-auto">
                                        <div class="sm:-mx-6 lg:-mx-8">
                                            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-0">
                                                <div class="overflow-x-auto">
                                                        <table  class="w-full border text-start text-md  text-surface ">
                                                        <thead
                                                            class="border-b border-neutral-200 font-medium dark:border-white/10">
                                                            <tr class='bg-green-100 text-black'>
                                                                <th class="text-center font-semibold py-2 py-4 px-8 w-[5%] ">#</th>
                                                                <th class="font-semibold py-2 text-left w-[10%]">File No</th>
                                                                <th class="font-semibold py-2 text-left">Names</th>
                                                                <th class="font-semibold py-2 text-left w-[10%]">College/Dept</th>
                                                                <th class="font-semibold py-2 text-left">Designation</th>
                                                                <th class="font-semibold py-2 text-left w-[10%]">Level/Step</th>
                                                                <th class="font-semibold py-2 text-left">Contact</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $counter = ($search_result->currentPage() - 1) * $search_result->perPage();
                                                            @endphp

                                                            @foreach($search_result as $result)
                                                            <tr class="border-b">
                                                                <td class='text-center px-2 py-8'>{{ ++$counter }}.</td>
                                                                <td class='text-left px-5 py-8'>{{ $result->fileno }}</td>
                                                                <td class='text-left py-8'>
                                                                    <a class='underline' href="{{ route('identity.staff.details',['identity' => $result->id]) }}">
                                                                        {{ $result->title }} {{ trim($result->names) }}
                                                                    </a>
                                                                </td>
                                                                <td class='text-left py-8'>{{ $result->college}}, {{$result->department }}</td>
                                                                <td class='text-left py-8 '>
                                                                    {{ $result->current_designation }}
                                                                    <div class='text-xs'>
                                                                        {{ $result->staff_status}}
                                                                    </div>

                                                                </td>
                                                                <td class='text-left py-8'>{{ $result->salary_level_step }}</td>
                                                                <td class='text-left py-8 w-fit'>{{ $result->phone }}<br/>{{ $result->email }}</td>
                                                               
                                                                

                                                            </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    </div>
       
                    <!-- end of search records in table //-->

                    <div class='py-2'>
                        {{ $search_result->links() }}
                    </div>

                
        @else
                <section class="flex flex-col w-[95%] md:w-[95%] border-1 mx-auto px-4 py-6">
                        <div class="flex flex-row justify-center items-center text-2xl font-bold text-gray-300">
                                No result is found
                        </div>
                </section>

        @endif
        
    @endif


    

    
</div>




</x-identity-layout>