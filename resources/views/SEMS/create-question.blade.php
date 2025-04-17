@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Create Question</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}" style="color: #000000; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000;">Create Question</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Course Information Form -->
    <form action="{{ route('exam.store') }}" method="POST">
        @csrf

        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Course Name -->
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course Name</label>
                            <div class="col-md-8">
                                <input type="text" name="course_name" class="form-control" required>
                            </div>
                        </div>

                        <!-- Course Code -->
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course Code</label>
                            <div class="col-md-8">
                                <input type="text" name="course_code" class="form-control" required>
                            </div>
                        </div>

                        <!-- Section -->
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Section</label>
                            <div class="col-md-8">
                                <input type="text" name="section" class="form-control">
                            </div>
                        </div>

                        <!-- Coordinator Name -->
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Coordinator Name</label>
                            <div class="col-md-8">
                                <input type="text" name="coordinator_name" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOS Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Table of Specification (TOS)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>CO / C</th>
                            <th>C1</th>
                            <th>C2</th>
                            <th>C3</th>
                            <th>C4</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['CO1', 'CO2', 'CO3'] as $co)
                            <tr>
                                <th>{{ $co }}</th>
                                @for ($c = 1; $c <= 4; $c++)
                                    <td class="text-center">
                                        <input type="checkbox" name="tos[{{ $co }}][C{{ $c }}]" value="1">
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Questions Section -->
        @for ($i = 1; $i <= 4; $i++)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Question {{ $i }}</h5>
                </div>
                <div class="card-body">
                    <!-- Question -->
                    <div class="form-group row">
                        <label class="col-form-label col-md-2">Question</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="question{{ $i }}"></textarea>
                        </div>
                    </div>

                    <!-- Answer -->
                    <div class="form-group row">
                        <label class="col-form-label col-md-2">Answer</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="answer{{ $i }}"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endfor

        <!-- Submit -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Send to Department</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        height: 200,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                  alignleft aligncenter alignright alignjustify | \
                  bullist numlist outdent indent | removeformat | help'
    });
</script>
@endpush
