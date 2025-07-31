<x-guest-layout>

<section class="flex flex-col w-full md:w-1/2 border-0 mx-auto justify-center">

            <!-- Right  panel //-->
            <div class="flex flex-col w-full md:w-[100%] items-center justify-center py-4">

                        <section class="flex flex-col w-full border border-0">
                            <div class="flex flex-col w-full border border-0" >
                            <form  action="{{ route('guest.staff_updates.login_auth') }}" method="POST" 
                            class="flex flex-col mx-auto w-[90%] items-center justify-center space-y-2 text-2xl text-green-800 text-center">
                                    
                            Thank you! <br/>
                            You have successfully completed and submitted the Staff Update Form

                                   

                                    

                                   
                            </div>
                        </section>
                
            </div>
            <!-- end of right panel //-->




</section>

</x-guest-layout>