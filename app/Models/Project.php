<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'name',
        'external_code',
        'contract_start',
        'contract_end',
        'contractor_name',
        'is_published',
        'project_type',
        'fiscal_year_id',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end'   => 'date',
        'is_published'   => 'boolean',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    /** 事業名・受託元・外部コードを横断検索 */
    public function scopeKeyword(Builder $q, ?string $kw): Builder
    {
        $kw = trim((string)$kw);
        if ($kw === '') return $q;

        return $q->where(function ($qq) use ($kw) {
            $qq->where('name', 'like', "%{$kw}%")
               ->orWhere('contractor_name', 'like', "%{$kw}%")
               ->orWhere('external_code', 'like', "%{$kw}%");
        });
    }

    /** 期間が重なるものを抽出（[from, to] と契約期間の交差） */
    public function scopeOverlapPeriod(Builder $q, ?string $from, ?string $to): Builder
    {
        if (!$from && !$to) return $q;

        if ($from && $to) {
            return $q->where('contract_start', '<=', $to)
                     ->where('contract_end', '>=', $from);
        }
        if ($from) {
            return $q->where('contract_end', '>=', $from);
        }
        return $q->where('contract_start', '<=', $to);
    }

    /** 事業タイプフィルタ（venue / individual） */
    public function scopeTypeIn(Builder $q, array $types): Builder
    {
        $allowed = array_intersect($types, ['venue','individual']);
        if (empty($allowed)) return $q;
        return $q->whereIn('project_type', $allowed);
    }

    /** 年度フィルタ */
    public function scopeFiscal(Builder $q, ?int $fiscalYearId): Builder
    {
        if (!$fiscalYearId) return $q;
        return $q->where('fiscal_year_id', $fiscalYearId);
    }
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }

}
