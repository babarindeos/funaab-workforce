<x-admin-layout>



<div class="flex flex-col w-[95%] mx-4 border border-0 mx-auto">
    <section class="flex flex-col border-b border-gray-200 py-2 mt-2 md:flex-row md:justify-between">
            <div>
                    <div class="text-2xl font-semibold mt-2 ">
                        Staff Information               
                    </div>
                    
            </div>
            
    </section>




    <section class='flex flex-col md:flex-row py-6'>
        <div class='flex flex-col md:flex-row w-full md:w-[80%]'>
                <div class='flex flex-col w-full justify-start md:w-2/5 md:justify-start items-center border-0'> 
                    <img class='w-64 h-64 rounded-full' src="{{ asset('images/avatar_64.jpg') }}" alt="Default Avatar" />
                     <div class="text-xl font-semibold py-1 mt-2">
                        {{ $identity->title }} {{ $identity->names }}  
                                     
                    </div>
                     <div class='text-lg'>{{ $identity->current_designation }}</div>
                    
                    <div> ({{ $identity->fileno }}) </div>

                </div>
                <div class='w-4/5 px-8 py-4 md:py-0 '> 
                    
                    <div class='border-b py-1 md:py-0'>               
                        <div class='text-sm py-1 font-semibold mt-0'>IPPIS No.</div>
                        <div>{{ $identity->ippis_no }}</div>

                    </div>

                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>College</div>
                        <div>{{ $identity->college }}</div>

                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Department</div>
                        <div>{{ $identity->department }}</div>

                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Date of Birth</div>
                        <div>{{ Carbon\Carbon::parse($identity->dob)->format('l jS F, Y') }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Gender</div>
                        <div>{{ $identity->gender == 'M' ? "Male" : "Female" }}</div>

                    </div>

                    

                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Date of First Appointment in FUNAAB</div>
                        <div>{{ Carbon\Carbon::parse($identity->date_first_appointment_funaab)->format('l jS F, Y') }}</div>

                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Date of Confirmation</div>
                        <div>{{ Carbon\Carbon::parse($identity->date_confirmation)->format('l jS F, Y') }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Current Designation</div>
                        <div>{{ $identity->current_designation }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Date of Present Appointment</div>
                        <div>{{ Carbon\Carbon::parse($identity->date_present_appointment)->format('l jS F, Y') }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Level/Step</div>
                        <div>{{ $identity->salary_level_step }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Cadre</div>
                        <div>{{ $identity->cadre }}</div>
                    </div>

                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Next of Kin</div>
                        <div>{{ $identity->nok }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Phone</div>
                        <div>{{ $identity->phone }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Email</div>
                        <div>{{ $identity->email }}</div>
                    </div>


                    <div class='border-b py-1'>               
                        <div class='text-sm py-1 font-semibold mt-4'>Status</div>
                        <div>{{ $identity->staff_status }}</div>
                    </div>




                </div>
        </div>


    </section>



</div>



</x-admin-layout>