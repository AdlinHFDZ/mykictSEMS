<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exam;

class PDFController extends Controller
{
    public function download($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];
        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];

        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'tos', 'questionCount'))
                  ->setPaper('A4');

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();
        $canvas->page_text(280, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download("Exam_Paper_{$exam->course_code}.pdf");
    }



    public function view($id)
    {
        $exam = Exam::with('semester')->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];
        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];

        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'tos', 'questionCount'))
                  ->setPaper('A4');

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();
        $canvas->page_text(280, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->stream("Exam_Paper_{$exam->course_code}.pdf");
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
