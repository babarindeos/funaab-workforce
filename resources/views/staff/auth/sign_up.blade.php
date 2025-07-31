<x-guest-layout>



<section class="flex border border-0 border-red-900 w-full">
        
            <!-- left side //-->
            <div class="hidden md:flex w-1/5 bg-green-100" 
                style="background-image:url('{{asset('images/goviflow_low.jpg')}}'); 
                background-size: cover; 
                background-repeat: repeat
                background-position: right; background-color:#f1f1f1;"
            >
                
            </div>
            <!-- end of left side //-->



            <!-- Right  panel //-->
            <div class="flex flex-col justify-center w-4/5 max-w-4xl">

                        <section class="flex flex-col w-full border border-0">
                            <div class="flex flex-col w-full border border-0" >
                            <form  action="{{ route('staff.auth.store_signup') }}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center space-y-2">
                                    @csrf


                                    <div class='w-[80%]'>
                                        @include('partials._session_response')
                                    </div>


                                    <div class="flex flex-col w-[80%] md:w-[80%] py-4 mt-4 font-serif" >
                                        <h2 class="font-semibold text-xl py-1" >Sign Up</h2>
                                        Provide your Staff information...
                                        
                                    </div>

                                    

                                    <!-- Staff No. //-->
                                    <div class="w-[80%] py-1">

                                        <input type="text" name="staff_no" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="Staff No."
                                                                                
                                                                                value="{{ old('staff_no') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                                                                                    

                                                                                @error('staff_no')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of Staff No. //-->


                                    <!-- IPPIS No.. //-->
                                    <div class="w-[80%] py-1">

                                        <input type="text" name="email" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="FUNAAB Email"
                                                                                
                                                                                value="{{ old('email') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                                                                                    

                                                                                @error('email')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of IPPIS No. //-->



                                    
                                    <!-- submit //-->
                                    <!-- submit button //-->
                                    <div class="flex flex-col border-red-900 w-[80%] md:w-[80%] mt-8 py-1">
                                        <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                                    hover:bg-gray-500
                                                    rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Submit</button>
                                    </div>

                                    <!-- end of submit //-->

                                </form>
                            </div>
                        </section>
                
            </div>
            <!-- end of right panel //-->




</section>




</x-guest-layout>