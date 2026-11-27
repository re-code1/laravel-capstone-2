@extends('layouts.app')

@section('title', 'New leave request')

@section('content')
    <div class="page-header">
        <h1>New leave request</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('leave.store') }}">
            @csrf

            <label for="start_date">Start date</label>
            <input id="start_date" name="start_date" type="date" min="{{ now()->toDateString() }}"
                   class="input @error('start_date') input-error @enderror"
                   value="{{ old('start_date') }}" required>
            @error('start_date') <div class="field-error">{{ $message }}</div> @enderror

            <label for="end_date">End date</label>
            <input id="end_date" name="end_date" type="date" min="{{ now()->toDateString() }}"
                   class="input @error('end_date') input-error @enderror"
                   value="{{ old('end_date') }}" required>
            @error('end_date') <div class="field-error">{{ $message }}</div> @enderror

            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" maxlength="500"
                      class="input @error('reason') input-error @enderror" required>{{ old('reason') }}</textarea>
            @error('reason') <div class="field-error">{{ $message }}</div> @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Send request</button>
                <a href="{{ route('leave.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
