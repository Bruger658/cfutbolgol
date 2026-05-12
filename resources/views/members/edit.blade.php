<x-layouts.app :title="__('Editar socio')">
    <div class="mx-auto max-w-4xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar socio</h1>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('members.update', $member) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @include('members._form')
        </form>
    </div>
</x-layouts.app>