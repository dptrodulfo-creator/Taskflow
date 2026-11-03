@extends('layouts.taskflow')

@section('title', 'Edit Task')

@section('content')

<div class="topbar">
    <div>
        <h1>Edit Task</h1>
        <p class="sub">Update your task details.</p>
    </div>

    <a href="{{ route('tasks.index') }}" class="btn btn-light">
        ← Back
    </a>
</div>

<div class="panel form-card">

    @include('task._form', [
        'task' => $task,
        'buttonText' => 'Save Changes'
    ])

</div>

@endsection