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
        .exam-info-table { margin: 18px auto 16px auto; width: 70%; border: none; font-size: 15px; }
        .exam-info-table td { border: none; padding: 4px 8px 2px 0; text-align: left; vertical-align: top; }
        .info-highlight { font-size: 15px; font-weight: bold; text-align: center; margin: 10px 0; }
        .instructions { text-align: center; font-style: italic; margin: 30px 0 10px 0; }
        .section-title { text-align: center; font-weight: bold; font-size: 16px; margin-top: 20px; margin-bottom: 45px; letter-spacing: 1px; text-transform: uppercase; }
        .cover-footer-block { margin-top: 90px; width: 100%; text-align: center; }
        .warning-block { font-weight: bold; font-size: 12px; color: #aa2e00; letter-spacing: 0.1px; margin-bottom: 14px; }
        .cover-signatures { display: flex; justify-content: center; gap: 120px; font-size: 13px; margin-top: 25px; }
        .signature-box { display: inline-block; text-align: center; }
        .signature-title { font-weight: bold; text-decoration: underline; font-size: 14px; }
        .answer-sheet-question { font-weight: bold; margin-bottom: 3px; }
        .answer-sheet-line { border-bottom: 1px solid #222; height: 40px; margin-bottom: 10px; }
        .page-number { position: fixed; bottom: 8px; left: 0; width: 100%; text-align: center; font-size: 12px; color: #333; }
    </style>
</head>

<body style="position: relative;">

@php
    function strip_leading_blocks($str) {
        return preg_replace('/^(\s*<(p|div|br)[^>]*>\s*)+/i', '', $str ?? '');
    }
@endphp

    {{-- COVER PAGE --}}
    <div class="cover-logo">
        <img src="{{ public_path('assets/img/iium-logo-exam.png') }}">
    </div>
    <h2>INTERNATIONAL ISLAMIC UNIVERSITY MALAYSIA</h2>
    <h3>END OF SEMESTER EXAMINATION</h3>
    <h4>SEMESTER {{ $exam->semester->name ?? 'N/A' }}</h4>
    <h4>KULLIYYAH OF INFORMATION AND COMMUNICATION TECHNOLOGY</h4>
    <table class="exam-info-table">
        <tr><td><strong>Programme:</strong> ICT</td><td><strong>Level of Study:</strong> UG</td></tr>
        <tr>
            <td><strong>Time:</strong> {{ $exam->exam_time ?? '-' }}</td>
            <td><strong>Date:</strong> {{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr><td><strong>Duration:</strong> {{ $exam->duration ?? '-' }}</td><td></td></tr>
        <tr><td><strong>Course Code:</strong> {{ $exam->course_code }}</td><td><strong>Section(s):</strong> {{ $exam->section }}</td></tr>
        <tr><td><strong>Course Title:</strong> {{ $exam->course_name }}</td><td></td></tr>
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

    <div class="cover-footer-block">
        <div class="warning-block">
            Any form of cheating or attempt to cheat is a serious offence<br>
            which may lead to dismissal.
        </div>
        <div class="cover-signatures">
            <div class="signature-box">
                <span class="signature-title">PREPARED BY:</span>
                <div style="margin-top: 15px;">
                    {{ $exam->ccAssignment && $exam->ccAssignment->user ? strtoupper($exam->ccAssignment->user->name) : 'N/A' }}<br>
                    Course Coordinator
                </div>
            </div>
            <div class="signature-box">
                <span class="signature-title">APPROVED BY:</span>
                <div style="margin-top: 15px;">
                    {{ $exam->approvedBy && $exam->approvedBy->name ? strtoupper($exam->approvedBy->name) : 'N/A' }}<br>
                    Head of Department
                </div>
            </div>
        </div>
    </div>

    <div style="page-break-after: always;"></div>

    {{-- QUESTIONS SECTION --}}
    <div class="section-title">Exam Questions</div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
        @foreach ($questions as $index => $q)
            @if (!empty($q['question']))
                <!-- Main Question Row: number + question + mark -->
                <tr>
                    <td style="width: 2%; font-weight: bold; vertical-align: top;">
                        {{ $index + 1 }}.
                    </td>
                    <td style="width: 83%; vertical-align: top;">
                        {!! strip_leading_blocks($q['question']) !!}
                    </td>
                    <td style="width: 15%; text-align: right; font-weight: bold; vertical-align: top;">
                        @if (!empty($q['mark'])) ({{ $q['mark'] }}) @endif
                    </td>
                </tr>
                @if (!empty($q['sub_questions']))
                    @foreach ($q['sub_questions'] as $subIdx => $subQ)
                        <tr>
                            <td></td>
                            <td style="padding-left: 32px;">
                                <span style="font-weight: bold;">
                                    {{ is_numeric($subIdx) ? chr(97 + $loop->index) : $subIdx }})
                                </span>
                                <span style="margin-left:8px;">
                                    {!! strip_leading_blocks($subQ['question'] ?? '') !!}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: bold;">
                                @if (!empty($subQ['mark'])) ({{ $subQ['mark'] }}) @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endif
        @endforeach
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>
    <div class="section-title">Answer Sheet</div>
    <p style="margin-bottom:12px;">Please write your answers below. Use additional sheets if necessary.</p>
    @for ($i = 1; $i <= $questionCount; $i++)
        <p class="answer-sheet-question">Question {{ $i }}:</p>
        <div class="answer-sheet-line"></div>
        <div class="answer-sheet-line"></div>
    @endfor

    {{-- Page Number (footer, all pages except cover page) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Sans, Arial, Helvetica, sans-serif", "normal");
            $size = 10;
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $marginLeft = 50; // match your @page margin
            $marginRight = 50;
            $pageWidth = 595; // A4 width in points
            $contentWidth = $pageWidth - $marginLeft - $marginRight;
            $textWidth = $fontMetrics->getTextWidth($pageText, $font, $size);

            $x = 265;
            $y = 820; // adjust for your footer (bottom margin + a bit up)

            // Use on all pages or add condition for non-cover pages
            $pdf->page_text($x, $y, $pageText, $font, $size, [0,0,0]);
        }
    </script>

</body>
</html>
