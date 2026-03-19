<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create views for common reporting queries
        DB::statement("DROP VIEW IF EXISTS active_parent_students");
        DB::statement("
            CREATE VIEW active_parent_students AS
            SELECT
                p.parentID,
                p.first_name as parent_first_name,
                p.last_name as parent_last_name,
                p.email,
                p.phone,
                s.studentID,
                s.student_name,
                s.grade_level,
                s.section,
                psr.relationship_type
            FROM parents p
            JOIN parent_student_relationships psr ON p.parentID = psr.parentID
            JOIN students s ON psr.studentID = s.studentID
            WHERE p.account_status = 'active' AND s.enrollment_status = 'active'
        ");

        DB::statement("DROP VIEW IF EXISTS project_financial_summary");
        DB::statement("
            CREATE VIEW project_financial_summary AS
            SELECT
                p.projectID,
                p.project_name,
                p.target_budget,
                p.current_amount,
                (p.current_amount / p.target_budget * 100) as completion_percentage,
                COUNT(pc.contributionID) as total_contributions,
                p.project_status
            FROM projects p
            LEFT JOIN project_contributions pc ON p.projectID = pc.projectID
            WHERE pc.payment_status = 'completed' OR pc.payment_status IS NULL
            GROUP BY p.projectID, p.project_name, p.target_budget, p.current_amount, p.project_status
        ");

        // Create trigger for updating project current_amount
        DB::statement("DROP TRIGGER IF EXISTS update_project_amount_after_contribution");
        DB::statement("
            CREATE TRIGGER update_project_amount_after_contribution
            AFTER INSERT ON project_contributions
            FOR EACH ROW
            BEGIN
                UPDATE projects
                SET current_amount = (
                    SELECT COALESCE(SUM(contribution_amount), 0)
                    FROM project_contributions
                    WHERE projectID = NEW.projectID AND payment_status = 'completed'
                )
                WHERE projectID = NEW.projectID;
            END
        ");

        // Create trigger for updating project amount when contribution status changes
        DB::statement("DROP TRIGGER IF EXISTS update_project_amount_after_contribution_update");
        DB::statement("
            CREATE TRIGGER update_project_amount_after_contribution_update
            AFTER UPDATE ON project_contributions
            FOR EACH ROW
            BEGIN
                UPDATE projects
                SET current_amount = (
                    SELECT COALESCE(SUM(contribution_amount), 0)
                    FROM project_contributions
                    WHERE projectID = NEW.projectID AND payment_status = 'completed'
                )
                WHERE projectID = NEW.projectID;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::statement("DROP TRIGGER IF EXISTS update_project_amount_after_contribution_update");
        DB::statement("DROP TRIGGER IF EXISTS update_project_amount_after_contribution");

        // Drop views
        DB::statement("DROP VIEW IF EXISTS project_financial_summary");
        DB::statement("DROP VIEW IF EXISTS active_parent_students");
    }
};
