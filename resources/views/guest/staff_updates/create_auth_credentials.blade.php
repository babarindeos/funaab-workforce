<x-guest-layout>


<section class="flex flex-col w-full md:w-1/2 border-0 mx-auto justify-center">

            <!-- Right  panel //-->
            <div class="flex flex-col w-full md:w-[100%] items-center justify-center py-4">

                        <section class="flex flex-col w-full border border-0">
                            <div class="flex flex-col w-full border border-0" >
                            <form  action="{{ route('guests.staff_updates.store_auth_credentials') }}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center space-y-2">
                                    @csrf

                                    @include('partials._session_response')


                                    <div class="flex flex-col w-[80%] md:w-[80%] py-4 mt-4 font-serif" >
                                        <h2 class="font-semibold text-xl py-1" >Staff Update</h2>
                                        Create your Staff Portal Login Credentials...
                                        
                                    </div>


                                    @if (session('candidate'))
                                        <div class="flex flex-col border-0 w-[80%] py-2 font-base">
                                             Welcome  {{ session('candidate')->names }}
                                        </div>

                                    @endif

                                

                                    

                                    <!-- Email //-->
                                    <div class="w-[80%] py-1">

                                        <input type="email" name="email" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="Email"
                                                                                
                                                                                value="{{ old('email') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                <div class='text-sm px-2'>Preferably FUNAAB email</div>
                                                                                                                                                    

                                                                                @error('email')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of Email //-->



                                    <!-- Password //-->
                                    <div class="w-[80%] py-1">

                                        <input type="password" name="password" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="Password"
                                                                                
                                                                                value="{{ old('password') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                                                                                    

                                                                                @error('password')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of Password //-->


                                    
                                    <!-- Password //-->
                                    <div class="w-[80%] py-1">

                                        <input type="password" name="password_confirmation" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="Confirm Password"
                                                                                
                                                                                value="{{ old('password_confirmation') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                                                                                    

                                                                                @error('password_confirmation')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of Confirm Password //-->



                                   
                                    <!-- submit //-->
                                    <!-- submit button //-->
                                    <div class="flex flex-col border-red-900 w-[80%] md:w-[80%] mt-8 py-1">
                                        <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                                    hover:bg-gray-500
                                                    rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Staff Portal Access</button>
                                    </div>

                                    <!-- end of submit //-->

                                </form>
                            </div>
                        </section>
                
            </div>
            <!-- end of right panel //-->




</section>




</x-guest-layout>