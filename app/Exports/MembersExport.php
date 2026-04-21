<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $organisationId;
    protected $roleFilter;

    public function __construct($organisationId = null, $roleFilter = 'member')
    {
        $this->organisationId = $organisationId;
        $this->roleFilter = $roleFilter;
    }

    public function collection()
    {
        $lastResetAt = null;
        if ($this->organisationId) {
            $lastResetAt = \App\Models\Organisation::find($this->organisationId)?->last_grade_reset_at;
        }

        $query = User::with(['organisation', 'division'])
            ->withCount(['attendances' => function($q) use ($lastResetAt) {
                $q->where('status', 'hadir');
                if ($lastResetAt) {
                    $q->where('created_at', '>', $lastResetAt);
                }
            }]);

        if ($this->organisationId) {
            $query->where('organisation_id', $this->organisationId);
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        return $query->get()->map(function($u) {
            $count = $u->attendances_count;
            if ($count >= 4) {
                $u->grade = 'A';
            } elseif ($count >= 2) {
                $u->grade = 'B';
            } else {
                $u->grade = '-';
            }
            return $u;
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Organisasi',
            'Divisi',
            'Total Hadir',
            'Nilai'
        ];
    }

    public function map($u): array
    {
        return [
            $u->name,
            $u->email,
            $u->organisation->name ?? '-',
            $u->division->name ?? '-',
            $u->attendances_count,
            $u->grade
        ];
    }
}
