<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        color: rgb(110, 108, 108);
        font-size: .9rem;
        padding: 1rem;
    }
    a {
        color: rgb(81, 168, 186);
        text-decoration: none;
    }
    a:hover {
        color: rgb(51, 131, 142);
    }
</style>

<p>Hi {{ $membership->member->name }}!</p>

<p>{{ $user->username }} assigned a task to you. This task is part of the project, “<em>{{ $task->project->name }}</em>”.</p>

<p>Task name: <span style="color: #939090;">{{ $task->name }}</span></p>
<p>Task description: <span style="color: #939090;">{{ $task->description }}</span></p>
<p>Task deadline: <span style="color: #939090;">{{ Carbon\Carbon::parse($task->end_date)->format('d M y') }}</span></p>

<p>If you would like to view or respond to the task, go to <a href="{{ route('talkboard') }}">we4v</a>, click the “Assigned tasks” panel on the right of each we4v page, and then click the relevant task.</p>

<br>
<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">