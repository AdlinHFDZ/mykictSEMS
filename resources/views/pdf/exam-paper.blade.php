<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Exam Paper</title>
    <style>
        @page {
            margin: 100px 50px 80px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            vertical-align: top;
            padding: 4px;
        }

        h2, h3, h4 {
            text-align: center;
            margin: 4px 0;
        }

        .instructions {
            text-align: center;
            font-style: italic;
            margin-top: 30px;
        }

        .bold-center {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }

        .warning {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            color: black;
            margin-top: 40px;
        }

        .question-block {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .marks {
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    {{-- COVER PAGE --}}
    <div style="text-align: center;">
        <img src="{{ public_path('images/iium-logo.png') }}" width="80" style="margin-bottom: 10px;">
    </div>

    <h2>INTERNATIONAL ISLAMIC UNIVERSITY MALAYSIA</h2>
    <h3>END OF SEMESTER EXAMINATION</h3>
    <h4>SEMESTER {{ $exam->semester->name ?? 'N/A' }}</h4>
    <h4>KULLIYYAH OF INFORMATION AND COMMUNICATION TECHNOLOGY</h4>

    <table>
        <tr>
            <td><strong>Programme:</strong> ICT</td>
            <td><strong>Level of Study:</strong> UG</td>
        </tr>
        <tr>
            <td><strong>Time:</strong> {{ $exam->exam_time ?? '-' }}</td>
            <td><strong>Date:</strong> {{ optional($exam->exam_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Duration:</strong> {{ $exam->duration ?? '-' }}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Course Code:</strong> {{ $exam->course_code }}</td>
            <td><strong>Section(s):</strong> {{ $exam->section }}</td>
        </tr>
        <tr>
            <td><strong>Course Title:</strong> {{ $exam->course_name }}</td>
            <td></td>
        </tr>
    </table>

    <p><strong>
        This Question Paper Contains {{ $questionCount }} Question{{ $questionCount > 1 ? 's' : '' }}.
    </strong></p>

    @if (!empty($exam->instruction))
        <div class="instructions">
            <u>INSTRUCTION(S) TO CANDIDATES</u><br>
            {!! nl2br(e($exam->instruction)) !!}
        </div>
    @endif

    <div class="warning">
        <p>Any form of cheating or attempt to cheat is a serious offence<br>which may lead to dismissal.</p>
    </div>

    <div style="page-break-after: always;"></div>

    {{-- QUESTIONS SECTION --}}
    <h3 style="text-align: center;">EXAM QUESTIONS</h3>

    @foreach ($questions as $index => $q)
        @if (!empty($q['question']))
            <div class="question-block">
                <strong>Section {{ $index + 1 }}: Question {{ $index + 1 }}</strong>
                <p>{!! $q['question'] !!}</p>

                @if (!empty($q['mark']))
                    <div class="marks">[{{ $q['mark'] }} marks]</div>
                @endif
            </div>
        @endif
    @endforeach

</body>
</html>
