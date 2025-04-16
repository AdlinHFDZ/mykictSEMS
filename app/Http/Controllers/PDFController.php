<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exam;

class PDFController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->all();
    
        // Save to database
        \App\Models\Exam::create([
            'course_name' => $data['course_name'],
            'course_id'   => $data['course_id'],
            'section'     => $data['section'],
            'questions'   => json_encode($data), // stores all question data
        ]);
    
        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.exam-template', compact('data'));
    
        return $pdf->download('Final-Exam.pdf');
    }
    
}
