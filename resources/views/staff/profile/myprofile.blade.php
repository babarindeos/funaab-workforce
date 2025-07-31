<x-staff-layout>

    <div class="flex flex-col container mx-4 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    My Profile              
                </div>                
        </section>

        <section class="flex flex-col md:flex-row rounded w-full mt-3 md:space-x-4">
            <div class="flex flex-col w-full  md:w-[30%] justify-center items-center 
                        border px-8 py-4 rounded-md">
                    <div class="">
                        @if (Auth::user()->profile->avatar != "" || Auth::user()->profile->avatar != null)
                            <img src="{{ asset('storage/'.Auth::user()->profile->avatar) }}" class="w-36 h-36 rounded-full" />
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
            <div class="flex flex-col justify-center md:border rounded-md w-full md:w-[70%] py-2 px-4 border-0">
                    <div class="flex justify-end py-4 md:px-4 md:py-0 space-x-2">
                            <a href="{{ route('staff.profile.myprofile.edit') }}" class="border px-4 py-1 rounded-md ring-0 
                                 border-gray-500 bg-gray-100 hover:shadow-md text-xs md:text-sm hover:font-semibold">
                                Edit
                            </a>

                            <a href="{{ route('staff.profile.change_password') }}" class="border px-4 py-1 rounded-md ring-0 
                                 border-gray-500 bg-gray-100 hover:shadow-md text-xs md:text-sm hover:font-semibold">
                                Change Password
                            </a>
                    </div>
                    <div class="mb-4 mt-4  mx-[10%] md:mx-0 ">
                            <div class="text-xl font-semibold">
                                    {{ Auth::user()->surname }} {{ Auth::user()->firstname }} {{ Auth::user()->middlename }}                                
                            </div>
                            <div class="text-sm">
                                    {{ Auth::user()->profile->designation}}, {{ Auth::user()->fileno}}
                            </div>                            
                    </div>


                    <div class="py-4 mx-[10%] md:mx-0">
                                <div class="font-semibold py-1">
                                        Contacts
                                    </div>
                                <div>
                                        {{ Auth::user()->email }}
                                </div>
                                <div>
                                        {{ Auth::user()->profile->phone}}
                                </div>
                    </div>



                    



                    <div class="py-4 mx-[10%] md:mx-0">
                                    <!-- circles //--> 
                                    <div class="font-semibold py-1">
                                        Work Circles
                                    </div>
                                               
                    </div>

            </div>
        </section>
    


    </div>


</x-staff-layout>