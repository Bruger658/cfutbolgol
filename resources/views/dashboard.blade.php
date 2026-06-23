<x-layouts.app>
    <div class="mb-6">
       <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">{{ __('Panel interno') }}</p>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard') }}</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Resumen rápido de socios, cuotas, inscripciones, tienda y contenido del club.') }}</p>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Socios activos') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['activeMembers']) }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Total de socios registrados') }}</p>    
                </div>
                <div class="rounded-full bg-blue-100 p-3 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300">
                    @svg('fas-users', 'h-6 w-6')
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">--</p>
                    <p class="text-xs text-gray-500 flex items-center mt-1">
                <div class="rounded-full bg-amber-100 p-3 text-amber-600 dark:bg-amber-900/50 dark:text-amber-300">
                    @svg('fas-receipt', 'h-6 w-6')       
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-300"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Inscripciones nuevas') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['newEnrollments']) }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Solicitudes pendientes') }}</p>
                </div>


                <div class="rounded-full bg-green-100 p-3 text-green-600 dark:bg-green-900/50 dark:text-green-300">
                    @svg('fas-clipboard-list', 'h-6 w-6')
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pedidos pendientes') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['pendingOrders']) }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Compras de tienda sin completar') }}</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3 text-purple-600 dark:bg-purple-900/50 dark:text-purple-300">
                    @svg('fas-shirt', 'h-6 w-6')
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-700">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Últimos pagos') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Movimientos recientes de cuotas') }}</p>
                </div>
                @can('manage-fees')
                    <a href="{{ route('members.fee-payments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">{{ __('Ver cuotas') }}</a>
                @endcan
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($latestPayments as $payment)
                    <div class="flex items-center justify-between gap-4 p-5">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->member?->first_name }} {{ $payment->member?->last_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Meses: :months', ['months' => collect($payment->months)->implode(', ')]) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">${{ number_format((float) $payment->total_amount, 2, ',', '.') }}</p>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300' }}">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Todavía no hay pagos registrados.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-700">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Últimas inscripciones') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Solicitudes recibidas desde la web') }}</p>
                </div>
                @can('manage-enrollments')
                    <a href="{{ route('enrollment-requests.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">{{ __('Ver todas') }}</a>
                @endcan
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($latestEnrollments as $enrollment)
                    <div class="flex items-center justify-between gap-4 p-5">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $enrollment->player_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->category }} · {{ $enrollment->created_at->format('d/m/Y') }}</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">{{ $enrollment->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Todavía no hay inscripciones registradas.') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Próximos partidos/eventos') }}</h2>
            <div class="mt-4 space-y-4">
                @forelse ($upcomingFixtures as $fixture)
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/40">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $fixture->home_team_name }} vs {{ $fixture->away_team_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $fixture->fixture_date->format('d/m/Y') }} · {{ $fixture->match_time?->format('H:i') }} · {{ $fixture->venue_name }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No hay partidos próximos cargados.') }}</p>
                @endforelse

                @foreach ($upcomingEvents as $event)
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/40">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $event->title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event->starts_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Últimas noticias') }}</h2>
            <div class="mt-4 space-y-4">
                @forelse ($latestPublications as $publication)
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $publication->title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $publication->category }} · {{ optional($publication->published_at)->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Todavía no hay noticias activas.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Accesos rápidos') }}</h2>
            <div class="mt-4 grid gap-3">
                @can('manage-members')
                    <a href="{{ route('members.create') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Registrar socio') }}</a>
                @endcan
                @can('manage-enrollments')
                    <a href="{{ route('enrollment-requests.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Ver inscripciones') }}</a>
                @endcan
                @can('manage-content')
                    <a href="{{ route('publications.create') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Crear noticia') }}</a>
                    <a href="{{ route('events.create') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Cargar evento') }}</a>
                @endcan
                @can('manage-store')
                    <a href="{{ route('products.create') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Agregar producto') }}</a>
                @endcan
            </div>
        </section>
    </div>

</x-layouts.app>
