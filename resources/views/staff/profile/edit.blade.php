<x-staff-layout>
    <div class="flex flex-col w-4/5 md:w-1/3 mx-auto items-center justify-center rounded-md mt-0 mb-8">
        <form name="profile_create" action="{{ route('staff.profile.myprofile.update') }}" method="POST"  enctype="multipart/form-data" 
             class="flex flex-col border border-1 justify-center items-center w-full rounded-md shadow-md py-16">
            @csrf
            <div class="flex flex-col py-2 justify-center items-center font-semibold text-xl">
                    Edit Profile
            </div>
            <div class="flex flex-row justify-center">
                    
                    @if (Auth::user()->profile==null)
                    <img src="{{ asset('images/avatar_150.jpg')}}" class="w-150" />
                    @else
                        <img src="{{ asset('storage/'.Auth::user()->profile->avatar) }}" class="w-36 h-36 rounded-full" />
                    @endif
                    
            </div>
            
            <!-- Display name, designation and fileno //-->
            <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1 mb-4">                             
                    <div class="mx-auto text-lg font-semibold">
                            {{ Auth::user()->surname }} {{ Auth::user()->firstname }} {{ Auth::user()->middlename }}
                    </div>
                    <div class="text-sm mx-auto">
                            {{ Auth::user()->profile->designation }}, {{ Auth::user()->fileno}}
                    </div>
                    
            </div>
           <!-- end of display name, designation and fileno //-->


            <!-- designation  //-->
            <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                    
                                    
                <input type="text" name="designation" placeholder="Designation" class="border border-1 border-gray-400 bg-gray-50
                                                        w-full p-3 rounded-md 
                                                        focus:outline-none
                                                        focus:border-blue-500 
                                                        focus:ring
                                                        focus:ring-blue-100" 
                @if (Auth::user()->profile !=null)  value="{{Auth::user()->profile->designation}} " @endif
                style="font-family:'Lato';font-size:16px;font-weight:500;"   
                required          
                 />
                    

                @error('designation')
                    <span class="text-red-700 text-sm">
                        {{$message}}
                    </span>
                @enderror
                
            </div>
            <!-- end of designation //-->


            <!-- phone  //-->
            <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                    
                                    
                <input type="text" name="phone" placeholder="Phone Number" class="border border-1 border-gray-400 bg-gray-50
                                                        w-full p-3 rounded-md 
                                                        focus:outline-none
                                                        focus:border-blue-500 
                                                        focus:ring
                                                        focus:ring-blue-100" 
                @if (Auth::user()->profile !=null) value="{{ Auth::user()->profile->phone }}" @endif
                style="font-family:'Lato';font-size:16px;font-weight:500;"             
                required/>
                    

                @error('phone')
                    <span class="text-red-700 text-sm">
                        {{$message}}
                    </span>
                @enderror
                
            </div>
            <!-- end of phone //-->


            <!-- button //-->
            <div class="flex flex-row border-red-900 w-[80%] md:w-[60%] py-2 mb-2 space-x-2">
                <div class="flex flex-1 w-full border border-1">
                    <button type="submit" class="w-full border border-1 bg-gray-400 py-4 text-white 
                                hover:bg-gray-500
                                rounded-md text-md" style="font-family:'Lato';font-weight:500;">Update Profile</button>
                </div>
                
                @if (Auth::user()->profile !=null)
                    <div class="flex border border-1">
                            <a href="{{ route('staff.profile.myprofile') }}" class="w-full border border-1 bg-green-400 py-4 px-4 text-white 
                                 hover:bg-green-500 rounded-md text-md" style="font-family:'Lato';font-weight:500;">Continue</a>
                    </div>
                @endif
            </div>



        <!-- end of buttons //-->



        </form>


    </div>

</x-staff-layout>