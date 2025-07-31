<x-guest-layout>


<section class="flex flex-col w-full md:w-1/2 border-0 mx-auto justify-center">

            <!-- Right  panel //-->
            <div class="flex flex-col w-full md:w-[100%] items-center justify-center py-4">

                        <section class="flex flex-col w-full border border-0">
                            <div class="flex flex-col w-full border border-0" >
                            <form  action="{{ route('guest.staff_updates.login_auth') }}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center space-y-2">
                                    @csrf

                                    @include('partials._session_response')


                                    <div class="flex flex-col w-[80%] md:w-[80%] py-4 mt-4 font-serif" >
                                        <h2 class="font-semibold text-xl py-1" >Staff Update</h2>
                                        Login to update your information...
                                        
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

                                        <input type="text" name="ippis_no" class="border border-1 border-gray-400 bg-gray-50
                                                                                w-full p-4 rounded-md 
                                                                                focus:outline-none
                                                                                focus:border-blue-500 
                                                                                focus:ring
                                                                                focus:ring-blue-100" placeholder="IPPIS No."
                                                                                
                                                                                value="{{ old('ippis_no') }}"
                                                                                
                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                required
                                                                                />  
                                                                                                                                                    

                                                                                @error('ippis_no')
                                                                                    <span class="text-red-700 text-sm">
                                                                                        {{$message}}
                                                                                    </span>
                                                                                @enderror
                                        
                                    </div><!-- end of IPPIS No. //-->



                                    <!-- Date of Birth //-->
                                    <div class="w-[80%] py-1">

                                        <input type="date" name="dob" class="border border-1 border-gray-400 bg-gray-50
                                            w-full p-4 rounded-md 
                                            focus:outline-none
                                            focus:border-blue-500 
                                            focus:ring
                                            focus:ring-blue-100" placeholder="Date of Birth"
                                            
                                            value="{{ old('dob') }}"
                                            
                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                            required
                                            />  
                                            <div class='text-sm px-2'>Date of Birth</div>
                                                                                                                

                                            @error('dob')
                                                <span class="text-red-700 text-sm">
                                                    {{$message}}
                                                </span>
                                            @enderror

                                    </div><!-- end of Date of Birth //-->

                                    <!-- submit //-->
                                    <!-- submit button //-->
                                    <div class="flex flex-col border-red-900 w-[80%] md:w-[80%] mt-8 py-1">
                                        <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                                    hover:bg-gray-500
                                                    rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Login</button>
                                    </div>

                                    <!-- end of submit //-->

                                </form>
                            </div>
                        </section>
                
            </div>
            <!-- end of right panel //-->




</section>




</x-guest-layout>