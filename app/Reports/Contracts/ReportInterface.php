<?php

namespace App\Reports\Contracts;

use Illuminate\Support\Collection;

/**
 * نفس عقد تقارير نظام الشكاوى.
 */
interface ReportInterface
{
    /** مفتاح التقرير في الرابط (مثلاً incidents-by-sector) */
    public function key(): string;

    /** اسم التقرير بالعربي */
    public function label(): string;

    /** وصف مختصر يظهر في كارت التقرير */
    public function description(): string;

    /** أيقونة boxicons للكارت */
    public function icon(): string;

    /** الصلاحية: reports.{key} */
    public function permission(): string;

    /** الفلاتر: [name, label, type (date|select), options?, required?] */
    public function filters(): array;

    /** الصفوف بعد تطبيق الفلاتر */
    public function generate(array $filters): Collection;

    /** عناوين الأعمدة (للشاشة + Excel + PDF) */
    public function headings(): array;

    /** صف واحد → مصفوفة بنفس ترتيب headings() */
    public function map(mixed $row): array;
}