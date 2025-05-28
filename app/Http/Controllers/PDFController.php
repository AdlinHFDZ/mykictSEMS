<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exam;

class PDFController extends Controller
{
    // Count all question parts: main, sub, breakdown
    private static function countQuestionParts($questions)
    {
        $count = 0;
        foreach ($questions as $q) {
            if (!empty($q['question'])) $count++; // Main question
            if (!empty($q['sub_questions'])) {
                foreach ($q['sub_questions'] as $sub) {
                    if (!empty($sub['question'])) $count++; // Sub-question
                    if (!empty($sub['breakdowns'])) {
                        foreach ($sub['breakdowns'] as $b) {
                            if (!empty($b['question'])) $count++; // Breakdown
                        }
                    }
                }
            }
        }
        return $count;
    }

    public function download($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];

        // Use the improved count
        $questionCount = self::countQuestionParts($questions);

        // Estimate total pages: 1 cover + 1 per 2 question parts
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

        $questionCount = self::countQuestionParts($questions);
        $questionsPerPage = 2;
        $questionPages = ceil($questionCount / $questionsPerPage);
        $totalPages = 1 + $questionPages;

        $pdf = Pdf::loadView('pdf.exam-paper', compact('exam', 'questions', 'questionCount', 'totalPages'))
                  ->setPaper('A4');

        return $pdf->stream("Exam_Paper_{$exam->course_code}.pdf");
    }
}
