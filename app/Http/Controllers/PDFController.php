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

        // Count only questions that are not empty
        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));

        // Estimate total pages: 1 cover + 1 per 2 questions (adjust logic if needed)
        $questionsPerPage = 2;
        $questionPages = ceil($questionCount / $questionsPerPage);
        $totalPages = 1 + $questionPages;

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'questionCount', 'totalPages'))
                  ->setPaper('A4');

        return $pdf->download("Exam_Paper_{$exam->course_code}.pdf");
    }

    public function view($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];

        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));
        $questionsPerPage = 2;
        $questionPages = ceil($questionCount / $questionsPerPage);
        $totalPages = 1 + $questionPages;

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'questionCount', 'totalPages'))
                  ->setPaper('A4');

        return $pdf->stream("Exam_Paper_{$exam->course_code}.pdf");
    }
}
