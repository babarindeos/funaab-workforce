<x-guest-layout>


<section class="flex flex-col w-[93%] border-0 mx-auto mb-8 " >

    <div class="flex flex-col md:flex-row">
        <div class="flex flex-col items-center justify-start border-0 py-8 md:w-1/5">
             @if(session('candidate_photo'))
                        @php
                            $avatar = session('candidate_photo');
                          
                        @endphp
                        <img src="{{ asset('storage/'.$avatar) }}" class="w-48 h-48 rounded-full" /> 
             @else if 

                        <img src="{{ asset('images/avatar_150.jpg')}}" class="w-150" />
             @endif

             @if (session('candidate'))
                <div class="flex flex-col py-3 items-center">
                        <div class='text-xl font-semibold'>                        
                                {{ session('candidate')->names }}
                            
                        </div>
                        <div class='text-base'>
                            
                                {{ session('candidate')->current_designation }}
                            
                        </div>
                </div>
             @endif
        </div>

        <form  action="{{ route('guests.staff_updates.store_update_form') }}" method="POST" class="w-full md:w-4/5">
            @csrf
        <div class="flex flex-col w-full md:w-5/5 md:px-8"><!-- form //-->
                <section class='w-full border-0 text-right py-4'>
                    <div>
                        <a class='hover:underline' href="{{ route('welcome') }}">Sign Out</a>
                    </div>
                </section>


                @include('partials._session_response')


            
                <div class="text-2xl py-2 font-semibold border-b" style="cursor:pointer;" id="personal_details_header">
                    A. Personal Details
                </div>


                <div id="personal_details_panel"><!-- Personal_details_pane //-->
                                    <!-- names //-->
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 ">
                                                    @php
                                                      
                                                        $names = explode(", ", session('candidate')->names);


                                                        $surname = $names[0];
                                                        $restParts = trim($names[1]);

                                                        $restParts = explode(" ", $names[1]);

                                                        $firstname = $restParts[0];

                                                        $othernames = isset($restParts[1]) ? $restParts[1] : null;


                                                        
                                                        

                                                    @endphp

                                                    <!-- Surname //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                    
                                                                    <div class='text-sm font-semibold px-1'>Surname</div>
                                                                    <input type="text" name="surname" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Surname"
                                                                                                            
                                                                                                            value="{{ $surname }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  
                                                                                                            <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                                

                                                                                                            @error('surname')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of surname //-->


                                                    <!-- Firstname  //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                    
                                                                    <div class='text-sm font-semibold px-1'>Firstname</div>
                                                                    <input type="text" name="firstname" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Firstname"
                                                                                                            
                                                                                                            value="{{ $firstname }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  
                                                                                                            <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                                

                                                                                                            @error('firstname')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    

                                                    </div><!-- end of firstname //-->


                                                    <!-- Othernames  //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                    
                                                                    <div class='text-sm font-semibold px-1' >Othernames</div>
                                                                    <input type="text" name="othernames" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Othernames"
                                                                                                            
                                                                                                            value="{{ $othernames }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  
                                                                                                            <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                                

                                                                                                            @error('othernames')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Othernames //-->


                                    </div>
                                    <!-- end of names //-->





                                    
                                    <!-- Have changed name //-->
                                    <div class="flex flex-col border-0 border-red-900 py-8" >
                                                    <div class="font-semibold">Have you changed your names at any time? <span class='text-red-800 text-lg overline'>*</span></div>

                                                    <!-- radio buttons //-->
                                                    <div class="flex flex-row space-x-4">

                                                            <!-- Have changed name -- Yes option //-->
                                                            <div class="flex flex-row border-red-900 w-[10%] md:w-[10%] py-3">
                                                                        
                                                                            
                                                                        <input type="radio" name="name_change" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                p-4 rounded-md 
                                                                                                                focus:outline-none
                                                                                                                focus:border-blue-500 
                                                                                                                focus:ring
                                                                                                                focus:ring-blue-100" placeholder=""
                                                                                                                
                                                                                                                value="yes"
                                                                                                                
                                                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                                required
                                                                                                                /> 
                                                                                                                <div class='flex flex-col justify-center px-2'>Yes</div> 
                                                                                                                
                                                                                                                                                                                    

                                                                                                                @error('name_change')
                                                                                                                    <span class="text-red-700 text-sm">
                                                                                                                        {{$message}}
                                                                                                                    </span>
                                                                                                                @enderror
                                                                        
                                                            </div><!-- end of have changed name //-->
                                                            


                                                            <!--  Radio button - No option //-->
                                                            <div class="flex flex-row border-red-900 w-[10%] md:w-[10%] py-3">
                                                                        
                                                                            
                                                                        <input type="radio" name="name_change" class="border border-1 border-gray-400 bg-gray-50
                                                                                                                p-4 rounded-md 
                                                                                                                focus:outline-none
                                                                                                                focus:border-blue-500 
                                                                                                                focus:ring
                                                                                                                focus:ring-blue-100" placeholder="Department or Agency full name"
                                                                                                                
                                                                                                                value="no"
                                                                                                                
                                                                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                                required
                                                                                                                /> 
                                                                                                                <div class='flex flex-col justify-center px-2'>No</div>  
                                                                                                                                                                                    

                                                                                                                @error('name_change')
                                                                                                                    <span class="text-red-700 text-sm">
                                                                                                                        {{$message}}
                                                                                                                    </span>
                                                                                                                @enderror
                                                                        
                                                            </div><!-- end of have changed name -- No option //-->
                                                    </div>
                                                    <!-- end of radio button //-->
                                    </div>
                                    <!-- end of change name //-->





                                    <!-- Yes have changed names //-->
                                    <!-- names //-->
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 " id='name_change_panel' style="display:none">
                                                
                                                    <!-- Date of name Change //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[40%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>When did you change your names <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="date" name="date_name_change" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Surname"
                                                                                                            
                                                                                                            value="{{ old('date_name_change') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            
                                                                                                            />  

                                                                                                                                                                                

                                                                                                            @error('date_name_change')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of date of name change //-->


                                                    <!-- Reason of reason change  //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>State reason(s) for name change <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="text" name="reason_name_change" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Reason for change of name"
                                                                                                            
                                                                                                            value="{{ old('reason_name_change') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                           
                                                                                                            />  
                                                                                                                                                                                

                                                                                                            @error('reason_name_change')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    

                                                    </div><!-- end of reason for name change //-->


                                                    <!-- Indicate your original names in full (Surname First)  //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1' >Indicate your original names in full (Surname First) <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="text" name="original_names"  class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Original names"
                                                                                                            
                                                                                                            value="{{ old('original_names') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            
                                                                                                            />  
                                                                                                                                                                                

                                                                                                            @error('original_names')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Original names //-->


                                    </div>
                                    <!-- end of Yes have changed names //-->



                                    <!-- Birth  //-->
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                
                                                <!-- Date of Birth //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[40%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Date of Birth</div>
                                                                <input type="text" name="dob" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Date of Birth"
                                                                                                        
                                                                                                        value="{{ \Carbon\Carbon::parse(session('candidate')->dob)->format('jS F, Y') }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                            

                                                                                                        @error('dob')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                
                                                </div><!-- end of dob //-->


                                                <!-- Place of Birth  //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Place of Birth <span class='text-red-800 overline'>*</span></div>
                                                                <input type="text" name="birth_place" class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Place of Birth"
                                                                                                        
                                                                                                        value="{{ old('birth_place') }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        
                                                                                                                                                                            

                                                                                                        @error('birth_place')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                

                                                </div><!-- end of Place of Birth //-->
                        

                                </div>
                                <!-- end of Birth //-->

                                    
                                    <!-- Marital Status and Country //-->               
                                
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                
                                                <!-- Marital Status //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[40%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Marital Status</div>
                                                                <input type="text" name="marital_status" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Marital Status"
                                                                                                        
                                                                                                        value="{{ session('candidate')->marital_status }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                            

                                                                                                        @error('marital_status')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                
                                                </div><!-- end of dob //-->


                                                <!-- Nationlity  //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Nationality <span class='text-red-800 overline'>*</span></div>
                                                                <input type="text" name="nationality" class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Nationality"
                                                                                                        
                                                                                                        value="{{ old('nationality') }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                                                                                            

                                                                                                        @error('nationality')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                

                                                </div><!-- end of Nationality //-->
                        

                                </div>
                                <!-- end of Marital Status and Country  //-->



                                
                                    
                                    <!-- State and LGA //-->               
                                
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                
                                                <!-- State //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[40%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>State</div>
                                                                <input type="text" name="state" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="state"
                                                                                                        
                                                                                                        value="{{ session('candidate')->state }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>


                                                                                                                                                                            

                                                                                                        @error('state')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                
                                                </div><!-- end of state //-->


                                                <!-- LGA  //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Local Government Area (LGA)</div>
                                                                <input type="text" name="lga" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="lga"
                                                                                                        
                                                                                                        value="{{ session('candidate')->lga }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                            

                                                                                                        @error('lga')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                

                                                </div><!-- end of LGA //-->
                        

                                </div>
                                <!-- end of State and LGA  //-->



                                <!-- Date of Present Appointment & designation //-->               
                                
                                <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                
                                                <!-- Date of Present Appointment //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[40%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Date of Present Appointment</div>
                                                                <input type="text" name="date_present_appointment" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="date_present_appointment"
                                                                                                        
                                                                                                        value="{{ \Carbon\Carbon::parse(session('candidate')->date_present_appointment)->format('jS F, Y') }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>


                                                                                                                                                                            

                                                                                                        @error('date_present_appointment')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                
                                                </div><!-- end of Date of Present Appointment //-->


                                                <!-- Designation on  Appointment  //-->
                                                <div class="flex flex-col border-0 border-red-900 w-[100%] md:w-[60%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Designation on Appointment</div>
                                                                <input type="text" name="present_appointment_designation" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="lga"
                                                                                                        
                                                                                                        value="{{ session('candidate')->current_designation }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                            

                                                                                                        @error('present_appointment_designation')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                

                                                </div><!-- end of  present_appointment_designation //-->




                                                <!-- Department //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[60%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Department</div>
                                                                <input type="text" name="department" disabled class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Department"
                                                                                                        
                                                                                                        value="{{ session('candidate')->department }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  
                                                                                                        <div class='text-xs px-1'>Read-only</div>

                                                                                                                                                                            

                                                                                                        @error('department')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                

                                                </div><!-- end of Department //-->

                                </div>
                                <!-- End Date of Present Appointment & designation //--> 
                                    
                                

                                <!-- Permanent Home Address //-->               
                                
                                <div class="flex flex-col md:flex-row md:space-x-4 border border-0 py-4 ">
                                                
                                                <!-- Date of Permanent Home Address //-->
                                                <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] py-3">
                                                
                                                                <div class='text-base font-semibold px-1'>Permanent Home Address <span class='text-red-800 text-lg overline'>*</span></div>
                                                                <input type="text" name="permanent_home_address" class="border border-1 border-gray-400 bg-gray-50
                                                                                                        w-full p-4 rounded-md 
                                                                                                        focus:outline-none
                                                                                                        focus:border-blue-500 
                                                                                                        focus:ring
                                                                                                        focus:ring-blue-100" placeholder="Permanent Home Address "
                                                                                                        
                                                                                                        value="{{ old('permanent_home_address') }}"
                                                                                                        
                                                                                                        style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                        required
                                                                                                        />  

                                                                                                                                                                            

                                                                                                        @error('permanent_home_address')
                                                                                                            <span class="text-red-700 text-sm">
                                                                                                                {{$message}}
                                                                                                            </span>
                                                                                                        @enderror
                                                                
                                                </div><!-- end of Date of permanent_home_address //-->
                                                    

                                </div>
                                <!-- End Date of Permanent Home Address //-->     


                </div><!-- end of Personal_Details_Pane //-->



               <div class="text-2xl py-2 mt-8 font-semibold border-b" id="nok_details_header" style="cursor:pointer;">
                    B. Details of Next of Kin
                </div>



                <div id='nok_details_panel'><!-- Next of Kin Details Pane //-->
                                        <!-- Fullname of Next of Kin //-->               
                                    
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                    
                                                    <!-- Fullname of Next of Kin //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>Full Name of Next of Kin (Surname First) <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="text" name="nok_fullnames" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Full Name of Next of Kin "
                                                                                                            
                                                                                                            value="{{ old('nok_fullnames') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  

                                                                                                                                                                                

                                                                                                            @error('nok_fullnames')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Date of Next of Kin //-->
                                                        

                                    </div>
                                    <!-- End Date of Fullname of Next of Kin//-->     


                                    <!-- Address of Next of Kin //-->               
                                    
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                    
                                                    <!-- Address of Next of Kin //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>Address of Next of Kin  <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="text" name="nok_address" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Address of Next of Kin "
                                                                                                            
                                                                                                            value="{{ old('nok_address') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  

                                                                                                                                                                                

                                                                                                            @error('nok_address')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Address of Next of Kin //-->
                                                        

                                    </div>
                                    <!-- End Date of Address of Next of Kin//-->     



                                    <!-- Phone of Next of Kin //-->               
                                    
                                    <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                    
                                                    <!-- Phone of Next of Kin //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>Phone No. of Next of Kin  <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="phone" name="nok_phone" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Phone No. of Next of Kin "
                                                                                                            
                                                                                                            value="{{ old('nok_phone') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  

                                                                                                                                                                                

                                                                                                            @error('nok_phone')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Address of Next of Kin //-->
                                                        

                                    </div>
                                    <!-- End Date of Phone No. of Next of Kin//-->     



                                        <!-- Email of Next of Kin //-->               
                                    
                                        <div class="flex flex-col md:flex-row md:space-x-4 border-0 py-4 ">
                                                    
                                                    <!-- Email of Next of Kin //-->
                                                    <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] py-3">
                                                    
                                                                    <div class='text-base font-semibold px-1'>Email of Next of Kin  <span class='text-red-800 text-lg overline'>*</span></div>
                                                                    <input type="email" name="nok_email" class="border border-1 border-gray-400 bg-gray-50
                                                                                                            w-full p-4 rounded-md 
                                                                                                            focus:outline-none
                                                                                                            focus:border-blue-500 
                                                                                                            focus:ring
                                                                                                            focus:ring-blue-100" placeholder="Email of Next of Kin "
                                                                                                            
                                                                                                            value="{{ old('nok_email') }}"
                                                                                                            
                                                                                                            style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                                                            required
                                                                                                            />  

                                                                                                                                                                                

                                                                                                            @error('nok_email')
                                                                                                                <span class="text-red-700 text-sm">
                                                                                                                    {{$message}}
                                                                                                                </span>
                                                                                                            @enderror
                                                                    
                                                    </div><!-- end of Email of Next of Kin //-->
                                                        

                                    </div>
                                    <!-- End of Email of Next of Kin//-->  



                </div><!-- end of Next of Kin Details //-->
                                        
                                    

                 <!-- submit button //-->
                 <div class="flex flex-col border-red-900 w-[100%] md:w-[100%] mt-8 py-1 " id='btn_submit' >
                                        <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                                    hover:bg-gray-500
                                                    rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Submit Update Form</button>
                </div>

                <!-- end of submit //-->









        </div><!-- form //-->

    </div>



</section>





</x-guest-layout>

<script>
$(document).ready(function(){
    $("#personal_details_header").bind('click', function(){
          $('#personal_details_panel').toggle();

         
    });

    $("#nok_details_header").bind('click', function(){
          $('#nok_details_panel').toggle();

          $("#btn_submit").show();
    });

    $('input[name="name_change"]').on('click', function(){
            const selectedValue = $(this).val();

            if (selectedValue === 'yes')
            {
                $("#name_change_panel").show();
            }
            else
            {
                $("#name_change_panel").hide();
            }
    });


});

</script>