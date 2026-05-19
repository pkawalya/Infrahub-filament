<?php

namespace App\Services;

use App\Models\BidStage;
use App\Models\BidStageTransition;
use App\Models\Notification;
use App\Models\StageAuditLog;
use App\Models\Tender;
use App\Models\TenderBid;
use App\Models\TenderStage;
use App\Models\TenderStageTransition;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StageWorkflowService
{
    // ═══════════════════════════════════════════════════════════
    //  TENDER STAGE TRANSITIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get allowed next stages for a tender.
     */
    public function getNextTenderStages(Tender $tender): \Illuminate\Support\Collection
    {
        if (!$tender->tender_stage_id || !$tender->stage) {
            return collect();
        }

        $user = auth()->user();
        $transitions = TenderStageTransition::where('company_id', $tender->company_id)
            ->where('from_stage_id', $tender->tender_stage_id)
            ->where('is_active', true)
            ->with('toStage')
            ->get();

        return $transitions->filter(function ($transition) use ($user) {
            if (!$transition->toStage || !$transition->toStage->is_active) {
                return false;
            }
            if ($transition->required_permission && !$user?->canManageCompany()) {
                return $user?->hasPermissionTo($transition->required_permission) ?? false;
            }
            return true;
        })->map(fn($t) => $t->toStage);
    }

    /**
     * Transition a tender to a new stage.
     *
     * @throws \Exception
     */
    public function transitionTender(Tender $tender, int $toStageId, ?string $comment = null): Tender
    {
        return DB::transaction(function () use ($tender, $toStageId, $comment) {
            $user = auth()->user();
            $fromStage = $tender->stage;
            $toStage = TenderStage::findOrFail($toStageId);

            // Validate transition is allowed
            $this->validateTenderTransition($tender, $toStageId, $user);

            // Record audit log
            StageAuditLog::record(
                model: $tender,
                fromStageName: $fromStage?->name,
                fromStageId: $fromStage?->id,
                toStageName: $toStage->name,
                toStageId: $toStage->id,
                comment: $comment,
                metadata: [
                    'previous_status' => $tender->status,
                    'ip_address' => request()->ip(),
                ],
            );

            // Update the tender
            $tender->update([
                'tender_stage_id' => $toStage->id,
                'stage_changed_at' => now(),
            ]);

            // Send notifications
            $this->notifyTenderStageChange($tender, $fromStage, $toStage, $user);

            return $tender->fresh(['stage']);
        });
    }

    /**
     * Validate a tender transition.
     *
     * @throws \Exception
     */
    protected function validateTenderTransition(Tender $tender, int $toStageId, ?User $user): void
    {
        if (!$tender->tender_stage_id) {
            return; // First transition (from null) is always allowed
        }

        $transition = TenderStageTransition::where('company_id', $tender->company_id)
            ->where('from_stage_id', $tender->tender_stage_id)
            ->where('to_stage_id', $toStageId)
            ->where('is_active', true)
            ->first();

        if (!$transition) {
            throw new \Exception('This stage transition is not allowed.');
        }

        if ($transition->required_permission && $user && !$user->canManageCompany()) {
            if (!$user->hasPermissionTo($transition->required_permission)) {
                throw new \Exception('You do not have permission to perform this stage transition.');
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    //  BID STAGE TRANSITIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get allowed next stages for a bid.
     */
    public function getNextBidStages(TenderBid $bid): \Illuminate\Support\Collection
    {
        if (!$bid->bid_stage_id || !$bid->stage) {
            return collect();
        }

        $user = auth()->user();
        $transitions = BidStageTransition::where('company_id', $bid->company_id)
            ->where('from_stage_id', $bid->bid_stage_id)
            ->where('is_active', true)
            ->with('toStage')
            ->get();

        return $transitions->filter(function ($transition) use ($user) {
            if (!$transition->toStage || !$transition->toStage->is_active) {
                return false;
            }
            if ($transition->required_permission && !$user?->canManageCompany()) {
                return $user?->hasPermissionTo($transition->required_permission) ?? false;
            }
            return true;
        })->map(fn($t) => $t->toStage);
    }

    /**
     * Transition a bid to a new stage.
     *
     * @throws \Exception
     */
    public function transitionBid(TenderBid $bid, int $toStageId, ?string $comment = null): TenderBid
    {
        return DB::transaction(function () use ($bid, $toStageId, $comment) {
            $user = auth()->user();
            $fromStage = $bid->stage;
            $toStage = BidStage::findOrFail($toStageId);

            // Validate transition
            $this->validateBidTransition($bid, $toStageId, $user);

            // Record audit log
            StageAuditLog::record(
                model: $bid,
                fromStageName: $fromStage?->name,
                fromStageId: $fromStage?->id,
                toStageName: $toStage->name,
                toStageId: $toStage->id,
                comment: $comment,
                metadata: [
                    'tender_id' => $bid->tender_id,
                    'ip_address' => request()->ip(),
                ],
            );

            // Update the bid
            $bid->update([
                'bid_stage_id'    => $toStage->id,
                'stage_changed_at' => now(),
            ]);

            // Notify stakeholders
            $this->notifyBidStageChange($bid, $fromStage, $toStage, $user);

            return $bid->fresh(['stage']);
        });
    }

    protected function validateBidTransition(TenderBid $bid, int $toStageId, ?User $user): void
    {
        if (!$bid->bid_stage_id) {
            return;
        }

        $transition = BidStageTransition::where('company_id', $bid->company_id)
            ->where('from_stage_id', $bid->bid_stage_id)
            ->where('to_stage_id', $toStageId)
            ->where('is_active', true)
            ->first();

        if (!$transition) {
            throw new \Exception('This bid stage transition is not allowed.');
        }

        if ($transition->required_permission && $user && !$user->canManageCompany()) {
            if (!$user->hasPermissionTo($transition->required_permission)) {
                throw new \Exception('You do not have permission to perform this bid stage transition.');
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    //  NOTIFICATIONS
    // ═══════════════════════════════════════════════════════════

    protected function notifyTenderStageChange(Tender $tender, ?TenderStage $from, TenderStage $to, ?User $actor): void
    {
        // Notify assigned estimator and company admins
        $recipients = User::where('company_id', $tender->company_id)
            ->where('is_active', true)
            ->where(function ($q) use ($tender) {
                $q->whereIn('user_type', ['company_admin', 'manager'])
                  ->orWhere('id', $tender->assigned_to);
            })
            ->where('id', '!=', $actor?->id)
            ->get();

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type'    => 'tender_stage_change',
                'title'   => "Tender Stage Updated",
                'message' => "\"{$tender->title}\" moved from " .
                             ($from?->name ?? 'Initial') . " → {$to->name}" .
                             ($actor ? " by {$actor->name}" : ''),
                'data'    => [
                    'tender_id'   => $tender->id,
                    'from_stage'  => $from?->name,
                    'to_stage'    => $to->name,
                    'actor_id'    => $actor?->id,
                ],
            ]);
        }
    }

    protected function notifyBidStageChange(TenderBid $bid, ?BidStage $from, BidStage $to, ?User $actor): void
    {
        $tender = $bid->tender;
        $recipients = User::where('company_id', $bid->company_id)
            ->where('is_active', true)
            ->where(function ($q) use ($tender, $bid) {
                $q->whereIn('user_type', ['company_admin', 'manager'])
                  ->orWhere('id', $tender?->assigned_to)
                  ->orWhere('id', $bid->created_by);
            })
            ->where('id', '!=', $actor?->id)
            ->get();

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type'    => 'bid_stage_change',
                'title'   => "Bid Stage Updated",
                'message' => "Bid from \"{$bid->bidder_name}\" on \"{$tender?->title}\" moved from " .
                             ($from?->name ?? 'Initial') . " → {$to->name}" .
                             ($actor ? " by {$actor->name}" : ''),
                'data'    => [
                    'bid_id'     => $bid->id,
                    'tender_id'  => $bid->tender_id,
                    'from_stage' => $from?->name,
                    'to_stage'   => $to->name,
                    'actor_id'   => $actor?->id,
                ],
            ]);
        }
    }
}
