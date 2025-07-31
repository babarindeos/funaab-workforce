<x-identity-layout>


<div class="flex flex-col w-[95%] mx-4 border border-0 mx-auto">
    <section class="flex flex-col border-b border-gray-200 py-2 mt-2 md:flex-row md:justify-between">
            <div>
                    <div class="text-2xl font-semibold mt-2 ">
                        Change Password            
                    </div>
                    
            </div>
            
    </section>


    <section>
            <div>
                    <form  action="{{ route('identity.auth.update_password') }}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center">
                        @csrf

                        

                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Update Password</h2>
                          
                        </div>

                        <div class="flex flex-col w-[80%] md:w-[60%]">
                                @include('partials._session_response')
                        </div>


                        <!-- Current Password //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                                
                                
                             <input type="password" name="current_password" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Current Password"
                                                                    
                                                                    value="{{ old('current_password') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('current_password')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div>
                        
                        <!-- end of Current Password //-->

                        
                        

                        <!-- New Password //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="password" name="new_password" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="New Password"
                                                                    
                                                                    value="{{ old('new_password') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('new_password')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of department name //-->

                        <!-- Confirm Password //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="password" name="new_password_confirmation" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Confirm Password"
                                                                    
                                                                    value="{{ old('new_password_confirmation') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        

                                                                    @error('new_password_confirmation')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Confirm Password //-->

                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Change Password</button>
                        </div>
                        
                    </form><!-- end of change password form //-->
                <div>


    </section>



</div>



</x-identity-layout>