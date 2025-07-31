<header class="flex flex-col shadow-md bg-gradient-to-b from-green-700 to-green-500">
   
    
    <nav class="py-3 border-0">
        <div class="max-w-8xl mx-auto px-2 sm:px-8 lg:px-4">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <div class="flex flex-shrink-0">
                    <!-- logo //-->
                    <div class="flex flex-row px-2 md:px-4 py-2">
                        <img src="{{ asset('images/logo.png')}}" />
                    </div>
                    <!-- end of logo //-->
                    <!-- Name //-->
                    <div class="flex flex-col item-center justify-center">
                            <div class="text-white font-bold text-2xl font-serif">FUNAAB WorkForce</div>
                            <div class="text-white font-semibold font-serif text-sm opacity-70 hidden md:block">Staff Resource Management Service...</div>
                                
                    </div>
                    <!-- end of name //-->
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden px-4">
                    <button class="text-white focus:outline-none" id="mobile-menu-button">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <!-- Main Menu -->
                <div class="hidden lg:flex space-x-4 border-0 mr-8">
                    @auth
                        @if (Auth::user()->role==='staff')

                            <a href="{{ route('staff.dashboard.index') }}" class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-2 ">Dashboard</a>

                            <a  href="#" class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-3 ">Staff Data</a>

                            <a  href="#" class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-3 ">APER Form</a>

                            <a  href="#" class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-3 ">Promotion</a>

                            <a  href="#" class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-3 ">Leave</a>
                            
                            
                            <div class="relative group flex">
                                <button class="text-white px-1 py-2 rounded-md font-semibold flex items-center">
                                    @if (Auth::user()->profile->avatar != null || Auth::user()->profile->avatar != '')
                                        <img class='w-12 h-12 rounded-full' src="{{ asset('storage/'.Auth::user()->profile->avatar )}}" alt="User Avatar" />
                                    @else
                                        <img class='w-12 h-12 rounded-full' src="{{ asset('images/avatar_64.jpg') }}" alt="Default Avatar" />
                                    @endif
                                    
                                </button>
                            
                                <!-- Dropdown Menu -->
                                <div class="absolute right-0 hidden group-hover:block top-10 bg-white text-gray-800 mt-2 py-2 shadow-lg rounded-md z-10">
                                    <a href="{{ route('staff.profile.myprofile') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Profile</a>
                                    <form action="{{ route('staff.auth.logout') }}" method="POST" class="flex flex-row w-full border-0 border-blue-900">
                                        @csrf
                                        <button type="submit" class="flex flex-row border-0 w-full text-left px-1 py-2 text-md text-gray-700
                                                                  hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pl-4 ">Log Out</button>
                                    </form>

                                </div>
                            </div>
                            
                             
                        @endif
                    @endauth     
                </div>
                
            </div>
            
            <!-- Mobile Menu -->
            <div class="lg:hidden hidden" id="mobile-menu">
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Dashboard</a>
                <div class="relative">
                    <button class="block w-full text-left text-white px-4 py-2 hover:bg-gray-700 rounded-md focus:outline-none" id="services-mobile">
                        Office
                    </button>
                    <!-- Sub-menu for Mobile -->
                    <div class="hidden bg-slate-50 rounded-md" id="services-sub-menu-mobile">                        
                        <a href="{{ route('staff.circles.index')}}" class="block px-4 py-2 hover:bg-gray-200">Circles</a>                        
                    </div>
                </div>                
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Documents</a>
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Profile</a>
                
                <form action="{{ route('admin.auth.logout') }}" method="POST" class="block w-full">
                    @csrf
                    
                    <button type="submit" class="block w-full text-white px-4 py-2 hover:bg-gray-700 rounded-md">Sign Out</button>
                </form> 
            </div>
        </div>
    </nav>
    
    <script>
        // Toggle Mobile Menu
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    
        // Toggle Mobile Sub-menu
        document.getElementById('services-mobile').addEventListener('click', function () {
            document.getElementById('services-sub-menu-mobile').classList.toggle('hidden');
        });
    </script>

    
</header>




