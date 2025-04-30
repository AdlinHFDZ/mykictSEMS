<?php

namespace App\Enums;

enum ExamStatus: string
{
    case ASSIGN_COORDINATOR = 'assign Coordinator';
    case DRAFT_QUESTION = 'draft question';
    case DRAFT_QUESTION_COMPLETE = 'draft question complete'; // ✅ Add this line
    case VETTING = 'vetting';
    case VETTED = 'vetted';
    case PENDING_APPROVAL = 'pending approval';
    case APPROVED = 'approved';
    case REVISE_REQUESTED = 'revise requested';
}

