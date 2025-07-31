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

            <!-- second side //-->
            <div class="flex flex-col justify-center w-4/5 max-w-4xl mx-auto">
                
                <div class="border border-0">
                    <form  action="{{ route('identity.auth.login') }}" method="POST" class="flex flex-col mx-auto w-[80%] items-center justify-center md:items-start">
                        @csrf

                        <div class="flex flex-col w-[80%] md:w-[60%] py-4 mt-4 font-serif" >
                            <h2 class="font-semibold text-xl py-1" >Sign In</h2>
                            Staff Identity Management
                            
                        </div>

                        {{-- @if ($errors->has('email'))
                            {{ $errors->first('email')}}
                        @endif --}}

                        <!-- Session Status -->
                        @if (session('error'))

                                    @if (session('status')=='success')
                                        <span class="flex flex-col w-[80%] md:w-[60%] py-4 px-2 my-2 bg-green-50 rounded text-green-800 font-medium" 
                                                style="font-family:'Lato'; font-size:16px;"> 
                                            {{ session('message') }}
                                        </span>
                                    @else
                                        <span class="flex flex-col w-[80%] md:w-[60%] py-4 px-2 my-2 bg-red-50 rounded text-red-800 font-medium" 
                                                style="font-family:'Lato'; font-size:16px;">
                                            {{ session('message') }}
                                        </span>
                                    @endif

                        @endif
                        


                        <!-- EMail //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                            <!--<label for="email" class="font-semibold text-gray-700">Email</label> //-->
                            
                            <input type="text" name="email" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Email"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                    
                                                                    value="{{ old('email')}}"
                                                                    />                                                                         

                                                                    @error('email')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div>
                        <!-- end of Email //-->


                         <!-- Password //-->
                         <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                            <!--<label for="password" class="font-semibold text-gray-700">Password</label> //-->
                            
                            <input type="password" name="password" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Password"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"
                                                                    
                                                                    
                                                                    />                                                                         

                                                                    @error('password')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div>
                        <!-- end of Password //-->

                        <!-- submit button //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-2">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                        hover:bg-gray-500
                                        rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Sign In</button>
                        </div>



                    </form>
                </div>
            </div>
            <!-- end of second side //-->

    </section>



</x-guest-layout>