<x-guest-layout>


<div class="flex flex-col w-4/5  md:w-1/3 mx-auto items-center justify-center rounded-md mt-8  ">
        <form name="profile_create" action="{{ route('guests.staff_updates.upload_photograph') }}" method="POST"  enctype="multipart/form-data" 
                class="flex flex-col border border-1 justify-center items-center w-full rounded-md shadow-md py-8">
            @csrf
            
            @include('partials._session_response')


            <div class="flex flex-col w-[80%] md:w-[80%] py-4 mb-8 font-serif" >
                <h2 class="font-semibold text-xl py-1" >Staff Update</h2>
                Upload a Photograph of you for your Staff Profile
                
            </div>


            @if (session('candidate'))
                <div class="flex flex-col border-0 w-[80%] py-4 font-base">
                    Welcome  {{ session('candidate')->names }}
                </div>

            @endif



            <div class="flex flex-row justify-center py-4">
                    
                    @if (session('candidate_photo'))
                        @php
                            $avatar = session('candidate_photo');
                          
                        @endphp
                        <img src="{{ asset('storage/'.$avatar) }}" class="w-36 h-36 rounded-full" />                        
                    @else
                        <img src="{{ asset('images/avatar_150.jpg')}}" class="w-150" />
                    @endif
                    
            </div>
            
            <!-- file upload //-->
            <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-1">
                                
                                
                <input type="file" name="avatar" id="avatar" class="border border-1 border-gray-400 bg-gray-50
                                                         w-full p-3 rounded-md 
                                                         focus:outline-none
                                                         focus:border-blue-500 
                                                         focus:ring
                                                         focus:ring-blue-100" 
                  
                 style="font-family:'Lato';font-size:16px;font-weight:500;"
                 accept=".jpg, .jpeg, .png"
                 required
                 />
                    

                 @error('avatar')
                    <span class="text-red-700 text-sm">
                        {{$message}}
                    </span>
                 @enderror
                
            </div>
           <!-- end of file upload //-->


            
            <!-- button //-->
            <div class="flex flex-row border-red-900 w-[80%] md:w-[60%] py-2 mb-8 space-x-2">
                <div class="flex flex-1 w-full border border-1">
                    <button type="submit" class="w-full border border-1 bg-gray-400 py-4 text-lg text-white 
                                hover:bg-gray-500 
                                focus:outline-none
                                focus:border-gray-200
                                focus:ring
                                focus:ring-gray-200
                                rounded-md text-md" style="font-family:'Lato';font-weight:500;">Create Staff Photo</button>
                </div>
                
                @if ( session('candidate_photo'))
                    <div class="flex border border-1">
                            <a href="{{ route('guests.staff_updates.create_update_form') }}" class="w-full border border-1 bg-green-400 py-4 px-4 text-white 
                                 hover:bg-green-500 rounded-md text-md" style="font-family:'Lato';font-weight:500;">Continue</a>
                    </div>
                @endif
            </div>



        <!-- end of buttons //-->



        </form>


    </div>




</x-guest-layout>