<!DOCTYPE html>
<html>
<head>
    <title>Exam Paper</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
        }
        .metadata {
            margin: 10px 0 20px;
        }
    </style>
</head>
<body>

    <h2>EXAM PAPER</h2>
    <h4>{{ $exam->course_name }} ({{ $exam->course_code }})</h4>

    <div class="metadata">
        <p><strong>Section:</strong> {{ $exam->section }}</p>
        <p><strong>Semester:</strong> {{ $exam->semester->name ?? '-' }}</p>
    </div>

    {!! $htmlBlocks !!}

</body>
</html>






















<!-- <!DOCTYPE html>
<html>
<head>
    <title>Exam Paper - {{ $exam->course_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h3 { text-align: center; margin-bottom: 10px; }
        .metadata { margin-bottom: 20px; }
        .question-block { margin-bottom: 50px; page-break-inside: avoid; }
        .question-title { font-weight: bold; margin-bottom: 5px; }
        .mark-info { font-style: italic; font-size: 11px; margin-top: 5px; }
        .answer-line { border-bottom: 1px solid #000; margin: 5px 0; height: 18px; }
        .answer-space { margin-top: 10px; }
    </style>
</head>
<body>

    <h1>EXAM PAPER</h1>
    <h3>{{ $exam->course_name }} ({{ $exam->course_code }})</h3>

    <div class="metadata">
        <p><strong>Section:</strong> {{ $exam->section }}</p>
        <p><strong>Semester:</strong> {{ $exam->semester->name ?? '-' }}</p>
    </div>

    @foreach($questions as $index => $item)
        @if(!empty($item['question']))
            <div class="question-block">
                <div class="question-title">
                    Section {{ $index + 1 }}: Question {{ $index + 1 }}
                </div>

                <p>{{ $item['question'] }}</p>

                @if(!empty($item['mark']))
                    <p class="mark-info">[{{ $item['mark'] }} marks]</p>
                @endif

                <div class="answer-space">
                    @for($i = 0; $i < 6; $i++)
                        <div class="answer-line"></div>
                    @endfor
                </div>
            </div>
        @endif
    @endforeach

</body>
</html> -->
