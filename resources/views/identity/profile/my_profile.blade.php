<x-identity-layout>



<div class="flex flex-col w-[95%] mx-4 border border-0 mx-auto">
    <section class="flex flex-col border-b border-gray-200 py-2 mt-2 md:flex-row md:justify-between">
            <div>
                    <div class="text-2xl font-semibold mt-2 ">
                        My Profile             
                    </div>
                    
            </div>
            
    </section>




    <section class='flex flex-col md:flex-row py-6'>
        <div class='flex flex-col md:flex-row w-full md:w-[80%]'>
                <div class='flex flex-col w-full justify-start md:w-2/5 md:justify-start items-center border-0'> 
                    <img class='w-64 h-64 rounded-full' src="{{ asset('images/avatar_64.jpg') }}" alt="Default Avatar" />
                     <div class="text-xl font-semibold py-1 mt-2">
                        {{ Auth::user()->surname }} {{ Auth::user()->firstname }}  {{ Auth::user()->middlename }} 
                                     
                    </div>
                    
                    
                    <div> ({{ Auth::user()->fileno }}) </div>

                </div>
                <div class='w-4/5 px-8 py-4 md:py-0 '> 
                    

                    <div class='border-b py-1 md:py-0'>               
                        <div class='text-sm py-1 font-semibold mt-0'>Email</div>
                        <div>{{ Auth::user()->email }}</div>

                    </div>


                   


                    


                </div>
        </div>


    </section>



</div>



</x-identity-layout>