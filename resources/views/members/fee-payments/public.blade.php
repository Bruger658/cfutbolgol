<x-layouts.base>
    <main class="pt-24 bg-brand-pale min-h-screen">
        <section class="px-6 py-16">
            <div class="max-w-6xl mx-auto grid gap-8 lg:grid-cols-[0.95fr_1.05fr] items-start">
                <div class="rounded-[2rem] bg-white p-8 shadow-xl border border-blue-100">
                    <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-2 text-sm font-black uppercase tracking-wide text-primary">
                        <span class="material-symbols-outlined text-base">payments</span>
                        Pagar cuota
                    </span>
                    <h1 class="mt-6 text-4xl md:text-5xl font-black text-on-surface leading-tight">Aboná la cuota desde la web</h1>
                    <p class="mt-4 text-lg text-on-surface-variant leading-relaxed">
                        Buscá al jugador por nombre, apellido, DNI o número de socio. Seleccioná los meses adeudados y el sistema recibirá el pago en el backend de socios.
                    </p>

                    <form method="GET" action="{{ route('fees.public.index') }}" class="mt-8 space-y-4">
                        <label for="search" class="block text-sm font-black uppercase tracking-wide text-on-surface">Buscar socio</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                type="search"
                                placeholder="Ej: DNI, nombre o apellido"
                                class="min-h-14 flex-1 rounded-2xl border-blue-100 bg-blue-50 px-5 text-base font-semibold text-on-surface placeholder:text-slate-400 focus:border-primary focus:ring-primary"
                            >
                            <button type="submit" class="rounded-2xl bg-primary px-6 py-4 font-black uppercase tracking-wide text-on-primary shadow-lg shadow-primary/25 hover:bg-blue-700 transition">
                                Buscar
                            </button>
                        </div>
                    </form>

                    @if(session('status'))
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($search !== '' && $members->isEmpty() && ! $selectedMember)
                        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800 font-bold">
                            No encontramos socios con ese dato. Revisá la búsqueda o consultá en la sede.
                        </div>
                    @endif

                    @if($members->count() > 1)
                        <div class="mt-8 space-y-3">
                            <h2 class="text-sm font-black uppercase tracking-wide text-on-surface">Elegí el socio</h2>
                            @foreach($members as $member)
                                <a href="{{ route('fees.public.index', ['member' => $member->id, 'search' => $search]) }}" class="flex items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 transition hover:border-primary hover:bg-primary/10">
                                    <span>
                                        <span class="block font-black text-on-surface">{{ $member->first_name }} {{ $member->last_name }}</span>
                                        <span class="text-sm font-semibold text-on-surface-variant">DNI {{ $member->document_number }} · Socio #{{ $member->id }}</span>
                                    </span>
                                    <span class="material-symbols-outlined text-primary">arrow_forward</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-xl border border-blue-100">
                    @if(! $selectedMember)
                        <div class="min-h-[420px] flex flex-col items-center justify-center text-center rounded-3xl border-2 border-dashed border-blue-100 bg-blue-50/60 p-8">
                            <span class="material-symbols-outlined text-6xl text-primary">manage_search</span>
                            <h2 class="mt-4 text-2xl font-black text-on-surface">Buscá un socio para ver su cuota</h2>
                            <p class="mt-2 max-w-md text-on-surface-variant">Cuando selecciones un jugador, vas a ver los meses pendientes y el total a abonar.</p>
                        </div>
                    @else
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-primary">Socio seleccionado</p>
                                <h2 class="mt-1 text-3xl font-black text-on-surface">{{ $selectedMember->first_name }} {{ $selectedMember->last_name }}</h2>
                                <p class="mt-1 text-sm font-semibold text-on-surface-variant">DNI {{ $selectedMember->document_number }} · {{ $selectedMember->category }}</p>
                            </div>
                            <span class="rounded-full {{ $selectedMember->is_up_to_date ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-4 py-2 text-sm font-black uppercase tracking-wide">
                                {{ $selectedMember->is_up_to_date ? 'Al día' : 'Con deuda' }}
                            </span>
                        </div>

                        @if($paymentSummary['months']->isEmpty())
                            <div class="mt-8 rounded-3xl bg-emerald-50 p-8 text-center border border-emerald-100">
                                <span class="material-symbols-outlined text-5xl text-emerald-600">check_circle</span>
                                <h3 class="mt-3 text-2xl font-black text-emerald-900">No hay cuotas pendientes</h3>
                                <p class="mt-2 text-emerald-700 font-semibold">El jugador figura al día en el backend.</p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('fees.public.store', $selectedMember) }}" class="mt-8 space-y-6">
                                @csrf
                                <div class="overflow-hidden rounded-3xl border border-blue-100">
                                    <table class="w-full text-sm">
                                        <thead class="bg-primary text-on-primary">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-black">Pagar</th>
                                                <th class="px-4 py-3 text-left font-black">Mes</th>
                                                <th class="px-4 py-3 text-right font-black">Base</th>
                                                <th class="px-4 py-3 text-right font-black">Recargo</th>
                                                <th class="px-4 py-3 text-right font-black">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-blue-100">
                                            @foreach($paymentSummary['months'] as $month)
                                                <tr class="bg-white">
                                                    <td class="px-4 py-3">
                                                        <input type="checkbox" name="months[]" value="{{ $month['number'] }}" checked class="h-5 w-5 rounded border-blue-200 text-primary focus:ring-primary">
                                                    </td>
                                                    <td class="px-4 py-3 font-black text-on-surface">{{ $month['name'] }}</td>
                                                    <td class="px-4 py-3 text-right font-semibold">${{ number_format($month['base_amount'], 0, ',', '.') }}</td>
                                                    <td class="px-4 py-3 text-right font-semibold">${{ number_format($month['surcharge_amount'], 0, ',', '.') }}</td>
                                                    <td class="px-4 py-3 text-right font-black">${{ number_format($month['total'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @error('months')
                                    <p class="text-sm font-black text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="rounded-3xl bg-blue-50 p-5 text-right border border-blue-100">
                                    <p class="text-sm font-black uppercase tracking-wide text-primary">Total a pagar</p>
                                    <p class="text-4xl font-black text-on-surface">${{ number_format($paymentSummary['total'], 0, ',', '.') }}</p>
                                    @if($paymentSummary['surcharge_applies'])
                                        <p class="mt-1 text-sm font-semibold text-on-surface-variant">Incluye recargo del {{ $paymentSummary['surcharge_percentage'] }}% por pago fuera de término.</p>
                                    @endif
                                </div>

                                <button type="submit" class="w-full rounded-3xl bg-emerald-600 px-6 py-5 text-lg font-black uppercase tracking-wide text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700">
                                    Confirmar y enviar pago al backend
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    </main>
</x-layouts.base>