<x-admin-layout>

    <div class="flex flex-col border-0 w-[89%] md:w-[93%] mx-auto">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 mt-6">
                <div class="text-2xl font-semibold ">
                    Staff Category         
                </div>

                <div>

                            <a href="{{ route('admin.staff_categories.index') }}" class="border border-green-600 text-green-600 py-2 px-6 
                                            rounded-lg text-xs md:text-sm hover:bg-green-600 hover:text-white hover:border-green-600">Staff Categories</a>
                </div>
                
        </section>

       


        <!-- Create Staff Category Section  //-->
        
       
    
        <section class="py-8 mt-2">
                <div>
                    <form  action="{{ route('admin.staff_categories.update',['staff_category' => $staff_category->id]) }} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                        @csrf
    
                        
    
                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Edit Staff Category</h2>
                            
                        </div>
    
                        <div class="flex flex-col w-[80%] md:w-[60%]" >
                                @include('partials._session_response')
                        </div>
                        

                        <!-- Parent Staff Category Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <select name="parent_staff_category" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Parent Staff Category"
                                                                    
                                                                    
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    
                                                                    /> 
                                            <option value=''>-- Select Parent Staff Category --</option>
                                            @foreach($staff_categories as $parent)
                                                <option value="{{ $parent->id }}" @if ($parent->id == $staff_category->parent_staff_category) selected @endif>{{ $parent->category }}</option>
                                            @endforeach
                                                                    
                                                                    
                            </select>
                                                                                                                                        
    
                                                                    @error('parent_staff_category')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Staff Category Name //-->      



                        
                        
    
                        <!-- Category Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="category" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Category"
                                                                    
                                                                    value="{{ $staff_category->category }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('category')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Category Name //-->      
                        
                        

                        
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Staff Category</button>
                        </div>
                        
                    </form><!-- end of Staff Category form //-->
                <div>
    
            

        </section>
        <!-- End of Staff Category Section //-->



    </div>

</x-admin-layout>