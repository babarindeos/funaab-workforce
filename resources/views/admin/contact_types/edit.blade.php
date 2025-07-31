<x-admin-layout>

    <div class="flex flex-col border-0 w-[89%] md:w-[93%] mx-auto">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 mt-6">
                <div class="text-2xl font-semibold ">
                    Contact Types           
                </div>

                <div>

                            <a href="{{ route('admin.contact_types.index') }}" class="border border-green-600 text-green-600 py-2 px-6 
                                            rounded-lg text-xs md:text-sm hover:bg-green-600 hover:text-white hover:border-green-600">Contact Types</a>
                </div>
                
        </section>

       


        <!-- Create Contact Type Section  //-->
        
       
    
        <section class="py-8 mt-2">
                <div>
                    <form  action="{{ route('admin.contact_types.update',['contact_type' => $contact_type->id]) }} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                        @csrf
    
                        
    
                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Edit Contact Type</h2>
                            
                        </div>
    
                        <div class="flex flex-col w-[80%] md:w-[60%]" >
                                @include('partials._session_response')
                        </div>
                        
    
                        <!-- Contact Type Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Contact Type Name"
                                                                    
                                                                    value="{{ $contact_type->contact_type_name }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('name')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Contact Type Name //-->                        
                        
                        
                       
    
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Update Contact Type</button>
                        </div>
                        
                    </form><!-- end of Contact Type form //-->
                <div>
    
            

        </section>
        <!-- End of Contact Type Section //-->



    </div>

</x-admin-layout>