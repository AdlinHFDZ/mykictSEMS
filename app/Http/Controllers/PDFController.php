<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exam;

class PDFController extends Controller
{
    /**
     * Download the approved exam paper as PDF.
     */
    public function download($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];
        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'questionCount'))
                  ->setPaper('A4');

        return $pdf->download("Exam_Paper_{$exam->course_code}.pdf");
    }

    /**
     * Stream the approved exam paper as PDF in-browser.
     */
    public function view($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];
        $questionCount = count(array_filter($questions, fn($q) => !empty($q['question'])));

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'questionCount'))
                  ->setPaper('A4');

        return $pdf->stream("Exam_Paper_{$exam->course_code}.pdf");
    }
}
