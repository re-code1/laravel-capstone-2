@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')
    <div class="page-header">
        <h1>My Subjects</h1>
        <a href="{{ route('subjects.create') }}" class="btn btn-compose">+ New subject</a>
    </div>

    @if ($subjects->isEmpty())
        <div class="empty-state">You have no subjects yet.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Section</th>
                        <th class="actions"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->section->fullName() }}</td>
                            <td class="actions">
                                <a href="{{ route('marks.edit', $subject) }}" class="btn btn-secondary">Enter marks</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
