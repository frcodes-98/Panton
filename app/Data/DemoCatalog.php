<?php

namespace App\Data;

class DemoCatalog
{
    public static function users(): array
    {
        return [
            self::user(1, 'a.tan', 'Aina Tan', 'IT', 'System Analyst', 'aina.tan@example.test', 'Enable', 'No', '2026-09-15 02:45:23', '2025-11-14 10:57:03', [6], [1, 6], [1]),
            self::user(2, 'm.hassan', 'Mei Hassan', 'Nursing', 'Nurse Manager', 'mei.hassan@example.test', 'Enable', 'No', '2026-09-14 08:12:04', '2026-09-14 17:40:22', [7], [2, 9], [2, 9]),
            self::user(3, 'r.ong', 'Ravi Ong', 'Pharmacy', 'Pharmacist', 'ravi.ong@example.test', 'Enable', 'No', '2026-09-13 09:02:11', '2026-09-13 18:05:41', [9], [3], [3]),
            self::user(4, 's.lim', 'Siti Lim', 'Dietary', 'Dietitian', 'siti.lim@example.test', 'Enable', 'Yes', '2026-08-02 11:20:00', '2026-08-02 16:10:18', [2], [6], [6]),
            self::user(5, 'j.wong', 'Johan Wong', 'Admin', 'Executive', 'johan.wong@example.test', 'Disable', 'No', '2026-07-19 10:00:01', '2026-07-19 12:44:55', [1], [7, 8], [7]),
            self::user(6, 'n.chua', 'Nadia Chua', 'IT', 'Support', 'nadia.chua@example.test', 'Enable', 'No', '2026-09-16 07:55:33', '2026-09-16 19:01:09', [6], [1], [1]),
            self::user(7, 'h.teo', 'Hana Teo', 'Nursing', 'Staff Nurse', 'hana.teo@example.test', 'Enable', 'No', '2026-09-12 06:40:12', '2026-09-12 14:22:47', [7], [4, 5], [4, 5]),
            self::user(8, 'k.goh', 'Kumar Goh', 'Radiology', 'Radiographer', 'kumar.goh@example.test', 'Enable', 'No', '2026-09-11 13:15:28', '2026-09-11 21:03:50', [13], [4], [4]),
            self::user(9, 'l.yap', 'Lina Yap', 'Laboratory', 'Scientist', 'lina.yap@example.test', 'Enable', 'No', '2026-09-10 09:48:16', '2026-09-10 18:12:03', [11], [5], [5]),
            self::user(10, 'f.rahman', 'Farid Rahman', 'Finance', 'Officer', 'farid.rahman@example.test', 'Disable', 'No', '2026-06-01 08:30:00', '2026-06-01 17:00:00', [12], [7], []),
            self::user(11, 'p.ng', 'Priya Ng', 'Admin', 'Coordinator', 'priya.ng@example.test', 'Enable', 'No', '2026-09-09 15:27:44', '2026-09-09 18:41:19', [1, 5], [3, 9], [3, 9]),
            self::user(12, 'd.koh', 'Daniel Koh', 'IT', 'Administrator', 'daniel.koh@example.test', 'Enable', 'No', '2026-09-17 01:11:05', '2026-09-17 04:20:36', [6], [6, 7, 8], [6, 7], [2]),
        ];
    }

    public static function groups(): array
    {
        $names = [
            ['A/M-Modify_A/M_Admin', 4],
            ['A/M-Modify_A/M_Dietary', 3],
            ['A/M-Modify_A/M_Doctor', 4],
            ['A/M-Modify_A/M_Gen', 23],
            ['A/M-Modify_A/M_HOD', 1],
            ['A/M-Modify_A/M_IT', 1],
            ['A/M-Modify_A/M_Nursing', 6],
            ['A/M-Modify_A/M_OT', 3],
            ['A/M-Modify_A/M_Phar', 1],
            ['A/M-Modify_A/M_PR', 3],
            ['A/M-Modify_A/M_Lab', 5],
            ['A/M-Modify_A/M_Finance', 2],
            ['A/M-Modify_A/M_Radiology', 4],
            ['A/M-Modify_A/M_Quality', 2],
            ['A/M-Modify_A/M_Emergency', 7],
            ['A/M-Modify_A/M_Physio', 3],
            ['A/M-Modify_A/M_CSSD', 2],
            ['A/M-Modify_A/M_Security', 4],
            ['A/M-Modify_A/M_Housekeeping', 8],
            ['A/M-Modify_A/M_Records', 3],
            ['A/M-Modify_A/M_Billing', 2],
            ['A/M-Modify_A/M_ICU', 6],
            ['A/M-Modify_A/M_CCU', 4],
            ['A/M-Modify_A/M_Paediatric', 5],
            ['A/M-Modify_A/M_OandG', 4],
            ['A/M-Modify_A/M_Ortho', 3],
            ['A/M-Modify_A/M_Eye', 2],
            ['A/M-Modify_A/M_ENT', 2],
            ['A/M-Modify_A/M_Dental', 2],
            ['A/M-Modify_A/M_Anaesthesia', 3],
            ['A/M-Modify_A/M_Rehab', 2],
            ['A/M-Modify_A/M_Social', 1],
            ['A/M-Modify_A/M_Infection', 2],
            ['A/M-Modify_A/M_Portering', 6],
            ['A/M-Modify_A/M_Ambulance', 2],
        ];

        return array_map(fn (array $group, int $index) => [
            'id' => $index + 1,
            'name' => $group[0],
            'total_users' => $group[1],
        ], $names, array_keys($names));
    }

    public static function workspaces(): array
    {
        return array_map(fn (string $name, int $index) => [
            'id' => $index + 1,
            'name' => $name,
            'repository' => $index % 4 === 0,
            'approver_ids' => $index < 3 ? [1, 12] : [],
            'control_ids' => $index === 0 ? [12] : [],
        ], self::workspaceNames(), array_keys(self::workspaceNames()));
    }

    public static function modules(): array
    {
        return [
            ['id' => 1, 'name' => 'Key Store', 'description' => 'Assign Function Security on User', 'status' => true],
            ['id' => 2, 'name' => 'Notification', 'description' => 'Manage notification for your business process.', 'status' => true],
            ['id' => 3, 'name' => 'Process Organizer', 'description' => 'Creating processes and business rules', 'status' => true],
            ['id' => 4, 'name' => 'Repository', 'description' => 'MCRE', 'status' => true],
            ['id' => 5, 'name' => 'System Control', 'description' => 'System Configuration', 'status' => true],
            ['id' => 6, 'name' => 'Tasks', 'description' => 'Starting, viewing and managing tasks', 'status' => true],
        ];
    }

    public static function widgets(): array
    {
        $names = array_slice(self::workspaceNames(), 0, 29);

        return array_map(fn (string $name, int $index) => [
            'id' => $index + 1,
            'name' => $name,
            'description' => 'MCRE',
            'status' => $name !== 'JCI',
            'height' => 240,
            'width' => 360,
            'module_id' => 4,
            'workspace_id' => $index + 1,
            'user_ids' => [],
            'group_ids' => [],
        ], $names, array_keys($names));
    }

    public static function settings(): array
    {
        $pairs = [
            ['AUTHENTICATION_REQUIRED', 'FALSE'],
            ['FILECOUNT', '645'],
            ['LOCK_FOLDER', 'c:/PantonSys/lock/'],
            ['LOGIN_PAGE', 'login'],
            ['MAIL_PORT', '587'],
            ['MAIL_SERVER', 'mail.example.test'],
            ['MAIL_USERNAME', 'panton@example.test'],
            ['SESSION_TIMEOUT', '30'],
            ['MAX_UPLOAD_MB', '20'],
            ['APP_TITLE', 'Panton EDMS'],
            ['DATE_FORMAT', 'Y-m-d'],
            ['TIMEZONE', 'Asia/Kuala_Lumpur'],
            ['PASSWORD_MIN_LENGTH', '8'],
            ['PASSWORD_EXPIRE_DAYS', '90'],
            ['AUDIT_ENABLED', 'TRUE'],
            ['DEFAULT_PAGE_SIZE', '10'],
            ['OBSOLETE_NOTICE_DAYS', '30'],
            ['ALLOW_DELEGATION', 'TRUE'],
            ['WIDGET_MAX_HEIGHT', '800'],
            ['WIDGET_MAX_WIDTH', '1200'],
            ['BACKUP_PATH', 'c:/PantonSys/backup/'],
            ['MARQUEE_ENABLED', 'FALSE'],
        ];

        return array_map(fn (array $pair, int $index) => [
            'id' => $index + 1,
            'key' => $pair[0],
            'value' => $pair[1],
        ], $pairs, array_keys($pairs));
    }

    public static function marquee(): array
    {
        return [
            'flag' => 'off',
            'text' => 'Welcome to new PANTON EDMS.',
        ];
    }

    public static function obsoleteNotices(): array
    {
        return [
            ['name' => 'Policy Procedure', 'version' => '3.2', 'expires' => '2026-10-12'],
            ['name' => 'MOH Guideline', 'version' => '2.0', 'expires' => '2026-11-02'],
            ['name' => 'Medical Acts', 'version' => '1.8', 'expires' => '2026-10-18'],
        ];
    }

    public static function desktop(): array
    {
        $apps = [
            'System Control' => 'wrench',
        ];

        $workspaces = [
            'Act Regulation By Law' => 'book',
            'Consultant Privileges' => 'badge',
            'Consultant On Leave' => 'calendar',
            'JCI' => 'badge',
            'JCI Policy' => 'clipboard',
            'MOH Guideline' => 'clipboard',
            'Medical Acts' => 'book',
            'PDPA' => 'shield',
            'Policy Procedure' => 'clipboard',
        ];

        $tile = fn (string $kind) => fn (string $icon, string $name) => [
            'name' => $name,
            'slug' => self::slug($name),
            'icon' => $icon,
            'kind' => $kind,
        ];

        return [
            ...array_map($tile('workspace'), $workspaces, array_keys($workspaces)),
            ...array_map($tile('app'), $apps, array_keys($apps)),
        ];
    }

    public static function workspaceNames(): array
    {
        return [
            'Act Regulation By Law',
            'Consultant Privileges',
            'Consultant On Leave',
            'JCI',
            'JCI Policy',
            'MOH Guideline',
            'Medical Acts',
            'PDPA',
            'Policy Procedure',
        ];
    }

    public static function slug(string $name): string
    {
        $slug = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $name));

        return trim($slug, '-');
    }

    private static function user(
        int $id,
        string $username,
        string $fullName,
        string $department,
        string $position,
        string $email,
        string $loginStatus,
        string $suspendLogin,
        string $lastLogin,
        string $lastLogout,
        array $groupIds,
        array $workspaceIds,
        array $widgetIds,
        array $delegateIds = [],
    ): array {
        return [
            'id' => $id,
            'username' => $username,
            'full_name' => $fullName,
            'department' => $department,
            'position' => $position,
            'email' => $email,
            'contact' => '',
            'fax' => '',
            'branch' => 'Main',
            'employee_no' => 'E'.(1000 + $id),
            'employee_code' => strtoupper(substr($department, 0, 2)).'-'.str_pad((string) $id, 2, '0', STR_PAD_LEFT),
            'user_type' => $id === 12 ? 'Admin' : ($id % 5 === 0 ? 'Editor' : 'View Only'),
            'login_status' => $loginStatus,
            'activation_date' => '2026-09-15',
            'disable_date' => '2027-09-15',
            'maximum_attempts' => 5,
            'suspend_login' => $suspendLogin,
            'last_login' => $lastLogin,
            'last_logout' => $lastLogout,
            'group_ids' => $groupIds,
            'workspace_ids' => $workspaceIds,
            'widget_ids' => $widgetIds,
            'delegate_ids' => $delegateIds,
        ];
    }
}
