@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="page-header">
        <h1>Students</h1>
        <a href="{{ route('students.create') }}" class="btn btn-compose">+ Add student</a>
    </div>

    @if ($students->isEmpty())
        <div class="empty-state">No students yet.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student no.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Section</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->student_no }}</td>
                            <td>{{ $student->user->name }}</td>
                            <td>{{ $student->user->email }}</td>
                            <td>{{ $student->section->fullName() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
