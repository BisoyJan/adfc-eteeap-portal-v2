<?php

namespace Database\Seeders;

use App\Models\CurriculumSubject;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // GE — General Education
            ['code' => 'GE-101', 'title' => 'Understanding the Self', 'units' => 3, 'category' => 'ge', 'year_level' => 1, 'semester' => 1],
            ['code' => 'GE-102', 'title' => 'Mathematics in the Modern World', 'units' => 3, 'category' => 'ge', 'year_level' => 1, 'semester' => 1],
            ['code' => 'GE-103', 'title' => 'Purposive Communication', 'units' => 3, 'category' => 'ge', 'year_level' => 1, 'semester' => 2],
            ['code' => 'GE-104', 'title' => 'Art Appreciation', 'units' => 3, 'category' => 'ge', 'year_level' => 1, 'semester' => 2],

            // PE — Physical Education
            ['code' => 'PE-101', 'title' => 'Physical Fitness and Wellness', 'units' => 2, 'category' => 'pe', 'year_level' => 1, 'semester' => 1],
            ['code' => 'PE-102', 'title' => 'Dance and Team Sports', 'units' => 2, 'category' => 'pe', 'year_level' => 1, 'semester' => 2],

            // NSTP
            ['code' => 'NSTP-101', 'title' => 'National Service Training Program 1', 'units' => 3, 'category' => 'nstp', 'year_level' => 1, 'semester' => 1],

            // Core — Computer Science/IT Foundation
            ['code' => 'CS-101', 'title' => 'Computer Programming 1 (Fundamentals)', 'units' => 3, 'category' => 'core', 'year_level' => 1, 'semester' => 1],
            ['code' => 'CS-102', 'title' => 'Computer Programming 2 (OOP)', 'units' => 3, 'category' => 'core', 'year_level' => 1, 'semester' => 2],
            ['code' => 'IT-101', 'title' => 'Introduction to Information Technology', 'units' => 3, 'category' => 'core', 'year_level' => 1, 'semester' => 1],
            ['code' => 'IT-102', 'title' => 'Discrete Mathematics', 'units' => 3, 'category' => 'core', 'year_level' => 1, 'semester' => 2],
            ['code' => 'IT-103', 'title' => 'Data Structures and Algorithms', 'units' => 3, 'category' => 'core', 'year_level' => 2, 'semester' => 1],
            ['code' => 'IT-104', 'title' => 'Information Management (Database Systems)', 'units' => 3, 'category' => 'core', 'year_level' => 2, 'semester' => 1],
            ['code' => 'IT-105', 'title' => 'Networking 1 (Fundamentals)', 'units' => 3, 'category' => 'core', 'year_level' => 2, 'semester' => 2],
            ['code' => 'IT-106', 'title' => 'Human-Computer Interaction', 'units' => 3, 'category' => 'core', 'year_level' => 2, 'semester' => 2],

            // Major — BSIT Professional Courses
            ['code' => 'IT-201', 'title' => 'Web Systems and Technologies', 'units' => 3, 'category' => 'major', 'year_level' => 2, 'semester' => 1],
            ['code' => 'IT-202', 'title' => 'Object-Oriented Analysis and Design', 'units' => 3, 'category' => 'major', 'year_level' => 2, 'semester' => 2],
            ['code' => 'IT-203', 'title' => 'Software Engineering', 'units' => 3, 'category' => 'major', 'year_level' => 3, 'semester' => 1],
            ['code' => 'IT-204', 'title' => 'Information Assurance and Security', 'units' => 3, 'category' => 'major', 'year_level' => 3, 'semester' => 1],
            ['code' => 'IT-205', 'title' => 'Capstone Project 1', 'units' => 3, 'category' => 'major', 'year_level' => 3, 'semester' => 2],
            ['code' => 'IT-206', 'title' => 'Capstone Project 2', 'units' => 3, 'category' => 'major', 'year_level' => 4, 'semester' => 1],
            ['code' => 'IT-207', 'title' => 'Systems Integration and Architecture', 'units' => 3, 'category' => 'major', 'year_level' => 3, 'semester' => 2],

            // Electives
            ['code' => 'IT-E1', 'title' => 'Web Application Development', 'units' => 3, 'category' => 'elective', 'year_level' => 3, 'semester' => 1],
            ['code' => 'IT-E2', 'title' => 'Mobile Application Development', 'units' => 3, 'category' => 'elective', 'year_level' => 3, 'semester' => 2],
            ['code' => 'IT-E3', 'title' => 'Network and Security Administration', 'units' => 3, 'category' => 'elective', 'year_level' => 4, 'semester' => 1],
        ];

        foreach ($subjects as $subject) {
            CurriculumSubject::updateOrCreate(
                ['code' => $subject['code']],
                array_merge($subject, [
                    'prerequisite_codes' => null,
                    'learning_outcomes' => null,
                    'is_active' => true,
                    'curriculum_version' => config('eteeap.curriculum.default_version'),
                ]),
            );
        }
    }
}
