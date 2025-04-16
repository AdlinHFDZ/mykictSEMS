<!DOCTYPE html>
<html>
<head>
    <title>Final Exam PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h2 { text-align: center; }
        .question { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Final Exam</h2>

    <p><strong>Course Name:</strong> {{ $data['course_name'] }}</p>
    <p><strong>Course ID:</strong> {{ $data['course_id'] }}</p>
    <p><strong>Section:</strong> {{ $data['section'] }}</p>

    <hr>

    @for ($i = 1; $i <= 4; $i++)
        <div class="question">
            <p><strong>Q{{ $i }}:</strong> {!! $data["question$i"] ?? '-' !!}</p>
            <p><strong>Answer:</strong> {!! $data["answer$i"] ?? '-' !!}</p>
        </div>
    @endfor
</body>
</html>
