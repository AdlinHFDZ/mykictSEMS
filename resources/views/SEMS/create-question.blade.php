
@extends('layouts.master')

@section('content')

<form action="{{ route('pdf.generate') }}" method="POST" target="_blank" id="examForm">
    @csrf
    <!-- tos -->
    <table class="table">
    <thead>
        <tr>
            <th>Question Number</th>
            <th>Spec1</th>
            <th>Spec2</th>
            <th>Spec3</th>
            <th>Spec4</th>
        </tr>
    </thead>
    <tbody>
        @for ($q = 1; $q <= 4; $q++)
            <tr>
                <td>Q{{ $q }}</td>
                @for ($s = 1; $s <= 4; $s++)
                    <td>
                        <input type="checkbox" name="tos[Q{{ $q }}][Spec{{ $s }}]" value="1">
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>
    <!-- tos -->



<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Final Exam</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="SEMS-dashboard" style="color: #000000; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000;">Create Question</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Course Information Section -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form action="#" id="courseInfoForm">
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course Name</label>
                            <div class="col-md-8">
                            <input type="text" name="course_name" class="form-control" placeholder="Enter Course Name">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course ID</label>
                            <div class="col-md-8">
                                <input type="text" name="course_id" placeholder="Enter Course ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Section</label>
                            <div class="col-md-8">
                                <input type="text" name="section" placeholder="Enter Section Number">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">TOS Table</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Question Number</th>
                                <th>Spec1</th>
                                <th>Spec2</th>
                                <th>Spec3</th>
                                <th>Spec4</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Q1</td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                            </tr>
                            <tr>
                                <td>Q2</td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                            </tr>
                            <tr>
                                <td>Q3</td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                            </tr>
                            <tr>
                                <td>Q4</td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                                <td><input type="checkbox"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

 <!-- Questions Section -->
<!-- Questions Section -->
@for ($i = 1; $i <= 4; $i++)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Question {{ $i }}</h5>
        </div>
        <div class="card-body">
            <!-- Question Input -->
            <div class="form-group row mb-3">
                <label for="question{{ $i }}" class="col-form-label col-md-2">Question</label>
                <div class="col-md-10">
                    <textarea id="question{{ $i }}" name="question{{ $i }}" class="tinymce form-control" placeholder="Enter question text..."></textarea>
                </div>
            </div>

            <!-- Answer Input -->
            <div class="form-group row">
                <label for="answer{{ $i }}" class="col-form-label col-md-2">Answer</label>
                <div class="col-md-10">
                    <textarea id="answer{{ $i }}" name="answer{{ $i }}" class="tinymce form-control" placeholder="Enter answer..."></textarea>
                </div>
            </div>
        </div>
    </div>
@endfor


 <!-- Submit Buttons -->
 <div class="form-group mb-0 row">
     <div class="col-md-10 offset-md-2">
         <button type="button" class="btn btn-secondary">Save Draft</button>
     </div>
 </div>
    <!-- Submit Button -->
    <div class="form-group mt-4 row">
        <div class="col-md-10 offset-md-2">
            <button type="submit" class="btn btn-success">Send to Department</button>
        </div>
    </div>
</div>

<script>
    function handleCompleteQuestion() {
        alert('Success! The questions have been reviewed and sent back to the department.');
    }
</script>
</div>

</form>

<!-- Include TinyMCE -->
<script src="{{ asset('assets/js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
 tinymce.init({
     selector: 'textarea.tinymce',
     plugins: 'lists link image table code',
     toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
     height: 300
 });
</script>

@endsection
