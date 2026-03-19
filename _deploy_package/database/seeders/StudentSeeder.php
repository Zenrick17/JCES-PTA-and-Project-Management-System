<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all parent profiles
        $parents = DB::table('parents')->get();

        if ($parents->isEmpty()) {
            $this->command->warn('No parents found. Please run PlaceholderDataSeeder first.');
            return;
        }

        // Sample student data - each parent will get 1-3 students
        $studentNames = [
            // Students for Parent 1 (Carmen Reyes)
            ['name' => 'Princess Sanalp', 'grade' => '6', 'section' => 'Aquino', 'gender' => 'female'],
            ['name' => 'Nicole Nacion', 'grade' => '4', 'section' => 'Duterte', 'gender' => 'female'],

            // Students for Parent 2 (Miguel Fernandez)
            ['name' => 'Gatchie Tumulak', 'grade' => '5', 'section' => 'Marcos', 'gender' => 'female'],
            ['name' => 'Kristel Faith Gawan', 'grade' => '3', 'section' => 'Garcia', 'gender' => 'female'],
            ['name' => 'Aiza Orilla', 'grade' => '2', 'section' => 'Arroyo', 'gender' => 'female'],

            // Students for Parent 3 (Sofia Mendoza)
            ['name' => 'Ed Lorenz Bersamin', 'grade' => '1', 'section' => 'Ramos', 'gender' => 'male'],
        ];

        // Student assignments per parent (1-3 students each)
        $parentStudentAssignments = [
            0 => [0, 1],        // Parent 1 gets 2 students
            1 => [2, 3, 4],     // Parent 2 gets 3 students
            2 => [5],           // Parent 3 gets 1 student
        ];

        $studentIds = [];
        $academicYear = '2025';

        // Create students
        foreach ($studentNames as $index => $student) {
            // Check if student already exists
            $existing = DB::table('students')->where('student_name', $student['name'])->first();

            if ($existing) {
                $studentIds[$index] = $existing->studentID;
            } else {
                $studentIds[$index] = DB::table('students')->insertGetId([
                    'student_name' => $student['name'],
                    'grade_level' => $student['grade'],
                    'section' => $student['section'],
                    'enrollment_status' => 'active',
                    'academic_year' => $academicYear,
                    'enrollment_date' => now()->subMonths(rand(1, 6)),
                    'birth_date' => now()->subYears(rand(6, 12))->subMonths(rand(1, 12)),
                    'gender' => $student['gender'],
                    'created_date' => now(),
                    'updated_date' => now(),
                ]);
            }
        }

        // Create parent-student relationships
        $parentIndex = 0;
        foreach ($parents as $parent) {
            if (!isset($parentStudentAssignments[$parentIndex])) {
                $parentIndex++;
                continue;
            }

            $studentIndices = $parentStudentAssignments[$parentIndex];
            $relationshipTypes = ['mother', 'father', 'guardian'];

            foreach ($studentIndices as $i => $studentIndex) {
                // Check if relationship already exists
                $existingRelation = DB::table('parent_student_relationships')
                    ->where('parentID', $parent->parentID)
                    ->where('studentID', $studentIds[$studentIndex])
                    ->first();

                if (!$existingRelation) {
                    DB::table('parent_student_relationships')->insert([
                        'parentID' => $parent->parentID,
                        'studentID' => $studentIds[$studentIndex],
                        'relationship_type' => $relationshipTypes[array_rand($relationshipTypes)],
                        'is_primary_contact' => $i === 0, // First student assignment is primary
                        'created_date' => now(),
                    ]);
                }
            }

            $parentIndex++;
        }

        $this->command->info('✓ Students seeded successfully!');
        $this->command->info('  - Created ' . count($studentIds) . ' students');
        $this->command->info('  - Created parent-student relationships');
    }
}
