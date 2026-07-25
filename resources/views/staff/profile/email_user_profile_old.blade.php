<x-staff-layout>
    <div class="flex flex-col container mx-4 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    User Profile              
                </div>                
        </section>

        @if ( $userprofile == null)
            <section class="mx-auto">
                    <div class="flex flex-col justify-content items-center  py-12">
                        <div class="text-2xl text-gray-400 font-bold">Oops! An error occurred</div>
                        <div class="text-lg font-semibold">Sorry, there is no such record </div>
                    </div>
            </section>
        
        @elseif ($userprofile->profile == null)
            
            <section class="mx-auto">
                    <div class="flex flex-col justify-content items-center  py-12">
                        <div class="text-2xl text-gray-400 font-bold">Oops! An error occurred</div>
                        <div class="text-lg font-semibold">Sorry, the user has not created profile for the account </div>
                    </div>
            </section>
        @else

                <section class="flex flex-col md:flex-row rounded w-full mt-3 space-x-4">
                    <div class="flex flex-col w-full  md:w-[30%] justify-center items-center 
                                border px-8 py-4 rounded-md">
                            <div class="">
                                @if ($userprofile->profile!=null && ($userprofile->profile->avatar != "" || $userprofile->profile->avatar != null))
                                    <img src="{{ asset('storage/'.$userprofile->profile->avatar) }}" class="w-36 h-36 rounded-full" />
                                @else
                                    <img src="{{ asset('images/avatar_200.jpg') }}" class="" />
                                @endif
                            </div>
                            

                    </div>
                    <div class="flex flex-col justify-center md:border rounded-md md:w-[70%] py-4 px-4">
                            
                            <div class="mb-4  mx-[10%] md:mx-0 ">
                                    <div class="text-xl font-semibold">
                                            {{ $userprofile->staff->surname }} {{ $userprofile->staff->firstname }} {{ $userprofile->staff->middlename }}                                
                                    </div>
                                    <div class="text-sm">
                                            {{ $userprofile->profile->designation}}
                                    </div>                            
                            </div>


                            <div class="py-4 mx-[10%] md:mx-0">
                                <div class="font-semibold py-1">
                                        Contacts
                                    </div>
                                <div>
                                        {{ $userprofile->email }}
                                </div>
                                <div>
                                        {{ $userprofile->profile->phone}}
                                </div>
                            </div>



                            <div class="py-4 mx-[10%] md:mx-0">
                                    <!-- circles //--> 
                                    <div class="font-semibold py-1">
                                        Work Circles
                                    </div>
                                    @if ($userprofile->circle->count())
                                
                                        @foreach($userprofile->circle as $circle)
                                           <div>
                                                {{ $circle->cell->name }} ({{$circle->cell->code}})
                                           </div>
                                        @endforeach
                                    @else
                                        <div>
                                                Currently in no Work Circle
                                        </div>                                        
                                    @endif                  
                            </div>


                            
                    </div>
                </section>

    @endif
    


    </div>

</x-staff-layout>