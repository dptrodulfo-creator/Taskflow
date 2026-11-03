@extends('layouts.taskflow')

@section('title', 'Create Task')

@section('content')

<div class="topbar">
    <div>
        <h1>Create a Task</h1>
        <p class="sub">Add the details for your new task.</p>
    </div>

    <a href="{{ route('tasks.index') }}" class="btn btn-light">
        ← Back
    </a>
</div>

<div class="panel form-card">

    @include('task._form', [
        'task' => null,
        'buttonText' => 'Create Task'
    ])

</div>

@endsection