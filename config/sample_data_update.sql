-- Sample Data Update Script for Enhanced Officer Submission
-- This script adds sample OPR and template links to existing report types

-- Update existing report types with OPR and deadline information
-- Modify these according to your actual report types

-- Example 1: Budget Report
UPDATE report_types 
SET opr = 'Budget Officer - Finance Department',
    template_link = 'https://drive.google.com/file/d/1234567890abcdef/view',
    deadline_day = 10,
    description = 'Monthly budget allocation and expenditure report'
WHERE report_code = 'BR-001';

-- Example 2: Quarterly Performance Report
UPDATE report_types 
SET opr = 'Performance Manager - Planning Office',
    template_link = 'https://drive.google.com/file/d/0987654321fedcba/view',
    deadline_day = 15,
    description = 'Quarterly performance metrics and achievements'
WHERE report_code = 'QPR-001';

-- Example 3: Infrastructure Report
UPDATE report_types 
SET opr = 'Infrastructure Officer - Engineering Department',
    template_link = 'https://drive.google.com/file/d/abcdef1234567890/view',
    deadline_day = 20,
    description = 'Infrastructure projects and maintenance report'
WHERE report_code = 'IR-001';

-- If you want to set a default OPR for all NULL values:
UPDATE report_types 
SET opr = 'General Admin - LGU Office'
WHERE opr IS NULL OR opr = '';

-- If you want to set a default deadline day for all NULL values:
UPDATE report_types 
SET deadline_day = 15
WHERE deadline_day IS NULL;

-- Query to see all report types with their new fields
SELECT 
    report_type_id,
    report_code,
    report_title,
    opr,
    deadline_day,
    CASE 
        WHEN template_link IS NOT NULL THEN 'Yes'
        ELSE 'No'
    END as has_template,
    is_active
FROM report_types
ORDER BY report_code;
