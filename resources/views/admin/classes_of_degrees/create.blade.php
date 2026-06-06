<x-admin-layout>

    <div class="flex flex-col border-0 w-[89%] md:w-[93%] mx-auto">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 mt-6">
                <div class="text-2xl font-semibold ">
                    Classes of Degrees       
                </div>

                <div>

                            <a href="{{ route('admin.classes_of_degrees.index') }}" class="border border-green-600 text-green-600 py-2 px-6 
                                            rounded-lg text-xs md:text-sm hover:bg-green-600 hover:text-white hover:border-green-600">Classes of Degrees</a>
                </div>
                
        </section>

       


        <!-- Create Degrees Section  //-->
        
       
    
        <section class="py-8 mt-2">
                <div>
                    <form  action="{{ route('admin.classes_of_degrees.store') }} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                        @csrf
    
                        
    
                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Create Class of Degree</h2>
                            
                        </div>
    
                        <div class="flex flex-col w-[80%] md:w-[60%]" >
                                @include('partials._session_response')
                        </div>
                        

                        
                        
                        
    
                        <!-- Degree //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="degree_class_name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Class of Degree"
                                                                    
                                                                    value="{{ old('degree_class_name') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('degree_class_name')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Degree //-->      


                        <!-- Order Level //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="order_level" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Order Level"
                                                                    
                                                                    value="{{ old('order_level') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('order_level')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Order Level //-->      
                        
                        

                        
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Class of Degree</button>
                        </div>
                        
                    </form><!-- end of Degree form //-->
                <div>
    
            

        </section>
        <!-- End of Degree Section //-->



    </div>

</x-admin-layout>