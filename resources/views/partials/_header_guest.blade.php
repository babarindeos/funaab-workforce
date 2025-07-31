<header class="flex flex-col shadow-md bg-gradient-to-b from-green-700 to-green-500">
    

    <!-- header bar //-->
    <div class="flex flex-row justify-between py-2 md:px-8">
        <div class="flex">
            <!-- logo //-->
            <div class="flex flex-row px-2 md:px-4 py-2">
                 <a href="{{ route('welcome') }}">
                        <img src="{{ asset('images/logo.png')}}" />
                 </a>
            </div>
            <!-- end of logo //-->
            <!-- Name //-->
            <a href="{{ route('welcome') }}" class="flex flex-col item-center justify-center">
                    <div class="text-white font-bold text-2xl font-serif">FUNAAB WorkForce</div>
                    <div class="text-white font-semibold font-serif text-sm opacity-70">Staff Resource Management Service...</div>
            </a>
            <!-- end of name //-->
        </div>

        @auth
            <div class="hidden md:flex flex-row items-center px-1"> 
               
                <form action="{{ route('staff.auth.logout') }}" method="POST">
                    @csrf
                    
                    <button type="submit" class="flex font-semibold items-center hover:border-b-yellow-100 text-white hover:border-b-4 mx-3 ">Sign Out</button>
                </form>       
            </div> 
        @endauth
       
        
    </div>
    <!-- end of header bar //-->
</header>
