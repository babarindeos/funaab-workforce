<x-admin-layout>

    <div class="flex flex-col border-0 w-[89%] md:w-[93%] mx-auto">
        <section class="flex flex-row justify-between border-b border-gray-200 py-2 mt-6">
                <div class="text-2xl font-semibold ">
                    Deployment Posts         
                </div>

                <div>

                            <a href="{{ route('admin.deployment_posts.index') }}" class="border border-green-600 text-green-600 py-2 px-6 
                                            rounded-lg text-xs md:text-sm hover:bg-green-600 hover:text-white hover:border-green-600">Deployments</a>
                </div>
                
        </section>

       


        <!-- Create Deployment Posts Section  //-->
        
       
    
        <section class="py-8 mt-2">
                <div>
                    <form  action="{{ route('admin.deployment_posts.store') }} " method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto w-full md:w-[95%] items-center justify-center">
                        @csrf
    
                        
    
                        <div class="flex flex-col w-[80%] md:w-[60%] py-2 md:py-4" style="font-family:'Lato'; font-size:18px; font-weight:400;">
                            <h2 class="font-semibold text-xl py-1" >Create Deployment Post</h2>
                            
                        </div>
    
                        <div class="flex flex-col w-[80%] md:w-[60%]" >
                                @include('partials._session_response')
                        </div>
                        

                        
                        
                        
    
                        <!-- Deployment Post  //-->
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] py-3">
                        
                            
                            <input type="text" name="deployment_post" class="border border-1 border-gray-400 bg-gray-50
                                                                    w-full p-4 rounded-md 
                                                                    focus:outline-none
                                                                    focus:border-blue-500 
                                                                    focus:ring
                                                                    focus:ring-blue-100" placeholder="Deployment Post"
                                                                
                                                                    value="{{ old('deployment_post') }}"
                                                                    
                                                                    style="font-family:'Lato';font-size:16px;font-weight:500;"                                                                     
                                                                    required
                                                                    />  
                                                                                                                                        
    
                                                                    @error('deployment_post')
                                                                        <span class="text-red-700 text-sm">
                                                                            {{$message}}
                                                                        </span>
                                                                    @enderror
                            
                        </div><!-- end of Deployment Post //-->      
                        
                        

                        
                        <div class="flex flex-col border-red-900 w-[80%] md:w-[60%] mt-4">
                            <button type="submit" class="border border-1 bg-gray-400 py-4 text-white 
                                           hover:bg-gray-500
                                           rounded-md text-lg" style="font-family:'Lato';font-weight:500;">Create Deployment Post</button>
                        </div>
                        
                    </form><!-- end of Deployment Post form //-->
                <div>
    
            

        </section>
        <!-- End of Deployment Post Section //-->



    </div>

</x-admin-layout>