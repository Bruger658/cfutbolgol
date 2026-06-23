            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                           
                            <x-layouts.sidebar-link href="{{ route('gallery-items.index') }}" icon='fas-image'
                                :active="request()->routeIs('gallery-items*')">Gallery</x-layouts.sidebar-link>

                                                    
                            
                            <x-layouts.sidebar-link href="{{ route('publications.index') }}" icon='fas-newspaper'
                                :active="request()->routeIs('publications*') || request()->routeIs('noticias.index')">Noticias
                            </x-layouts.sidebar-link>
    
                             <x-layouts.sidebar-link href="{{ route('fixture') }}" icon='fas-calendar-days'
                                :active="request()->routeIs('fixtures*') || request()->routeIs('fixture')">Fixture</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('events.index') }}" icon='fas-calendar'
                                :active="request()->routeIs('events*')">Calendario</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('staff.index') }}" icon='fas-user-tie'
                                :active="request()->routeIs('staff*')">Staff</x-layouts.sidebar-link>


                            <x-layouts.sidebar-link href="{{ route('members.index') }}" icon='fas-users'
                                :active="request()->routeIs('members*')">Socios</x-layouts.sidebar-link>
                            
                            <x-layouts.sidebar-link href="{{ route('enrollment-requests.index') }}" icon='fas-clipboard-list'
                                :active="request()->routeIs('enrollment-requests*')">Inscripciones</x-layouts.sidebar-link>
                                
                            <x-layouts.sidebar-link href="{{ route('products.index') }}" icon='fas-shirt'
                                :active="request()->routeIs('products*')">Tienda</x-layouts.sidebar-link>

                            @can('manage-users')
                                <x-layouts.sidebar-link href="{{ route('users.index') }}" icon='fas-user-plus'
                                    :active="request()->routeIs('users*')">Usuarios</x-layouts.sidebar-link>
                            @endcan

                            @can('manage-roles')
                                <x-layouts.sidebar-link href="{{ route('roles.index') }}" icon='fas-user-shield'
                                    :active="request()->routeIs('roles*')">Roles</x-layouts.sidebar-link>
                                <x-layouts.sidebar-link href="{{ route('permissions.index') }}" icon='fas-key'
                                    :active="request()->routeIs('permissions*')">Permisos</x-layouts.sidebar-link>
                            @endcan
                               
                        </ul>
                    </nav>
                </div>
            </aside>
