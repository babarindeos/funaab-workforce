<x-staff-layout>

    <div class="flex flex-col container mx-4 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    My Profile              
                </div>                
        </section>

        <section class="flex flex-col md:flex-row rounded w-full mt-3 space-x-4">
            <div class="flex flex-col w-full  md:w-[30%] justify-center items-center 
                        border px-8 py-4 rounded-md">
                    <div class="">
                        @if (Auth::user()->profile->avatar != "" || Auth::user()->profile->avatar != null)
                            @php
                                
                            @endphp
                            <img src="{{ asset('storage/'.Auth::user()->profile->avatar) }}" class="w-42 h-42 rounded-full" />
                        @else
                            <img src="{{ asset('images/avatar_150.jpg') }}" />
                        @endif
                    </div>
                    <div class="mt-2">
                        <form enctype="multipart/form-data" action="{{ route('staff.profile.myprofile.update_avatar') }}"  method="POST" class="flex flex-col items-center">
                            @csrf
                            <input type="file" name="photo" class="text-sm border-0 border-gray-500
                                                     items-center 
                                                     justify-center w-3/5" />
                            @error('photo')
                                <span class="text-red-700 text-sm mx-[10%]">
                                    {{$message}}
                                </span>
                            @enderror
                            

                            <div class="py-2">
                                    <button type="submit" class="px-4 py-1 text-sm 
                                                         rounded-md border border-gray-500 ring-0 
                                                         ring-cyan-500
                                                         hover:shadow-md hover:font-semibold bg-gray-100 ">Update Avatar</button>
                            </div>
                        </form>
                    </div>

            </div>
            <div class="flex flex-col justify-center md:border rounded-md md:w-[70%] py-2 px-4">
                    <div class="flex justify-end px-4 space-x-2">
                            <a href="{{ route('staff.profile.myprofile.edit') }}" class="border px-4 py-1 rounded-md ring-0 
                                 border-gray-500 bg-gray-100 hover:shadow-md text-xs md:text-sm hover:font-semibold">
                                Edit
                            </a>

                            <a href="{{ route('staff.profile.change_password') }}" class="border px-4 py-1 rounded-md ring-0 
                                 border-gray-500 bg-gray-100 hover:shadow-md text-xs md:text-sm hover:font-semibold">
                                Change Password
                            </a>
                    </div>
                    <div class="mb-4  mx-[10%] md:mx-0 ">
                            <div class="text-xl font-semibold">
                                    {{ Auth::user()->surname }} {{ Auth::user()->firstname }} {{ Auth::user()->middlename }}                                
                            </div>
                            <div class="text-sm">
                                    {{ Auth::user()->profile->designation}}, {{ Auth::user()->fileno}}
                            </div>                            
                    </div>


                    {{-- <div class="py-4 mx-[10%] md:mx-0">
                                                             
                    </div> --}}

                    @include('partials._session_response')

                    <!-- change password //-->
                    <section>
                        <form action="{{ route('staff.profile.update_password') }}" method="POST">
                            @csrf

                                <!-- current password  //-->
                                <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                                        
                                                        
                                    <input type="password" name="current_password" placeholder="Current Password" 
                                                                            class="border border-1 border-gray-400 bg-gray-50
                                                                            w-full p-3 rounded-md 
                                                                            focus:outline-none
                                                                            focus:border-blue-500 
                                                                            focus:ring
                                                                            focus:ring-blue-100"
                                    
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"   
                                    required          
                                    />                               

                                    @error('current_password')
                                        <span class="text-red-700 text-sm">
                                            {{$message}}
                                        </span>
                                    @enderror
                                    
                                </div>
                                <!-- end of current password //-->


                                <!-- new password //-->                        
                                <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                                        
                                                        
                                    <input type="password" name="new_password" placeholder="New Password" 
                                                                            class="border border-1 border-gray-400 bg-gray-50
                                                                            w-full p-3 rounded-md 
                                                                            focus:outline-none
                                                                            focus:border-blue-500 
                                                                            focus:ring
                                                                            focus:ring-blue-100"
                                    
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"   
                                    required          
                                    />                               

                                    @error('new_password')
                                        <span class="text-red-700 text-sm">
                                            {{$message}}
                                        </span>
                                    @enderror
                                    
                                </div>                        
                                <!-- end of new password //-->

                                <!-- confirm password //-->                        
                                <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                                        
                                                        
                                    <input type="password" name="new_password_confirmation" placeholder="Confirm Password" 
                                                                            class="border border-1 border-gray-400 bg-gray-50
                                                                            w-full p-3 rounded-md 
                                                                            focus:outline-none
                                                                            focus:border-blue-500 
                                                                            focus:ring
                                                                            focus:ring-blue-100"
                                    
                                    style="font-family:'Lato';font-size:16px;font-weight:500;"   
                                    required          
                                    />                               

                                    @error('new_password_confirmation')
                                        <span class="text-red-700 text-sm">
                                            {{$message}}
                                        </span>
                                    @enderror
                                    
                                </div>                        
                                <!-- end of confirm password //-->


                                <!-- button //-->
                                <div class="flex flex-row border-red-900 w-[80%] md:w-[60%] py-2 mb-2 space-x-2">
                                    <div class="flex flex-1 w-full border border-1">
                                        <button type="submit" class="w-full border border-1 bg-gray-400 py-4 text-white 
                                                    hover:bg-gray-500
                                                    rounded-md text-md" style="font-family:'Lato';font-weight:500;">Change Password</button>
                                    </div>                            
                                
                                </div>
                                <!-- end of button //-->
                        </form>


                    </section>
                    <!-- end of change password //-->
                    

            </div>
        </section>
    


    </div>


</x-staff-layout>