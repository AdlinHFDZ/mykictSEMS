<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Exam Paper</title>
    <style>
        @page { margin: 100px 50px 80px; }
        body, h2, h3, h4, table, td, th, strong, p {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000 !important;
            line-height: 1.5;
        }
        .cover-logo { text-align: center; margin-bottom: 16px; }
        .cover-logo img { height: 110px; margin-bottom: 8px; }
        h2, h3, h4 { text-align: center; font-weight: bold; margin: 0; }
        h2 { font-size: 20px; margin-bottom: 5px; letter-spacing: 1px; }
        h3 { font-size: 15px; margin-bottom: 2px; letter-spacing: 0.2px; }
        h4 { font-size: 13px; margin-bottom: 2px; letter-spacing: 0.1px; }

        .kulliyyah-box {
            border: 2px solid black;
            padding: 4px;
            font-weight: bold;
            text-align: center;
            margin: 12px auto 15px auto;
            font-size: 13px;
            width: 100%;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 14px;
        }
        .info-table td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }
        .info-table td strong {
            display: inline-block;
            min-width: 100px;
        }

        .instructions, .warning-block {
            margin-top: 20px;
            text-align: center;
        }
        .instructions u {
            font-weight: bold;
            display: inline-block;
            margin-bottom: 8px;
        }
        .instructions p {
            margin: 3px 0;
        }

        .warning-block {
            font-weight: bold;
            font-size: 12px;
            color: #aa2e00;
            margin: 30px auto 10px auto;
            max-width: 90%;
        }

        .warning-block .note {
            color: red;
            font-style: italic;
            font-weight: normal;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 40px 0 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .question {
            margin-bottom: 28px;
        }

        .question p {
            margin: 4px 0;
        }
    </style>
</head>
<body>

@php
    function strip_leading_blocks($str) {
        return preg_replace('/^(\s*<(p|div|br)[^>]*>\s*)+/i', '', $str ?? '');
    }
@endphp

{{-- COVER PAGE --}}
<div style="page-break-inside: avoid;">
    <div class="cover-logo">
        <img src="{{ public_path('assets/img/iium-logo-exam.png') }}">
    </div>

    <h2>INTERNATIONAL ISLAMIC UNIVERSITY MALAYSIA</h2>
    <h3>END-OF-SEMESTER EXAMINATION</h3>
    <h4>SEMESTER {{ $exam->semester->name ?? 'N/A' }}</h4>

    <div class="kulliyyah-box">KULLIYYAH OF INFORMATION AND COMMUNICATION TECHNOLOGY</div>

    <table class="info-table">
        <tr>
            <td><strong>Programme</strong> : {{ $exam->programme ?? 'BIT/BCS' }}</td>
            <td><strong>Level of Stud</strong> : {{ $exam->level ?? 'UNDERGRADUATE' }}</td>
        </tr>
        <tr>
            <td><strong>Time</strong> : {{ $exam->exam_time ?? '-' }}</td>
            <td><strong>Date</strong> : {{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Duration</strong> : {{ $exam->duration ?? '-' }}</td>
            <td><strong>Section(s)</strong> : {{ $exam->section ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Course Code</strong> : {{ $exam->course_code ?? '-' }}</td>
            <td><strong>Total Page(s)</strong> : {{ $totalPages }} pages</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Course Title</strong> : {{ $exam->course_name ?? '-' }}</td>
        </tr>
    </table>

    <div class="instructions" style="margin-top: 30px; font-size: 13px;">
        <table style="margin: 0 auto; border-collapse: collapse; width: 90%; text-align: center;">
            <tr>
                <td colspan="2" style="font-weight: bold; text-decoration: underline; padding-bottom: 5px;">
                    INSTRUCTIONS TO CANDIDATES
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 5px 0;">
                    Please refrain from opening the question paper until instructed to do so.
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom: 5px;">
                    This question paper consists of {{ $totalPages }} pages, excluding the cover page.
                </td>
            </tr>

            @if (!empty($exam->instruction))
                <tr>
                    <td colspan="2" style="padding-top: 8px; text-align: center; font-style: italic;">
                        {!! nl2br(e($exam->instruction)) !!}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <div class="warning-block">
        <h4>WARNING</h4>
        <p>Cheating is strictly prohibited and will be subject to disciplinary action, including dismissal, as outlined in the Student Academic Performance Evaluation Regulations.</p>
        <p>This question paper, along with all used and unused rough/graph paper, must be submitted at the end of the examination. No examination materials may be removed from the examination hall.</p>
    </div>

    <h4 style="margin-top: 60px; text-align: center;">APPROVED BY</h4>
</div>

<div style="page-break-after: always;"></div>


{{-- QUESTIONS SECTION --}}
<div class="section-title">Exam Questions</div>
@foreach ($questions as $index => $q)
    <div class="question">
        <p><strong>Question {{ $index + 1 }}</strong></p>

        @if (!empty($q['sub_questions']))
            @foreach ($q['sub_questions'] as $subIdx => $subQ)
                <div style="margin-left: 25px; margin-bottom: 6px;">
                    <p style="margin: 0;">
                        <strong>{{ chr(97 + $loop->index) }})</strong>
                        {!! strip_leading_blocks($subQ['question'] ?? '') !!}
                    </p>
                    @if (!empty($subQ['mark']))
                        <p style="text-align: right; margin: 0 0 10px 0;">({{ $subQ['mark'] }} Marks)</p>
                    @endif
                </div>
            @endforeach
        @else
            <div style="margin-left: 25px; margin-bottom: 6px;">
                <p style="margin: 0;">
                    {!! strip_leading_blocks($q['question']) !!}
                </p>
                @if (!empty($q['mark']))
                    <p style="text-align: right; margin: 0 0 10px 0;">({{ $q['mark'] }} Marks)</p>
                @endif
            </div>
        @endif

        @php
            $totalMark = 0;
            if (!empty($q['sub_questions'])) {
                $totalMark = collect($q['sub_questions'])->sum(fn ($sub) => $sub['mark'] ?? 0);
            } else {
                $totalMark = $q['mark'] ?? 0;
            }
        @endphp
        <p style="text-align: right; font-weight: bold; margin-top: 5px;">[Total: {{ $totalMark }} marks]</p>
    </div>
@endforeach

{{-- PAGE FOOTER --}}
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->get_font("DejaVu Sans", "normal");
        $size = 10;
        $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $x = 265;
        $y = 820;
        $pdf->page_text($x, $y, $pageText, $font, $size, [0, 0, 0]);
    }
</script>

</body>
</html>
