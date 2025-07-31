<x-admin-layout>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div class="x flex-col container mx-4 mb-8 border border-0 md:mx-auto">

        <!-- Page Header //-->
        <section class="border-b border-gray-200 py-2 mt-2">
            <div class="text-2xl font-semibold ">
                Dashboard         
            </div>            
        </section>
        <!-- end of Page Header //-->


        <!-- board //-->
        <section class="flex flex-row border border-0 py-1 mt-3">
                <div class="flex flex-col md:flex-row mx-auto md:space-x-2 w-4/5 justify-center items-center">
                    <div class="flex flex-col border border-1 border-yellow-500 
                                w-full md:w-[20%] px-4 py-4 mt-1 rounded-md bg-yellow-500">
                        <div class="text-white text-3xl">
                            
                        </div>
                        <div class="text-sm text-white font-normal">
                            Documents
                        </div>
                    </div>

                    <div class="flex flex-col border border-1 border-pink-500 
                                w-full md:w-[20%] px-4 py-4 mt-1 rounded-md bg-pink-500">
                        <div class="text-white text-3xl">
                           
                        </div>
                        <div class="text-sm text-white font-normal">
                            Workflows
                        </div>
                    </div>


                    <div class="flex flex-col border border-1 border-blue-500 
                                w-full md:w-[20%] px-4 py-4 mt-1 rounded-md bg-blue-500">
                        <div class="text-white text-3xl">
                            
                        </div>
                        <div class="text-sm text-white">
                            Staff
                        </div>
                    </div>

                    <div class="flex flex-col border border-1 border-purple-500 
                                w-full md:w-[20%] px-4 py-4 mt-1 rounded-md bg-purple-500">
                        <div class="text-white text-3xl">
                            
                        </div>
                        <div class="text-sm text-white">
                            Departments
                        </div>
                    </div>

                    
                </div>
        </section>
        <!-- end of board //-->


        <!-- Document Charts 
        <section class="flex flex-col w-full md:flex-row border-0 py-4">
                <!-- Documents by Ministries 
                <div class="flex-1 border-0">
                        <div class="hidden">
                            Documents By Ministries
                        </div>
                        <div>
                            <div id="ministry_document_piechart_3d" style="width: 650px; height: 400px;"></div>
                        </div>
                </div>
                <!-- end of Documents by Ministries 

                <!-- Documents by Departments 
                <div class="flex-1">
                        <div class="hidden">
                            Departments Chart
                        </div>
                        <div>
                            <div id="department_document_piechart_3d" style="width:650px; height:400px"></div>
                        </div>
                </div>
                <!-- end of Documents by Departments //-->
        </section>
        <!-- end of Document Charts 
        
        
        <!-- Staff Charts 
        <section class="flex flex-col w-full md:flex-row border-0 py-4">
                <!-- Staff by Ministries 
                <div class="flex-1 border-0">
                        <div class="hidden">
                            Staff By Ministries
                        </div>
                        <div>
                            <div id="ministry_staff_piechart" style="width: 650px; height: 400px;"></div>
                        </div>
                </div>
                <!-- end of Staff by Ministries 

                <!-- Staff by Departments 
                <div class="flex-1">
                        <div class="hidden">
                            Staff by Departments
                        </div>
                        <div>
                            <div id="department_staff_dotnut" style="width:650px; height:400px"></div>
                        </div>
                </div>
                <!-- end of Staff by Departments 
        </section>
        <!-- end of Staff Charts //-->




            
    </div>
</x-admin-layout>

