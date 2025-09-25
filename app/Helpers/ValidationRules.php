<?php

namespace App\Helpers;

use App\Enums\Gender;
use App\Enums\RelationshipType;
use Illuminate\Validation\Rule;

class ValidationRules
{
    // Common field validation rules
    public const PERSONAL_NAME_RULES = 'required|min:2|max:50';

    public const OPTIONAL_NAME_RULES = 'nullable|max:50';

    public const EMAIL_RULES = 'required|email|max:255';

    public const PASSWORD_RULES = 'required|min:8|confirmed';

    public const OPTIONAL_PASSWORD_RULES = 'nullable|min:8|confirmed';

    public const PHONE_RULES = 'nullable|max:15';

    public const REQUIRED_PHONE_RULES = 'required|max:15';

    public const DATE_RULES = 'required|date|before:today';

    public const PROFILE_IMAGE_RULES = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';

    public const NUMERIC_RULES = 'nullable|numeric|min:0';

    public const REQUIRED_NUMERIC_RULES = 'required|numeric|min:0';

    public const BOOLEAN_RULES = 'boolean';

    public const GRADE_LEVEL_RULES = 'required|integer|min:1|max:13';

    public const ADDRESS_RULES = 'nullable|max:255';

    /**
     * Get common person validation rules
     */
    public static function getPersonRules(bool $isUpdate = false, ?int $userId = null): array
    {
        if ($isUpdate && $userId) {
            $emailRule = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id'),
            ];
        } else {
            $emailRule = self::EMAIL_RULES . '|unique:users,email';
        }

        return [
            'first_name' => self::PERSONAL_NAME_RULES,
            'last_name' => self::PERSONAL_NAME_RULES,
            'middle_name' => self::OPTIONAL_NAME_RULES,
            'date_of_birth' => self::DATE_RULES,
            'gender' => 'required|' . Gender::getValidationRule(),
            'email' => $emailRule,
            'password' => $isUpdate ? self::OPTIONAL_PASSWORD_RULES : self::PASSWORD_RULES,
            'profile_image' => self::PROFILE_IMAGE_RULES,
            'mobile_phone' => self::PHONE_RULES,
            'home_phone' => self::PHONE_RULES,
            'nationality' => self::OPTIONAL_NAME_RULES,
            'address_line1' => self::ADDRESS_RULES,
            'address_line2' => self::ADDRESS_RULES,
            'city' => self::OPTIONAL_NAME_RULES,
            'state' => self::OPTIONAL_NAME_RULES,
            'postal_code' => 'nullable|max:20',
            'country' => self::OPTIONAL_NAME_RULES,
        ];
    }

    /**
     * Get student specific validation rules
     */
    public static function getStudentRules(bool $isUpdate = false, ?int $userId = null): array
    {
        $rules = self::getPersonRules($isUpdate, $userId);

        return array_merge($rules, [
            'grade_level' => self::GRADE_LEVEL_RULES,
            'class_id' => 'nullable|exists:school_classes,id',
            'section' => 'nullable|max:10',
            'enrollment_date' => 'required|date',
            'religion' => self::OPTIONAL_NAME_RULES,
            'home_language' => self::OPTIONAL_NAME_RULES,
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'parents' => 'nullable|array',
            'parents.*' => 'exists:parents,parent_id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
    }

    /**
     * Get teacher specific validation rules
     */
    public static function getTeacherRules(bool $isUpdate = false, ?int $userId = null): array
    {
        $rules = self::getPersonRules($isUpdate, $userId);

        return array_merge($rules, [
            'qualification' => 'required|max:255',
            'specialization' => 'nullable|max:255',
            'experience_years' => self::NUMERIC_RULES,
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'is_class_teacher' => self::BOOLEAN_RULES,
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
    }

    /**
     * Get parent validation rules for array inputs
     */
    public static function getParentArrayRules(): array
    {
        return [
            'parent_first_name' => 'nullable|array',
            'parent_first_name.*' => 'required_with:parent_last_name.*|max:50',
            'parent_last_name' => 'nullable|array',
            'parent_last_name.*' => 'required_with:parent_first_name.*|max:50',
            'parent_middle_name' => 'nullable|array',
            'parent_middle_name.*' => 'nullable|max:50',
            'parent_gender' => 'nullable|array',
            'parent_gender.*' => 'required_with:parent_first_name.*|' . Gender::getValidationRule(),
            'parent_relationship_type' => 'nullable|array',
            'parent_relationship_type.*' => 'required_with:parent_first_name.*|' . RelationshipType::getValidationRule(),
            'parent_mobile_phone' => 'nullable|array',
            'parent_mobile_phone.*' => 'required_with:parent_first_name.*|max:15',
            'parent_email' => 'nullable|array',
            'parent_email.*' => 'nullable|email|max:100',
            'parent_date_of_birth' => 'nullable|array',
            'parent_date_of_birth.*' => 'nullable|date|before:today',
            'parent_occupation' => 'nullable|array',
            'parent_occupation.*' => 'nullable|max:100',
            'parent_workplace' => 'nullable|array',
            'parent_workplace.*' => 'nullable|max:100',
            'parent_work_phone' => 'nullable|array',
            'parent_work_phone.*' => 'nullable|max:15',
            'parent_address_line1' => 'nullable|array',
            'parent_address_line1.*' => 'nullable|max:255',
        ];
    }
}
