<?php

namespace App\Enums;

enum ActivityType: string
{
    case ModularStudy = 'modular_study';
    case CaseAnalysis = 'case_analysis';
    case Research = 'research';
    case VideoPresentation = 'video_presentation';
    case Webinar = 'webinar';
    case SkillsDemo = 'skills_demo';
    case Reporting = 'reporting';

    public function label(): string
    {
        return match ($this) {
            self::ModularStudy => 'Modular Study',
            self::CaseAnalysis => 'Case Analysis',
            self::Research => 'Research Activity',
            self::VideoPresentation => 'Video Presentation',
            self::Webinar => 'Webinar Attendance',
            self::SkillsDemo => 'Skills Demonstration',
            self::Reporting => 'Reporting',
        };
    }
}
