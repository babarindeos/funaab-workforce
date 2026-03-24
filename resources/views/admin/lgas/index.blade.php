<x-admin-layout>

    <div class="flex flex-col border-0 w-[95%]  mx-auto">
        

        <!-- page header //-->
        <section class="flex flex-col w-full md:w-full py-6 px-2 md:px-4 border-0 border-red-900 mx-auto border border-1">
        
            <div class="flex border-b border-gray-300 py-2 justify-between">
                    <div class='flex'>
                            <h1 class="text-2xl font-semibold font-serif text-gray-800">LGAs</h1>
                        
                    </div>
                    

                    
            </div>
        </section>
        <!-- end of page header //-->

        


        <!-- Geo Lgas Section  //-->
        

        
        <section class="flex flex-col w-[95%] md:w-[60%] mx-auto py-2 mt-2 border-0">
                   


        @if ($lgas->count())
                    <div class='flex flex-col w-full md:flex-row md:items-center'>
                        <div class='py-1 text-xl font-medium md:w-1/2'>LGAs ({{ $lgas->total() }})</div>

                        <!-- Search //-->
                        <div class='flex flex-col gap-2 w-full py-2 md:py-2 md:w-1/2 md:justify-end md:items-end border-0'>
                                <form action="{{ route('admin.lgas.index') }}" method="GET" class="flex flex-row items-center w-full border-0">
                                
                                                <div class="flex flex-col justify-end items-end border-0 w-full">
                                                
                                                        <input type="text" name="q" class="w-full md:w-4/5 border border-1 border-gray-400 bg-gray-50
                                                                p-2 rounded-l-md 
                                                                focus:outline-none
                                                                focus:border-blue-500 
                                                                focus:ring
                                                                focus:ring-blue-100" placeholder="Search..."                
                                                                
                                                                style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                  
                                                        
                                                        />  
                                                </div>
                                                <div class='border-0'>
                                                        
                                                        <button type='submit' class="hover:bg-blue-500 hover:border-blue-500 bg-blue-400 text-white rounded-r-md 
                                                                px-4 py-2 text-md border-blue-400 border-0" ><i class="fa-solid fa-magnifying-glass"></i></button>
                                                        
                                                </div>
                                                
                                        
                                </form>
                        </div>
                        <!-- end of Search //-->
                    </div>
                    <div >
                        {{ $lgas->links() }}
                    </div>
                   

                    <table class="table-auto border-collapse border border-1 border-gray-200 w-[100%] mt-2">
                        <thead>
                            <tr class="bg-gray-200">
                                <th width='20%' class="text-center font-semibold py-4 w-16">#</th>
                                <th width='55%' class="font-semibold py-2 text-left">LGA</th>
                                <th width='25%' class="font-semibold py-2 text-left">State</th>   
                                                                            
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach($lgas as $lga)
                            <tr class="border border-b border-gray-200 ">
                                <td class='text-center py-8'>{{ ++$counter }}.</td>
                                <td class="py-4 pr-4">
                                    
                                    <div class='py-1'>
                                        
                                            <a class="font-semibold text-blue-800 underline" href="{{ route('admin.lgas.show',['lga'=>$lga->id]) }}">
                                                {{ $lga->lga_name }}
                                            </a>                            
                                    </div>
                                    <div class='flex flex-wrap md:flex-row gap-3 md:gap-4 text-sm'>
                                            <a href="#" class='hover:underline'>Staff (0)</a>            
                                           
                                    </div>                     
                                </td>
                                <td>
                                            {{ $lga->state->state_name }}    
                                </td>                      
                                
                            </tr>
                            @endforeach
                        

                        </tbody>
                    </table>
                     <div class='py-2' >
                        {{ $lgas->links() }}
                    </div>

                   

            @else
                    <section class="flex flex-col w-[95%] md:w-[95%] mx-auto px-0 py-8 border-0">
                        <div class="flex flex-row border-0 justify-center text-2xl font-semibold text-gray-300">
                                There is currently no LGAs
                        </div>
                    </section>
            @endif



        </section>
       
        <!-- end of LGA Section //-->
    
        
    
    
        
    </div>
    


</x-admin-layout>