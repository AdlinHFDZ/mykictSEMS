<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exam;

class PDFController extends Controller
{
    public function download($id)
    {
        $exam = Exam::with('semester')->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];

        $html = "
        <html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
                h2, h4 { text-align: center; margin: 0; }
                .metadata { margin: 10px 0 20px; }
                .question-block { margin-bottom: 40px; page-break-inside: avoid; }
                .line { border-bottom: 1px solid #000; height: 18px; margin: 4px 0; }
            </style>
        </head>
        <body>
            <h2>EXAM PAPER</h2>
            <h4>{$exam->course_name} ({$exam->course_code})</h4>

            <div class='metadata'>
                <p><strong>Section:</strong> {$exam->section}</p>
                <p><strong>Semester:</strong> " . ($exam->semester->name ?? '-') . "</p>
            </div>
        ";

        foreach ($questions as $index => $q) {
            if (!empty($q['question'])) {
                $mark = !empty($q['mark']) ? "<em>[{$q['mark']} marks]</em>" : "";
                $html .= "
                    <div class='question-block'>
                        <strong>Section " . ($index + 1) . ": Question " . ($index + 1) . "</strong><br>
                        <p>{$q['question']}</p>
                        <p>{$mark}</p>
                        " . str_repeat("<div class='line'></div>", 6) . "
                    </div>
                ";
            }
        }

        $html .= "</body></html>";

        return Pdf::loadHTML($html)->setPaper('A4')->download("Exam_Paper_{$exam->course_code}.pdf");
    }
}

















// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;
// use App\Models\Exam;

// class PDFController extends Controller
// {
//     /**
//      * Optional: Generate PDF from manual form submission (e.g., testing)
//      */
//     public function generate(Request $request)
//     {
//         $data = $request->all();

//         // Store the exam if needed (optional)
//         Exam::create([
//             'course_name' => $data['course_name'],
//             'course_id'   => $data['course_id'],
//             'section'     => $data['section'],
//             'questions'   => json_encode($data['questions'] ?? []), // expects 'questions' key
//         ]);

//         // Load a test PDF view (e.g., exam-template)
//         $questions = $data['questions'] ?? [];
//         $tos = []; // empty if not used in generate()
//         $exam = (object) $data;

//         $html = view('pdf.exam-template', compact('exam', 'questions', 'tos'))->render();
//         $pdf = Pdf::loadHTML($html);

//         return $pdf->download('Final-Exam.pdf');
//     }

//     /**
//      * Download the approved exam paper (used by General Office)
//      */
//     public function download($id)
//     {
//         $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);

//         $questions = json_decode($exam->questions, true) ?? [];
//         $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];

//         // ✅ Compile Blade view into HTML before passing to DomPDF
//         $html = view('pdf.exam-paper', compact('exam', 'questions', 'tos'))->render();
//         $pdf = Pdf::loadHTML($html);

//         return $pdf->download('Exam_Paper_' . $exam->course_code . '.pdf');
//     }
