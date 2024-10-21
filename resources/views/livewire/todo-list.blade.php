<div>
    @include('livewire.includes.create-todo-box')
    @include('livewire.includes.search-box')

    <div id="todos-list">
        @if ($todos->count())
            @foreach ($todos as $todo)
                @include('livewire.includes.todo-card')
            @endforeach
            @if ($todos->hasPages())
                <div class="my-2">
                    {{ $todos->links() }}
                </div>
            @endif
        @else
            <div class="todo mb-5 card px-5 py-6 bg-white col-span-1 border-t-2 border-blue-500 hover:shadow">
                <strong>No hay datos...</strong>
            </div>

        @endif
    </div>
</div>
