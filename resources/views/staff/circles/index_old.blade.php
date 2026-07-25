<x-staff-layout>

    <div class="flex flex-col container mx-4 border border-0 md:mx-auto">
        <section class="border-b border-gray-200 py-2 mt-2">
                <div class="text-2xl font-semibold ">
                    Circles               
                </div>
                
        </section>

        <!-- section //-->
        <section class="py-4 border-0">
            <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:flex-wrap w-[100%]">
                @php
                     $colors = ['bg-red-500', 'bg-green-500', 'bg-blue-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-orange-500', 'bg-teal-500'];

                @endphp
                @foreach ($circles as $circle)
                    <div class="flex w-full md:w-[50%] border-0">
                        <div class="flex flex-row items-center p-2">
                            <div>
                                @php
                                    $initial = strtoupper(substr($circle->cell->name, 0, 1));
                                    $randomColor = $colors[array_rand($colors)];
                                @endphp
                                <div class="flex items-center justify-center w-16 h-16 
                                            rounded-full border-0 text-2xl {{$randomColor}} 
                                            font-semibold text-white">
                                    {{ $initial}}
                                </div>
                                
                            </div>
                            <div class="px-4 text-lg font-medium">
                                <a href="{{ route('staff.circles.general_room', ['circle'=>$circle->id])}}" class="hover:underline">
                                    {{ $circle->cell->name }} ({{ $circle->cell->code }})
                                </a>
                                <div class="text-sm">
                                    {{$circle->cell->cell_type->name}}
                                </div>

                            </div>
                        </div>
                    </div>
                   
                @endforeach
            </div>

        </section>
        <!-- section //-->
    
        
    
    
        
    </div>
    


</x-staff-layout>