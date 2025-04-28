@if (isset($semesters) && count($semesters))
    <div class="mb-3">
        <form method="POST" action="{{ route('semesters.activate', $activeSemester->id ?? $semesters[0]->id) }}">
            @csrf
            @method('PATCH')
            <div class="d-flex align-items-center gap-2">
                <label for="semester_id" class="mb-0">Semester:</label>
                <select name="semester_id" id="semester_id" class="form-select form-select-sm w-auto">
                    @foreach ($semesters as $s)
                        <option value="{{ $s->id }}" {{ $activeSemester->id == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-success">Set Active</button>
            </div>
        </form>
    </div>
@endif
