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

        // === First Pass: Generate Draft PDF ===
        $pdfDraft = Pdf::loadView('pdf.exam-paper', [
            'exam' => $exam,
            'questions' => $questions,
            'totalPages' => 'DRAFT', // Placeholder
            'questionCount' => self::countQuestionParts($questions)
        ])->setPaper('A4');

        // Save draft PDF to storage
        $draftPath = storage_path('app/public/exam_draft_' . uniqid() . '.pdf');
        $pdfDraft->save($draftPath);

        // === Count Actual Pages ===
        $parser = new \Smalot\PdfParser\Parser();
        $pdfFile = $parser->parseFile($draftPath);
        $details = $pdfFile->getDetails();
        $pageCount = $details['Pages'] ?? 1;

        // === Second Pass: Generate Final PDF with actual page count ===
        $pdfFinal = Pdf::loadView('pdf.exam-paper', [
            'exam' => $exam,
            'questions' => $questions,
            'totalPages' => $pageCount,
            'questionCount' => self::countQuestionParts($questions)
        ])->setPaper('A4');

        // Clean up the draft file
        @unlink($draftPath);

        // Download the final PDF
        return $pdfFinal->download("Exam_Paper_{$exam->course_code}.pdf");
    }

    public function view($id)
    {
        $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
        $questions = json_decode($exam->questions, true) ?? [];

        // === First Pass: Generate Draft PDF ===
        $pdfDraft = Pdf::loadView('pdf.exam-paper', [
            'exam' => $exam,
            'questions' => $questions,
            'totalPages' => 'DRAFT',
            'questionCount' => self::countQuestionParts($questions)
        ])->setPaper('A4');

        $draftPath = storage_path('app/public/exam_draft_' . uniqid() . '.pdf');
        $pdfDraft->save($draftPath);

        // === Count Actual Pages ===
        $parser = new \Smalot\PdfParser\Parser();
        $pdfFile = $parser->parseFile($draftPath);
        $details = $pdfFile->getDetails();
        $pageCount = $details['Pages'] ?? 1;

        // === Second Pass: Generate Final PDF with actual page count ===
        $pdfFinal = Pdf::loadView('pdf.exam-paper', [
            'exam' => $exam,
            'questions' => $questions,
            'totalPages' => $pageCount,
            'questionCount' => self::countQuestionParts($questions)
        ])->setPaper('A4');

        @unlink($draftPath);

        // Stream the final PDF in the browser
        return $pdfFinal->stream("Exam_Paper_{$exam->course_code}.pdf");
    }


public function downloadAnswerScheme($id)
{
    $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
    $questions = json_decode($exam->questions, true) ?? [];

    // First pass: generate draft
    $pdfDraft = Pdf::loadView('pdf.answer-scheme', [
        'exam' => $exam,
        'questions' => $questions,
        // no need for questionCount here unless needed in your view
    ])->setPaper('A4');

    $draftPath = storage_path('app/public/answer_scheme_draft_' . uniqid() . '.pdf');
    $pdfDraft->save($draftPath);

    // Count pages
    $parser = new \Smalot\PdfParser\Parser();
    $pdfFile = $parser->parseFile($draftPath);
    $details = $pdfFile->getDetails();
    $pageCount = $details['Pages'] ?? 1;

    // Second pass: final PDF with real page count if needed
    $pdfFinal = Pdf::loadView('pdf.answer-scheme', [
        'exam' => $exam,
        'questions' => $questions,
        'totalPages' => $pageCount,
    ])->setPaper('A4');

    @unlink($draftPath);

    return $pdfFinal->download("Answer_Scheme_{$exam->course_code}.pdf");
}

public function viewAnswerScheme($id)
{
    $exam = Exam::with(['createdBy', 'semester'])->findOrFail($id);
    $questions = json_decode($exam->questions, true) ?? [];

    // First pass: generate draft
    $pdfDraft = Pdf::loadView('pdf.answer-scheme', [
        'exam' => $exam,
        'questions' => $questions,
    ])->setPaper('A4');

    $draftPath = storage_path('app/public/answer_scheme_draft_' . uniqid() . '.pdf');
    $pdfDraft->save($draftPath);

    // Count pages
    $parser = new \Smalot\PdfParser\Parser();
    $pdfFile = $parser->parseFile($draftPath);
    $details = $pdfFile->getDetails();
    $pageCount = $details['Pages'] ?? 1;

    // Second pass: final PDF with real page count if needed
    $pdfFinal = Pdf::loadView('pdf.answer-scheme', [
        'exam' => $exam,
        'questions' => $questions,
        'totalPages' => $pageCount,
    ])->setPaper('A4');

    @unlink($draftPath);

    return $pdfFinal->stream("Answer_Scheme_{$exam->course_code}.pdf");
}

}
