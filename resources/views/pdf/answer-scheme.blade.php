<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Answer Scheme</title>
    <style>
        @page { margin: 100px 50px 80px; }
        body, h2, h3, h4, table, td, th, strong, p {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000 !important;
            line-height: 1.5;
            font-weight: normal;
        }
        .cover-logo { text-align: center; margin-bottom: 16px; }
        .cover-logo img { height: 110px; margin-bottom: 8px; }
        h2, h3, h4 { text-align: center; font-weight: bold; margin: 0; }
        h2 { font-size: 20px; margin-bottom: 5px; letter-spacing: 1px; }
        h3 { font-size: 15px; margin-bottom: 2px; letter-spacing: 0.2px; }
        h4 { font-size: 13px; margin-bottom: 2px; letter-spacing: 0.1px; }
        .section-title { font-size: 16px; font-weight: bold; margin: 40px 0 20px; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
        .answer-scheme { margin-bottom: 28px; }
        .main-a { margin-bottom: 10px; }
        .sub-a { margin-left: 28px; margin-bottom: 5px; }
        .breakdown-a { margin-left: 56px; margin-bottom: 2px; }
        .answer-label { font-weight: bold; }
        .answer-content { display: inline-block; margin-left: 4px; }
    </style>
</head>
<body>

@php
    $roman = ['i','ii','iii','iv','v','vi','vii','viii','ix','x'];
    if (!function_exists('strip_leading_blocks')) {
        function strip_leading_blocks($str) {
            return preg_replace('/^(\s*<(p|div|br)[^>]*>\s*)+/i', '', $str ?? '');
        }
    }
@endphp

{{-- COVER PAGE --}}
<div style="page-break-inside: avoid;">
    <div class="cover-logo">
        <img src="{{ public_path('assets/img/iium-logo-exam.png') }}">
    </div>
    <h2>INTERNATIONAL ISLAMIC UNIVERSITY MALAYSIA</h2>
    <h3>ANSWER SCHEME</h3>
    <h4>SEMESTER {{ $exam->semester->name ?? 'N/A' }}</h4>
    <p style="text-align:center; font-weight:bold; margin:12px auto 15px auto; font-size:13px;">
        KULLIYYAH OF INFORMATION AND COMMUNICATION TECHNOLOGY
    </p>
    <table class="info-table">
        <tr>
            <td class="info-label">Course Code</td>
            <td class="info-colon">:</td>
            <td class="info-value">{!! $exam->course_code ?? '&mdash;' !!}</td>
            <td class="info-label">Course Title</td>
            <td class="info-colon">:</td>
            <td class="info-value">{!! $exam->course_name ?? '&mdash;' !!}</td>
        </tr>
        <tr>
            <td class="info-label">Date</td>
            <td class="info-colon">:</td>
            <td class="info-value">
                {!! $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d F Y') : '&mdash;' !!}
            </td>
            <td class="info-label">Prepared By</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $exam->createdBy->name ?? '-' }}</td>
        </tr>
    </table>
    <h4 style="margin-top: 60px; text-align: center;">FOR INTERNAL USE ONLY</h4>
</div>
<div style="page-break-after: always;"></div>

<div class="section-title">ANSWER SCHEME</div>

@foreach ($questions as $qIdx => $q)
    <div class="answer-scheme">
        {{-- MAIN ANSWER --}}
        <div class="main-a">
            <span class="answer-label">Question {{ $loop->iteration }}</span>
            <span class="answer-content">{!! strip_leading_blocks($q['answer'] ?? '<em>No answer provided.</em>') !!}</span>
        </div>
        {{-- SUB-ANSWERS --}}
        @if (!empty($q['sub_questions']))
            @foreach ($q['sub_questions'] as $subQ)
                <div class="sub-a">
                    <span class="answer-label">{{ chr(97 + $loop->index) }})</span>
                    <span class="answer-content">{!! strip_leading_blocks($subQ['answer'] ?? '<em>No answer provided.</em>') !!}</span>
                </div>
                {{-- BREAKDOWN ANSWERS --}}
                @if (!empty($subQ['breakdowns']))
                    @foreach ($subQ['breakdowns'] as $bQ)
                        <div class="breakdown-a">
                            <span class="answer-label">{{ $roman[$loop->index] }})</span>
                            <span class="answer-content">{!! strip_leading_blocks($bQ['answer'] ?? '<em>No answer provided.</em>') !!}</span>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
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
