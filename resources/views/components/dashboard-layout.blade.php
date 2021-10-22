<x-app-layout>
    <div class="min-h-screen overflow-hidden">
        <div class="grid grid-cols-12 min-h-screen">
            <x-dashboard.aside-menu></x-dashboard.aside-menu>
            <div class="col-span-12 md:col-span-7 lg:col-span-9 bg-gray-100 p-12">
                {{ $slot }}
            </div>
        </div>
    </div>

</x-app-layout>
