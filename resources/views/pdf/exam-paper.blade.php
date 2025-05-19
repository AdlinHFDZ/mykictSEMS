<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Exam Paper</title>
    <style>
        @page {
            margin: 100px 50px 80px;
        }

        body, h2, h3, h4, table, td, th, strong, p {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000 !important;
            line-height: 1.5;
        }

        .cover-logo {
            text-align: center;
            margin-bottom: 16px;
        }
        .cover-logo img {
            height: 110px;
            margin-bottom: 8px;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 0;
            letter-spacing: 1px;
        }
        h3 {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
            margin-top: 0;
            letter-spacing: 0.2px;
        }
        h4 {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
            margin-top: 0;
            letter-spacing: 0.1px;
        }

        .exam-info-table {
            margin-left: auto;
            margin-right: auto;
            width: 70%;
            border: none;
            font-size: 15px;
            margin-top: 18px;
            margin-bottom: 16px;
        }
        .exam-info-table td {
            border: none;
            padding: 4px 8px 2px 0;
            text-align: left;
            vertical-align: top;
        }

        .exam-info-table tr td:first-child {
            width: 50%;
        }

        .info-highlight {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .instructions {
            text-align: center;
            font-style: italic;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-top: 35px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .question-block {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border-bottom: 1px dashed #999;
            padding-bottom: 8px;
        }

        .question-block strong {
            font-size: 14px;
        }

        .marks {
            font-style: italic;
            margin-top: 5px;
            color: #1e8e3e;
        }

        .warning-bottom {
            position: absolute;
            left: 0;
            bottom: 50px;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            color: #aa2e00;
            letter-spacing: 0.1px;
        }

        .answer-sheet-question {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .answer-sheet-line {
            border-bottom: 1px solid #222;
            height: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body style="position: relative;">

    {{-- COVER PAGE --}}
    <div class="cover-logo">
        <img src="{{ public_path('assets/img/iium-logo-exam.png') }}">
    </div>

    <h2>INTERNATIONAL ISLAMIC UNIVERSITY MALAYSIA</h2>
    <h3>END OF SEMESTER EXAMINATION</h3>
    <h4>SEMESTER {{ $exam->semester->name ?? 'N/A' }}</h4>
    <h4>KULLIYYAH OF INFORMATION AND COMMUNICATION TECHNOLOGY</h4>

    <table class="exam-info-table">
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

    <div class="info-highlight">
        This Question Paper Contains {{ $questionCount }} Question{{ $questionCount > 1 ? 's' : '' }}.
    </div>

    @if (!empty($exam->instruction))
        <div class="instructions">
            <u>INSTRUCTION(S) TO CANDIDATES</u><br>
            {!! nl2br(e($exam->instruction)) !!}
        </div>
    @endif

    <div class="warning-bottom">
        Any form of cheating or attempt to cheat is a serious offence<br>which may lead to dismissal.
    </div>

    <div style="page-break-after: always;"></div>

    {{-- QUESTIONS SECTION --}}
    <div class="section-title">Exam Questions</div>
    @foreach ($questions as $index => $q)
        @if (!empty($q['question']))
            <div class="question-block">
                <strong>Question {{ $index + 1 }}</strong>
                <p>{!! $q['question'] !!}</p>
                @if (!empty($q['mark']))
                    <div class="marks">[{{ $q['mark'] }} marks]</div>
                @endif
            </div>
        @endif
    @endforeach

    {{-- ANSWER SHEET --}}
    <div style="page-break-after: always;"></div>
    <div class="section-title">Answer Sheet</div>
    <p style="margin-bottom:12px;">Please write your answers below. Use additional sheets if necessary.</p>
    @for ($i = 1; $i <= $questionCount; $i++)
        <p class="answer-sheet-question">Question {{ $i }}:</p>
        <div class="answer-sheet-line"></div>
        <div class="answer-sheet-line"></div>
    @endfor

</body>
</html>
