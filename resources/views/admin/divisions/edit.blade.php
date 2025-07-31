<x-admin-layout>

    <div class="flex flex-col border-0 w-[89%] md:w-[93%] mx-auto">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 mt-6">
                <div class="text-2xl font-semibold ">
                    Division           
                </div>

                <div>

                            <a href="{{ route('admin.divisions.index') }}" class="border border-green-600 text-green-600 py-2 px-6 
                                            rounded-lg text-xs md:text-sm hover:bg-green-600 hover:text-white hover:border-green-600">Divisions</a>
                </div>
                
        </section>

       


        <!-- Create Division Section  //-->
        
       
    
        <section class="py-8 mt-2">
                <div>
                    <form  action="{{ route('admin.divisions.update',['division' => $division->id ]) }} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                        @csrf
    
                        
    
                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Edit Division</h2>
                            
                        </div>
    
                        <div class="flex flex-col w-[80%] md:w-[60%]" >
                                @include('partials._session_response')
                        </div>
                        

                        <!-- Division Type Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <select name="division_type" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Division Type"
                                                                    
                                                                    
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    /> 
                                            <option value=''>-- Select Division Type --</option>
                                            @foreach($division_types as $division_type)
                                                <option value="{{ $division_type->id }}" @if ($division_type->id == $division->division_type_id) selected  @endif '>{{ $division_type->division_type_name }}</option>
                                            @endforeach
                                                                    
                                                                    
                            </select>
                                                                                                                                        
    
                                                                    @error('division_type')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Division Type Name //-->      



                        <!-- Division Parent Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <select name="parent_division" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Parent Division"
                                                                    
                                                                    
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    
                                                                    /> 
                                            <option value=''>-- Select Parent Division --</option>
                                            @foreach($parent_divisions as $parent)
                                                <option value='{{ $parent->id }}' @if ($parent->id == $division->parent_division) selected @endif>{{ $parent->division_name }}</option>
                                            @endforeach
                                                                    
                                                                    
                            </select>
                                                                                                                                        
    
                                                                    @error('parent_division')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Parent Division Name //-->      
                        
                        
    
                        <!-- Division Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="division_name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Division Name"
                                                                    
                                                                    value="{{ $division->division_name }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('division_name')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Division Name //-->      
                        
                        

                        <!-- Division Short Name //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="short_name" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Division Short Name"
                                                                    
                                                                    value="{{ $division->short_name }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('short_name')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Division Short Name //-->   
                        
                        
                       
    
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Update Division</button>
                        </div>
                        
                    </form><!-- end of Division Type form //-->
                <div>
    
            

        </section>
        <!-- End of Division Type Section //-->



    </div>

</x-admin-layout>