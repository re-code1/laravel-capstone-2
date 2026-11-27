@extends('layouts.app')

@section('title', 'My subjects')

@section('content')
    <div class="page-header">
        <div>
            <h1>My subjects</h1>
            <p>{{ $student->user->name }} &middot; {{ $student->section->fullName() }}</p>
        </div>
    </div>

    @if ($subjects->isEmpty())
        <div class="empty-state">No subjects in your section yet.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->teacher->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
