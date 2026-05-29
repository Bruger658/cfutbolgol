<x-layouts.app :title="__('Pagar cuota')">
    <div class="mx-auto max-w-5xl space-y-6 p-6">
        <div class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-emerald-500 p-6 text-white shadow-xl">
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-blue-100">Socios</p>
            <h1 class="mt-2 text-3xl font-black">Pagar cuota</h1>
            <p class="mt-2 max-w-3xl text-blue-50">
                Buscá por nombre, apellido, número de socio o documento. Al seleccionar un socio vas a ver sus datos,
                los meses adeudados y el total a cobrar con el recargo automático del 10% desde el día 11.
            </p>
        </div>

        <div class="rounded-3xl border border-outline/30 bg-surface p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <form action="{{ route('members.fee-payments.index') }}" method="GET" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <div class="space-y-1.5">
                    <label for="search" class="ml-1 text-sm font-bold text-on-surface dark:text-gray-100">Buscar socio</label>
                    <input
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Ej: Juan Pérez, 25 o 30111222"
                        class="w-full rounded-xl border border-outline/30 bg-surface px-5 py-3 text-on-surface placeholder:text-on-surface-variant transition-all focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                        type="text"
                        autofocus
                    />
                </div>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 font-black text-white shadow-lg shadow-blue-600/25 transition-all hover:bg-blue-700">
                    Buscar
                </button>
            </form>
        </div>

        @if($members->isNotEmpty() && !$selectedMember)
            <div class="rounded-3xl border border-outline/30 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Resultados encontrados</h2>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach($members as $member)
                        <a href="{{ route('members.fee-payments.index', ['member' => $member->id, 'search' => $search]) }}"
                            class="rounded-2xl border border-gray-200 p-4 transition hover:border-blue-400 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-500 dark:hover:bg-gray-900">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-gray-100">{{ $member->first_name }} {{ $member->last_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Socio #{{ $member->id }} · DNI {{ $member->document_number }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $member->is_up_to_date ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $member->is_up_to_date ? 'Al día' : 'Adeuda' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif($search !== '' && !$selectedMember)
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-sm">
                No encontramos socios para “{{ $search }}”. Probá con otro dato o revisá el número de socio.
            </div>
        @endif

        @if($selectedMember && $paymentSummary)
            <div class="grid gap-6 lg:grid-cols-[1fr_1.15fr]">
                <div class="rounded-3xl border border-outline/30 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-xl font-black text-gray-900 dark:text-gray-100">Datos del socio</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3 dark:border-gray-700">
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">N° socio</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">#{{ $selectedMember->id }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3 dark:border-gray-700">
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $selectedMember->first_name }} {{ $selectedMember->last_name }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3 dark:border-gray-700">
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">Categoría</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $selectedMember->category }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3 dark:border-gray-700">
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">Documento</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $selectedMember->document_number }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="font-bold {{ $selectedMember->is_up_to_date ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $selectedMember->is_up_to_date ? 'Al día' : 'Con cuota adeudada' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-outline/30 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-gray-100">Detalle a cobrar</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Cuota base: ${{ number_format($paymentSummary['monthly_fee'], 0, ',', '.') }}.
                                @if($paymentSummary['surcharge_applies'])
                                    Hoy corresponde recargo del {{ $paymentSummary['surcharge_percentage'] }}% por pago posterior al día 10.
                                @else
                                    Sin recargo hasta el día 10 inclusive.
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('members.edit', $selectedMember) }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900">
                            Editar socio
                        </a>
                    </div>

                    @if($paymentSummary['months']->isEmpty())
                        <div class="mt-5 rounded-2xl bg-emerald-50 p-5 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                            Este socio está al día hasta el mes en curso. No hay cuotas pendientes para cobrar.
                        </div>
                    @else
                        <form action="{{ route('members.fee-payments.store', $selectedMember) }}" method="POST" class="mt-5 space-y-4">
                            @csrf
                            <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-bold">Pagar</th>
                                            <th class="px-4 py-3 text-left text-sm font-bold">Mes</th>
                                            <th class="px-4 py-3 text-right text-sm font-bold">Base</th>
                                            <th class="px-4 py-3 text-right text-sm font-bold">Recargo</th>
                                            <th class="px-4 py-3 text-right text-sm font-bold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($paymentSummary['months'] as $month)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" name="months[]" value="{{ $month['number'] }}" checked class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                </td>
                                                <td class="px-4 py-3 font-semibold">{{ $month['name'] }}</td>
                                                <td class="px-4 py-3 text-right">${{ number_format($month['base_amount'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right">${{ number_format($month['surcharge_amount'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-bold">${{ number_format($month['total'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @error('months')
                                <p class="text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="rounded-2xl bg-blue-50 p-4 text-right dark:bg-blue-950">
                                <p class="text-sm font-semibold text-blue-700 dark:text-blue-200">Total sugerido</p>
                                <p class="text-3xl font-black text-blue-900 dark:text-blue-100">${{ number_format($paymentSummary['total'], 0, ',', '.') }}</p>
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-6 py-4 text-lg font-black text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700">
                                Confirmar pago de cuota
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>