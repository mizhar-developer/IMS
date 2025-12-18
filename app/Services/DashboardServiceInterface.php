<?php
namespace App\Services;

interface DashboardServiceInterface
{
    /**
     * Return top-level counts for dashboard widgets.
     *
     * @return array<string,int>
     */
    public function getCounts(): array;

    /**
     * Recent uploaded images collection.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function recentImages(int $limit = 6): \Illuminate\Support\Collection;

    /**
     * Recent diagnoses collection.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function recentDiagnoses(int $limit = 6): \Illuminate\Support\Collection;

    /**
     * Number of staff per role keyed by role name.
     *
     * @return array<string,int>
     */
    public function staffByRole(): array;

    /**
     * Time series of image counts by day.
     *
     * @param int $days
     * @return array<string,int>
     */
    public function imagesTimeline(int $days = 14): array;
}
