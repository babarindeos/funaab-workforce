<x-guest-layout>
<div class="flex flex-col flex-1 w-full border-0">

    <div class="flex flex-col md:flex-row h-full">
            <!-- left  panel //-->
            <div class="flex w-full h-[40%] md:h-[40%] md:w-[70%]">
                    <div class="flex flex-col border-0 py-16 w-full h-[100%] md:w-[100%]"
                        style="background-image:url('{{asset('images/header.jpg')}}'); 
                            background-size: cover; 
                            background-repeat: no-repeat;
                            background-position: center; background-color:#f1f1f1;"
                    >
                            <!-- <img src="{{ asset('images/goviflow_low.jpg') }}" /> //-->
                    </div>
                    <!-- end of left panel //-->
            </div>



            <!-- Right  panel //-->
            <div class="flex flex-col w-full  md:w-[30%] items-center justify-center py-4 border-l">

                <a href="{{ route('guest.staff_updates.login') }}" class='text-2xl underline hidden'>Fill the Staff Update Form</a>

                <section class="flex flex-col w-full border border-0">
                    <div class="flex flex-col w-full border border-0" >
                    <form  action="{{ route('staff.auth.login') }}" method="POST" class="flex flex-col mx-auto w-[90%] items-center justify-center space-y-2">
                            @csrf

                            <div class="flex flex-col w-[80%] md:w-[80%] py-4 mt-4 font-serif" >
                                <h2 class="font-semibold text-xl py-1" >Sign In</h2>
                                Staff Members Only 
                                
                            </div>

                            <!-- username //-->
                            <div class="w-[80%] py-1">

                                <input type="text" name="email" class="border border-1 border-gray-400 bg-gray-50
                                                                        w-full p-4 rounded-md 
                                                                        focus:outline-none
                                                                        focus:border-blue-500 
                                                                        focus:ring
                                                                        focus:ring-blue-100" placeholder="Email"
                                                                        
                                                                        value="{{ old('email') }}"
                                                                        
                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                        required
                                                                        />  
                                                                                                                                            

                                                                        @error('email')
                                                                            <span class="text-red-700 text-sm">
                                                                                {{$message}}
                                                                            </span>
                                                                        @enderror
                                
                            </div><!-- end of username //-->

                            <!-- password //-->
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

                            </div><!-- end of password //-->

                            <!-- submit //-->
                            <!-- submit button //-->
                            <div class="flex flex-col border-red-900 w-[80%] md:w-[80%] mt-8 py-1">
                                <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                               hover:bg-gray-500
                                               rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Login</button>
                            </div>

                            <!-- end of submit //-->

                             <div class='flex flex-row border-0 w-[80%] justify-end items-center space-x-5'>                                 
                                <a href="#" class='flex flex-row text-sm border-0 hover:underline'>Forgot Password</a>
                            </div>

                        </form>
                    </div>
                </section>
                    
            </div>
            <!-- end of right panel //-->
    </div>


</div>
</x-guest-layout>