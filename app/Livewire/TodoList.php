<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|string|max:255')]
    public $name;

    public $search = '';

    public $editingTodoID;

    #[Rule('required|string|min:3|max:255')]
    public $editingTodoName;

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la tarea es obligatorio',
            'name.max' => 'El nombre de la tarea no debe tener más de 255 caracteres.'
        ];
    }

    public function save()
    {
        $validated = $this->validateOnly('name');

        $todo = Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Tarea creada');

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update()
    {
        $this->validateOnly('editingTodoName');

        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName,
        ]);


        $this->cancelEdit();
    }

    public function delete($todoID)
    {
        try {
            Todo::find($todoID)->delete();

            // Verificar si la página actual tiene registros después de la eliminación
            $todos = Todo::where('name', 'LIKE', "%{$this->search}%")->paginate(5);

            // Si no hay registros en la página actual y no es la primera página, retroceder una página
            if ($todos->isEmpty() && $todos->currentPage() > 1) {
                $this->gotoPage($todos->lastPage());
            }
        } catch (Exception $e) {
            session()->flash('error', 'Error al eliminar!');
            return;
        }
    }

    public function render()
    {
        $todos = Todo::latest()->where('name', 'LIKE', "%{$this->search}%")->paginate(5);
        return view('livewire.todo-list', [
            'todos' => $todos,
        ]);
    }
}
