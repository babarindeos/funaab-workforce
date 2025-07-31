<header class="flex flex-col shadow-md bg-gradient-to-b from-green-700 to-green-500">

    
    
    <nav class="py-3 border-0">
        <div class="mx-auto px-2 sm:px-8 lg:px-8">
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
                            <div class="text-white font-semibold font-serif text-sm opacity-70">Staff Resource Management Service</div>
                                
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
                <div class="hidden lg:flex lg:px-4 space-x-2">
                    @auth
                        @if (Auth::user()->role==='admin')

                            <a href='{{ route('admin.dashboard.index') }}' class="flex font-semibold items-center text-white hover:border-b-yellow-100 hover:border-b-4 mx-0 
                                    hover:bg-green-500 px-4 hover:rounded-t ">Dashboard</a>

                            <div class="relative group">
                                <button class="text-white px-1 py-2 font-semibold hover:bg-green-500 px-4 hover:rounded-t">
                                    Staff
                                </button>
                                <!-- Sub-menu -->
                                <div class="absolute hidden group-hover:block bg-white text-gray-800 mt-0 py-2 shadow-lg w-[300%]">
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Staff Management</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Employment Types</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Staff Categories</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Staff Titles</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Staff Status</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Class of Degree</a>                                     
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Degrees</a>                                                                       
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Deployment Post</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Deployment</a>
                                    <a href="{{ route('admin.identity.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Identity</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Off Employment</a>
                                    
                                    
                                    
                                    
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="text-white px-1 py-2  font-semibold hover:bg-green-500 px-2 hover:rounded-t">
                                    Progression
                                </button>
                                <!-- Sub-menu -->
                                <div class="absolute hidden group-hover:block bg-white text-gray-800 mt-0 py-2 shadow-lg w-[180%]">
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Promotion</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Step Increment</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Cadre</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Rank</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Designation</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Salary Structure</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Salary Level</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Salary Step</a>
                                    
                                </div>
                            </div>


                            <div class="relative group">
                                <button class="text-white px-1 py-2 rounded-md font-semibold hover:bg-green-500 px-4 hover:rounded-t">
                                    Evaluation
                                </button>
                                <!-- Sub-menu -->
                                <div class="absolute hidden group-hover:block bg-white text-gray-800 mt-0 py-2 shadow-lg w-[170%]">
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">APER Form</a>
                                    
                                </div>
                            </div>


                            <div class="relative group">
                                <button class="text-white px-1 py-2 font-semibold hover:bg-green-500 px-4 hover:rounded-t">
                                    Leave
                                </button>
                                <!-- Sub-menu -->
                                <div class="absolute hidden group-hover:block bg-white text-gray-800 mt-0 py-2 shadow-lg w-[250%]">
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Leave Types</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Leaves</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Leave Application</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="text-white px-1 py-2 font-semibold hover:bg-green-500 px-4 hover:rounded-t">
                                    Settings
                                </button>
                                <!-- Sub-menu -->
                                <div class="absolute hidden group-hover:block bg-white text-gray-800 mt-0 py-2 shadow-lg w-[200%]">
                                    <a href="{{ route('admin.division_types.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200 hover:border-l-yellow-500 hover:border-l-4 pr-8">Division Types</a>
                                    <a href="{{ route('admin.divisions.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Divisions</a>
                                    <a href="{{ route('admin.departments.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Departments</a>
                                    <a href="{{ route('admin.contact_types.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Contact Types</a>
                                    <a href="{{ route('admin.gender.index') }}" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Gender</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Marital Status</a>       
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">Geo-Pol Zones</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">States</a>
                                    <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200  hover:border-l-yellow-500 hover:border-l-4 pr-8">LGAs</a>
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.auth.logout') }}" method="POST" class="flex items-center justify-center border-0">
                                @csrf
                                
                                <button type="submit" class="flex font-semibold items-center hover:border-b-yellow-100 text-white hover:border-b-4 mx-3 hover:bg-green-500 px-4 py-1 hover:rounded-t">Sign Out</button>
                            </form>  
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
                        <a href="#" class="block px-4 py-2 hover:bg-gray-200">Cells</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-200">Circles</a>
                        <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200">Admin</a>
                        <a href="#" class="flex flex-row px-4 py-2 hover:bg-gray-200">Staff</a>
                    </div>
                </div>
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Users</a>
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Documents</a>
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Tracker</a>
                <a href="#" class="block text-white px-4 py-2 hover:bg-gray-700 rounded-md">Analytics</a>
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
